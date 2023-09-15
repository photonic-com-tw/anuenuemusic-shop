<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use think\Env;

class Ajax extends PublicController
{
    public function product() {
		$productMenu = Db::table('product')
						->field('title, id,pic_icon as pic')
						->order('order_id asc, id asc')
						->where('online = 1')
						->select();
		//dump($productMenu);exit;				

		foreach ($productMenu as $key => $vo) {
			if(empty($_GET['tag'])){		
				$productMenu[$key]['subType'] =
				Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.parent_id = ' . $vo['id'] . "
								AND typeinfo.branch_id = 0
								AND typeinfo.online = 1
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();
				continue;
			}
		
			if( $_GET['tag'] == $vo['id']){
				$productMenu[$key]['title'] = Db::table('typeinfo')->field('typeinfo.title')->find( $_GET['id'])['title'];
				$productMenu[$key]['t_id'] = Db::table('typeinfo')->field('typeinfo.id as t_id')->find( $_GET['id'])['t_id'];
				unset($productMenu[$key]['pic']);

				$productMenu[$key]['subType'] =
					Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.parent_id = ' . $_GET['id'] . "
								AND typeinfo.online = 1
								AND typeinfo.branch_id = 0
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();	
			}else{

				$productMenu[$key]['subType'] =
					Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.parent_id = ' . $vo['id'] . "
								AND typeinfo.online = 1
								AND typeinfo.branch_id = 0
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();
			}

			if(empty($productMenu[$key]['subType'])){
				$productMenu[$key]['subType'] =
					Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.branch_id = ' . $_GET['id'] . "
								AND typeinfo.online = 1
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();
			}

			foreach ($productMenu[$key]['subType'] as $key2 => $vo2) {
				if(mb_strlen($productMenu[$key]['subType'][$key2]["title"], 'utf8') > 15)
					$productMenu[$key]['subType'][$key2]["title"] = mb_substr($productMenu[$key]['subType'][$key2]["title"],0,15,'utf8').'…';
			}
		}

		$actCond = ['online'=>1];
		$act = DB::table('act')->where($actCond)->select();
		if ($act){
		    foreach ($act as $actKey => $actValue){
                $act[$actKey]['title'] = $actValue['name'];
            }
            $productMenu[] = ['title'=>LANG_MENU['優惠專區'],'id'=>0,'subType'=>$act];
        }


		// $onlineactCond = ['online'=>1];
		// $onlineact = DB::table('experience')->where($onlineactCond)->select();
		// if ($onlineact){
		//     foreach ($onlineact as $actKey => $actValue){
        //         $onlineact[$actKey]['title'] = $actValue['title'];
        //     }
        //     $productMenu[] = ['title'=>'活動折扣區','id'=>0,'subType'=>$onlineact];
        // }

		return json($productMenu, 200);
    }

    public function product2() {
		$productMenu = Db::table('product')
						->field('title, id')
						->order('order_id asc, id asc')
						->where('online = 1')
						->select();
		foreach ($productMenu as $key => $vo) {
			$productMenu[$key]['subType'] =
				Db::table('typeinfo')
				->field('typeinfo.title, typeinfo.id')
				->where('typeinfo.parent_id = ' . $vo['id'] . "
							AND typeinfo.online = 1
							AND typeinfo.branch_id = 0
							AND (
								typeinfo.end <= 0
								 OR
								 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
							)")
				->order('typeinfo.order_id')
				//->limit(4)
				->select();

			foreach ($productMenu[$key]['subType'] as $key2 => $vo2) {
				if(mb_strlen($productMenu[$key]['subType'][$key2]["title"], 'utf8') > 15)
					$productMenu[$key]['subType'][$key2]["title"] = mb_substr($productMenu[$key]['subType'][$key2]["title"],0,15,'utf8').'…';

				$productMenu[$key]['subType'][$key2]['subType'] = Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.branch_id = ' . $vo2['id'] . "
								AND typeinfo.online = 1
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();
			}
		}

        return json($productMenu, 200);
    }

	public function product_phone() {
		$productMenu = Db::table('product')
						->field('title, id,pic_icon as pic')
						->order('order_id asc, id asc')
						->where("online = 1 and id = '".$_GET['tag']."' ")
						->select();
		//dump($productMenu);exit;		

		foreach ($productMenu as $key => $vo) {
			if(empty($_GET['tag'])){
				$productMenu[$key]['subType'] =
					Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.parent_id = ' . $vo['id'] . "
								AND typeinfo.branch_id = 0
								AND typeinfo.online = 1
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();
				continue;
			}

			if( $_GET['tag'] == $vo['id']){
				$productMenu[$key]['title'] = Db::table('typeinfo')->field('typeinfo.title')->find( $_GET['id'])['title'];
				$productMenu[$key]['t_id'] = Db::table('typeinfo')->field('typeinfo.id as t_id')->find( $_GET['id'])['t_id'];
				$productMenu[$key]['subType'] =
					Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.parent_id = ' . $_GET['id'] . "
								AND typeinfo.online = 1
								AND typeinfo.branch_id = 0
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();
			}else{
				$productMenu[$key]['subType'] =
					Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.parent_id = ' . $vo['id'] . "
								AND typeinfo.online = 1
								AND typeinfo.branch_id = 0
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();
			}

			if(empty($productMenu[$key]['subType'])){
				$productMenu[$key]['subType'] =
				Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.branch_id = ' . $_GET['id'] . "
								AND typeinfo.online = 1
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();
			}

			foreach ($productMenu[$key]['subType'] as $key2 => $vo2) {
				if(mb_strlen($productMenu[$key]['subType'][$key2]["title"], 'utf8') > 15){
					$productMenu[$key]['subType'][$key2]["title"] = mb_substr($productMenu[$key]['subType'][$key2]["title"],0,15,'utf8').'…';
				}

				$productMenu[$key]['subType'][$key2]['subType'] = Db::table('typeinfo')
					->field('typeinfo.title, typeinfo.id')
					->where('typeinfo.branch_id = ' . $vo2['id'] . "
								AND typeinfo.online = 1
								AND (
									typeinfo.end <= 0
									 OR
									 (typeinfo.start < " . time() . " AND typeinfo.end > " . time() . ")
								)")
					->order('typeinfo.order_id')
					->select();
			}
		}

		return json($productMenu, 200);
    }

    public function productALL() {

		$productMenu = Db::table('product')
						->field('title, id')
						->order('order_id asc, id asc')
						->select();

		foreach ($productMenu as $key => $vo) {
			$productMenu[$key]['subType'] =
			Db::table('typeinfo')
				->field('typeinfo.title, typeinfo.id')
				->where('typeinfo.parent_id = ' . $vo['id'])
				->order('typeinfo.order_id')
        		// ->limit(4)
				->select();
		}
		foreach ($productMenu[0]['subType'] as $key => $vo) {
			if(mb_strlen($productMenu[0]['subType'][$key]["title"], 'utf8') > 15)
				$productMenu[0]['subType'][$key]["title"] = mb_substr($productMenu[0]['subType'][$key]["title"],0,15,'utf8').'…';
		}

        return json($productMenu, 200);
    }

	public function newslink() {
		$newspage = Request::instance()->get('newspage');
		$outputData = Db::table('news')->field('id, title, time')->order('orders asc,time desc')->where('online = 1')->limit(3)->select();

		foreach ($outputData as $key => $value) {
			$outputData[$key]['time'] = date("Y-m-d", strtotime($outputData[$key]['time']) );
		}
		return json($outputData, 200);
    }

	public function chpwd() {
		$id = Request::instance()->post('id');
		$pwd = Request::instance()->post('pwd');
		$Data = Db::connect(config('main_db'))->table('account')->field('pwd')->find($id);
		if($Data['pwd'] == md5($pwd)){
			$outputData = [
				'status' => true
			];
			return json($outputData, 200);
		}
		$outputData = [
			'status' => false
		];
		return json($outputData, 200);
	}

	public function ckaccount() {
		$email = Request::instance()->post('email');
		$where = [
			'email' => $email
		];
		$adminData = Db::connect(config('main_db'))->table('account')->where($where)->limit(1)->select();
		ob_clean();
		if($adminData){
			return false;
		}
		return true;
	}

	/*商品問答功能*/
    public function prodAllQa(){
        $siteName  = Env::get('web.site_name');
        $prod_id = $_POST['prodInfoId'];
        $cond = [
            'site_name' => $siteName,
            'prod_id'   => $prod_id,
        ];
        // dump($cond);exit;
        $resData =  DB::connect('main_db')->name('product_qa')->where($cond)->order('prod_qa_id desc')->select();
        foreach ($resData as $key => $value) {
        	$resData[$key]['q_datetime'] = $value['q_datetime'] ? date('Y-m-d', strtotime($value['q_datetime'])) : "";
        	$resData[$key]['a_datetime'] = $value['a_datetime'] ? date('Y-m-d', strtotime($value['a_datetime'])) : "";
        }
        $data = ['prodQa' => $resData];

        return json($data, 200);
    }
	public function prodQueCreate(){
        $prod_q 	= Request::instance()->post('prodQ');
        if(!$prod_q)$this->error('請填寫詢問內容');

        $siteName  = Env::get('web.site_name');
        $siteAddr  = Env::get('web.site_addr');

        $uid    	= session('user.id');
        $prod_id	= Request::instance()->post('prodInfoId');
        $q_datetime = date('Y-m-d h:i:s');

        $insData = array(
                    'uid'       => $uid,
                    'prod_id'   => $prod_id,
                    'prod_q'    => $prod_q,
                    'q_datetime'=> $q_datetime,
                	'site_name' => $siteName,
                	'prod_addr' => $siteAddr.'index/product/productinfo.html?id='.$prod_id);
        DB::connect('main_db')->name('product_qa')->insert($insData);

        $user = DB::connect('main_db')->name('account')->where(['id'=>$uid])->find();
        $data = [
            'status'    => '200'
        ];

        $globalMailData = parent::getMailData();

        $subject= "客戶產品發問";
        $message= $prod_q;
        $headers= "From: ". $user['email']."\r\n";
		$mailBody = "
			<html>
				<head></head>
				<body>
					<div>
						".$prod_q."<br>
						<br><br>
						<br><br>
					</div>

					<center>
					</center>

					<div>
						". $globalMailData['system_email']['product_qa'] ."
					</div>

					<div style='color:red;'>
					≡ 此信件為系統自動發送，請勿直接回覆 ≡
					</div>
				</body>
			</html>
		";

		$mail_return = parent::Mail_Send($mailBody,'admin','',"客戶產品發問");

        echo json_encode($data);
    }

    /*關閉側邊廣告*/
    public function closeAdSide(){
    	Session('closeAdSide','true');
    	// $closeAdSide = Session::get('closeAdSide');
    	// echo $closeAdSide;
	}

	/*商品加入/取消人氣*/
	public function love_record()
	{
		$result = $this->deal_record('product_love');
		return $result;
	}
	/*商品加入/取消我的收藏*/
	public function store_record()
	{
		$result = $this->deal_record('product_store');
		return $result;
	}
	/*依給定資料表處理紀錄*/
	private function deal_record($tableName){
		$prodInfoId = isset($_POST['prodInfoId']) ? $_POST['prodInfoId'] : 0;
		if(!$prodInfoId){ $this->error(LANG_MENU['資訊不足']); } /*請提供商品id*/
		$status = isset($_POST['status']) ? $_POST['status'] : null;
		if($status===null){ $this->error(LANG_MENU['資訊不足']); } /*請提供收藏狀態*/
		$user_id = Session('user.id');
		if(!$user_id){ $this->error(LANG_MENU['請先登入會員']); }

		if($status==0){ /*取消*/
			Db::table($tableName)->where('product_id', $prodInfoId)->where('user_id', $user_id)->delete();
		}else{ /*加入*/
			$has_loved = Db::table($tableName)->where('product_id', $prodInfoId)->where('user_id', $user_id)->select();
			if(!$has_loved){
				Db::table($tableName)->insert([
					'user_id' => $user_id,
					'product_id' => $prodInfoId,
				]);
			}
		}

		$love_num = Db::table($tableName)->where('product_id', $prodInfoId)->count();
		$this->success($love_num);
	}

	/*展示icon*/
	public function icon(){
    	return $this->fetch();
	}


	public function get_prod_search_menu(){
		$SHOP_URL = 'http://'.$_SERVER['HTTP_HOST'].'/';
		$this->assign('SHOP_URL', $SHOP_URL);

		$products = Db::table('product')->field('id, title')->where('online', 1)->order('order_id asc, id desc')->select();
		foreach ($products as $key => $value) {
			$products[$key]['typeinfo'] = Db::table('typeinfo')->field('id, title')->where('online', 1)
											->where('parent_id', $value['id'])->where('online', 1)
											->where('branch_id', 0)
											->order('order_id asc, id desc')->select();
		}
		$this->assign('products', $products);

		// dump($products); exit;
		return $this->fetch('ajax/prod_search_menu');
	}
}

