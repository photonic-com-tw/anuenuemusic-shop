<?php

namespace app\admin\controller;

use app\admin\controller\MainController;
use think\Request;
use think\Db;

//Photonic Class
use controllerInterface\resources\SoleType;
use DBtool\DBTextConnecter;

class Shippingfee extends MainController implements SoleType
{

    private $DBTextConnecter;
    private $resTableName;
    const PER_PAGE_ROWS = 20;
	const SIMPLE_MODE_PAGINATE = false;

    //this resources's frontend use Single Page Web, some CURD method is useless
    public function edit(){}
    public function create(){}

    public function __construct() {
        parent::__construct();
        $this->DBTextConnecter = DBTextConnecter::withTableName('shipping_fee');
        $this->resTableName = 'shipping_fee';
    }

    public function index() {
        $searchKey = Request::instance()->get('searchKey') ?? '';
		$this->assign('searchKey', $searchKey);
        $shipping_fee = Db::table($this->resTableName)
            ->where("name LIKE '%$searchKey%'")
            ->order('order_id')
            ->paginate(
                self::PER_PAGE_ROWS,
                self::SIMPLE_MODE_PAGINATE, 
                [
                    'query' => [
                        'searchKey' => $searchKey
                    ]
                ]
            );

        $this->assign('shipping_fee', $shipping_fee);
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
	public function cellCtrl() {
		try{
			$updateData = Request::instance()->post();

            // 自動更新排序
            if( isset($updateData['order_id']) ){
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

    public function doCreate() {
        $newData = Request::instance()->post();
        try{
            $this->DBTextConnecter->setDataArray($newData);
            $id = $this->DBTextConnecter->createTextRow();

            // 自動更新排序
            $table = $this->resTableName;
            $column = 'order_id';
            $order_num = 0;
            $primary_key = 'id';
            $primary_value = $id;
            parent::auto_change_orders($table, $column, $order_num, $primary_key, $primary_value);

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

    public function update() {
        $newData = Request::instance()->post();

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
		return json($outputData, 200);
    }
}