<?php
namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

use GuzzleHttp\Client;

class Examination extends MainController{
	public $control_register;
	private $DBTextConnecter;
	private $DBFileConnecter;
	const PER_PAGE_ROWS = 20;
	const SIMPLE_MODE_PAGINATE = false;
	const AREA_CODE = [
						'台北'=>'01',
						'桃園'=>'02',
						'竹苗'=>'03',
						'台中'=>'04',
						'彰化'=>'05',
						'南投'=>'06',
						'草屯'=>'07',
						'埔里'=>'08',
						'普台'=>'09',
						'斗六'=>'10',
						'嘉義'=>'11',
						'台南'=>'12',
						'高雄'=>'13',
						'花蓮'=>'14',
						'台東'=>'15'
					  ];
	
	public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('examinee_info');
        $this->DBFileConnecter = DBFileConnecter::withTableName('examinee_info');

        // 是否啟用報名功能
        $this->control_register = Db::connect('main_db')->table('excel')->field('value1')->find(14)['value1'];//照片數量
        if($this->control_register==0){$this->error('未啟用此功能');}
        $this->assign('control_register', $this->control_register);

	}

	public function examination_room($id){
		// dump($id);
		$each_exam = $this->print_ex($id);
		// exit;
		$this->assign('id', $id);

		$title = Db::table('productinfo info')->field('info.title as info_title')->find($id);
		$this->assign('title', $title);

		$this_exam_area = Db::table('productinfo_type')->field('id,title')->where("product_id = '".$id."'")->select();
		$this->assign('this_exam_area', $this_exam_area);

		return $this->fetch('examination-room');
	}


	public function excel($id){
		$each_exam = $this->print_ex($id);

		$title = Db::table('productinfo info')->field('info.title as info_title')->find($id);

		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '訂單編號');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '報名考場');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '報名學校');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '報名班級');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '學生姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '學生身分證');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '報名日期');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '家長姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', '家長聯絡手機');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', '家長聯絡email');
		$objPHPExcel->getActiveSheet()->setCellValue('K1', '家長聯絡室內電話');
		$objPHPExcel->getActiveSheet()->setCellValue('L1', '通訊地址');
		$objPHPExcel->getActiveSheet()->setCellValue('M1', '繳費狀況');
		$objPHPExcel->getActiveSheet()->setCellValue('N1', '備註');
		$objPHPExcel->getActiveSheet()->setCellValue('O1', '考試地點');
		$objPHPExcel->getActiveSheet()->setCellValue('P1', '教室');
		$objPHPExcel->getActiveSheet()->setCellValue('Q1', '座位');
		$objPHPExcel->getActiveSheet()->setCellValue('R1', '淮考證');
		$objPHPExcel->getActiveSheet()->setCellValue('S1', '成績');
		$objPHPExcel->getActiveSheet()->setCellValue('T1', '落點');
		$objPHPExcel->getActiveSheet()->setCellValue('U1', '成績備註');	
		$objPHPExcel->getActiveSheet()->setCellValue('V1', '回傳值');			

		$row = 2;
		foreach ($each_exam as $value) {
			$rs = RECEIPTS_STATE($value['receipts_state']);
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $value['order_number']);
        	$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $value['title']);			
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $value['examinee_school']);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $value['examinee_class']);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $value['examinee_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $value['examinee_id']);
        	$objPHPExcel->getActiveSheet()->setCellValue('G' . $row, date('Y-m-d', $value['create_time']));	
			$objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $value['parents_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('I' . $row, '="'.$value['parents_phone'].'"');
			$objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $value['parents_mail']);
			$objPHPExcel->getActiveSheet()->setCellValue('K' . $row, '="'.$value['parents_tel'].'"');
			$objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $value['parents_add']);
			$objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $rs);
			$objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $value['examinee_note']);
			$objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $value['exam_school']);
			$objPHPExcel->getActiveSheet()->setCellValue('P' . $row, '="'.$value['examinee_room'].'"');
			$objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, '="'.$value['examinee_site'].'"');	
			$objPHPExcel->getActiveSheet()->setCellValue('R' . $row, '="'.$value['examinee_ticket'].'"');
			$objPHPExcel->getActiveSheet()->setCellValue('S' . $row, '="'.$value['grade'].'"');	
			$objPHPExcel->getActiveSheet()->setCellValue('T' . $row, '="'.$value['grade_point'].'"');
			$objPHPExcel->getActiveSheet()->setCellValue('U' . $row, $value['grade_note']);	
			$objPHPExcel->getActiveSheet()->setCellValue('V' . $row, $value['id']);	
			$row++;
		}
		
		$PHPExcelWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$filename = $title['info_title']." 報表資料.xlsx";
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		$PHPExcelWriter->save('php://output');
	}

	function print_ex($id){

		$search ='';
		$order_time = '';
		$receipts_state = "";

		//搜尋訂單編號
		if(!empty($_GET['order_number'])){
			$order = Db::connect(config('main_db'))->field('id')->table('orderform')->where("order_number  = '".$_GET['order_number']."'")->select();
			if(!empty($order)){
				$search .= " and einfo.order_id = '".$order[0]['id']."' ";
			}else{
				$search .= " and einfo.order_id = '0' ";
			}
		}

		//排除取消訂單
		$order_id_cancel = [];
		$order_cancel = Db::connect(config('main_db'))->field('id')->table('orderform')->where("status not in ('New', 'Complete')")->select();
		if(count($order_cancel)>0){
			foreach ($order_cancel as $key => $value) {
				array_push($order_id_cancel, $value['id']);
			}
			// dump($order_id_cancel);
			$search .=  (" and einfo.order_id not in (".join(",",$order_id_cancel).")");
		}

		if(!empty($_GET['searchKey'])){
			foreach($_GET['searchKey'] as $k => $v){
				if($v == '')
					continue;

				switch ($k) {
				  case 0:
					$search .=" and einfo.type_id = '".$v."'";
					break;

				  case 1:
					$T = mb_strpos($v,"臺",0,"utf-8");
					$t = mb_strpos($v,"台",0,"utf-8");
					if($T >=0 || $t >=0){
						$search .=" and (einfo.examinee_school like '%".str_replace("台","臺",$v)."%' ";
						$search .="  or  einfo.examinee_school like '%".str_replace("臺","台",$v)."%' )";
					}else{
						$search .=" and einfo.examinee_school like '%".$v."%'";
					}
					break;

				  case 2:
					$ex_array = ['parents_name','parents_phone','parents_mail','parents_tel','parents_add','examinee_school','examinee_class','examinee_name','examinee_birthday','examinee_id','examinee_note','exam_school','examinee_room','examinee_site','examinee_ticket','grade','grade_point','grade_note'];
					$or = '';
					foreach($ex_array as $ek => $ev){
						$or .="einfo.".$ev." like '%".str_replace("台","臺",$v)."%' or ";
						$or .="einfo.".$ev." like '%".str_replace("臺","台",$v)."%' ";

						if(!empty($ex_array[$ek+1]))
							$or .= ' or ';
					}
					$search .=" and ( ".$or." ) ";
					break;

				  case 3:
					$order_time = $v;
					break;

				  case 4:
					$receipts_state = $v;
					break;

				case 5:
					$T = mb_strpos($v,"臺",0,"utf-8");
					$t = mb_strpos($v,"台",0,"utf-8");
					if($T >=0 || $t >=0){
						$search .=" and (einfo.exam_school like '%".str_replace("台","臺",$v)."%' ";
						$search .="  or  einfo.exam_school like '%".str_replace("臺","台",$v)."%' )";
					}else{
						$search .=" and einfo.exam_school like '%".$v."%'";
					}
					break;

				  case 6:
					$search .=" and einfo.examinee_ticket like '%".$v."%'";
					break;
				}
			}
		}

		$each_exam = Db::table('examinee_info einfo')
			->field('einfo.*,type.title')
			->where("prod_id= '".$id."' {$search} ")
			->join('productinfo_type type','einfo.type_id = type.id ')
			->select();
		// dump("prod_id= '".$id."' {$search} ");exit;
		
		if(!empty($each_exam)){

			//取得訂單資料
			$get_order_info = '';
			foreach($each_exam as $key => $v){
				$get_order_info .= $v['order_id'];

				if(!empty($each_exam[$key+1]))
					$get_order_info .= ',';
			}

			$order_info = Db::connect(config('main_db'))->field('id,order_number,create_time,receipts_state')->table('orderform')->where("id in(".$get_order_info.")")->select();

			$show_order_info = [];
			foreach($order_info as $key => $v){
				$show_order_info[$v['id']] = array($v['order_number'],$v['create_time'],$v['receipts_state']);
			}

			//取得訂單資料
			foreach($each_exam as $key => $v){
				$each_exam[$key]['order_number'] = $show_order_info[$v['order_id']][0];
				$each_exam[$key]['create_time'] = $show_order_info[$v['order_id']][1];
				$each_exam[$key]['receipts_state'] = $show_order_info[$v['order_id']][2];

				if($receipts_state != ''){
					if($show_order_info[$v['order_id']][2] != $receipts_state){
						unset($each_exam[$key]);
					}	
				}

				if($order_time != ''){
					if(!($show_order_info[$v['order_id']][1] >= strtotime($order_time) and $show_order_info[$v['order_id']][1] <= strtotime($order_time)+84600)){
						unset($each_exam[$key]);
					}	
				}
			}		
		}

		$this->assign('each_exam', $each_exam);
		return $each_exam;
	}


	public function personal(){
		$personal = Db::table('examinee_info')->find($_POST['id']);

		$this->assign('p', $personal);
	
		return $this->fetch('personal');
	}


	public function personal_update(){
		
		$id = $_POST['id'];
		unset($_POST['id']);	

		try{
			$data = Request::instance()->post();
			Db::table('examinee_info')->where(['id'=>$id])->update($_POST);
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


	public function update_examinee(){
		try{
			$data = Request::instance()->post();
			Db::table('examinee_info')->where(['id'=>$data['id']])->update([$data['col']=>$data['ticket']]);
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



	public function examinee_ticket($prod_id){

		$output = Db::table('examinee_info ex')
					->field('ex.id,ex.examinee_id,ex.examinee_room,ex.examinee_site,type.title')
					->where("prod_id = '".$prod_id."' and (exam_school IS NOT NULL and exam_school !='') and (examinee_room IS NOT NULL and examinee_room !='') and (examinee_site IS NOT NULL and examinee_site !='') and (examinee_ticket ='0' or examinee_ticket IS NULL)")
					->join("productinfo_type type","ex.type_id = type.id")
					->select();					

		foreach($output as $k => $v){
			$examinee_ticket = '';

			if(isset( self::AREA_CODE[mb_substr( $v['title'],0,2,"utf-8")] ) ){
				$examinee_ticket .= self::AREA_CODE[mb_substr( $v['title'],0,2,"utf-8")];
			}else{
				$examinee_ticket .= '00';
			}
			$examinee_ticket .= str_pad($v['examinee_room'],2,'0',STR_PAD_LEFT);
			$examinee_ticket .= str_pad($v['examinee_site'],2,'0',STR_PAD_LEFT);
			$examinee_ticket .= mb_substr( $v['examinee_id'],-4,4,"utf-8");
			if(strlen($examinee_ticket) == 10){
				Db::table('examinee_info')->where('id', $v['id'])->update(['examinee_ticket' => $examinee_ticket]);
			}else{
				Db::table('examinee_info')->where('id', $v['id'])->update(['examinee_ticket' => '0']);
			}
		}
		$this->redirect('examination/examination_room',['id'=>$prod_id]);
	}


	public function examinee_grade($prod_id){		
		$output = Db::table('examinee_info')
					->where("prod_id = '".$prod_id."' and grade != ''  and grade IS NOT NULL and grade_show ='0' ")
					->update(['grade_show' => '1']);
		$this->redirect('examination/examination_room',['id'=>$prod_id]);
	}	

	public function Import($id) {

	//接收檔案
	$files = Request::instance()->file("file");
	$type = explode(".",$_FILES['file']['name']);

		if($type[1]== 'xlsx'){

			//儲存檔案
			$info = $files->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'examination');

			//檔案路徑
			$filename = ROOT_PATH.'public'.DS.'uploads'. DS . 'examination'. DS .$info->getSaveName();

			self::read_excel($filename,$id);
		}else{
			echo "<script>alert('警告：格式錯誤'); location.href = '".url('examination/examination_room',['id'=>$id])."';</script>";
		}
	}

	function read_excel($filename,$id){
		$objPHPExcel = new \PHPExcel();
		//require_once 'Classes/PHPExcel/Reader/Excel5.php';

		$PHPReader = new \PHPExcel_Reader_Excel2007(); 
		$PHPExcel = $PHPReader->load($filename); 
		$sheet = $PHPExcel->getSheet(0); 

		$allRow = $sheet->getHighestRow(); //取得最大的行號 
		$allColumn = $sheet->getHighestColumn(); //取得最大的列號 

		if($allRow <= 30001){
			$jub = '';
			for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) { 
				$up_id = trim($PHPExcel->getActiveSheet()->getCell("V" . $currentRow)->getValue()); 
				$jub .= $up_id;

				$A = trim($PHPExcel->getActiveSheet()->getCell("A" . $currentRow)->getValue()); 
				if($A == '')break;
				
				$B = $PHPExcel->getActiveSheet()->getCell("A" . ($currentRow+1))->getValue(); 
				if($B != '')
					$jub .= ',';
			}

			$with = Db::table('examinee_info')->field('id,examinee_ticket,grade_show')->where("id IN (".$jub.")")->select();
			$with2 = [];
			foreach ($with as $kk => $vv) { 
				$with2[$vv['id']] = $vv;
			}

			for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) { 
				$A = trim($PHPExcel->getActiveSheet()->getCell("A" . $currentRow)->getValue()); 
				if($A == '')break;

				$up_id = trim($PHPExcel->getActiveSheet()->getCell("V" . $currentRow)->getValue()); 
				$data['examinee_note'] = trim($PHPExcel->getActiveSheet()->getCell("N" . $currentRow)->getValue()); 
				//$data['examinee_ticket'] = trim($PHPExcel->getActiveSheet()->getCell("R" . $currentRow)->getValue()); 

				if($with2[$up_id]['grade_show'] == '0' ){
					$data['grade'] = trim($PHPExcel->getActiveSheet()->getCell("S" . $currentRow)->getValue()); 
					$data['grade_point'] = trim($PHPExcel->getActiveSheet()->getCell("T" . $currentRow)->getValue()); 
					$data['grade_note'] = trim($PHPExcel->getActiveSheet()->getCell("U" . $currentRow)->getValue()); 
				}

				if($with2[$up_id]['examinee_ticket'] == '' || empty($with2[$up_id]['examinee_ticket'])){
					$data['exam_school'] = trim($PHPExcel->getActiveSheet()->getCell("O" . $currentRow)->getValue()); 
					$data['examinee_room'] = trim($PHPExcel->getActiveSheet()->getCell("P" . $currentRow)->getValue()); 
					$data['examinee_site'] = trim($PHPExcel->getActiveSheet()->getCell("Q" . $currentRow)->getValue()); 

					/*
					if($data['examinee_room'] != '' && $data['examinee_site'] != ''){
					$title = trim($PHPExcel->getActiveSheet()->getCell("B" . $currentRow)->getValue()); 
					$examinee_id = trim($PHPExcel->getActiveSheet()->getCell("F" . $currentRow)->getValue()); 
					$data['examinee_ticket'] = '';
					$data['examinee_ticket'] .= self::AREA_CODE[mb_substr( $title,0,2,"utf-8")];
					$data['examinee_ticket'] .= str_pad($data['examinee_room'],2,'0',STR_PAD_LEFT);
					$data['examinee_ticket'] .= str_pad($data['examinee_site'],2,'0',STR_PAD_LEFT);
					$data['examinee_ticket'] .= mb_substr( $examinee_id,-4,4,"utf-8");
					}*/
				}

				Db::table('examinee_info')->where('id',$up_id)->update($data);
				unset($data);
			}

			echo "<script>alert('匯入成功'); location.href = '".url('examination/examination_room',['id'=>$id])."';</script>";
		}else{
			echo "<script>alert('匯入資料超過3萬筆'); location.href = '".url('examination/examination_room',['id'=>$id])."';</script>";
		}
	}


	public function delete() {
        $id = Request::instance()->get('id');

        try{
			Db::table('examinee_info')->delete($id);
			Db::table('expiring_product')->where('product_id', $id)->setField('product_id', 0);
			Db::table('hot_product')->where('product_id', $id)->setField('product_id', 0);
			Db::table('recommend_product')->where('product_id', $id)->setField('product_id', 0);
			Db::table('spe_price_product')->where('product_id', $id)->setField('product_id', 0);
			Db::table('productinfo_type')->where('product_id', $id)->delete();
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
				Db::table('examinee_info')->delete($idList);
				Db::table('expiring_product')->where('product_id', 'in', $idList)->setField('product_id', 0);
				Db::table('hot_product')->where('product_id', 'in', $idList)->setField('product_id', 0);
				Db::table('recommend_product')->where('product_id', 'in', $idList)->setField('product_id', 0);
				Db::table('spe_price_product')->where('product_id', 'in', $idList)->setField('product_id', 0);
				Db::table('productinfo_type')->where('product_id', 'in', $idList)->delete();
			}
        } catch (\Exception $e){
            $this->dumpException($e);
        }

        $this->success('刪除成功');
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


	public function addToIndexADV($privateId = null, $privateTableName = null) {
		$id = $privateId ?? Request::instance()->post('id');
		$tableName = $privateTableName ?? Request::instance()->post('tableName');
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


	public function cellCtrlFromType() {
		try{
			$updateData = Request::instance()->post();
			$DBTextConnecter = DBTextConnecter::withTableName('productinfo_type');
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


	public function categories() {

		$categories = $_POST['categories'];
		$typeinfo = Db::table('typeinfo')
				->field('typeinfo.id AS id,
						typeinfo.title AS title')
				->where('typeinfo.online = 1 AND typeinfo.parent_id = '.$categories)
				->order('id desc')->select();

		$data = [];
		$arrary = array();
		foreach ($typeinfo as $item) {
			$data['id'] = $item['id'];
			$data['title'] = $item['title'];
			array_push($arrary, $data);
		}

		return json($arrary, 200);
	}

	public function types() {
		$types = $_POST['types'];
		$productinfo = Db::table('examinee_info')
				->field('productinfo.product_id AS id,
						productinfo.title AS title')
				->where('productinfo.online = 1 AND productinfo.parent_id = '.$types)
				->order('id desc')->select();

		$data = [];
		$arrary = array();
		foreach ($productinfo as $item) {
			$data['id'] = $item['id'];
			$data['title'] = $item['title'];
			array_push($arrary, $data);
		}

		return json($arrary, 200);
	}


	public function re_categories(){
		$re_categories = $_POST['re_categories'];
		if(!empty($re_categories)){
			$productinfo = Db::table('examinee_info')
					->field('typeinfo.parent_id AS type_parent_id, productinfo.parent_id AS parent_id')
					->where('productinfo.online = 1 AND productinfo.product_id = '.$re_categories)
					->join('typeinfo', 'productinfo.parent_id = typeinfo.id')
					->select();

			$arrary = array($productinfo[0]['type_parent_id'],$productinfo[0]['parent_id']);
			return json($arrary, 200);
		}

	}

}