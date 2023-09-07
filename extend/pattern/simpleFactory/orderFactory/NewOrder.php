<?php

namespace pattern\simpleFactory\orderFactory;

use think\Db;
use pattern\PointRecords;

 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 08 2017
 * @Description: orderClass that status is new
 * @depend: (1)thinkPHP5.x think\Db static class
 *
*/

class NewOrder extends Order
{
    public function changeStatus2Next() {
        if($this->already2Next){
            throw new \LogicException('Status already change to next'); 
        }
        try{
            Db::connect($this->order_db)->table($this->tableName)
                ->where('id', $this->id)
                ->setField('status', 'Complete');
            $owner = Db::connect($this->order_db)->table($this->tableName)
                        ->field('user_id, add_point, total')
                        ->find($this->id);
            Db::connect($this->order_db)->table('account')
                ->where('id', $owner['user_id'])
                ->setInc('total', $owner['total']);
            Db::connect($this->order_db)->table('account')
                ->where('id', $owner['user_id'])
                ->setInc('point', $owner['add_point']);
            $this->already2Next = true;
        }catch (\Exception $e) {
            throw new \RuntimeException('Db operating error：' . $e->getMessage());
        }
    }

    public function changeStatus2Return($reason) {
        $this->returndiscount();
        parent::changeStatus2Return($reason);
    }

    public function changeStatus2Cancel($reason) {
        $this->returndiscount();
        parent::changeStatus2Cancel($reason);
    }

    private function returndiscount() {
        $lang_menu = get_lang_menu();

        $discount = Db::connect($this->order_db)->table('orderform')
            ->field('*')
            ->find($this->id);

        $discount['discount'] = json_decode($discount['discount'], true);

        if($discount['discount']){
            switch ($discount['discount'][0]['type']) {
                case $lang_menu['紅利']:
				
					$PointRecords = new PointRecords($discount['user_id']);
                    $records = $PointRecords->add_records([
                        'msg'           => $lang_menu['取消訂單'].'：'.$discount['order_number'].$lang_menu['，返還使用點數'],
                        'points'        => $discount['discount'][0]['dis'],
                        'belongs_time'  => $discount['create_time']
                    ]);
                    $PointRecords->set_point_expire(); /*扣除過期點數*/
					
                    break;
                case $lang_menu['優惠券']:
					/*Db::connect(substr($discount['order_number'], 0, 1) . '_sub')*/
                    Db::connect('A_sub')
                        ->table('coupon_pool')
                        ->where('id', $discount['discount'][0]['coupon_pool_id'])
                        ->setField('use_time', null);
                    break;
            }
        }
        return;
    }
}

























