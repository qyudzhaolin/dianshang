<?php
require_once('base.php');
/*******  7.1 修改用户信息  **********************/
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$type		= isset($_POST['type'])?trim($_POST['type']):NULL;
if (is_null($type)) {
	$obj->r = "文件类型为空";
	CommonUtil::return_info($obj);
	return;
}

// 获取token
$params = array(
    "bucket"    => 'csdk'.$type
);
$result = request_service_api("Common.QiniuUpload.getToken",$params);

$token = $result['status'] == 0 ? trim($result['response']) : "";

if ($token)
{
	$obj->status = 200;
	$obj->token = $token;
	CommonUtil::return_info($obj);
}
else
{
	$obj->r = "获取token失败";
	CommonUtil::return_info($obj);
}
     
?>