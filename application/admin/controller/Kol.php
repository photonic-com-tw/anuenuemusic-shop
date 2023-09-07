<?php
namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

//Photonic Class
use controllerInterface\resources\SoleType;
use DBtool\DBTextConnecter;
use app\index\controller\Kol as KolFront;

class Kol extends MainController implements SoleType{

    private $DBTextConnecter;
    private $resTableName;
    const PER_PAGE_ROWS = 50;
	const SIMPLE_MODE_PAGINATE = false;



    //this resources's frontend use Single Page Web, some CURD method is useless
    public function edit(){}
    public function create(){}

    public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('kol');
        $this->resTableName = 'kol';
    }

    public function index() {
        $searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);
        $kol = Db::table($this->resTableName)
            ->where("
                        email LIKE '%$searchKey%' OR 
                        kol_name LIKE '%$searchKey%' OR 
                        real_name LIKE '%$searchKey%' OR 
                        english_name LIKE '%$searchKey%' OR 
                        phone LIKE '%$searchKey%' OR 
                        category LIKE '%$searchKey%'")
            ->order('id desc')
            ->paginate(
                self::PER_PAGE_ROWS,
                self::SIMPLE_MODE_PAGINATE, 
                [
                    'query' => [
                        'searchKey' => $searchKey
                    ]
                ]
            );
        $this->assign('kol', $kol);
		return $this->fetch('set');
    }

    public function delete() {
        $id = Request::instance()->get('id');
        try{
            Db::table($this->resTableName)->delete($id);
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功');
    }

    public function multiDelete() {
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

    // 檢查必填欄位
    private function check_require($data){
        if($data['email']==''){
            $this->error('請輸入帳號(MAIL)');
        }else if($data['password']==''){
            $this->error('請輸入密碼');
        }else if($data['kol_name']==''){
            $this->error('請輸入網紅名');
        }else if($data['mobile']==''){
            $this->error('請輸入手機');
        }else if($data['bank_name']==''){
            $this->error('請輸入匯款銀行');
        }else if($data['bank_account']==''){
            $this->error('請輸入匯款帳號');
        }else if($data['start_date']==''){
            $this->error('請輸入起計日');
        }else if($data['count_days']==''){
            $this->error('請輸入結算日周期');
        }
    }

    public function doCreate() {
        $newData = Request::instance()->post();

        // 檢查必填欄位
        $this->check_require($newData);

        $has_account = Db::table($this->resTableName)->where('email ="'.$newData['email'].'"')->select();
        if(empty($has_account)){
            $newData['creatdate'] = time();
            try{
                $this->DBTextConnecter->setDataArray($newData);
                $this->DBTextConnecter->createTextRow();
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
        }else{
            $outputData = [
                'status' => false,
                'message' => '帳號重複'
            ];
        }

		return json($outputData, 200);
    }

    public function update() {
        $newData = Request::instance()->post();

        // 檢查必填欄位
        $this->check_require($newData);

        // 不允許修改起計日、結算日周期
        unset($newData['start_date']);
        unset($newData['count_days']);

        $has_account = Db::table($this->resTableName)->where('email ="'.$newData['email'].'" and id !='.$newData['id'])->select();
        if(empty($has_account)){
            try{
                $this->DBTextConnecter->setDataArray($newData);
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
        }else{
            $outputData = [
                'status' => false,
                'message' => '帳號重複'
            ];
        }
		return json($outputData, 200);
    }




    public function salelist(){
        $kol_id = Request::instance()->get('id') ? Request::instance()->get('id') : $this->error('請選擇網紅');

        $type = Request::instance()->get('type') ? Request::instance()->get('type') : '0'; // 預設未結算
        $this->assign('type', $type);

        $page = Request::instance()->get('page') ? Request::instance()->get('page') : 1; // 預設第一頁
        $this->assign('page', $page);
        
        $kol = Db::table($this->resTableName)->find($kol_id);
        $kol_startdate = $kol['start_date'];
        $kol_counttdate = (int)$kol['count_days'];
        $this->assign('kol', $kol);
        
        // 取得符合搜尋區間及類型的訂單
        $start = Request::instance()->get('start');
        $end = Request::instance()->get('end');
        $KolFront = new KolFront();
        $return_data = $KolFront->get_target_order($kol_id, $kol_startdate, $kol_counttdate, $type, $start, $end, $page); // 依頁數找出符合條件的訂單
        $this->assign('start', $return_data['start']);
        $this->assign('end', $return_data['end']);
        $this->assign('orderform', $return_data['orderform']);

        switch ($type) {
            case '0': // 未結算
                // 計算總頁數
                $return_all_data = $KolFront->get_target_order($kol_id, $kol_startdate, $kol_counttdate, $type, $start, $end, false); // 找出所有符合條件的訂單
                $this->assign('totalpage', ceil(count($return_all_data['orderform'])/self::PER_PAGE_ROWS));
                return $this->fetch('salelist');
                break;

            case '1' || '2': // 結算中or已結算
                $KolFront = new KolFront();
                $period_sale = $KolFront->group_by_period($kol_id, $kol_startdate, $kol_counttdate, $type, $return_data['orderform'], [$return_data['p_s_period'], $return_data['p_e_period']]);
                $this->assign('period_sale', $period_sale);

                // 計算總頁數
                $all_period = $KolFront->group_by_period($kol_id, $kol_startdate, $kol_counttdate, $type, $return_data['orderform'], [$return_data['s_period'], $return_data['e_period']]);
                $this->assign('totalpage', ceil( count($all_period)/self::PER_PAGE_ROWS) );

                return $this->fetch('salelist_period');
                break;
        }
    }

    public function sale_detail(){
        $kol_id = Request::instance()->get('id') ? Request::instance()->get('id') : $this->error('請選擇網紅');
        $kol = Db::table($this->resTableName)->find($kol_id);
        $kol_startdate = $kol['start_date'];
        $kol_counttdate = (int)$kol['count_days'];
        $this->assign('kol', $kol);

        $period = Request::instance()->get('period') ? Request::instance()->get('period') : $this->error('請選擇期數'); // 所選期數
        $this->assign('period', $period);

        // 依期數取得標訂單
        $KolFront = new KolFront();
        $orderform = $KolFront->get_target_order_by_period($kol_id, $kol_startdate, $kol_counttdate, $period);
        $this->assign('orderform', $orderform);
        
        $KolFront = new KolFront();
        $period_sale = $KolFront->group_by_period($kol_id, $kol_startdate, $kol_counttdate, false, $orderform, [$period,$period]);
        if(empty($period_sale)){
            $period_sale[0] =  ['total'=>0, 'order'=>[], 's_period'=>strtotime($s_time), 'e_period'=>strtotime($e_time)];
        }
        $this->assign('period_sale', $period_sale);

        $confirm_sale = Db::table('kol_confirm_sale')->where('kol_id ='.$kol_id.' AND period ='.$period)->find();
        $need_confirm = empty($confirm_sale) ? 'true' : 'false';
        $this->assign('need_confirm', $need_confirm);
        $this->assign('confirm_sale', $confirm_sale);

        return $this->fetch('sale_detail');
    }

    // 確認結算
    public function confirm_period(){

        if( empty($_POST['kol_id']) ) $this->error('沒選擇網紅');

        $kol_confirm_sale = Db::table('kol_confirm_sale')->where('kol_id ='.$_POST['kol_id'].' AND period ='.$_POST['period'])->select();
        if($kol_confirm_sale){
            $this->error('此期數已確認過');
        }

        // 取得網紅資料
        $kol = Db::table($this->resTableName)->find($_POST['kol_id']);
        $kol_startdate = $kol['start_date'];
        $kol_counttdate = (int)$kol['count_days'];

        // 計算該期開始時間
        $add_day = ((int)($_POST['period'])-1) * $kol_counttdate;
        $_POST['s_time'] = strtotime($kol['start_date'].' +'.$add_day.'Days');

        // 確認本單時間
        $_POST['create_time'] = time();

        // dump($_POST);
        $kol_confirm_sale_db = DBTextConnecter::withTableName('kol_confirm_sale');
        $kol_confirm_sale_db->setDataArray($_POST);
        $kol_confirm_sale_db->createTextRow();

        $this->success('確認成功');
    }
}