<?php
require_once('base.php');
/**********  2.23 单个修改用户信息  **********************/
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$sign_sn		= trim($_POST['sign_sn']);
$uid		= trim($_POST['uid']);
$img_user_logo =  isset($_POST['img_user_logo'])? trim($_POST['img_user_logo']):NULL;
$district =  isset($_POST['district'])? trim($_POST['district']):NULL;
$per_degree =  isset($_POST['per_degree'])? trim($_POST['per_degree']):NULL;
$img_card_logo =  isset($_POST['img_card_logo'])? trim($_POST['img_card_logo']):NULL;
$id_cardz_logo =  isset($_POST['id_cardz_logo'])? trim($_POST['id_cardz_logo']):NULL;
$id_cardf_logo =  isset($_POST['id_cardf_logo'])? trim($_POST['id_cardf_logo']):NULL;
 
$is_review =  isset($_POST['is_review'])? trim($_POST['is_review']):NULL;
$sql_check_uid_base = " select id from cixi_user where id = ?";
$para_check_uid_base = array($uid);
$user =  PdbcTemplate::query($sql_check_uid_base,$para_check_uid_base);

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
if(!is_null($id_cardz_logo))
{
		$update_fields .="id_cardz_logo = ?,";
		$para_value[] = $id_cardz_logo;
}
if(!is_null($id_cardf_logo))
{
		$update_fields .="id_cardf_logo = ?,";
		$para_value[] = $id_cardf_logo;
}
if(!is_null($per_degree))
{
		$update_fields .="per_degree = ?,";
		$para_value[] = $per_degree;
}
if(!is_null($is_review))
{
		$update_fields .="is_review = ?,";
		$para_value[] = $is_review;
}
 
 
if(!is_null($district))
{
	if(!empty($district))
		{
	    $province_city 	= explode("_",$district);
	    
	    $sql_province_city = " select id from cixi_region_conf where pid=  ?";
	    $para_province_city = array($province_city[0]);
	    $in_province_city =  PdbcTemplate::query($sql_province_city,$para_province_city);
	    $city_array = array ();
	    if ($in_province_city) {
	    	foreach($in_province_city as $k=>$v){
	    		$province_city_info=new stdClass;
	    		$province_city_info->id= is_null($v->id) ? "" : $v->id;
	    		array_push($city_array, $province_city_info->id) ;
	    	}
	    }
	    if (in_array($province_city[1],$city_array,true)){
				$update_fields .="city =?,";
				$para_value[] = $province_city[1];
	    }else {
				$update_fields .="city =?,";
				$para_value[] = $city_array[0];
	    }
			    $update_fields .="province =?,";
			    $para_value[] = $province_city[0];
		}
}
 
if (!is_null($img_user_logo)||!is_null($per_degree)||!is_null($district)||!is_null($img_card_logo)||!is_null($id_cardz_logo)||!is_null($id_cardf_logo)||!is_null($is_review)) 
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
$org_name =  isset($_POST['org_name'])? trim($_POST['org_name']):NULL;
$org_title =  isset($_POST['org_title'])? trim($_POST['org_title']):NULL;
$org_linkman =  isset($_POST['org_linkman'])? trim($_POST['org_linkman']):NULL;
$org_mobile =  isset($_POST['org_mobile'])? trim($_POST['org_mobile']):NULL;
$org_url =  isset($_POST['org_url'])? trim($_POST['org_url']):NULL;
$org_desc =  isset($_POST['org_desc'])? trim($_POST['org_desc']):NULL;
$para_value_org=array();
$update_fields_org="";

if(!is_null($org_name))
{
		$update_fields_org .="org_name = ?,";
		$para_value_org[] = $org_name;
}
if(!is_null($org_title))
{
		$update_fields_org .="org_title = ?,";
		$para_value_org[] = $org_title;
} 
if(!is_null($org_linkman))
{
		$update_fields_org .="org_linkman = ?,";
		$para_value_org[] = $org_linkman;
} 
if(!is_null($org_mobile))
{
		$update_fields_org .="org_mobile = ?,";
		$para_value_org[] = $org_mobile;
} 
if(!is_null($org_url))
{
		$update_fields_org .="org_url = ?,";
		$para_value_org[] = $org_url;
}  
if(!is_null($org_desc))
{
		$update_fields_org .="org_desc = ?,";
		$para_value_org[] = $org_desc;
}  
 
if (!is_null($org_name)||!is_null($org_title)||!is_null($org_linkman)||!is_null($org_mobile)||!is_null($org_url)||!is_null($org_desc)) 
{
	$update_fields_org=substr($update_fields_org,0,-1);
	$where_fields_org ="user_id=?";
	$para_value_org[] = $uid;
	$sql_org="update cixi_user_ex_investor set  ".$update_fields_org."  where ".$where_fields_org;
	$result_org =  PdbcTemplate::execute($sql_org,$para_value_org);
	if($result_org[0]===false){
		$obj->r = "修改公司信息失败";
		CommonUtil::return_info($obj);
		return;
	}else{
		$obj->status = 200;
		CommonUtil::return_info($obj);
	}

}
 
?>