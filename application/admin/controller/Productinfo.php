<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;
use GuzzleHttp\Client;

class Productinfo extends MainController{

	private $resTableName;
	private $DBTextConnecter;
	private $DBFileConnecter;

	const PER_PAGE_ROWS = 10;
	const SIMPLE_MODE_PAGINATE = false;
	public $img_Quantity;

	public function __construct() {
        parent::__construct();
        $this->resTableName = 'productinfo';
        $this->DBTextConnecter = DBTextConnecter::withTableName('productinfo');
        $this->DBFileConnecter = DBFileConnecter::withTableName('productinfo');

        /* 三固定屬性名稱 */
        $this->assign('property1', property1);
		$this->assign('property2', property2);
		$this->assign('property3', property3);

        /* 商品照片數量 */
		$this->img_Quantity = Db::connect('main_db')->table('excel')->field('value1')->find(3)['value1'];
		$this->assign('img_Quantity', $this->img_Quantity);

        /* 商品可否上傳影片 */
		$upload_film = Db::connect('main_db')->table('excel')->where('id = 6')->find()['value1'];
		$this->assign('upload_film', $upload_film);
        
        /* 商品可否設定刷卡 */
		$card_pay = Db::connect('main_db')->table('excel')->where('id = 7')->find()['value1'];
		$this->assign('card_pay', $card_pay);

		/* 商品可否嵌入EDM */
		$insert_edm = Db::connect('main_db')->table('excel')->where('id = 8')->find()['value1'];
		$this->assign('insert_edm', $insert_edm);
		
		/* 商品可使用特價商品 */
		$use_sepc_price = Db::connect('main_db')->table('excel')->where('id = 10')->find()['value1'];
		$this->assign('use_sepc_price', $use_sepc_price);

		/* 商品可限用運法 */
		$product_shipping = Db::connect('main_db')->table('excel')->where('id = 15')->find()['value1'];
		$this->assign('product_shipping', $product_shipping);

		/* 商品可限用運法 */
		$product_shipping = Db::connect('main_db')->table('excel')->where('id = 15')->find()['value1'];
		$this->assign('product_shipping', $product_shipping);
	}

	/* 展示庫存編碼 */
	public function show_position_portion() {

		$product_id = $_GET['searchKey']??'';
		$datetime = $_GET['datetime']??'';

	
		if($datetime != ''){
			$position_portion = Db::table('position_portion Pp')
							->where("Pp.product_id = '".$product_id."' and Pp.num != '0' and Pp.datetime = '".$datetime."' ")
							->field('Pp.*,pos.name,pos.number,pt.title,pos.max')
							->join('position pos','Pp.position_id = pos.id')
							->join('productinfo_type pt','Pp.productinfo_type = pt.id','left')
							->order('pt.order_id asc, pt.id asc, Pp.position_number asc')
							->select();
		}else{

			$position_portion = Db::table('position_portion Pp')
							->where("Pp.product_id = '".$product_id."' and Pp.num != '0' ")
							->field('Pp.*,pos.name,pos.number,pt.title,pos.max')
							->join('position pos','Pp.position_id = pos.id')
							->join('productinfo_type pt','Pp.productinfo_type = pt.id','left')
							->order('pt.order_id asc, pt.id asc, Pp.position_number asc')
							->select();
		}

		$searchName = Db::table('productinfo')->field('title')->find($product_id)['title'];
		// dump($position_portion); exit;


		$show_list=[];
		$number = '';
		foreach($position_portion as $pk => $pv){

			if($pv['max'] == 1)
				$pv['number'] = 0; // 如果是無限，則左側不補零
			$data = [
				'position_code'	=> $pv['name'].str_pad($pv['position_number'],strlen($pv['number']),'0',STR_PAD_LEFT),
				'p_type'		=> $pv['title'],
				'num'			=> $pv['num']
			];

			array_push($show_list, $data);
		}
		// dump($show_list);exit;


		$this->assign('searchKey', $product_id);
		$this->assign('searchName', $searchName);
		$this->assign('show_list_spp', $show_list);

		return $this->fetch('show_position_portion');
	}

	/* 利用條碼判斷新增or編輯商品 */
	public function input_ISBN(){//ISBN查詢  2019/12/18

		$ISBN = $_POST['ISBN'];
		$ISBN = Db::table('productinfo')->field('id')->where("ISBN = '".$ISBN."' and ISBN != ''")->find();

		return $ISBN['id'];
	}

	/* 回傳商品顯示位置 */
	public static function show_array(){//多階層查詢  2019/12/17
		$_POST['array'] = $_POST['array'] ? $_POST['array'] : '[]';
		$array = json_decode($_POST['array'],true);
		$type = $_POST['type']?? 'text';
		//dump($array);

		$re_array = [];
		foreach($array as $k =>$v){

			if($v['prev_id'] !=0 && $v['parent_id'] ==0 && $v['branch_id'] ==0){
				$return = Db::table('product')->field('id, title, online')->find($v['prev_id']);	
				$re_array[$k] = array($return['title']);
				continue;
			}

			if($v['prev_id'] !=0 && $v['parent_id'] !=0 && $v['branch_id'] ==0){
				$return = Db::table('product')->field('id, title, online')->find($v['prev_id']);	
				$return2 = Db::table('typeinfo')->field('id, title, online')->find($v['parent_id']);	
				$re_array[$k] = array($return['title'],$return2['title']);
				continue;
			}	

			if($v['prev_id'] !=0 && $v['parent_id'] !=0 && $v['branch_id'] !=0){

				$return = Db::table('product')->field('id, title, online')->find($v['prev_id']);	
				$return2 = Db::table('typeinfo')->field('id, title, online')->find($v['parent_id']);	
				$re_array[$k] = array($return['title'],$return2['title']);
				$return3 = Db::table('typeinfo')
						   ->field('id, title,parent_id,branch_id, online')
						   ->find($v['branch_id']);	
				$array3 = [];

				while($return3['branch_id'] != 0){

					//dump($return3);
					array_push($array3,$return3['title']);
					$return3 = Db::table('typeinfo')->field('id, title,parent_id,branch_id, online')->find($return3['branch_id']);	
				}

				//dump($array3);
				for($i=count($array3)-1;$i >=0;$i--){
					array_push($re_array[$k],$array3[$i]);
				}

				continue;
			}		
		}
		//dump($re_array);

		switch($type){
			case 'text':
				//return json_encode($re_array);
				//dump($re_array);
				$text=[];
				foreach($re_array as $k => $v){

					//dump($re_array[$k]);
					foreach($re_array[$k] as $k2 => $v2){
						if(empty($text[$k]))
							$text[$k] = '';
						$text[$k].=$v2;

						if(!empty($re_array[$k][$k2+1]))
							$text[$k].=' > ';
					}
				}
				return json_encode($text,JSON_UNESCAPED_UNICODE);
				break;

			case 'array':
				return json_encode($re_array,JSON_UNESCAPED_UNICODE);
				break;	
		}
	}

	/* 新增 */
	public function allcreate() {//fat

		/* 指派資料 */
		$this->assign_data();

		$final_array = '{}';
		$back =  url('all/index');
		
		// 根據分類樹點擊的位置預設階層
		$prev = Request::instance()->param('prev_id')??'';
		$parent_id = Request::instance()->param('parent_id')??'';
		$branch_id = Request::instance()->param('branch_id')??'';
		if($prev != ''){
			$final_array = '[{"prev_id":"'.$prev.'","branch_id":"0","parent_id":"0"}]';
			$back = url('typeinfo/index', ['parent_id' => $prev]);
		}
		if($parent_id != ''){
			$prev = Db::table('typeinfo')->field('parent_id')->find($parent_id);
			$final_array = '[{"prev_id":"'.$prev['parent_id'].'","branch_id":"'.$parent_id.'","parent_id":"'.$parent_id.'"}]';
			$back = url('branch/index', ['id' => $parent_id]);
		}
		if($branch_id != ''){
			$parent_id = Db::table('typeinfo')->field('id,parent_id,branch_id')->find($branch_id);
			while($parent_id['branch_id'] > 0){
				$parent_id = Db::table('typeinfo')->field('id,parent_id,branch_id')->find($parent_id['branch_id']);
			}
			$final_array = '[{"prev_id":"'.$parent_id['parent_id'].'","branch_id":"'.$branch_id.'","parent_id":"'.$parent_id['id'].'"}]';
			$back = url('branch/index', ['id' => $branch_id]);
		}

		// 根據上次建立的商品預設階層
		if($final_array == '{}'){
			$pre_prod = Db::table('productinfo')->order('id desc')->find();
			$final_array = $pre_prod['final_array'];
		}
		
		$this->assign('final_array', $final_array);
		$this->assign('back', $back);

		/* 運費標籤 */
		$shipping_fee_tag = Db::table('shipping_fee_tag')->order('order_id asc, id desc')->select();
        $this->assign('shipping_fee_tag', $shipping_fee_tag);
		
		return $this->fetch('create-all-product');
	}
	public function doCreate() {

		$newData = Request::instance()->post();
		unset($newData['prodesc_select']);

		if($newData['title'] == ''){
			$this->error('名稱為空');
		}

		$kol_id = isset($newData['kol_id']) ? $newData['kol_id'] : 0;
		unset($newData['kol_id']);

		if($newData['form_error'] == 1){
			$this->error('商品庫存錯誤');
		}
		unset($newData['form_error']);

		if($newData['final_array']=="{}"){
			$this->error('請給階層位置');
		}
		$final_array = json_decode($newData['final_array'],true);

		$subProjectJson = $newData['subProjectJson']; unset($newData['subProjectJson']);
		$index_ADV_Json = $newData['index_ADV_Json']; unset($newData['index_ADV_Json']);
		$repeat =  $newData['r_repeat']; //unset($newData['repeat']);
		$newData['create_time'] = time();

		$this->DBTextConnecter->setDataArray($newData);
		$id = $this->DBTextConnecter->createTextRow();
		if($kol_id != '0'){
			// 更新網紅
			$this->create_kol_productinfo($id, $kol_id);
		}
		if(count($final_array)>0){
			// 建立每個階層與商品的紀錄(用於儲存不同排序)
			$this->create_productinfo_orders($id, $final_array);
		}

		// 處理時間相關欄位
		$newData['expire_date'] = strtotime($newData['expire_date']);
		$newData['examinee_date'] = strtotime($newData['examinee_date']);

		// 自動更新排序
		$table = $this->resTableName;
		$column = 'order_id';
		$order_num = 0;
		$primary_key = 'id';
        $primary_value = $id;
		parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value);
		unset($newData['order_id']);

		// 處理圖片
		$newData['id'] = $id;
		$width = 600; $height = 600;
		for($i = 0; $i < $this->img_Quantity; $i++){
			$image = Request::instance()->file('image' . $i);
			if($image){
				$imgData[$i] = 
					$this->DBFileConnecter
						 ->fixedFileUp($image, 
									   'productinfo_' . 'pic'.$i . '_' . $id, 
									   $width, $height);
			}
			unset($newData['image' . $i]);
		}
		$imgData = array_values($imgData);
		$newData['pic'] = json_encode($imgData);

		// 產生商品編號
		$newData['product_id'] = config('subDeparment') . 'P' . date('Ymd') . $this->getNumberCount();


		// 更新商品資料(使用前面建立的id補上圖片、品號...)
		$this->DBTextConnecter->setDataArray($newData);
		$this->DBTextConnecter->upTextRow();

		// 處理品項
		$deal_datetime = $this->deal_with_productinfo_type($id, $repeat, $subProjectJson, '[]');

		// 處理標籤(店長、推薦、即期、特價)
		$index_ADV_Json = json_decode($index_ADV_Json);
		$client = new Client(['verify' => false]);
		foreach ($index_ADV_Json as $value) {
			$Data = [
				'form_params' => [
					'tableName' => $value->tableName,
					'id' => $newData['id']
				]
			];

			if($value->value == 0){
				$this->removeToIndexADV($newData['id'], $value->tableName);
			}else{
				$this->addToIndexADV($newData['id'], $value->tableName);
			}
		}


		if(!empty($this->close_function['存放位置管理'])){
			$this->success('新增成功', url('productinfo/edit',['id'=>$id]));
		}

		if($deal_datetime==''){
			$this->success('新增成功', url('productinfo/edit',['id'=>$id]));
		}else{
			$this->success('新增成功', url('productinfo/show_position_portion').'?searchKey='.$id.'&datetime='.$deal_datetime);
		}
	}

	/* 編輯 */
	public function edit($id) {

		$productinfo = Db::table('productinfo')->find($id);
		if($productinfo == null){
			$this->error('商品不存在');
		}

		$expiring_product = Db::table('expiring_product')->field('product_id')->where('product_id <> 0')->select();
		$hot_product = Db::table('hot_product')->field('product_id')->where('product_id <> 0')->select();
		$recommend_product = Db::table('recommend_product')->field('product_id')->where('product_id <> 0')->select();
		$spe_price_product = Db::table('spe_price_product')->field('product_id')->where('product_id <> 0')->select();

		$productinfo['items'] = '[]';
		if($productinfo['r_repeat']=='1'){
			$item = Db::table('productinfo_type pt')
				->where('pt.product_id = ' . $productinfo['id'].' AND online=1')
				->field('pt.*, pt.num as online_num, pt.position as position_id')
				->order('pt.order_id asc, pt.id asc')->select();
		}else{
			$item = Db::table('productinfo_type pt')
				->where('pt.product_id = ' . $productinfo['id'].' AND online=1')
				->field('pt.*, pt.num as online_num, pt.position as position_id')
				->order('pt.order_id asc, pt.id asc')->select();	
		}
		//dump($item);

		if(empty($item)){
			$item = Db::table('productinfo_type pt')
				->where('pt.product_id = ' . $productinfo['id'].' AND online=1')
				->field('pt.id,pt.product_id,pt.num,pt.price,pt.count,pt.title,pt.position,pt.discount_id')
				->order('pt.order_id asc, pt.id asc')->select();	

				foreach($item as $k => $v){
					$item[$k]['title_name']=$item[$k]['title'];
					$item[$k]['title']=$item[$k]['discount_id'];
					$item[$k]['position']=$item[$k]['position'];
					$item[$k]['num']=0;
					$item[$k]['old_num']=0;

					unset($item[$k]['position_id']);
					unset($item[$k]['Pp_num']);
				}
		}else{
			//dump($item);//exit;
			foreach($item as $k => $v){

				$item[$k]['title_name']=$item[$k]['title'];
				$item[$k]['title']=$item[$k]['discount_id'];
				$item[$k]['position']=$item[$k]['position_id'];

				if($productinfo['r_repeat']==0){ // 一碼一品項
					$item[$k]['num']=Db::table('position_portion')->where("product_id = '".$v['product_id']."' and productinfo_type = '".$v['id']."' and position_id = '".$v['position_id']."' and num!=0")->count();

				}else{ // 一碼多品項
					$item[$k]['num']=Db::table('position_portion')->where("product_id = '".$v['product_id']."' and productinfo_type = '".$v['id']."' and position_id = '".$v['position_id']."' and num!=0")->find()['num'];	
				}

				$item[$k]['old_num']=$item[$k]['num'];

				unset($item[$k]['position_id']);
				unset($item[$k]['Pp_num']);
			}
		}
		//dump($item);exit;

		$productinfo['items'] = json_encode($item);

		$productinfo['expiring_product'] = in_array(
			['product_id' => $productinfo['id']], 
			$expiring_product
		) ? 1 : 0;

		$productinfo['hot_product'] = in_array(
			['product_id' => $productinfo['id']], 
			$hot_product
		) ? 1 : 0;

		$productinfo['recommend_product'] = in_array(
			['product_id' => $productinfo['id']], 
			$recommend_product
		) ? 1 : 0;

		$productinfo['spe_price_product'] = in_array(
			['product_id' => $productinfo['id']], 
			$spe_price_product
		) ? 1 : 0;

		$productinfo['pic'] = json_decode($productinfo['pic'],true);

		$kol_productinfo = Db::table('kol_productinfo')->where('productinfo_id ='.$productinfo['id'].' AND is_using=1')->order('id desc')->select();
		$productinfo['kol_id'] = ($kol_productinfo) ? $kol_productinfo[0]['kol_id'] : 0;

		$this->assign('productinfo', $productinfo);

		/* 指派資料 */
		$this->assign_data();

        /* 已使用的運費 */
        $shipping_where = $productinfo['shipping_type'] ? 'id in ('.$productinfo['shipping_type'].')' : '0 = 1';
		$shipping_selected = Db::table('shipping_fee')->where($shipping_where)->order('order_id asc, id desc')->select();
        $this->assign('shipping_selected', $shipping_selected);

        /* 運費標籤 */
		$shipping_fee_tag = Db::table('shipping_fee_tag')->order('order_id asc, id desc')->select();
        $this->assign('shipping_fee_tag', $shipping_fee_tag);

		return $this->fetch('item-product-info');
	}
	public function update() {

		$newData = Request::instance()->post();
		unset($newData['prodesc_select']);
		// dump($newData);exit;

		$imgData = Db::table('productinfo')->field('pic')->find($newData['id'])['pic'];//照片數量
		$imgData = json_decode($imgData,true);

		if($newData['title'] == ''){
			$this->error('名稱為空');
		}

		if($newData['form_error'] == 1){
			$this->error('商品位置錯誤');
		}
		unset($newData['form_error']);

		if($newData['final_array']=="{}"){
			$this->error('請給階層位置');
		}

		// 更新網紅
		if( isset($newData['kol_id']) ){
			$this->create_kol_productinfo($newData['id'], $newData['kol_id']);
			unset($newData['kol_id']);
		}
		
		// 處理時間相關欄位
		$newData['expire_date'] = strtotime($newData['expire_date']);
		$newData['examinee_date'] = strtotime($newData['examinee_date']);

		// 處理排序，建立每個階層與商品的紀錄(用於儲存不同排序)
		$final_array = json_decode($newData['final_array'],true);
		$this->create_productinfo_orders($newData['id'], $final_array);

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
		
		$repeat =  $newData['r_repeat']; //unset($newData['repeat']);
		$subProjectJson = $newData['subProjectJson']; unset($newData['subProjectJson']);
		$delProjectJson = $newData['delProjectJson']; unset($newData['delProjectJson']);
		$index_ADV_Json = $newData['index_ADV_Json']; unset($newData['index_ADV_Json']);
		

		// 處理圖片
		$width = 600; $height = 600;
		for($i = 0; $i < $this->img_Quantity; $i++){
			$image = Request::instance()->file('image' . $i);
			if($image){
				$imgData[$i] = 
					$this->DBFileConnecter
						 ->fixedFileUp($image, 
									   'productinfo_' . 'pic'.$i . '_' . $newData['id'], 
									   $width, $height);		   
			}

			unset($newData['image' . $i]);
			if($newData['delimg'.$i] == 1){

				unset($imgData[$i]);
			}
			unset($newData['delimg'.$i]);
		}
		$imgData = array_values($imgData);
		$newData['pic'] = json_encode($imgData);

		$this->DBTextConnecter->setDataArray($newData);
		$this->DBTextConnecter->upTextRow();

		// 處理品項
		$deal_datetime = $this->deal_with_productinfo_type($newData['id'], $repeat, $subProjectJson, $delProjectJson);

		// 處理標籤(店長、推薦、即期、特價)
		$index_ADV_Json = json_decode($index_ADV_Json);
		$client = new Client(['verify' => false]);
		foreach ($index_ADV_Json as $value) {
			$Data = [
				'form_params' => [
					'tableName' => $value->tableName,
					'id' => $newData['id']
				]
			];

			if($value->value == 0){
				$this->removeToIndexADV($newData['id'], $value->tableName);
			}else{
				$this->addToIndexADV($newData['id'], $value->tableName);
			}
		}


		if(!empty($this->close_function['存放位置管理'])){
			$this->success('修改成功', url('productinfo/edit',['id'=>$newData['id']]));
		}

		if($deal_datetime==''){
			$this->success('修改成功', url('productinfo/edit',['id'=>$newData['id']]));
		}else{
			$this->success('修改成功', url('productinfo/show_position_portion').'?searchKey='.$newData['id'].'&datetime='.$deal_datetime);
		}
	}

	/* 引資料入html(allcreate,edit使用) */
	private function assign_data(){
		/* 品項下拉選 */
		$discount = Db::table('discount')->order('number asc')->select();
		$this->assign('discount', $discount);

		/* 庫存區下拉選 */
		$position = Db::table('position')->order('name asc, id desc')->select();
		$this->assign('position', $position);

		/* 分館 */
		$product = Db::table('product')->field('id,title')->select();
		$this->assign('product', $product);
		
		/* 商品描述下拉選 */
		$prodesc = Db::table('prodesc')->select();
		$this->assign('prodesc', $prodesc);

		/* 網紅 */
		$kol = Db::table('kol')->order('id desc')->select();
		$this->assign('kol', $kol);

		/* 各種運費 */
		$shipping_fee = Db::table('shipping_fee')->where('online=1')->order('order_id asc, id desc')->select();
        $this->assign('shipping_fee', $shipping_fee);
	}

	/*AJAX 編輯商品*/
	public function cellCtrl() {

		try{
			$updateData = Request::instance()->post();

			if( isset($updateData['po_order_id']) ){
				// 自動更新排序
				$table = 'productinfo_orders';
				$column = 'order_id';
				$order_num = $updateData['po_order_id'];
				$primary_key = 'id';
            	$primary_value = Db::table('productinfo_orders')->where('prod_id ='.$updateData['id'].' AND prev_id ='.$updateData['prev_id'].' AND branch_id ='.$updateData['branch_id'])->select()[0]['id'];
				$filter_where ='prev_id ='.$updateData['prev_id'].' AND branch_id ='.$updateData['branch_id'];
				parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value, $filter_where);
			}else{
				if( isset($updateData['order_id']) ){
					// 自動更新排序
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
	/*AJAX 刪除單一商品*/
	public function delete() {
        $id = Request::instance()->get('id');
        try{
			Db::table('productinfo')->delete($id);
			Db::table('expiring_product')->where('product_id', $id)->setField('product_id', 0);
			Db::table('hot_product')->where('product_id', $id)->setField('product_id', 0);
			Db::table('recommend_product')->where('product_id', $id)->setField('product_id', 0);
			Db::table('spe_price_product')->where('product_id', $id)->setField('product_id', 0);
			Db::table('productinfo_type')->where('product_id', $id)->delete();
			Db::table('position_portion')->where('product_id', $id)->delete();
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功',url('all/index'));
    }
    /*AJAX 刪除多個商品*/
	public function multiDelete() { 
		$idList = Request::instance()->post('id');
        try{
			if ($idList) {
				$idList = json_decode($idList);
				Db::table('productinfo')->delete($idList);
				Db::table('expiring_product')->where('product_id', 'in', $idList)->setField('product_id', 0);
				Db::table('hot_product')->where('product_id', 'in', $idList)->setField('product_id', 0);
				Db::table('recommend_product')->where('product_id', 'in', $idList)->setField('product_id', 0);
				Db::table('spe_price_product')->where('product_id', 'in', $idList)->setField('product_id', 0);
				Db::table('productinfo_type')->where('product_id', 'in', $idList)->delete();
				Db::table('position_portion')->where('product_id', 'in', $idList)->delete();
			}
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功');
    }

	/* 複製商品 */
	public function copy(){

		$idList = Request::instance()->get('id');
		if (!$idList) {
			throw new \Exception("請選擇商品");
		}
		$idList = json_decode($idList);
		// dump($idList);exit;

		array_walk($idList, function($value){

			$productinfo = Db::table('productinfo')->find($value);
			$old_id = $productinfo['id'];
			unset($productinfo['id']);
			unset($productinfo['ISBN']);
			unset($productinfo['updatetime']);

			// 產生商品編號
			$productinfo['product_id'] = config('subDeparment') . 'P' . date('Ymd') . $this->getNumberCount();

			$id = Db::table('productinfo')->insertGetId($productinfo);

			// 複製圖片
			$productinfo["pic"] = json_decode($productinfo["pic"],true);
			foreach($productinfo["pic"] as $k => $v){
				$str_sec = explode("?",$productinfo["pic"][$k]);
				$oldPath = $str_sec[0]; 
				$newPath = str_replace( $old_id, $id ,$str_sec[0]);
				copy('./public/static/index' . $oldPath, './public/static/index' . $newPath);
				$productinfo["pic"][$k] = str_replace( $old_id, $id , $productinfo["pic"][$k]);
			}

			$productinfo["pic"] = json_encode($productinfo["pic"]);
			Db::table('productinfo')->where('id', $id)->setField('pic', $productinfo["pic"]);
		});

		$this->success('複製成功');
	}

	/* 修改商品建立計數(用於產生品號) */
	private function getNumberCount() {
		$count = Db::table('productinfo')->where('product_id like "'.config('subDeparment').'P'.date('Ymd').'%"')->order('id desc')->find();
		$count = $count ? intval(substr($count['product_id'],-3)) + 1 : 1;

		if($count < 10){
			$count = '00' . $count;
		}else if($count < 100){
			$count = '0' . $count;
		}

		return $count;
	}

	/* 處理標籤勾選 */
	public function addToIndexADV($privateId = null, $privateTableName = null) {

		$id = $privateId ?? Request::instance()->post('id');

		$tableName = $privateTableName ?? Request::instance()->post('tableName');

		//檢查是否有勾選過
		$check_exit = Db::table($tableName)->where('product_id = ' . $id)->select();
		if(count($check_exit) > 0){
			$outputData = [

				'status' => true,

				'message' => 'already exits'

			];
			return json($outputData, 200);
		}

        try{

			if($tableName == 'spe_price_product'){

				$newData = [

						'product_id' => $id

				];

				Db::table($tableName)->insert($newData);

				$outputData = [

						'status' => true,

						'message' => 'success'

				];

				

			}else{

				$emptyId = Db::table($tableName)->field('id')->where('product_id = 0')->limit(1)->select();

				if(count($emptyId) != 0){

					$updateData = [

						'id' => $emptyId[0]['id'],

						'product_id' => $id

					];

					$DBTextConnecter = DBTextConnecter::withTableName($tableName);

					$DBTextConnecter->setDataArray($updateData);

					$DBTextConnecter->upTextRow();

					$outputData = [

						'status' => true,

						'message' => 'success'

					];

				}else{

					$outputData = [

						'status' => true,

						'message' => 'alreadyFull'

					];

				}

				

			}

        } catch (\Exception $e){

            $outputData = [

				'status' => false,

				'message' => $e->getMessage()

			];

			return json($outputData, 200);

        }

        return json($outputData, 200);
	}
	public function removeToIndexADV($privateId = null, $privateTableName = null) {

		$id = $privateId ?? Request::instance()->post('id');

		$tableName = $privateTableName ?? Request::instance()->post('tableName');

        try{

			if($tableName == 'spe_price_product'){

				

				Db::table($tableName)->where('product_id = ' . $id)->delete();

				$outputData = [

						'status' => true,

						'message' => 'success'

				];

			}else{

				$emptyId = Db::table($tableName)->field('id')->where('product_id = ' . $id)->limit(1)->select();

				if(count($emptyId) != 0){

					$updateData = [

						'id' => $emptyId[0]['id'],

						'product_id' => 0

					];

					$DBTextConnecter = DBTextConnecter::withTableName($tableName);

					$DBTextConnecter->setDataArray($updateData);

					$DBTextConnecter->upTextRow();

					$outputData = [

						'status' => true,

						'message' => 'success'

					];

				}else{

					$outputData = [

						'status' => true,

						'message' => 'alreadyNotExist'

					];

				}

			}

			

        } catch (\Exception $e){

            $outputData = [

				'status' => false,

				'message' => $e->getMessage()

			];

			return json($outputData, 200);

        }

        return json($outputData, 200);
	}

	/* 建立網紅跟商品關係 */
	public function create_kol_productinfo($prod_id, $kol_id){
		$kol_record = Db::table('kol_productinfo')->where('productinfo_id ='.$prod_id)->order('id desc')->select();//抓取此商品的網紅設定紀錄
		if(!empty($kol_record)){
			if($kol_record[0]['kol_id'] == $kol_id){
				return;
			}
		}

		// 修改過去紀錄成過期
		Db::table('kol_productinfo')->where('productinfo_id ='.$prod_id)->data(['is_using' => 0])->update();

		// 新增新紀錄
		$newData = [
			'kol_id'		=> $kol_id,
			'productinfo_id'=> $prod_id,
			'time'			=> time(),
			'is_using'		=> 1
		];
		$DBTextConnecter = DBTextConnecter::withTableName('kol_productinfo');
		$DBTextConnecter->setDataArray($newData);
		$DBTextConnecter->createTextRow($newData);
	}

	/* 處理商品的品項 */
	public function deal_with_productinfo_type($prod_id, $repeat, $subProjectJson, $delProjectJson){

		// 檢查編碼是否足夠
		$_POST['data'] = $subProjectJson;
		$_POST['repeat'] = $repeat;
		$check_position_portion = $this->position_portion();
		if($check_position_portion['form_error'] == 1) $this->error($check_position_portion['alert']);

		$subProjectJson = json_decode($subProjectJson);
		$DB_productinfo_type = DBTextConnecter::withTableName('productinfo_type');

		// 處理刪除的品項
		$delProjectJson = json_decode($delProjectJson);
		foreach ($delProjectJson as $value) {
			Db::table('productinfo_type')->where('id='.$value)->update(['num'=>0, 'online'=>0]); /*修改品項線上可購買數量、狀態*/
			Db::table('position_portion')->where('productinfo_type',$value)->delete();/*刪除品項庫存編碼*/
		}

		// 留存的品項的品項
		// dump($subProjectJson);exit;
		$deal_datetime = date('Y-m-d H:i:s'); // 紀錄操作時間
		$add_position_portion = false;
		foreach ($subProjectJson as $value) {
			$old_poistion = '';
			$value = get_object_vars($value);

			/*處理品項變換數量*/
			if( !empty($this->close_function['存放位置管理']) ){
				$change_num = isset($value['online_num']) ? $value['online_num'] : $value['num'];
			}else{
	        	$change_num = isset($value['old_num']) ? (int)$value['num'] - (int)$value['old_num'] : (int)$value['num']; // 變化量
			}
			$value['title'] = isset($value['title_name']) ? $value['title_name'] : '';

			if($change_num <0){$this->error('如欲扣除品項數量，請從商品銷售處理');}

			unset($value['title_name']);
			unset($value['total']);
			unset($value['old_num']);
			unset($value['online_num']);

			if(array_key_exists('id', $value)){ // 編輯品項
				$productinfo_type = Db::table('productinfo_type')
									->field('id, num, position')
									->find($value['id']);

				$productinfo_type_id = $productinfo_type['id'];
				$old_poistion = $productinfo_type['position']; // 紀錄原始位置
				// 如果關閉了存放位置管理，則使用$change_num作為最終數量，若有開啟則需加上原本的數量
				$value['num']= !empty($this->close_function['存放位置管理']) ? $change_num : $change_num + (int)$productinfo_type['num']; // 線上可購買數量

				$DB_productinfo_type->setDataArray($value);
				$DB_productinfo_type->upTextRow($value);

			}else{ // 新增品項
				$value['product_id'] = $prod_id; // 設定商品id
				$value['num'] = $change_num;  // 線上可購買數量
				$DB_productinfo_type->setDataArray($value);
				$productinfo_type_id = $DB_productinfo_type->createTextRow($value);
			}


			if($old_poistion!=$value['position'] && $old_poistion!=''){ /*庫存位置不同 且 有原始位置*/
				if(isset($value['id'])){ // 如果有品項id
					$ori_position_portion = Db::table('position_portion')->where('productinfo_type',$value['id'])->select();
					foreach ($ori_position_portion as $k_oripp => $v_oripp) {
						$position_number = $this->get_position_number($value['position']);
						Db::table('position_portion')->where('id',$v_oripp['id'])->update([
							'position_id'		=> $value['position'],
							'position_number' 	=> $position_number,
						]);
					}
				}
			}

			if($change_num >0 && empty($this->close_function['存放位置管理']) ){ // 變化量大於0 且 有開啟存放位置管理，則需處理額外庫存編碼數量

				if(isset($value['id'])){ // 如果有品項id
					$has_position_portion = Db::table('position_portion')->where("productinfo_type = '".$value['id']."' and product_id ='".$value['product_id']."'")->select();
				}else{
					$has_position_portion = false;
				}

				if($repeat==1 && $has_position_portion){ // 編碼方式為一碼多品項 且 已經存在編碼
					Db::table('position_portion')
						->where("productinfo_type = '".$value['id']."' and product_id ='".$value['product_id']."'")
						->inc('num', $change_num)
						->update();
				}else{
					if($repeat==1){ // 編碼方式為一碼多品項 但 不存在編碼
						// 根據數量指派位置
						$position_number = $this->get_position_number($value['position']);
						$newData = [
							'position_id' 		=> $value['position'],
							'position_number'	=> $position_number,
							'num'				=> $change_num,
							'product_id'		=> $prod_id,
							'productinfo_type'	=> $productinfo_type_id,
							'radio'				=> $repeat,
							'datetime'			=> $deal_datetime,
						];

						if($position_number != 0){
							$add_position_portion = true;
							Db::table('position_portion')->insert($newData);
						}else{ // 編碼不足
							Db::table('productinfo_type')->where('id',$value['id'])->dec('num',$change_num)->update(); // 扣回線上可購買數量
							$this->error('品項：'.$value['title'].'庫存編碼已達上限，請更換庫存區');
						}
					}else{ // 編碼方式為一碼一品項
						foreach ( range(0, $change_num-1) as $index) { // 依據新增數量逐個建立編碼
							// 根據數量指派位置
							$position_number = $this->get_position_number($value['position']);
							$newData = [
								'position_id' 		=> $value['position'],
								'position_number'	=> $position_number,
								'num'				=> 1,
								'product_id'		=> $prod_id,
								'productinfo_type'	=> $productinfo_type_id,
								'radio'				=> $repeat,
								'datetime'			=> $deal_datetime,
							];

							if($position_number != 0){
								$add_position_portion = true;
								Db::table('position_portion')->insert($newData);
							}else{
								Db::table('productinfo_type')->where('id',$value['id'])->dec('num',$change_num-$index)->update(); // 扣回線上可購買數量
								$this->error('品項：'.$value['title'].'庫存編碼已達上限，請更換庫存區');
							}
						}
					}
				}
			}
		}

		if($add_position_portion){ // 有新增position_portion
			return $deal_datetime; // 回傳處理時間
		}else{
			return '';
		}
	}

	/* 取得分館/分類下選 */
	public function position_select(){//fat

		$type = $_POST['type']??'product_select';//product_select
		$po =  $_POST['next']??'0';
		$value = $_POST['value']??'0';

		switch($type){
			case 'product_select':
				$parent_array = Db::table('typeinfo')->where("parent_id='".$value."'  and branch_id='0' ")->field('id, title')->select();
				if($parent_array){	
					$return_select= '
						<select  id="branch_select-1" onchange="product_position(\'branch_select\',\'1\')">
						  <option value="0">請選擇階層</option>
					';

					foreach($parent_array as $v => $k){
						$return_select.=  
							'<option value="'.$k['id'].'">'.$k['title'].'</option>';
					}

					$return_select .= '</select><span id="branch_select-2"></span>'; 
					echo $return_select;
				}
				break;

			case 'branch_select':
				$parent_array = Db::table('typeinfo')->where("branch_id='".$value."'  and  online = '1'")->field('id, title')->select();
				if($parent_array){
					$return_select= '
						<select  id="branch_select-'.($po+1).'" onchange="product_position(\'branch_select\','.($po+1).')">
						  <option value="0">請選擇階層</option>
					';

					if($value != '0'){
						foreach($parent_array as $v => $k){
							$return_select.=  
								'<option value="'.$k['id'].'">'.$k['title'].'</option>';
						}
					}
					$return_select .= '</select><span id="branch_select-'.($po+2).'"></span>'; 
					echo $return_select;
				}
				break;
		}
	}

	/* 檢查編碼是否超出上限 */
	public function position_portion(){
		$data = json_decode($_POST['data'],true);

		$pos_num=[];
		$repeat = $_POST['repeat']??0; // 沒repeat值預設為重複

		//處理相同position代碼及數量
		foreach($data as $k => $v){

			if($v['num'] == 0 )
				continue;

			if(!empty($this->close_function['存放位置管理'])) /*關閉 存放位置管理功能*/
				continue;

			if($v['position']==0 && empty($this->close_function['存放位置管理'])){ /*未選庫存區 且 使用 存放位置管理功能*/
				$msg['alert'] = '請選品項的擇存放位置';
				$msg['form_error'] = 1;
				return $msg;
			}

			if(empty($pos_num[$v['position']])){
				$pos_num[$v['position']]['new_num'] = 0;
				$pos_num[$v['position']]['old_num'] = $v['old_num']??0;
			}

			if($repeat == 0){ //不重複
				$v['old_num'] =  isset($v['old_num']) ? $v['old_num'] : 0;
				$pos_num[$v['position']]['new_num'] += ($v['num'] - $v['old_num']);
			}else{ //重複
				if(!isset($v['id']))
					$pos_num[$v['position']]['new_num']++;
			}
		}

		$msg['alert'] = 'ok';
		$msg['form_error'] = 0;

		foreach($pos_num as $k => $v){
			$position_portion = Db::table('position_portion')->where("position_id = '".$k."' and product_id != '0' ")->field("count(position_id) as num")->group('position_id')->find();//已用位置

			$position = Db::table('position')->field('name,number,max')->find($k);//位置限制數量
			//dump($position['number']);

			if($position['max']=='1')
				continue;

			$use_pos = $position_portion['num'] + $v['new_num'];

			if($position['number'] < $use_pos ){
				$msg['alert'] = $position['name'].'位置不夠';
				$msg['form_error'] = 1;
				break;
			}
		}

		//dump($data);
		//dump($pos_num);
		return $msg;
	}
	/* 取得下一個庫存編碼 */
	public function get_position_number($position_id){

		$position = Db::table('position')->where('id='.$position_id)->find();
		$position_number = 1;

		if($position['max']==1){ // 無限
			while (true) {
				$has_empty_position = Db::table('position_portion')->where('position_id='.$position_id.' AND position_number='.$position_number)->select();
				if($has_empty_position){
					$position_number += 1; // +1後繼續找
				}else{
					break;
				}
			}
		}else{
			while ( $position_number <= $position['number'] ) {
				$has_empty_position = Db::table('position_portion')->where('position_id='.$position_id.' AND position_number='.$position_number)->select();
				if($has_empty_position){
					$position_number += 1;
				}else{
					break;
				}
			}

			$position_number = $position_number <= $position['number'] ? $position_number : 0;
		}

		return $position_number;
	}

	/* 建立商品與分館/分類的紀錄(排序用) */
	public function create_productinfo_orders($prod_id, $final_array){
		$id_in = [];
		// 針對每個階層處理
		foreach ($final_array as $key => $value) {
			$productinfo_orders = DB::table('productinfo_orders')->where('prod_id ='.$prod_id.' AND prev_id ='.$value['prev_id'].' AND branch_id ='.$value['branch_id'])->select();
			if(count($productinfo_orders)>0){
				// 有紀錄則紀錄有此筆紀錄
				array_push($id_in, $productinfo_orders[0]['id']);
			}else{
				// 沒紀錄則新增紀錄
				DB::table('productinfo_orders')->data([
														'prod_id'=>$prod_id, 
														'prev_id'=>$value['prev_id'], 
														'branch_id'=>$value['branch_id'], 
														'order_id'=>0]
													)->insert();
				// 記錄剛建立出的資料
				$productinfo_orders = DB::table('productinfo_orders')->where('prod_id ='.$prod_id.' AND prev_id ='.$value['prev_id'].' AND branch_id ='.$value['branch_id'])->select();
				array_push($id_in, $productinfo_orders[0]['id']);
			}
		}

		// 把沒有的階層刪掉
		if(count($id_in)>0){
			$id_in = join(',', $id_in);
			DB::table('productinfo_orders')->where('prod_id ='.$prod_id.' AND id not in ('.$id_in.')')->delete();
		}
	}

	/* 儲存商品說明/屬性/訂購須知/付款方式 */
	public function cellCtrlFromDefault() {
		try{
			$updateData = Request::instance()->post();
			$DBTextConnecter = DBTextConnecter::withTableName('default_content');
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
	/* 載入商品說明/屬性/訂購須知/付款方式 */
	public function cellGetFromDefault() {
		try{
			$id = Request::instance()->post('id') ?? 1;
			$productinfo = Db::table('default_content')
								->field(Request::instance()->post('textNumber'))
								->find($id);			
			$outputData = [
				'status' => true,
				'message' => $productinfo[Request::instance()->post('textNumber')]
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

	/******** 好像沒用的 ********/
	/******** 好像沒用的 ********/
	// public function index($parent_id) {
	// 	$searchKey = Request::instance()->get('searchKey') ?? '';

	// 	$this->assign('searchKey', $searchKey);

	// 	$productinfo = Db::table('productinfo')
	// 			->field('title, id, order_id, product_id, pic, has_price, id, updatetime, online')
	// 			->where('parent_id = ' . $parent_id . 
	// 					" AND productinfo.title like  '%" . $searchKey . "%'")
	// 			->order('order_id, id desc')->paginate(
	// 				self::PER_PAGE_ROWS,
	// 				self::SIMPLE_MODE_PAGINATE, 
	// 				[
	// 					'query' => [
	// 						'searchKey' => $searchKey
	// 					]
	// 				]
	// 			);

	// 	$expiring_product = Db::table('expiring_product')->field('product_id')->where('product_id <> 0')->select();
	// 	$hot_product = Db::table('hot_product')->field('product_id')->where('product_id <> 0')->select();
	// 	$recommend_product = Db::table('recommend_product')->field('product_id')->where('product_id <> 0')->select();
	// 	$spe_price_product = Db::table('spe_price_product')->field('product_id')->where('product_id <> 0')->select();

		
	// 	$this->assign('productinfo', $productinfo);

	// 	$productinfoItem = $productinfo->items();
	// 	foreach ($productinfoItem as &$value) {
	// 		$value['pic'] = json_decode($value['pic'],true);
	// 		$value['item'] = '[]';

	// 		if ($value['has_price'] == 1){
	// 			$value['item'] = json_encode(Db::table('productinfo_type')
	// 					->where('product_id = ' . $value['id'])
	// 					->order('id')->select());
	// 		}

	// 		$value['expiring_product'] = in_array(
	// 			['product_id' => $value['id']], 
	// 			$expiring_product
	// 		) ? 1 : 0;

	// 		$value['hot_product'] = in_array(
	// 			['product_id' => $value['id']], 
	// 			$hot_product
	// 		) ? 1 : 0;

	// 		$value['recommend_product'] = in_array(
	// 			['product_id' => $value['id']], 
	// 			$recommend_product
	// 		) ? 1 : 0;

	// 		$value['spe_price_product'] = in_array(
	// 			['product_id' => $value['id']], 
	// 			$spe_price_product
	// 		) ? 1 : 0;
	// 	}
	// 	$this->assign('productinfoItem', $productinfoItem);

	// 	$parent = Db::table('typeinfo')
	// 			->field('product.id AS product_id, 
	// 					 product.title AS product_title, 
	// 					 typeinfo.id AS typeinfo_id, 
	// 					 typeinfo.limit_num AS typeinfo_limit_num, 
	// 					 typeinfo.title AS typeinfo_title')
	// 			->join('product', 'typeinfo.parent_id = product.id')
	// 			->where('typeinfo.id = '.$parent_id)->select();
	// 	$this->assign('parent', $parent[0]);

	// 	var_dump($parent);die();
	// 	return $this->fetch('item-product');
	// }

	// public function cellCtrlFromType() {

	// 	try{

	// 		$updateData = Request::instance()->post();

	// 		$DBTextConnecter = DBTextConnecter::withTableName('productinfo_type');

	// 		$DBTextConnecter->setDataArray($updateData);

	// 		$DBTextConnecter->upTextRow();

	// 		$outputData = [

	// 			'status' => true,

	// 			'message' => 'success'

	// 		];

	// 	}catch (\Exception $e){

	// 		$outputData = [

	// 			'status' => false,

	// 			'message' => $e->getMessage()

	// 		];

	// 		return json($outputData, 200);

	// 	}

	// 	return json($outputData, 200);
	// }
	

	// public function types() {
	// 	$types = $_POST['types'];

	// 	$productinfo = Db::table('productinfo')
	// 			->field('productinfo.product_id AS id,
	// 					productinfo.title AS title')
	// 			->where('productinfo.online = 1 AND productinfo.parent_id = '.$types)
	// 			->order('id desc')->select();

	// 	$data = [];
	// 	$arrary = array();
	// 	foreach ($productinfo as $item) {
	// 		$data['id'] = $item['id'];
	// 		$data['title'] = $item['title'];
	// 		array_push($arrary, $data);
	// 	}
	// 	return json($arrary, 200);
	// }
	// public function categories() {

	// 	$categories = $_POST['categories'];

	// 	$typeinfo = Db::table('typeinfo')

	// 			->field('typeinfo.id AS id,

	// 					typeinfo.title AS title')

	// 			->where('typeinfo.online = 1 AND typeinfo.parent_id = '.$categories)

	// 			->order('id desc')->select();

	// 	$data = [];

	// 	$arrary = array();

	// 	foreach ($typeinfo as $item) {

	// 		$data['id'] = $item['id'];

	// 		$data['title'] = $item['title'];

	// 		array_push($arrary, $data);

	// 	}

	// 	return json($arrary, 200);
	// }
	// public function re_categories(){

	// 	$re_categories = $_POST['re_categories'];
	// 	if(!empty($re_categories)){

	// 		$productinfo = Db::table('productinfo')
	// 				->field('typeinfo.parent_id AS type_parent_id,
	// 						productinfo.parent_id AS parent_id')
	// 				->where('productinfo.online = 1 AND productinfo.product_id = '."'".$re_categories."'")
	// 				->join('typeinfo', 'productinfo.parent_id = typeinfo.id')
	// 				->select();

	// 		$arrary = array($productinfo[0]['type_parent_id'],$productinfo[0]['parent_id']);
	// 		return json($arrary, 200);
	// 	}
	// }
	// function classPoition($type,$id){  // 20191211 fat
	// 	switch($type){
	// 		case 'prev_id':
	// 			$parent_array = Db::table('product')->field('id, title, online')->find($id);	
	// 			$final_title = "<a href=\"".url('typeinfo/index', ['parent_id' => $parent_array['id']])."\">".$parent_array['title']."</a>";
	// 			$edit_poition['prev_id'][0]=$parent_array['id'];
	// 			$this->assign('parent_title', '0');
	// 			$this->assign('final_title', $final_title);
	// 			$this->assign('edit_poition', $edit_poition);

	// 			break;

	// 		case 'branch_id':
	// 			/***階層位置***/
	// 			$parent_title = Db::table('typeinfo')->field('id,branch_id,parent_id, title, online')->find($id);	
	// 			$title_branch_id = $parent_title['branch_id'];
	// 			$title_array[0] = "<a href=\"".url('branch/index', ['id' => $parent_title['id']])."\">".$parent_title['title']."</a>";
	// 			$edit_poition[0]=$parent_title['id'];

	// 			$i = 1;
	// 			while($title_branch_id != 0){
	// 				$new_title = Db::table('typeinfo')->field('id,branch_id,parent_id, title, online')->find($title_branch_id);	
	// 				$title_branch_id = $new_title['branch_id'];
	// 				$title_array[$i] = "<a href=\"".url('branch/index', ['id' => $new_title['id']])."\">".$new_title['title']."</a>";
	// 				$edit_poition[$i]=$new_title['id'];
	// 				$i++;
	// 			}

	// 			if(empty($new_title['parent_id']))
	// 				$new_title['parent_id'] = $parent_title['parent_id'];

	// 			$parent_array = Db::table('product')->field('id, title, online')->find($new_title['parent_id']);	
	// 			$title_array[$i] = "<a href=\"".url('typeinfo/index', ['parent_id' => $parent_array['id']])."\">".$parent_array['title']."</a>";
	// 			$edit_poition[$i]=$parent_array['id'];

	// 			$final_title ='';

	// 			$ii = 0;
	// 			for($i=count($title_array)-1;$i >=0;$i--){
	// 				$edit_poition['branch_id'][$ii]= $edit_poition[$i];
	// 				$final_title .= $title_array[$i];
	// 				$ii++;
	// 				if($i >=1)
	// 					$final_title = $final_title.' > ';
	// 			}

	// 			$this->assign('parent_title', $parent_title);
	// 			$this->assign('final_title', $final_title);
	// 			$this->assign('edit_poition', $edit_poition);

	// 			/***階層位置***/
	// 			break;

	// 		case 'branch_select':
	// 			$parent_title = Db::table('typeinfo')->field('id,branch_id,parent_id, title, online')->find($id);	
	// 			$title_branch_id = $parent_title['branch_id'];
	// 			$title_array[0] = "<a href=\"".url('branch/index', ['id' => $parent_title['id']])."\">".$parent_title['title']."</a>";

	// 			$i = 1;
	// 			while($title_branch_id != 0){
	// 				$new_title = Db::table('typeinfo')->field('id,branch_id,parent_id, title, online')->find($title_branch_id);	
	// 				$title_branch_id = $new_title['branch_id'];
	// 				$title_array[$i] = "<a href=\"".url('branch/index', ['id' => $new_title['id']])."\">".$new_title['title']."</a>";
	// 				$i++;
	// 			}
	// 			break;
	// 	}
	// }
}