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
class noneDiscount extends Discount
{
    public function getDiscountAndTotal($OrderData) {
        return [
            'total' => $this->total,
            'discount' => '[]'
        ];
    }
}

