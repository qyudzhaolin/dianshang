<?php 
 /**
 * 我的基金收益详情----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Version: 2.1.1
 * +----------------------------------------------------------------------
 * |
 */
require_once('base.php');
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);

$param = array(
		'uid' 	=> isset($_POST['uid'])? trim($_POST['uid']):NULL,
		'fund_id' 	=> isset($_POST['fund_id'])? trim($_POST['fund_id']):NULL
);

$result=request_service_api('Contents.Profit.ProfitInfo',$param);
$response=$result['response'] ;
if ($result['status']==0&&!empty($response)) {
	    $deal_list = array();
        foreach($response['deal_list'] as $key => $val) {
		$obj_final = new stdClass;
		$obj_final->deal_id =$val['deal_id'];
		$obj_final->fund_income =$val['fund_income'];
		$obj_final->deal_name =$val['deal_name'];
		$obj_final->assist_rate =$val['assist_rate'];
		array_push($deal_list, $obj_final) ;
	}
	$obj->deal_list = $deal_list;
	$obj->compute_formula="基金收益计算公式（以下均为税前收益）\n基金投资收益率＝基金投资收益（不含本金）／基金出资份额（本金）。";
	$obj->status = 200;
}
CommonUtil::return_info ( $obj );
?>