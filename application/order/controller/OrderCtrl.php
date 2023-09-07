<?php
namespace app\order\controller;


use app\order\controller\MainController;
use think\Request;
use think\Db;

//photonicClass
use pattern\simpleFactory\orderFactory\OrderFactory;
use pattern\PointRecords;
use app\ajax\controller\Ecpaylogistic;
use pattern\MemberInstance;

class OrderCtrl extends MainController{


	const PER_PAGE_ROWS = 10;
	const SIMPLE_MODE_PAGINATE = false;
	const RECEIPTS_STATE = ['未收款', '已收款'];
	const TRANSPORT_STATE = ['未出貨', '已出貨'];
	const ORDER_STATE = [
		'New' => '新進訂單',
		'Complete' => '完成訂單',
		'Cancel' => '取消訂單',
		'Return' => '退貨訂單'
	];

    private $Order;
	private $tableName;
    private $coupon_tableName;
	
	public $control_register;	// 是否使用報名功能
    public $ecpay_card; 		// 是否使用綠界金流
	public $ecpay_logistic; 	// 是否使用綠界物流
	public $tspg_card; 			// 是否使用台新金流

	public function __construct() {

        parent::__construct();
		$this->tableName = 'orderform';
		$this->coupon_tableName = 'coupon_pool';
		$id = Request::instance()->post('id');

		if ($id != '' && $id != null){
			$this->Order = OrderFactory::createOrder($id, $this->tableName, $this->coupon_tableName);
		}

		// 是否啟用報名功能
		$this->control_register = Db::connect('main_db')->table('excel')->find(14)['value1'];
		$this->assign('control_register', $this->control_register);

		// 是否啟用綠界金流
		$this->ecpay_card = Db::connect('main_db')->table('excel')->find(16)['value1'];
		$this->assign('ecpay_card', $this->ecpay_card);
		// 是否啟用綠界物流
		$this->ecpay_logistic = Db::connect('main_db')->table('excel')->find(17)['value1'];
		$this->assign('ecpay_logistic', $this->ecpay_logistic);
		// 是否啟用台新金流
		$this->tspg_card = Db::connect('main_db')->table('excel')->find(18)['value1'];
		$this->assign('tspg_card', $this->tspg_card);
    }

	public function index($state) {

		$searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);

		$where = "status = '" . $state . "'";

		if($state == 'Trash'){
			$where = "(status = 'Cancel') OR (status = 'Return')";
			$order_sql = 'cancel_date desc';
		}
		if($state == 'New'){
			$where = "(status = 'New')";
			$order_sql = 'create_time desc';
		}
		if($state == 'Complete'){
			$where = "(status = 'Complete')";
			$order_sql = 'create_time desc';
		}

		$rowData = Db::connect('main_db')->table($this->tableName)
			->where($where)
			->where("order_number LIKE '%$searchKey%' or transport_location_name LIKE '%$searchKey%' or transport_location_phone LIKE '%$searchKey%'")
			->order($order_sql)
			->paginate(
				self::PER_PAGE_ROWS,
				self::SIMPLE_MODE_PAGINATE,
				[
					'query' => [
						'searchKey' => $searchKey
					]
				]
			);

		$this->assign('rowData', $rowData);
		$rowDataItem = $rowData->items();
		$rowDataItem = array_map(function ($value) {
			$value['user'] = Db::connect('main_db')->table('account')->find($value['user_id']);
			$value['product'] = json_decode($value['product']);

			$value['product'] = array_map(function ($value){
				return get_object_vars($value);
			}, $value['product']);

			return $value;
		}, $rowDataItem);

		$this->assign('rowDataItem', $rowDataItem);
		return $this->fetch($state . 'Order');
	}

	public function show($id) {
		$singleData = Db::connect('main_db')->table($this->tableName)->find($id);

		// 是否使用存放位置管理
		$config_db = $this->get_shop_db_config($singleData['order_number']);
        $Front_name = $this->get_shop_access($config_db);
		$this->assign('Front_name', $Front_name);

		/*會員資料*/
		$MemberInstance = new MemberInstance($singleData['user_id']);
		$singleData['user'] = $MemberInstance->get_user_data();

		$singleData['receipts_state'] = self::RECEIPTS_STATE[$singleData['receipts_state']];
		$singleData['transport_state'] = self::TRANSPORT_STATE[$singleData['transport_state']];
		$singleData['statusName'] = self::ORDER_STATE[$singleData['status']];

		$singleData['product'] = array_map(function ($value) {
			return get_object_vars($value);
		}, json_decode($singleData['product']));
		foreach($singleData['product'] as $k=>$v){
			if(isset($v['type_id'])){
				$singleData['product'][$k]['type_id_ori'] = explode('_', $v['type_id'])[0];
			}
 		}

		$singleData['discount'] = array_map(function ($value) {
			return get_object_vars($value);
		}, json_decode($singleData['discount']));

		if(empty($singleData['product'][0]['url']))$singleData['product'][0]['url']='none';

		$this->assign('singleData', $singleData);

		//物流單按鈕
		$transportPaperBtn = "";
		if($this->ecpay_logistic==1 && in_array($singleData['transport'], ["宅配","7-11取貨","全家取貨","萊爾富取貨"])){
			if($singleData['AllPayLogisticsID'] != ''){
				$Ecpaylogistic = new Ecpaylogistic();
				$transportPaperBtn = $Ecpaylogistic->checkTransportPaper($id);
				// dump($transportPaperBtn);exit;
			}else{
				$transportPaperBtn = '<br><button onclick="location.href=\'/ajax/EcpayLogistic/createTransportPaper/id/'. $singleData['id'] .'\'" class="NoPrint">產生物流單</button>';
			}
		}

		// dump($transportPaperBtn);
		$this->assign('transportPaperBtn', $transportPaperBtn);
		return $this->fetch('member-order-info');
	}

    public function next() {
		$this->Order->changeStatus2Next();
    }

    /*確認*/
	public function checkReport() {
		try {
			$this->Order->setReportState();
			$outputData = [
				'status' => true,
				'message' => 'success'
			];
			return json($outputData, 200);
		} catch (\Exception $e) {
			$outputData = [
				'status' => false,
				'message' => $e->getMessage()
			];
			return json($outputData, 200);
		}
	}

	/*收款*/
	public function setReceiptsState() {
		try {
			$state = Request::instance()->post('state');
			$this->Order->setReceiptsState($state);
			$outputData = [
				'status' => true,
				'message' => 'success'
			];
			return json($outputData, 200);
		} catch (\Exception $e) {
			$outputData = [
				'status' => false,
				'message' => $e->getMessage()
			];
			return json($outputData, 200);
		}
	}

	/*出貨*/
	public function setTransportState() {
		try {
			$state = Request::instance()->post('state');
			$this->Order->setTransportState($state);
			$outputData = [
				'status' => true,
				'message' => 'success'
			];
			return json($outputData, 200);
		} catch (\Exception $e) {
			$outputData = [
				'status' => false,
				'message' => $e->getMessage()
			];
			return json($outputData, 200);
		}
	}

	/*更新訂單備註*/
	public function setPS() {
		try {
			$ps = Request::instance()->post('ps');
			if($ps){
				$this->Order->setPS($ps);
			}else{
				$ps2 = Request::instance()->post('ps2');
				$id = Request::instance()->post('id');
				if($id){
					Db::connect('main_db')->table('orderform')->where("id = ".$id)->data(['ps2'=>$ps2])->update();
				}
			}
			$outputData = [
				'status' => true,
				'message' => 'success'
			];
			return json($outputData, 200);
		} catch (\Exception $e) {
			$outputData = [
				'status' => false,
				'message' => $e->getMessage()
			];
			return json($outputData, 200);
		}
	}

	/*退貨*/
	public function changeStatus2Return() {

		$reason = Request::instance()->post('reason');

		/*加回數量*/
		// $id = Request::instance()->post('id');
		// $o=Db::connect('main_db')->table($this->tableName)->where('id', $id)->find();
		// $return_num = json_decode($o['product'],true);

		// foreach($return_num as $k => $v){
		// 	// dump($v);
		// 	if(isset($v['type_id'])){
		// 		$pt = Db::connect(config('A_sub'))->table('productinfo_type')->where("id = '".$v['type_id']."'")->find();
		// 		if( (int)$pt['num'] + (int)$v['num'] > (int)$pt['total']){
		// 			Db::connect(config('A_sub'))->table('productinfo_type')->where("id = '".$v['type_id']."'")->inc('total',$v['num'])->update();
		// 		}
		// 		Db::connect(config('A_sub'))->table('productinfo_type')->where("id = '".$v['type_id']."'")->inc('num',$v['num'])->update();
		// 	}
		// }
		/*加回數量*/

		$this->Order->changeStatus2Return($reason);

		$this->success('退貨轉換成功');
	}

	/*	取消*/
	public function changeStatus2Cancel() {

		$id = Request::instance()->post('id');

		// /*加回數量*/
		// $o=Db::connect('main_db')->table($this->tableName)->where('id', $id)->find();
		// $return_num = json_decode($o['product'],true);

		// foreach($return_num as $k => $v){
		// 	// dump($v);
		// 	if(isset($v['type_id'])){
		// 		$pt = Db::connect(config('A_sub'))->table('productinfo_type')->where("id = '".$v['type_id']."'")->find();
		// 		if( (int)$pt['num'] + (int)$v['num'] > (int)$pt['total']){
		// 			Db::connect(config('A_sub'))->table('productinfo_type')->where("id = '".$v['type_id']."'")->inc('total',$v['num'])->update();
		// 		}
		// 		Db::connect(config('A_sub'))->table('productinfo_type')->where("id = '".$v['type_id']."'")->inc('num',$v['num'])->update();
		// 	}
		// }
		// /*加回數量*/

	    try{
	    	$this->Order->changeStatus2Cancel(Request::instance()->post('reason'));
        } catch (\Exception $e){
            $this->dumpException($e);
        }
		$this->success('取消轉換成功');
	}


	/*恢復訂單*/
	public function changeStatus2Restore() {

		$reason = Request::instance()->post();
		$id = $reason['id'];

		// /*扣回數量*/
		// $o=Db::connect('main_db')->table($this->tableName)->where('id', $id)->find();
		// $return_num = json_decode($o['product'],true);

		// foreach($return_num as $k => $v){/*先檢查還有沒有數量*/
		// 	if(isset($v['type_id'])){
		// 		$ck = Db::connect(config('A_sub'))->table('productinfo_type')->where("id = '".$v['type_id']."'")->field('num')->find()['num'];
		// 		if($ck - $v['num'] < 0){
		// 			$this->error('庫存數量不足，此訂單無法恢復');
		// 			exit;
		// 		}
		// 	}
		// }

		// foreach($return_num as $k => $v){/*扣除數量*/
		// 	if(isset($v['type_id'])){
		// 		Db::connect(config('A_sub'))->table('productinfo_type')->where("id = '".$v['type_id']."'")->dec('num',$v['num'])->update();
		// 	}
		// }
		// /*扣回數量*/

        $this_order = Db::connect('main_db')->table('orderform')
            ->find($id);
		$user_point = Db::connect('main_db')->table('account')->field('point')->find($this_order['user_id'])['point'];
		$this_order['discount'] = json_decode($this_order['discount'], true);

		/*回歸紅利*/
        if($this_order['discount']){
            switch ($this_order['discount'][0]['type']) {
                case '紅利':
                	if($user_point <= 0 || $user_point < $this_order['discount'][0]['dis']){
						$this->error('會員點數不足');
					}

					$PointRecords = new PointRecords($this_order['user_id']);
	                $records = $PointRecords->add_records([
	                    'msg'           => '恢復訂單：'.$this_order['order_number'].'，使用紅利線上購物',
	                    'points'        => $this_order['discount'][0]['dis'] * (-1),
	                    'belongs_time'  => $this_order['create_time']
	                ]);

                    break;

                case '優惠券':
                	$config_db = $this->get_shop_db_config($this_order['order_number']);
                	$coupon_id = Db::connect($config_db)->table('coupon_pool')->find($this_order['discount'][0]['coupon_pool_id'])['coupon_id'];
                	$coupon_pool_id = Db::connect($config_db)
                		->table('coupon_pool')
                		->where("coupon_id = ".$coupon_id." and login_time is not null and use_time is null and owner =".$this_order['user_id'])
                		->find();
                	if($coupon_pool_id){
                		$coupon_pool_id = $coupon_pool_id['id'];
                		Db::connect($config_db)
                			->table('coupon_pool')
                			->where('id',$coupon_pool_id)
                			->update(['use_time'=>time()]);
                		$this_order['discount'][0]['coupon_pool_id'] = $coupon_pool_id;
                		Db::connect('main_db')->table('orderform')->where('id', $id)->update(['discount'=>json_encode($this_order['discount'], JSON_UNESCAPED_UNICODE)]);
                	}else{
                		$this->error('優惠券不足');
                	}
                	break;

                default:
                	break;
            }
        }

		Db::connect('main_db')->table('orderform')->where('id', $id)->update(['status'=>'New']);
		$this->success('恢復轉換成功');
	}

    public function delete() {
		$this->Order->delete();
		$this->success('刪除成功');
	}

	public function multiCancel() {

        $idList = Request::instance()->post('idList');
        try{
            if ($idList) {
				$idList = json_decode($idList);
				foreach ($idList as $value) {
					$Order = OrderFactory::createOrder($value, $this->tableName, $this->coupon_tableName);
					$Order->changeStatus2Cancel('');
				}
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('複數取消成功', url('OrderCtrl/index', ['state' => 'Trash']));
	}

	public function multiNext() {
        $idList = Request::instance()->post('idList');
        try{
            if ($idList) {
				$idList = json_decode($idList);
				foreach ($idList as $value) {
					$Order = OrderFactory::createOrder($value, $this->tableName, $this->coupon_tableName);
					$Order->changeStatus2Next();
				}
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('複數拋轉成功', url('OrderCtrl/index', ['state' => 'Complete']));
	}


	public function multiDelete() {
        $idList = Request::instance()->post('idList');
        try{
            if ($idList) {
				$idList = json_decode($idList);
				foreach ($idList as $value) {
					$Order = OrderFactory::createOrder($value, $this->tableName, $this->coupon_tableName);
					$Order->delete();
				}
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('複數刪除成功', url('OrderCtrl/index', ['state' => 'Trash']));
	}

	/*AJAX 物流狀態*/
	public function ajax_logistic_status($id) {
		$singleData = Db::connect('main_db')->table('orderform')->where('id',$id)->find();

		$order = Db::connect('main_db')->table('orderform')->find($id);
		$AllPayLogisticsID = $order['AllPayLogisticsID'];

		$logisticsRecord = Db::connect('main_db')->table('logistics_record lr')
							->Distinct(true)
							->field('lr.time, lc.type, lc.message')
							->join('logistics_code lc', 'lr.RtnCode = lc.code', 'LEFT')
							->where('lr.order_id ='.$id.' and lr.logistics_id = "'.$AllPayLogisticsID.'" and lr.LogisticsType = lc.type  and lr.RtnCode = lc.code')
							->order('lr.id asc')
							->select();

		// dump($logisticsRecord);
		return json($logisticsRecord, 200);
	}

	/*取得對應商品前台的config db設定*/
	public function get_shop_db_config($order_number){
		return substr($order_number,0,1).'_sub';
	}

	/*取得對應商品前台的權限設定*/
	public function get_shop_access ($config_db){
		$purvuew = Db::connect($config_db)->table('admin')->field('purview')->where("permission = 'current'")->find()['purview'];
		$blokc = Db::connect($config_db)->table('backstage_menu_second')->field('id,name,Front_desk,backstage_menu_id')->select();
		$purvuew = json_decode($purvuew,true);
		$Front_desk = [];

		foreach($blokc as $k => $v){
			if(!empty($purvuew[$v['backstage_menu_id']])){
				foreach($purvuew[$v['backstage_menu_id']] as $pk => $pv){
					if($v['id'] == $pv){
						$Front_name[$v['name']]=1;
					}
				}
			}
		}
		// dump($Front_name);exit;

		return $Front_name;
	}

	/* 取得商品品項庫存編碼 */
	public function get_position(){

		$order_number = isset($_POST['order_number']) ? $_POST['order_number'] : $this->error('請提供訂單編號');
		$prod_id = isset($_POST['prod_id']) ? $_POST['prod_id'] : $this->error('請提供商品ID');
		$type_id = isset($_POST['type_id']) ? $_POST['type_id'] : $this->error('請提供品項ID');

		// 處理type_id
		$key_list = explode("_", $type_id);
        $key_id = $key_list[0];
        $key_type = isset($key_list[1]) ? $key_list[1] : 'normal';
        if($key_type == 'coupon' ) $this->error('此商品無須撿貨');

		$config_db = $this->get_shop_db_config($order_number);
		$productinfo = Db::connect($config_db)->table('productinfo')->where('id='.$prod_id)->select();
		if(!$productinfo) $this->error('查無商品');

		$position_portion = Db::connect($config_db)->table('position_portion pp')
    								->field('	
    											pos.name as p_name,
												pos.number as  p_number,
												pos.max as p_max,
												pp.position_number,
												pp.id as pp_id,
												pp.num												
											')
			->where("pp.productinfo_type = '".$key_id."'")
			->join('position pos','pos.id = pp.position_id', 'left')
			->order('pp.position_number asc')
			->select();
		array_walk($position_portion, function($item,$key)use(&$position_portion){
			$position_portion[$key]['p_code'] = $item['p_max'] == "1" ? $item['p_name'].$item['position_number'] : $item['p_name'].str_pad($item['position_number'], strlen($item['p_number']), "0", STR_PAD_LEFT);
		});

		if($productinfo[0]['r_repeat'] == 0){ // 一碼一品項			
			return ['code'=>1, 'position_portion'=>$position_portion];

		}else{  // 一碼多品項
			$orderform = Db::connect('main_db')->table('orderform')->where('order_number="'.$order_number.'"')->select();
        	if(!$orderform) $this->error('查無此訂單'); 

			$product = json_decode( $orderform[0]['product'] );
	        foreach ($product as $k => $v) {
	        	$v = (array)$v;
	        	if(!isset($v['type_id']))
	        		continue;

	        	if($v['type_id'] == $type_id){
	        		Db::connect($config_db)->table('position_portion')->where('product_id='.$prod_id.' AND productinfo_type='.$key_id)->limit(1)->dec('num',$v['num'])->update(); // 釋出庫存編碼、扣數量
	        		Db::connect($config_db)->table('position_portion')->where('num',0)->delete(); // 編碼剩餘數為0則刪除紀錄
					Db::connect($config_db)->table('position_portion')->where('product_id',0)->delete(); // 編碼無對應商品則刪除紀錄
	        		$product[$k]->deal_position= 1;
	        		$product[$k]->position_code = $position_portion[0]['p_code'].":".$v['num'].'個';
	        		break;
	        	}
	        }

	        // 更新訂單內該品項為已撿貨
	        Db::connect('main_db')->table('orderform')->where('order_number="'.$order_number.'"')->update([
	        	'product' => json_encode($product, JSON_UNESCAPED_UNICODE)
	        ]);
			
			return ['code'=>2];
		}
	}

	/* 釋出庫存編碼、扣數量 */
	public function release_position(){
		$order_number = isset($_POST['order_number']) ? $_POST['order_number'] : $this->error('請提供訂單編號');
		$type_id = isset($_POST['type_id']) ? $_POST['type_id'] : $this->error('請提供品項ID');
		$positions = isset($_POST['positions']) ? $_POST['positions'] : $this->error('請提公庫存編碼ID');

		// 處理type_id
		$key_list = explode("_", $type_id);
        $key_id = $key_list[0];
        $key_type = isset($key_list[1]) ? $key_list[1] : 'normal';
        if($key_type == 'coupon' ) $this->error('此商品無須撿貨');

        $orderform = Db::connect('main_db')->table('orderform')->where('order_number="'.$order_number.'"')->select();
        if(!$orderform) $this->error('查無此訂單');

        $config_db = $this->get_shop_db_config($order_number);
        $position_code = [];
        foreach ($positions as $k => $v) {
        	Db::connect($config_db)->table('position_portion')->where('id='.$v['pp_id'])->dec('num',$v['num'])->update(); // 釋出庫存編碼、扣數量
        	array_push($position_code, $v['p_code'].":".$v['num'].'個' );
        }

        Db::connect($config_db)->table('position_portion')->where('num',0)->delete(); // 編碼剩餘數為0則刪除紀錄
		Db::connect($config_db)->table('position_portion')->where('product_id',0)->delete(); // 編碼無對應商品則刪除紀錄

        $product = json_decode( $orderform[0]['product'] );
        foreach ($product as $k => $v) {
        	$v = (array)$v;
        	if(!isset($v['type_id']))
	        	continue;

        	if($v['type_id'] == $type_id){
        		$product[$k]->deal_position = 1;
        		$product[$k]->position_code = join(',', $position_code);
        	}
        }
        // 更新訂單內該品項為已撿貨
        Db::connect('main_db')->table('orderform')->where('order_number="'.$order_number.'"')->update([
        	'product' => json_encode($product, JSON_UNESCAPED_UNICODE)
        ]);

        $this->success('撿貨成功');
	}
}



