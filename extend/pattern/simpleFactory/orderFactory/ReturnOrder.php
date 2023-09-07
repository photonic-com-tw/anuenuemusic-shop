<?php

namespace pattern\simpleFactory\orderFactory;

 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 08 2017
 * @Description: orderClass that status is cancel
 * @depend: none
 *
*/

class ReturnOrder extends Order
{
    public function changeStatus2Next() {
        throw new \LogicException('Cancel status without next status');
    }
    public function changeStatus2Return($reason) {
        throw new \LogicException('Cancel status without next status');
    }
    public function changeStatus2Cancel($reason) {
        throw new \LogicException('Cancel status without next status');
    }
}
