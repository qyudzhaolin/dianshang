<?php
require_once('base.php');
/*******  7.2	用户加关注  **********************/

$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$sign_sn		= trim($_POST['sign_sn']);
$uid		= isset($_POST['uid'])? trim($_POST['uid']):NULL;
$user_id =  isset($_POST['user_id'])? trim($_POST['user_id']):NULL;

$para_select[]=$uid;
$para_select[]=$user_id;

$sql="select id from cixi_user_focus_log where  user_id = ? and focus_user_id=?";
$result_select =  PdbcTemplate::query($sql,$para_select);
if (!empty($result_select)) {
	$obj->r = "已关注过";
	CommonUtil::return_info($obj);
	return;
}


if(!is_null($user_id)&&!is_null($uid)){
	$para_value=array();
	$sql="insert into cixi_user_focus_log (user_id,focus_user_id,create_time)  values (?,?,?)";
	$para_value[]=$uid;
	$para_value[]=$user_id;
	$para_value[]=time();
	$result =  PdbcTemplate::execute($sql,$para_value);
	if (!empty($result)) {
		$sql_update="update cixi_user set focus_count = focus_count + 1 where id = ?";
		$para_value_update[]=$user_id;
		$result_update =  PdbcTemplate::execute($sql_update,$para_value_update);
		if ($result_update[0]===false) {
			$obj->r = "关注失败";
			CommonUtil::return_info($obj);
			return;
		}
	}
}else{
	$obj->r = "关注失败";
	CommonUtil::return_info($obj);
	return;
}

if ($result[0])
{
	$obj->status = 200;
	CommonUtil::return_info($obj);
}
else
{
	$obj->r = "关注失败";
	CommonUtil::return_info($obj);
}

?>