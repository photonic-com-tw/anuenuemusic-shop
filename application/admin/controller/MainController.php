<?php


namespace app\admin\controller;


use think\Controller;
use think\Session;
use think\Config;
use think\Request;
use think\Db;


use controllerInterface\parentFunction\Auth;
use controllerInterface\parentFunction\ErrorProcess;


class MainController extends Controller implements Auth, ErrorProcess
{

	protected $user;

	public $control_register;	// 是否使用報名功能
	public $ecpay_card; 		// 是否使用綠界金流
	public $ecpay_logistic; 	// 是否使用綠界物流
	public $tspg_card; 			// 是否使用台新金流

	public function __construct()
	{
		parent::__construct();
		define('UPLOAD_PATH', ROOT_PATH . 'public' . DS . 'static' . DS . 'index' . DS . 'upload');

		$c = request()->controller();
		$a = request()->action();
		// var_dump($c);
		$sec_ative = strtolower($c).'_'.strtolower($a);

		$second_list = 'false';
		$backstage_menu = Db::table('backstage_menu_second')->field('name, backstage_menu_id')->where("url like '/admin/".$c."/%'")->find();
		$backstage_menu_name = $backstage_menu['name'];
		$backstage_menu_id = $backstage_menu['backstage_menu_id'];

		// auto unfold menu
		if( (string)array_search(strtolower($c), ['typeinfo', 'productinfo', 'product', 'branch']) != '' ){
			$backstage_menu_id = 2;
			// $second_list = 'true';
		}else if( (string)array_search(strtolower($c), ['stronghold']) != '' ){
			$backstage_menu_id = 4;
			$second_list = 'true';
		}

		// active
		if( (string)array_search($sec_ative, ['productinfo_allcreate', 'productinfo_edit']) != '' ){
			$sec_ative = 'all_index';
			$second_list = 'false';
		}else if( (string)array_search($sec_ative, ['index_spepriceproduct']) != '' ){
			$sec_ative = 'index_index';
		}else if( (string)array_search($sec_ative, ['act_create', 'act_edit']) != '' ){
			$sec_ative = 'act_index';
		}else if( (string)array_search($sec_ative, ['discount_create', 'discount_edit']) != '' ){
			$sec_ative = 'discount_index';
		}else if( (string)array_search($sec_ative, ['coupondirect_create', 'coupondirect_edit']) != '' ){
			$sec_ative = 'coupondirect_index';
		}else if( (string)array_search($sec_ative, ['coupon_create', 'coupon_show']) != '' ){
			$sec_ative = 'coupon_index';
		}else if( (string)array_search($sec_ative, ['product_index','typeinfo_index','branch_index']) != '' ){
			$sec_ative = 'layertree_tree';
		}else if( (string)array_search($sec_ative, ['kol_index','kol_salelist', 'kol_sale_detail']) != '' ){
			$sec_ative = 'kol_index';
		}else if( (string)array_search($sec_ative, ['stronghold_index']) != '' ){
			$sec_ative = '';
		}
		


		$this->assign('backstage_menu_name',$backstage_menu_name);
		$this->assign('target',$backstage_menu_id);
		$this->assign('sec_ative', $sec_ative);
		$this->assign('second_list',$second_list);

		//auth system
		$this->auth();

		if(!empty($_FILES)){
			$type_error = '';
			foreach($_FILES as $v => $s){
				//echo $_FILES[$v]["type"];exit;//application/octet-stream  //application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
				if($_FILES[$v]["type"]== "image/jpeg" || $_FILES[$v]["type"]=="image/jpg" || $_FILES[$v]["type"]=="image/png" || $_FILES[$v]["type"]=="" || $_FILES[$v]["type"]=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ){
					$type_error = 'ok';
				}
				if($_FILES[$v]["type"]== "application/octet-stream"){
					$type_error = 'no';
				}
				if($type_error != 'ok'){
					$this->error('操作失敗：');
				}
			}
		}

		/***開啟/關閉選單 fat***/
		$close_function = [];
		$current_Block = Db::table('admin')->field('purview')->where("permission = 'current'")->find()['purview'];
		$current_Block = json_decode($current_Block,true);
		$Block = json_decode($this->user['purview'],true);
		$first_list = Db::table('backstage_menu')->select();
		$second_list = Db::table('backstage_menu_second')->order('sort asc, id asc')->select();
		$extra = [
			'5' => array('item','myList','add-item','newProductName','addProdect()','ShowList()','add'),
			'12' => array('item2','myList2','add-item2','newProductName2','addstronghold()','ShowList2()','add2')
		];

		$show_list =[];
		foreach($first_list as $k => $v){
			$show_list[$v['id']]['id'] = $v['id'];
			$show_list[$v['id']]['name'] = $v['name'];
		}
	
		foreach($second_list as $k => $v){
			if(empty($show_list[$v['backstage_menu_id']]['sub'])){
				$show_list[$v['backstage_menu_id']]['sub'] = [];
			}

			$ck = 0;
			/*if($v['name'] == '帳號管理' && ($this->user['permission'] == 'no' ))
				$ck = 1;*/

			if(!empty($Block[$v['backstage_menu_id']])){
				foreach($Block[$v['backstage_menu_id']] as $bk => $bv){

					if($v['id'] == $bv){
						$close_function[ $v['name'] ] = 'close'; //紀錄被關閉的功能
						$ck = 1;
						// break;
					}
				}
			}

			if(!empty($current_Block[$v['backstage_menu_id']]) && $this->user['permission'] == 'no'){

				foreach($current_Block[$v['backstage_menu_id']] as $bk => $bv){
					if($v['id'] == $bv){
						$close_function[ $v['name'] ] = 'close'; //紀錄被關閉的功能
						$ck = 1;
						// break;
					}
				}
			}

			if($ck == 0)
				array_push($show_list[$v['backstage_menu_id']]['sub'],$v);
		}

		foreach($show_list as $k => $v){
			if(count($v['sub'])==0)
				unset($show_list[$k]);
		}

		$this->show_list = $show_list;
		$this->assign('show_list', $show_list);
		$this->assign('extra', $extra);
		$this->assign('close_function', $close_function);
		$this->close_function = $close_function;
		// dump($close_function);
		/***開啟/關閉選單 fat***/

		/* 標籤名稱(特價、即期...) */
		$tag = Db::table('frontend_data_name')->where('show_type = "tag"')->order('id asc')->select();
		$this->assign('tag', $tag);

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

		/*取得前台選單文字*/
		$frontend_menu_name= Db::table('frontend_menu_name')->order('id')->select();
        $frontend_menu = [];
        foreach ($frontend_menu_name as $key => $value) {
            $frontend_menu[$value['controller']] = $value;
            $frontend_menu[$value['controller']]['second_menu'] = json_decode($value['second_menu']);
        }
        $this->assign('frontend_menu', $frontend_menu);

        $admin_info = Db::table('admin_info')->find(1);
		$this->assign('admin_info', $admin_info);
	}



	public function auth()
	{
		$this->user = Session::get('admin');
		if($this->user == null){
			$this->redirect('Login/index');
		}

		// 控制可否編輯報名資料
		$this->user['edi_examination'] = 1; // 預設可以

		$this->assign('user', $this->user);

	}



	public function dumpException(\Exception $e)

	{

		if(Config::get('app_debug')){

			$this->assign('waitSecond', 5);

			$this->error('操作失敗：' . $e->getMessage());

		}else{

			$this->assign('waitSecond', 1);

			$this->error('操作失敗，請洽管理員');

		}

	}



	public function dumpError($errorMessage)

	{

		if(Config::get('app_debug')){

			$this->assign('waitSecond', 5);

		}else{

			$this->assign('waitSecond', 1);

		}

		$this->error('操作失敗：' . $errorMessage);

	}

	// 自動更改排序
	public function auto_change_orders($table, $column, $order_num, $primary_key, $primary_value, $filter_where=false){

		if(!$table) $this->error('操作失敗：請提供更改的資料表');
		if(!$column) $this->error('操作失敗：請提供更改的欄位');
		if(!$primary_key) $this->error('操作失敗：請提供改目標資料表的主鍵');
		if(!$primary_value) $this->error('操作失敗：請提供改目標資料表的主鍵值');

		$order_num = $order_num ? $order_num : 0;// 未提供排序，預設為0

		$filter_where = $filter_where !== false ? $filter_where : 'true = true';

		// 利用篩選條件檢查此次設定的排序 是否已被設定 且是 別人
		$order_num_isset =DB::table($table)->where($filter_where.' and '.$column.' = '.$order_num.' and '.$primary_key.' != '.$primary_value)->select();
		if(count($order_num_isset)>0){
			// 被設定走了，開始自動修改排序

			// 利用篩選條件找出要一起檢查排序的資料
			$rows =	DB::table($table)->where($filter_where)->select();
			foreach ($rows as $key => $value) {
				// 如果該資料排序等於或大於此次設定的排序
				if($value[$column] >= $order_num){
					$new_order = $value[$column]+1; // 排序自動+1
					DB::table($table)
						->where($primary_key.' = '.$value[$primary_key])
						->data([ $column=>$new_order ])->update();
				}
			}
		}

		// 根據目標篩選，修改目標資料排序
		DB::table($table)->where($primary_key.' = '.$primary_value)->data([ $column=>$order_num ])->update();
	}

}