<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
 
/*if (strstr($_SERVER['REQUEST_URI'],'api')){}
elseif ($_SERVER['REQUEST_URI']!="/") 
{header("location: /");}*/
define('APP_DEBUG',True);
require './system/common.php';
require './app/Lib/App.class.php';
clear_cache();//清缓存
//实例化一个网站应用实例
$AppWeb = new App(); 

?>