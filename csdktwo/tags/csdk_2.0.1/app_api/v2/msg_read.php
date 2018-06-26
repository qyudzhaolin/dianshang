<?php
require_once('base.php');
/*******  7.2	用户加关注  **********************/

$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$sign_sn		= trim($_POST['sign_sn']);
$uid		= isset($_POST['uid'])? trim($_POST['uid']):NULL;



$sql_update   ="update cixi_user_notify set is_read= 1 where user_id= $uid";
	
	   		$result =  PdbcTemplate::execute($sql_update,null);
	    	if($result[0]===false){
				$obj->r = "标记失败";
				CommonUtil::return_info($obj);
				return;
			}
			else{
				$obj->status = 200;
				CommonUtil::return_info($obj);
				return;
			}

?>