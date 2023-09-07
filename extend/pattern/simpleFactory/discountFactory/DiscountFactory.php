<?php
namespace pattern\simpleFactory\discountFactory;

use think\Db;
/*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 08 2017
 * @Description: simpleStaticFactory that create class by reflaction database data
 * @depend: (1)thinkPHP5.x think\Db static class
 *
*/
class DiscountFactory
{
    const DISCOUNT_DATA = [
        'name' => 0,
        'discountId' => 1
    ];

    public static function createDiscount($discountData, $total) {
        $orderClassName = 'pattern\\simpleFactory\\discountFactory\\' . $discountData[self::DISCOUNT_DATA['name']] . 'Discount';
        return new $orderClassName($discountData[self::DISCOUNT_DATA['discountId']], $total);
    }
}