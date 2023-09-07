<?php
namespace pattern\simpleFactory\discountFactory;

use think\Db;
/*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 08 2017
 * @Description: Abstract Class that all orderClass's parent
 * @depend: (1)thinkPHP5.x think\Db static class
 *
*/
abstract class Discount
{
    protected $discountId;
    protected $total;

    public function __construct($discountId, $total) {
        $this->discountId = $discountId;
        $this->total = $total;
    }

    abstract public function getDiscountAndTotal($OrderData);
}
