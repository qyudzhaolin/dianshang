<?php
/**
 * 基金详情----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Version: 2.0|2.01
 * +----------------------------------------------------------------------
 * |
 */
require_once('base.php');
$obj = new stdClass();
$obj->status = 500;
$uid = isset ( $_POST ['uid'] ) ? trim ( $_POST ['uid'] ) : NULL;
$sign_sn = isset ( $_POST ['sign_sn'] ) ? trim ( $_POST ['sign_sn'] ) : NULL;
$fund_id = isset ( $_POST ['fund_id'] ) ? trim ( $_POST ['fund_id'] ) : NULL;
// $uid = 1569;
// $fund_id = 2;

/**** Get fund detail for each person ****************/
$fund_sql = "
			SELECT fund.* ,userfund.investor_amount,userfund.investor_rate,userfund.create_time,userfund.id as userfund_id
			FROM cixi_fund as fund, cixi_user_fund_relation userfund
			WHERE userfund.fund_id = fund.id
		    and fund.status=2  and userfund.user_type=1 and fund.is_delete=1 and fund.is_delete=1
			AND	  userfund.user_id = ?
			";
/***If user login first time, then we return the first fund as his/her default fund *******/
if(!is_null($fund_id)){
		
		$user_type_sql="select user_type from cixi_user_fund_relation where user_id=? and fund_id=?";
		$user_type_final = array($uid,$fund_id);
		 $result_user_type= PdbcTemplate::query($user_type_sql, $user_type_final, PDO::FETCH_OBJ, 0);
		 $user_type_item=  "";
		 if(!empty($result_user_type)){
		  foreach ($result_user_type as $key => $val)
		  {
		  $user_type_item= $user_type_item.$val->user_type.',';
		  }
		  $user_type_item=(substr($user_type_item,0,strlen($user_type_item)-1));
		  if ($user_type_item=="1,3"){
		  	$condition = "AND fund.id = ? ORDER BY  investor_amount desc,investor_rate desc
						  , fund.establish_date desc
						  , fund.id desc limit 0,1 ";
		  }
		  else{
		  	$condition = "AND fund.id = ? ORDER BY is_default_fund desc,investor_amount desc,investor_rate desc
						  , fund.establish_date desc
						  , fund.id desc limit 0,1 ";
		  }
		 }
		 else {
		 	$condition = "AND fund.id = ? ORDER BY is_default_fund desc,investor_amount desc,investor_rate desc
						  , fund.establish_date desc
						  , fund.id desc limit 0,1 ";
		 }			
	$para_final = array($uid,$fund_id);
}else{
	$para_final = array($uid);
	$condition = "ORDER BY is_default_fund desc,investor_amount desc,investor_rate desc
						  , fund.establish_date desc
						  , fund.id desc limit 0,1 ";
}
/********************/
//$para_final = array($uid);
$sql_final = $fund_sql." ".$condition;
 //var_dump($sql_final);
// var_dump($sql_final);
$result = PdbcTemplate::query($sql_final, $para_final, PDO::FETCH_OBJ, 0);

$fund_info = array();

if (! empty ( $result )) {
	foreach ( $result as $key => $val ) {
		//默认基金逻辑，如果没有设置最新一条为默认
		$is_default_fund_sql="
			SELECT
		    userfund.id
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id
		    and fund.is_delete=1
		    and fund.status=2
		    and fund.is_csdk_fund=1
		    and userfund.user_id=?
			and is_default_fund=1  ";
		
		$is_default_fund_final = array($uid);
		$is_default_fund = PdbcTemplate::query($is_default_fund_sql,$is_default_fund_final,PDO::FETCH_OBJ,0);
		if(empty($is_default_fund)){
			$default_sql="update cixi_user_fund_relation set is_default_fund=1   where id=".$val->userfund_id."  and user_id=".$uid;
			$result =  PdbcTemplate::execute($default_sql,NULL);
		}
		
		//结束
		$obj = new stdClass ();
		$obj->fund_id = is_null ( $val->id ) ? "" : $val->id;
		$obj->fund_name = is_null ( $val->name ) ? "" : $val->name;
		$obj->fund_short_name = is_null ( $val->short_name ) ? "" : $val->short_name;
		
		$obj->fund_total_amount = is_null (number_format($val->total_amount)) ? "" : number_format($val->total_amount);
		$obj->fund_establish_date = is_null ( $val->establish_date ) ? "" : $val->establish_date;
		$obj->fund_deadline = is_null ( $val->deadline ) ? "" : $val->deadline;
		$obj->fund_summary = is_null ( $val->summary ) ? "" : $val->summary;
		$obj->user_investor_amount = is_null ( number_format($val->investor_amount) ) ? "" : number_format($val->investor_amount);
		$obj->user_investor_rate = is_null ( floatval($val->investor_rate) ) ? "" : (string)(floatval($val->investor_rate));
		if (floatval($val->max_payback)<=1){
			$obj->fund_max_payback='-';
		}else{
			$obj->fund_max_payback=(string)(floatval($val->max_payback));
		}
		if (floatval($val->average_payback)<=1){
			$obj->fund_average_payback='-';
		}else{
			$obj->fund_average_payback=(string)(floatval($val->average_payback));
		}
		if (floatval($val->total_payback)<=1){
			$obj->fund_total_payback='-';
		}else{
			$obj->fund_total_payback=(string)(floatval($val->total_payback));
		}
		// 基金总投资金额
		$sql_fund_sum = "
						SELECT SUM(investor_amount) as sum_amount
						FROM cixi_deal a, cixi_deal_trade_event b, cixi_deal_trade_fund_relation c
						WHERE a.is_publish = 2
						AND   a.is_effect in (2,3)
						AND   a.id = b.deal_id
						AND   a.id = c.deal_id
						AND   b.investor_record_type = 2
						AND   b.id = c.deal_trade_event_id
						AND   c.is_csdk_fund = 1
						AND   c.fund_id = ?
						";
		$para_fund_id = array($obj->fund_id);
		
		$result_deal_sum = PdbcTemplate::query ( $sql_fund_sum, $para_fund_id, PDO::FETCH_OBJ, 1 );
		 
		if (! empty ( $result_deal_sum )) {
			$obj->fund_investor_amount = is_null ( number_format($result_deal_sum->sum_amount) ) ? "" : number_format($result_deal_sum->sum_amount);
		}else{
			$obj->fund_investor_amount ="";
		}
		//基金管理人
		$sql_fund_company = "
							SELECT  a.short_name 
							FROM cixi_fund_managers a
							WHERE id = (SELECT managers_id FROM cixi_fund WHERE id = ? )
							";
		$result_fund_company = PdbcTemplate::query($sql_fund_company, $para_fund_id, PDO::FETCH_OBJ, 1 );
		if (! empty ( $result_fund_company )) {
		 
			$obj->fund_manager =is_null( $result_fund_company->short_name ) ? "" :$result_fund_company->short_name;
		}
			
			// 基金管理团队
		$sql_manage_team = "
						select 
						fund.position as position,fund.brief as summary,team.user_logo as head_logo,users.user_name as name
						from 
						cixi_user_fund_relation as fund,
						cixi_fund_managers_team as team,
						cixi_user as users
					    where 
						fund.user_type=4 
						and fund.user_id= users.id
						and  fund.fund_id=". $obj->fund_id . "
						and fund.managers_team_id= team.id
						order by is_director asc,fund.id asc";
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
						SELECT a.id as deal_id, a.s_name, Max(investor_date) as investor_date, c.sort, sum(c.investor_amount) as investor_amount
						FROM cixi_deal a, cixi_deal_trade_event b, cixi_deal_trade_fund_relation c
						WHERE a.is_publish = 2
						AND a.is_effect in (2,3)  
						AND a.id = b.deal_id
						AND a.id = c.deal_id
						AND b.investor_record_type = 2
						AND b.id = c.deal_trade_event_id
						AND c.is_csdk_fund = 1
						AND c.fund_id = ?
						GROUP BY a.id,c.fund_id	
						ORDER BY sort ASC, investor_amount DESC ,investor_date DESC
						";
		$result_deal = PdbcTemplate::query ( $sql_fund_deal, $para_fund_id, PDO::FETCH_OBJ, 0 );
		$obj->fund_investor_deal = array ();
		//var_dump($result_deal);
		if (! empty ( $result_deal )) {
			foreach ( $result_deal as $key => $val ) {
				$obj_deal = new stdClass ();
				$obj_deal->deal_id = is_null ( $val->deal_id ) ? "" : $val->deal_id;
				$obj_deal->deal_short_name = is_null ( $val->s_name ) ? "" : $val->s_name;
				$obj_deal->investor_amount = is_null ( number_format($val->investor_amount) ) ? "" : number_format($val->investor_amount);
				$obj_deal->investor_date = is_null ( $val->investor_date ) ? "" : $val->investor_date;
				array_push ( $obj->fund_investor_deal, $obj_deal );
			}
		}
 
	}
	$obj->status = 200;	
} else {
	$obj-> r = "暂无数据";
	$obj-> status = 286;
}
// 返回数据
CommonUtil::return_info ( $obj );
?>