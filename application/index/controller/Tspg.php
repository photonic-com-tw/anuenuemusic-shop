<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use PHPMailer\PHPMailer\PHPMailer;

class Tspg extends PublicController
{	

	const API_ROOT = "tspg.taishinbank.com.tw/tspgapi/restapi";

	const Tspg_DATA = [
		"sender"	=>	"rest",
		"ver"		=>	"1.0.0",		// 格式版本
		"mid"		=>	Tspg_mid,		// 特店代號
		"s_mid"		=>	Tspg_s_mid,		// 子特店代號
		"tid"		=>	Tspg_tid,		// 端末代號
		"pay_type"	=>	1,				// 付款類別
	];

	public function __construct() {
        parent::__construct(request()->instance());
    }

	public function returnurl($id) {
		$postdata = file_get_contents("php://input",'r');

		Db::connect('main_db')->table('orderform')->where('id', $id)->update([
			'tspg_return_data' => $postdata
		]);

		$postdata = json_decode($postdata);

		if($postdata->params->ret_code == "00"){
			Db::connect('main_db')->table('orderform')->where('id', $id)->update([
				'receipts_state' => 1
			]);
		
			$o=Db::connect('main_db')->table('orderform')->join('account','orderform.user_id = account.id')->where('orderform.id', $id)->select();
			$result=(array)json_decode($o[0]['product'], true);
			$res_goods='';
			foreach ($result as $key => $value) {
				$res_goods .=$value['name'];
				if(!empty($result[$key+1])){
					$res_goods .= '、';
				}
			}

			$globalMailData = parent::getMailData();

			$email= $o[0]['transport_email'] ? $o[0]['transport_email'] : $o[0]['email'];
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

			$paid_order_letter = $this->lang_menu['訂單已付款消費者信'];
	        $paid_order_letter = str_replace("{order_number}", $o[0]['order_number'], $paid_order_letter);
	        $paid_order_letter = str_replace("{id}", $id, $paid_order_letter);
			$mail->Body = $paid_order_letter;

			$mail->Send();
		}
	}


	public function auth($order_number, $total, $mailFromName, $backurl, $order_id){	// 信用卡授權交易
		$post_data = self::Tspg_DATA;
			$post_data["tx_type"] = 1;									// 交易類別	1:授權

			$layout = self::isMobile() ? '2' : '1'; 					// 檢查流覽方式

			$post_data["params"] = [  									// 交易要求參數清單
			"layout"		=>	$layout,									// 裝置 '1'.電腦  '2'.手機
			"order_no"		=>	$order_number,								// 訂單編號
			"amt"			=>	$total."00",								// 交易金額
			"cur"			=>	"NTD",										// 幣別
			"order_desc"	=>	$mailFromName."訂單",						// 交易說明
			"amt"			=>	$total.'00',								// 交易金額
			"capt_flag"		=>	"0",										// 不同步請款
			"result_flag"	=>	"1",										// 要回傳詳細訂單資料
			"post_back_url"	=>	$backurl,									// 接續網址
			"result_url"	=>  url('Tspg/returnurl', [						// 交易狀況回傳網址
										'id' => $order_id
									],'' , true)
		];
		$post_jsondata = json_encode($post_data);
		// dump($post_jsondata);exit;

		$post_url = "https://".(self::API_ROOT)."/auth.ashx";
		$res = $this->http_request($post_url, $post_jsondata);
		return json_decode($res);
	}


	public function check_order($order_no){
		$post_data = self::Tspg_DATA;
		$post_data["tx_type"] = 7;										// 交易類別	7:查詢
		$post_data["params"] = [  										// 交易要求參數清單
			"order_no"	=>	$order_no,									// 訂單編號
			"result_flag"	=>	"1",									// 要回傳詳細訂單資料
		];
		$post_jsondata = json_encode($post_data);
		// dump($post_jsondata);exit;

		$post_url = "https://".(self::API_ROOT)."/other.ashx";
		$res = self::http_request($post_url, $post_jsondata);
		return json_decode($res);
	}


	public function send_order_complete_email($order_id){

		try {
			// Db::connect('main_db')->table('orderform')->where('id', $order_id)->update([
			// 	'receipts_state' => 1,	// 已收款
			// 	'card4no' => $card4no	// 卡號末四碼
			// ]);
			$o=Db::connect('main_db')->table('orderform')->join('account','orderform.user_id = account.id', 'left')->where('orderform.id', $order_id)->select();
			$result=(array)json_decode($o[0]['product'], true);
			$res_goods='';
			foreach ($result as $key => $value) {
				$res_goods .=$value['name'];
				if(!empty($result[$key+1])){
					$res_goods .= '、';
				}
			}
			$globalMailData = parent::getMailData();

			$email=$o[0]['transport_email'];
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
			/*
			$email = Db::table('admin')->field('email')->find(1);
			$mail->AddAddress($email['email']);
			*/
			$mail->AddAddress($email);
			$mail->IsHTML(true);
			$new_order_letter = $this->lang_menu['訂單成功消費者信'];
            $new_order_letter = str_replace("{globalMailData_mailFromName}", $globalMailData['mailFromName'], $new_order_letter);
            $new_order_letter = str_replace("{Service_Tel}", Service_Tel, $new_order_letter);
            $mail->Body = "
                <html>
                    <head></head>
                    <body>
                        <div>
                            ".$new_order_letter."
                            ".$this->lang_menu['訂單編號']."：".$o[0]['order_number']."<br>
                            ".$this->lang_menu['訂單時間']."：".date('Y/m/d H:i',$o[0]['create_time'])."<br>
                            ".$this->lang_menu['訂購商品']."：".$res_goods."<br>
                            ".$this->lang_menu['訂單金額']."：".$OrderData['total']."<br>
                            ".$this->lang_menu['購買人']."：".$this->user['name']."<br>
                            ".$this->lang_menu['收件人']."：".$o[0]['transport_location_name']."<br>
                            ".$this->lang_menu['出貨地址']."：".$o[0]['transport_location']."<br>
                            E-mail：".$OrderData['email']."<br>
                            ".$this->lang_menu['手機號碼']."：".$o[0]['transport_location_phone']."<br>
                            ".$this->lang_menu['聯絡電話']."：".$o[0]['transport_location_tele']."<br>
                            ".$this->lang_menu['付款方式']."：".$o[0]['payment']."<br>
                            ".$this->lang_menu['備註']."：".$o[0]['transport_location_textarea']."<br>
                        </div>
                        <div>
                        ". $globalMailData['system_email']['order_complete'] ."
                        </div>
                    </body>
                </html>
            ";
			$mail->Send();
			
			$mailcom = new PHPMailer();
			$mailcom->IsSMTP();
			$mailcom->Host = $globalMailData['mailHost'];
			$mailcom->Port = 465;
			$mailcom->SMTPAuth = true;
			$mailcom->SMTPSecure = "ssl";
			$mailcom->CharSet = "UTF-8";
			$mailcom->Encoding = "base64";
			$mailcom->Username = $globalMailData['mailUsername'];
			$mailcom->Password = $globalMailData['mailPassword'];
			$mailcom->Subject = $globalMailData['mailSubject'];
			$mailcom->From = $globalMailData['mailFrom'];
			$mailcom->FromName = $globalMailData['mailFromName'];

			$return_email=Db::table('admin')->where('id','1')->value('email');
			$mailcom->AddAddress($return_email);
			$mailcom->IsHTML(true);
			$mailcom->Body = "
				<html>
					<head></head>
					<body>
						<div>
							新訂單提醒<br>
							訂單位置：".$globalMailData['mailFromName']."
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
			$mailcom->Send();
			
		} catch(\Exception $e) {
			echo '0|' . $e->getMessage();
			Db::connect('main_db')->table('orderform')->where('id', $order_id)->update([
				'error' => 1
			]);
		}
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

    public function isMobile() {
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
}
