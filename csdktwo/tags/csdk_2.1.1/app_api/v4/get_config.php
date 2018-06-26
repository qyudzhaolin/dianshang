<?php
require_once('base.php');
require_once('../function/Session.php');
//5.2	请求融资阶段列表
$obj = new stdClass;
$obj->status = 500;
//$user_status = CommonUtil::verify_user();

//增加app版本信息
$type	= isset($_POST['app_type'])? trim($_POST['app_type']):NULL;
if (is_null($type)) {
	$obj->r = "参数为空";
	CommonUtil::return_info($obj);
	return;
}
$obj_config = new stdClass;
$obj_config->img_domain=IMG_DOMAIN;
$obj_config->bp_domain=BP_DOMAIN;

$sql="select app_md5,app_version,app_url from cixi_app where type in  (?,'cates_verison','period_verison','degree_verison') ";
$para_value[]=$type;
$result=  PdbcTemplate::query($sql,$para_value);
//var_dump($result);

if ($type=='android') {
		if (!empty($result)) {
			$obj_config->app_md5			= is_null($result[0]->app_md5) ? "" :  $result[0]->app_md5 ;
			$obj_config->app_version		= is_null($result[0]->app_version) ? "" :  $result[0]->app_version ;
			$obj_config->app_url			= is_null($result[0]->app_url) ? "" :  $result[0]->app_url ;
			$obj_config->cates_verison			= is_null($result[1]->app_version) ? "" :  $result[1]->app_version ;
			$obj_config->period_verison			= is_null($result[2]->app_version) ? "" :  $result[2]->app_version ;
			$obj_config->degree_verison			= is_null($result[3]->app_version) ? "" :  $result[3]->app_version ;
		}
}elseif ($type=='mac'){
		
		if (!empty($result)) {
			$obj_config->app_version		= is_null($result[0]->app_version) ? "" :  $result[0]->app_version ;
			$obj_config->cates_verison			= is_null($result[1]->app_version) ? "" :  $result[1]->app_version ;
			$obj_config->period_verison			= is_null($result[2]->app_version) ? "" :  $result[2]->app_version ;
			$obj_config->degree_verison			= is_null($result[3]->app_version) ? "" :  $result[3]->app_version ;
		}
}else{
	$obj->r = "获取配置信息失败";
	CommonUtil::return_info($obj);
	return;
}

$obj->status = 200;
$obj->data = $obj_config;
$obj->data = $obj_config;


CommonUtil::return_info($obj);
?>