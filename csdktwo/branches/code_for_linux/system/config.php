<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

// 前后台加载的系统配置文件


// 加载数据库中的配置与数据库配置
/*if(file_exists(APP_ROOT_PATH.'public/db_config.php'))
{
	$db_config	=	require APP_ROOT_PATH.'public/db_config.php';
}*/
define('RUN_ENV',isset($_SERVER['RUN_ENV'])?$_SERVER['RUN_ENV']:'develop');
if(file_exists(APP_ROOT_PATH.'public/env_conf/'.RUN_ENV.'/db_config.php'))
{
	$db_config	=	require APP_ROOT_PATH.'public/env_conf/'.RUN_ENV.'/db_config.php';
}

//加载系统配置信息
if(file_exists(APP_ROOT_PATH.'public/sys_config.php'))
{
	$db_conf	=	require APP_ROOT_PATH.'public/sys_config.php';
}

//加载系统信息
if(file_exists(APP_ROOT_PATH.'public/version.php'))
{
	$version	=	require APP_ROOT_PATH.'public/version.php';
}

//加载时区信息
if(file_exists(APP_ROOT_PATH.'public/timezone_config.php'))
{
	$timezone	=	require APP_ROOT_PATH.'public/timezone_config.php';
}

if(is_array($db_config))
$config = array_merge($db_conf,$db_config,$version,$timezone);
elseif(is_array($db_conf))
$config = array_merge($db_conf,$version,$timezone);
else
$config = array_merge($version,$timezone);

$config['REWRITER_DEPART'] = '-';
$config['DEBUG'] = false;
$config['SITE_DOMAIN'] = "";


$config['accessKey']='5CQmagmT27DNXYhsmVewntOpd9VLGD8sC5c02ptg';
$config['secretKey']='lQm-akaIb3JOS-WAlBycTXF_95hx7JEd9s88ARk5';
$config['maxSize']=5 * 1024 * 1024;
//$config['qiniu_domain']='http://7xju4h.com1.z0.glb.clouddn.com/';
return $config;
?>