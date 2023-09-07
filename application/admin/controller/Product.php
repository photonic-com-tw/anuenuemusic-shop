<?php
namespace app\admin\controller;
use app\admin\controller\MainController;
use think\Request;
use think\Db;
use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;


class Product extends MainController
{
	private $resTableName;
	private $DBTextConnecter;
	private $DBFileConnecter;
	public function __construct() {
		parent::__construct();
        $this->resTableName = 'product';
		$this->DBTextConnecter = DBTextConnecter::withTableName('product');
		$this->DBFileConnecter = DBFileConnecter::withTableName('product');
	}

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
				parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value);
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

	public function index($id) {
		$product = Db::table('product')->find($id);
		if(!$product){
			$this->redirect('index/index');
		}
		$this->assign('product', $product);
		//var_dump($product);die();
		return $this->fetch('adgroup');
	}

	public function update() {
		$newData = Request::instance()->post();

		// 自動更新排序
		if( isset($newData['order_id']) ){
			$table = $this->resTableName;
			$column = 'order_id';
			$order_num = $newData['order_id'];
			$primary_key = 'id';
            $primary_value = $newData['id'];
			parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value);
			unset($newData['order_id']);
		}

		//the post data name show-btn is for online,
		//but online is change by AJAX,
		//so we don't need the post data that name show-btn
		unset($newData['show-btn']);
		$picNameList = [
			'pic', 'index_adv01_pic',
			'index_adv02_pic', 'index_adv03_pic', 'index_adv04_pic', 'index_adv05_pic',
			'index_adv06_pic', 'index_adv07_pic', 'inner_adv01_pic', 'inner_adv02_pic',
			'pic_icon'
		];
		$picSizeList = [
			['width' => 430, 'height' => 150], ['width' => 400, 'height' => 730],
			['width' => 370, 'height' => 370], ['width' => 800, 'height' => 640],
			['width' => 370, 'height' => 370], ['width' => 800, 'height' => 640],
			['width' => 370, 'height' => 370], ['width' => 800, 'height' => 640],
			['width' => 880, 'height' => 128], ['width' => 880, 'height' => 280],
			['width' => 50, 'height' => 50]
		];
		$file = Request::instance()->file();
		try{
			for($i = 1; $i <= 11; $i++) {
				if( $newData['del_' . $picNameList[ $i-1 ]] ){
					$newData[$picNameList[ $i-1 ]] = '';
					unset($file[$picNameList[ $i-1 ]]);
				}
				unset($newData['del_' . $picNameList[ $i-1 ]]);
			}
		} catch (\Exception $e) {
			// $this->dumpException($e);
		}
		
		try{
			$this->DBTextConnecter->setDataArray($newData);
			$this->DBTextConnecter->upTextRow();
			if(!empty($file)){
				if(count($file)>0){
					$this->DBFileConnecter->setFileArray($file);
					$this->DBFileConnecter->setPrivateKeyId($newData['id']);
					$this->DBFileConnecter->upFileRow();
				}
			}
		} catch (\Exception $e) {
			$this->dumpException($e);
		}
		$this->success('更新成功');
	}

	public function delete() {
		$id = Request::instance()->get('id');
		$produc_in_layer = Db::table('productinfo')->field('id')->where("final_array like '%".'"prev_id":"'.$id.'","branch_id":"0","parent_id":"0"'."%'")->select();
		$layer_in_layer = Db::table('typeinfo')->field('id')->where('parent_id', $id)->select();

		if (sizeof($produc_in_layer)) {
			$this->error('此分館還有商品，請先清光');
		}else if(sizeof($layer_in_layer)){
			$this->error('此分館下還有分類，請先清光');
		}
		Db::table('product')->delete($id);
		$this->success('刪除成功');
	}

	/*AJAX*/
	public function create() {
		try{
			$updateData = Request::instance()->post();
			$this->DBTextConnecter->setDataArray($updateData);
			$id = $this->DBTextConnecter->createTextRow();

			// 自動更新排序
			$table = $this->resTableName;
			$column = 'order_id';
            $order_num = 0;
			$primary_key = 'id';
            $primary_value = $id;
			parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value);

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

	public function getList() {
		try{
			$product = Db::table('product')
					->field('id, title')
					->order('order_id asc, id desc')->select();
			$product = array_map(
				function ($arrayItem){
					$arrayItem['url'] = url('Product/index', ['id' => $arrayItem['id']]);
					$arrayItem['all_count'] =  Db::table('productinfo')->where("final_array like '%\"prev_id\": \"".$arrayItem['id']."\",%'")->count();
					return $arrayItem;
				},
			$product);
			$outputData = [
				'status' => true,
				'message' => $product
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

	public function getCate(){
		$id = Request::instance()->post('seriesId');
		$cate = Db::table('typeinfo')->where("parent_id = '".$id."' and branch_id = '0'")->select();
		
		if(empty($cate))
			$cate = Db::table('typeinfo')->where("branch_id = '".$id."'")->select();
		
		if(empty($cate))
			$data = ['seriesId' => $id,'cate' => $cate,'getCateProd'=>1];
		else
			$data = ['seriesId' => $id,'cate' => $cate];
		return json($data, 200);
	}

	public function getCate2(){
		$id = Request::instance()->post('seriesId');
		$cate = Db::table('typeinfo')->where("branch_id = '".$id."'")->select();
		
		if(empty($cate))
			$data = ['seriesId' => $id,'cate' => $cate,'getCateProd'=>1];
		else
			$data = ['seriesId' => $id,'cate' => $cate];
		return json($data, 200);
	}

	// 活動、折扣取得商品
	public function getCateProd(){
		$id = Request::instance()->post('cateId');
		$first = Request::instance()->post('first');
		if($first){
			//是第一階
			$productinfo = Db::table('productinfo')->alias('pi')
			->join('act_product ap','pi.id=ap.prod_id','LEFT')
			->where("final_array like '%\"prev_id\":\"".$id."\"%\"parent_id\":\"0\"%'")
			->where('ap.act_prod_id is null')
			->order('pi.order_id, pi.id desc')->select();
			
		}else{
			$productinfo = Db::table('productinfo')->alias('pi')
			->join('act_product ap','pi.id=ap.prod_id','LEFT')
			->where("final_array like '%\"parent_id\":\"".$id."\"%'")
			->where('ap.act_prod_id is null')
			->order('pi.order_id, pi.id desc')->select();
		}
			
		
		if(empty($productinfo)){
			$productinfo = Db::table('productinfo')->alias('pi')
			->join('act_product ap','pi.id=ap.prod_id','LEFT')
			->where("final_array like '%\"branch_id\": \"".$id."\"%' ")
			->where('ap.act_prod_id is null')
			->order('pi.order_id, pi.id desc')->select();
		}
		
		foreach($productinfo as $k => $v){
			$pic1 = json_decode($v['pic'],true);
			$productinfo[$k]['pic1'] = $pic1[0];
		}
			
		//echo Db::table('productinfo')->getLastSql();
		$data = ['cateId' => $id,'productinfo' => $productinfo];
		return json($data, 200);
	}
}