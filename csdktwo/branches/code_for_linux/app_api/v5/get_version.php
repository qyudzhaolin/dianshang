<?php
/**
 * 客户端升级----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Version: 2.1
 * +----------------------------------------------------------------------
 * |
 */
require_once('base.php');
//请求版本号

$obj = new stdClass;
$obj->status = 500;
$type	= isset($_POST['app_type'])? trim($_POST['app_type']):NULL;
$app_version_number	= isset($_POST['app_version_number'])? trim($_POST['app_version_number']):NULL; 
if (is_null($type)&&is_null($app_version_number)) {
	$obj->r = "参数为空";
	CommonUtil::return_info($obj);
	return;
}
 
//版本描述和版本号判断
$sql="select app_upgrade_desc,app_md5,app_url,app_version_number,app_present_version from cixi_app where type =? ";
$para_value[]=$type;
$result=  PdbcTemplate::query($sql,$para_value);
if (!empty($result)) {
	//最低版本号和客户端比较，最低>客户端强制升级
	if($result[0]->app_version_number>$app_version_number){
		$obj->app_update=2;
	}
	//当前版本号和客户端比较，当前>客户端友情提示升级
	elseif ($result[0]->app_present_version>$app_version_number){
		$obj->app_update=1;
	}
	//当前版本号和客户端比较，当前=客户端表示最新版本
	else
	{
		$obj->app_update=0;
	}
	//升级描述
	$obj->app_upgrade_desc			= is_null($result[0]->app_upgrade_desc) ? "" :  $result[0]->app_upgrade_desc ;
	//android返回下载链接和MD5值
	if ($type=='android') {
		$obj->app_md5			= is_null($result[0]->app_md5) ? "" :  $result[0]->app_md5 ;
		$obj->app_url			= is_null($result[0]->app_url) ? "" :  $result[0]->app_url ;
	}  

}else{
	$obj->r = "获取版本号失败";
	CommonUtil::return_info($obj);
	return;
}

$obj->status = 200;
 
 

CommonUtil::return_info($obj);
 
?>