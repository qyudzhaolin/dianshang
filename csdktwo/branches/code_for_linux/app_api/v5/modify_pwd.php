<?php
// API:modify_pwd.php 用于会员在我的模块把旧密码变更为新密码
require_once('base.php');

/* 验证登录*/
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$obj = new stdClass;
$obj->status = 500;

$uid	= isset($_POST['uid'])? trim($_POST['uid']):NULL;
$user_pwd = isset($_POST['user_pwd'])? trim($_POST['user_pwd']):NULL;
$user_new_pwd = isset($_POST['user_new_pwd'])?trim($_POST['user_new_pwd']):NULL;

if($uid == null)
{
	$obj->r = "用户id不能为空";
	CommonUtil::return_info($obj);
    return;
}

if($user_pwd == null)
{
	$obj->r = "原密码不得为空";
	CommonUtil::return_info($obj);
    return;
}

if($user_new_pwd == null)
{
	$obj->r = "新密码不得为空";
	CommonUtil::return_info($obj);
	return;
}

if (check_pwd($user_new_pwd) == false)
{
    	$obj->r = "新密码格式不正确";
		CommonUtil::return_info($obj);
    	return;
}

	$sql_query_user = "SELECT id,user_pwd FROM cixi_user WHERE id = ?";
	$para_value_query_user = array();
	$para_value_query_user[]=$uid;
	$result_query_user = PdbcTemplate::query($sql_query_user,$para_value_query_user,PDO::FETCH_OBJ,1);
	if(empty($result_query_user)){
		$obj->r = "未查询到该会员信息";
		CommonUtil::return_info($obj);
		return;
	}
	
	if(md5($user_pwd) <> $result_query_user->user_pwd){
		$obj->r = "您输入的原密码有误";
		CommonUtil::return_info($obj);
		return;
	}
	
  $sql="update cixi_user set  user_pwd= ?  where id= ?";
  $para_value=array();
  $para_value[]=md5($user_new_pwd);
  $para_value[]=$uid;
  $result =  PdbcTemplate::execute($sql,$para_value);
  if($result[0]===false){
		$obj->r = "修改密码失败";
		CommonUtil::return_info($obj);
		return;
  }

	$obj->status = 200;
	CommonUtil::return_info($obj);
?>