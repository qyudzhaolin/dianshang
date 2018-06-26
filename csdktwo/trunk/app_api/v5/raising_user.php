<?php
/**
 * 发送募集基金投资意向金额，投资总监审核操作等----API
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
$user_id = isset ( $_POST ['user_id'] ) ? trim ( $_POST ['user_id'] ) : NULL;
if(empty($user_id)){
	$id=isset ( $_POST ['uid'] ) ? trim ( $_POST ['uid'] ) : NULL;
}
else{
	$id=$user_id;
}
$param = array (
		'uid' => isset ( $id ) ? trim ( $id ) : NULL,
		'fund_id' => isset ( $_POST ['fund_id'] ) ? trim ( $_POST ['fund_id'] ) : NULL,
		'amount' => isset ( $_POST ['amount'] ) ? trim ( $_POST ['amount'] ) : NULL,
		'remark' => isset ( $_POST ['remark'] ) ? trim ( $_POST ['remark'] ) : NULL,
		'turn_type' => isset ( $_POST ['turn_type'] ) ? trim ( $_POST ['turn_type'] ) : NULL,
);

$request=request_service_api('Contents.ExpectantInvestor.InvestmentAmount',$param);
$results=$request['status'] ;
if ($results==0||$results==3001) {
	$obj->status = 200;
}elseif($results==14) {
	$obj->r = "该意向投资人的投资份额已确认，不可驳回！";
}else{
	$obj->r = "操作失败";
}
CommonUtil::return_info ( $obj );
?>