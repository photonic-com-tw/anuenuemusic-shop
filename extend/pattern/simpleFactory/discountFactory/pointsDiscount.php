<?php

namespace pattern\simpleFactory\discountFactory;

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

class pointsdiscount extends discount
{
    public function getdiscountAndTotal($OrderData) {
        $PointRecords = new PointRecords($OrderData['id']);
        $records = $PointRecords->add_records([
            'msg'           => LANG_MENU['使用紅利線上購物'],
            'points'        => $this->discountId * (-1),
            'belongs_time'  => time()
        ]);

        return [
            'total' => $this->total - $this->discountId,
            'discount' => urldecode(json_encode([
                [
                    'type' => urlencode(LANG_MENU['紅利']),
                    'name' => urlencode(LANG_MENU['使用'] . (int)$this->discountId . LANG_MENU['點']),
                    'count' => urlencode(LANG_MENU['扣'] . (int)$this->discountId . LANG_MENU['元']),
					'dis' => (int)$this->discountId
                ]
            ]))
        ];
    }
}
