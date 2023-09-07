<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
// 定义应用目录	ini_set('display_errors','1');error_reporting(E_ALL);

define('APP_PATH', __DIR__ . '/application/');
date_default_timezone_set('Asia/Taipei');
//ini_set('xdebug.var_display_max_depth', 5);
//ini_set('xdebug.var_display_max_children', 256);
//ini_set('xdebug.var_display_max_data', 1024);
//define('BIND_MODULE','index');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
/*
function clert_temp_cache()
{
    array_map('unlink', glob(TEMP_PATH . '/*.php'));
    if(is_dir(TEMP_PATH)){
        rmdir(TEMP_PATH);
    }
}
clert_temp_cache();
*/
