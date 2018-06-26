<?php
require_once('base.php');
/*******  7.1 修改用户信息  **********************/
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$sign_sn		= trim($_POST['sign_sn']);
$uid		= trim($_POST['uid']);
$img_user_logo =  isset($_POST['img_user_logo'])? trim($_POST['img_user_logo']):NULL;
$img_card_logo =  isset($_POST['img_card_logo'])? trim($_POST['img_card_logo']):NULL;

$user_name =  isset($_POST['user_name'])? trim($_POST['user_name']):NULL;
$district =  isset($_POST['district'])? trim($_POST['district']):NULL;
$personality_desc =  isset($_POST['personality_desc'])? trim($_POST['personality_desc']):NULL;
$org_name =  isset($_POST['org_name'])? trim($_POST['org_name']):NULL;
$preference_cate =  isset($_POST['preference_cate'])? trim($_POST['preference_cate']):NULL;
$preference_period =  isset($_POST['preference_period'])? trim($_POST['preference_period']):NULL;

$sql_check_uid_base = " select id,user_type from cixi_user where id = ?";
$para_check_uid_base = array($uid);
$user =  PdbcTemplate::query($sql_check_uid_base,$para_check_uid_base);
//var_dump($user[0]->id,$user[0]->user_type);
if(empty($user)){
	$obj->r = "用户不存在";
	CommonUtil::return_info($obj);
	return;
}

$para_value=array();
$update_fields="";
if(!is_null($img_user_logo))
{
		$update_fields .="img_user_logo = ?,";
		$para_value[] = $img_user_logo;
}

if(!is_null($img_card_logo))
{
		$update_fields .="img_card_logo = ?,";
		$para_value[] = $img_card_logo;
}
if(!is_null($user_name))
{
		$update_fields .="user_name = ?,";
		$para_value[] = $user_name;
}
if(!is_null($district))
{
	if(!empty($district))
		{
	    $province_city 	= explode("_",$district);
		$update_fields .="province =?,";
		$para_value[] = $province_city[0];
		$update_fields .="city =?,";
		$para_value[] = $province_city[1];
		}
}
/*if(!is_null($per_brief))
{
		$update_fields .="per_brief =?,";
		$para_value[] = $per_brief;
}*/
if (!is_null($img_user_logo)||!is_null($user_name)||!is_null($district)||!is_null($img_card_logo)) 
{
	$update_fields=substr($update_fields,0,-1);
	$where_fields ="id=?";
	$para_value[] = $uid;
	$sql="update cixi_user set  ".$update_fields."  where ".$where_fields;
	$result =  PdbcTemplate::execute($sql,$para_value);
	if($result[0]===false){
		$obj->r = "修改用户信息失败";
		CommonUtil::return_info($obj);
		return;
	}else{
		$obj->status = 200;
		CommonUtil::return_info($obj);
	}

}

//查看扩展表是否存在此用户信息
/*$para_value_user=array();
$para_value_user[]=$uid;
$sql="select id from cixi_user_ex_investor where user_id= ? ";
$result_ex =  PdbcTemplate::query($sql,$para_value_user);

$para_value_extend=array();
if(!empty($result_ex)){
	$update_fields_extend="";
	if(!is_null($preference_cate)){
		//扩展表
		$update_fields_extend .="cate_choose =?,";
		$para_value_extend[] = $preference_cate;
				
		//删除cixi_user_select_cate旧数据
		$sql_del="delete from cixi_user_select_cate where user_id= ?";
		$para_value_delete[] = $uid;
		$result_del =  PdbcTemplate::execute($sql_del,$para_value_delete);
	}
	if(!is_null($preference_period)){
		$update_fields_extend .="period_choose =?,";
		$para_value_extend[] = $preference_period;
	}
	if(!is_null($org_name)){
		$update_fields_extend .="org_name =?,";
		$para_value_extend[] = $org_name;
	}
	if (!is_null($preference_cate)||!is_null($preference_period)||!is_null($org_name)) {
	    $update_fields_extend=substr($update_fields_extend,0,-1);
		$where ="user_id=?";
		$para_value_extend[] = $uid;
		$sql_ex="update cixi_user_ex_investor set  ".$update_fields_extend."  where ".$where;
	}
}else{
		$insert_fields="";
		$insert_fields_value="";
		if(!is_null($preference_cate)){
			//扩展表
			$insert_fields .="cate_choose,";
			$insert_fields_value .="?".",";
			$para_value_extend[] = $preference_cate;
		}
		if(!is_null($preference_period)){
			  //扩展表
			$insert_fields .="period_choose,";
			$insert_fields_value .="?".",";
			$para_value_extend[] = $preference_period;
		}
		if(!is_null($org_name)){
			$insert_fields .="org_name,";
			$insert_fields_value .="?".",";
			$para_value_extend[] = $org_name;
		}
		if(!is_null($preference_cate)){
		   //cixi_user_select_cate插入新数据
		if(!empty($preference_cate)){
			$cate_array 	= explode("_",$preference_cate);
			$user_select_cate = " ";
			foreach($cate_array as $item_in_array)
			{		
				$user_select_cate .= "(?,?)," ;	
				$para_value_cate[]=$uid;
				$para_value_cate[]=$item_in_array;
			}
			$user_select_cate=substr($user_select_cate,0,-1);
			$sql_cate="insert into cixi_user_select_cate (user_id,cate_id) values ".$user_select_cate;
	    	$result_cate =  PdbcTemplate::execute($sql_cate,$para_value_cate);
	    	if ($result_cate[0]===false)
			{
				$obj->r = "修改用户信息失败";
				CommonUtil::return_info($obj);
				return;
			}
		}
			
	}
		if(!is_null($preference_cate)||!is_null($preference_period)||!is_null($org_name)){
			$insert_fields .="user_id";
			$insert_fields_value .="?";
			$para_value_extend[] = $uid;
			$sql_ex="insert into cixi_user_ex_investor (".$insert_fields.") values (".$insert_fields_value.")";

		}
		
}
	if(isset($sql_ex)){
		$result_extend =  PdbcTemplate::execute($sql_ex,$para_value_extend);
		if ($result_extend[0])
		{
			$obj->status = 200;
			CommonUtil::return_info($obj);
		}
		else
		{
			$obj->r = "修改用户信息失败";
			CommonUtil::return_info($obj);
			return;
		}
	}else{
		if ($result[0])
		{
			$obj->status = 200;
			CommonUtil::return_info($obj);
		}
		else
		{
			$obj->r = "修改用户信息失败";
			CommonUtil::return_info($obj);
		}

	}
*/
/*******  暂时不考虑网络攻击  **********************/


?>