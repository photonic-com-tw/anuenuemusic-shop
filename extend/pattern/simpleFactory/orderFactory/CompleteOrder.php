<?php

namespace pattern\simpleFactory\orderFactory;

use think\Db;
use pattern\PointRecords;

 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 08 2017
 * @Description: orderClass that status is complete
 * @depend: none
 *
*/

class CompleteOrder extends Order
{
    public function changeStatus2Next() {
        throw new \LogicException('Complete status without next status');
    }

    public function changeStatus2Return($reason) {
        $this->returnPointAndPrice();
        $this->returndiscount();
        parent::changeStatus2Return($reason);
    }

    public function changeStatus2Cancel($reason) {
        $this->returnPointAndPrice();
        $this->returndiscount();
        parent::changeStatus2Cancel($reason);
    }

    private function returnPointAndPrice() {
        $owner = Db::connect($this->order_db)->table('orderform')
            ->field('*')
            ->find($this->id);

        $PointRecords = new PointRecords($owner['user_id']);
        $records = $PointRecords->add_records([
            'msg'           => LANG_MENU['取消訂單'].'：'.$owner['order_number'].LANG_MENU['，扣除贈送點數'],
            'points'        => $owner['add_point'] * (-1),
            'belongs_time'  => time()
        ]);
        $PointRecords->set_point_expire(); /*扣除過期點數*/
    }

    private function returndiscount() {
        $discount = Db::connect($this->order_db)->table('orderform')
            ->field('*')
            ->find($this->id);
        $discount['discount'] = json_decode($discount['discount'], true);
        if($discount['discount']){
            switch ($discount['discount'][0]['type']) {
                case LANG_MENU['紅利']:

                    $PointRecords = new PointRecords($discount['user_id']);
                    $records = $PointRecords->add_records([
                        'msg'           => LANG_MENU['取消訂單'].'：'.$discount['order_number'].LANG_MENU['，返還使用點數'],
                        'points'        => $discount['discount'][0]['dis'],
                        'belongs_time'  => $discount['create_time']
                    ]);
                    $PointRecords->set_point_expire(); /*扣除過期點數*/

                    break;

                case LANG_MENU['優惠券']:
                    Db::connect($this->order_db)->connect(substr($discount['order_number'], 0, 1) . '_sub')
                        ->table('coupon_pool')
                        ->where('id', $discount['discount'][0]['coupon_pool_id'])
                        ->setField('use_time', null);
                    break;
            }
        }
        return;
    }
}
