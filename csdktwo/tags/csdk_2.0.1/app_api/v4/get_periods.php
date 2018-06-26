<?php
require_once('base.php');
//5.2	请求融资阶段列表

$obj = new stdClass;
$obj->status = 500;
//$user_status = CommonUtil::verify_user();
//CommonUtil::check_status($user_status);

$sql = "select 	id period_id
			    , mapname period_name  
				, brief   period_brief
		from	cixi_deal_period
		GROUP BY mapname 
		ORDER BY sort asc";

$result =  PdbcTemplate::query($sql);

if (!empty($result))
{
	$obj->status = 200;
	$obj->data = $result;
	CommonUtil::return_info($obj);
}
else
{
	$obj->r = "没有返回阶段值";
	CommonUtil::return_info($obj);
}



      

?>