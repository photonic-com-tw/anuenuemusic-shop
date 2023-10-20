<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use DBtool\DBTextConnecter;

class Examination extends PublicController
{

	public function __construct()
    {
    	$Request = Request::instance();
		parent::__construct($Request);
		$this->assign('mebermenu_active', 'examination');

		$consent = Db::table('consent')->where("id=1")->find();
		$this->assign('consent_other', $consent['other']);

		if(!$this->user){ $this->error(LANG_MENU['請先登入會員'], url('Login/login').'?jumpUri='.$_SERVER['REQUEST_URI']);};
	}

	public function examination(){
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
		$re = Db::table('examinee_info ex')
		->field('ex.id,ex.prod_id,ex.examinee_ticket,info.examinee_date,info.title as in_title,tinfo.title as area_title')
		->join('productinfo_type tinfo','tinfo.id = ex.type_id')
		->join('productinfo info','info.id = ex.prod_id')
		->where("ex.user_id = '".$this->user['id']."' ".$search)
		->order('examinee_date desc')
		->group('ex.prod_id')
		->select();
		$this->assign('re',$re);
		return $this->fetch('examination');
	}

	public function ex($id){
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

		$re = Db::table('examinee_info ex')
		->field('ex.*,ex.type_id,info.examinee_date,info.title as in_title,tinfo.title as area_title')
		->join('productinfo_type tinfo','tinfo.id = ex.type_id')
		->join('productinfo info','info.id = ex.prod_id')
		->where(" prod_id = '".$id."' and user_id = '".$this->user['id']."' ".$search)
		->order('examinee_date desc')
		->select();
		$this->assign('re', $re);

		return $this->fetch('ex');
	}

	public function ex_update(){
		$personal = Db::table('examinee_info')->find($_POST['id']);
		$this->assign('p', $personal);
		return $this->fetch('ex_update');
	}

	public function personal_update(){
		$id = $_POST['id'];
		unset($_POST['id']);

		$ticket = $_POST['ticket'];
		unset($_POST['ticket']);

		try{
			if($ticket == ''){
				$data = Request::instance()->post();
				Db::table('examinee_info')->where(['id'=>$id])->update($_POST);
				}
				$outputData = [
				'status' => true,
				'message' => 'success'
				];
			}catch (\Exception $e){
				$outputData = [
				'status' => false,
				'message' => $e->getMessage()
				];
				return json($outputData, 200);
		}
		return json($outputData, 200);
	}
}

