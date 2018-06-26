<?php
/**
 * 用户设置默认基金----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * |
 */
require_once('base.php');
//基金   3.1号
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$uid	= isset($_POST['uid'])? trim($_POST['uid']):NULL;
$sign_sn	= isset($_POST['sign_sn'])? trim($_POST['sign_sn']):NULL;
$default_fund_id =  isset($_POST['default_fund_id'])? trim($_POST['default_fund_id']):NULL;
$user_type =  isset($_POST['user_type'])? trim($_POST['user_type']):NULL; 
$para_value_other=array();
if (!is_null($default_fund_id)&&!is_null($user_type)) 
{
	$sql_other="update cixi_user_fund_relation set  is_default_fund=0  where    user_id=".$uid;
	$para_value_other[] = $default_fund_id;
	$result_other =  PdbcTemplate::execute($sql_other,$para_value_other);
 	$sql="update cixi_user_fund_relation set  is_default_fund=1   where fund_id=".$default_fund_id." and user_type=".$user_type." and user_id=".$uid;
 	$result =  PdbcTemplate::execute($sql,NULL);
 	
	if($result[0]===false){
		$obj->r = "修改默认基金失败";
		CommonUtil::return_info($obj);
		return;
	}else{
		$obj->status = 200;
		CommonUtil::return_info($obj);
	}

}
	
?>