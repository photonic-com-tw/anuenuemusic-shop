<?php

namespace app\ajax\controller;


use think\Controller;
use think\Request;
use think\Db;


//photonicClass
use pattern\simpleFactory\orderFactory\OrderFactory;
use pattern\simpleFactory\orderFactory\PointEffective;
use app\ajax\controller\Zipapi;
use app\index\controller\PublicController;
use Exception;


class Ecpaylogistic extends Controller
{

    public function __construct() {
        parent::__construct();
    }

	public function createTransportPaper($id) {

		$singleData = Db::connect('main_db')->table('orderform')->find($id);
		//綠界建物流訂單
		try{
			$this->expressCreate($id, $singleData);
		 }catch(\Exception $e) {
			$this->error($e->getMessage());
		}

	    //綠界物流託運單
	    $html = $this->getTransportPaper($id);

	    return $html;
	}

	public function checkTransportPaper($id) {

	    //綠界物流託運單
	    $html = $this->getTransportPaper($id);

	    return $html;
	}


	//綠界建物流訂單
	public function expressCreate($id, $orderData){
		if((int)$orderData['total']>20000){
			throw new Exception('綠界不接受單筆金額超過20,000元之訂單');
		}

		// 超商取貨物流訂單幕後建立
		include(ROOT_PATH.'extend/thirdparty/Ecpay.Logistic.Integration.php');

		//檢查是否需超商付款
		if($orderData['payment'] == "貨到付款"){
			$CollectionAmount = (int)$orderData['total'];
			$IsCollection = \EcpayIsCollection::YES;
		}else{
			$CollectionAmount = 0;
			$IsCollection = \EcpayIsCollection::NO;
		}

		//檢查哪種超商取貨
		$LogisticsType = \EcpayLogisticsType::CVS;
        if($orderData['transport'] =="全家取貨"){
			$LogisticsSubType = \EcpayLogisticsSubType::FAMILY; //FAMILY(B2C), FAMILY_C2C(C2C)
        }elseif ($orderData['transport'] =="7-11取貨") {
        	$LogisticsSubType = \EcpayLogisticsSubType::UNIMART; //UNIMART(B2C), UNIMART_C2C(C2C)
        }elseif ($orderData['transport'] =="萊爾富取貨") {
        	$LogisticsSubType = \EcpayLogisticsSubType::HILIFE; //HILIFE(B2C), HILIFE_C2C(C2C)
        }elseif ($orderData['transport'] =="宅配"){
        	$LogisticsType = \EcpayLogisticsType::HOME;
        	$LogisticsSubType = \EcpayLogisticsSubType::TCAT; //TCAT.黑貓(宅配)  ECAN.宅配通

        	if($IsCollection == 'Y'){
        		throw new Exception('宅配無法使用貨到付款');
        	}

        }else{
        	throw new Exception('無此取件方式');
        }


    	$seo = PublicController::getSeoData();

        $AL = new \EcpayLogistics();
        $AL->HashKey = Logistic_HashKey;
        $AL->HashIV = Logistic_HashIV;
        $AL->Send = array(
            'MerchantID' => MerchantID,
            'MerchantTradeNo' => 'no' . date('YmdHis'),
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'LogisticsType' => $LogisticsType,
            'LogisticsSubType' => $LogisticsSubType,
            'GoodsAmount' => (int)$orderData['total'],
            'CollectionAmount' => $CollectionAmount,
            'IsCollection' => $IsCollection,
            'GoodsName' => $seo['title'].'線上購物商品',
            'SenderName' => SenderName,
            'SenderPhone' => SenderPhone,
            'SenderCellPhone' => SenderCellPhone,
            'ReceiverName' => $orderData['transport_location_name'],
            'ReceiverPhone' => '', 	//$orderData['transport_location_tele']
            'ReceiverCellPhone' => $orderData['transport_location_phone'],
            'ReceiverEmail' => $orderData['transport_email'],
            'TradeDesc' => '', // 交易敘述
            'ServerReplyURL' => 'https://'.$_SERVER['HTTP_HOST'].'/ajax/EcpayLogistic/updateStatus/id/'.$id,				//更新物流狀態用
            'LogisticsC2CReplyURL' => 'https://'.$_SERVER['HTTP_HOST'].'/ajax/EcpayLogistic/LogisticsC2CReplyURL/id/'.$id,	//取貨超商有問時，提醒修改超商用
            'Remark' => '', // 備註
            'PlatformID' => '',
        );


        if($orderData['transport'] =="宅配"){
        	//產生郵遞區號
        	$Zipapi = new Zipapi();
        	try{
	        	$ReceiverZipCode = $Zipapi->zip32_api($orderData['transport_location']);
	        	// dump($ReceiverZipCode);exit;
	        }catch(\Exception $e) {
		        throw new Exception('地址錯誤');
		    }
		    if($ReceiverZipCode==""){
        		throw new Exception('地址錯誤，找不到對應郵遞區號');
        	}

	        //填入收件資訊(宅配)
        	$AL->SendExtend = array(
	        	'SenderZipCode' => SenderZipCode,
	            'SenderAddress' => SenderAddress,
	            'ReceiverZipCode' => $ReceiverZipCode,
	            'ReceiverAddress' => $orderData['transport_location'],

	            'Temperature' => \EcpayTemperature::ROOM,
	            'Distance' => \EcpayDistance::SAME,
	            'Specification' => \EcpaySpecification::CM_120,
	            'ScheduledDeliveryTime' => \EcpayScheduledDeliveryTime::TIME_17_20
            );
        }
        else{
        	//填入收件資訊(超商)
        	$AL->SendExtend = array(
            	'ReceiverStoreID' => $orderData['CVSStoreID'],
            	'ReturnStoreID' => $orderData['CVSStoreID']
        	);
        }

        // BGCreateShippingOrder()
        $Result = $AL->BGCreateShippingOrder();
	    // dump($Result);exit;
        if(!isset($Result['AllPayLogisticsID'])){
        	$error_msg = array_keys($Result)[1];
        	if($Result[$error_msg]){
        		throw new Exception($Result[$error_msg]);
        	}else{
        		throw new Exception($error_msg);
        	}
        	exit;
        }

	    //儲存資料
	    Db::connect('main_db')->table('orderform')->where('id ='.$id)->update(['AllPayLogisticsID'=>$Result['AllPayLogisticsID'], 'CheckMacValue'=>$Result['CheckMacValue'] ]);

	    //更新物流狀態
	    $this->checkStatus($id);
	}



	//綠界物流託運單
	public function getTransportPaper($id){

		$AllPayLogisticsID = Db::connect('main_db')->table('orderform')->find($id)['AllPayLogisticsID'];

    	include(ROOT_PATH.'extend/thirdparty/Ecpay.Logistic.Integration.php');

		try {
	        $AL = new \EcpayLogistics();
	        $AL->HashKey = Logistic_HashKey;
	        $AL->HashIV = Logistic_HashIV;
	        $AL->Send = array(
	            'MerchantID' => MerchantID,
	            'AllPayLogisticsID' => $AllPayLogisticsID,
	            'PlatformID' => ''
	        );
	        // PrintTradeDoc(Button名稱, Form target)
	        $html = $AL->PrintTradeDoc('產生托運單/一段標');
	        // echo $html;
	    } catch(Exception $e) {
	        echo $e->getMessage();
	    }

	    return $html;
	}


	//物流狀態更新
	public function updateStatus($id){

		$text = $_POST;
		if(empty($text) || $text == ''){
			echo '0|' . '回傳訊息為空';
			return;
		}

		include(ROOT_PATH.'extend/thirdparty/Ecpay.Logistic.Integration.php');
	    try {
	        // 收到綠界科技的物流狀態，並判斷檢查碼是否相符
	        $AL = new \EcpayLogistics();
	        $AL->HashKey = Logistic_HashKey;
	        $AL->HashIV = Logistic_HashIV;
	        $AL->CheckOutFeedback($_POST);

	        // 以物流狀態進行相對應的處理
	        /** 
	        回傳的綠界科技的物流狀態如下:
	        Array
	        (
	            [AllPayLogisticsID] =>
	            [BookingNote] =>
	            [CheckMacValue] =>
	            [CVSPaymentNo] =>
	            [CVSValidationNo] =>
	            [GoodsAmount] =>
	            [LogisticsSubType] =>
	            [LogisticsType] =>
	            [MerchantID] =>
	            [MerchantTradeNo] =>
	            [ReceiverAddress] =>
	            [ReceiverCellPhone] =>
	            [ReceiverEmail] =>
	            [ReceiverName] =>
	            [ReceiverPhone] =>
	            [RtnCode] =>
	            [RtnMsg] =>
	            [UpdateStatusDate] =>
	        )
	        */

  			// $order = Db::connect('main_db')->table('orderform')->find($id);
			// $AllPayLogisticsID = $order['AllPayLogisticsID'];

			$insertData = [
				'order_id' => $id,
				'logistics_id' => $text['AllPayLogisticsID'],
				'time' => date('Y-m-d H:i:s'),
				'text' => json_encode($text),
				'LogisticsType' => $text['LogisticsType'].'_'.$text['LogisticsSubType'],
				'RtnCode' => (string)$text['RtnCode'],
				'ShipmentNo' => $text['CVSPaymentNo'],
				'BookingNote' => $text['BookingNote'],
				'CheckMacValue' => $text['CheckMacValue']
			];

			Db::connect('main_db')->table('logistics_record')->insert($insertData);

	        // 在網頁端回應 1|OK
	        echo '1|OK';
	    } catch(\Exception $e) {
	        echo '0|' . $e->getMessage();
	    }
	}


	//查詢物流狀態
	public function checkStatus($id){

		$AllPayLogisticsID = Db::connect('main_db')->table('orderform')->find($id)['AllPayLogisticsID'];

		include(ROOT_PATH.'extend/thirdparty/Ecpay.Logistic.Integration.php');
	    try {
	        $AL = new \EcpayLogistics();
	        $AL->HashKey = Logistic_HashKey;
	        $AL->HashIV = Logistic_HashIV;
	        $AL->Send = array(
	            'MerchantID' => MerchantID,
	            'AllPayLogisticsID' => $AllPayLogisticsID,
	            'PlatformID' => ''
	        );
	        // QueryLogisticsInfo()
	        $Result = $AL->QueryLogisticsInfo();
	        // echo '<pre>' . print_r($Result, true) . '</pre>';

	        $order = Db::connect('main_db')->table('orderform')->find($id);
			$AllPayLogisticsID = $order['AllPayLogisticsID'];

			$insertData = [
				'order_id' => $id,
				'logistics_id' => $AllPayLogisticsID,
				'time' => date('Y-m-d H:i:s'),
				'text' => json_encode($Result),
				'LogisticsType' => $Result['LogisticsType'],
				'RtnCode' => $Result['LogisticsStatus'],
				'ShipmentNo' => $Result['ShipmentNo'],
				'BookingNote' => $Result['BookingNote'],
				'CheckMacValue' => $Result['CheckMacValue']
			];

			Db::connect('main_db')->table('logistics_record')->insert($insertData);

	    } catch(Exception $e) {
	        echo $e->getMessage();
	    }
	}
}

