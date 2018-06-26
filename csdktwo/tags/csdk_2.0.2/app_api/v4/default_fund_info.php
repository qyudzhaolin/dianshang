<?php
require_once ('base.php');
// 基金 3.1号
$obj = new stdClass ();
$obj->status = 500;
// $user_status = CommonUtil::verify_user ();
// CommonUtil::check_status ( $user_status );
$uid = isset ( $_POST ['uid'] ) ? trim ( $_POST ['uid'] ) : NULL;
$sign_sn = isset ( $_POST ['sign_sn'] ) ? trim ( $_POST ['sign_sn'] ) : NULL;
$fund_id = isset ( $_POST ['fund_id'] ) ? trim ( $_POST ['fund_id'] ) : NULL;
$fund_sql = "
			SELECT fund.* ,userfund.investor_amount,userfund.investor_rate,userfund.create_time
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id and status=2 and fund.is_delete=1 and userfund.user_id=? ";
if (! is_null ( $fund_id )) {
	$condition = "and   	fund.id 	= $fund_id ";
/**
 * ****所选基金ID***
 */
} else {
	$condition = " ";
}
$sql_page = "
				order by is_default_fund desc,fund.establish_date desc,fund.id desc limit 0,1";
$para_final = array (
		$uid 
);
$sql_final = $fund_sql . " " . $condition . " " . $sql_page;

$result = PdbcTemplate::query ( $sql_final, $para_final, PDO::FETCH_OBJ, 0 );
$fund_info = array ();
if (! empty ( $result )) {
	foreach ( $result as $key => $val ) {
		$obj = new stdClass ();
		$obj->fund_id = is_null ( $val->id ) ? "" : $val->id;
		$obj->fund_name = is_null ( $val->name ) ? "" : $val->name;
		$obj->fund_short_name = is_null ( $val->short_name ) ? "" : $val->short_name;
		$obj->fund_manager = is_null ( $val->manager ) ? "" : $val->manager;
		$obj->fund_total_amount = is_null (number_format($val->total_amount)) ? "" : number_format($val->total_amount);
		$obj->fund_establish_date = is_null ( $val->establish_date ) ? "" : $val->establish_date;
		$obj->fund_deadline = is_null ( $val->deadline ) ? "" : $val->deadline;
		$obj->fund_summary = is_null ( $val->summary ) ? "" : $val->summary;
		$obj->user_investor_amount = is_null ( number_format($val->investor_amount) ) ? "" : number_format($val->investor_amount);
		$obj->user_investor_rate = is_null ( floatval($val->investor_rate) ) ? "" : (string)(floatval($val->investor_rate));
		if (floatval($val->max_payback)==0){
			$obj->fund_max_payback='-';
		}else{
			$obj->fund_max_payback=(string)(floatval($val->max_payback));
		}
		if (floatval($val->average_payback)==0){
			$obj->fund_average_payback='-';
		}else{
			$obj->fund_average_payback=(string)(floatval($val->average_payback));
		}
		if (floatval($val->total_payback)==0){
			$obj->fund_total_payback='-';
		}else{
			$obj->fund_total_payback=(string)(floatval($val->total_payback));
		}
		// 基金总投资金额
		$sql_fund_sum = "
       		 select  sum(investor_amount) as sum_amount   
from cixi_deal as deal left join cixi_deal_trade_fund_relation as relation on deal.id=relation.deal_id,cixi_deal_trade_event as event 
where    deal.is_publish=2 and deal.is_effect in (2,3)  and fund_id=" . $obj->fund_id . " and is_csdk_fund=1
and deal.id=event.deal_id and relation.deal_trade_event_id=event.id and investor_record_type=2
 order by investor_amount desc ,investor_date desc ,relation.id desc ";
		$result_deal_sum = PdbcTemplate::query ( $sql_fund_sum, null, PDO::FETCH_OBJ, 1 );
		if (! empty ( $result_deal_sum )) {
	 	$obj->fund_investor_amount = is_null ( number_format($result_deal_sum->sum_amount) ) ? "" : number_format($result_deal_sum->sum_amount);
		}else{
	    $obj->fund_investor_amount ="";
		}
		// 基金管理团队
		$sql_manage_team = "
                select head_logo,name,position,summary from cixi_fund_manage_team where fund_id=" . $obj->fund_id . " order by id asc ";
		$result_team = PdbcTemplate::query ( $sql_manage_team );
		$obj->fund_manage_team = array ();
		if (! empty ( $result_team )) {
			foreach ( $result_team as $k => $v ) {
				$obj_team = new stdClass ();
				$obj_team->manage_head_logo = is_null ( $v->head_logo ) ? "" : $v->head_logo;
				$obj_team->manage_name = is_null ( $v->name ) ? "" : $v->name;
				$obj_team->manage_position = is_null ( $v->position ) ? "" : $v->position;
				$obj_team->manage_summary = is_null ( $v->summary ) ? "" : $v->summary;
				array_push ( $obj->fund_manage_team, $obj_team );
			}
		}
		
		// 基金投资项目
		$sql_fund_deal = "
                select event.deal_id,sum(investor_amount) as investor_amount,Max(investor_date) as investor_date,deal.s_name 
from cixi_deal as deal left join cixi_deal_trade_fund_relation as relation on deal.id=relation.deal_id,cixi_deal_trade_event as event 
where    deal.is_publish=2 and deal.is_effect in (2,3)  and fund_id=" . $obj->fund_id . " and is_csdk_fund=1
and deal.id=event.deal_id and relation.deal_trade_event_id=event.id and investor_record_type=2
GROUP BY event.deal_id order by investor_amount desc ,investor_date desc ,relation.id desc ";
		$result_deal = PdbcTemplate::query ( $sql_fund_deal );
		$obj->fund_investor_deal = array ();
		if (! empty ( $result_deal )) {
			foreach ( $result_deal as $k => $v ) {
				$obj_deal = new stdClass ();
				$obj_deal->deal_id = is_null ( $v->deal_id ) ? "" : $v->deal_id;
				$obj_deal->deal_short_name = is_null ( $v->s_name ) ? "" : $v->s_name;
				$obj_deal->investor_amount = is_null ( number_format($v->investor_amount) ) ? "" : number_format($v->investor_amount);
				$obj_deal->investor_date = is_null ( $v->investor_date ) ? "" : $v->investor_date;
				array_push ( $obj->fund_investor_deal, $obj_deal );
			}
		}
	}
	$obj->status = 200;
} else {
	$obj->r = "暂无数据";
	$obj->status = 286;
}
// 返回数据
CommonUtil::return_info ( $obj );

?>