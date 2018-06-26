<?php
require_once('base.php');
require_once('/fun/session.php');
require_once('../../system/utils/logger.php');
//5.2	请求融资阶段列表
$obj = new stdClass;
$obj->status = 500;
//$user_status = CommonUtil::verify_user();
//CommonUtil::check_status($user_status);

$user_init_pwd 		= isset($_POST['user_pwd'])? trim($_POST['user_pwd']):NULL;
$mobile		    = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
//$login_mobile		    = isset($_POST['login_mobile'])? trim($_POST['login_mobile']):NULL;
if($user_init_pwd==null||$mobile==null)
{
	$obj->r = "手机号或者密码为空";
	CommonUtil::return_info($obj);
    return;
}

/*if($login_mobile==null){
	$login_mobile=$mobile;
}*/

if (check_mobile($mobile)==false)
{
    	$obj->r = "手机号格式不正确";
		CommonUtil::return_info($obj);
    	return;
}
if (check_pwd($user_init_pwd)==false)
{
    	$obj->r = "密码格式不正确";
		CommonUtil::return_info($obj);
    	return;
}


	if(is_exist_mobile($mobile)==false)
	{
		$obj ->status = 500;
		$obj ->r = "用户不存在";
		CommonUtil::return_info($obj);
	    return;
	}
	
  
		 
		 
		$sql_user_info = "	select ciuser.*,
							investor.user_id,investor.org_name,investor.org_desc,investor.org_url,investor.org_mobile,investor.org_linkman,investor.org_title,investor.vip_money,investor.vip_begin_time,investor.vip_end_time
							FROM
							cixi_user as ciuser
							LEFT JOIN cixi_user_ex_investor as investor ON ciuser.id = investor.user_id
							where 
							ciuser.mobile='{$mobile}'";
		/*$para_user_info[] = array();
		$para_user_info[] = $mobile;*/
		$user_info = PdbcTemplate::query($sql_user_info,null,PDO::FETCH_OBJ, 1);
	
		
		$user_pwd  =MD5($user_init_pwd);
   				   if($user_pwd != $user_info->user_pwd)
			  	 	{
					   $obj->r = "密码不正确";
					   CommonUtil::return_info($obj);
					   return;
			 		 }
		
		
		$obj_user = new stdClass;
		$obj_user->uid=$user_info->id;
 		$obj_user->sign_sn=Session::set_token($user_info->id,$mobile);
		$obj_user->img_domain=IMG_DOMAIN;
		$obj_user->bp_domain=BP_DOMAIN;
		$obj_user->uid=is_null($user_info->id) ? "":$user_info->id;
		$obj_user->img_user_logo=is_null($user_info->img_user_logo) ? ""	:$user_info->img_user_logo;
	    $obj_user->mobile=is_null($user_info->mobile) ? ""	:$user_info->mobile;
		$obj_user->img_card_logo=is_null($user_info->img_card_logo) ? ""	:$user_info->img_card_logo;
		$obj_user->id_cardz_logo=is_null($user_info->id_cardz_logo)?"":$user_info->id_cardz_logo;
		$obj_user->id_cardf_logo=is_null($user_info->id_cardf_logo)?"":$user_info->id_cardf_logo;
		$obj_user->is_review=is_null($user_info->is_review) ? 0	:$user_info->is_review;
		$obj_user->user_name=is_null($user_info->user_name) ? ""	:$user_info->user_name;
 	    $obj_user->province=is_null($user_info->province) ? ""	:$user_info->province;
	    $obj_user->city=is_null($user_info->city) ? ""	:$user_info->city;
		$obj_user->focus_count=is_null($user_info->focus_count) ? 0:$user_info->focus_count;
		$obj_user->intend_count=is_null($user_info->intend_count) ? 0:$user_info->intend_count;
		$obj_user->per_degree=is_null($user_info->per_degree)?"":$user_info->per_degree;

		$obj_user->org_name=is_null($user_info->org_name)?"":$user_info->org_name;
		$obj_user->org_desc=is_null($user_info->org_desc)?"":$user_info->org_desc;
		$obj_user->org_url=is_null($user_info->org_url)?"":$user_info->org_url;
		$obj_user->org_mobile=is_null($user_info->org_mobile)?"":$user_info->org_mobile;
		$obj_user->org_linkman=is_null($user_info->org_linkman)?"":$user_info->org_linkman;
		$obj_user->org_title=is_null($user_info->org_title)?"":$user_info->org_title;

		$obj_user->vip_money=is_null($user_info->vip_money)?"":$user_info->vip_money;
		$obj_user->vip_begin_time=is_null($user_info->vip_begin_time)?"":$user_info->vip_begin_time;
		$obj_user->vip_end_time=is_null($user_info->vip_end_time)?"":$user_info->vip_end_time;
		
  
	 
		 

		 $sql_notify_count = "select count(*) as number from cixi_user_notify where user_id ={$user_info->id} and is_read = 0";
		 $result_notify_count = PdbcTemplate::query($sql_notify_count,null,PDO::FETCH_OBJ, 1);
		  if(!empty($result_notify_count))
		  {
		  	$obj_user->msg_count=intval($result_notify_count->number);
		  }
		  else
		  {
		  	$obj_user->msg_count=0;
		  }

		 $obj->status = 200;
	  	 $obj->data = $obj_user;
	  	 CommonUtil::return_info($obj);
		 
		 $Logs = new logger();
	  	 $msg = array($user_info->id,$user_info->user_name,$user_info->mobile,date("Y-m-d H:i:s"),'APP');
	  	 $Logs::write($msg,$Logs::INFO,$Logs::FILE,"../../public/logger/".date('y_m_d').".logger");
	  	  
?>