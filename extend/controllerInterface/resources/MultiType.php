<?php

 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Set 12 2017
 * @Description: Interface that for MultiType resources controller
 * @depend: none
 *
*/

namespace controllerInterface\resources;

interface MultiType
{
    //productcat
    public function typeIndex();
    public function typeEdit();
    public function typeUpdate();
    public function typeCreate();
    public function typeDoCreate();
    public function typeDelete();
    public function typeGetter($id);

    //product
    public function index($type);
    public function edit();
    public function update();    
    public function create();
    public function doCreate();
    public function delete();
}