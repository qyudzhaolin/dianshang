<?php
require_once('base.php');
/*******  6.1 创建更新项目  **********************/
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$sign_sn		= trim($_POST['sign_sn']);
$uid		= trim($_POST['uid']);
$deal_id =  isset($_POST['deal_id'])? trim($_POST['deal_id']):NULL;
$deal_name =  isset($_POST['deal_name'])? trim($_POST['deal_name']):NULL;
$img_deal_logo =  isset($_POST['img_deal_logo'])? trim($_POST['img_deal_logo']):NULL;
$project_desc =  isset($_POST['project_desc'])? trim($_POST['project_desc']):NULL;
$project_cate =  isset($_POST['project_cate'])? trim($_POST['project_cate']):NULL;
$company_name =  isset($_POST['company_name'])? trim($_POST['company_name']):NULL;
$title =  isset($_POST['title'])? trim($_POST['title']):NULL;
$period_id =  isset($_POST['period_id'])? trim($_POST['period_id']):NULL;

//项目名称、简介不能为空
if((is_null($project_name)&&isset($_POST['project_name']))||(is_null($project_desc)&&isset($_POST['project_desc']))){
	$obj->r = "创建或更新项目失败";
	CommonUtil::return_info($obj);
	return;
}

$para_value=array();
if(!is_null($project_id)){
	$update_fields="";
	if(!is_null($project_name)){
		$update_fields .="name = ?,";
		$para_value[] = $project_name;
	}
	if(!is_null($img_project_url)){
		$update_fields .="img_deal_logo = ?,";
		$para_value[] = $img_project_url;
	}
	if(!is_null($project_desc)){
		$update_fields .="brief =?,";
		$para_value[] = $project_desc;
	}
	if(!is_null($project_cate)){
		$update_fields .="cate_choose =?,";
		$para_value[] = $project_cate;

		//删除cixi_deal_select_cate旧数据
			$sql_del="delete from cixi_deal_select_cate where user_id= ?";
			$para_value_delete[] = $uid;
			$result_del =  PdbcTemplate::execute($sql_del,$para_value_delete);
	}
	if(!is_null($company_name)){
		$update_fields .="company_name =?,";
		$para_value[] = $company_name;
	}
	if(!is_null($title)){
		$update_fields .="company_title =?,";
		$para_value[] = $title;
	}
	if(!is_null($period_id)){
		$update_fields .="period_id = ?,";
		$para_value[] = $period_id;
	}
	$update_fields .="user_id=?";
	$para_value[] = $uid;
	if(!empty($update_fields)){
		$sql="update cixi_deal set  ".$update_fields."  where id=".$project_id;
	}
	
}else{
	$insert_fields="";
	$insert_fields_value="";
	if(!is_null($project_name)){
		$insert_fields .="name,";
		$insert_fields_value .="?".",";
		$para_value[] = $project_name;
	}
	if(!is_null($img_project_url)){
		$insert_fields .="img_deal_logo,";
		$insert_fields_value .="?".",";
		$para_value[] = $img_project_url;
	}
	if(!is_null($project_desc)){
		$insert_fields .="brief,";
		$insert_fields_value .="?".",";
		$para_value[] = $project_desc;
	}
	if(!is_null($project_cate)){
		$insert_fields .="cate_choose,";
		$insert_fields_value .="?".",";
		$para_value[] = $project_cate;
	}
	if(!is_null($company_name)){
		$insert_fields .="company_name,";
		$insert_fields_value .="?".",";
		$para_value[] = $company_name;
	}
	if(!is_null($title)){
		$insert_fields .="company_title,";
		$insert_fields_value .="?".",";
		$para_value[] = $title;
	}
	if(!is_null($period_id)){
		$insert_fields .="period_id,";
		$insert_fields_value .="?".",";
		$para_value[] = $period_id;
	}
	$insert_fields .="create_time,is_delete,user_id";
	$insert_fields_value .="?,?,?";
	$para_value[] = time();
	$para_value[] = 0;
	$para_value[] = $uid;
	$sql="insert into cixi_deal (".$insert_fields.") values (".$insert_fields_value.")";
}
if($sql){
	$result =  PdbcTemplate::execute($sql,$para_value);
	if ($result[0]===false)
	{
		$obj->r = "关注失败";
		CommonUtil::return_info($obj);
		return;
	}
}
//var_dump($result);
$deal_id=is_null($project_id)?$result[1] : $project_id;
if(!is_null($project_cate)){
		$cate_array 	= explode("_",$project_cate);
		$deal_select_cate = " ";
		foreach($cate_array as $item_in_array)
		{		
			$deal_select_cate .= "(?,?)," ;	
			$para_value_cate[]=$deal_id;
			$para_value_cate[]=$item_in_array;
		}
		$deal_select_cate=substr($deal_select_cate,0,-1);
		$sql_cate="insert into cixi_deal_select_cate (deal_id,cate_id) values ".$deal_select_cate;
    	$result_cate =  PdbcTemplate::execute($sql_cate,$para_value_cate);
    	if ($result_cate[0]===false)
		{
			$obj->r = "关注失败";

			CommonUtil::return_info($obj);
			return;
		}
}


if ($result[0])
 {
	$obj->status = 200;
	if(!is_null($project_id)){
		$obj->project_id = $project_id;
	}else{
		$obj->project_id = $result[1];
	}
		//$obj_final->focus_count			= is_null($val->focus_count) ? ""     		: $val ->focus_count;
	//var_dump($obj);
	CommonUtil::return_info($obj);
}
else
{
	$obj->r = "创建或更新项目失败";
	CommonUtil::return_info($obj);
}

?>