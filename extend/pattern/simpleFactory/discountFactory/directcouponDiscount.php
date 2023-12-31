<?php
namespace pattern\simpleFactory\discountFactory;

use think\Db;
use app\index\controller\Coupondirect;
/*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 08 2017
 * @Description: orderClass that status is new
 * @depend: (1)thinkPHP5.x think\Db static class
 *
*/
class directcouponDiscount extends Discount
{
    public function getDiscountAndTotal($OrderData) {
        $DiscountAndTotal = $this->getDiscount($OrderData['directcoupon_code']);
        return [
            'total' => $DiscountAndTotal['total'],
            'discount' => urldecode(json_encode($DiscountAndTotal['discount']))
        ];
    }

    private function getDiscount($user_code){
        $coupondirectDiscount = Coupondirect::get_discount($user_code);
        
        if($coupondirectDiscount['status']=="1"){
            return [
                'discount' => [
                    [
                        'type' => urlencode(LANG_MENU['直接輸入型優惠券']),
                        'name' => urlencode($coupondirectDiscount['name']),
                        'count' => urlencode(LANG_MENU['扣'] . $coupondirectDiscount['discount'] . LANG_MENU['元']),
                        'coupon_id' => $coupondirectDiscount['id']
                    ]
                ],
                'total' => $this->total - $coupondirectDiscount['discount']
            ];
        }else{
            return ['discount' => [], 'total' => $this->total];
        }
    }
}
