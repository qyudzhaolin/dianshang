<?php
require_once('base.php');
//
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$uid	= isset($_POST['uid'])? trim($_POST['uid']):NULL;//其他用户的USERID
$sign_sn	= isset($_POST['sign_sn'])? trim($_POST['sign_sn']):NULL;
//$user_id = 251;
$sql = "select ex.vip_money,ex.vip_begin_time,ex.vip_end_time
		from cixi_user u	 left join cixi_user_ex_investor ex  on ex.user_id=u.id
		where 	 u.id = ?
		";
$para = array($uid);

$result = PdbcTemplate::query($sql,$para);
$obj_final = new stdClass;
if(!empty($result))
{
	$obj_final ->vip_money = is_null($result[0]->vip_money) ? "" 		: $result[0]->vip_money;
	$obj_final ->vip_begin_time = is_null($result[0]->vip_begin_time) ? "" 		: $result[0]->vip_begin_time;
	$obj_final ->vip_end_time = is_null($result[0]->vip_end_time) ? "" 		: $result[0]->vip_end_time;
	$obj->status = 200;
	$obj->data = $obj_final;
		
}
else
{
	$obj->r = "此投资人不存在";
}

CommonUtil::return_info($obj);	
?>