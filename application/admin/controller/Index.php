<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class Index extends MainController
{
	const NONE_PRODUCT_FLAG = '0';

	public function index() {
		$this->redirect('admin/All/index');

		$index_online = Db::table('index_online')->find(1);
		$this->assign('index_online', $index_online);

		$index_excel = Db::table('index_excel')->order('id')->select();
		$this->assign('index_excel', $index_excel);

		$slideshow = Db::table('slideshow')->field('title, pic, link')->order('id')->select();
		$this->assign('slideshow', $slideshow);

		$hot_product = Db::table('hot_product')->order('id')->select();
		$hot_product = $this->id2number($hot_product);
		$this->assign('hot_product', $hot_product);

		$recommend_product = Db::table('recommend_product')->order('id')->select();
		$recommend_product = $this->id2number($recommend_product);
		$this->assign('recommend_product', $recommend_product);

		$spe_price_product = Db::table('spe_price_product')->order('id')->select();
		$spe_price_product = $this->id2number($spe_price_product);
		$this->assign('spe_price_product', $spe_price_product);

		$expiring_product = Db::table('expiring_product')->order('id')->select();
		$expiring_product = $this->id2number($expiring_product);		
		$this->assign('expiring_product', $expiring_product);

		$time_product = Db::table('time_product')->order('id')->select();
		$time_product = $this->id2number($time_product);				
		$this->assign('time_product', $time_product);

		$product = Db::table('product')
				->field('id, title, online')
				->order('order_id')->select();
		$this->assign('product', json_encode($product));	
		//var_dump( json_encode($product));

		$show_time_limit_prod = Db::connect('main_db')->table('excel')->where('id = 9')->find()['value1'];
		$this->assign('show_time_limit_prod', $show_time_limit_prod);

		$use_sepc_price = Db::connect('main_db')->table('excel')->where('id = 10')->find()['value1'];
		$this->assign('use_sepc_price', $use_sepc_price);

		$index_edm = Db::connect('main_db')->table('excel')->where('id = 11')->find()['value1'];
		$this->assign('index_edm', $index_edm);

		return $this->fetch('index');
	}

	private function id2number(array $product) {
		return array_map(function ($value){
			if ($value['product_id'] == '0'){
				return $value;
			}

			$number = Db::table('productinfo')->field('product_id')->where('id', $value['product_id'])->find();
			$value['product_id'] = $number['product_id'];
			return $value;
		},$product);
	}

	/*iframe*/
	/*those method will cover old image by DB table id*/
	public function setSlideShow() {
		$width = 1920;
		$height = 470;
		$DBTextConnecter = DBTextConnecter::withTableName('slideshow');
		$DBFileConnecter = new DBFileConnecter();
		try{
			for($i = 1; $i <= 5; $i++){
				$updateData = [
					'id' => $i,
					'title' => Request::instance()->post('title' . $i),
					'link' => Request::instance()->post('link' . $i)
				];
				$image = Request::instance()->file('image' . $i);
				if($image){
					$updateData['pic'] = $DBFileConnecter->fixedFileUp($image, 'slideshow'.$i, $width, $height);
				}
				$DBTextConnecter->setDataArray($updateData);
				$DBTextConnecter->upTextRow();
			}
			ob_clean();
			echo('<h1>上傳成功</h1>');die();
		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
	}

	public function setPicWithLink() {
		$image = Request::instance()->file('image');
		$width = Request::instance()->post('width');
		$height = Request::instance()->post('height');
		$link = Request::instance()->post('link');
		$id = Request::instance()->post('id');
		try{
			$DBTextConnecter = DBTextConnecter::withTableName('index_excel');
			$DBFileConnecter = DBFileConnecter::withTableName('index_excel');
			$updateData = [
				'id' => $id,
				'data2' => $link
			];
			/*
			if($image){
				$DBFileConnecter = new DBFileConnecter();
				$updateData['data1'] = $DBFileConnecter->fixedFileUp($image, 'index_excel'.$id, $width, $height);
			}
			*/

			$DBTextConnecter->setDataArray($updateData);
			$DBTextConnecter->upTextRow();
			if($image){
				$DBFileConnecter->setFileArray([
					'data1' => $image
				]);
				$DBFileConnecter->setPrivateKeyId($id);
				$DBFileConnecter->upFileRow();
			}
			ob_clean();
			echo('<h1>上傳成功</h1>');die();
		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
	}

	public function setthirteenth() {
		$width = 1200; $height = 680;
		if(Request::instance()->post('location') == 'left'){
			$dataId = [22, 24, 25];
		}else{
			$dataId = [23, 26, 27];
		}

		try{
			$DBTextConnecter = DBTextConnecter::withTableName('index_excel');

			$updateData = [
				'id' => $dataId[0],
				'data2' => Request::instance()->post('link')
			];

			$image = Request::instance()->file('image');		
			if($image){
				$DBFileConnecter = new DBFileConnecter();
				$updateData['data1'] = $DBFileConnecter->fixedFileUp($image, 'index_excel'.$dataId[0], $width, $height);
			}

			$DBTextConnecter->setDataArray($updateData);
			$DBTextConnecter->upTextRow();

			$updateData = [
				'id' => $dataId[1],
				'data3' => Request::instance()->post('title')
			];

			$DBTextConnecter->setDataArray($updateData);
			$DBTextConnecter->upTextRow();	

			$updateData = [
				'id' => $dataId[2],
				'data3' => Request::instance()->post('content')
			];
			$DBTextConnecter->setDataArray($updateData);
			$DBTextConnecter->upTextRow();

			ob_clean();
			echo('<h1>上傳成功</h1>');die();

		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
	}

	/*AJAX*/
	public function blockCtrl() {
		try{

			$updateData = Request::instance()->post();

			$DBTextConnecter = DBTextConnecter::withTableName('index_online');

			$DBTextConnecter->setDataArray($updateData);

			$DBTextConnecter->upTextRow();

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


	public function setSocialLink() {

		try{

			$DBTextConnecter = DBTextConnecter::withTableName('index_excel');

			$updateData = [

				'id' => 3,

				'data2' => Request::instance()->post('line')

			];

			$DBTextConnecter->setDataArray($updateData);

			$DBTextConnecter->upTextRow();

			$updateData = [

				'id' => 4,

				'data2' => Request::instance()->post('fb')

			];

			$DBTextConnecter->setDataArray($updateData);

			$DBTextConnecter->upTextRow();

			$updateData = [

				'id' => 5,

				'data2' => Request::instance()->post('email')

			];

			$DBTextConnecter->setDataArray($updateData);

			$DBTextConnecter->upTextRow();

			$updateData = [

				'id' => 6,

				'data2' => Request::instance()->post('phone')

			];

			$DBTextConnecter->setDataArray($updateData);

			$DBTextConnecter->upTextRow();

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



	public function setProduct() {

		$tableName = Request::instance()->post('tableName');

		$DBTextConnecter = DBTextConnecter::withTableName($tableName);

		try{

			for($i = 1; $i <= 10; $i++){

				$product_Number = Request::instance()->post('product_id'.$i);

				if($product_Number == self::NONE_PRODUCT_FLAG) {

					$product['id'] = 0;

				}else{

					$product = Db::table('productinfo')->where('product_id', $product_Number)->find();

				}

				$updateData = [

					'id' => $i,

					'product_id' => $product['id']

				];

				$DBTextConnecter->setDataArray($updateData);

				$DBTextConnecter->upTextRow();

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



	public function setText() {

		$text = nl2br(Request::instance()->post('text'));

		$id = Request::instance()->post('id');

		/*$hasbr = Request::instance()->post('hasbr');if($hasbr){}*/

		$DBTextConnecter = DBTextConnecter::withTableName('index_excel');

		try{

			$updateData = [

				'id' => $id,

				'data3' => $text

			];

			$DBTextConnecter->setDataArray($updateData);

			$DBTextConnecter->upTextRow();

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



	public function setLink() {

		$text = Request::instance()->post('link');

		$id = Request::instance()->post('id');

		$DBTextConnecter = DBTextConnecter::withTableName('index_excel');

		try{

			$updateData = [

				'id' => $id,

				'data2' => $text

			];

			$DBTextConnecter->setDataArray($updateData);

			$DBTextConnecter->upTextRow();

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

		$start = strtotime(Request::instance()->post('start'));

		$end = strtotime(Request::instance()->post('end'));

		$DBTextConnecter = DBTextConnecter::withTableName('index_excel');

		try{

			$updateData = [

				'id' => 32,

				'data2' => $start,

				'data3' => $end

			];

			$DBTextConnecter->setDataArray($updateData);

			$DBTextConnecter->upTextRow();

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



	public function setProductOnline() {

		$id = Request::instance()->post('id');

		$online = Request::instance()->post('online');

		$DBTextConnecter = DBTextConnecter::withTableName('product');

		try{

			$updateData = [

				'id' => $id,

				'online' => $online

			];

			$DBTextConnecter->setDataArray($updateData);

			$DBTextConnecter->upTextRow();

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



	public function setProductOrder() {

		$idArray = json_decode(Request::instance()->post('idArray'));

		$DBTextConnecter = DBTextConnecter::withTableName('product');

		try{

			foreach ($idArray as $key => $value) {

				$updateData = [

					'id' => $value,

					'order_id' => $key

				];

				$DBTextConnecter->setDataArray($updateData);

				$DBTextConnecter->upTextRow();

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

	public function spePriceProduct(){

		$productInfo = Db::table('spe_price_product')
			->alias('sp')
			->field('productinfo.title, productinfo.id,
					productinfo.pic, productinfo.has_price, sp.orders, sp.id as sp_id')
			->where('productinfo.online = 1 AND sp.product_id <> 0')
			->order('sp.orders asc, sp.id desc')
			->join('productinfo','productinfo.id = sp.product_id')
			->select();

		foreach ($productInfo as &$vo) {
			$vo['pic'] = json_decode($vo['pic'],true)[0];
		}
		// dump($productInfo);
		$this->assign('productInfo', $productInfo);
		return $this->fetch('speprice');
	}

	public function updateSpePrice(){
		// dump($_POST['data']);exit;

		$data = $_POST['data'];
		
		foreach ($data as $key => $value) {

			try{
				if($value['del'] == 0){
					Db::table('spe_price_product')->where('id',$value['sp_id'])->update(['orders' => $value['orders']]);
				}elseif ($value['del'] == 1) {
					Db::table('spe_price_product')->where('id',$value['sp_id'])->delete();
				}
			}catch (\Exception $e){
				// $outputData = [
				// 	'status' => false,
				// 	'message' => $e->getMessage()
				// ];
				// return json($outputData, 500);
				continue;
			}
		}


		$outputData = [
			'status' => true,
			'message' => 'success'
		];
		return json($outputData, 200);
	}

}