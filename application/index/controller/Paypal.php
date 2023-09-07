<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use PHPMailer\PHPMailer\PHPMailer;

use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use PayPal\Api\PaymentExecution;

class Paypal extends PublicController
{
	// Sandbox account
	// sb-lz43pt9149445@business.example.com
	
	// const clientId = 'AZUaA-jptku630bkxzVAI138vtW41fpahzteIAa0jZjujiJnogW5P4RqyTl5EvptIYMZzeQMez57w8V8';//ID TEST
    // const clientSecret = 'EGpI28Jv_SZ2NuzQcxztghU_GwOvCqJ-VK2WQD9D7z1trfg51tcbBIgVNPh_wav54us3X6KtLhOjv9yK';//祕鑰 TEST
	const clientId = 'AWaq_dVSvtq9lXgN_L8A4Wysnv6UxZb30CIXouyGcMY3MXhfuSn6LIgjnoquhhwMAb5Bl0EBeZGX1ioD';//ID
    const clientSecret = 'EMXoc3brhOkrv69lR6z4kq6fdshYCSioOZU3btVMQwrKtMocr0nVEmiVi7ijkkjw51B2xg9ejIxHwSb3';//祕鑰
    const Currency = 'USD';//幣種
    protected $PayPal;

    public function __construct()
    {
        $this->PayPal = new ApiContext(
            new OAuthTokenCredential(
                self::clientId,
                self::clientSecret
            )
        );
        //如果不是沙盒測試環境就設定為live
        if(self::clientId!='AZUaA-jptku630bkxzVAI138vtW41fpahzteIAa0jZjujiJnogW5P4RqyTl5EvptIYMZzeQMez57w8V8'){
			$this->PayPal->setConfig(
			    array(
			        'mode' => 'live',
			    )
			);
        }
    }

    /**
     * @param
     * $product 商品
     * $price 價錢
     * $shipping 運費
     * $description 描述內容
     */
    public function pay($order_number, $total, $mailFromName, $backurl, $order_id){
    	$orderform = Db::connect('main_db')->table('orderform')->where("id", $order_id)->find();
    	$seo = Db::connect()->table('seo')->find();

        $paypal = $this->PayPal;
        $description = $order_number;
        $product =$seo['title'].'購物';
        $total = $total;
        $shipping = $orderform['shipping'];
        $price = $total - $shipping;//商品小記

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName($product)->setCurrency(self::Currency)->setQuantity(1)->setPrice($price);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $details = new Details();
        $details->setShipping($shipping)->setSubtotal($price);
        $amount = new Amount();
        $amount->setCurrency(self::Currency)->setTotal($total)->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($itemList)->setDescription($description)->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($backurl . '?success=true')->setCancelUrl($backurl . '/?success=false');

        $payment = new Payment();
        $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions([$transaction]);

        try {
            $payment->create($paypal);
        } catch (PayPalConnectionException $e) {
            echo $e->getData();
            die();
        }

        $approvalUrl = $payment->getApprovalLink();
        header("Location: {$approvalUrl}");
    }

    public function callback()
    {
        $success = trim($_GET['success']);

        if ($success == 'false' && !isset($_GET['paymentId']) && !isset($_GET['PayerID'])) {
            // echo '取消付款';die;
            $this->redirect("https://".$_SERVER['SERVER_NAME']."/index/orderform/orderform");
        }

        $paymentId = trim($_GET['paymentId']);
        $PayerID = trim($_GET['PayerID']);

        if (!isset($success, $paymentId, $PayerID)) {
            // echo '支付失敗';die;
            $this->redirect("https://".$_SERVER['SERVER_NAME']."/index/orderform/orderform");

        }

        if ((bool)$_GET['success'] === 'false') {
            // echo  '支付失敗，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';die;
            $this->redirect("https://".$_SERVER['SERVER_NAME']."/index/orderform/orderform");
        }

        $payment = Payment::get($paymentId, $this->PayPal);        

        $execute = new PaymentExecution();

        $execute->setPayerId($PayerID);

        try {
            $payment->execute($execute, $this->PayPal);
        } catch (Exception $e) {
            // echo ',支付失敗，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';die;
            $this->redirect("https://".$_SERVER['SERVER_NAME']."/index/orderform/orderform");
        }

        $order_number = $payment->transactions[0]->description;
        Db::connect('main_db')->table('orderform')->where("order_number", $order_number)->update([
        	'receipts_state' => 1,
			'tspg_return_data' => json_encode([
				"success" => $success,
				"paymentId" => $paymentId,
				"PayerID" => $PayerID,
			], JSON_UNESCAPED_UNICODE)
		]);
        $this->redirect("https://".$_SERVER['SERVER_NAME']."/index/orderform/orderform_success/id/".$order_number);
        // echo '支付成功，支付ID【' . $paymentId . '】,支付人ID【' . $PayerID . '】';die;
    }
}
