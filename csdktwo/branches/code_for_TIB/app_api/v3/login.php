<?php
require_once('base.php');
require_once('/fun/session.php');
//5.2	请求融资阶段列表
$obj = new stdClass;
$obj->status = 500;
//$user_status = CommonUtil::verify_user();
//CommonUtil::check_status($user_status);

$user_init_pwd 		= isset($_POST['user_pwd'])? trim($_POST['user_pwd']):NULL;
$mobile		    = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
$login_mobile		    = isset($_POST['login_mobile'])? trim($_POST['login_mobile']):NULL;
if($user_init_pwd==null||$mobile==null)
{
	$obj->r = "手机号或者密码为空";
	CommonUtil::return_info($obj);
    return;
}

if($login_mobile==null){
	$login_mobile=$mobile;
}
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
/*************判断是创业者还是投资人*******************/

	if(is_exist_mobile($mobile)==false)
	{
		$obj ->status = 500;
		$obj ->r = "用户不存在";
		CommonUtil::return_info($obj);
	    return;
	}
	

	$sql="select *,cixi_user.id id,cixi_user.user_pwd user_pwd,cixi_user.mobile mobile	from cixi_user 
	      left join cixi_sub_user
    	  on cixi_user.id=cixi_sub_user.user_id where cixi_sub_user.sub_mobile='{$mobile}' or cixi_user.mobile='{$mobile}'";

        $user_info=  PdbcTemplate::query($sql,null,PDO::FETCH_OBJ, 1);
        if(empty($user_info))
        {
        	
        		$obj ->status = 500;
			    $obj ->r = "用户不存在";
			    CommonUtil::return_info($obj);
	   	 	    return;
	   	
        }
     		
     	
   			if($user_info->sub_mobile==$mobile)
   			{
				$user_info->is_sub_user=1;//子账号
				//$user_info->mobile=$user_info->mobile;//多账号登陆
				if($user_info->sub_user_pwd!=$user_init_pwd)
			  	 {
				   $obj->r = "密码不正确";
				   CommonUtil::return_info($obj);
				   return;
			 	 }
   			}
   			else
   			{
   				  $user_info->is_sub_user=0;//主账号
   				  //$user_info->mobile=$mobile;//多账号登陆
   				   $user_pwd  =MD5($user_init_pwd);
   				   if($user_pwd != $user_info->user_pwd)
			  	 	{
					   $obj->r = "密码不正确";
					   CommonUtil::return_info($obj);
					   return;
			 		 }	
   			}

             //$user_info->mobile=$mobile;//多账号登陆
          
        	 
		$obj_user = new stdClass;
		$obj_user->uid=$user_info->id;
		$obj_user->is_sub_user=$user_info->is_sub_user;
		$obj_user->user_type=$user_info->user_type;
		$obj_user->sign_sn=Session::set_token($user_info->id,$login_mobile);
		$obj_user->img_domain=IMG_DOMAIN;
		$obj_user->bp_domain=BP_DOMAIN;
		$obj_user->uid=is_null($user_info->id) ? "":$user_info->id;
		$obj_user->img_user_logo=is_null($user_info->img_user_logo) ? ""	:$user_info->img_user_logo;
	    $obj_user->mobile=is_null($user_info->mobile) ? ""	:$user_info->mobile;
		$obj_user->img_card_logo=is_null($user_info->img_card_logo) ? ""	:$user_info->img_card_logo;
		$obj_user->is_review=is_null($user_info->is_review) ? 0	:$user_info->is_review;
		$obj_user->user_name=is_null($user_info->user_name) ? ""	:$user_info->user_name;
		$obj_user->per_sign=is_null($user_info->per_sign) ? "":$user_info->per_sign;
		$obj_user->per_brief=is_null($user_info->per_brief) ? ""	:$user_info->per_brief;
	    $obj_user->province=is_null($user_info->province) ? ""	:$user_info->province;
	    $obj_user->city=is_null($user_info->city) ? ""	:$user_info->city;
		$obj_user->focus_count=is_null($user_info->focus_count) ? 0:$user_info->focus_count;
		$obj_user->intend_count=is_null($user_info->intend_count) ? 0:$user_info->intend_count;

		if(ROLE_INVESTOR==$user_info->user_type)
		{
			$sql_select = "select investor.org_name 	org_name
					,investor.org_desc 	org_desc
					,investor.org_url 	org_url
					,investor.cate_choose   investor_cate
					,investor.period_choose investor_period
					,org_desc from cixi_user_ex_investor investor where investor.user_id =?";
			$para = array($user_info->id);
			$result_inverstor_user = PdbcTemplate::query($sql_select,$para,PDO::FETCH_OBJ, 1);
			//var_dump($result_inverstor_user);
			if(!empty($result_inverstor_user))
			{
				$obj_user->org_name = is_null($result_inverstor_user->org_name) ? "":$result_inverstor_user->org_name;
				$obj_user->org_desc = is_null($result_inverstor_user->org_desc) ? "":$result_inverstor_user->org_desc;
				//$obj_user->org_desc = is_null($result_inverstor_user->org_desc) ? "":$result_inverstor_user->org_desc;
				$obj_user->org_url = is_null($result_inverstor_user->org_url) ? "":$result_inverstor_user->org_url;
				$obj_user->investor_cate = is_null($result_inverstor_user->investor_cate) ? "":$result_inverstor_user->investor_cate;
				$obj_user->investor_period = is_null($result_inverstor_user->investor_period) ? "":$result_inverstor_user->investor_period;
				
			}


			//投资人亮点
			$sql_style="select point_info from cixi_investor_point where user_id = ".$obj_user->uid;
			$result_style=PdbcTemplate::query($sql_style);
			$obj_user->investor_point=array();
			if ($result_style) {
				foreach($result_style as $k=>$v){
					$style_info=new stdClass;
					//$style_info->id=$v->id;
					$style_info->title= is_null($v->point_info) ? "" : trim($v->point_info);
					array_push($obj_user->investor_point, $style_info) ;
				}
			}


			//投资人风格
			$sql_style="select id,style_info from cixi_investor_style where user_id = ".$obj_user->uid;
			$result_style=PdbcTemplate::query($sql_style);
			$obj_user->investor_style=array();
			if ($result_style) {
				foreach($result_style as $k=>$v){
					$style_info=new stdClass;
					//$style_info->id=$v->id;
					$style_info->title= is_null($v->style_info) ? "" : trim($v->style_info);
					array_push($obj_user->investor_style, $style_info) ;
				}
			}

			//投资人成绩
			$sql_mark="select mark_info from cixi_investor_mark where user_id = ".$obj_user->uid;
			$result_mark=PdbcTemplate::query($sql_mark);
			$obj_user->investor_mark=array();
			if ($result_mark) {

				foreach($result_mark as $k=>$v){
					$mark=new stdClass;
					//$mark_info->id=$v->id;
					$mark->title=is_null($v->mark_info) ? "" : trim($v->mark_info);
					array_push($obj_user->investor_mark, $mark) ;
				}
			}
		}
		if(ROLE_INITIATOR==$user_info->user_type)
		{
			$sql_initiator_project_id = "select project.id project_id,project.is_effect is_effect from cixi_deal project where 	project.user_id = ?";
			$para = array($user_info->id);
			$result_initiator_project_id = PdbcTemplate::query($sql_initiator_project_id,$para,PDO::FETCH_OBJ, 1);

			if(false==$result_initiator_project_id)
			{
				$obj_user->project_id = "";
				$obj_user->is_effect = "";
			}
			else
			{
				$obj_user->deal_id = is_null($result_initiator_project_id->project_id) ? "":$result_initiator_project_id->project_id;
				$obj_user->is_effect = is_null($result_initiator_project_id->is_effect) ? 0:$result_initiator_project_id->is_effect;
			}
		}

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
?>