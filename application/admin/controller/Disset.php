<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use DBtool\DBTextConnecter;

class Disset extends MainController
{
	private $tableName;
	public function __construct() {
        parent::__construct();
		$this->tableName = 'discount';
	}

	public function index() {
		$dis = Db::table($this->tableName)->order('number asc')->select();
		$this->assign('dis',$dis);
		return $this->fetch('index');
	}

	public function edit() {
		$type = $_POST['type'];
		$id = $_POST['id'];
	
		switch ($type) {
		  case "add":
			$data['name'] = $_POST['name'];
			$data['number'] = $_POST['number'];
			if(Db::table($this->tableName)->insert($data)){
				echo 'success';
			}else{
				echo '新增失敗';
			}
			break;

		  case "delete":
			if(Db::table($this->tableName)->where('id',$id)->delete()){
				echo 'success';
			}else{
				echo '失敗';
			}
			break;
		}
	}
}

























































































































