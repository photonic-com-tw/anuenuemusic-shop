<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;
use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class Act extends MainController
{
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
		$searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);
		$rowData = Db::table($this->tableName)
						->where('act_type',1)
						->where(function($query) use($searchKey){
							$query->whereOr([
								'name' => ['like', '%'.$searchKey.'%'],
								'number' => ['like', '%'.$searchKey.'%']
							]);
						})
						->order('id desc')
						->paginate(
							self::PER_PAGE_ROWS,
							self::SIMPLE_MODE_PAGINATE,
							[
								'query' => ['searchKey' => $searchKey]
							]
						);
		// echo $sql = Db::table($this->tableName)->getLastSql();
        $this->assign('start', date('Y-m-d'));
        $this->assign('end', date('Y-m-d'));
		$this->assign('act', $rowData);
		return $this->fetch('act');
	}

	public function searchTime() {
		$start = Request::instance()->get('start');
		$end = Request::instance()->get('end');
        $this->assign('start', $start);
        $this->assign('end', $end);
		$this->assign('searchKey', '時間：' . $start . '到' . $end);

		$rowData = Db::table($this->tableName)
						->order('id desc')
                        ->where('act_type',1)
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
                        })
						->paginate(
							self::PER_PAGE_ROWS,
							self::SIMPLE_MODE_PAGINATE,
							[
								'query' => [
									'start' => $start,
									'end' => $end
								]
							]
						);
		$this->assign('act', $rowData);
		return $this->fetch('act');
	}

	public function edit($id) {
		$singleData = Db::table($this->tableName)->find($id);
		$this->assign('singleData', $singleData);
        $this->assign('actId', $id);
        $this->assign('useNg','true');
		return $this->fetch('act-edit');
	}

	public function update() {
		try{
			$newData = [
				'id' => Request::instance()->post('id'),
				'name' => Request::instance()->post('name'),
				// 'ps' => Request::instance()->post('ps'),
				'content' => Request::instance()->post('content'),
				'start' => strtotime(Request::instance()->post('start')),
				'end' => strtotime(Request::instance()->post('end')),
				'location' => Request::instance()->post('location'),
				'type' => Request::instance()->post('discountType'),
				'online' => 1
			];

			$newData['online1'] = Request::instance()->post('online1'.$newData['type']) ? 1 : 0;
			$newData['condition1'] = Request::instance()->post('condition1'.$newData['type']);
			$newData['discount1'] = Request::instance()->post('discount1'.$newData['type']);
			$newData['online2'] = Request::instance()->post('online2'.$newData['type']) ? 1 : 0;
			$newData['condition2'] = Request::instance()->post('condition2'.$newData['type']);
			$newData['discount2'] = Request::instance()->post('discount2'.$newData['type']);
			$newData['online3'] = Request::instance()->post('online3'.$newData['type']) ? 1 : 0;
			$newData['condition3'] = Request::instance()->post('condition3'.$newData['type']);
			$newData['discount3'] = Request::instance()->post('discount3'.$newData['type']);
			$this->DBTextConnecter->setDataArray($newData);
			$this->DBTextConnecter->upTextRow();

            // 上傳圖片
            $img = Request::instance()->file('img');
            if($img){
                $DBFileConnecter = DBFileConnecter::withTableName('act');
                $DBFileConnecter->setFileArray([
                    'img' => $img
                ]);
                $DBFileConnecter->setPrivateKeyId(Request::instance()->post('id'));
                $DBFileConnecter->upFileRow();
            }

		} catch (\Exception $e) {
            $this->dumpException($e);
        }
        $this->success('更新成功');
	}

	public function create() {
        $this->assign('useNg','true');
		return $this->fetch('act-info');
	}

	// public function doCreate() {
	// 	//var_dump(Request::instance()->post());die();
	// 	try{
	// 		$newData = [
	// 			'name' => Request::instance()->post('name'),
	// 			// 'ps' => Request::instance()->post('ps'),
	// 			'content' => Request::instance()->post('content'),
	// 			'start' => strtotime(Request::instance()->post('start')),
	// 			'end' => strtotime(Request::instance()->post('end')),
	// 			'location' => Request::instance()->post('location'),
	// 			'type' => Request::instance()->post('discountType'),
	// 			'online' => 1
	// 		];

	// 		$count = $this->getNumber();
	// 		$newData['number'] = config('subDeparment') . 'A' . date('Ymd') . $count;

	// 		$newData['online1'] = Request::instance()->post('online1'.$newData['type']) ? 1 : 0;
	// 		$newData['condition1'] = Request::instance()->post('condition1'.$newData['type']);
	// 		$newData['discount1'] = Request::instance()->post('discount1'.$newData['type']);
	// 		$newData['online2'] = Request::instance()->post('online2'.$newData['type']) ? 1 : 0;
	// 		$newData['condition2'] = Request::instance()->post('condition2'.$newData['type']);
	// 		$newData['discount2'] = Request::instance()->post('discount2'.$newData['type']);
	// 		$newData['online3'] = Request::instance()->post('online3'.$newData['type']) ? 1 : 0;
	// 		$newData['condition3'] = Request::instance()->post('condition3'.$newData['type']);
	// 		$newData['discount3'] = Request::instance()->post('discount3'.$newData['type']);

	// 		$this->DBTextConnecter->setDataArray($newData);
	// 		$this->DBTextConnecter->createTextRow();

	// 	} catch (\Exception $e) {
 //            $this->dumpException($e);
 //        }

 //        $this->success('新增成功', url('act/index'));
	// }

	public function delete() {
        $id = Request::instance()->get('id');
        try{
            Db::table($this->tableName)->delete($id);
            Db::table('act_product')->where('act_id',$id)->delete();
        } catch (\Exception $e){
            $this->dumpException($e);
        }

        $this->success('刪除成功', url('act/index'));
    }

    public function multiDelete() {
        $idList = Request::instance()->post('id');
        try{
            if ($idList) {
                $idList = json_decode($idList);
                Db::table($this->tableName)->delete($idList);
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功', url('act/index'));
    }

    /*AJAX*/
	public function cellCtrl() {
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

	public function addAct(){
        $newData = [
            'name'      => Request::instance()->post('name'),
            // 'ps'        => Request::instance()->post('ps'),
            'content'   => Request::instance()->post('content'),
            'start'     => strtotime(Request::instance()->post('start')),
            'end'       => strtotime(Request::instance()->post('end')),
            'location'  => Request::instance()->post('location'),
            'type'      => Request::instance()->post('discountType'),
            'online'    => 1
        ];

        $count = $this->getNumber();
        $newData['number'] = config('subDeparment') . 'A' . date('Ymd') . $count;

        $newData['online1'] = Request::instance()->post('online1'.$newData['type']) ? 1 : 0;
        $newData['condition1'] = Request::instance()->post('condition1'.$newData['type']);
        $newData['discount1'] = Request::instance()->post('discount1'.$newData['type']);
        $newData['online2'] = Request::instance()->post('online2'.$newData['type']) ? 1 : 0;
        $newData['condition2'] = Request::instance()->post('condition2'.$newData['type']);
        $newData['discount2'] = Request::instance()->post('discount2'.$newData['type']);
        $newData['online3'] = Request::instance()->post('online3'.$newData['type']) ? 1 : 0;
        $newData['condition3'] = Request::instance()->post('condition3'.$newData['type']);
        $newData['discount3'] = Request::instance()->post('discount3'.$newData['type']);

        $this->DBTextConnecter->setDataArray($newData);
        $this->DBTextConnecter->createTextRow();
    }

    public function createAct() {
        // var_dump(Request::instance()->post());die();
        $actData = json_decode(file_get_contents('php://input'), true);
        try{
            $newData = [
                'name'      => $actData['name'],
                // 'ps'        => $actData['ps'],
                'content'   => $actData['content'],
                'start'     => strtotime($actData['start']),
                'end'       => strtotime($actData['end']),
                'location'  => $actData['location'],
                'type'      => $actData['discountType'],
                'online'    => 1
            ];

            $count = $this->getNumber();
            $newData['number'] = config('subDeparment') . 'A' . date('Ymd') . $count;

            // $newData['online1'] = Request::instance()->post('online1'.$newData['type']) ? 1 : 0;
            if (isset($actData['disc'][0]['sel'])){
                if ($actData['disc'][0]['sel'] == 'true')   $newData['online1'] = 1;
                else                                        $newData['online1'] = 0;
            }else{
                $newData['online1'] = 0;
            }

            if(isset($actData['disc'][0]['cond']))
                $newData['condition1'] = $actData['disc'][0]['cond'];//Request::instance()->post('condition1'.$newData['type']);
            if(isset($actData['disc'][0]['value']))
                $newData['discount1'] = $actData['disc'][0]['value'];//Request::instance()->post('discount1'.$newData['type']);

            // $newData['online2'] = Request::instance()->post('online2'.$newData['type']) ? 1 : 0;
            if (isset($actData['disc'][1]['sel'])){
                if ($actData['disc'][1]['sel'] == 'true')   $newData['online2'] = 1;
                else                                        $newData['online2'] = 0;
            }else{
                $newData['online2'] = 0;
            }

            if(isset($actData['disc'][1]['cond']))
                $newData['condition2'] = $actData['disc'][1]['cond'];//Request::instance()->post('condition2'.$newData['type']);
            if(isset($actData['disc'][1]['value']))
                $newData['discount2'] = $actData['disc'][1]['value'];//Request::instance()->post('discount2'.$newData['type']);

            // $newData['online3'] = Request::instance()->post('online3'.$newData['type']) ? 1 : 0;

            if (isset($actData['disc'][2]['sel'])){
                if ($actData['disc'][2]['sel'] == 'true')   $newData['online3'] = 1;
                else                                        $newData['online3'] = 0;
            }else{
                $newData['online3'] = 0;
            }

            if(isset($actData['disc'][2]['cond']))
                $newData['condition3'] = $actData['disc'][2]['cond'];//Request::instance()->post('condition3'.$newData['type']);
            if(isset($actData['disc'][2]['value']))
                $newData['discount3'] = $actData['disc'][2]['value'];//Request::instance()->post('discount3'.$newData['type']);

            $this->DBTextConnecter->setDataArray($newData);
            $insertGetId = $this->DBTextConnecter->createTextRow();

            // 上傳圖片
            if($actData['img']){
                $file_type = explode('/', explode(';', $actData['img'])[0])[1];
                $fileName = 'act_'.time().'.'.$file_type;
                $filePath = '/upload' . DS .$fileName;
                $fileData = substr($actData['img'],strpos($actData['img'],",") + 1);
                $decodedData = base64_decode($fileData);
                file_put_contents($_SERVER['DOCUMENT_ROOT'].'/public/static/index'.$filePath, $decodedData);
                $updateData = ['img'=>$filePath];
                Db::table('act')->where('id', $insertGetId)->update($updateData);
            }

        } catch (\Exception $e) {
            $this->dumpException($e);
        }

        //$this->success('新增成功', url('act/index'));
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
                    'act_id'    => $insertGetId,
                    'prod_id'   => $asValue['id']
                ];
            }//foreach
        }elseif($createCate){
            foreach ($actCate as $acKey => $acValue){
                $actProd[] = [
                    'act_id'    => $insertGetId,
                    'prod_id'   => $acValue['id']
                ];
            }//foreach
        }else{
            foreach ($actData['cateProd'] as $cateProdKey => $cateProdValue){
                if (isset($cateProdValue['select'])){
                    if($cateProdValue['select'] == true){
                        $actProd[] = [
                            'act_id'    => $insertGetId,
                            'prod_id'   => $cateProdValue['id']
                        ];
                    }//if
                }//if
            }//foreach
        }//else

        Db::table('act_product')->insertAll($actProd);
        $echoData = [
            'status'    => 200,
            'actData'   => $actData,
            'actProd'   => $actProd
        ];

        echo json_encode($echoData);
    }

    public function getActProd(){
        $postData = json_decode(file_get_contents('php://input'), true);
        $actId = $postData['actId'];
        $actProd = Db::table('act_product')->alias('ap')
                    ->join('productinfo pi','ap.prod_id = pi.id')
                    ->where('ap.act_id',$actId)->select();
					
		foreach($actProd as $k => $v){
			$pic1 = json_decode($v['pic'],true);
			$actProd[$k]['pic1'] = $pic1[0];
		}			

        $retData = [
            'status'  => 200,
            'actProd' => $actProd,
        ];

        echo json_encode($retData);
    }

    public function insertAct(){
	    $postData   = json_decode(file_get_contents('php://input'), true);
        $actData    = $postData['actData'];
        $actId      = $postData['actId'];

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
                        ->where('prod.id',$serValue['id'])->select();

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
                        ->where('ti.id',$cateValue['id'])->select();

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
        $echoData = [
            'status'    => 200,
            'actData'   => $actData,
            'actProd'   => $actProd
        ];

        echo json_encode($echoData);
    }

    public function delActProd(){
        $postData = json_decode(file_get_contents('php://input'), true);
        $delProd = $postData['cateProd'];
        foreach($delProd as $dpKey => $dpValue){
            if (isset($dpValue['select'])) {
                if ($dpValue['select'] == true) {
                    Db::table('act_product')->where('prod_id',$dpValue['id'])->delete();
                }
            }
        }

        $retData = [
            'status'  => 200,
            'delProd' => $delProd,
        ];

        echo json_encode($retData);
    }

	public function getCount(){
		try{
			$act_count = Db::table('act')->where("online = '1' and ( (start <= '".time()."' and end >= '".time()."' ) or  (start <= '".time()."' and end = '-28800' ) ) and act_type = '1' ")->count();
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

    public function getNumber(){
        $count = Db::table('act')->where('number like "'.config('subDeparment').'A'.date('Ymd').'%"')->order('id desc')->find();
        $count = $count ? intval(substr($count['number'],-3)) + 1 : 1;
        if($count < 10){
            $count = '00' . $count;
        }else if($count < 100){
            $count = '0' . $count;
        }

        return $count;
    }
}















































