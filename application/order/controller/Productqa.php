<?php

namespace app\order\controller;

use app\order\controller\MainController;
use think\Request;
use think\Db;
use think\Validate;
use think\Env;

use app\index\controller\PublicController;

class Productqa extends MainController
{

	private $resTableName;
	const PER_PAGE_ROWS = 10;
	const SIMPLE_MODE_PAGINATE = false;

    public function __construct() {
        parent::__construct();
		$this->resTableName = 'product_qa';
    }


	public function index() {
		$searchKey = Request::instance()->get('searchKey') ?? '';

		$this->assign('searchKey', $searchKey);
		$qa = Db::connect('main_db')->table($this->resTableName)->alias('pq')
			->join('account acnt','pq.uid = acnt.id')
            ->where("pq.prod_addr LIKE '%$searchKey%' OR acnt.name LIKE '%$searchKey%'")
			->order('q_datetime desc')
			->paginate(
				self::PER_PAGE_ROWS,
				self::SIMPLE_MODE_PAGINATE, 
				[
					'query' => [
						'searchKey' => $searchKey
					]
				]
			)->each(function($item, $key)use(&$coupon_button, &$act_button){
				if ($item['prod_a']){
	                $item['new'] = false;
	            }else{
	                $item['new'] = true;
	            }
				return $item;
			});

		$this->assign('qa', $qa);
		return $this->fetch('index');
	}

	public function delete() {
		$id = Request::instance()->get('id');
		try{
			Db::connect('main_db')->table($this->resTableName)->delete($id);
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
				Db::connect('main_db')->table($this->resTableName)->delete($idList);
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

		$productQa = Db::connect('main_db')->table($this->resTableName)->where('prod_qa_id',$newData['prod_qa_id'])->find();

		Db::connect('main_db')->table($this->resTableName)->where('prod_qa_id',$newData['prod_qa_id'])->update($updData);

		$user = Db::connect('main_db')->table('account')->where('id',$newData['uid'])->find();

		////////////
        /// mail
        ////////////
		$globalMailData = PublicController::getMailData($productQa['site_name']);
        $message    = "親愛的顧客您好:\n\n您的商品提問已被回覆\n回覆內容：".$newData['prod_a']."\n產品網址:".$productQa['prod_addr'];

        PublicController::Mail_Send($message,'client',$user['email'],"產品問答回覆",$config_db=$productQa['site_name']);

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