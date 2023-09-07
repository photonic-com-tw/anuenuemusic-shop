<?php

namespace pattern\simpleFactory\orderFactory;

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

class OrderFactory
{
    public static function createOrder($id, $tableName, $tableName2) {
        try{
            $order = Db::connect('main_db')->table($tableName)
                         ->field('status')
                         ->find($id);
        }catch(\Exception $e){
            throw new \RuntimeException('Db operating error：' . $e->getMessage());
        }
        if(!$order){
            throw new \UnexpectedValueException('Order not find');
        }
        $orderClassName = 'pattern\\simpleFactory\\orderFactory\\' . $order['status'] . 'Order';
        return new $orderClassName($id, $tableName, $tableName2);
    }
}