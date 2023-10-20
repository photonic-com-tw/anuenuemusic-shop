<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use pattern\simpleFactory\orderFactory\OrderFactory;
use think\Session;
use PHPMailer\PHPMailer\PHPMailer;

use app\index\controller\Tspg;

class Orderform extends PublicController{
	private $order_tableName = 'orderform';
	private $coupon_tableName = 'coupon_pool';

	public function __construct()
    {
    	$Request = Request::instance();
		parent::__construct($Request);
		$this->assign('mebermenu_active', 'orderform');

		$consent = Db::table('consent')->where("id=1")->find();
		$this->assign('consent_other', $consent['other']);
	}


	public function orderform() {
		if(!$this->user){ $this->error(LANG_MENU['請先登入會員'], url('Login/login').'?jumpUri='.$_SERVER['REQUEST_URI']);};

		$orderform = Db::connect('main_db')
				->table('orderform')
				->where([
					'user_id' => $this->user['id'],
					'status' => 'New',
					'show_date' => '1'
				])
				->order('id desc')
				->paginate(10);

		$page = $orderform->render();

		$this->assign('page', $page);

		$this->assign('orderform',$orderform);
		//var_dump($orderform);die();

		return $this->fetch('orderform');
	}


	public function history() {
		if(!$this->user){ $this->error(LANG_MENU['請先登入會員'], url('Login/login').'?jumpUri='.$_SERVER['REQUEST_URI']);};
		
		if($this->user == null){
			$this->redirect('Index/index');
		}

		$orderform = Db::connect('main_db')
		->table('orderform')
		->order('id desc')
		->where([
			'user_id' => $this->user['id'],
			'status' => ['in', 'Complete,Cancel,Return']
		])
		->select();



		$this->assign('orderform',$orderform);
		//var_dump($orderform);die();

		return $this->fetch('history');
	}

	/* 訂購成功 */
	public function orderform_success($id) {

		if($this->tspg_card==1 && isset($_GET['order_no']) && isset($_GET['ret_code']) ){ // 從台新銀行跳轉回來
			if($_GET['ret_code'] == '00'){
				$Tspg = new Tspg();
				$res = $Tspg->check_order($_GET['order_no']);
				if($res->params->ret_code == '00'){
					$Tspg->send_order_complete_email($id);
				}
				echo("<script>alert('".LANG_MENU['信用卡交易成功，但收款狀態可能不會即時更新，若1日後仍無更新，請來電洽詢']."')</script>");
			}else{
				echo("<script>alert('".LANG_MENU['信用卡交易失敗，請點選補單按鈕再次進行交易']."')</script>");
			}
		}

		$this->get_order_detail($id);
		return $this->fetch('orderform_success');
	}
	/* 訂單詳細內容頁 */
	public function orderform_c($id, $view_type="mormal") {
		$this->get_order_detail($id);
		return $this->fetch('orderform_c');		
	}
	public function get_order_detail($id){
		// $singleData = Db::connect('main_db')->table('orderform')->find($id);
		$singleData = Db::connect('main_db')->table('orderform')->where('order_number = "'.$id.'"')->find();
		
		$userId = Session::get('user.id');

		if (($singleData['user_id'] != $userId)&&($singleData['user_id'] != 0)){
			$this->redirect('Index/index');
		}

		$product_data = json_decode($singleData['product']);
		$product_data = array_map(function ($value){ return get_object_vars($value); }, $product_data);
		array_walk($product_data , function($v, $k)use(&$product_data){
			if(isset($v['type_id'])) $product_data[$k]['type_id_ori'] = explode('_', $v['type_id'])[0];
		});
		$singleData['product'] = $product_data;

		$this->assign('singleData', $singleData);

		$singleData['discount'] = json_decode($singleData['discount'],true);

		// $singleData['discount'] = array_map(function ($value)
		// {
		// 	return get_object_vars($value);
		// }, $singleData['discount']);

		$singleData['name']=Db::connect('main_db')->table('account')->where('id',$singleData['user_id'])->value('name');

		$orderform = Db::connect('main_db')
			->table('orderform')
			->where([
				'order_number' => $singleData['order_number'],
			])->select();

		// 資料加密
		$singleData['name'] = parent::hidestr($singleData['name'], 1,2);
		$singleData['transport_location_name'] = parent::hidestr($singleData['transport_location_name'], 1,2);
		$singleData['transport_location'] = parent::hidestr($singleData['transport_location'], 7);
		$singleData['transport_location_phone'] = parent::hidestr($singleData['transport_location_phone'], -3);
		$singleData['transport_location_tele'] = parent::hidestr($singleData['transport_location_tele'], -3);
		//dump($singleData);
		$this->assign('singleData', $singleData);

		$examinee_info = Db::table('examinee_info')->where(['order_id' => $singleData['id']])->select();
		$examinee_info_array = [];
		foreach($examinee_info as $k => $v){
			if(empty($examinee_info_array[$v['type_id']]))
				$examinee_info_array[$v['type_id']] = [];
			array_push($examinee_info_array[$v['type_id']],$v);
		}
		// dump($examinee_info);
		$this->assign('examinee_info', $examinee_info_array);

		//var_dump($singleData);die();
		$this->assign('orderform',$orderform);
	}

	/* 回報匯款 */
	public function setReportNumber() {
		try{
			$reportNumber = Request::instance()->post('reportNumber');
			if($reportNumber == ''){
				return json([
					'status' => false,
					'message' => LANG_MENU['內容有誤'] /*不可為空*/
				], 200);
			}

			$id = Request::instance()->post('id');
			$Order_data = Db::connect('main_db')->table('orderform')->find($id);
			if($Order_data['report']!=''){ // 已經回報過
				return;
			}
			if($this->user == null && $Order_data['user_id'] != 0){ // 訂單為會員訂單，且未登入會員
				$this->error(LANG_MENU['請先登入會員']);
			}

			$Order = OrderFactory::createOrder($id, $this->order_tableName, $this->coupon_tableName);
			$Order->setReportNumber($reportNumber);
			$outputData = [
				'status' => true,
				'message' => 'success'
			];

			// 寄送提醒信
			$globalMailData = parent::getMailData();
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
			$mailcom->IsHTML(true);
			$mailcom->Body = "
				<html>
					<head></head>
					<body>
						<div>
							匯款回報提醒：<br>
							訂單編號 ".$Order_data['order_number']." 的訂單已回覆匯款資訊<br>
							再麻煩管理人員至後台處理<br>
						</div>
						<div style='color:red;'>
						≡ 此信件為系統自動發送，請勿直接回覆(".$Order_data['id'].") ≡
						</div>
					</body>
				</html>
			";
			foreach($globalMailData['return_email'] as $k => $v){
				try{
					$mailcom->AddAddress($v['email']);
					$mailcom->Send();
				}catch (\Exception $e) {
					echo $e->getMessage();
				}
			}

		}catch (\Exception $e){
			$outputData = [
				'status' => false,
				'message' => $e->getMessage()
			];
			return json($outputData, 200);
		}

		return json($outputData, 200);
	}

	// 取消訂單
	public function cancel(){
		$order_number = $_POST['order_number'];
		$singleData = Db::connect('main_db')->table('orderform')->where('order_number="'.$order_number.'"')->find();

		$userId = Session::get('user.id');
		if (($singleData['user_id'] != $userId)&&($singleData['user_id'] != 0)){
			$this->redirect('Index/index');
		}

		$id = $singleData['id'];
		if ($id != '' && $id != null){
			$this->Order = OrderFactory::createOrder($id, $this->order_tableName, $this->coupon_tableName);
			$this->Order->changeStatus2Cancel('消費者取消');
			$this->success(LANG_MENU['操作成功']);
		}else{
			$this->error(LANG_MENU['發生錯誤']);
		}
	}

	public function tracking(){
		$this->assign('useNg','true');
		return $this->fetch('tracking');
	}
	public function orderTracking(){
		$jData = json_decode(file_get_contents('php://input'), true);
		$singleData = Db::connect('main_db')->table('orderform')->where('order_number',$jData['orderNum'])->find();
		if ($singleData){
			if ($singleData['user_id']!=0){
				if (!Session::get('user')){
					$retData = ['status' => 200,'order_number'=>$singleData['order_number'],'info'=>LANG_MENU['請先登入會員']];
				}else{
					$retData = ['status' => 200,'order_number'=>$singleData['order_number'],'info'=>''];
				}
			}else{
				$retData = ['status' => 200,'order_number'=>$singleData['order_number'],'info'=>''];
			}
			echo json_encode($retData);

		}else{
			echo json_encode(['status' => '400']);
		}
	}
	/*AJAX 物流狀態*/
	public function ajax_logistic_status($id) {

		$singleData = Db::connect('main_db')->table('orderform')->where('id',$id)->find();
		$logisticsRecord = Db::connect('main_db')->table('logistics_record lr')
							->Distinct(true)
							->field('lr.time, lc.type, lc.message')
							->join('logistics_code lc', 'lr.RtnCode = lc.code', 'LEFT')
							->where('lr.order_id ='.$id.' and lr.logistics_id = "'.$singleData['AllPayLogisticsID'].'" and lr.LogisticsType = lc.type  and lr.RtnCode = lc.code')
							->order('lr.id asc')
							->select();
		// dump($logisticsRecord);

		return json($logisticsRecord, 200);
	}


	public function tracking_registration(){
		$this->assign('useNg','true');

		$g_process = Db::table('consent')->where("id=1")->find();
		$this->assign('g_process', $g_process['g_process']);
		return $this->fetch('tracking_registration');
	}
	public function tracking_view(){
		//排除取消訂單
		$search = '';
		$order_id_cancel = [];
		$order_cancel = Db::connect(config('main_db'))->field('id')->table('orderform')->where("status not in ('New', 'Complete')")->select();

		if(count($order_cancel)>0){
			foreach ($order_cancel as $key => $value) {
				array_push($order_id_cancel, $value['id']);
			}
			// dump($order_id_cancel);
			$search = " and ex.order_id not in (".join(",",$order_id_cancel).")";
		}

		$id = Request::instance()->post('id_card')?? $this->error(LANG_MENU['請輸入身分證'],'orderform/tracking_registration');;
		// $pw = Request::instance()->post('password')??'977878';

		$re = Db::table('examinee_info ex')
				->field('ex.id,ex.examinee_ticket,info.examinee_date,info.title as in_title, tinfo.title as area_title')
				->join('productinfo_type tinfo','tinfo.id = ex.type_id')
				->join('productinfo info','info.id = ex.prod_id')
				->where("(ex.examinee_id = '".trim($id)."') ".$search)
				// ->where("(ex.examinee_id = '".trim($id)."') or (ex.examinee_ticket = '".trim($id)."')")
				// ->where("(ex.examinee_id = '".trim($id)."' and ex.examinee_birthday = '".trim($pw)."') or (ex.examinee_ticket = '".trim($id)."' and ex.examinee_birthday = '".trim($pw)."')")
				->order('examinee_date desc')
				->group('ex.prod_id')
				->select();
		$this->assign('re',$re);

		$id_card = isset($_POST['id_card']) ? parent::hidestr($_POST['id_card'],3,3) : '';
		$this->assign('id_card',$id_card);

		$g_process = Db::table('consent')->where("id=1")->find();
		$this->assign('g_process', $g_process['g_process']);

		return $this->fetch('tracking_view');	
	}
	public function personal(){
		$personal = Db::table('examinee_info e')->field('e.*,type.title, pi.title as pi_title, pi.examinee_date')->where("e.id = '".$_POST['id']."'")
			->join('productinfo pi','pi.id = e.prod_id')
			->join('productinfo_type type','type.id = e.type_id')->select();

		array_walk($personal, function($v,$k)use(&$personal){
			$orderform = Db::connect('main_db')->table('orderform')->where('id',$v['order_id'])->find();
			$personal[$k]['examinee_name'] = parent::hidestr($v['examinee_name'],1,-1);
			$personal[$k]['receipts_state'] = RECEIPTS_STATE($orderform['receipts_state']);
		});

		$this->assign('p', $personal);
		return $this->fetch('personal');
	}
	public function personal_show($id,$examinee_id){
		$personal = Db::table('examinee_info e')
						->field('e.*,type.title,info.examinee_date,info.title as in_title')
						->join('productinfo_type type','type.id = e.type_id')
						->join('productinfo info','info.id = e.prod_id')
						->where('e.id="'.$id.'" AND e.examinee_id="'.$examinee_id.'"')
						->find();
		array_walk($personal, function($item, $key)use(&$personal){
			$personal[$key]	 = str_replace('&nbsp;', '<sws></sws>', htmlentities($item));
		});
		// dump($personal);
		$this->assign('p', $personal);

		return $this->fetch('personal_show');
	}
}



