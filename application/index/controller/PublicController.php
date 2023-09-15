<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use pattern\recursiveCorrdination\cartRC\Proposal;
use pattern\recursiveCorrdination\cartRC\MemberFactory;
use app\index\controller\Product;

class PublicController extends Controller{
	
	public $control_register;	// 是否使用報名功能
	public $ecpay_card; 		// 是否使用綠界金流
	public $ecpay_logistic; 	// 是否使用綠界物流
	public $tspg_card; 			// 是否使用台新金流
	static public $closeFunction;	// 紀錄前台哪些關閉功能

	public function __construct(Request $request)
	{
		parent::__construct();
		foreach ($_POST as $key => $value) {
			$_POST[$key] = param_filter($value);
		}
		foreach ($_GET as $key => $value) {
			$_GET[$key] = param_filter($value);
		}

		/***版面控制***/
		$controller = strtolower($request->controller());
		$module = $request->module();
		$action = $request->action();

		$purvuew = Db::table('admin')->field('purview')->where("permission = 'current'")->find()['purview'];
		$blokc = Db::table('backstage_menu_second')->field('id,name,Front_desk,backstage_menu_id')->select();
		$purvuew = json_decode($purvuew,true);
		$Front_desk = [];
		$Front_name = [];

		$ck = 0;
		foreach($blokc as $k => $v){
			if(!empty($purvuew[$v['backstage_menu_id']])){
				foreach($purvuew[$v['backstage_menu_id']] as $pk => $pv){
					if($v['id'] == $pv){
						$Front_desk[$v['Front_desk']]=1;
						$Front_name[$v['name']]=1;
					}
				}
			}
		}

		/*
		dump($Front_desk);//de_bug 檢查被隱藏位置
		dump($Front_name);//de_bug 檢查被隱藏位置名稱
		*/

		$front_desk_str = $module.'/'.$controller.'/'.$action;
		if(!empty($Front_desk[$front_desk_str])){
			$ck = 1;
		}

		if($ck == 1){
			$this->redirect(url('Login/signup'));
			// $this->redirect(MAIN_WEB_LAN);
			$this->error(LANG_MENU['無此頁面']);
		}

		$this->assign('Front_name',$Front_name);
		$this->Front_name = $Front_name;
		self::$closeFunction = $Front_name;
		/***版面控制***/



		//auth system
		$this->user = Session::get('user');
		$this->userId = $this->user['id'] ? $this->user['id'] : '0';

		// if($this->user != null){
			$this->assign('login', 'login');
			$this->assign('user', $this->user);
			$Proposal = Proposal::withTeamMembersAndRequire(
				['GetCartData'],
				['user_id' => $this->userId]
			);

			$Proposal = MemberFactory::createNextMember($Proposal);

			/* 檢查購物車 */
			$check_cart = json_decode($Proposal->projectData['data'],true);

			$check_cart = json_encode($check_cart);
			Session('cart',$check_cart);

			$Proposal->projectData['data'] = 	$check_cart;
			/*檢查購物車 */

			$this->assign('cartCount', sizeof(json_decode($Proposal->projectData['data'], true)));
		// }else{
		// $this->assign('login', 'nologin');
		// }
		//index page block on off

		// 檢查網紅登入狀態
		$this->kol = Session::get('kol');
		if($this->kol){
			$this->assign('kol_logined', 'true');
		}else{
			$this->assign('kol_logined', 'false');
		}

		$index_online = Db::table('index_online')->find(1);
		$index_excel = Db::table('index_excel')->order('id')->select();
		$index_excel[15]['product'] =
			Db::table('productinfo')->field('title, content, id, pic, has_price, id')
			->where('online = 1')
			->find($index_excel[15]['data3']);

		if($index_excel[15]['product']){
			if ($index_excel[15]['product']['has_price'] == 1){

				$price = $price = Product::get_price($index_excel[15]['product']['id']);
				if(!empty($price)){
					$index_excel[15]['product']['subtitle'] = $price[0]['title'];
					$index_excel[15]['product']['has_price'] = $price[0]['count'];
				}else{
					$index_excel[15]['product']['subtitle'] = '';
					$index_excel[15]['product']['has_price'] = LANG_MENU['請詢價'];
				}

			}else{
				$index_excel[15]['product']['subtitle'] = '';
				$index_excel[15]['product']['has_price'] = LANG_MENU['請詢價'];
			}
			$index_excel[15]['product']['pic'] = json_decode($index_excel[15]['product']['pic'],true)[0];
		}

		$index_excel[16]['product'] = 
			Db::table('productinfo')->field('title, content, id, pic, has_price, id')
			->where('online = 1')
			->find($index_excel[16]['data3']);

		if($index_excel[16]['product']) {
			if ($index_excel[16]['product']['has_price'] == 1){

				$price = Product::get_price($index_excel[16]['product']['id']);
				if(!empty($price)){
					$index_excel[16]['product']['subtitle'] = $price[0]['title'];
					$index_excel[16]['product']['has_price'] = $price[0]['count'];
				}else{
					$index_excel[16]['product']['subtitle'] = '';
					$index_excel[16]['product']['has_price'] = LANG_MENU['請詢價'];
				}		

			} else {
				$index_excel[16]['product']['subtitle'] = '';
				$index_excel[16]['product']['has_price'] = LANG_MENU['請詢價'];
			}
			$index_excel[16]['product']['pic'] = json_decode($index_excel[16]['product']['pic'],true)[0];
		}

		$this->assign('index_online', $index_online);
		//dump($index_excel);

		$this->assign('index_excel', $index_excel);
		
		/*LINE*/
		$this->assign('client_secret', client_secret);
		$this->assign('client_id', client_id);
		$this->assign('line_url', line_url);

		/*Google*/
		$this->assign('Google_appId', Google_appId);

		/*FB*/
		$this->assign('FB_appID', FB_appID);

		$seo = Db::table('seo')->select();
		$this->assign('seo', $seo);

		$admin_info = Db::table('admin_info')->find(1);
		$this->assign('admin_info', $admin_info);
		/****************************/
		//*********andy
		/****************************/

		/*edm網址*/
		$this->assign('EDM_URL', EDM_URL);

		$distribution = Db::table('stronghold')->field('id')->order('order_id asc, id desc')->select();
		if(!$distribution){
			$distribution[0] = ['id'=>0];
		}
		$this->assign('distribution', $distribution[0]);

		$excel = Db::table('excel')->group('product_name')->select();
		$this->assign('excel', $excel);

		$closeAdSide = Session::get('closeAdSide');
		$this->assign('closeAdSide', $closeAdSide);

		$frontend_menu_name= Db::table('frontend_menu_name')->order('id')->select();
		$frontend_menu = [];
		foreach ($frontend_menu_name as $key => $value) {
			$frontend_menu[$value['controller']] = $value;
			$frontend_menu[$value['controller']]['second_menu'] = json_decode($value['second_menu']);
		}
		$this->assign('frontend_menu', $frontend_menu);
		$this->assign('controller', $controller);
		$this->assign('action', $action);
		// dump($frontend_menu);
		// dump($controller);
		// dump($action);

		/* 標籤名稱 */
		$tag = Db::table('frontend_data_name')->where('show_type = "tag"')->order('id asc')->select();
		$this->assign('tag', $tag);

		// 讀取功能設定
		$excel = Db::connect(config('main_db'))->table('excel')->select();
		// 是否啟用報名功能
		$this->control_register = $excel[13]['value1'];
		$this->assign('control_register', $this->control_register);

		// 是否啟用綠界金流
		$this->ecpay_card = $excel[15]['value1'];
		$this->assign('ecpay_card', $this->ecpay_card);
		// 是否啟用綠界物流
		$this->ecpay_logistic = $excel[16]['value1'];
		$this->assign('ecpay_logistic', $this->ecpay_logistic);
		// 是否啟用台新金流
		$this->tspg_card = $excel[17]['value1'];
		$this->assign('tspg_card', $this->tspg_card);

		$this->FirstBuyDiscount = $excel[18]['value1'];
		$this->assign('FirstBuyDiscount', $this->FirstBuyDiscount);

		$this->VipDiscount = $excel[19]['value1'];
		$this->assign('VipDiscount', $this->VipDiscount);

		// 一些通用資訊
		$this->assign('dolar', dolar);
		$this->assign('dolar_mark', dolar_mark);
		$this->assign('Footer_Title', Footer_Title);
		$this->assign('Service_Tel', Service_Tel);
		$this->assign('Service_Tel_A', Service_Tel_A);
		$this->assign('Service_Email', Service_Email);
	}

	public function dumpException(\Exception $e) {
		if(Config::get('app_debug')){
			$this->assign('waitSecond', 5);
			$this->error(LANG_MENU['發生錯誤'].'：' . $e->getMessage());
		}else{
			$this->assign('waitSecond', 1);
			$this->error(LANG_MENU['發生錯誤']); /*'操作失敗，請洽管理員'*/
		}
	}

	public function dumpError($errorMessage) {
		if(Config::get('app_debug')){
			$this->assign('waitSecond', 5);
		}else{
			$this->assign('waitSecond', 1);
		}
		$this->error(LANG_MENU['發生錯誤'].'：' . $errorMessage);
	}

	public function product_species($id) {
		$expiring_product = Db::table('expiring_product')->field('product_id')->where("product_id = '".$id."'")->count();
		$hot_product = Db::table('hot_product')->field('product_id')->where("product_id = '".$id."'")->count();
		$recommend_product = Db::table('recommend_product')->field('product_id')->where("product_id = '".$id."'")->count();
		$spec_product = Db::table('spe_price_product')->field('product_id')->where("product_id = '".$id."'")->count();

		$expiring_product_name='';
		$expiring_product_type=0;
		$hot_product_name='';
		$hot_product_type=0;
		$recommend_product_name='';
		$recommend_product_type=0;
		$spec_product_name='';
		$spec_product_type=0;

		//設定標籤名稱
		$tag = Db::table('frontend_data_name')->where('show_type = "tag"')->order('id asc')->select();

		// 人氣商品
		if($hot_product>0){
			$hot_product_name='<div class="act_hot"><div class="cancel_crown"></div>'.$tag[0]['name'].'</div>';
			$hot_product_type=1;
		}

		// 店長推薦
		if($recommend_product>0){
			$recommend_product_name='<div class="act_rec"><div class="cancel_crown"></div>'.$tag[1]['name'].'</div>';
			$recommend_product_type=1;
		}

		// 即期良品
		if($expiring_product>0){
			$expiring_product_name='<div class="act_exp"><div class="cancel_crown"></div>'.$tag[2]['name'].'</div>';
			$expiring_product_type=1;
		}

		// 特價商品
		if($spec_product>0){
			$spec_product_name='<div class="act_exp"><div class="cancel_crown"></div>'.$tag[3]['name'].'</div>';
			$spec_product_type=1;
		}

		$this->assign('hot_product', $hot_product_name);
		$this->assign('hot_product_type', $hot_product_type);
		$this->assign('recommend_product', $recommend_product_name);
		$this->assign('recommend_product_type', $recommend_product_type);
		$this->assign('expiring_product', $expiring_product_name);
		$this->assign('expiring_product_type', $expiring_product_type);
		$this->assign('spec_product', $spec_product_name);
		$this->assign('spec_product_type', $spec_product_type);
	}

	//andy
	public static function getMailData($config_db=""){
		$seo = Db::connect($config_db)->table('seo')->select();
		$system_email = Db::connect($config_db)->table('system_email')->where("id=1")->select()[0];
		$return_email = Db::table('admin')->field('email')->where("email IS NOT NULL")->select();

		$globalMailData = [
			'mailHost'		 =>	Mail_Host,
			'mailUsername'	 =>	Mail_Username,
			'mailPassword'	 =>	Mail_Password,
			'mailSubject'	 =>	$seo[0]['title'].' '.LANG_MENU['系統信箱'],
			'mailFrom'		 =>	Mail_Username,
			'mailFromName'	 =>	$seo[0]['title'],
			'system_email'	 => $system_email,
			'return_email'   => $return_email,
		];
		return $globalMailData;
	}

	public static function Mail_Send($Body='',$type='admin',$client_email='',$subject=NULL, $config_db=""){
		$globalMailData = self::getMailData($config_db);
		// dump($globalMailData);
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Host = $globalMailData['mailHost'];
		$mail->Port = 25;
		$mail->SMTPAuth = true;
		// $mail->SMTPSecure = "ssl";
		$mail->CharSet = "UTF-8";
		$mail->Encoding = "base64";
		$mail->Username = $globalMailData['mailUsername'];
		$mail->Password = $globalMailData['mailPassword'];
		$mail->Subject = $globalMailData['mailSubject'].' '.$subject;
		$mail->From = $globalMailData['mailFrom'];
		$mail->SMTPAutoTLS = false;
		$mail->FromName = $globalMailData['mailFromName'];
		$mail->Body = $Body;
		$mail->IsHTML(true);
		// dump($mail);
		
		switch ($type) {
			case 'client':
				$mail->AddAddress($client_email);
				break;

			case 'admin':
				foreach($globalMailData['return_email'] as $k => $v){
					$mail->AddAddress($v['email']);
				}
				break;
		}

		if ($mail->Send()){
			return true;
		}else{
			// dump($mail->ErrorInfo);
			// exit;//檢查錯誤
			return false;
		}
	}

	public function isMobileCheck(){
	    //Detect special conditions devices
	    $iPod = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
	    $iPhone = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
	    $iPad = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");

	    if(stripos($_SERVER['HTTP_USER_AGENT'],"Android") && stripos($_SERVER['HTTP_USER_AGENT'],"mobile")){
	        $Android = true;
	    }else if(stripos($_SERVER['HTTP_USER_AGENT'],"Android")){
	        $Android = false;
	        $AndroidTablet = true;
	    }else{
	        $Android = false;
	        $AndroidTablet = false;
	    }

	    $webOS = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
	    $BlackBerry = stripos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
	    $RimTablet= stripos($_SERVER['HTTP_USER_AGENT'],"RIM Tablet");

	    //do something with this information
	    if( $iPod || $iPhone || $iPad || $Android || $AndroidTablet || $webOS || $BlackBerry || $RimTablet){
	        return true;
	    }else{
	        return false;
	    }
	}

	// 文字星號隱藏
	public function hidestr($string, $start = 0, $length = 0, $re = '*') {
	    if (empty($string)) return false;
	    $strarr = array();
	    $mb_strlen = mb_strlen($string);
	    while ($mb_strlen) {//循环把字符串变为数组
	        $strarr[] = mb_substr($string, 0, 1, 'utf8');
	        $string = mb_substr($string, 1, $mb_strlen, 'utf8');
	        $mb_strlen = mb_strlen($string);
	    }
	    $strlen = count($strarr);
	    $begin  = $start >= 0 ? $start : ($strlen - abs($start));
	    $end    = $last   = $strlen - 1;
	    if ($length > 0) {
	        $end  = $begin + $length - 1;
	    } elseif ($length < 0) {
	        $end -= abs($length);
	    }
	    for ($i=$begin; $i<=$end; $i++) {
	        $strarr[$i] = $re;
	    }
	    if ($begin >= $last || $end > $last) return false;
	    return implode('', $strarr);
	}

	public function get_user_home(){
		$home = '';
		if(Session::get('user.home')){
			$home = Session::get('user.home');
			if(strpos($home,"<fat>")){
				$ex = explode("<fat>",$home);
				$ex[0]=Db::table('city')->where(" AutoNo = '".$ex['0']."'")->field('Name')->find()['Name'];
				$ex[1]= Db::table('town')->where(" AutoNo = '".$ex['1']."'")->field('Name')->find()['Name'];
				$home = $ex[2].$ex[0].$ex[1].$ex[3];
			}
		}
		return $home;
	}
}



















































































