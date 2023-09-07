<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

//Photonic Class
use controllerInterface\resources\SoleType;
use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class stronghold extends MainController
{

    private $DBTextConnecter;
    private $resTableName;
    const PER_PAGE_ROWS = 10;
	const SIMPLE_MODE_PAGINATE = false;

    //this resources's frontend use Single Page Web, some CURD method is useless
    public function edit(){}
    public function __construct() 
    {
        parent::__construct();
		$this->DBTextConnecter = DBTextConnecter::withTableName('typeinfo_str');
		$this->DBTextConnecter2 = DBTextConnecter::withTableName('stronghold');
		//$this->DBFileConnecter = DBFileConnecter::withTableName('stronghold');
		
        $this->resTableName = 'typeinfo_str';
    }

    public function index($id)
	{
        $searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);
        $stronghold = Db::table($this->resTableName)
                        ->where("title LIKE '%$searchKey%' and  parent_id = '".$id."' ")
						->order('orders', 'asc')
                        ->order('time desc')->paginate(
                        self::PER_PAGE_ROWS,
                        self::SIMPLE_MODE_PAGINATE, 
                        [
                            'query' => [
                                'searchKey' => $searchKey
                            ]
						]);
		if($stronghold == null){
			$this->error('商品不存在');
		}
		$distrtitle = Db::table('stronghold')->field('id, title')->where('id = '.$id)->order('order_id asc, id desc')->select();
		$this->assign('distrtitle', $distrtitle);
		$this->assign('parent_id', $id);				
        $this->assign('stronghold', $stronghold);
		return $this->fetch('adgroup');
    }
    
    public function doCreate(){
        $width = 700; $height = 475;
        $newData = Request::instance()->post();
        unset($newData['id']);
        unset($newData['json_sub_pics']);
        try{
            $this->DBTextConnecter->setDataArray($newData);
			$mainId = $this->DBTextConnecter->createTextRow();
            $newData['id'] = $mainId;

			/*處理大圖*/
            $image = Request::instance()->file('image');
            if($image){
                $DBFileConnecter = new DBFileConnecter();
                $newData['pic'] = $DBFileConnecter->fixedFileUp($image, 'stronghold_' . $mainId, $width, $height);
            }

            /*處理附圖*/
            $sub_pics = Request::instance()->file('sub_pics');
            // dump($sub_pics);
            if($sub_pics){
            	$DBFileConnecter = new DBFileConnecter();
            	foreach ($sub_pics as $key => $value) {
        			$sub_pics[$key] = $DBFileConnecter->fixedFileUp($value, 'stronghold_'.$mainId.'_sub'.$key.'_'.time());
            	}
            	// dump($sub_pics);
            	$newData['sub_pics'] = json_encode($sub_pics, JSON_UNESCAPED_UNICODE);
            }

            /*更新圖片資料*/
            if($image || $sub_pics){
            	$this->DBTextConnecter->setDataArray($newData);
				$this->DBTextConnecter->upTextRow();
            }     	

			// 自動更新排序
			if( isset($newData['orders']) ){
	            $table = $this->resTableName;
	            $column = 'orders';
	            $order_num = $newData['orders'];
	            $primary_key = 'id';
	            $primary_value = $mainId;
	            $filter_where = 'parent_id='.$newData['parent_id'];
	            parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value, $filter_where);
	            unset($newData['orders']);
	        }

            ob_clean();
			echo('<h1>上傳成功</h1>');die();
		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
    }

    public function update(){
        $width = 700; $height = 475;
        $newData = Request::instance()->post();
        // dump($newData);

        try{
        	/* 處理主圖 */
            $image = Request::instance()->file('image');
            if($image){
                $DBFileConnecter = new DBFileConnecter();
                $newData['pic'] = $DBFileConnecter->fixedFileUp($image, 'stronghold_' . $newData['id'], $width, $height);
            }

            /* 處理附圖 */
            $sub_pics = json_decode($newData['json_sub_pics']);
            $sub_pics_files = Request::instance()->file('sub_pics');
            // dump($sub_pics_files);
            if($sub_pics_files){
            	$DBFileConnecter = new DBFileConnecter();
            	$sub_count = 0;
            	foreach ($sub_pics as $key => $value) {
            		if( substr($value, 0, 7) != "/upload" && $sub_pics_files[$sub_count]){
            			$sub_pics[$key] = $DBFileConnecter->fixedFileUp($sub_pics_files[$sub_count], 'stronghold_'.$newData['id'].'_sub'.$key.'_'.time());
            			$sub_count+=1;
            		}
            	}
            }
            // dump($sub_pics);
            $newData['sub_pics'] = json_encode($sub_pics, JSON_UNESCAPED_UNICODE);
        	unset($newData['json_sub_pics']);

        	/*更新資料*/
            // dump($newData);exit;
            $this->DBTextConnecter->setDataArray($newData);
            $this->DBTextConnecter->upTextRow();

            // 自動更新排序
			if( isset($newData['orders']) ){
	            $table = $this->resTableName;
	            $column = 'orders';
	            $order_num = $newData['orders'];
	            $primary_key = 'id';
	            $primary_value = $newData['id'];
	            $filter_where = 'parent_id='.$newData['parent_id'];
	            parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value, $filter_where);
	            unset($newData['orders']);
	        }

            ob_clean();
			echo('<h1>上傳成功</h1>');die();
		}catch (\Exception $e){
			ob_clean();
			echo($e->getMessage());die();
		}
    }

    public function delete(){
		$id = Request::instance()->get('id');
		$parent_id = Request::instance()->get('parent_id');
		
        try{
            Db::table($this->resTableName)->delete($id);
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功');
    }

    public function multiDelete()
    {
        $idList = Request::instance()->post('id');
        try{
            if ($idList) {
                $idList = json_decode($idList);
                Db::table($this->resTableName)->delete($idList);
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功');
    }

    /*AJAX*/
	public function cellCtrl()
	{
		try{
			$updateData = Request::instance()->post();
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



	public function Dodelete() {
		$id = Request::instance()->get('id');
		$url = Request::instance()->get('url');
		$urllist = explode( '/', $url);
		if($urllist[2] == 'stronghold' && explode( '.', $urllist[5])[0] == $id){
			$url = '/admin';
		}		

		$deleteData = Db::table('typeinfo_str')->field('id')->where('parent_id', $id)->select();

		if (sizeof($deleteData)) {
			$this->error('內部還有分類，請先清光');
		}
		Db::table('stronghold')->delete($id);
        $this->success('刪除成功', $url,-1,3);
    }

	/*AJAX*/
	public function create() {
		try{
			$updateData = Request::instance()->post();
			$this->DBTextConnecter2->setDataArray($updateData);
			$id = $this->DBTextConnecter2->createTextRow();

			// 自動更新排序
            $table = 'stronghold';
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
			$product = Db::table('stronghold')
					->field('id, title')
					->order('order_id asc , id desc')->select();
			$product = array_map(
				function ($arrayItem){
					$arrayItem['url'] = url('stronghold/index', ['id' => $arrayItem['id']]);
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

        $cate = Db::table('typeinfo')->where('parent_id', $id)->select();
		if($cate == null){
			$this->error('商品不存在');
		}
        $data = ['seriesId' => $id,'cate' => $cate];

        return json($data, 200);
    }


    public function getCateProd(){
        $id = Request::instance()->post('cateId');

        $productinfo = Db::table('productinfo')->alias('pi')
            ->join('act_product ap','pi.id=ap.prod_id','LEFT')
            ->where('pi.parent_id = ' . $id)
            ->where('ap.act_prod_id is null')
            ->order('pi.order_id, pi.id desc')->select();
		if($productinfo == null){
			$this->error('商品不存在');
		}
        //echo Db::table('productinfo')->getLastSql();

        $data = ['cateId' => $id,'productinfo' => $productinfo];

        return json($data, 200);
    }

	public function updatetitle(){
		$newData = Request::instance()->post();
		
		$cate = Db::table('stronghold')->where('id ='.$newData['id'])->update(['title' =>$newData['title']]);
		
		if($cate == null){
			$this->error('商品不存在');
		}
		
		$this->success('更新成功');
	}

}

