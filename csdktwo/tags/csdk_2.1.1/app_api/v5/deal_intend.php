<?php
require_once('base.php');
/*******   6.5	项目投资意向  **********************/

$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$sign_sn		= trim($_POST['sign_sn']);
$uid		= isset($_POST['uid'])? trim($_POST['uid']):NULL;
$deal_id =  isset($_POST['deal_id'])? trim($_POST['deal_id']):NULL;
if (is_null($deal_id)) {
	$obj->r = "项目ID不能为空";
	CommonUtil::return_info($obj);
	return;
}
$para_select[]=$deal_id;
$para_select[]=$uid;
$sql="select id from cixi_deal_intend_log where deal_id=? and user_id = ?";
$result_select =  PdbcTemplate::query($sql,$para_select);
if (!empty($result_select)) {
	$obj->r = "已添加过投资意向";
	CommonUtil::return_info($obj);
	return;
}

if(!is_null($deal_id)&&!is_null($uid)){
	$para_value=array();
	$sql="insert into cixi_deal_intend_log (deal_id,user_id,create_time)  values (?,?,?)";
	$para_value[]=$deal_id;
	$para_value[]=$uid;
	$time=time();
	$para_value[]=$time;
	$result =  PdbcTemplate::execute($sql,$para_value);
}else{
	$obj->r = "添加投资意向失败";
	CommonUtil::return_info($obj);
	return;
}
if (!empty($result)) {
		$sql_update="update cixi_user set intend_count = intend_count + 1 where id = ?";
		$para_value_update[]=$uid;
		$result_update =  PdbcTemplate::execute($sql_update,$para_value_update);
		if ($result_update[0]===false) {
			$obj->r = "添加投资意向失败";
			CommonUtil::return_info($obj);
			return;
		}
}
if ($result[0])
{
	$data['intend_time']=$time;
	$obj->status = 200;
	$obj->data = $data;
	CommonUtil::return_info($obj);
}
else
{
	$obj->r = "添加投资意向失败";
	CommonUtil::return_info($obj);
}

?>