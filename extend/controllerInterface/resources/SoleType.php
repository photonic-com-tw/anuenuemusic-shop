<?php

 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Set 12 2017
 * @Description: Interface that for SoleType resources controller
 * @depend: none
 *
*/

namespace controllerInterface\resources;

interface SoleType
{
    public function index();
    public function edit();
    public function update();    
    public function create();
    public function doCreate();
    public function delete();
}
