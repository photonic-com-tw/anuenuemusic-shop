<?php

namespace pattern\simpleFactory\orderFactory;

 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 08 2017
 * @Description: orderClass that status is delete
 * @depend: none
 *
*/

class DeleteOrder extends Order
{
    public function changeStatus2Next() {
        throw new \LogicException('Delete status without next status');
    }
    public function changeStatus2Return($reason) {
        throw new \LogicException('Delete status without next status');
    }
    public function changeStatus2Cancel($reason) {
        throw new \LogicException('Delete status without next status');
    }
}
