<?php
require_once('base.php');

$obj = new stdClass;
$obj->status = 500;

$mobile		 = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
$user_pwd = isset($_POST['user_pwd'])? trim($_POST['user_pwd']):NULL;

if($mobile==null)
{
	$obj->r = "手机号为空";
	CommonUtil::return_info($obj);
    return;
}

if($user_pwd==null)
{
	$obj->r = "密码不得为空";
	CommonUtil::return_info($obj);
    return;
}
if (check_mobile($mobile)==false)
{
    	$obj->r = "手机号格式不正确";
		CommonUtil::return_info($obj);
    	return;
}
if (check_pwd($user_pwd)==false)
{
    	$obj->r = "密码格式不正确";
		CommonUtil::return_info($obj);
    	return;
}
if (is_exist_mobile_user($mobile)==false)
{
    	$obj->r = "手机号没有注册过";
		  CommonUtil::return_info($obj);
    	return;
}

  $sql="update cixi_user set  user_pwd= ?  where mobile= ?";
  $para_value=array();
  $para_value[]=md5($user_pwd);
  $para_value[]=$mobile;
  $result =  PdbcTemplate::execute($sql,$para_value);
  if($result[0]===false){
		$obj->r = "修改密码失败";
		CommonUtil::return_info($obj);
		return;
  }

	$obj->status = 200;
	CommonUtil::return_info($obj);
?>