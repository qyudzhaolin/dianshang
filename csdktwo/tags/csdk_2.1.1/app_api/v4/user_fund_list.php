<?php
require_once('base.php');
//基金   3.1号
$obj = new stdClass;
$obj->status = 500;
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$uid	= isset($_POST['uid'])? trim($_POST['uid']):NULL;
$sign_sn	= isset($_POST['sign_sn'])? trim($_POST['sign_sn']):NULL;


$fund_sql="
			SELECT fund.*,is_default_fund
			FROM
			cixi_fund as fund ,
			cixi_user_fund_relation as userfund
			WHERE
			fund.id = userfund.fund_id and fund.is_delete=1 and fund.status=2 and userfund.user_type=1 and userfund.user_id=? 
			order by is_default_fund desc,fund.establish_date desc,fund.id desc  ";

$para_final = array($uid);
$result = PdbcTemplate::query($fund_sql,$para_final,PDO::FETCH_OBJ,0);
$obj->fund_list = array(); 
 	if(!empty($result))
	{
		foreach ($result as $key => $val) 
		{
			$obj_fund = new stdClass; 
			$obj_fund->fund_id = is_null($val->id) ? ""		: $val->id;
			$obj_fund->fund_name = is_null($val->name) ? ""		: $val->name;
			$obj_fund->fund_short_name = is_null ( $val->short_name ) ? "" : $val->short_name;
 			$obj_fund->is_default_fund = is_null($val->is_default_fund) ? ""		: $val->is_default_fund;
 			array_push($obj->fund_list, $obj_fund) ;
   		}
 	}
	else
	{
	$obj->r = "暂无数据";
	}
	//返回数据
	$obj->status = 200;
	CommonUtil::return_info($obj);	
	
?>