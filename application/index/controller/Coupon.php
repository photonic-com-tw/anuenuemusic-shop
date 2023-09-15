<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Coupon extends PublicController
{

	public function __construct()
    {
    	$Request = Request::instance();
		parent::__construct($Request);
		$this->assign('mebermenu_active', 'coupon');

		$consent = Db::table('consent')->where("id=1")->find();
		$this->assign('consent_other', $consent['other']);

		if(!$this->user){ $this->error(LANG_MENU['請先登入會員'], url('Login/login'));};
	}

	public function coupon() {
		if($this->user == null){
			$this->redirect('Index/index');
		}

		$consent = Db::table('consent')->select()[0]['coupon'];
		$this->assign('consent',$consent);

		$Coupons = Db::table('coupon')
                ->field('
                    coupon.id AS coupon_id, 
                    coupon.title AS coupon_title, 
                    coupon.transfer AS coupon_transfer, 
                    coupon_pool.login_time AS coupon_pool_login_time, 
                    coupon_pool.id AS coupon_pool_id, 
                    coupon.end AS coupon_end
                ')
                ->join('coupon_pool', 'coupon.id = coupon_pool.coupon_id')
                ->where([
                    'coupon_pool.owner' => $this->user['id'],
                    'coupon.online' => 1,
                    'coupon.start' => ['<', time()],
                    'coupon.end' => ['>', time()]
				])
				->whereNull('coupon_pool.use_time')
				->whereNotNull('coupon_pool.login_time')
				->select();
		$this->assign('Coupons',$Coupons);
		return $this->fetch('coupon');
	}

	public function record() {
		$Coupons = Db::table('coupon')
                ->field('
                    coupon.id AS coupon_id, 
                    coupon.title AS coupon_title, 
                    coupon.transfer AS coupon_transfer, 
                    coupon_pool.use_time AS coupon_pool_use_time, 
                    coupon_pool.id AS coupon_pool_id, 
                    coupon.end AS coupon_end
                ')
                ->join('coupon_pool', 'coupon.id = coupon_pool.coupon_id')
                ->where(
                    'coupon_pool.owner = ' . $this->user['id'] . ' AND ' .
                    'coupon.online = 1 AND (' .
						'coupon.end < ' . time() . ' OR ' .
						'coupon_pool.use_time IS NOT NULL' .
					')'
				)
				->select();
		$consent = Db::table('consent')->select()[0]['coupon'];
		$this->assign('consent',$consent);
		$this->assign('Coupons',$Coupons);

		return $this->fetch('record');
	}

	public function teach() {
		$fixedResourcesRowId = 1;
		$consent = Db::table('consent')->field('coupon')->find($fixedResourcesRowId);
		return json($consent);

		$this->assign('consent', $consent);
		return $this->fetch('teach');
	}

	public function description($id) {
		$description = Db::table('coupon')->field('content')->find($id);
		$description = $description['content'];
		return json($description);

		// $this->assign('description', $description);
		// return $this->fetch('description');
	}

	public function getCouponByNumber() {
		try {
			$number = Request::instance()->post('number');
			$coupon = Db::table('coupon_pool')
				->where('number', $number)
				->whereNull('owner')
				->find();

			if(!$coupon){
				throw new \Exception(LANG_MENU['內容有誤']); /*找不到這張優惠券*/
			}
			if (!sizeof($coupon)) {
				throw new \Exception(LANG_MENU['內容有誤']); /*找不到這張優惠券*/
			}

			$coupon_type = Db::table('coupon')
				->where('id', $coupon['coupon_id'])
				->find();
			$limit_num = $coupon_type['limit_num'];
			$user_coupons = Db::table('coupon_pool')
				->where('coupon_id', $coupon['coupon_id'])
				->where('owner', $this->user['id'])
				->select();
			if(count($user_coupons)>=$limit_num){
				throw new \Exception(LANG_MENU['您已達優惠券領取上限']);
			}

			Db::table('coupon_pool')
				->where('number', $number)
				->update([
					'owner' => $this->user['id'],
					'login_time' => time()
				]);
		} catch (\Exception $e) {
            $this->error($e->getMessage());
		}

		$this->success(LANG_MENU['操作成功']); /*領取成功*/
	}

	public function transforCoupon() {
		try {
			$userNumber = Request::instance()->post('number');
			$userId = Db::connect('main_db')
				->table('account')
				->field('id')
				->where('number', $userNumber)
				->find();

			if (!sizeof($userId)) {
				throw new \Exception(LANG_MENU['內容有誤']); /*找不到這位會員*/
			}

			$couponId = Request::instance()->post('id');
			$coupon = Db::table('coupon_pool')
				->where('id', $couponId)
				->update([
					'owner' => $userId['id'],
					'login_time' => time()
				]);
		} catch (\Exception $e) {
            $this->error($e->getMessage());
		}

		$this->success(LANG_MENU['操作成功']); /*轉移成功*/
	}
}
