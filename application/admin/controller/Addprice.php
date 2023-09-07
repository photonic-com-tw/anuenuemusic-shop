<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use app\admin\controller\Productinfo;

class Addprice extends MainController
{
	public function __construct() {
        parent::__construct();
		$this->table = 'addprice';
		$this->actProdTable = 'addprice_product';
		$this->ruleProdTable = 'addprice_rule';
	}

	/*PAGE---------------------------------*/
	public function index() {
		$searchtype = isset($_GET['searchtype']) ? $_GET['searchtype'] : 0;
		$this->assign('searchtype', $searchtype);
		$this->assign('useNg','true');
		return $this->fetch('index');
	}
	public function edit($id) {
		$this->assign('useNg','true');
		return $this->fetch('edit');
	}
	public function print($id) {
		$this->assign('useNg','true');
		return $this->fetch('print');
	}

	/*API---------------------------------*/
	/*新增加價購*/
	public function doCreate() {
		$insertData = $_POST;
		$insertData['end_time'] = $insertData['noEndTime'] ? '' : $insertData['end_time'];
		if(!$insertData['title']){ $this->error('請輸入標題'); }
		if(!$insertData['discount']){ $this->error('請輸入優惠折扣'); }
		if($insertData['discount']<0 || $insertData['discount']>1){ $this->error('優惠折扣請輸入0~1之間的數'); }
		if(!$insertData['start_time']){ $this->error('請選擇開始時間'); }
		if(!$insertData['end_time'] && !isset($insertData['noEndTime']) ){ $this->error('請選擇結束時間'); }
		unset($insertData['noEndTime']);

		Db::table($this->table)->insert($insertData);
		return $this->success('新增成功');
    }
    /*更新加價購資料*/
	public function doUpdate(){
		$actData = json_decode(file_get_contents('php://input'), true);
		$actInfo = $actData['act'];
		// dump($actData);exit;

		if(!$actInfo['title']){ $this->error('請輸入標題'); }
		if(!$actInfo['discount']){ $this->error('請輸入優惠折扣'); }
		if($actInfo['discount']<0 || $actInfo['discount']>1){ $this->error('優惠折扣請輸入0~1之間的數'); }
		if(!$actInfo['start_time']){ $this->error('請選擇開始時間'); }
		if(!$actInfo['end_time'] && !isset($actInfo['noEndTime']) ){ $this->error('請選擇結束時間'); }
		$actInfo['end_time'] = $actInfo['noEndTime'] ? '' : $actInfo['end_time'];
		unset($actInfo['noEndTime']);
		
		DB::table($this->table)->where('id',$actInfo['id'])->update($actInfo);
		$retData = ['status' => '200', 'msg' => '更新成功'];
		echo json_encode($retData);
	}
	/*更新狀態*/
    public function changeOnline(){
		$itemData = $_POST;

		$online =  ($itemData['online'] == 'true') ? 1 : 0;
		$result = Db::table($this->table)->where('id',$itemData['id'])->update(['online' => $online]);

		if($result){
			$this->success('修改成功');
		}else{
			$this->error('修改失敗');
		}
	}

    /*依條件取得加價購*/
    public function getList(){
    	$type = Request::instance()->post('type');
    	$searchKey = Request::instance()->post('searchKey');
    	$start = Request::instance()->post('start');
    	$end = Request::instance()->post('end');
    	$searchtype = Request::instance()->post('searchtype');

    	if($type == 'keyword' && !empty($searchKey)){
    		$list = Db::table($this->table)->where('title like "%'.$searchKey.'%" OR location like "%'.$searchKey.'%"');
    		$search = "> 搜尋：".$searchKey;
    	}else{
    		$list = Db::table($this->table);
    		$search = "";
    	}
		$list = $list->select();

		if($type == 'date'){
    		$list = $this->getListByDate($start, $end);
    		$search = "> 搜尋：時間：".$start."到".$start;
    	}

    	/*計算各加價購展出商品數量*/
    	foreach ($list as $key => $value) {
    		$list[$key]['count'] = Db::table($this->actProdTable)->where('addprice_id',$value['id'])->count();
    		if($value['end_time']=="0000-00-00 00:00:00") $list[$key]['end_time'] = "沒有結束時間";
    	}

		$retData = ['actList' => $list, 'search'=>$search];
		echo json_encode($retData);
	}
	/*依時間取得加價購資料*/
	static public function getListByDate($start,$end, $frontEnd=false){
		$db = Db::table('addprice');
		$list = $db->whereOr([ /*搜尋的時間區包含開始或結束時間*/
						'UNIX_TIMESTAMP(start_time)' => ['between', [strtotime($start), strtotime($end)]],
						'UNIX_TIMESTAMP(end_time)' => ['between', [strtotime($start), strtotime($end)]]
					])
					->whereOr(function($query)use($start,$end){ /*搜尋的時間區間在開始結束時間內*/
					    $query->where([
							'UNIX_TIMESTAMP(start_time)' => ['elt', strtotime($start)],
							'UNIX_TIMESTAMP(end_time)' => ['egt', strtotime($end)]
						]);
					})->whereOr(function($query)use($start,$end){ /*搜尋的開始時間大於開始時間，且被設定為無結束時間*/
					    $query->where([
							'UNIX_TIMESTAMP(start_time)' => ['elt', strtotime($start)],
							'end_time' => ['eq', '0000-00-00 00:00:00']
						]);
					});
		if($frontEnd) $list->having('online',1);

		$list = $list->select();
		// dump($db->getLastSql());
		return $list;
	}
	/*刪除加價購*/
	public function doDel(){
		/*刪除加價購*/
		Db::table($this->table)->where('id',$_POST['id'])->delete();

		/*刪除加價購的紀錄*/
		Db::table($this->actProdTable)->where('addprice_id',$_POST['id'])->delete();

		return $this->success('刪除成功');
	}

	/*取得加價購內容+商品*/
	public function getDetail(){
		$_POST = json_decode(file_get_contents('php://input'), true);
		$actId = $_POST['actId'];

		$actInfo = Db::table($this->table)->where('id ="'.$actId.'"')->find();
		$actInfo['noEndTime'] = $actInfo['end_time'] == '0000-00-00 00:00:00' ? true : false;

		$ruleProd = Db::table($this->ruleProdTable)->alias('adrp')
				->field('pt.*,pi.title pi_title,pi.pic, adrp.id as adrp_id')
				->join('productinfo_type pt', 'pt.id = adrp.product_type_id', 'right')
				->join('productinfo pi','pi.id = pt.product_id')
				->where('adrp.addprice_id = "'.$actId.'"')->select();
		foreach($ruleProd as $k => $v){
			$pic1 = json_decode($v['pic'],true);
			$ruleProd[$k]['pic1'] = $pic1[0];

			// $_POST['array'] = $v['final_array'];
			// $ruleProd[$k]['show_array'] = json_decode(Productinfo::show_array());
			// $ruleProd[$k]['productinfo_type'] = Db::table('productinfo_type')->where('product_type_id ="'.$v['id'].'"')->find();
		}

		$actProd = Db::table($this->actProdTable)->alias('adpp')
				->field('pt.*,pi.title pi_title,pi.pic, adpp.id as adpp_id, adpp.limit_num')
				->join('productinfo_type pt', 'pt.id = adpp.product_type_id', 'right')
				->join('productinfo pi','pi.id = pt.product_id')
				->where('adpp.addprice_id = "'.$actId.'"')->select();
		foreach($actProd as $k => $v){
			$pic1 = json_decode($v['pic'],true);
			$actProd[$k]['pic1'] = $pic1[0];

			// $_POST['array'] = $v['final_array'];
			// $actProd[$k]['show_array'] = json_decode(Productinfo::show_array());
			// $actProd[$k]['productinfo_type'] = Db::table('productinfo_type')->where('product_type_id ="'.$v['id'].'"')->find();
		}

		$retData = [
			'status'  => 200,
			'actInfo' => $actInfo,
			'ruleProd'=> $ruleProd,
			'actProd' => $actProd,
		];

		echo json_encode($retData);
		// return $retData;
	}


	/*添加條件商品*/
	public function addRuleProd(){
		echo $this->addProd($this->ruleProdTable);
	}
	/*添加活動商品*/
	public function addActProd(){
		echo $this->addProd($this->actProdTable);
	}
	/*添加商品*/
	public function addProd($targetTable){
		$actData = json_decode(file_get_contents('php://input'), true);
		$actId 	 = $actData['actId'];

		/*處理添加商品資料*/
		$addProd = [];
		foreach ($actData['cateProd'] as $k_p => $v_p){
			if (isset($v_p['select'])){
				if($v_p['select'] == true){
					$addProd[] = [
						'addprice_id'    => $actId ,
						'product_type_id'=> $v_p['id']
					];
				}
			}
		}

		Db::table($targetTable)->insertAll($addProd);
		$retData = ['status' => '200', 'msg' => '添加成功'];

		return json_encode($retData);
	}


	/*刪除條件商品*/
	public function delRuleProd(){
		echo $this->delProd($this->ruleProdTable);
	}
	/*刪除活動商品*/
	public function delActProd(){
		echo $this->delProd($this->actProdTable);
	}
    /*刪除加價購商品*/
	public function delProd($targetTable){
        $postData = json_decode(file_get_contents('php://input'), true);
        $actId = $postData['actId'];

        $delProd = $postData['cateProd'];
        foreach($delProd as $d_k => $d_v){
            if (isset($d_v['select'])) {
                if ($d_v['select'] == true) {
                    Db::table($targetTable)->where('product_type_id',$d_v['id'])->where('addprice_id',$actId)->delete();
                }
            }
        }

        $retData = [
            'status'  => 200,
            'delProd' => $delProd,
        ];
        return json_encode($retData);
	}


	/*取得允許添加的條件商品清單*/
	public function getAddableRuleProd(){
		return $this->addableProduct($this->ruleProdTable);
	}
	/*取得允許添加的活動商品清單*/
	public function getAddableActProd(){
		return $this->addableProduct($this->actProdTable);
	}
	/*取得允許添加的商品清單*/
	public function addableProduct($targetTable, $useCate=true){
		$id = Request::instance()->post('cateId');
		$first = Request::instance()->post('first');
		$actId = Request::instance()->post('actId');
		// dump($id,$first,$actId);
		// exit;

		/*取得此加價購資料*/
		$actInfo = Db::table($this->table)->where('id',$actId)->find();

		/*找已加入此加價購的商品*/
		$usedProduct = Db::table($targetTable)->where('addprice_id',$actId)->select();
		$usedProduct_where = [];
		array_walk($usedProduct, function($item)use(&$usedProduct_where){
			array_push($usedProduct_where, $item['product_type_id']);
		});
		$usedProduct_where = $usedProduct_where ? ' AND pt.id not in ('. implode(',', $usedProduct_where).') ' : ' AND true ';
		// dump($usedProduct_where);

		/*找出未勾選的品項*/
		$productinfo = Db::table('productinfo')->alias('pi')->field('pt.*,pi.title pi_title,pi.pic')
					->join('productinfo_type pt', 'pt.product_id = pi.id', 'right');
		if($useCate){
			/*階層搜尋*/
			$productinfo = $productinfo->join('productinfo_orders po','po.prod_id = productinfo.id', 'left')
						->where('true '.$usedProduct_where);
			if($first){
				//是第一階(分館)
				$productinfo = $productinfo->where("final_array like '%\"prev_id\":\"".$id."\"%\"parent_id\":\"0\"%'");
				$productinfo_orders_where = 'po.prev_id='.$id.' AND po.branch_id=0';
				$order_sql = "po.order_id asc, pi.id desc";
			}else{
				//其他(分類)
				$productinfo = $productinfo->where("final_array like '%\"branch_id\":\"".$id."\"%' ");
				$productinfo_orders_where = 'po.branch_id='.$id;
				$order_sql = "po.order_id asc, pi.id desc";
			}

			$productinfo = $productinfo->where($productinfo_orders_where)->order($order_sql);

		}else{
			/*無階層搜尋*/
			$productinfo = $productinfo->where('true '.$usedProduct_where);
		}

		$productinfo = $productinfo->order('pt.order_id asc')->order('pt.id asc')->select();
		
		foreach($productinfo as $k => $v){
			$pic1 = json_decode($v['pic'],true);
			$productinfo[$k]['pic1'] = $pic1[0];
		}
		// dump($productinfo);
		// exit;
			
		//echo Db::table('productinfo')->getLastSql();
		$data = ['cateId' => $id,'productinfo' => $productinfo];
		return json($data, 200);
	}

	public function updatelimitNum(){
		$id = $_POST['adpp_id'];
		$result = Db::table($this->actProdTable)->where('id ="'.$id.'"')->update(['limit_num'=>$_POST['limitNum']]);
		if($result){
			$this->success('修改成功');
		}else{
			$this->error('修改失敗');
		}
	}
}
