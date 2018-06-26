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
 * | Version: 2.1
 * +----------------------------------------------------------------------
 * |
 */
require_once('base.php');
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$uid	= isset($_POST['uid'])? trim($_POST['uid']):NULL;
$sign_sn	= isset($_POST['sign_sn'])? trim($_POST['sign_sn']):NULL;
$user_type	= isset($_POST['user_type'])? trim($_POST['user_type']):NULL;
$final = array($uid);
$condition = "";
$compute_formula="";
$leading_word="";
if($user_type=="1"){	$condition = " and user_type=1 ";
$leading_word="尊敬的会员，您在磁斯达克平台投资收益如下：";
$compute_formula="基金收益计算公式（以下均为税前收益）：\n
您的账面预期总收益＝基金出资份额收益\n
预期收益率＝账面预期总收益（不含本金）／基金出资份额（本金）\n
基金出资份额收益（不含本金）：\n
指您在扣除基金出资本金和carry分成后所获得的投资收益，具体收益由基金初始出资份额占比决定";
}
elseif ($user_type=="2"){	$condition = " and user_type=3 ";
$leading_word="尊敬的会员，您在磁斯达克平台投资收益如下：";
$compute_formula="基金收益计算公式（以下均为税前收益）：\n
您的账面预期总收益＝基金carry分成收益\n
基金carry分成收益：\n
指基金获得投资收益后分给您协助募资的激励，具体分成比例由基金管理公司决定";
}
elseif ($user_type=="3"){	$condition = " and user_type in (1,3) ";
$leading_word="尊敬的会员，您在磁斯达克平台投资收益如下：";
$compute_formula="基金收益计算公式（以下均为税前收益）：\n
您的账面预期总收益＝基金carry分成收益＋基金出资份额收益\n
预期收益率＝账面预期总收益（不含本金）／基金出资份额（本金）\n
基金carry分成收益：\n
指基金获得投资收益后分给您协助募资的激励，具体分成比例由基金管理公司决定\n
基金出资份额收益（不含本金）：\n
指您在扣除基金出资本金和carry分成后所获得的投资收益，具体收益由基金初始出资份额占比决定";

}
$obj->compute_formula =$compute_formula;
$obj->leading_word =$leading_word;
if (is_null ( $user_type )) {
	$obj->r = "身份标识为空";
	CommonUtil::return_info ( $obj );
	return;
}
$profit_select="
		select
		sum(fund_income) as fund_income
		from
		cixi_user_fund_relation as relation, cixi_fund as fund
		where
        relation.fund_id=fund.id and fund.is_csdk_fund=1 and fund.status=2 and fund.is_delete=1 and
	    user_id=? and relation.fund_income>0 
		"; 
//	账面预期总收益

$profit_sql_user = $profit_select.$condition ;
$profit_result = PdbcTemplate::query($profit_sql_user,$final,PDO::FETCH_OBJ,1);
if ($profit_result){
$obj->expect_profit = is_null($profit_result->fund_income) ? "0" : number_format($profit_result->fund_income,2);
}

if($user_type=="2"||$user_type=="3"){

	//	基金Carry税前收益（合计）
	$profit_sql_Carry = $profit_select." and user_type=3" ;
	$profit_result_Carry = PdbcTemplate::query($profit_sql_Carry,$final,PDO::FETCH_OBJ,1);
	if ($profit_result_Carry){
		$obj->sum_carry_profit = is_null($profit_result_Carry->fund_income) ? "0" : number_format($profit_result_Carry->fund_income,2);
	}
	else {
		$obj->sum_carry_profit="0";
	}
}
else {
	$obj->sum_carry_profit ='0';
}

if($user_type=="1"||$user_type=="3"){

	//	基金出资份额税前收益（合计）
	$profit_sql_investor = $profit_select." and user_type=1" ;
	$profit_result_investor = PdbcTemplate::query($profit_sql_investor,$final,PDO::FETCH_OBJ,1);
	if ($profit_result_investor){
		$obj->sum_investor_profit = is_null($profit_result_investor->fund_income) ? "0" : $profit_result_investor->fund_income<="0.00"?"0.00":number_format($profit_result_investor->fund_income,2); 
	}
 
}
else {
	$obj->sum_investor_profit ='0';
}

if($user_type=="1"||$user_type=="3"){
//	基金出资份额
$amount_sql="
		select 
		sum(investor_amount) as investor_amount 
		from 
		cixi_user_fund_relation  as relation, cixi_fund as fund
		where 
		relation.fund_id=fund.id and fund.is_csdk_fund=1 and fund.status=2 and fund.is_delete=1 and
	    relation.user_id=? and relation.user_type=1  
			  "; 
//$amount_sql_user = $amount_sql.$condition ;
$amount_result = PdbcTemplate::query($amount_sql,$final,PDO::FETCH_OBJ,1);
if ($amount_result){
	$obj->sum_amount =is_null($amount_result->investor_amount) ? "0" :  $amount_result->investor_amount<="0.00"?"0.00":number_format($amount_result->investor_amount,2); 
}
 
//	预期收益率

//投资收益不等于0的出资份额的累加和
$fund_income_sql=$amount_sql." and relation.fund_income>0";
$fund_income_result = PdbcTemplate::query($fund_income_sql,$final,PDO::FETCH_OBJ,1);
if (!empty($profit_result->fund_income)&&!empty($fund_income_result->investor_amount)){
	$expect_rate=($profit_result->fund_income/$fund_income_result->investor_amount)*100;
    $obj->expect_rate =number_format($expect_rate,2);
}
else{
	$obj->expect_rate ='0';
}
}else {
	$obj->sum_amount ='';
	$obj->expect_rate ='';
}
$obj->investor_fund_list =new stdClass;
$obj->assist_fund_list =new stdClass;
//基金出资份额（税前收益） 投资人或者渠道合伙投资人可见
if($user_type=="1"||$user_type=="3"){
    $invest_fund_sql="
			SELECT 
    		short_name,
		    investor_amount,
    		fund_income
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id
			and user_type=1
		    and fund.is_delete=1
		    and fund.status=2
    		and fund.is_csdk_fund=1
		    and userfund.user_id=?
			order by fund_income desc  "; 
    $invest_result = PdbcTemplate::query($invest_fund_sql,$final,PDO::FETCH_OBJ,0); 
    if(!empty($invest_result))
    { 
    	$obj_invest_fund_out = new stdClass;
    	$obj_invest_fund_out->profit_title = '基金出资份额收益（税前）';
    	$obj_invest_fund_out->fund_list = array();
    	foreach ($invest_result as $key => $val)
    	{
    		$fund_list = new stdClass;
    		$fund_list->short_name = is_null ( $val->short_name ) ? "" : $val->short_name;
    		$fund_list->fund_amount = is_null ( $val->investor_amount ) ? "" : number_format($val->investor_amount);
    		$fund_list->fund_profit = is_null ( $val->fund_income ) ? "" : $val->fund_income<="0.00"?"-":number_format($val->fund_income,2);
    		array_push($obj_invest_fund_out->fund_list, $fund_list) ;
    	}
    	$obj->investor_fund_list=$obj_invest_fund_out ;
    }

}


//基金carry分成（税前收益） 渠道合伙人或者渠道合伙投资人可见
if($user_type=="2"||$user_type=="3"){
	$carry_fund_sql="
			SELECT
    		short_name,
		    investor_amount,
    		fund_income
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id
			and user_type=3
		    and fund.is_delete=1
			and fund.is_csdk_fund=1
		    and fund.status=2
		    and userfund.user_id=?
			order by fund_income desc  ";
	 
	$carry_result = PdbcTemplate::query($carry_fund_sql,$final,PDO::FETCH_OBJ,0);

	if(!empty($carry_result))
	{
		$obj_carry_fund_out = new stdClass;
		$obj_carry_fund_out->profit_title = '基金carry分成收益（税前）';
		$obj_carry_fund_out->fund_list = array();
		foreach ($carry_result as $key => $val)
		{
			$fund_list = new stdClass;
			$fund_list->short_name = is_null ( $val->short_name ) ? "" : $val->short_name;
			$fund_list->fund_amount = is_null ( $val->investor_amount ) ? "" : number_format($val->investor_amount);
			$fund_list->fund_profit = is_null ( $val->fund_income ) ? "" : $val->fund_income<="0.00"?"-":number_format($val->fund_income,2);
			array_push($obj_carry_fund_out->fund_list, $fund_list) ;
		}
		$obj->assist_fund_list=$obj_carry_fund_out ;
	}

}



$obj->status = 200;
CommonUtil::return_info ( $obj );
?>