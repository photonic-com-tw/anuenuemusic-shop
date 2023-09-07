<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;
use DBtool\DBTextConnecter;

class Position extends MainController{
	private $tableName;

	public function __construct() {
        parent::__construct();
		$this->tableName = 'position';
	}



	public function index() {
		$dis = Db::table($this->tableName)->order('name asc, id desc')->select();
		array_walk($dis, function($item,$key)use(&$dis){
			$dis[$key]['used_num'] = Db::table('position_portion')->where("position_id = '".$item['id']."' and product_id != '0'")->count();
		});
		$this->assign('dis',$dis);
		return $this->fetch('position');
	}

	

	public function edit() {
		// dump($_POST);exit;
		$type = $_POST['type'];
		$id = $_POST['id'];
		$use_pos = Db::table('position_portion')->where("position_id = '".$id."' and product_id != '0'")->count();


		if($type == 'delete' ){
			if($use_pos > 0){
				echo '已使用'.$use_pos.'個位置，請先清除';
				exit;
			}
		}

		switch ($type) {

		  case "add":
			$data['name'] = $_POST['name'];
			$data['number'] = $_POST['number'];
			$data['max'] = $_POST['max'];

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

		  case "update":

			$data['number'] = $_POST['number'];
			$data['max'] = $_POST['max'];
			// dump($data);

			$ori_position = Db::table($this->tableName)->field('number, max')->find($id);
			// dump($ori_position);

			if($use_pos > 0){
				echo '已使用的庫存區不可修改上限';
				break;
			}else{
				if($ori_position['number'] > $data['number']  && $ori_position['max'] !=1){
					echo '不可縮減數量';
					break;
				}
				if($ori_position['max'] == 1 && $data['max'] ==0){
					echo '無上限不可調整回有上限';
					break;
				}
			}

			if(Db::table($this->tableName)->where('id',$id)->update($data)){
				echo 'success';
			}else{
				echo '新增失敗';
			}
			break;
		}

	}

}

























































































































