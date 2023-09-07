<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

//Photonic Class
use controllerInterface\resources\SoleType;
use DBtool\DBTextConnecter;

class Contact extends MainController implements SoleType
{

    private $DBTextConnecter;
    private $typeDBTextConnecter;
    private $resTableName;
    private $typeTableName;
    const PER_PAGE_ROWS = 10;
	const SIMPLE_MODE_PAGINATE = false;

    //this resources cannet edit and create
    public function edit(){}
    public function create(){}
    public function doCreate(){}
    public function update(){}

    public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('contact_log');
        $this->typeDBTextConnecter = DBTextConnecter::withTableName('contact');
        $this->resTableName = 'contact_log';
        $this->typeTableName = 'contact';
    }

	public function index() {
        $searchKey = Request::instance()->get('searchKey') ?? '';
        $searchKey = trim($searchKey);
        $this->assign('searchKey', $searchKey);
        $contact_log = Db::table($this->resTableName)
                        ->where("
                            type LIKE '%$searchKey%' or 
                            phone LIKE '%$searchKey%' or 
                            homephone LIKE '%$searchKey%' or 
                            name LIKE '%$searchKey%' or 
                            email LIKE '%$searchKey%' or 
                            order_number  LIKE '%$searchKey%' 
                        ")
                        ->order('id desc')->paginate(
                            self::PER_PAGE_ROWS,
                            self::SIMPLE_MODE_PAGINATE,
                            [
                                'query' => [
                                    'searchKey' => $searchKey
                                ]
                            ]
                        );
        $contact_type = Db::table($this->typeTableName)->find(1);
        $this->assign('contact_log', $contact_log);
        $this->assign('contact_type', $contact_type);
        $this->assign('empty', '沒有數據');
		return $this->fetch('replay');
    }

    public function searchTime()
    {
        $start = Request::instance()->get('start') ?? '1970-01-01';
        $end = Request::instance()->get('end') ?? '9999-01-01';
        $this->assign('searchKey', '時間：' . $start . '到' . $end);
        $contact_log = Db::table($this->resTableName)
                        ->where("time between '$start' AND '$end'")
                        ->order('id desc')->paginate(
                            self::PER_PAGE_ROWS,
                            self::SIMPLE_MODE_PAGINATE,
                            [
                                'query' => [
                                    'start' => $start,
                                    'end' => $end
                                ]
                            ]
                        );
        $contact_type = Db::table($this->typeTableName)->find(1);
        $this->assign('contact_log', $contact_log);
        $this->assign('contact_type', $contact_type);
        $this->assign('empty', '沒有數據');
		return $this->fetch('replay');
    }

    public function delete() {
        $id = Request::instance()->get('id');
        try{
            Db::table($this->resTableName)->delete($id);
        } catch (\Exception $e){
            $this->dumpException($e);
        }
        $this->success('刪除成功',url('Contact/index'));
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

    /*更改status和新增回覆，直接對JQ post下tp5看得懂得更新指令*/
    public function status() {
        $update = Request::instance()->post();
        try{
            $this->DBTextConnecter->setDataArray($update);
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

    /*更改type，直接對JQ post下tp5看得懂得更新指令*/
    public function type() {
        $update = Request::instance()->post();
        try{
            $this->typeDBTextConnecter->setDataArray($update);
            $this->typeDBTextConnecter->upTextRow();
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
	
	
	public function getCount(){
		try{
			$unprocess = Db::table($this->resTableName)->where("status = '0'")->count();
			$count = Db::table($this->resTableName)->count();
			$outputData = [
				'status' => true,
				'message' => array($unprocess,$count),
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
