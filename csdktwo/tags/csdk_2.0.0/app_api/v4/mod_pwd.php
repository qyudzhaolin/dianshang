<?php
require_once('base.php');

$obj = new stdClass;
$obj->status = 500;

$mobile		 = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
$sms_code = isset($_POST['sms_code'])? trim($_POST['sms_code']):NULL;

if($mobile==null)
{
	$obj->r = "手机号为空";
	CommonUtil::return_info($obj);
  return;
}

if($sms_code==null)
{
	$obj->r = "验证码不得为空";
	CommonUtil::return_info($obj);
  return;
}

$is_register=is_exist_mobile_user($mobile);
if (!$is_register) {
      $obj->r= "手机号未注册";
      CommonUtil::return_info($obj);
      return;
}
	$sql="select id from cixi_deal_msg_list where mobile_num= ? and code= ?";
  $para_value[]=$mobile;
	$para_value[]=$sms_code;
  $sms_count=  PdbcTemplate::query($sql,$para_value,PDO::FETCH_OBJ, 1);
  if (empty($sms_count))
    {
    	$obj->r= "验证码不正确";
		  CommonUtil::return_info($obj);
    	return;
    }

     $obj->status = 200;
	   CommonUtil::return_info($obj);
?>