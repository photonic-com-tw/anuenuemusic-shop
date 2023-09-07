<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class Discount extends MainController
{
	const ACT_NUMBAER_ROW_ID = 1;
	const PER_PAGE_ROWS = 10;
	const SIMPLE_MODE_PAGINATE = false;

	private $DBTextConnecter;
	private $tableName;

	public function __construct() {
        parent::__construct();
		$this->DBTextConnecter = DBTextConnecter::withTableName('act');
		$this->tableName = 'act';
	}

	public function index() {
		$this->assign('useNg','true');
		return $this->fetch('index');
	}

	public function searchTime() {
	}

	public function edit($id) {
		$this->assign('useNg','true');
		return $this->fetch('edit');
	}

	public function update() {
	}

	public function create() {
		$this->assign('useNg','true');
		return $this->fetch('create');
	}

	public function doCreate() {

	}

	public function delete() {

    }

    public function multiDelete() {

    }

    /*AJAX*/
	public function cellCtrl() {	}


	public function addAct(){	}

	public function updateAct(){
		$actData 	= json_decode(file_get_contents('php://input'), true);
		$actInfo 	= $actData['actInfo'];

		unset($updData);
		$updData['start'] 	= strtotime($actInfo['start']);
		if ($actData['noEndTime'] == true){
			$updData['end'] 	= strtotime('1970-01-01');
		}else{
			$updData['end'] 	= strtotime($actInfo['end']);
		}
		$updData['discount1'] 	=  $actInfo['discount1'];
		$updData['name'] 		=  $actInfo['name'];
		$updData['content'] 	=  $actInfo['content'];

		DB::table('act')->where('id',$actInfo['id'])->update($updData);



		///////////////////////////////
		///  Add discount product
		///////////////////////////////
		$actId = $actInfo['id'];

		$createSeries   = false;
		$createCate     = false;

		$actSeries  = [];
		$actCate    = [];
		$actProd    = [];

		foreach($actData['series'] as $serKey => $serValue){
			if (isset($serValue['select'])){
				if($serValue['select'] == true){
					$createSeries = true;
					$seriesProd = DB::table('product')->alias('prod')
						->join('typeinfo ti','prod.id = ti.parent_id')
						->join('productinfo pi','ti.id = pi.parent_id')
						->join('act_product ap','pi.id=ap.prod_id','LEFT')
						->where('prod.id',$serValue['id'])
						->where('ap.act_prod_id is null')->select();

					foreach($seriesProd as $spKey => $spValue){
						$actSeries[] = $spValue;
					}

				}//if
			}//if
		}//foreach


		foreach($actData['cate'] as $cateKey => $cateValue){
			if (isset($cateValue['select'])){
				if($cateValue['select'] == true){
					$createCate = true;

					$cateProd = DB::table('typeinfo')->alias('ti')
						->join('productinfo pi','ti.id = pi.parent_id')
						->join('act_product ap','pi.id=ap.prod_id','LEFT')
						->where('ti.id',$cateValue['id'])
						->where('ap.act_prod_id is null')->select();
					// echo $sql = Db::table('typeinfo')->getLastSql();

					foreach($cateProd as $cpKey => $cpValue){
						$actCate[] = $cpValue;
					}
				}//if
			}//if
		}//foreach

		if ($createSeries){
			foreach ($actSeries as $asKey => $asValue){
				$actProd[] = [
					'act_id'    => $actId ,
					'prod_id'   => $asValue['id']
				];
			}//foreach
		}elseif($createCate){
			foreach ($actCate as $acKey => $acValue){
				$actProd[] = [
					'act_id'    => $actId ,
					'prod_id'   => $acValue['id']
				];
			}//foreach
		}else{
			foreach ($actData['cateProd'] as $cateProdKey => $cateProdValue){
				if (isset($cateProdValue['select'])){
					if($cateProdValue['select'] == true){
						$actProd[] = [
							'act_id'    => $actId ,
							'prod_id'   => $cateProdValue['id']
						];
					}//if
				}//if
			}//foreach
		}//else

		Db::table('act_product')->insertAll($actProd);

		// 上傳圖片
        if($actData['img']){
            $file_type = explode('/', explode(';', $actData['img'])[0])[1];
            $fileName = 'discount_'.time().'.'.$file_type;
            $filePath = '/upload' . DS .$fileName;
            $fileData = substr($actData['img'],strpos($actData['img'],",") + 1);
            $decodedData = base64_decode($fileData);
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/public/static/index'.$filePath, $decodedData);
            $updateData = ['img'=>$filePath];
            Db::table('act')->where('id', $actId)->update($updateData);
        }

		$retData = ['status' => '200'];
		echo json_encode($retData);

	}

    public function createAct() {
		$actData = json_decode(file_get_contents('php://input'), true);

		$discountAct = Db::table('act')->where('act_type',2)->select();

		$count = count($discountAct);
		if($count < 10){
			$count = '00' . $count;
		}else if($count < 100){
			$count = '0' . $count;
		}

		unset($insData);
		$insData['name'] 		= $actData['name'];
		$insData['content'] 	= $actData['content'];
		$insData['number']		= config('subDeparment'). 'D' . date('Ymd') . $count;
		$insData['discount1'] 	= $actData['discount1'];
		$insData['type'] 		= 0;
		$insData['act_type']	= 2;
		$insData['start'] 		= strtotime($actData['start']);
		$insData['end'] 		= strtotime($actData['end']);

		$actId = Db::name('act')->insertGetId($insData);


		///////////////////////////////
		///  Add discount product
		///////////////////////////////
		$createSeries   = false;
		$createCate     = false;

		$actSeries  = [];
		$actCate    = [];
		$actProd    = [];

		foreach($actData['series'] as $serKey => $serValue){
			if (isset($serValue['select'])){
				if($serValue['select'] == true){
					$createSeries = true;
					$seriesProd = DB::table('product')->alias('prod')
						->join('typeinfo ti','prod.id = ti.parent_id')
						->join('productinfo pi','ti.id = pi.parent_id')
						->join('act_product ap','pi.id=ap.prod_id','LEFT')
						->where('prod.id',$serValue['id'])
						->where('ap.act_prod_id is null')->select();

					foreach($seriesProd as $spKey => $spValue){
						$actSeries[] = $spValue;
					}

				}//if
			}//if
		}//foreach


		foreach($actData['cate'] as $cateKey => $cateValue){
			if (isset($cateValue['select'])){
				if($cateValue['select'] == true){
					$createCate = true;

					$cateProd = DB::table('typeinfo')->alias('ti')
						->join('productinfo pi','ti.id = pi.parent_id')
						->join('act_product ap','pi.id=ap.prod_id','LEFT')
						->where('ti.id',$cateValue['id'])
						->where('ap.act_prod_id is null')->select();
					// echo $sql = Db::table('typeinfo')->getLastSql();

					foreach($cateProd as $cpKey => $cpValue){
						$actCate[] = $cpValue;
					}
				}//if
			}//if
		}//foreach

		if ($createSeries){
			foreach ($actSeries as $asKey => $asValue){
				$actProd[] = [
					'act_id'    => $actId ,
					'prod_id'   => $asValue['id']
				];
			}//foreach
		}elseif($createCate){
			foreach ($actCate as $acKey => $acValue){
				$actProd[] = [
					'act_id'    => $actId ,
					'prod_id'   => $acValue['id']
				];
			}//foreach
		}else{
			foreach ($actData['cateProd'] as $cateProdKey => $cateProdValue){
				if (isset($cateProdValue['select'])){
					if($cateProdValue['select'] == true){
						$actProd[] = [
							'act_id'    => $actId ,
							'prod_id'   => $cateProdValue['id']
						];
					}//if
				}//if
			}//foreach
		}//else

		Db::table('act_product')->insertAll($actProd);

		// 上傳圖片
        if($actData['img']){
            $file_type = explode('/', explode(';', $actData['img'])[0])[1];
            $fileName = 'discount_'.time().'.'.$file_type;
            $filePath = '/upload' . DS .$fileName;
            $fileData = substr($actData['img'],strpos($actData['img'],",") + 1);
            $decodedData = base64_decode($fileData);
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/public/static/index'.$filePath, $decodedData);
            $updateData = ['img'=>$filePath];
            Db::table('act')->where('id', $actId)->update($updateData);
        }

		$retData = ['status' => '200'];
		echo json_encode($retData);
    }

	public function getActProd(){
		$postData = json_decode(file_get_contents('php://input'), true);
		$actId = $postData['actId'];

		$actInfo = Db::table('act')->where('id',$actId)->find();

		// $actInfo['start'] = '2019-03-08';
		// $actInfo['start'] = gmdate("Y-m-d", $actInfo['start']);
		$actInfo['start'] = date('Y-m-d',$actInfo['start']+8*3600);


		if ($actInfo['end'] == -28800){
			$actInfo['end'] = '';
		}else{
			$actInfo['end'] = date('Y-m-d',$actInfo['end']+8*3600);
		}



		$actProd = Db::table('act_product')->alias('ap')
			->join('productinfo pi','ap.prod_id = pi.id')
			->where('ap.act_id',$actId)->select();
			
		foreach($actProd as $k => $v){
			
			$pic1 = json_decode($v['pic'],true);
			
			$actProd[$k]['pic1'] = $pic1[0];
			
		}		

		$retData = [
			'status'  => 200,
			'actInfo' => $actInfo,
			'actProd' => $actProd,
		];

		echo json_encode($retData);

	}

    public function insertAct(){

    }

    public function delActProd(){

    }

    public function getActList(){
    	$type = Request::instance()->post('type');
    	$searchKey = Request::instance()->post('searchKey');
    	$start = Request::instance()->post('start');
    	$end = Request::instance()->post('end');
    	// var_dump($searchKey);
    	if($type == 'keyword' && !empty($searchKey)){
    		$list = Db::table('act')->where('act_type = 2 and name like "%'.$searchKey.'%"')->select();
    		$search = "> 搜尋：".$searchKey;
    	}else if($type == 'date'){
    		$form=Db::table('act');
    		$list = $form->where('act_type',2)
                        ->where(function($query)use($start,$end){
                           $query->whereOr([ /*搜尋的時間區包含開始或結束時間*/
                                'start' => ['between', [strtotime($start), strtotime($end)]],
                                'end' => ['between', [strtotime($start), strtotime($end)]]
                            ])
                            ->whereOr(function($query)use($start,$end){ /*搜尋的時間區間在開始結束時間內*/
                                $query->where([
                                    'start' => ['elt', strtotime($start)],
                                    'end' => ['egt', strtotime($end)]
                                ]);
                            })
                            ->whereOr(function($query)use($start,$end){ /*搜尋的開始時間大於開始時間，且被設定為無結束時間*/
                                $query->where([
                                    'start' => ['elt', strtotime($start)],
                                    'end' => ['eq', '-28800']
                                ]);
                            });
                        })->select();
    					// dump($form->getLastSql());
    		$search = "> 搜尋：時間：".$start."到".$start;
    	}else{
    		$list = Db::table('act')->where('act_type = 2')->select();
    		$search = "";
    	}
		// $list = Db::table('act')->where('act_type',2)->select();

		foreach($list as $liKey => $liValue){
			$list[$liKey]['start'] = date("Y-m-d", $liValue['start']);

			if ($list[$liKey]['end'] == -28800){
				$list[$liKey]['end'] = '沒有設定時間';
			}else{
				$list[$liKey]['end'] = date("Y-m-d", $liValue['end']);
			}
		}

		$retData = ['actList' => $list, 'search'=>$search];

		echo json_encode($retData);
	}

	public function changeOnline(){
		$itemData = json_decode(file_get_contents('php://input'), true);

		$online =  ($itemData['online'] == true) ? 1 : 0;

		Db::table('act')->where('id',$itemData['id'])->update(['online' => $online]);

		$retData = ['status' => '200'];
		echo json_encode($retData);
	}


	public function delAct(){
		$itemData = json_decode(file_get_contents('php://input'), true);

		$delId = Db::table('act')->where('id',$itemData['id'])->delete();

		$delProd = Db::table('act_product')->where('act_id',$itemData['id'])->delete();

		$retData = ['status' => '200'];

		echo json_encode($retData);
	}
	public function getCount(){
		try{
			$act_count = Db::table('act')->where("online = '1' and ( (start <= '".time()."' and end >= '".time()."' ) or  (start <= '".time()."' and end = '-28800' ) ) and act_type = '2' ")->count();
			$outputData = [
				'status' => true,
				'message' => $act_count
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
