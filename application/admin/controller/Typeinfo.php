<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class Typeinfo extends MainController
{

	private $resTableName;
	private $DBTextConnecter;
	private $DBFileConnecter;

	public function __construct() {
        parent::__construct();
        $this->resTableName = 'typeinfo';
        $this->DBTextConnecter = DBTextConnecter::withTableName('typeinfo');
        $this->DBFileConnecter = DBFileConnecter::withTableName('typeinfo');
	}
	

	public function index($parent_id) {
		$typeinfo = Db::table('typeinfo')
				->where("typeinfo.parent_id = '".$parent_id."'   and branch_id = '0'")
				->order('typeinfo.order_id')
				->select();

		foreach ($typeinfo as &$value) {
			$value['product_num'] = 
				Db::table('productinfo')
					->where('parent_id = ' . $value['id'] . " AND online = 1  and branch_id = '0'")
					->count();
		}
		$this->assign('typeinfo', $typeinfo);
		$parent_title = Db::table('product')->field('id, title, online')->find($parent_id);		
		$this->assign('parent_title', $parent_title);
		//var_dump($parent_title);die();
		return $this->fetch('item');
	}

	public function delete() {
        $id = Request::instance()->get('id');
		$produc_in_layer = Db::table('productinfo')->field('id')->where("final_array like '%".'"branch_id":"'.$id.'"'."%'")->select();
		$layer_in_layer = Db::table('typeinfo')->field('id')->where("branch_id =".$id)->select();

		if (sizeof($produc_in_layer)) {
			$this->error('此分類還有商品，請先清光');
		}else if(sizeof($layer_in_layer)){
			$this->error('此分類下還有分類，請先清光');
		}
		Db::table('typeinfo')->delete($id);
        $this->success('刪除成功');
    }


	public function update() {
        $width = 80; $height = 365;
		$newData = Request::instance()->post();

		// 自動更新排序
		if( isset($newData['order_id']) ){
			$table = $this->resTableName;
			$column = 'order_id';
			$order_num = $newData['order_id'];
			$primary_key = 'id';
            $primary_value = $newData['id'];
			$filter_where = 'parent_id='.$newData['parent_id'].' AND branch_id='.$newData['branch_id'];
			parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value, $filter_where);
			unset($newData['order_id']);
		}

        $image = Request::instance()->file('image');
        if($image){
        	try {
            	$newData['pic'] = $this->DBFileConnecter->fixedFileUp($image, 'typeinfo_' . $newData['id'], $width, $height);
        	} catch (\Exception $e) {
        		$this->error('更新圖片有誤');
        	}
        }
        $this->DBTextConnecter->setDataArray($newData);
        $this->DBTextConnecter->upTextRow();
        
        $this->success('更新成功');
	}
	
	public function multiDelete() {
		$idList = Request::instance()->post('id');
		if ($idList == '[]') {
			$this->error('沒有指定');
		}
		$idList = json_decode($idList);
		$deleteData = Db::table('productinfo')->field('id')->where('parent_id', 'in', $idList)->select();
		if (sizeof($deleteData)) {
			$this->error('內部還有商品，請先清光');
		}
		Db::table('typeinfo')->delete($idList);
        $this->success('刪除成功');
    }

	/*iframe*/
	/*those method will cover old image by DB table id*/
	public function create() {
		$width = 80;
		$height = 365;
		$data = Request::instance()->post();
		unset($data['id']);
		$image = Request::instance()->file('image');
		try{
			if($image){
				$data['pic'] = $this->DBFileConnecter->fixedFileUp($image, 'typeinfo'.md5(rand().time()), $width, $height);
			}
		}catch (\Exception $e){
			$this->error('上傳圖片有誤');
		}

		$this->DBTextConnecter->setDataArray($data);
		$id = $this->DBTextConnecter->createTextRow();

		// 自動更新排序
		if( isset($data['order_id']) ){
			$table = $this->resTableName;
			$column = 'order_id';
			$order_num = $data['order_id'];
			$primary_key = 'id';
        	$primary_value = $id;
			$filter_where = 'parent_id='.$data['parent_id'].' AND branch_id='.$data['branch_id'];
			parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value, $filter_where);
			unset($data['order_id']);
		}
		
		$this->success('新增成功');
	}

	public function setPicWithLink() {
		$width = 80;
		$height = 365;
		$id = Request::instance()->post('id');
		try{
			if($image){
				$DBFileConnecter = new DBFileConnecter();
				$updateData['pic'] = $DBFileConnecter->fixedFileUp($image, 'typeinfo'.$id, $width, $height);
				$this->DBTextConnecter->setDataArray($updateData);
				$this->DBTextConnecter->upTextRow();
			}
			ob_clean();
			echo('<h1>上傳成功</h1>');die();
		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
	}

	/*AJAX*/
	public function cellCtrl() {
		try{
			$updateData = Request::instance()->post();

			// 自動更新排序
			if( isset($updateData['order_id']) ){
				$table = $this->resTableName;
				$column = 'order_id';
				$order_num = $updateData['order_id'];
				$primary_key = 'id';
            	$primary_value = $updateData['id'];
				$filter_where = 'parent_id='.$data['parent_id'].' AND branch_id='.$data['branch_id'];
				parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value, $filter_where);
				unset($updateData['order_id']);
			}

			$this->DBTextConnecter->setDataArray($updateData);
			$this->DBTextConnecter->upTextRow();
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

	public function setTimeRange() {
		$id = Request::instance()->post('id');
		$start = strtotime(Request::instance()->post('start'));
		$end = strtotime(Request::instance()->post('end'));
		try{
			$updateData = [
				'id' => $id,
				'data2' => $start,
				'data3' => $end
			];
			$this->DBTextConnecter->setDataArray($updateData);
			$this->DBTextConnecter->upTextRow();
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