<?php
 /*
 *
 * @author: MazeR
 * @email: mazer0701@gmail.com
 * @lastUpdate: Nov 12 2017
 * @Description: Interface that for parentController Implementation error process function
 * @depend: none
 *
*/

namespace controllerInterface\parentFunction;

interface ErrorProcess
{
    function dumpException(\Exception $e);
    function dumpError($errorMessage);
}