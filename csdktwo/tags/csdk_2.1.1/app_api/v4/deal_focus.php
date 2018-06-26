<?php
require_once('base.php');
/*******   6.4	项目加关注 **********************/

$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$sign_sn		= trim($_POST['sign_sn']);
$uid		= isset($_POST['uid'])? trim($_POST['uid']):NULL;
$deal_id =  isset($_POST['deal_id'])? trim($_POST['deal_id']):NULL;

$para_select[]=$deal_id;
$para_select[]=$uid;
$sql="select id from cixi_deal_focus_log where deal_id=? and user_id = ?";
$result_select =  PdbcTemplate::query($sql,$para_select);
if (!empty($result_select)) {
	$obj->r = "已关注过";
	CommonUtil::return_info($obj);
	return;
}

if(!is_null($deal_id)&&!is_null($uid)){
	$para_value=array();
	$sql="insert into cixi_deal_focus_log (deal_id,user_id,create_time)  values (?,?,?)";
	$para_value[]=$deal_id;
	$para_value[]=$uid;
	$time=time();
	$para_value[]=$time;

	$result =  PdbcTemplate::execute($sql,$para_value);
	if (!empty($result)) {
		$sql_update="update cixi_deal set focus_count = focus_count + 1,comment_count = comment_count + 1 where id = ?";
		$para_value_update[]=$deal_id;
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
	$data['focus_time']=$time;
	$obj->status = 200;
	$obj->data = $data;
	CommonUtil::return_info($obj);
}
else
{
	$obj->r = "关注失败";
	CommonUtil::return_info($obj);
}

?>