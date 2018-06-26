<?php
	require_once('base.php');
    require_once('../function/Session.php');
	

	$obj = new stdClass;
	$obj->status = 500;
	$user_status = CommonUtil::verify_user();
	CommonUtil::check_status($user_status);

	$uid 		 = isset($_POST['uid'])? trim($_POST['uid']):NULL;
	$sign_sn	 = isset($_POST['sign_sn'])? trim($_POST['sign_sn']):NULL;
	$mobile		    = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
	if($uid==null)
	{
		$obj->r = "用户ID为空";
		CommonUtil::return_info($obj);
	    return;
	}
	$obj->status = 200;
	Session::del_token($uid,$mobile);
	$obj->data = "";
	CommonUtil::return_info($obj);
?>