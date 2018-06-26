<?php
require_once('base.php');
/*******  5.1 请求分类列表  **********************/

$obj = new stdClass;
$obj->status = 500;
//$user_status = CommonUtil::verify_user();
//CommonUtil::check_status($user_status);

$sql = "select id cate_id
				, name cate_name
		from	cixi_deal_cate order by sort ";

$result =  PdbcTemplate::query($sql);
if (!empty($result))
{
	$obj->status = 200;
	$obj->data = $result;
	CommonUtil::return_info($obj);
}
else
{
	$obj->r = "没有返回类型值";
	CommonUtil::return_info($obj);
}

?>