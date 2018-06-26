<?php
/**
 * 意向投资人列表----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Version: 2.2
 * +----------------------------------------------------------------------
 * | CreateTime: 2016年9月21日11:11:11
 * +----------------------------------------------------------------------
 * |
 */
require_once ('base.php');
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status); 
$param = array (
		'uid' => isset ( $_POST ['uid'] ) ? trim ( $_POST ['uid'] ) : NULL,
		'fund_id' => isset ( $_POST ['fund_id'] ) ? trim ( $_POST ['fund_id'] ) : NULL,
		'search' => isset ( $_POST ['search'] ) ? trim ( $_POST ['search'] ) : NULL,
		'page' => isset ( $_POST ['page'] ) ? trim ( $_POST ['page'] ) : 1 
);

$result=request_service_api('Contents.ExpectantInvestor.ExpectantInvestorList',$param);
$response=$result['response'] ;
if ($result['status']==0&&!empty($response)) {
	$obj->data=    $response;
	$obj->status = 200;
}
CommonUtil::return_info ( $obj );
?>