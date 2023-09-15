<?php

namespace app\index\controller;

use think\Controller;

use think\Db;

use think\Request;

use pattern\PointRecords;

class Points extends PublicController

{

	public function __construct()
    {
    	$Request = Request::instance();
		parent::__construct($Request);
		$this->assign('mebermenu_active', 'points');

		$consent = Db::table('consent')->where("id=1")->find();
		$this->assign('consent_other', $consent['other']);

		if(!$this->user){ $this->error(LANG_MENU['請先登入會員'], url('Login/login'));};
	}

	public function set_point_expire() {
		$PointRecords = new PointRecords($this->user['id']);
		$PointRecords->set_point_expire();
	}
	public function points() {
		if($this->user == null){
			$this->redirect('Index/index');
		}
		
		$PointRecords = new PointRecords($this->user['id']);
		$records = $PointRecords->get_user_records();
		$this->assign('records', $records);

		$expiring_poiints = $PointRecords->get_expiring_poiints();
		$this->assign('expiring_poiints', $expiring_poiints);

		$current_poiints = $PointRecords->get_current_poiints();
		$this->assign('current_poiints', $current_poiints);

		$expiring_count_date = $PointRecords->expiring_count_date();
		$this->assign('expiring_count_date', $expiring_count_date);

		$consent = Db::table('consent')->select()[0]['point'];
		$this->assign('consent',$consent);

		return $this->fetch('points');
	}



	public function teach() {
		$fixedResourcesRowId = 1;
		$consent = Db::table('consent')->field('point')->find($fixedResourcesRowId);

		$this->assign('consent', $consent);

		return $this->fetch('teach');
	}

}

