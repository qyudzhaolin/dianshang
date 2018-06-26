<?php
require_once('base.php');
//请求融资阶段列表、分类列表、学历分类列表

$obj = new stdClass;
$obj->status = 500;
$period	= isset($_POST['period'])? trim($_POST['period']):NULL;
$cate	= isset($_POST['cate'])? trim($_POST['cate']):NULL;
$degree	= isset($_POST['degree'])? trim($_POST['degree']):NULL;

//轮次分类
$sql_period = "select 	id period_id
			    , mapname period_name  
				, brief   period_brief
		from	cixi_deal_period
		GROUP BY mapname 
		ORDER BY sort asc";
$result_period =  PdbcTemplate::query($sql_period);

//行业分类
$sql_case = "select id cate_id
				, name cate_name
		from	cixi_deal_cate order by sort ";

$result_case =  PdbcTemplate::query($sql_case);

//学历分类
$sql_degree = "select id degree_id
				, name degree_name
		from	cixi_education_degree order by sort desc";

$result_degree =  PdbcTemplate::query($sql_degree);

if (!empty($result_period)||!empty($result_case)||!empty($result_degree))
{
	$obj->status = 200;
	if (!is_null($period))
	{$obj->period_list = $result_period;}
	if(!is_null($cate))
	{$obj->cate_list = $result_case;}
	if(!is_null($degree))
	{$obj->degree_list = $result_degree;}
	CommonUtil::return_info($obj);
}

else
{
	$obj->r = "没有返回值";
	CommonUtil::return_info($obj);
}

?>