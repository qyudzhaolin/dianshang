<?php
require_once('base.php');
require_once('../function/Session.php');
//5.2	请求融资阶段列表
$obj = new stdClass;
$obj->status = 500;
//$user_status = CommonUtil::verify_user();
//CommonUtil::check_status($user_status);

$user_pwd 		= isset($_POST['user_pwd'])? trim($_POST['user_pwd']):NULL;
$mobile		    = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
//$login_mobile		    = isset($_POST['login_mobile'])? trim($_POST['login_mobile']):NULL;
//$user_type 		= isset($_POST['user_type'])? trim($_POST['user_type']):NULL;
$user_name		= isset($_POST['user_name'])? trim($_POST['user_name']):NULL;
$sms_code = isset($_POST['sms_code'])? trim($_POST['sms_code']):NULL;
$login_type = isset ( $_POST ['login_type'] ) ? trim ( $_POST ['login_type'] ) : NULL;

if($mobile==null)
{
	$obj->r = "手机号为空";
	CommonUtil::return_info($obj);
    return;
}
if($user_pwd==null)
{
	$obj->r = "密码为空";
	CommonUtil::return_info($obj);
    return;
}


/*if($login_mobile==null)
{
	$login_mobile=$mobile;
	//$obj->r = "手机号为空";
	//CommonUtil::return_info($obj);
    //return;
}*/

/*if($user_type==null)
{
	$obj->r = "未选择角色";
	CommonUtil::return_info($obj);
    return;
}
*/
if($user_name==null)
{
	$obj->r = "姓名为为空";
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
 
if(is_exist_mobile($mobile)=='1')
{
	$obj ->status = 500;
	$obj ->r = "该手机号码已注册";
	CommonUtil::return_info($obj);
	return;
}
elseif (is_exist_mobile($mobile)=='0')
{
	$obj ->status = 500;
	$obj ->r = "该手机号码已经禁用";
	CommonUtil::return_info($obj);
	return;
}
if($sms_code==null)
{
	$obj->r = "短信验证码为空";
	CommonUtil::return_info($obj);
    return;
}
$sql_sms_code="select id from cixi_deal_msg_list where mobile_num= ? and code= ?";
  $para_value_sms_code[]=$mobile;
	$para_value_sms_code[]=$sms_code;
  $sms_count=  PdbcTemplate::query($sql_sms_code,$para_value_sms_code,PDO::FETCH_OBJ, 1);
  if (empty($sms_count))
    {
    	$obj->r= "验证码不正确";
		  CommonUtil::return_info($obj);
    	return;
    }

//var_dump($result_sms_check);
	$user_pwd       =MD5($user_pwd);
	$para_value=array();
	$sql="insert into cixi_user (user_pwd,mobile,user_type,is_review,is_effect,user_name,login_time,create_time,update_time) 
	      values (?,?,?,?,?,?,?,?,?)";
	$para_value[]=$user_pwd;
	$para_value[]=$mobile;
	$para_value[]=1;
	$para_value[]=0;
	$para_value[]=1;
	$para_value[]=$user_name;
	$para_value[]=time();
	$para_value[]=time();
	$para_value[]=time();
	$result =  PdbcTemplate::execute($sql,$para_value);
	$user_type=1;
	if(ROLE_INVESTOR==$user_type)
	{
		$sql_investor_id = "select id from cixi_user where mobile = ?";
		$para_investor_id = array($mobile);
		$result_investor = PdbcTemplate::query($sql_investor_id,$para_investor_id,PDO::FETCH_OBJ, 1);
		if(!empty($result_investor))
		{
			$sql_investor_ex = "insert into cixi_user_ex_investor (user_id) values(?)";
			$para_investor_ex = array($result_investor->id);
			$result_insert = PdbcTemplate::execute($sql_investor_ex,$para_investor_ex);
			if(false==$result_insert[0])
			{
				$obj->status = 500;
				$obj->r = "DB Failed";
			}
		}
	}

    if ($result[0]==true)
    {
	   $obj->status = 200;
	   $obj_user=new stdClass;
	   $obj_user->uid=$result[1];
	   $obj_user->sign_sn=Session::set_token($obj_user->uid,$mobile	);
	   $obj->data=$obj_user;
		$sql="insert into cixi_user_notify (user_id,log_info,url,log_time,is_read) values (?,?,?,?,?)";
		$para_notify[]=$obj_user->uid;
		$para_notify[]="欢迎您使用磁斯达克-股权交易信息服务平台，您可以在磁斯达克网站（www.cisdaq.com ）的帮助中心内查看我们为您提供的服务。";
		$para_notify[]="";
		$para_notify[]=time();
		$para_notify[]=0;
		PdbcTemplate::execute($sql,$para_notify);
		//记录会员登录日志
		// 	  	 $Logs = new logger();
		// 	  	 $msg = array($user_info->id,$user_info->user_name,$user_info->mobile,date("Y-m-d H:i:s"),'APP');
		// 	  	 $Logs::write($msg,$Logs::INFO,$Logs::FILE,"../../public/logger/".date('y_m_d').".logger");
		if(!is_null($login_type)){
				
			$log_value=array();
			$log_sql="insert into cixi_user_login_log ( user_id,login_time,login_type)  values (?,?,?)";
			$log_value[]=$obj_user->uid;
			$time=time();
			$log_value[]=$time;
			$log_value[]=$login_type;
			$result =  PdbcTemplate::execute($log_sql,$log_value);
		}
	    CommonUtil::return_info($obj);
	   
        
    }
    else
    {
	  $obj->r = "注册用户失败";
	  CommonUtil::return_info($obj);
    }
?>