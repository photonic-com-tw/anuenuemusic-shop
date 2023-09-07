<?php

namespace pattern\recursiveCorrdination\discountRC;

use think\Db;

class CouponCheck extends TeamMember {

    public static function createFactory($Proposal) {
        $instance = new self($Proposal);
		return $instance->participate();
	}

    public function participate() {
        $Coupons = $this->getCoupon();
        $Total = $this->getTotal();
        $require = $this->Proposal->getRequire();
        $Coupons = array_filter($Coupons, function ($value) use ($Total, $require) {

            // 全館商品
            if (!$value['coupon_area']) {
                return ($value['coupon_condition'] <= $Total);
            }

            // 單一商品
            return sizeof(array_filter($require['cartData'], function ($cartValue) use ($value) {
                $key_list = explode("_", $cartValue['type_product_id']);
                $key_id = $key_list[0];
                if(count($key_list) == 1){
                    return Db::table('productinfo')->where(
                        'id = "'.$cartValue['type_product_id'].'"'
                    )->select()[0]['id'] == $value['coupon_area_id'];
                }else{
                    return [];
                }
            }));
        });
        $this->Proposal->projectData['coupon'] = $Coupons;
        return $this->send2Next();
    }

    private function getCoupon() {
        $require = $this->Proposal->getRequire();
        return Db::table('coupon')
                ->field('
                    coupon.id AS coupon_id, 
                    coupon.area AS coupon_area, 
                    coupon.area_id AS coupon_area_id, 
                    coupon.coupon_condition AS coupon_condition, 
                    coupon.discount AS discount, 
                    coupon_pool.id AS coupon_pool_id, 
                    coupon.title AS coupon_title
                ')->join(
                    'coupon_pool', 
                    'coupon.id = coupon_pool.coupon_id'
                )->where([
                    'coupon_pool.owner' => $require['user_id'],
                    'coupon.online' => 1,
                    'coupon.start' => ['<', time()],
                    'coupon.end' => ['>', time()]
				])
                ->whereNull('coupon_pool.use_time')
				->whereNotNull('coupon_pool.login_time')
                ->select();
    }

    private function getTotal() {
        $Total = 0;
        $require = $this->Proposal->getRequire();        
        array_walk($require['cartData'], function ($value) use (&$Total){
            $Total += (int)($value['countPrice']) * (int)$value['num'];
        });
        return $Total;
    }

}
