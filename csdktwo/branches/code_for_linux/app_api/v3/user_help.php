<?php
require_once('base.php');
/******* 	申请磁斯达克协助  **********************/

$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$sign_sn		= trim($_POST['sign_sn']);
$uid		= isset($_POST['uid'])? trim($_POST['uid']):NULL;

$para_select[]=$uid;
$sql="select apply_help_time from cixi_user where id = ?";
$result_select =  PdbcTemplate::query($sql,$para_select,PDO::FETCH_OBJ, 1);
if (!empty($result_select->apply_help_time)) {
	$obj->status = 200;
	CommonUtil::return_info($obj);
	return;
}
$para_value=array();
$sql="update cixi_user set apply_help_time=? where id = ?";
$time=time();
$para_value[]=$time;
$para_value[]=$uid;
$result =  PdbcTemplate::execute($sql,$para_value);

if ($result[0]===false) {
	$obj->r = "申请磁斯达克协助失败";
	CommonUtil::return_info($obj);
	return;
}

$obj->status = 200;
CommonUtil::return_info($obj);


?>