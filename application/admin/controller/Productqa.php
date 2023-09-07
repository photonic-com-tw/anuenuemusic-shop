<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

//Photonic Class
use controllerInterface\resources\SoleType;
use DBtool\DBTextConnecter;

class Productqa extends MainController
{

    private $DBTextConnecter;
    private $resTableName;
    const PER_PAGE_ROWS = 2;
	const SIMPLE_MODE_PAGINATE = false;

    //this resources's frontend use Single Page Web, some CURD method is useless
    public function edit(){}
    public function create(){}

    public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('product_qa');
        $this->resTableName = 'product_qa';
    }

    public function index() {
        $searchKey = Request::instance()->get('searchKey') ?? '';
        $mainDb = config('main_db');
		$this->assign('searchKey', $searchKey);
        $qa = Db::table($this->resTableName)->alias('pq')
            ->join('productinfo pi','pq.prod_id = pi.id')
            ->join($mainDb.'.account acnt','pq.uid = acnt.id')
            ->where("pi.title LIKE '%$searchKey%' OR acnt.name LIKE '%$searchKey%'")
            ->order('q_datetime')
            ->paginate(
                self::PER_PAGE_ROWS,
                self::SIMPLE_MODE_PAGINATE, 
                [
                    'query' => [
                        'searchKey' => $searchKey
                    ]
                ]
            );

        $this->assign('qa', $qa);
        $this->assign('useNg','true');
		return $this->fetch('index');
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
    
    /*AJAX*/
    public function update() {
        $newData = Request::instance()->post();


        $updData = [
            'prod_a'    => $newData['prod_a'],
            'a_datetime'=> date("Y-m-d H:i:s")
        ];


       Db::table($this->resTableName)->where('prod_qa_id',$newData['prod_qa_id'])->update($updData);

		return json($newData, 200);
    }

    public function mailTest(){
        $to="photonicsam123@gmail.com";
        $subject="這是封測試郵件";
        $message="這是測試內容";
        $headers = "From: photonic.service@photonic.com.tw"."\r\n";

        mail($to,$subject,$message,$headers);//調用 mail() 函式將此封信件發送出去
        echo "mail test";
    }

}