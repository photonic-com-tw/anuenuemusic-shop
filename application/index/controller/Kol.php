<?php

namespace app\index\controller;

use think\Validate;
use think\Db;
use think\Request;
use think\Session;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use DBtool\DBTextConnecter;

class Kol extends PublicController {
// class Kol extends PublicController {

	public static $ACCOUNT_MODE = ['審核中', '通過', '黑名單', '停用'];
	const PER_PAGE_ROWS = 50;
	const SIMPLE_MODE_PAGINATE = false;

	public function __construct() {
        $request = Request::instance();
        parent::__construct($request); // PublicController建構時需傳入Request物件
        $this->DBTextConnecter = DBTextConnecter::withTableName('kol');
        $this->resTableName = 'kol';

        self::$ACCOUNT_MODE = [
            LANG_MENU['審核中'], 
            LANG_MENU['通過'], 
            LANG_MENU['黑名單'], 
            LANG_MENU['停用']
        ];
    }

	public function pagelogin() {
		$email = Request::instance()->post('email');
		$password = Request::instance()->post('password');
		$rule = [
			'email'  => 'require',
			'password' => 'require'
		];
		$msg = [
			'email.require' => LANG_MENU['帳號不得為空'],
			'password.require' => LANG_MENU['密碼不得為空']
		];
		$validate = new Validate($rule,$msg);
		$data = [
			'email'  => $email,
			'password' => $password
		];

		if (!$validate->check($data)) {
			$this->error($validate->getError());
		}
		$where = [
			'email' => $email
		];
		$kolData = Db::table($this->resTableName)->where($where)->limit(1)->select();

		if($kolData){
			if($kolData[0]['password'] == $password){

				Session::set('kol', $kolData[0]);
				$this->success(LANG_MENU['操作成功'], url('kol/kol_data')); /*登入成功*/

			}else{
				$this->error(LANG_MENU['內容有誤']); /*密碼錯誤*/
			}
		}else{
			$this->error(LANG_MENU['內容有誤']); /*$email.'帳號不存在'*/
		}
	}

	public function logout() {
		Session::delete('kol');
		$this->redirect('Index/index');
	}

	public function check_login(){
		if( empty(session::get('kol')) ){
			$this->error(LANG_MENU['請以網紅身分登入'], url('Index/index'));
		}else{
			// dump(session::get('kol'));
			return true;
		}
	}

	public function kol_data(){
		$this->check_login();

		$kol = session::get('kol');
		$this->assign('kol', $kol);

		$this->assign('kol_active', 'kol_data');

		return $this->fetch('kol_data');
	}

	public function sale_record(){
		$this->check_login();

		$kol = session::get('kol');
		$this->assign('kol', $kol);

		$this->assign('kol_active', 'sale_record');

		return $this->fetch('sale_record');
	}

	public function selling_proudct(){
		$this->check_login();

		$kol = session::get('kol');
		$this->assign('kol', $kol);

		$this->assign('kol_active', 'selling_proudct');
		
		return $this->fetch('selling_proudct');
	}

	public function sale_list(){
		$this->check_login();

		$kol = session::get('kol');
		$this->assign('kol', $kol);

		$kol_id = $kol['id'];
		$kol_startdate = $kol['start_date'];
        $kol_counttdate = (int)$kol['count_days'];

        $type = Request::instance()->get('type') ? Request::instance()->get('type') : '0'; // 預設未結算
        $this->assign('type', $type);

        $page = Request::instance()->get('page') ? Request::instance()->get('page') : 1; // 預設第一頁
        $this->assign('page', $page);

		// 取得符合搜尋區間及類型的訂單
        $start = Request::instance()->get('start');
        $end = Request::instance()->get('end');
        $return_data = $this->get_target_order($kol_id, $kol_startdate, $kol_counttdate, $type, $start, $end, $page); // 依頁數找出符合條件的訂單

        // 隱藏訂單資料
        $return_data['orderform'] = $this->hide_info($return_data['orderform']);

        $this->assign('start', $return_data['start']);
        $this->assign('end', $return_data['end']);
        $this->assign('orderform', $return_data['orderform']);

        $this->assign('kol_active', 'sale_list');

        switch ($type) {
            case '0': // 未結算
            	// 計算總頁數
            	$return_all_data = $this->get_target_order($kol_id, $kol_startdate, $kol_counttdate, $type, $start, $end, false); // 找出所有符合條件的訂單
            	$this->assign('totalpage', ceil(count($return_all_data['orderform'])/self::PER_PAGE_ROWS));
                return $this->fetch('sale_list');
                break;

            case '1' || '2': // 結算中or已結算
                $period_sale = $this->group_by_period($kol_id, $kol_startdate, $kol_counttdate, $type, $return_data['orderform'], [$return_data['p_s_period'], $return_data['p_e_period']]);
                $this->assign('period_sale', $period_sale);

                // 計算總頁數
                $all_period = $this->group_by_period($kol_id, $kol_startdate, $kol_counttdate, $type, $return_data['orderform'], [$return_data['s_period'], $return_data['e_period']]);
                $this->assign('totalpage', ceil( count($all_period)/self::PER_PAGE_ROWS) );

                return $this->fetch('sale_preriod');
                break;
        }
	}

	public function sale_detail(){
		$this->check_login();

		$kol = session::get('kol');
		$this->assign('kol', $kol);

		$kol_id = $kol['id'];
		$kol_startdate = $kol['start_date'];
        $kol_counttdate = (int)$kol['count_days'];

        $period = Request::instance()->get('period') ? Request::instance()->get('period') : $this->error(LANG_MENU['請選擇期數']); // 所選期數
        $this->assign('period', $period);

        // 依期數取得標訂單
        $orderform = $this->get_target_order_by_period($kol_id, $kol_startdate, $kol_counttdate, $period);
        $this->assign('orderform', $orderform);
        
        $period_sale = $this->group_by_period($kol_id, $kol_startdate, $kol_counttdate, false, $orderform, [$period,$period]);
        if(empty($period_sale)){
            $period_sale[0] =  ['total'=>0, 'order'=>[], 's_period'=>strtotime($s_time), 'e_period'=>strtotime($e_time)];
        }
        $this->assign('period_sale', $period_sale);

        $confirm_sale = Db::table('kol_confirm_sale')->where('kol_id ='.$kol_id.' AND period ='.$period)->find();
        $need_confirm = empty($confirm_sale) ? 'true' : 'false';
        $this->assign('need_confirm', $need_confirm);
        $this->assign('confirm_sale', $confirm_sale);

        $this->assign('kol_active', 'sale_list');
        
        return $this->fetch('sale_detail');
	}

	// 依期數取得標訂單
	public function get_target_order_by_period($kol_id, $kol_startdate, $kol_counttdate, $period){
		// 計算當下與開始計算當下時間與該網紅指定開賣日的差異
        $period_now = $this->get_current_period($kol_startdate, $kol_counttdate); // 取得當前期數 
        if($period<=0){
            $this->error(LANG_MENU['內容有誤']); /*期數選擇有誤*/
        }else if( $period_now-$period < 2){ // 當前期數與所選期數相差小於2期，則不能看
            $this->error(LANG_MENU['內容有誤']); /*此期數尚未拋轉至結算中，無法查看*/
        }

        $s_time = date('Y-m-d', strtotime($kol_startdate)).' +'.($kol_counttdate*($period-1)).'Days';
        $e_time = $s_time.' +'.($kol_counttdate).'Days';

        // 選出該期有該網紅推廣商品的訂單
        $type_where = strtotime($s_time).' <= o.create_time and o.create_time <'.strtotime($e_time);
        $orderform = Db::connect('main_db')->table('orderform o')
                    ->field('o.order_number, o.create_time, o.transport_location, o.product, a.name as user_name')
                    ->join('account a', 'a.id = o.user_id', 'left')
                    ->where('o.create_time >= '.strtotime($kol_startdate).' AND o.status ="Complete" AND o.product like "%_kol'.$kol_id.'%" AND '.$type_where)
                    ->order('o.id desc')
                    ->select();
        return $this->hide_info($orderform);
	}
	// 取得符合搜尋結果的目標訂單
    public function get_target_order($kol_id, $kol_startdate, $kol_counttdate, $type, $pass_s_time=false, $pass_e_time=false, $page=false){
        $return_data = [];

        $period_now = $this->get_current_period($kol_startdate, $kol_counttdate); // 取得當前期數
        if( $period_now > 2 ){ // 如果當前期數不是1,2期
        	// 計算最近2期的開始時間
        	$pre_two_period =  date('Y-m-d', strtotime($kol_startdate.' 00:00:00 +'.(($period_now - 2)*$kol_counttdate).'Days'));

        	// 計算當下與最近兩期的開始時間相差幾天
            $diff_startdate = date_diff( date_create($pre_two_period), date_create(date('Y-m-d',time())) );
        }else{
        	// 計算當下與開始計算當下時間與該網紅指定開賣日的相差幾天
        	$diff_startdate= date_diff( date_create($kol_startdate), date_create(date('Y-m-d',time())) );
        }
        $pre_days = $diff_startdate->days;
        $time_pre_days = date('Y-m-d',time()).' 00:00:00 -'.$pre_days.'Days';
        $time_pre_days_1s = date('Y-m-d',time()).' 00:00:00 -'.$pre_days.'Days -1seconds';

        $type_where = 'true'; $type_iscounted = 'true'; $page_where = 'true';
        $s_time=''; $e_time ='';
        $limmit='';
        switch ($type) {
            case '0': // 未結算( 顯示最近兩期的成交訂單 )
                // 處理開始時間
                $start = $pass_s_time ? $pass_s_time : $time_pre_days;
                if( strtotime($start) <= strtotime($time_pre_days) ){ // 限制開始時間必須大於最近兩期的時間開始點
                    $s_time = strtotime($time_pre_days);
                    $start = date('Y-m-d',strtotime($start));
                }else{
                    $s_time = strtotime($start);
                }

                // 處理結束時間
                $end = $pass_e_time ? $pass_e_time : date('Y-m-d',time());
                $e_time = strtotime($end.'+1Days');

                // 處理page條件(limit)
                if($page)
                	$limmit = (($page-1)*self::PER_PAGE_ROWS).','.self::PER_PAGE_ROWS;
                break;

            case '1' || '2': // 結算中or已結算( 顯示最近兩期之外的成交訂單 )
                // 處理開始時間
                if( $pass_s_time ){
                    $start = $pass_s_time;
                    $s_time = strtotime($start);
                }else{
                    $start = $kol_startdate;
                    $s_time = strtotime($kol_startdate);
                }

                // 處理結束時間
                $end = $pass_e_time ? $pass_e_time : $time_pre_days;
                if( strtotime($end) >= strtotime($time_pre_days) ){ // 搜尋時間必須小於最近兩期的時間開始點
                    $e_time = strtotime($time_pre_days);
                    $end = date('Y-m-d',strtotime($time_pre_days_1s));
                }else if( strtotime($end) < strtotime($kol_startdate)){ // 若小於了網紅開賣時間
                    // $e_time = '-28800';
                    $e_time = strtotime($kol_startdate.' +1Days');
                }else{
                    $diff_end= date_diff( date_create($kol_startdate), date_create(date('Y-m-d',strtotime($end))) ); // 計算搜尋的結束時間與網紅開罵時間差異
                    if($diff_end->days % $kol_counttdate == 0){ // 強制調整搜尋時間符合期數區間
                        $e_time = strtotime( date('Y-m-d',strtotime($end)).' 00:00:00 +'.$kol_counttdate.'Days' );
                    }else{
                        $add_day = $kol_counttdate-($diff_end->days%$kol_counttdate);
                        $e_time = strtotime( $end.' 00:00:00 +'.(string)$add_day.'Days' );
                    }
                }

                // 處理page條件
				$s_period_diff = date_diff( date_create($kol_startdate), date_create(date('Y-m-d', $s_time)) );
				$s_period = $s_period_diff->days % $kol_counttdate == 0 ? ($s_period_diff->days/$kol_counttdate)+1 : ceil($s_period_diff->days/$kol_counttdate);

				$e_period_diff = date_diff( date_create($kol_startdate), date_create(date('Y-m-d', $e_time)) );
				$e_period_diff_days = $e_period_diff->days-$kol_counttdate;
				$e_period = ($e_period_diff_days) % $kol_counttdate == 0 ? ($e_period_diff_days/$kol_counttdate)+1 : ceil($e_period_diff_days/$kol_counttdate);

				$page = $page ? $page : 1; // 預設第一頁
				$page = (int)$page;
				$p_s_period = $e_period - ($page)*self::PER_PAGE_ROWS + 1;
				$p_s_period = $p_s_period < $s_period ? $s_period : $p_s_period;

				$p_e_period = $e_period - ($page-1)*self::PER_PAGE_ROWS;
				$p_e_period = $e_period < 1 ? 0 : $p_e_period;

				$page_s_time = strtotime($kol_startdate.' 00:00:00 +'.(($p_s_period-1)*$kol_counttdate).'Days');
				$page_e_time = strtotime($kol_startdate.' 00:00:00 +'.($p_e_period*$kol_counttdate).'Days');
				$page_where = '('.$page_s_time.' <= o.create_time and o.create_time < '.$page_e_time.')';

				// 處理確認期數條件
                $confirm_sale = Db::table('kol_confirm_sale')->where('kol_id ='.$kol_id)->where('s_time between '.$page_s_time.' and '.$page_e_time)->select();
                if($type=='1'){ // 結算中
                    if($confirm_sale){
                        $type_iscounted = [];
                        foreach ($confirm_sale as $key => $value) { // 排除已確認期數的訂單
                            array_push( $type_iscounted, '( o.create_time < '.$value['s_time'].' or '.strtotime( date('Y-m-d',$value['s_time']).' +'.$kol_counttdate.'Days' ).' <= o.create_time)' ); // not between
                        }
                        $type_iscounted = join(' AND ', $type_iscounted);
                    }else{
                        $type_iscounted = 'true'; // 沒確認過的就全顯示
                    }
                }else if($type=='2'){ // 已結算
                    if($confirm_sale){
                        $type_iscounted = [];
                        foreach ($confirm_sale as $key => $value) { // 只顯示已確認期數的訂單
                            array_push( $type_iscounted, '('.$value['s_time'].' <= o.create_time and o.create_time < '.strtotime( date('Y-m-d',$value['s_time']).' +'.$kol_counttdate.'Days' ).')' ); // between
                        }
                        $type_iscounted = '('.join(' OR ', $type_iscounted).')';
                    }else{
                        $type_iscounted = 'false'; // 沒確認過的就全部不顯示
                    }
                }
                break;
        }
        // dump('start:'.$start);
        // dump('s_time:'.$s_time);
        // dump('end:'.$end);
        // dump('e_time:'.$e_time);
        // exit;

        $type_where = '('.$s_time.' <= o.create_time and o.create_time < '.$e_time.')'; 
        // dump('o.product like "%_kol'.$kol_id.'%" AND '.$type_where.' AND '.$type_iscounted);

        // 選出有該網紅推廣商品的訂單
        $orderform = Db::connect('main_db')->table('orderform o')
                    ->field('o.order_number, o.create_time, o.transport_location, o.product, a.name as user_name')
                    ->join('account a', 'a.id = o.user_id', 'left')
                    ->where('o.create_time >= '.strtotime($kol_startdate).' AND o.status ="Complete" AND o.product like "%_kol'.$kol_id.'%" AND '.$type_where.' AND '.$type_iscounted.' AND '.$page_where)
                    ->order('o.id desc')
                    ->limit($limmit) // $type==0時才會用到
                    ->select();
        $return_data = [
            'start'     => $start,      // 搜尋區間的時間開始點
            'end'       => $end,        // 搜尋區間的時間結束點

            's_period'	=> isset($s_period) ? $s_period : '',	// 搜尋區間包含期數的第一期的期數
            'e_period'	=> isset($e_period) ? $e_period : '',	// 搜尋區間包含期數的最後一期的期數
            
            'p_s_period'=> isset($p_s_period) ? $p_s_period : '',	// 頁數包含期數的第一期的期數
            'p_e_period'=> isset($p_e_period) ? $p_e_period : '',	// 頁數包含期數的最後一期的期數

            'orderform' => $orderform   // 回傳目標訂單
        ];
        return $return_data;
    }
    // 依期數整理
    public function group_by_period($kol_id, $kol_startdate, $kol_counttdate, $type=false, $orderform, $period_range=[0,0]){
        $period_sale =[];
        // dump($orderform);exit;

        // 處理確認期數條件
        if($type=='1'){ // 結算中，顯示未確認的期數
            $check_confirm = function($check){ return empty($check); };
        }else if($type=='2'){ // 已結算，顯示已確認的期數
            $check_confirm = function($check){ return !empty($check); };
        }else{
        	$check_confirm = function($check){ return true; };
        }

        // 根據給定的期數區間產生資料
        if($period_range[0] > 0 && $period_range[1] > 0){
            foreach (range($period_range[0], $period_range[1]) as $period) {
                $confirm_sale = Db::table('kol_confirm_sale')->where('kol_id ='.$kol_id.' AND period ='.$period)->select();
                
                if( $check_confirm($confirm_sale)){ // 符合確認條件
                    // 產生沒訂單的對應期數資料
                    $s_period = date('Y-m-d', strtotime($kol_startdate)).' +'.($kol_counttdate*($period-1)).'Days';
                    $e_period = $s_period.' +'.($kol_counttdate-1).'Days';
                    $period_sale[$period] = ['total'=>0, 'order'=>[], 's_period'=>strtotime($s_period), 'e_period'=>strtotime($e_period)];
                }
            }
        }
        
        // 依對應期數整理符合條件的訂單
        foreach ($orderform as $key => $value) {
            $diff_createtime= date_diff( date_create($kol_startdate), date_create(date('Y-m-d', $value['create_time'])) );

            // 計算該訂單落於哪一期
            if( $diff_createtime->days % $kol_counttdate == 0){
                $preiod_count = $diff_createtime->days / $kol_counttdate + 1;
            }else{
                $preiod_count = ceil($diff_createtime->days / $kol_counttdate);
            }

            // 記錄對應期數銷售的商品
            $decode_product = json_decode($value['product']);
            $value['product'] = [];
            foreach ($decode_product as $p_key => $p_value) {
                $p_value = (array)$p_value;
                if(isset($p_value['key_type'])){
                    if($p_value['key_type'] == 'kol'.$kol_id){
                        array_push($value['product'], $p_value);
                        $period_sale[$preiod_count]['total'] += (int)$p_value['total'];
                    }
                }
            }
            array_push($period_sale[$preiod_count]['order'], $value);
        }

        return $period_sale;
    }

    // 取得當前期數
    public function get_current_period($kol_startdate, $kol_counttdate){
        $diff_startdate= date_diff( date_create($kol_startdate), date_create(date('Y-m-d',time())) );
        $diff_startdate = $diff_startdate->days;
        $period_now = $diff_startdate % $kol_counttdate == 0 ? $diff_startdate / $kol_counttdate+1 : ceil($diff_startdate / $kol_counttdate); // 當前期數
        return $period_now;
    }

    // 取得過往所有已結算銷售記錄
    public function get_sale_record(){
    	if(session::get('kol')){ // 有用網紅登入就用網紅看
	    	$kol = session::get('kol');
			$kol_id = $kol['id'];
			$kol_startdate = $kol['start_date'];
	        $kol_counttdate = (int)$kol['count_days'];
	    }else if(session::get('admin')){ // 用admin登入則依查詢值看
	        $kol_id = $_POST['kol_id'] ? $_POST['kol_id'] : $this->error(LANG_MENU['資訊不足']); /*請選擇網紅*/
	        $kol = Db::table($this->resTableName)->find($kol_id);
	        $kol_startdate = $kol['start_date'];
	        $kol_counttdate = (int)$kol['count_days'];
	    }else{ // 都沒有就不允許使用
	    	$this->error(LANG_MENU['請以網紅身分登入']);
	    }

        $type = $_POST['type'] ? $_POST['type'] : $this->error(LANG_MENU['資訊不足']); /*請選結算類型*/

        // 不設時間，取得所有已結算的訂單
        $return_data = $this->get_target_order($kol_id, $kol_startdate, $kol_counttdate, $type);

        // 依產品+品項整理
        $product_list = [];
        foreach ($return_data['orderform'] as $key => $value) { // 處理每一筆訂單
            $product = json_decode($value['product']);

            foreach ($product as $k_p => $v_p) { // 檢查每一個商品
                $v_p = (array)$v_p;
                if(!isset($v_p['key_type'])) continue;

                if($v_p['key_type']=='kol'.$kol_id){ // 該商品是此網紅推廣的
                    if( !isset($product_list[$v_p['name']]) ) $product_list[$v_p['name']] = ['num'=>0,'total'=>0, 'product'=>[]];

                    $product_list[$v_p['name']]['num'] += $v_p['num'];                    
                    $product_list[$v_p['name']]['total'] += $v_p['total'];                    
                    $product_list[$v_p['name']]['product'] = $v_p;                    
                }
            }
        }

        // 找出開賣日期
        foreach ($product_list as $key => $value) {
            // 找此網紅對此商品最早的設定記錄
            if(isset($value['product']['info_id'])){
                $kol_productinfo = Db::table('kol_productinfo')->where('kol_id='.$kol_id.' AND productinfo_id='.$value['product']['info_id'])->order('id asc')->select()[0];
                $product_list[$key]['s_time'] = date('Y-m-d', $kol_productinfo['time']);
            }else{
                $product_list[$key]['s_time'] = '';
            }
        }

        return $product_list;
    }

    // 取得所有代銷中的商品
    public function get_selling_product(){
	    if(session::get('kol')){ // 有用網紅登入就用網紅看
	    	$kol = session::get('kol');
			$kol_id = $kol['id'];
	    }else if(session::get('admin')){ // 用admin登入則依查詢值看
	        $kol_id = $_GET['kol_id'] ? $_GET['kol_id'] : $this->error(LANG_MENU['資訊不足']); /*請選擇網紅*/
	    }else{ // 都沒有就不允許使用
	    	$this->error(LANG_MENU['請以網紅身分登入']);
	    }
    	$productinfo = Db::table('productinfo p')
                    ->field('p.id, p.title, p.product_id, p.pic, kp.kol_id')
                    ->join('kol_productinfo kp', 'kp.productinfo_id = p.id', 'left')
                    ->where('kp.kol_id ='.$kol_id.' AND kp.is_using=1')
                    ->select();
    	array_walk($productinfo,  function($item, $key) use(&$productinfo){
    		$productinfo[$key]['pic'] = 'https://'.$_SERVER['HTTP_HOST'].'/public/static/index/'.json_decode($item['pic'],true)[0];
    	});

    	return $productinfo;
    }

	// 訂單加密
	public function hide_info($orderform){
		foreach ($orderform	 as $key => $value) {
			$orderform[$key]['order_number'] = parent::hidestr($value['order_number'], -6);
			$orderform[$key]['user_name'] = $value['user_name'] ? parent::hidestr($value['user_name'],1,-1) : '';
			$orderform[$key]['transport_location'] = parent::hidestr($value['transport_location'],6);
		}

		return $orderform;
	}
}

