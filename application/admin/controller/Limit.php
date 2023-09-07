<?php



namespace app\admin\controller;



use app\admin\controller\MainController;

use think\Request;

use think\Db;



class Limit extends MainController

{

	const PER_PAGE_ROWS = 20;

	const SIMPLE_MODE_PAGINATE = false;



	public function index() {



		$searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);

		$kol_id = Request::instance()->get('kol_id') ?? '-1';
		$this->assign('kol_id', $kol_id);
		if($kol_id == -1){ // 不依搜尋網紅
        	$kol_search = ' ( kp.is_using=1 OR kp.kol_id is null)';
        }else if($kol_id == 0){ // 搜尋無網紅
        	$kol_search = ' ( (kp.kol_id =0 AND kp.is_using=1) OR kp.kol_id is null )';
        }else{ // 搜尋某網紅的
        	$kol_search = ' kp.kol_id ='.$kol_id.' AND kp.is_using=1';
        }

		$orderGet = Request::instance()->get('orderGet');
		$order = "productinfo_create_time desc, productinfo_title asc, productinfo_type_num asc";
		if(!($orderGet == '' || $orderGet == null)){
			switch ($orderGet) {
				case 'num':
					$order = 'productinfo_type_num desc';
					break;
				case 'create_time':
					$order = 'productinfo.create_time desc';
					break;
			}
        }

		$limit_num = Db::table('productinfo')
			->field('	productinfo.title AS productinfo_title, 
						productinfo.create_time AS productinfo_create_time, 
						productinfo.product_id AS productinfo_product_id, 
						productinfo.id AS productinfo_id, 
						productinfo.r_repeat AS r_repeat,
						productinfo_type.id AS type_id,
						productinfo_type.title AS productinfo_type_title,
						productinfo_type.num AS productinfo_type_num,
						kol.kol_name
						')
			->where("productinfo_type.num <= productinfo_type.limit_num
					 AND (
						productinfo_type.title like  '%" . $searchKey . "%' OR 
						productinfo.product_id like  '%" . $searchKey . "%' OR 
						productinfo.title like  '%" . $searchKey . "%'
					 )
					 AND 
					 productinfo_type.online = 1 AND
					 ".$kol_search
			)
			->join('productinfo_type','productinfo_type.product_id = productinfo.id')
			->join('kol_productinfo kp', 'kp.productinfo_id = productinfo.id', 'left')
			->join('kol','kol.id = kp.kol_id', 'left')
			->order($order)
			->paginate(
				self::PER_PAGE_ROWS,
				self::SIMPLE_MODE_PAGINATE, 
				[
					'query' => [
						'searchKey' => $searchKey,
						'orderGet' => $orderGet
					]
				]
			);

		$limit_num_items = $limit_num->items();
		array_walk($limit_num_items, function($item,$key)use(&$limit_num_items){
			if($item['r_repeat']==0){ // 一碼一品項
				$limit_num_items[$key]['productinfo_type_total']=Db::table('position_portion')->where("product_id = '".$item['productinfo_id']."' and productinfo_type = '".$item['type_id']."' and num!=0")->count();
			}else{ // 一碼多品項
				$limit_num_items[$key]['productinfo_type_total']=Db::table('position_portion')->where("product_id = '".$item['productinfo_id']."' and productinfo_type = '".$item['type_id']."' and num!=0")->find()['num'];	
			}
		});
		// dump($limit_num_items);exit;
		$this->assign('limit_num_items', $limit_num_items);
		$this->assign('limit_num', $limit_num);

		$kol = Db::table('kol')->order('id desc')->select();
		$this->assign('kol', $kol);

		return $this->fetch('warning');
	}



	public function getCount(){

		try{
			$totalCount = Db::table('productinfo')
			->field('	productinfo.title AS productinfo_title, 
						productinfo.create_time AS productinfo_create_time, 
						productinfo.product_id AS productinfo_product_id, 
						productinfo.id AS productinfo_id, 
						productinfo_type.title AS productinfo_type_title,
						productinfo_type.num AS productinfo_type_num,
						')
			->where("productinfo_type.num <=  productinfo_type.limit_num AND productinfo_type.online = 1")
			->join('productinfo_type','productinfo_type.product_id = productinfo.id')
			->count();

			$outputData = [
				'status' => true,
				'message' => $totalCount
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