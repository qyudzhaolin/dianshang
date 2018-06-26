<?php
/**
 * 我的基金列表----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Version: 2.2
 * +----------------------------------------------------------------------
 * |
 */
require_once('base.php');
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$param = array(
		'uid'  => isset($_POST['uid'])? trim($_POST['uid']):NULL,
		'label'  => isset($_POST['label'])? trim($_POST['label']):0,
);
$result=request_service_api('Contents.Funds.FundsList',$param);
$response=$result['response'] ;
if ($result['status']==0&&!empty($response)) {
	$obj->data=    $response;
	$obj->status = 200;
}
//返回数据
CommonUtil::return_info($obj);	
?>