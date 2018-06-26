<?php 
 /**
 * 我的基金收益列表----API
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
$param = array('uid'  => isset($_POST['uid'])? trim($_POST['uid']):NULL,);
$result=request_service_api('Contents.Profit.ProfitList',$param);
$response=$result['response'] ;
 
if ($result['status']==0&&!empty($response)) {
	
	$profit_list = array();
	foreach($response['profit_list'] as $key => $val) {
		$obj_final = new stdClass;
		$obj_final->fund_id =$val['fund_id'];
		$obj_final->short_name =$val['short_name'];
		$obj_final->user_type =$val['user_type'];
		$obj_final->investor_amount =$val['investor_amount'];
		$obj_final->fund_income =$val['fund_income'];
		if($val['user_type']==1){
		$obj_final->profit_title = '基金投资收益(税前)';
		$obj_final->profit_rate = $val['profit_rate'];
		}
		else{
	    $obj_final->profit_title = '基金carry分成收益(税前)';
		}
		array_push($profit_list, $obj_final) ;
	}

	$obj->expect_profit =$response['expect_profit'];
	$obj->profit_type =$response['profit_type'];
	$obj->expect_rate =$response['expect_rate'];
	$obj->compute_formula =$response['compute_formula'];
	$obj->sum_amount =$response['sum_amount'];
	$obj->sum_investor_profit =$response['sum_investor_profit'];
	$obj->sum_carry_profit =$response['sum_carry_profit'];
	$obj->profit_list = $profit_list;
	$obj->status = 200;
	

}
CommonUtil::return_info ( $obj );
?>