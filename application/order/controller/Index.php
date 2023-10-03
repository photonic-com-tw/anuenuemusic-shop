<?php
namespace app\order\controller;

use app\order\controller\MainController;
use think\Validate;
use think\Request;
use think\Db;

//Photonic Class
use DBtool\DBTextConnecter;
use pattern\PointRecords;
use pattern\MemberInstance;
use app\ajax\controller\VipType;

class Index extends MainController
{

	const PER_PAGE_ROWS = 20;
	const SIMPLE_MODE_PAGINATE = false;
	const ACCOUNT_MODE_ENUM = ['審核中', '通過', '黑名單', '停用'];
	const ORDER_STATE = [
		'New' => '新進訂單', 
		'Complete' => '完成訂單', 
		'Cancel' => '取消訂單',
		'Return' => '退貨訂單'
	];
	private $tableName;
	private $DBTextConnecter;

	public function __construct() {
		header("Access-Control-Allow-Origin: *");
        parent::__construct();
		$this->tableName = 'account';
		$this->DBTextConnecter = DBTextConnecter::withTableName('account', 'main_db');		

    }
    public function accountDB(){
    	return Db::connect('main_db')->table($this->tableName);
    }
    public function orderformDB(){
    	return Db::connect('main_db')->table('orderform');
    }

    /*會員列表頁*/
	public function index($status="", $skip='no') {
		$search_result = MemberInstance::search_member($status, Request::instance());
		$this->assign('tag', $search_result['tag']);
		$this->assign('searchKey1', $search_result['searchKey1']);
		$this->assign('searchKey2', $search_result['searchKey2']);
		$this->assign('memberKey', $search_result['memberKey']);
		$this->assign('memberFromType', $search_result['memberFromType']);
		$this->assign('nameKey', $search_result['nameKey']);
		$this->assign('vipType', $search_result['vipType']);
		$this->assign('date_st', $search_result['date_st']);	
		$this->assign('date_en', $search_result['date_en']);
		$this->assign('buy_date_st1', $search_result['buy_date_st1']);
		$this->assign('buy_date_en1', $search_result['buy_date_en1']);
		$this->assign('buy_date_st2', $search_result['buy_date_st2']);
		$this->assign('buy_date_en2', $search_result['buy_date_en2']);
		$this->assign('reg_date_st', $search_result['reg_date_st']);
		$this->assign('reg_date_en', $search_result['reg_date_en']);

		
		$allMember = count($search_result['rowData']);
		$this->assign('allMember', $allMember);
		
		$page = Request::instance()->get('page') ?? '1';
		$this->assign('page', $page);
		$offset = 0+(($page-1)*self::PER_PAGE_ROWS);
		if($search_result['tag'] == '1' && $search_result['nameKey']!=''){
			//  已註冊/已購買的會員
			$rowDataItem = array_slice($search_result['h_number'], $offset,self::PER_PAGE_ROWS, true);
			$total_pages = floor(count($search_result['h_number'])/self::PER_PAGE_ROWS);
		}
		if($search_result['tag'] == '2' && $search_result['nameKey']!=''){
			//  未註冊/未購買的會員
			$rowDataItem = array_slice($search_result['n_number'], $offset,self::PER_PAGE_ROWS, true);
			$total_pages = floor(count($search_result['n_number'])/self::PER_PAGE_ROWS);
		}
		if($search_result['nameKey']==''){
			//  總資料
			$rowDataItem = array_slice($search_result['rowData'], $offset,self::PER_PAGE_ROWS, true);
			$total_pages = floor($allMember/self::PER_PAGE_ROWS);
		}
		$total_pages += 1;
		$this->assign('pages', range(1,$total_pages));
		$this->assign('total_pages', $total_pages);

		$this->assign('do_number', $search_result['do_number']); // 已註冊/已購買人數
		$do_percent = $search_result['do_number']!=0 ? round(($search_result['do_number']/$allMember)*100,2).'%' : '0%';
		$this->assign('do_percent', $do_percent); // 已註冊/已購買率

		if($search_result['nameKey'] == "1"){
			$this->assign('tag_name',['','已註冊','未註冊']);
		}
		if($search_result['nameKey'] == "2"){
			$this->assign('tag_name',['','已購買','未購買']);
		}
		//dump($rowDataItem);
		foreach ($rowDataItem as $key => $value) {						
			if($rowDataItem[$key]['status'] < count(self::ACCOUNT_MODE_ENUM) && $rowDataItem[$key]['status'] >= 0){
				$rowDataItem[$key]['status'] = self::ACCOUNT_MODE_ENUM[$rowDataItem[$key]['status']];
			}else{
				$rowDataItem[$key]['status'] = "";
			}
			if($skip=='skip'){ // 略過計算
				$rowDataItem[$key]['cancel'] = 0;
				$rowDataItem[$key]['return'] = 0;
				$rowDataItem[$key]['complete'] = 0;
				$rowDataItem[$key]['totalcom'] = 0;
				$rowDataItem[$key]['totaldel'] = 0;
			
			}else{ // 處理計算
				$rowDataItem[$key]['cancel'] = $this->orderformDB()->where([
					'user_id' => $rowDataItem[$key]['id'],
					'status' => 'Cancel'
				])->count();
				$rowDataItem[$key]['return'] = $this->orderformDB()->where([
					'user_id' => $value['id'],
					'status' => 'Return'
				])->count();
				$rowDataItem[$key]['complete'] = $this->orderformDB()->where([
					'user_id' => $rowDataItem[$key]['id'],
					'status' => 'Complete'
				])->count();
				$rowDataItem[$key]['totalcom'] = $this->orderformDB()->where([
					'user_id' => $rowDataItem[$key]['id'],
					'status' => 'Complete'
				])->sum('total');
				$rowDataItem[$key]['totaldel'] = $this->orderformDB()->where([
					'user_id' => $rowDataItem[$key]['id'],
					'status' => 'Cancel',
					'status' => 'Return'
				])->sum('total');
			}
		}
		$this->assign('rowDataItem', $rowDataItem);

		$this->assign('Export', ['未匯出','已匯出']);
		$this->assign('Registered', ['未註冊','已註冊']);
		$this->assign('Buy', ['未購買','已購買']);
		
		$Member_status = ['會員全部列表','新進未開通','黑名單列表','停用名單列表'];
		$Member_status_tag = $status ? $Member_status[$status] : $Member_status['0'];
		$this->assign('Member_status', $Member_status_tag);
		$this->assign('urlstatus',$status);
		
		// VIP等級
		$vip_type = VipType::get_types();
		$this->assign('vip_type', $vip_type);
		
		return $this->fetch('index');
	}
	/*AJAX搜尋會員*/
	public function ajax_search(){
		$search_result = MemberInstance::search_member($status="", Request::instance());
		if($search_result['tag'] == '1' && $search_result['nameKey']!=''){
			//  已註冊/已購買的會員
			$rowDataItem = $search_result['h_number'];
		}
		if($search_result['tag'] == '2' && $search_result['nameKey']!=''){
			//  未註冊/未購買的會員
			$rowDataItem = $search_result['n_number'];
		}
		if($search_result['nameKey']==''){
			//  總資料
			$rowDataItem = $search_result['rowData'];
		}

		$search_result['rowData'] = $rowDataItem;
		return json($search_result, 200);
	}

	/*詳細內容頁面*/
	public function edit($id) {
		/*會員資料*/
		$MemberInstance = new MemberInstance($id);
		$rowData = $MemberInstance->get_user_data();

		/*會員相關統計*/
		$rowData['cancel'] = $MemberInstance->get_user_order_data(['status'=>'Cancel', 'method'=>'count']);
		$rowData['return'] = $MemberInstance->get_user_order_data(['status'=>'Return', 'method'=>'count']);
		$rowData['complete'] = $MemberInstance->get_user_order_data(['status'=>'Complete', 'method'=>'count']);
		$rowData['totalcom'] = $MemberInstance->get_user_order_data(['status'=>'All', 'method'=>'sum']);
		// $rowData['totaldel'] = $MemberInstance->get_user_order_data(['status'=>'All_no', 'method'=>'sum']);

		/*訂單資料*/
		$rowData['order'] = $MemberInstance->get_user_order_data(['status'=>'All', 'method'=>'select']);
		foreach ($rowData['order'] as &$value) {
			$value['subDepartment'] = substr($value['order_number'], 0, 1);
			$value['status'] = self::ORDER_STATE[$value['status']];
		}

		$MemberInstance->change_user_id($rowData['upline_user'] ?? 0);
		$rowData['up_user'] = $MemberInstance->get_user_data();
		// dump($rowData);exit();
		$this->assign('rowData', $rowData);

		/*註冊商品資料*/
		$reg_product = Db::connect(config('A_sub'))->table('excel')->where("account_number = '" . $rowData['id'] . "'" )->select();
		$this->assign('reg_product', $reg_product);

		return $this->fetch('member-manager-info');
	}

    /*單筆刪除*/
	public function delete() {
        $id = Request::instance()->get('id');
        try{
            $this->accountDB()->delete($id);
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功', url('Index/index'));
    }
    /*批次刪除*/
    public function multiDelete() {
        $idList = Request::instance()->post('id');
        try{
            if ($idList) {
                $idList = json_decode($idList);
                $this->accountDB()->delete($idList);
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功', url('Index/index'));
	}
	/*批次修改(VIP等級、會員狀態)*/
	public function multiupdate() {
		$idList = Request::instance()->post('id');
		$column = Request::instance()->post('column');
		$value = Request::instance()->post('value');

		// 檢查欄位是否允許批次修改
		if(!in_array($column, ["vip_type", "status"])) $this->error("此欄位不可批次修改");

        try{
            if ($idList) {
				$idList = json_decode($idList);
				foreach ($idList as $v) {

					if($column=="vip_type"){
						// 修改vip_type並新增等級轉換紀錄
						$VipType = new VipType();
						$VipType->update_vip_type($user_id=$v, $vip_type=$value);

					}else{
						$newData = [
							'id' => $v,
							$column => $value
						];
						$this->DBTextConnecter->setDataArray($newData);
						$this->DBTextConnecter->upTextRow();
					}
				}
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('修改成功', url('Index/index'));
    }

	
	/*匯出會員*/
	public function member_excel($status="") {
		$search_result = MemberInstance::search_member($status, Request::instance());
		if($search_result['nameKey']!=''){ /*註冊、購買商品搜尋*/
			$memeber = $search_result['h_number'];
			$memeber_no = $search_result['n_number'];
		}
		else{ /*會員搜尋*/
			//  總資料
			$memeber = $search_result['rowData'];
			$memeber_no = [];
		}

		foreach ($memeber as $key =>$value) {
			//歷史購買定單編開始
			$memeber[$key]['order'] = $this->get_user_order($value['id']);
			//註冊商品編號開始
			$memeber[$key]['excel'] = $this->get_user_excel($value['id']);
		}
		foreach ($memeber_no as $key =>$value) {
			//歷史購買定單編開始
			$memeber_no[$key]['order'] = $this->get_user_order($value['id']);
			//註冊商品編號開始
			$memeber_no[$key]['excel'] = $this->get_user_excel($value['id']);
		}
		// dump($memeber);dump($memeber_no);exit;

		$objPHPExcel = new \PHPExcel();

		//輸出Excel檔
		header('Content-Type: application/vnd.ms-excel');
		//下載檔案名字跟類型
		header('Content-Disposition: attachment;filename=會員資料匯出.xlsx');
		//禁止緩存每次都是新下載
		header('Cache-Control: max-age=0');
		//建表（格式可以是2007（2010之後版本的EXCEL）、或5（支持95～2003））
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
		//輸出檔案
		
		// 設定工作表
		$objPHPExcel -> setActiveSheetIndex(0);       //要使用的工作表
		$Sheet = $objPHPExcel -> getActiveSheet();    //取得作用中的工作表
		switch ($search_result['nameKey']) {
		  case "1":
			$Sheet -> setTitle("已註冊");             //設定工作表名稱
			break;
		  case "2":
			$Sheet -> setTitle("已購買");             //設定工作表名稱
			break;
		  default:
			$Sheet -> setTitle("會員");             //設定工作表名稱
		}

		$objPHPExcel -> createSheet();                //建立工作表
		//儲存格一般文字或數字欄位用setCellvalue即可
		$Sheet -> setCellValue("A1","此檔案可用匯入，匯入時請勿刪除第1,2列");
		$Sheet -> setCellValue("A2","會員名稱");
		$Sheet -> setCellValue("B2","信箱(帳號，一樣視為編輯、不一樣視為新增)");
		$Sheet -> setCellValue("C2","密碼(不輸入則不修改)");
		$Sheet -> setCellValue("D2","地址");
		$Sheet -> setCellValue("E2","手機");
		$Sheet -> setCellValue("F2","電話");
		$Sheet -> setCellValue("G2","生日(YYYY/MM/DD)");	
		$Sheet -> setCellValue("H2","歷史訂單編號");
		$Sheet -> setCellValue("I2","註冊商品編號");
		
		// 建立未註冊、未購買商品搜尋的工作表
		if($search_result['nameKey'] != ''){
			$objPHPExcel -> setActiveSheetIndex(1);       //要使用的工作表
			$Sheet_1 = $objPHPExcel -> getActiveSheet();    //取得作用中的工作表
			switch ($search_result['nameKey']) {
			  case "1":
				$Sheet_1 -> setTitle("未註冊");             //設定工作表名稱
				break;
			  case "2":
				$Sheet_1 -> setTitle("未購買");             //設定工作表名稱
				break;

			}
			$objPHPExcel -> createSheet();                //建立工作表		
			
			//儲存格一般文字或數字欄位用setCellvalue即可
			$Sheet_1 -> setCellValue("A1","此檔案可用匯入，匯入時請勿刪除第1,2列");
			$Sheet_1 -> setCellValue("A2","會員名稱");
			$Sheet_1 -> setCellValue("B2","信箱(帳號，一樣視為編輯、不一樣視為新增)");
			$Sheet_1 -> setCellValue("C2","密碼(不輸入則不修改)");
			$Sheet_1 -> setCellValue("D2","地址");
			$Sheet_1 -> setCellValue("E2","手機");
			$Sheet_1 -> setCellValue("F2","電話");	
			$Sheet_1 -> setCellValue("G2","生日(YYYY/MM/DD)");	
			$Sheet_1 -> setCellValue("H2","歷史訂單編號");
			$Sheet_1 -> setCellValue("I2","註冊商品編號");
		}

		// 設定excel內容
		$Sheet = $this->set_excel_member_content($Sheet, $memeber);
		if(isset($Sheet_1)){ $Sheet_1 = $this->set_excel_member_content($Sheet_1, $memeber_no); }
		
		$objWriter -> save('php://output');
		//echo "<script>alert('匯出資料完成'); location.href = '".url('Index/index')."';</script>";
		//$objWriter->save('test.xlsx');
	}
	/*取得使用者訂單編號(組成文字)*/
	public function get_user_order($user_id){
		$MemberInstance = new MemberInstance($user_id);
		$orderform = $MemberInstance->get_user_order_data(['status'=>'All', 'method'=>'select']);

		$order_list = '';
		foreach ($orderform as $order_key =>$order_value){  
			$order_list .= $orderform[$order_key]['order_number'];
			if(!empty($orderform[$order_key+1]['order_number']))
				$order_list .='，';
		}
		return $order_list;
	}
	/*取得使用者註冊商品機身碼(組成文字)*/
	public function get_user_excel($user_id){
		$excel = Db::connect(config('A_sub'))->table('excel')->where("account_number = '" . $user_id . "'" )->order('id desc')->field('product_code')->select();
		$product_code_list = '';
		foreach ($excel as $order_key =>$order_value){  
			$product_code_list .= $excel[$order_key]['product_code'];

			if(!empty($excel[$order_key+1]['product_code']))
				$product_code_list .='，';
		}
		return $product_code_list;
	}
	/*設定會員匯出excel內容*/
	public function set_excel_member_content($target_sheet, $memeber){
		foreach ($memeber as $key =>$value) {	
			$target_index = $key+3;
			
			$this->accountDB()->where('id',$value['id'])->setField('export', 1);

			//修改地址格式
			try{
				if($value['home']){
					$addcods = explode('<fat>',$value['home']);
					
					$city = Db::connect(config('A_sub'))->table('city')->where("AutoNo = '".$addcods[0]."'")->field('Name')->find();
					$town = Db::connect(config('A_sub'))->table('town')->where("AutoNo = '".$addcods[1]."'")->field('Name')->find();
					$post = $addcods[2];
					$otheradd = $addcods[3];
					
					$value['home'] = $post.' '.$city["Name"].$town["Name"].$otheradd;
				}
			}catch (\Exception $e){
				$value['home'] = $value['home'];
			}
	
			$target_sheet -> setCellValue("A".$target_index , '="'.$value['name'].'"');
			$target_sheet -> setCellValue("B".$target_index , '="'.$value['email'].'"');
			$target_sheet -> setCellValue("C".$target_index , "");
			$target_sheet -> setCellValue("D".$target_index , '="'.$value['home'].'"');
			$target_sheet -> setCellValue("E".$target_index , '="'.$value['phone'].'"');
			$target_sheet -> setCellValue("F".$target_index , '="'.$value['tele'].'"');
			if(!empty($value['birthday'])){
				$target_sheet ->setCellValue("G".$target_index , '="'.date('Y/m/d',$value['birthday']).'"');
			}
			$target_sheet -> setCellValue("H".$target_index , '="'.$value['order'].'"');
			$target_sheet -> setCellValue("I".$target_index , '="'.$value['excel'].'"');
		}

		//調整欄寬
		$target_sheet -> getColumnDimension('A') -> setWidth(20);
		$target_sheet -> getColumnDimension('B') -> setWidth(60);
		$target_sheet -> getColumnDimension('C') -> setWidth(30);
		$target_sheet -> getColumnDimension('D') -> setWidth(60);
		$target_sheet -> getColumnDimension('E') -> setWidth(20);
		$target_sheet -> getColumnDimension('F') -> setWidth(20);
		$target_sheet -> getColumnDimension('G') -> setWidth(20);
		$target_sheet -> getColumnDimension('H') -> setWidth(60);	
		$target_sheet -> getColumnDimension('I') -> setWidth(60);

		return $target_sheet;
	}

	/*會員匯入*/
	public function import(){
		//接收檔案
		$files = Request::instance()->file("file");
		$type = explode(".",$_FILES['file']['name']);

		if(!$type[1]== 'xlsx'){
			$this->error("格式錯誤，請上傳Excel檔");
		}
		//儲存檔案
		$info = $files->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel');
		//檔案路徑
		$filename = ROOT_PATH.'public'.DS.'uploads'. DS . 'excel'. DS .$info->getSaveName();


		$objPHPExcel = new \PHPExcel();
		//require_once 'Classes/PHPExcel/Reader/Excel5.php';
		$PHPReader = new \PHPExcel_Reader_Excel2007(); 
		$PHPExcel = $PHPReader->load($filename); 
		$sheet = $PHPExcel->getSheet(0); 
		$allRow = $sheet->getHighestRow(); //取得最大的行號 
		$allColumn = $sheet->getHighestColumn(); //取得最大的列號

		if($allRow > 30000){ $this->error('匯入資料超過3萬筆，請分批操作'); }

		$save_data = [];
		$error_data = [];
		for ($currentRow = 3; $currentRow <= $allRow; $currentRow++) { 
			$data['name'] = trim($PHPExcel->getActiveSheet()->getCell("A" . $currentRow)->getCalculatedValue()); 
			$data['email'] = trim($PHPExcel->getActiveSheet()->getCell("B" . $currentRow)->getCalculatedValue()); 
			$data['pwd'] = trim($PHPExcel->getActiveSheet()->getCell("C" . $currentRow)->getCalculatedValue()); 
			$data['home'] = trim($PHPExcel->getActiveSheet()->getCell("D" . $currentRow)->getCalculatedValue()); 
			$data['phone'] = trim($PHPExcel->getActiveSheet()->getCell("E" . $currentRow)->getCalculatedValue()); 
			$data['tele'] = trim($PHPExcel->getActiveSheet()->getCell("F" . $currentRow)->getCalculatedValue()); 
			$data['birthday'] = trim($PHPExcel->getActiveSheet()->getCell("G" . $currentRow)->getCalculatedValue()); 
			if($data['pwd'] == ""){ /*未輸入密碼*/
				unset($data['pwd']);
			}

			$returnData = MemberInstance::arrange_data_to_db_format($data);
			if($returnData['code']==0){
				array_push($error_data, "<small>帳號：".$data['email']."資料有誤，".$returnData['msg']."</small>");
			}
			
			// 檢查無誤，紀錄要儲存的資料
			array_push($save_data, $returnData['data']);
		}

		if($error_data){ /*有錯誤資料*/
			$error_data = implode("<br>", $error_data);
			$this->error('匯入失敗', $url=null, $error_data, $wait=-1);
		}

		// dump($save_data);
		$MemberInstance = new MemberInstance(0);
		foreach ($save_data as $key => $value) {
			$account = $MemberInstance->get_user_data($addr_change="ori", ['a.email'=>array('eq', $value['email'])] );
			if($account){ /*帳號已存在，視為編輯*/
				unset($value['email']);
				$MemberInstance->update_user_data($value, $cond=['a.id'=>array('eq', $account['id'])], $change_format=false);
			}
			else{ /*帳號不存在，視為新增*/
				$value['status'] = 1;
				$MemberInstance->insert_user_data($value, $change_format=false);
			}
		}
		$this->success('匯入成功');
	}

	/*贈送/收回點數*/
	public function gift() {
		$number = $_POST['number'];
		try{
			$member = $this->accountDB()->where('number = "'.$number.'"')->find();
			
			Db::startTrans();

			$PointRecords = new PointRecords($member['id']);
			$records = $PointRecords->add_records([
				'msg'			=> $_POST['msg'],
				'points'		=> $_POST['point'],
				'belongs_time'	=> time()
			]);

			Db::commit();    

			return json([
				'status' => true,
				'message' => '成功贈送',
			], 200);
		}catch (\Exception $e){
			
			Db::rollback();
			return json([
				'status' => false,
				'message' => $e->getMessage(),
			], 200);
		}
	}
	
	/*添加會員(單個)*/
	public function addMember(){
		$id = 0;
		$newData = Request::instance()->post();
		$newData['status'] = 1;
		
		/*新增會員*/
		$MemberInstance = new MemberInstance(0);
		$returnData = $MemberInstance->insert_user_data($newData);
		if($returnData['code']==0){ $this->error($returnData['msg']); }

		$this->success('新增成功');	
	}
	/*修改會員密碼*/
	public function changePassword() {
		$id = Request::instance()->post('id');
		$data = [
			'password' => Request::instance()->post('password')
		];
		
		/*修改會員*/
		$MemberInstance = new MemberInstance($id);
		$returnData = $MemberInstance->update_user_data($data);
		if($returnData['code']==0){ $this->error($returnData['msg']); }

		$this->success('修改成功');
	}
	/*修改會員資料(密碼外)*/
	public function updateMember(){
		$id = Request::instance()->post('id');
		if(!$id){ $this->error('請提供編輯對象'); }

		$updateData = Request::instance()->post();

		/*修改會員*/
		$MemberInstance = new MemberInstance($id);
		$returnData = $MemberInstance->update_user_data($updateData);
		if($returnData['code']==0){ $this->error($returnData['msg']); }

		$this->success('修改成功');		
	}
}