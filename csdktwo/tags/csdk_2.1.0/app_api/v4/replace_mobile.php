<?php
require_once('base.php');
 
//
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$uid		= trim($_POST["uid"]) ;
$sign_sn   	= trim($_POST["sign_sn"]) ;
$mobile		    = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
$sms_code = isset($_POST['sms_code'])? trim($_POST['sms_code']):NULL;
$replace_mobile		    = isset($_POST['replace_mobile'])? trim($_POST['replace_mobile']):NULL;
$replace_sms_code = isset($_POST['replace_sms_code'])? trim($_POST['replace_sms_code']):NULL;

if(!is_null($mobile)&&!is_null($sms_code))
{
			if($mobile==null)
			{
			$obj->r = "手机号为空";
			CommonUtil::return_info($obj);
			return;
			}
			if($sms_code==null)
			{
			$obj->r = "短信验证码为空";
			CommonUtil::return_info($obj);
			return;
			}
			if (check_mobile($mobile)==false)
			{
			$obj->r = "手机号格式不正确";
			CommonUtil::return_info($obj);
			return;
			}
			 $para_value_sms_code[]=$mobile;
			 $para_value_sms_code[]=$sms_code;
}
if(!is_null($replace_mobile)&&!is_null($replace_sms_code))        
{
			if($replace_mobile==null)
			{
			$obj->r = "新手机号为空";
			CommonUtil::return_info($obj);
			return;
			}
			if (check_mobile($replace_mobile)==false)
			{
			$obj->r = "新手机号格式不正确";
			CommonUtil::return_info($obj);
			return;
			}
			 
			if(is_exist_mobile($replace_mobile)=='1')
			{
				 
				$obj ->r = "新手机号码已注册";
				CommonUtil::return_info($obj);
				return;
			}
			elseif (is_exist_mobile($replace_mobile)=='0')
			{
				 
				$obj ->r = "该手机号码已被禁用";
				CommonUtil::return_info($obj);
				return;
			}
			if($replace_sms_code==null)
			{
			$obj->r = "短信验证码为空";
			CommonUtil::return_info($obj);
			return;
			}
			$para_value_sms_code[]=$replace_mobile;
			$para_value_sms_code[]=$replace_sms_code;
 }

$sql_sms_code="select id from cixi_deal_msg_list where mobile_num= ? and code= ?";
 
	 
  $sms_count=  PdbcTemplate::query($sql_sms_code,$para_value_sms_code,PDO::FETCH_OBJ, 1);
  if (empty($sms_count))
    {
    	$obj->r= "验证码不正确";
		  CommonUtil::return_info($obj);
    	return;
    }else{				

					if(!is_null($replace_mobile)&&!is_null($replace_sms_code)) 
					{
					$para_value[] = $replace_mobile; 
					$para_value[] = $uid;
					$sql="update cixi_user set mobile=?  where id= ? ";
					$result =  PdbcTemplate::execute($sql,$para_value);
					if($result[0]===false){
					$obj->r = "换绑手机号失败";
					CommonUtil::return_info($obj);
					return;
					} 

					}




		$obj->status = 200;
		CommonUtil::return_info($obj);
	}
 


    
?>