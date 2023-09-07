<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Validate;
use think\Db;
use think\Session;

class Login extends Controller
{
	public function index()
	{
		$admin_info = Db::table('admin_info')->find(1);
		$this->assign('admin_info', $admin_info);
		return $this->fetch('login');
	}

	public function loginAction()
	{
		$admin_info = Db::table('admin_info')->find(1);
		$this->assign('admin_info', $admin_info);

		$account = Request::instance()->post('account');
		$password = Request::instance()->post('password');
		$auth = Request::instance()->post('auth');

		$rule = [
			'account'  => 'require',
			'password' => 'require'
		];

		$msg = [
			'account.require' => '帳號不得為空',
			'password.require' => '密碼不得為空'
		];

		$validate = new Validate($rule,$msg);
		$data = [
			'account'  => $account,
			'password' => $password
		];

		if (!$validate->check($data)) {
			$this->error($validate->getError());
		}

		$where = [
			'account' => $account
		];
		$adminData = Db::table('admin')->where($where)->limit(1)->select();

		if($adminData){
			if($adminData[0]['password'] == md5($password)){
				include_once('./extend/thirdparty/GoogleAuthenticator.php');
				$ga = new \PHPGangsta_GoogleAuthenticator();
				$checkResult = $ga->verifyCode('AJVROPCJEJYGCIAG', $auth, 2);    // 2 = 2*30sec clock tolerance

				// if (!$checkResult) {
				// 	$this->error('驗證碼錯誤');
				// }


				// $auto_logout = Db::connect(config('main_db'))->table('excel')->where('id = 5')->find();
				// session_start();
				// $lifeTime = $auto_logout['value1'];
				// setcookie(session_name(), session_id(), time() + $lifeTime, '/');

				Session::set('admin', $adminData[0]);
				$this->success('登入成功', 'All/index');
				// $this->success('登入成功', 'Index/index');
			}else{
				$this->error('密碼錯誤');
			}
		}else{
			$this->error($account . '帳號不存在');
		}
	}

	public function logout()
	{
		Session::delete('admin');
		$this->redirect('Login/index');
	}

	public function renew_coockie(){
		$admin_data = Session::get('admin');
		if( !empty($admin_data) ){
			$auto_logout = Db::connect(config('main_db'))->table('excel')->where('id = 5')->find();
			$lifeTime = $auto_logout['value1'];
			setcookie(session_name(), session_id(), time() + $lifeTime, '/');

			Session::set('admin', $admin_data);
			return 'yes';
		}else{
			return 'no';
		}
	}

	public function renew_session(){
		$admin_data = Session::get('admin');
		if( !empty($admin_data) ){
			Session::set('admin', $admin_data);
			return 'yes';
		}else{
			return 'no';
		}
	}
}

