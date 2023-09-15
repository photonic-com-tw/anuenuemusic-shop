<?php

namespace app\index\controller;



use think\Controller;

use think\Db;



class Ecreturn extends PublicController
{
	public function returnurl($id) {

		include(ROOT_PATH.'extend/thirdparty/ECPay.Payment.Integration.php');

		try {
			$obj = new \ECPay_AllInOne();

            //服務參數
			$obj->ServiceURL  = ServiceURL;	//服務位置
			$obj->HashKey     = HashKey;	//Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV      = HashIV;		//HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID  = MerchantID;	//MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';

			$obj->CheckOutFeedback();

			if($_POST['RtnCode']=='1'){
				Db::connect('main_db')->table('orderform')->where('id', $id)->update([
					'receipts_state' => 1,'card4no' =>$_POST['card4no']
				]);

				$o=Db::connect('main_db')->table('orderform')->join('account','orderform.user_id = account.id')->where('id', $id)->select();
				$result=(array)json_decode($o[0]['product'], true);
				$res_goods='';

				foreach ($result as $key => $value) {
					$res_goods .=$value['name'];
					if(!empty($result[$key+1])){
						$res_goods .= '、';
					}
				}

				$globalMailData = parent::getMailData();
				$new_order_letter = LANG_MENU['訂單成功消費者信'];
	            $new_order_letter = str_replace("{globalMailData_mailFromName}", $globalMailData['mailFromName'], $new_order_letter);
	            $new_order_letter = str_replace("{Service_Tel}", Service_Tel, $new_order_letter);
				$mailBody = "
	                <html>
	                    <head></head>
	                    <body>
	                        <div>
	                            ".$new_order_letter."
	                            ".LANG_MENU['訂單編號']."：".$o[0]['order_number']."<br>
	                            ".LANG_MENU['訂單時間']."：".date('Y/m/d H:i',$o[0]['create_time'])."<br>
	                            ".LANG_MENU['訂購商品']."：".$res_goods."<br>
	                            ".LANG_MENU['訂單金額']."：".$OrderData['total']."<br>
	                            ".LANG_MENU['購買人']."：".$this->user['name']."<br>
	                            ".LANG_MENU['收件人']."：".$o[0]['transport_location_name']."<br>
	                            ".LANG_MENU['出貨地址']."：".$o[0]['transport_location']."<br>
	                            E-mail：".$OrderData['email']."<br>
	                            ".LANG_MENU['手機號碼']."：".$o[0]['transport_location_phone']."<br>
	                            ".LANG_MENU['聯絡電話']."：".$o[0]['transport_location_tele']."<br>
	                            ".LANG_MENU['付款方式']."：".$o[0]['payment']."<br>
	                            ".LANG_MENU['備註']."：".$o[0]['transport_location_textarea']."<br>
	                        </div>
	                        <div>
	                        ". $globalMailData['system_email']['order_complete'] ."
	                        </div>
	                    </body>
	                </html>
	            ";
				$mail_return = parent::Mail_Send($mailBody,'client',$o[0]['email'],LANG_MENU['訂單成功資訊']);

				$mailBody = "
					<html>
						<head></head>
						<body>
							<div>
								新訂單提醒<br>
								訂單位置：".$globalMailData['mailFromName']."<br>
								訂單編號：".$o[0]['order_number']."<br>
								訂單時間：".date('Y/m/d H:i',$o[0]['create_time'])."<br>
								訂購商品：".$res_goods."<br>
								訂單金額：".$o[0]['total']."<br>
								購買人：".$this->user['name']."<br>
								收件人：".$o[0]['transport_location_name']."<br>
								出貨地址：".$o[0]['transport_location']."<br>
								E-mail：".$o[0]['email']."<br>
								手機號碼：".$o[0]['transport_location_phone']."<br>
								聯絡電話：".$o[0]['transport_location_tele']."<br>
								付款方式：".$o[0]['payment']."<br>
								備註：".$o[0]['transport_location_textarea']."<br>
							</div>
							<div style='color:red;'>
								≡ 此信件為系統自動發送，請勿直接回覆 ≡
							</div>
						</body>
					</html>
				";
				$mail_return = parent::Mail_Send($mailBody,'admin','',"新訂單提醒");

			}else{
				Db::connect('main_db')->table('orderform')->where('id', $id)->update([
					'receipts_state' => 0
				]);

			}
			echo '1|OK';
		} catch(\Exception $e) {
			Db::connect('main_db')->table('orderform')->where('id', $id)->update([
				'error' => 1
			]);
			echo '0|' . $e->getMessage();
		}
	}

	public function returncoupon($id, $user) {

		include(ROOT_PATH.'extend/thirdparty/ECPay.Payment.Integration.php');

		try {

			$obj = new \ECPay_AllInOne();



            //服務參數

			$obj->ServiceURL  = ServiceURL;	//服務位置
			$obj->HashKey     = HashKey ;	//Hashkey，請自行帶入ECPay提供的HashKey
			$obj->HashIV      = HashIV ;	//HashIV，請自行帶入ECPay提供的HashIV
			$obj->MerchantID  = MerchantID;	//MerchantID，請自行帶入ECPay提供的MerchantID
			$obj->EncryptType = '1';
			$obj->CheckOutFeedback();
			Db::table('coupon_pool')->where('id', $id)->update([
				'owner' => $user
			]);
			echo '1|OK';

		} catch(\Exception $e) {
			echo '0|' . $e->getMessage();
		}

	}

}

