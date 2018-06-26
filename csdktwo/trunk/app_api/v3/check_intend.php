<?php
require_once('base.php');
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$obj = new stdClass;
$obj->status = 500;

$uid		= trim($_POST["uid"]) ;
$sign_sn   	= trim($_POST["sign_sn"]) ;
$deal_id = isset($_POST['deal_id'])?trim($_POST['deal_id']):NULL;

if(is_null($deal_id)){
	$obj->r = "项目ID不能为空";
	CommonUtil::return_info($obj);
	return;	
}
/***************投资意向****************************/

$intend_exists ="
					select 	create_time
					from   	cixi_deal_intend_log
					where 	deal_id = ?
					and 	user_id = ?
					";

$para_intend_exists	= array($deal_id,$uid);
$result_intend 		= PdbcTemplate::query($intend_exists,$para_intend_exists);
if (!empty($result_intend)) {
	$obj->intend_time =is_null($result_intend[0]->create_time) ? ""	:$result_intend[0]->create_time;
	$obj->is_intend =1;
	$obj->status = 200;

}else{
	$obj->is_intend =0;
	$obj->status = 200;
}
CommonUtil::return_info($obj);
?>