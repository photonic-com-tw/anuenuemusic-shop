<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use PHPMailer\PHPMailer\PHPMailer;

class Tsbc extends PublicController
{	
	// const TOKEN_KEY = "cd644e26055eefca6c31579b8690f742"."73f7d2f0044fd784eb5c82d7bcf5b8a4"; /*測試*/
	// const MERCHANTID = "000812463900297"; /*測試*/
	// const MERCHANTID_SUB = "WSID01"; /*測試*/
	// const API_ROOT = "tscbweb-t.taishinbank.com.tw/TSCBOgwAPI"; /*測試*/

	const TOKEN_KEY = "028804939962db2c487f2ce3f5d99f44"."19adf4625a0eccdf5e7d7b638cb60662"; /*正式*/
	const MERCHANTID = "000812463300037"; /*正式*/
	const MERCHANTID_SUB = "WSID01"; /*正式*/
	const API_ROOT = "tscboweb.taishinbank.com.tw/TSCBOgwAPI"; /*正式*/

	const QUERY_RETURN_CODE = [
		"c000" => "成功",
		"c001" => "成功，但查無訂單資料",
		"c701" => "付款閘道錯誤",
		"c702" => "特店代號錯誤",
		"c703" => "店鋪代號錯誤",
		"c704" => "終端機代號錯誤",
		"c705" => "查詢訂單編號錯誤",
		"c706" => "查詢時間異常",
		"c707" => "簽章異常",
		"c708" => "交易類別未輸入",
		"c709" => "例外錯誤",
		"c710" => "例外錯誤",
		"c711" => "例外錯誤",
		"c712" => "例外錯誤",
		"c713" => "例外錯誤",
		"c714" => "例外錯誤",
		"c715" => "例外錯誤",
		"c900" => "例外錯誤(該錯誤回傳XML時不一定包含sign資料)",
		"c901" => "例外錯誤(該錯誤回傳XML時不一定包含sign資料)",
		"c902" => "簽章失敗(該錯誤回傳XML時不一定包含sign資料)",
		"c903" => "例外錯誤(該錯誤回傳XML時不一定包含sign資料)",
		"c904" => "例外錯誤(該錯誤回傳XML時不一定包含sign資料)",
	];

	public function __construct() {
        parent::__construct(request()->instance());
    }

	public function notify() { /*通知付款狀態*/
		// $postdata = file_get_contents("php://input",'r');
		// $postdata = json_decode($postdata);
		$orderid = $_GET['orderid'];
		Db::connect('main_db')->table('orderform')->where('order_number', $orderid)->update([
			'tspg_return_data' => json_encode($_GET, JSON_UNESCAPED_UNICODE),
		]);

		$sign_in = $_GET['sign'];
		$sign_out = $this->add_sign($_GET)['data']['sign'];
		if($sign_in!=$sign_out){ /*簽章不正確*/
			return;
		}

		Db::connect('main_db')->table('orderform')->where('order_number', $orderid)->update([
			'receipts_state' => 1,
		]);
	
		$o=Db::connect('main_db')->table('orderform')->join('account','orderform.user_id = account.id')->where('orderform.order_number', $orderid)->find();
		if(!$o){ return; }
		$globalMailData = parent::getMailData();
		$email= $o['transport_email'] ? $o['transport_email'] : $o['email'];

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Host = $globalMailData['mailHost'];
		$mail->Port = 465;
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		$mail->CharSet = "UTF-8";
		$mail->Encoding = "base64";
		$mail->Username = $globalMailData['mailUsername'];
		$mail->Password = $globalMailData['mailPassword'];
		$mail->Subject = $globalMailData['mailSubject'];
		$mail->From = $globalMailData['mailFrom'];
		$mail->FromName = $globalMailData['mailFromName'];

		$mail->AddAddress($email);
		$mail->IsHTML(true);

		$paid_order_letter = LANG_MENU['訂單已付款消費者信'];
        $paid_order_letter = str_replace("{order_number}", $o['order_number'], $paid_order_letter);
        $paid_order_letter = str_replace("{id}", $o['id'], $paid_order_letter);
		$mail->Body = $paid_order_letter;

		$mail->Send();

		echo "ok";
	}
	public function auth($OrderData, $mailFromName, $backurl){	// 授權交易
		// dump($OrderData);exit;
		$request_url = "https://".(self::API_ROOT)."/gwMerchantApiQRCodePlus.ashx";

		$data = [ "amount" => (Int)$OrderData['total'] ];
		if($OrderData['pay_way']==LANG_MENU['支付寶']){
			$data['gw'] = "ALIPAY_O";
		}else if($OrderData['pay_way']==LANG_MENU['微信支付']){
			$data['gw'] = "WEIXIN_O";
		}else{
			return;
		}

		$data['merchantid'] = self::MERCHANTID;
		$data['redirectimage'] = 'Y';
		$data['storeid'] = self::MERCHANTID_SUB;
		$data['timestamp'] = date('YmdHis');
		$data['includeamt'] = 'Y';
		$data['orderid'] = $OrderData['order_number'];
		$data['ordername'] = $mailFromName;
		$data['ordermemo'] = '線上購物';
		$data['redirectimagesize'] = 'M';
		$data['terminalid'] = 'M';
		// dump($data);

		$params_str = $this->add_sign($data)['params_str'];
		// dump($params_str);
		$request_url .= '?'.$params_str;
		// dump($request_url);exit;
		$qr_code_url = $request_url;
		
		// $res = $this->http_request($request_url);
		// // dump($res);exit;
		// try {
		// 	$dom = new \domdocument();
		// 	$dom->loadHtml(mb_convert_encoding($res,'HTML-ENTITIES','UTF-8'));
		// 	$atags = $dom->getelementsbytagname('a');
		// 	if($atag){
		// 		$qr_code_url = $atags[0]->getattribute('href');
		// 	}else{
		// 		$qr_code_url = "";
		// 	}
		// } catch (\Exception $e) {
		// 	$qr_code_url = "";
		// }
		
		Db::connect('main_db')->table('orderform')->where('order_number', $OrderData['order_number'])->update([
			'tsbc_qr_code_url' => $qr_code_url,
		]);
		
		// dump($qr_code_url);exit;

		return $qr_code_url;
	}
	public function check_order($orderid){
		$request_url = "https://".(self::API_ROOT)."/gwMerchantApiQuery.ashx";
		$data = [];

		$o=Db::connect('main_db')->table('orderform')->join('account','orderform.user_id = account.id')->where('orderform.order_number', $orderid)->find();
		if($o){
			if($o['payment']==LANG_MENU['支付寶']){
				$data['gw'] = "ALIPAY_O";
			}else if($o['payment']==LANG_MENU['微信支付']){
				$data['gw'] = "WEIXIN_O";
			}
		}

		$data['merchantid'] = self::MERCHANTID;
		$data['id'] = $orderid;
		$data['storeid'] = self::MERCHANTID_SUB;
		$data['terminalid'] = 'M';
		$data['timestamp'] = date('YmdHis');
		$data['type'] = 'P';

		$params_str = $this->add_sign($data)['params_str'];
		// dump($params_str);
		$request_url .= '?'.$params_str;
		// dump($request_url);
		$res = $this->http_request($request_url);
		try {
			$xml=simplexml_load_string($res);
			$xml->return_msg = self::QUERY_RETURN_CODE['c'.$xml->return_code];
			$xml = (array)$xml;
		} catch (\Exception $e) {
			$xml = [];
		}
		$xml = $xml ? (array)$xml : [];
		// dump($xml);exit;

		if($xml['return_code']=="000"){
			if($xml['status']!='0'){
				Db::connect('main_db')->table('orderform')->where('order_number', $orderid)->update([
					'receipts_state' => 1,
				]);
				$this->success(LANG_MENU['交易已確認付款'], url('orderform/orderform_c', ['id' => $orderid]));
			}else{
				$this->error(LANG_MENU['交易未付款，請完成付款後再進行交易確認'], url('orderform/orderform_c', ['id' => $orderid]));
			}
		}else{
			$this->error(LANG_MENU['查詢交易有誤，請使用補單更新交易'], url('orderform/orderform'));
		}
	}
	public function cancel_pay($orderid=""){
		$orderform = Db::connect('main_db')->table('orderform')->where('order_number', $orderid)->find();
		// dump($orderform);exit;

		$request_url = "https://".(self::API_ROOT)."/gwMerchantApiRefund.ashx";
		
		$data = [ "amount" => (Int)$orderform['total'] ];
		if($orderform['payment']==LANG_MENU['支付寶']){
			$data['gw'] = "ALIPAY_O";
		}else if($orderform['payment']==LANG_MENU['微信支付']){
			$data['gw'] = "WEIXIN_O";
		}else{
			$this->error(LANG_MENU['訂單內容有誤，無法退款']);
		}

		$data['merchantid'] = self::MERCHANTID;
		$data['orderid'] = $orderform['order_number'];
		$data['refundid'] = '??';
		$data['refundreason'] = '消費者取消';
		$data['storeid'] = self::MERCHANTID_SUB;
		$data['terminalid'] = 'M';
		$data['timestamp'] = date('YmdHis');
		// dump($data);exit;

		$params_str = $this->add_sign($data)['params_str'];
		// dump($params_str);
		$request_url .= '?'.$params_str;
		// dump($request_url);exit;
		$res = $this->http_request($request_url);
		try {
			$xml=simplexml_load_string($res);
			$xml = (array)$xml;
		} catch (\Exception $e) {
			$xml = [];
		}
		$xml = $xml ? (array)$xml : [];
		// dump($xml);exit;

		if($xml['return_code']=="000"){
			$this->success(LANG_MENU['交易完成退款'], url('orderform/orderform_c', ['id' => $orderid]));
		}else{
			$this->error($xml['return_message'], url('orderform/orderform_c', ['id' => $orderid]));
		}
	}

	public function add_sign($data){
		unset($data['sign']);
		ksort($data);
		// dump($data);
		$str = '';
		foreach ($data as $k=>$val){
			$str .= $k .'=' . $val . '&';
		}
		$strs = rtrim($str, '&');
		$strs .= self::TOKEN_KEY;
		// dump($strs);
		$data['sign'] = hash('sha256', $strs);
		// dump($data);
		$str .= 'sign='. $data['sign'];
		return [ 'params_str'=>$str, 'data'=>$data ];
	}

	public function http_request($url, $data = null){
        $curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, $url);  
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (! empty($data)) {  
            curl_setopt($curl, CURLOPT_POST, 1);  
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
        }  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);  
        $output = curl_exec($curl);  
        curl_close($curl);  
        return $output;  
    }

    // public function isMobile() {
	    // return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	// }
}
