<?php


namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Validate;
use think\Request;
use DBtool\DBTextConnecter;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use ReCaptcha\ReCaptcha;

class About extends PublicController
{
	public function about_contact() {
		$contact = Db::table('contact')->find(1);
		$contact_type = explode(",", $contact['contact_type']);
		$this->assign('contact_type', $contact_type);
		return $this->fetch('about_contact');
	}

	function doContact(){
		$name = Request::instance()->post('name');
		$homephone = Request::instance()->post('homephone');
		$phone = Request::instance()->post('phone');
		$clientEmail = Request::instance()->post('email');
		$freeTime = Request::instance()->post('freeTime');
		$wanted = Request::instance()->post('wanted');
		$message =  Request::instance()->post('message');
		$order_number =  Request::instance()->post('order_number');

		if($name==""){ $this->error('請輸入姓名'); }
		if($clientEmail==""){ $this->error('請輸入電子信箱'); }
		if($wanted=="訂單問題"){ 
			if($order_number==""){ $this->error('請輸入訂單編號'); }
		}

		// _GOOGLE_RECAPTCHA_SEC_KEY 就是 google 給的 Secret Key
		$recaptcha = new ReCaptcha('6LeiEKAdAAAAAD28DJd3_o7_7JfvrnCedZfDKCrk');
		$gRecaptchaResponse = $_POST['recaptcha'];
		$remoteIp = $_SERVER['REMOTE_ADDR'];
		$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);
		if(!$resp->isSuccess()){
			$this->error('請先證明您不是機器人');
		}

		$this->DBTextConnecter = DBTextConnecter::withTableName('contact_log');
		try{
			$this->DBTextConnecter->setDataArray([
				'name' => $name,
				'homephone' => $homephone,
				'phone' => $phone,
				'email' => $clientEmail,
				'type' => $wanted,
				'freeTime' => $freeTime,
				'content' => $message,
				'order_number' => $order_number,
			]);
			$this->DBTextConnecter->createTextRow();
		} catch (\Exception $e){
			$this->error('操作錯誤，請聯絡管理員');
		}

		$globalMailData = parent::getMailData();
		$mailBody = "
			<html>
				<head></head>
				<body>
					<div>
						親愛的<u>&nbsp;&nbsp;$name&nbsp;&nbsp;</u>，您好，<br>
						我們已收到您的來信，並儘速處理您的問題，<br>
						請您耐心等候。<br><br>
						您所反應的問題內容如下：<br>

						<u>$message</u><br><br><br>
					</div>

					<center></center>

					<div>
						". $globalMailData['system_email']['contact_complete'] ."
					</div>
				</body>
			</html>
		";

		$mail_return = parent::Mail_Send($mailBody,'client',$clientEmail, "回函表");
		$mailBody = "
			<html>
				<head></head>
				<body>
					<div>
						新回函提醒<br>
						後台已收到新的回函<br>
						回函內容如下:<br>
						姓名:".$name."<br>
						聯絡電話:".$homephone."<br>
						手機號碼:".$phone."<br>
						電子信箱:".$clientEmail."<br>
						問題內容:
						<u>$message</u>

						<br><br>
						完整內容請至後台查看<br>
					</div>
					<div style='color:red;'>
						≡ 此信件為系統自動發送，請勿直接回覆 ≡
					</div>
				</body>
			</html>
		";

		$mail_return = parent::Mail_Send($mailBody,'admin','', "新回函提醒");
		$this->success('發送成功');
	}
	public function test_mail(){
		$mail_return = parent::Mail_Send('test','client','andyplanner@photonic.com.tw', "測試回函");
	}

	public function about_map() {
		$about = Db::table('about_story')->field('mapurl')->find(1);
		$this->assign('about',$about);
		return $this->fetch('about_map');
	}

	public function about_story() {
		$about = Db::table('about_story')->field(
						'image_left_top, 
						 image_right_top, 
						 image_right_bottom, 
						 content'
					)->find(1);
		$this->assign('about',$about);
		return $this->fetch('about_story');
	}
}



