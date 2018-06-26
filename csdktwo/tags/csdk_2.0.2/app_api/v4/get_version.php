<?php
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
$sql="select app_upgrade_desc,app_md5,app_url,app_version_number from cixi_app where type =? ";
$para_value[]=$type;
$result=  PdbcTemplate::query($sql,$para_value);

if ($type=='android') {
	if (!empty($result)) {
		if(($result[0]->app_version_number-$app_version_number)<=0){
			$obj->app_update=0;
		}
		elseif (($result[0]->app_version_number-$app_version_number)<=1){
			$obj->app_update=1;
		} 
		else 
		{
			$obj->app_update=2;
		}	
		$obj->app_md5			= is_null($result[0]->app_md5) ? "" :  $result[0]->app_md5 ;
		$obj->app_upgrade_desc			= is_null($result[0]->app_upgrade_desc) ? "" :  $result[0]->app_upgrade_desc ;
		$obj->app_url			= is_null($result[0]->app_url) ? "" :  $result[0]->app_url ;
		 
	}
}elseif ($type=='mac'){

	if (!empty($result)) {
	    if(($result[0]->app_version_number-$app_version_number)<=0){
			$obj->app_update=0;
		}
		elseif (($result[0]->app_version_number-$app_version_number)<=1){
			$obj->app_update=1;
		} 
		else 
		{
			$obj->app_update=2;
		}	
		 
		$obj->app_upgrade_desc			= is_null($result[0]->app_upgrade_desc) ? "" :  $result[0]->app_upgrade_desc ;
	 
		 
	}
}else{
	$obj->r = "获取版本号失败";
	CommonUtil::return_info($obj);
	return;
}

$obj->status = 200;
 
 

CommonUtil::return_info($obj);
 
?>