<?php
require_once('base.php');
//
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$uid 		= isset($_POST['uid'])? trim($_POST['uid']):NULL;//登陆用户USERID
$user_id	= isset($_POST['user_id'])? trim($_POST['user_id']):NULL;//其他用户的USERID
$sign_sn	= isset($_POST['sign_sn'])? trim($_POST['sign_sn']):NULL;

if (is_null($user_id)) {
	$obj->r = "用户ID不能为空";
	CommonUtil::return_info($obj);
	return;
}
//$user_id = 251;
$sql = "select investor.user_id 		user_id
				,user_all.is_review 	is_review
				,investor.img_org_logo 	img_org_logo
				,investor.cate_choose 	preference_label
				,user_all.user_name 	user_name
				,user_all.user_type     user_type
				,org_desc 				org_brief
				,org_url 				org_url
				,per_brief 				per_brief
		from 	cixi_user				user_all
				,cixi_user_ex_investor	investor
		where 	investor.user_id = user_all.id 
		and 	investor.user_id = ?
		";
$para = array($user_id);

$result = PdbcTemplate::query($sql,$para);
$obj_final = new stdClass;

/******关注用户*****************/

$sql_follow = "
				select 	'x' 
				from 	cixi_user_focus_log
				where 	user_id = ?
				and 	focus_user_id = ?
				";
//$uid = 101;
//$user_id = 103;
$para_follow = array($uid, $user_id);
$result_follow = PdbcTemplate::query($sql_follow,$para_follow);
if(!empty($result))
{
	$obj_final ->user_id = is_null($result[0]->user_id) ? "" 		: $result[0]->user_id;
	$obj_final ->is_review = is_null($result[0]->is_review) ? "" 		: $result[0]->is_review;
	$obj_final ->img_org_logo = is_null($result[0]->img_org_logo) ? "" 		: $result[0]->img_org_logo;
	$obj_final ->preference_label = is_null($result[0]->preference_label) ? "" 		: $result[0]->preference_label;
	$obj_final ->user_name = is_null($result[0]->user_name) ? "" 		: $result[0]->user_name;
	$obj_final ->org_brief = is_null($result[0]->org_brief) ? "" 		: $result[0]->org_brief;
	$obj_final ->org_url = is_null($result[0]->org_url) ? "" 		: $result[0]->org_url;
	$obj_final ->per_brief = is_null($result[0]->per_brief) ? "" 		: $result[0]->per_brief;
	
		if($result[0]->user_type==ROLE_INVESTOR)
		{
			//投资人风格
			$sql_style="select id,style_info from cixi_investor_style where user_id = ? limit 0,3";
			$para_style[]=$user_id;
			$result_style=PdbcTemplate::query($sql_style,$para_style);
			$obj_final->investor_style=array();
			if ($result_style) {

				foreach($result_style as $k=>$v){
					$style_info=new stdClass;
					//$style_info->id=$v->id;
					$style_info->title= is_null($v->style_info) ? "" : trim($v->style_info);
					if($style_info->title!='')
					{
						array_push($obj_final->investor_style, $style_info) ;
					}
				}
			}

			//投资人成绩
			$sql_mark="select mark_info from cixi_investor_mark where user_id = ? limit 0,3";
			$para_mark[]=$user_id;
			$result_mark=PdbcTemplate::query($sql_mark,$para_mark);
			$obj_final->investor_mark=array();
			if ($result_mark) {

				foreach($result_mark as $k=>$v){
					$mark_info=new stdClass;
					//$mark_info->id=$v->id;
					$mark_info->title=is_null($v->mark_info) ? "" : trim($v->mark_info);
					if($mark_info->title!='')
						{
							array_push($obj_final->investor_mark, $mark_info) ;
						}
				}
			}
	}

	if(!empty($result_follow))
	{
		$obj_final ->is_focus = "1";
	}
	else
	{
		$obj_final ->is_focus = "0";	
	}
	$obj->status = 200;
	$obj->data = $obj_final;
	//var_dump($obj);
}
else
{
	$obj->r = "此投资人不存在";
}

CommonUtil::return_info($obj);	
?>