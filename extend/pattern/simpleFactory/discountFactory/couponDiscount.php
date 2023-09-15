<?php
namespace pattern\simpleFactory\discountFactory;

use think\Db;
/*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 08 2017
 * @Description: orderClass that status is new
 * @depend: (1)thinkPHP5.x think\Db static class
 *
*/
class couponDiscount extends Discount
{
    public function getDiscountAndTotal($OrderData) {
        $DiscountAndTotal = $this->getDiscount();
        return [
            'total' => $DiscountAndTotal['total'],
            'discount' => urldecode(json_encode($DiscountAndTotal['discount']))
        ];
    }

    private function getDiscount(){
        $coupon = Db::table('coupon_pool')
             ->field('
                    coupon_pool.id AS coupon_pool_id, 
                    coupon.discount AS discount, 
                    coupon.title AS coupon_title
                ')
            ->join('coupon', 'coupon.id = coupon_pool.coupon_id')
            ->where('coupon_pool.id', $this->discountId)
            ->find();
        Db::table('coupon_pool')
            ->where('id', $this->discountId)
            ->setField('use_time', time());
        return [
            'discount' => [
                [
                    'type' => urlencode(LANG_MENU['優惠券']),
                    'name' => urlencode($coupon['coupon_title']),
                    'count' => urlencode(LANG_MENU['扣'] . $coupon['discount'] . LANG_MENU['元']),
                    'coupon_pool_id' => $coupon['coupon_pool_id']
                ]
            ],
            'total' => $this->total - $coupon['discount']
        ];
    }
}
