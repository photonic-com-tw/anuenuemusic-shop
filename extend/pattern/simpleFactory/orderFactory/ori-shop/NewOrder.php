<?php

namespace pattern\simpleFactory\orderFactory;

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

class NewOrder extends Order
{
    public function changeStatus2Next() {
        if($this->already2Next){
            throw new \LogicException('Status already change to next'); 
        }
        try{
            Db::connect('main_db')->table($this->tableName)
                ->where('id', $this->id)
                ->setField('status', 'Complete');
            $this->already2Next = true;
        }catch (\Exception $e) {
            throw new \RuntimeException('Db operating error：' . $e->getMessage());
        }
    }
}
