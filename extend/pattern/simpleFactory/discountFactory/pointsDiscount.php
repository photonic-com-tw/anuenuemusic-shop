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
        $lang_menu = get_lang_menu();

        $PointRecords = new PointRecords($OrderData['id']);
        $records = $PointRecords->add_records([
            'msg'           => $lang_menu['使用紅利線上購物'],
            'points'        => $this->discountId * (-1),
            'belongs_time'  => time()
        ]);

        return [
            'total' => $this->total - $this->discountId,
            'discount' => urldecode(json_encode([
                [
                    'type' => urlencode($lang_menu['紅利']),
                    'name' => urlencode($lang_menu['使用'] . (int)$this->discountId . $lang_menu['點']),
                    'count' => urlencode($lang_menu['扣'] . (int)$this->discountId . $lang_menu['元']),
					'dis' => (int)$this->discountId
                ]
            ]))
        ];
    }
}
