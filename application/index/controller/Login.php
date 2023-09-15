<?php

namespace app\index\controller;

use think\Validate;
use think\Db;
use think\Request;
use think\Session;
use DBtool\DBTextConnecter;

class Login extends PublicController {

	public static $ACCOUNT_MODE = ['審核中', '通過', '黑名單', '停用'];
	public function __construct(){
		parent::__construct(request()->instance());

		self::$ACCOUNT_MODE = [
			LANG_MENU['審核中'], 
			LANG_MENU['通過'], 
			LANG_MENU['黑名單'], 
			LANG_MENU['停用']
		];
	}

	public function login() {
		return $this->fetch('login');
	}

	/*一般登入*/
	public function pagelogin() {

		$email = Request::instance()->post('email');
		$password = Request::instance()->post('password');
		
		$rule = [
			'email'  => 'require',
			'password' => 'require'
		];
		$msg = [
			'email.require' => LANG_MENU['帳號不得為空'],
			'password.require' => LANG_MENU['密碼不得為空']
		];
		$validate = new Validate($rule,$msg);
		$data = [
			'email'  => $email,
			'password' => $password
		];
		if (!$validate->check($data)) {
			$this->error($validate->getError());
		}

		$where = [
			'email' => $email
		];
		$adminData = Db::connect(config('main_db'))->table('account')->where($where)->limit(1)->select();
		if($adminData){
			if($adminData[0]['pwd'] == md5($password)){
				if($adminData[0]['status'] == '0'){
					$this->error(LANG_MENU['請先去到您的MAIL收取驗証信 點擊連結後帳號才會開通喔！']);
				}else if($adminData[0]['status'] == '3'){
					$this->error(LANG_MENU['帳號為審核中或停用，無法登入']);
				}

				Session::set('user', $adminData[0]);
				$this->success(LANG_MENU['操作成功'], url('Member/member')); /*登入成功*/

			}else{
				$this->error(LANG_MENU['內容有誤']); /*密碼錯誤*/
			}
		}else{
			$this->error(LANG_MENU['內容有誤']); /*$email.'帳號不存在'*/
		}
	}

	/*登出*/
	public function logout() {
		Session::delete('user');
		Session::delete('cart');
		$this->redirect(MAIN_WEB_LAN);
	}

	/*註冊畫面*/
	public function signup() {
		$city = Db::table('city')->select();
		$this->assign('city', $city);

		$consent = Db::table('consent')->find();
		$this->assign('consent', $consent);

		return $this->fetch('signup');
	}
	
	/*隱私政策畫面*/
	public function privacy_rule() {
		$consent = Db::table('consent')->find();
		$this->assign('consent', $consent);
		return $this->fetch('privacy_rule');
	}

	/*建立註冊資料*/
	public function dosignup() {
		$name = Request::instance()->post('name');
		$password = Request::instance()->post('password');
		$passwordB = Request::instance()->post('passwordB');
		$email = Request::instance()->post('email');
		$mobile = Request::instance()->post('mobile');
		$tel = Request::instance()->post('tel');
		$birthday = Request::instance()->post('birthday');
		$term = Request::instance()->post('term');
		$F_S_NH_Address = Request::instance()->post('F_S_NH_Address');
		$F_I_CNo = Request::instance()->post('F_I_CNo');
		$F_I_TNo = Request::instance()->post('F_I_TNo');
		$F_S_NH_Zip	= Request::instance()->post('F_S_NH_Zip');

		$rule = [
			'name'  => 'require',
			'password' => 'require',
			'email' => 'require|email',
			'mobile' => 'require|number',
			'term' => 'accepted',
			'F_S_NH_Address' => 'require',
			// 'F_I_CNo' => 'require',
			// 'F_I_TNo' => 'require',
			// 'F_S_NH_Zip' => 'require'
		];

		$msg = [
			'name.require' => LANG_MENU['名稱不得為空'],
			'email.email' => LANG_MENU['帳號格式錯誤'],
			'email.require' => LANG_MENU['帳號不得為空'],
			'mobile.require' => LANG_MENU['手機不得為空'],
			'mobile.number' => LANG_MENU['手機只能是數字'],
			'term.accepted' => LANG_MENU['請確認已閱讀，並同意遵守本網站之會員條款'],
			'F_S_NH_Address.require' => LANG_MENU['地址不得為空'],
			// 'F_I_CNo.require' => LANG_MENU['請選擇縣市'],
			// 'F_I_TNo.require' => LANG_MENU['請選擇區'],
			// 'F_S_NH_Zip.require' => LANG_MENU['請確認郵遞區號有填寫'],
		];

		$validate = new Validate($rule,$msg);
		$validate->rule('regex', '/^.[A-Za-z0-9]+$/');
		$data = [
			'password' => $password,
			'name' => $name,
			'email' => $email,
			'mobile' => $mobile,
			'term' => $term,
			'F_S_NH_Address' => $F_S_NH_Address,
			// 'F_I_CNo' => $F_I_CNo,
			// 'F_I_TNo' => $F_I_TNo,
			// 'F_S_NH_Zip' => $F_S_NH_Zip
		];

		if ($validate->check($data)) {
			if(!preg_match("/^[A-Za-z0-9]{6,14}$/", $password)){
				$this->error($email . LANG_MENU['密碼需包含英文及數字']);
			}
			$where = [
				'email' => $email
			];
			$adminData = Db::connect(config('main_db'))->table('account')->where($where)->limit(1)->select();
			if($adminData){
				$this->error($email . LANG_MENU['帳號已被使用']);
			}else{
				if( preg_match('/([0-9]+)/' ,$password) && preg_match('/([a-zA-Z]+)/' ,$password)){

					if($passwordB == $password){
						$count = $this -> getMemberNumber();
						if($F_I_CNo != '' && $F_I_TNo !='' && $F_S_NH_Zip !=''){
							$home = $F_I_CNo.'<fat>'.$F_I_TNo.'<fat>'.$F_S_NH_Zip.'<fat>'.$F_S_NH_Address;
						}else{
							$home = $F_S_NH_Address;
						}
						$newData = [
							'pwd' => md5($password),
							'name' => $name,
							'email' => $email,
							'phone' => $mobile,
							'tele' => $tel == '' ? null : 0,
							'birthday' => $birthday == '' ? null : strtotime($birthday),
							'home' => $home,
							'createtime' => time()
						];
						$newData['number'] = config('subDeparment') . 'US' . date('Ymd') . $count;
						$globalMailData = parent::getMailData();

						$do_signup_letter = LANG_MENU['註冊消費者信'];
            			$do_signup_letter = str_replace("{name}", $name, $do_signup_letter);
            			$do_signup_letter = str_replace("{mailFromName}", $globalMailData['mailFromName'], $do_signup_letter);
						$verify_url = "http://".$_SERVER['HTTP_HOST']."/index/login/signcheck.html?id=".$email;
            			$do_signup_letter = str_replace("{verify_url}", $verify_url, $do_signup_letter);
						$mailBody = "
								<html>
									<head></head>
									<body>
										".$do_signup_letter."
										<center></center>
										<div>
											". $globalMailData['system_email']['signup_complete'] ."
										</div>
									</body>
								</html>
								";

						$mail_return = parent::Mail_Send($mailBody,'client',$email,LANG_MENU['註冊通知']);
						if ($mail_return) {
							$mailBody = "
								<html>
									<head></head>
									<body>
										<div>
											親愛的<u>&nbsp;&nbsp;".$name."&nbsp;&nbsp;</u>，您好:<br>
											歡迎您加入成為".$globalMailData['mailFromName']."會員
											<br><br><br><br>
											<center></center>
										</div>
										<div>
											". $globalMailData['system_email']['signup_complete'] ."
										</div>
									</body>
								</html>
							";
							$mail_return = parent::Mail_Send($mailBody,'admin','','新會員註冊通知');
							Db::connect(config('main_db'))->table('account')->insert($newData);

							$this->redirect('Login/signup_complete');
							$this->success('註冊成功，已寄出註冊信', 'Login/signup');
						}else{
							$this->error(LANG_MENU['驗證信寄送有誤']);// 输出错误信息
						}
						
					}else{
						$this->error(LANG_MENU['密碼不一致']);
					}
				}else{
					$this->error(LANG_MENU['密碼需包含英文及數字']);
				}
			}
		}else{
			$this->error($validate->getError());
		}
	}
	public function signup_complete(){
		return $this->fetch('signup_complete');
	}

	/*激活帳號*/
	public function signcheck() {
		$name = Request::instance()->get('id');
		if(Db::connect(config('main_db'))->table('account')->where('email', $name)->update(['status' => '1'])){
			$this->success(LANG_MENU['操作成功'], 'index/index');
		}else{
			$this->success(LANG_MENU['內容有誤'], 'index/index');
		}
	}

	/*寄送忘記密碼信*/
	public function forgot_form(){
		$name = Request::instance()->post('account');
		$main=Db::connect('main_db')->table('account')->where('email', $name)->select();
		$oekd = Request::instance()->param('oekd');
		$globalMailData = parent::getMailData();

		if(!empty($main)){
			$email=$main[0]['email'];
			$time=base64_encode(time());

			$forget_password_letter = LANG_MENU['密碼重設消費者信'];
			$forget_password_letter = str_replace("{name}", $name, $forget_password_letter);
			$forget_password_letter = str_replace("{date_time}", date("Y-m-d H:i:s"), $forget_password_letter);
			$forget_password_letter = str_replace("{mailFromName}", $globalMailData['mailFromName'], $forget_password_letter);
			$change_password_url = "http://".$_SERVER['HTTP_HOST']."/index/login/check_forgot.html?id=".base64_encode($email)."&asef=".$time;
			$forget_password_letter = str_replace("{change_password_url}", $change_password_url, $forget_password_letter);
			$mailBody = "
				<html>
					<head>
						<style>
							table, th{
							    border: 1px solid #c7c5c5;
							    border-collapse: collapse;
									background-color: #fffbed;
							}
							td{
								border: 0px solid black;
								border-collapse: collapse;
								vertical-align:middle;
							}
						</style>
					</head>
					<body>
						".$forget_password_letter."
						<div>
							". $globalMailData['system_email']['forget_password'] ."
						</div>
					</body>
				</html>
			";
			// dump($mailBody);exit;
			$mail_return = parent::Mail_Send($mailBody,'client',$email,LANG_MENU['密碼重設通知']);
			$oekd = $email;
		}

		if(!empty($main)){ $main=$oekd; }

		$this->assign('Service_Tel', Service_Tel);
		$this->assign('main', $main);
		return $this->fetch();
	}

	/*檢查忘記密碼是否還可使用*/
	public function check_forgot(){
		$id = Request::instance()->param('id');
		$asef = Request::instance()->param('asef');
		$asef = base64_decode($asef);

		$now_time=(time()-$asef)/60;
		if($id=='' or $now_time > 30){
			$this->error(LANG_MENU['無此頁面'], url('index/index'));
		}

		$this->assign('id', base64_decode($id));

		return $this->fetch();
	}

	/*透過忘記密碼信更改密碼*/
	public function change_forgot(){
		$id = Request::instance()->post('id');
		$password = Request::instance()->post('password');
		$re_password = Request::instance()->post('re_password');

		if($password != $re_password){
			$this->error(LANG_MENU['密碼不一致']);
		}else if( !preg_match('/([0-9]+)/' ,$password) || !preg_match('/([a-zA-Z]+)/' ,$password) ){
			$this->error(LANG_MENU['密碼需包含英文及數字']);
		}else{
			if(Db::connect('main_db')->table('account')->where('email', $id)->update(['pwd'=> md5($password)])){
				$this->success(LANG_MENU['操作成功'], 'index/index');
			}else{
				$this->error(LANG_MENU['您輸入的密碼與原密碼相同，請重新輸入']);
			}
		}
	}

	/*google 登入*/
	public function g_login(){
		// Get and decode the POST data
		header("Content-Type: application/json; charset=UTF-8");
		$userEmail = $_POST['U3'];
		$userName = $_POST['ig'];
		$open = $_POST['open'];
		$count =  $this -> getMemberNumber();

		if(!empty($userEmail) && $open != 1 ){
			if(empty($adminData = Db::connect(config('main_db'))->table('account')->where("email = '".$userEmail."'")->select())){
				if(empty(Db::connect(config('main_db'))->table('account')->where("gmail = '".$userEmail."'")->select())){
					$newData = [
						'name' => $userName,
						//'email' => $email,
						'gmail' => $userEmail,
						'phone' => '',
						'birthday' => '',
						'home' => '',
						'status' => '1',
						'createtime' => time(),
						'number' => config('subDeparment') . 'US' . date('Ymd') . $count
					];
					//dump($newData);
					Db::connect(config('main_db'))->table('account')->insert($newData);
				}

				$adminData = Db::connect(config('main_db'))->table('account')->where("gmail = '".$userEmail."'")->limit(1)->select();
				if($adminData){
					if($adminData[0]['status'] == '0' || $adminData[0]['status'] == '3'){
						$this->error(LANG_MENU['此帳號無法登入，請連繫客服人員']);
					}
					Session::set('user', $adminData[0]);
					//Session::set('cart','[]');
					return true;

				}else{
					$this->error(LANG_MENU['內容有誤']);
				}
				//$this->success('登入成功', 'Index/index');
			}else{
				Db::connect(config('main_db'))->table('account')->where("email = '".$userEmail."'")->update(['gmail' => $userEmail]);
				$adminData = Db::connect(config('main_db'))->table('account')->where("gmail = '".$userEmail."'")->limit(1)->select();
				if($adminData){
					if($adminData[0]['status'] == '0' || $adminData[0]['status'] == '3'){
						$this->error(LANG_MENU['此帳號無法登入，請連繫客服人員']);
					}	
					Session::set('user', $adminData[0]);
					//Session::set('cart','[]');
					return true;
				}else{
					$this->error(LANG_MENU['內容有誤']);
				}
					//$this->success('登入成功', 'Index/index');
			}
		}else{//做開通
			if(Db::connect(config('main_db'))->table('account')->where("gmail = '".$userEmail."' and email IS NOT NULL ")->limit(1)->select()){
				return LANG_MENU['內容有誤'];
				// return '此google已註冊';

			}else if(empty(Db::connect(config('main_db'))->table('account')->where("gmail = '".$userEmail."'")->limit(1)->select())){//都沒有直接加
				Db::connect(config('main_db'))->table('account')->where("email = '".$this->user['email']."'")->update(['gmail' => $userEmail]);
				return true;
			}else{
				$aa = Db::connect(config('main_db'))->table('account')->where("email = '".$this->user['email']."'")->select()[0];
				$user_new = Db::connect(config('main_db'))->table('account')->where("gmail = '".$userEmail."'")->select()[0];
				Db::table('coupon_pool')->where("owner = '".$user_new['id']."'")->update(['owner' =>$aa['id']]);//優待券合併
				Db::connect(config('main_db'))->table('orderform')->where("user_id = '".$user_new['id']."'")->update(['user_id' =>$aa['id']]);//訂單合併

				Db::connect(config('main_db'))->table('account')->where("email = '".$aa['email']."'")//合併
				->update([
					'point' =>$aa['point']+$user_new['point'],
					'total' =>$aa['total']+$user_new['total'],		
					'ordernum' =>$aa['ordernum']+$user_new['ordernum'],
					'gmail' => $user_new['gmail']
					]);
				Db::table('excel')->where("account_number = '".$user_new['id']."'")->update(['account_number' =>$aa['id']]);//商品註冊
					Db::connect(config('main_db'))->table('account_log')//紀錄
					->insert([
						'original_id'=>$aa['id'],
						'original_point'=>$aa['point'],
						'original_total'=>$aa['total'],
						'original_ordernum'=>$aa['ordernum'],
						'aim_id'=>$user_new['id'],
						'aim_point'=>$user_new['point'],
						'aim_total'=>$user_new['total'],
						'aim_ordernum'=>$user_new['ordernum']
						]);

						Db::connect(config('main_db'))->table('account')->delete($user_new['id']);//合併完刪除
				return true;
			}
		}
	}
	/*商城帳號綁定google*/
	public function g_login_2(){
		dump(Session::set("user.gmail",'asdfasdf'));
		dump(Session::get("user"));

		// Get and decode the POST data
		header("Content-Type: application/json; charset=UTF-8");
		$userData = $_POST['userData'];

		if(!empty($userData)){
			$email = !empty($userData)?$userData:'';
			if(!empty(Db::connect(config('main_db'))->table('account')->where("gmail = '".$email."'")->select())){
				return false;
			}else{
				$newData = [
					'gmail' => $email,
				];
				//dump($newData);
				Db::connect(config('main_db'))->table('account')->where("email = '".$this->user['email']."'")->update($newData);		
				Session::set("user.gmail",$email);
				return LANG_MENU['內容有誤'];
			}		
		}
	}

	/*facebook 登入*/
	public function FB_login(){
		// Get and decode the POST data
		header("Content-Type: application/json; charset=UTF-8");
		$userEmail = $_POST['U3'];
		$userName = $_POST['ig'];
		if(!empty($userEmail)){
			if(empty(Db::connect(config('main_db'))->table('account')->where("FB_id = '".$userEmail."'")->select())){
				$count =  $this -> getMemberNumber();
				$newData = [
					'name' => $userName,
					//'email' => $email,
					'FB_id' => $userEmail,
					'phone' => '',
					'birthday' => '',
					'home' => '',
					'status' => '1',
					'createtime' => time(),
					'number' => config('subDeparment') . 'US' . date('Ymd') . $count
				];
				//dump($newData);
				Db::connect(config('main_db'))->table('account')->insert($newData);
			}

			$adminData = Db::connect(config('main_db'))->table('account')->where("FB_id = '".$userEmail."'")->limit(1)->select();
			if($adminData){
				if($adminData[0]['status'] == '0' || $adminData[0]['status'] == '3'){
						$this->error(LANG_MENU['此帳號無法登入，請連繫客服人員']);
				}
				Session::set('user', $adminData[0]);
				//Session::set('cart','[]');
				return true;
			}else{
				$this->error(LANG_MENU['內容有誤']);
			}								
		}	
	}
	/*商城帳號綁定 facebook*/
	public function FB_open(){
		$userEmail = $_POST['U3'];
		$userName = $_POST['ig'];
		if(Db::connect(config('main_db'))->table('account')->where("FB_id = '".$userEmail."' and email IS NOT NULL ")->limit(1)->select()){
			return LANG_MENU['內容有誤'];

		}else if(empty(Db::connect(config('main_db'))->table('account')->where("FB_id = '".$userEmail."'")->limit(1)->select())){//都沒有直接加
			$aa = Db::connect(config('main_db'))->table('account')->where("email = '".$this->user['email']."'")->update(['FB_id' => $userEmail]);
			return true;

		}else{
			//$aa = Db::connect(config('main_db'))->table('account')->where("email = '".$this->user['email']."'")->update(['FB_id' => $userEmail]);
			$aa = Db::connect(config('main_db'))->table('account')->where("email = '".$this->user['email']."'")->select()[0];
			$user_new = Db::connect(config('main_db'))->table('account')->where("FB_id = '".$userEmail."'")->select()[0];
			Db::table('coupon_pool')->where("owner = '".$user_new['id']."'")->update(['owner' =>$aa['id']]);//優待券合併
			Db::connect(config('main_db'))->table('orderform')->where("user_id = '".$user_new['id']."'")->update(['user_id' =>$aa['id']]);//訂單合併
			Db::connect(config('main_db'))->table('account')->where("email = '".$aa['email']."'")//合併
			->update([
				'point' =>$aa['point']+$user_new['point'],
				'total' =>$aa['total']+$user_new['total'],		
				'ordernum' =>$aa['ordernum']+$user_new['ordernum'],
				'FB_id' => $user_new['FB_id']
			]);

			Db::table('excel')->where("account_number = '".$user_new['id']."'")->update(['account_number' =>$aa['id']]);//商品註冊

			Db::connect(config('main_db'))->table('account_log')//紀錄
			->insert([
				'original_id'=>$aa['id'],
				'original_point'=>$aa['point'],
				'original_total'=>$aa['total'],
				'original_ordernum'=>$aa['ordernum'],
				'aim_id'=>$user_new['id'],
				'aim_point'=>$user_new['point'],
				'aim_total'=>$user_new['total'],
				'aim_ordernum'=>$user_new['ordernum']
			]);

			Db::connect(config('main_db'))->table('account')->delete($user_new['id']);//合併完刪除
			return true;
		}
	}

	/*line 登入*/
	public function line_login(){
		// Get and decode the POST data
		header("Content-Type: application/json; charset=UTF-8");
		$userEmail = $_POST['U3'];
		$userName = $_POST['ig'];
		if(!empty($userEmail)){
			if(empty(Db::connect(config('main_db'))->table('account')->where("line_id = '".$userEmail."'")->select())){
				$count =  $this -> getMemberNumber();
				$newData = [
					'name' => $userName,
					//'email' => $email,
					'line_id' => $userEmail,
					'phone' => '',
					'birthday' => '',
					'home' => '',
					'status' => '1',
					'createtime' => time(),
					'number' => config('subDeparment') . 'US' . date('Ymd') . $count
				];
				//dump($newData);
				Db::connect(config('main_db'))->table('account')->insert($newData);
			}

			$adminData = Db::connect(config('main_db'))->table('account')->where("line_id = '".$userEmail."'")->limit(1)->select();

			if($adminData){
				if($adminData[0]['status'] == '0' || $adminData[0]['status'] == '3'){
					$this->error(LANG_MENU['此帳號無法登入，請連繫客服人員']);
				}
				Session::set('user', $adminData[0]);
				//Session::set('cart','[]');
				return true;
			}else{
				$this->error(LANG_MENU['內容有誤']);
			}
		}}
	/*商城帳號綁定 line*/
	public function line_open(){

		$userEmail = $_POST['U3'];
		$userName = $_POST['ig'];
		if(Db::connect(config('main_db'))->table('account')->where("line_id = '".$userEmail."' and email IS NOT NULL ")->limit(1)->select()){
			return LANG_MENU['內容有誤'];

		}else if(empty(Db::connect(config('main_db'))->table('account')->where("line_id = '".$userEmail."'")->limit(1)->select())){//都沒有直接加
			Db::connect(config('main_db'))->table('account')->where("email = '".$this->user['email']."'")->update(['line_id' => $userEmail]);
			return true;
		}else{
			//Db::connect(config('main_db'))->table('account')->where("email = '".$this->user['email']."'")->update(['line_id' => $userEmail]);
			$aa = Db::connect(config('main_db'))->table('account')->where("email = '".$this->user['email']."'")->select()[0];
			$user_new = Db::connect(config('main_db'))->table('account')->where("line_id = '".$userEmail."'")->select()[0];

			Db::table('coupon_pool')->where("owner = '".$user_new['id']."'")->update(['owner' =>$aa['id']]);//優待券合併
			Db::connect(config('main_db'))->table('orderform')->where("user_id = '".$user_new['id']."'")->update(['user_id' =>$aa['id']]);//訂單合併

			Db::connect(config('main_db'))->table('account')->where("email = '".$aa['email']."'")//合併
			->update([
				'point' =>$aa['point']+$user_new['point'],
				'total' =>$aa['total']+$user_new['total'],		
				'ordernum' =>$aa['ordernum']+$user_new['ordernum'],
				'line_id' => $user_new['line_id']
			]);

			Db::table('excel')->where("account_number = '".$user_new['id']."'")->update(['account_number' =>$aa['id']]);//商品註冊

			Db::connect(config('main_db'))->table('account_log')//紀錄
			->insert([
				'original_id'=>$aa['id'],
				'original_point'=>$aa['point'],
				'original_total'=>$aa['total'],
				'original_ordernum'=>$aa['ordernum'],
				'aim_id'=>$user_new['id'],
				'aim_point'=>$user_new['point'],
				'aim_total'=>$user_new['total'],
				'aim_ordernum'=>$user_new['ordernum']
			]);

			Db::connect(config('main_db'))->table('account')->delete($user_new['id']);//合併完刪除

			return true;
		}
	}

	/*社群帳號綁定(合併) 商城帳號*/
	public function ck_number(){
		$id = $_POST['id'];
		$pw = $_POST['pw'];
		$user_new = $_POST['user_new'];
		$aim = $_POST['aim'];

		if(!empty($id)){
			if(!empty($aa = Db::connect(config('main_db'))->table('account')->where("email = '".$id."'")->select())){
				$aa = $aa[0];
				if($aa[$aim] == $user_new){
					return LANG_MENU['內容有誤'];
					// '此會員已註冊';
					exit;
				}
				if($aa[$aim] != ''){
					return LANG_MENU['內容有誤'];
					// '此會員已開通';
					exit;
				}
				if($aa['pwd'] == md5($pw)){
					$user_new = Db::connect(config('main_db'))->table('account')->where("$aim = '".$user_new."'")->select()[0];
					Db::table('coupon_pool')->where("owner = '".$user_new['id']."'")->update(['owner' =>$aa['id']]);//優待券合併
					Db::connect(config('main_db'))->table('orderform')->where("user_id = '".$user_new['id']."'")->update(['user_id' =>$aa['id']]);//訂單合併

					Db::connect(config('main_db'))->table('account')->where("email = '".$id."'")//合併
					->update([
						'point' =>$aa['point']+$user_new['point'],
						'total' =>$aa['total']+$user_new['total'],		
						'ordernum' =>$aa['ordernum']+$user_new['ordernum'],
						$aim => $user_new[$aim]
					]);

					Db::table('excel')->where("account_number = '".$user_new['id']."'")->update(['account_number' =>$aa['id']]);//商品註冊

					Db::connect(config('main_db'))->table('account_log')//紀錄
					->insert([
						'original_id'=>$aa['id'],
						'original_point'=>$aa['point'],
						'original_total'=>$aa['total'],
						'original_ordernum'=>$aa['ordernum'],
						'aim_id'=>$user_new['id'],
						'aim_point'=>$user_new['point'],
						'aim_total'=>$user_new['total'],
						'aim_ordernum'=>$user_new['ordernum']
					]);

					Db::connect(config('main_db'))->table('account')->delete($user_new['id']);//合併完刪除
					return 1;
				}else{
					return LANG_MENU['內容有誤'];
					// '密碼錯誤'
				}
			}else{
				return LANG_MENU['內容有誤'];
				// '找不到帳密'
			}
		}
	}


	public function Town_ajax(){
		if(!empty($city = Db::table('town')->where("CNo = '".$_POST["CNo"]."'")->select())){
			foreach( $city as $k => $v){
				echo "<option value='".$v['AutoNo']."'>".$v['Name']."</option>";
			}
		}else{
			echo "<option value=''>".LANG_MENU['請選擇區']."</option>";
		}
	}
	public function ZIP_ajax(){	

		if(!empty($town = Db::table('town')->where("AutoNo = '".$_POST["TNo"]."'")->select())){
			echo $town[0]['Post'];
		}else{
			echo LANG_MENU['內容有誤'];
		}
	}

	/*生成會員編號*/
	public function getMemberNumber(){
		$count = Db::connect(config('main_db'))->table('account')->where('number like "'.config('subDeparment').'US'.date('Ymd').'%"')->order('id desc')->find();
		$count = $count ? intval(substr($count['number'],-3)) + 1 : 1;
		if($count < 10){
			$count = '00' . $count;
		}else if($count < 100){
			$count = '0' . $count;
		}

		return $count;
	}
}

