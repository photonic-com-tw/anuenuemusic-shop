<?php
namespace app\order\controller;

use think\Controller;
use think\Session;
use think\Config;
use think\Request;
use think\Db;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use controllerInterface\parentFunction\Auth;
use controllerInterface\parentFunction\ErrorProcess;

class MainController extends Controller implements Auth, ErrorProcess {

	protected $user;

	public function __construct() {
		parent::__construct();

		if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', ROOT_PATH . 'public' . DS . 'static' . DS . 'index' . DS . 'upload');

		//auth system
		$this->auth();

		/*功能是否啟用*/
		$this->assign('POINT_DISCOUNT', POINT_DISCOUNT);		/*點數*/
		$this->assign('NOTIFICATION', NOTIFICATION);			/*推播消息*/
		$excel = Db::connect('main_db')->table('excel')->select();
		$this->assign('PointDuration', $excel[1]['value1']);	/*點數到期週期(年)*/
		$this->assign('FirstBuyDiscount', $excel[18]['value1']);/*首購優惠*/
		$this->assign('VipDiscount', $excel[19]['value1']);		/*VIP等級*/



		$c = request()->controller();
		$a = request()->action();
		// var_dump($c);

		$target = '';
		$sec_ative = '';

		// auto unfold menu
		if( $c == 'Index' ){
			$target = 'memberList';
			$url_list = explode('status/', $_SERVER['REQUEST_URI']);
			$sec_ative = count($url_list) > 1 ? 'member_'.explode('.', $url_list[1])[0] : 'member';
		}else if( $c == 'OrderCtrl' ){
			$target = 'orderList';
			$url_list = explode('state/', $_SERVER['REQUEST_URI']);
			$sec_ative = count($url_list) > 1 ? 'order_'.explode('.', $url_list[1])[0] : 'member';
		}else if( $c == 'Productqa' ){
			$target = 'serviceList';
			$sec_ative = $c.'_'.$a;
		}else if( $c == 'Admin' || $c == 'Fee' ){
			$target = 'settingList';
			$sec_ative = $c.'_'.$a;
		}

		$this->assign('target',$target);
		$this->assign('sec_ative', $sec_ative);
	}

	public function auth() {
		$this->user = Session::get('admin');
		// dump($_GET);
		if($this->user == null){
			if( isset($_GET['acc']) && isset($_GET['pwd']) ){
				$admin = Db::table('admin')->where('account',$_GET['acc'])->where('password', $_GET['pwd'])->select();
				if($admin){
					$this->user = $admin[0];
				}else{
					$this->redirect('/admin/Login/index');
				}
			}else{
				$this->redirect('/admin/Login/index');
			}
		}

		// dump($this->user);
		$this->assign('user', $this->user);
	}

	public function dumpException(\Exception $e) {
		if(Config::get('app_debug')){
			$this->assign('waitSecond', 5);
			$this->error('操作失敗：' . $e->getMessage());
		}else{
			$this->assign('waitSecond', 1);
			$this->error('操作失敗，請洽管理員');
		}
	}

	public function dumpError($errorMessage) {
		if(Config::get('app_debug')){
			$this->assign('waitSecond', 5);
		}else{
			$this->assign('waitSecond', 1);
		}
		$this->error('操作失敗：' . $errorMessage);
	}
}