<?php
namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

use DBtool\DBTextConnecter;
use DBtool\DBFileConnecter;

class Coupon extends MainController {

	private $DBTextConnecter;
	private $DBFileConnecter;
	private $tableName;
	private $poolTableName;

	const PER_PAGE_ROWS = 10;
	const SIMPLE_MODE_PAGINATE = false;
	const COUPON_TYPE = ['虛擬卷', '實體卷'];
	const TRANSFER_TYPE = ['不能轉移', '可以轉移'];
	const ONLINE = ['隱藏', '顯示'];
	const AREA = ['全體', '單一'];

	public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('coupon');
		$this->DBFileConnecter = DBFileConnecter::withTableName('coupon');
		$this->tableName = 'coupon';
		$this->poolTableName = 'coupon_pool';
	}


	public function index() {	
		$searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);

		$rowData = Db::table($this->tableName)
						->order('id desc')->whereOr([
							'title' => ['like', '%'.$searchKey.'%'],
							'number' => ['like', '%'.$searchKey.'%']
						])
						->paginate(
							self::PER_PAGE_ROWS,
							self::SIMPLE_MODE_PAGINATE, 
							[
								'query' => ['searchKey' => $searchKey]
							]
						);

		$rowDataItem = $rowData->items();
		$rowDataItem = array_map(function ($value) {
			$value['sellCount'] = Db::table($this->poolTableName)
									->where('login_time IS NOT NULL')
									->where('owner IS NOT NULL')
									->where('coupon_id = ' . $value['id'])
									->count();

			$value['useCount'] = Db::table($this->poolTableName)
									->where('use_time IS NOT NULL')
									->where('owner IS NOT NULL')
									->where('coupon_id = ' . $value['id'])
									->count();
			return $value;
		}, $rowDataItem);

		$this->assign('rowDataItems', $rowDataItem);		
		$this->assign('coupon', $rowData);

		return $this->fetch('member-discount');
	}


	public function searchTime() {	

		$start = Request::instance()->get('start') ?? '1970-01-01';
        $end = Request::instance()->get('end') ?? '9999-01-01';
        $this->assign('searchKey', '時間：' . $start . '到' . $end);

		$rowData = Db::table($this->tableName)
						->order('id desc')->whereOr([
							'start' => ['between', [strtotime($start), strtotime($end)]],
							'end' => ['between', [strtotime($start), strtotime($end)]]
						])
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
		$rowDataItem = $rowData->items();

		$rowDataItem = array_map(function ($value) {
			$value['sellCount'] = Db::table($this->poolTableName)
									->where('login_time IS NOT NULL')
									->where('owner IS NOT NULL')
									->where('coupon_id = ' . $value['id'])
									->count();

			$value['useCount'] = Db::table($this->poolTableName)
									->where('use_time IS NOT NULL')
									->where('owner IS NOT NULL')
									->where('coupon_id = ' . $value['id'])
									->count();
			return $value;
		}, $rowDataItem);

		$this->assign('rowDataItems', $rowDataItem);		
		$this->assign('coupon', $rowData);

		return $this->fetch('member-discount');
	}


	public function show($id) {
		$singleData = Db::table($this->tableName)->find($id);

		if($singleData == null){
			$this->error('商品不存在');
		}

		$singleData['pool'] = Db::table($this->poolTableName)
								->where('login_time IS NOT NULL')
								->where('owner IS NOT NULL')
								->where('coupon_id = ' . $singleData['id'])
								->select();

		$singleData['pool'] = array_map(function ($value) {
			$value['owner'] = Db::connect(config('main_db'))
										->table('account')
										->field('number')	
										->find($value['owner']);
			return $value;
		}, $singleData['pool']);

		$singleData['sellCount'] = Db::table($this->poolTableName)
									->where('login_time IS NOT NULL')
									->where('owner IS NOT NULL')
									->where('coupon_id = ' . $singleData['id'])
									->count();

		$singleData['useCount'] = Db::table($this->poolTableName)
									->where('use_time IS NOT NULL')
									->where('owner IS NOT NULL')
									->where('coupon_id = ' . $singleData['id'])
									->count();

		$singleData['type'] = self::COUPON_TYPE[$singleData['type']];
		$singleData['transfer'] = self::TRANSFER_TYPE[$singleData['transfer']];
		$singleData['text1_online'] = self::ONLINE[$singleData['text1_online']];
		$singleData['text2_online'] = self::ONLINE[$singleData['text2_online']];
		$singleData['text3_online'] = self::ONLINE[$singleData['text3_online']];
		$singleData['text4_online'] = self::ONLINE[$singleData['text4_online']];

		if ($singleData['area']) {
			$singleData['area_id'] = $productinfo = Db::table('productinfo')->find($singleData['area_id']);

			if (!$singleData['area_id']) {
				$singleData['area_id'] = '已經找不到商品了，請關閉優惠卷';
			} else {
				$singleData['area_id'] = $singleData['area_id']['title'];
			}

		}else{
			$singleData['area_id'] = '';
		}

		$singleData['area'] = self::AREA[$singleData['area']];
		$this->assign('singleData', $singleData);

		return $this->fetch('show');
	}


	public function create() {
		$productinfo = Db::table('productinfo')->field(
			'productinfo.title AS title, productinfo.id AS id'
		)->where(
			'productinfo.online < 2
			AND productinfo.has_price = 1'
		)->order('id desc')->select();

		$this->assign('productinfo', $productinfo);

		return $this->fetch('member-discount-new');
	}


	public function doCreate() {

		$width = 600; $height = 600;
        $newData = Request::instance()->post();

        try{
			if($newData['title'] == ''){
				throw new \Exception("沒有標題");
			}

			$count = Db::table('coupon')->where('number like "'.config('subDeparment').'C'.date('Ymd').'%"')->order('id desc')->find();
			$count = $count ? intval(substr($count['number'],-3)) + 1 : 1;
			if($count < 10){
				$count = '00' . $count;
			}else if($count < 100){
				$count = '0' . $count;
			}

			$newData['number'] = config('subDeparment') . 'C' . date('Ymd') . $count;
			$newData['start'] = strtotime($newData['start']);
			$newData['end'] = strtotime($newData['end']);
			$this->DBTextConnecter->setDataArray($newData);

            $mainId = $this->DBTextConnecter->createTextRow();
            $image = Request::instance()->file('image');

            if($image){
                $DBFileConnecter = new DBFileConnecter();
                $newData['pic'] = 
                    $DBFileConnecter->fixedFileUp($image, 'coupon_' . $mainId, $width, $height);
				$newData['id'] = $mainId;
				$this->DBTextConnecter->setDataArray($newData);
				$this->DBTextConnecter->upTextRow();
			}

			//pool
			$pool = array();
			for ($i=0; $i < $newData['num']; $i++) {
				array_push($pool,[
					'number' => config('subDeparment') . 
								'CP' . $mainId . 
								chr(mt_rand(65, 90)) . 
								chr(mt_rand(65, 90)) . 
								$i . 
								chr(mt_rand(65, 90)) . 
								chr(mt_rand(65, 90)),
					'coupon_id' => $mainId
				]);
			}

			Db::table($this->poolTableName)->insertAll($pool);
		}catch (\Exception $e) {
			$this->dumpException($e);
		}

		$this->success('新增成功', url('coupon/index'));
	}


	public function delete() {
        $id = Request::instance()->get('id');
        try{
            Db::table($this->tableName)->delete($id);
            Db::table($this->poolTableName)->where('coupon_id', $id)->delete();
        } catch (\Exception $e){
            $this->dumpException($e);
        }

        $this->success('刪除成功', url('coupon/index'));
    }


    public function multiDelete() {
        $idList = Request::instance()->post('id');
        try{
            if ($idList) {
                $idList = json_decode($idList);
				Db::table($this->tableName)->delete($idList);
				Db::table($this->poolTableName)->where('coupon_id', 'in', $idList)->delete();
            }
        } catch (\Exception $e){
            $this->dumpException($e);
        }

        $this->success('刪除成功', url('coupon/index'));
    }

	public function dumpUsedExcel($id) {

		$singleData = Db::table($this->tableName)->find($id);

		if($singleData == null){
			$this->error('商品不存在');
		}

		$singleData['pool'] = Db::table($this->poolTableName)
								->where('login_time IS NOT NULL')
								->where('coupon_id = ' . $singleData['id'])
								->select();

		$singleData['pool'] = array_map(function ($value) {
			$value['owner'] = Db::connect(config('main_db'))
										->table('account')
										->field('number')	
										->find($value['owner']);
			return $value;
		}, $singleData['pool']);

		$singleData['sellCount'] = Db::table($this->poolTableName)
									->where('login_time IS NOT NULL')
									->where('coupon_id = ' . $singleData['id'])
									->count();

		$singleData['useCount'] = Db::table($this->poolTableName)
									->where('use_time IS NOT NULL')
									->where('coupon_id = ' . $singleData['id'])
									->count();

		$singleData['type'] = self::COUPON_TYPE[$singleData['type']];
		$singleData['transfer'] = self::TRANSFER_TYPE[$singleData['transfer']];

		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '名稱');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $singleData['title']);
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '品號');
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('D1', $singleData['number']);
        $objPHPExcel->getActiveSheet()->setCellValue('A2', '開始日期');
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('B2', date('Y-m-d', $singleData['start']));
        $objPHPExcel->getActiveSheet()->setCellValue('C2', '結束日期');
		$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('D2', date('Y-m-d', $singleData['end']));
        $objPHPExcel->getActiveSheet()->setCellValue('A3', '折扣方式');
		$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('B3', "折扣".$singleData['discount']."元");
        $objPHPExcel->getActiveSheet()->setCellValue('C3', '使用限制');
		$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('D3', "滿".$singleData['coupon_condition']."元");
        $objPHPExcel->getActiveSheet()->setCellValue('A4', '可否轉移');
		$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('B4', $singleData['transfer']);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', '類型');
		$objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('D4', $singleData['type']);
        $objPHPExcel->getActiveSheet()->setCellValue('A5', '發行張數');
		$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('B5', $singleData['num']);
        $objPHPExcel->getActiveSheet()->setCellValue('C5', '已登入張數');
		$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('D5', $singleData['sellCount']);
        $objPHPExcel->getActiveSheet()->setCellValue('E5', '已回饋張數');
		$objPHPExcel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('F5', $singleData['useCount']);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);

		$objPHPExcel->getActiveSheet()->setCellValue('A7', '優惠券代碼');
		$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B7', '登入日期');
		$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C7', '登入會員');
		$objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D7', '使用日期');
		$objPHPExcel->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);

		$row = 8;
		foreach ($singleData['pool'] as $value) {
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $value['number']);
        	$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, date('Y-m-d', $value['login_time']));
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $value['owner']['number']);
			$value['use_time'] = $value['use_time'] ? date('Y-m-d', $value['use_time']) : '未使用';
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $value['use_time']);
			$row++;
		}

		$PHPExcelWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$filename = $singleData['number'] . " - " . $singleData['title'] . " 使用報表.xls";
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=\"$filename\"");

		$PHPExcelWriter->save('php://output');
	}


	public function dumpPoolExcel($id){

		$singleData = Db::table($this->tableName)->find($id);

		if($singleData == null){
			$this->error('商品不存在');
		}

		$singleData['pool'] = Db::table($this->poolTableName)
								->where('coupon_id = ' . $singleData['id'])
								->select();

		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16);

		$row = 1;
		foreach ($singleData['pool'] as $value) {
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $value['number']);
			$row++;
		}

		$PHPExcelWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$filename = $singleData['number'] . " - " . $singleData['title'] . "編號列表.xls";

		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=\"$filename\"");

		$PHPExcelWriter->save('php://output');
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

}