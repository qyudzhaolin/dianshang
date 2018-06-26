<?php
/**
 * 新闻列表----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * |
 */
require_once('base.php');
define("PAGE_SIZE", 10);
$obj = new stdClass;
$obj->status = 500;
// 服务层新闻列表
$param = array(
		'page'  => isset($_POST['page'])?trim($_POST['page']):1 ,
		'n_class' => isset($_POST['news_type'])?trim($_POST['news_type']):1,
		'deal_id' 	=> isset($_POST['deal_id'])? trim($_POST['deal_id']):NULL,
		'is_deal_cate_news' 	=> isset($_POST['is_deal_cate_news'])? trim($_POST['is_deal_cate_news']):NULL,
);
$result=request_service_api('Contents.News.queryNewsByCondition',$param);
$resultstatus=$result['status'];
$response=$result['response'] ;
if ($resultstatus==0&&!empty($response)) {
	$news_info = array();
	foreach($response as $key => $val) {
		$obj_final = new stdClass;
		$obj_final->id=is_null($val['id']) ? "" 		: $val['id'];
		$obj_final->n_title=is_null($val['n_title']) ? "" 		: $val['n_title'];
		$obj_final->n_brief=is_null($val['n_brief']) ? "" 		: $val['n_brief'];
		$obj_final->n_list_img=is_null($val['n_list_img']) ? "" 		: $val['n_list_img'];
		$obj_final->n_source=is_null($val['n_source']) ? "" 		: $val['n_source'];
		$obj_final->create_time=is_null($val['create_time']) ? "" 		: $val['create_time'];
		array_push($news_info, $obj_final) ;
	}
}
else 
{
	$obj ->status = 500;
	$obj ->r = "无消息";
	CommonUtil::return_info($obj);
	return;
}
$obj->status = 200;
$obj->data = $news_info;
CommonUtil::return_info($obj);	
?>