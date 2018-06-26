<?php
/**
 * 案例详情----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.k
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * |
 */
require_once ('base.php');
// $user_status = CommonUtil::verify_user ();
// CommonUtil::check_status ( $user_status );
$obj = new stdClass ();
$obj->status = 500;
$deal_id = isset ( $_POST ['deal_id'] ) ? trim ( $_POST ['deal_id'] ) : NULL;
$version = isset ( $_POST ['version'] ) ? trim ( $_POST ['version'] ) : NULL;
$dealword = isset ($_POST ['dealword']) ? trim ($_POST ['dealword']) : NULL;
// $uid = trim($_POST['uid']);
if (is_null ( $deal_id )) {
	$obj->r = "项目ID为空";
	CommonUtil::return_info ( $obj );
	return;
}
/**
 * ********获取成功案例信息*****************
 */
$sql_select = "
				select id 	        deal_id
			    ,img_deal_app_cover 	    image_deal_logo
				,name			    deal_name
				,cate_choose 		deal_cates
			    ,province		    province
				,com_time		    com_time
				,deal_brief  	deal_brief
				,deal_url 			deal_url
			    ,capital        capital 
				,entry_info 	 entry_info
		        ,spot_app_img    entry_info_img
		        ,spot_app_img_scale   entry_info_img_scale
				,profession_info 			profession_info
		        ,profession_app_img         profession_info_img
		        ,profession_app_img_scale   profession_info_img_scale
				,operation_info             operation_info
				,vision_info 			vision_info
		        ,vision_app_img         vision_info_img
		        ,vision_app_img_scale   vision_info_img_scale
				";
$sql_deal_rest = "
		from	cixi_deal	deal
		where 	is_case=1 and id = ?		";
$sql_deal = $sql_select . " " . $sql_deal_rest;
$para_deal = array (
		$deal_id 
);
$r_deal = PdbcTemplate::query ( $sql_deal, $para_deal, PDO::FETCH_OBJ, 1 );
/**
 * ********操作步骤信息*****************
 */
$sql_opera = "select img_deal_opera_steps,opera_steps_name,opera_steps_brief from cixi_deal_operation_steps  where deal_id = ? order by id asc";
$r_opera = PdbcTemplate::query ( $sql_opera, $para_deal );

/**
 * ********获取融资记录信息*****************
 */
$event_list_sql = "
		SELECT event.id,period,investor_time ,sum(investor_amount) as investor_amount
FROM cixi_deal_trade_event AS event left join  cixi_deal_trade_fund_relation AS fund on (event.id=fund.deal_trade_event_id),cixi_deal_period as period 
		WHERE `event`.deal_id=?  and investor_record_type=2   and `event`.deal_id=fund.deal_id and `event`.period=period.id
group by event.id
ORDER BY  period.sort,id asc";
$para_event = array (
		$deal_id 
);
$result_event_list = PdbcTemplate::query ( $event_list_sql, $para_event );
// var_dump($result_event_list);
/**
 * ********获取团队信息*****************
 */
$sql_team = "
				select  name	name
					,title		title
					,brief 		brief
				from cixi_deal_team  
				where deal_id = ? 
				order by id asc
			";
$para_team = array (
		$deal_id 
);
$r_team = PdbcTemplate::query ( $sql_team, $para_team );
/**
 * ********数据图片信息*****************
 */
$sql_data_img = "select img_data_url from cixi_deal_data_img  where deal_id = ? order by id asc";
$r_data_img = PdbcTemplate::query ( $sql_data_img, $para_deal );
/**
 * **********拼接数据**************************
 */
if (! empty ( $r_deal )) {
	$obj->status = 200;
	$obj->deal_id = is_null ( $r_deal->deal_id ) ? "" : $r_deal->deal_id;
	$obj->deal_name = is_null ( $r_deal->deal_name ) ? "" : $r_deal->deal_name;
	$obj->image_deal_logo = is_null ( $r_deal->image_deal_logo ) ? "" : $r_deal->image_deal_logo;
	/**
	 * ********获取行业名称*****************
	 */
	$sql_deal_cates = "  select cate.name as cate_name from cixi_deal project, cixi_deal_cate cate, cixi_deal_select_cate selected where selected.deal_id = project.id and selected.cate_id = cate.id and project.id = " . $r_deal->deal_id . " ";
	$result_deal_cates = PdbcTemplate::query ( $sql_deal_cates );
	if (! empty ( $result_deal_cates )) {
		$deal_cates_string = array ();
		foreach ( $result_deal_cates as $k => $v ) {
			$obj_deal_cates = new stdClass ();
			$obj_deal_cates = $v->cate_name . '  ';
			array_push ( $deal_cates_string, $obj_deal_cates );
		}
	}
	$obj->deal_cates = implode ( $deal_cates_string );
	$obj->capital = is_null ( $r_deal->capital ) ? "" : $r_deal->capital;	 
	if ($dealword==1){
		//省市
		$province_sql = "select name from cixi_region_conf where id={$r_deal->province} ";
		$province_name = PdbcTemplate::query ( $province_sql, null, PDO::FETCH_OBJ, 1 );
		$obj->province = is_null ( $province_name->name ) ? "" : $province_name->name;
	}else{
		$obj->province = is_null ( $r_deal->province ) ? 0 : $r_deal->province;
	} 
	$obj->com_time = is_null ( $r_deal->com_time ) ? "" : $r_deal->com_time;
	$obj->deal_brief = is_null ( $r_deal->deal_brief ) ? "" : $r_deal->deal_brief;
	$obj->deal_url = is_null ( $r_deal->deal_url ) ? "" : $r_deal->deal_url;
	$obj->entry_info = is_null ( $r_deal->entry_info ) ? "" : $r_deal->entry_info;
	$obj->entry_info_img = is_null ( $r_deal->entry_info_img ) ? "" : $r_deal->entry_info_img;
	$obj->entry_info_img_scale = is_null ( $r_deal->entry_info_img_scale ) ? "" : $r_deal->entry_info_img_scale;
	$obj->profession_info = is_null ( $r_deal->profession_info ) ? "" : $r_deal->profession_info;
	$obj->profession_info_img = is_null ( $r_deal->profession_info_img ) ? "" : $r_deal->profession_info_img;
	$obj->profession_info_img_scale = is_null ( $r_deal->profession_info_img_scale ) ? "" : $r_deal->profession_info_img_scale;
	$obj->operation_info = is_null ( $r_deal->operation_info ) ? "" : $r_deal->operation_info;
	$obj->vision_info = empty ( $r_deal->vision_info ) ? "" : $r_deal->vision_info;
	$obj->vision_info_img = is_null ( $r_deal->vision_info_img ) ? "" : $r_deal->vision_info_img;
	$obj->vision_info_img_scale = is_null ( $r_deal->vision_info_img_scale ) ? "" : $r_deal->vision_info_img_scale;
} else {
	$obj->r = "项目不存在";
	CommonUtil::return_info ( $obj );
	return;
}
if (! empty ( $r_team )) {
	$obj->deal_team = array ();
	foreach ( $r_team as $key => $val ) {
		$obj_team = new stdClass ();
		$obj_team->name = is_null ( $val->name ) ? "" : $val->name;
		$obj_team->title = is_null ( $val->title ) ? "" : $val->title;
		$obj_team->brief = is_null ( $val->brief ) ? "" : '●' . ' ' . str_replace ( "\n", "\n" . '●' . ' ', $val->brief );
		array_push ( $obj->deal_team, $obj_team );
	}
}
if (! empty ( $r_opera )) {
	$obj->deal_operation_steps = array ();
	foreach ( $r_opera as $key => $val ) {
		$obj_opera = new stdClass ();
		$obj_opera->img_deal_operation_steps = is_null ( $val->img_deal_opera_steps ) ? "" : $val->img_deal_opera_steps;
		$obj_opera->operation_steps_name = is_null ( $val->opera_steps_name ) ? "" : $val->opera_steps_name;
		$obj_opera->operation_steps_brief = is_null ( $val->opera_steps_brief ) ? "" : $val->opera_steps_brief;
		array_push ( $obj->deal_operation_steps, $obj_opera );
	}
}
if (! empty ( $r_data_img )) {
	$obj->deal_data_img = array ();
	foreach ( $r_data_img as $key => $val ) {
		$obj_data_img = new stdClass ();
		$obj_data_img->img_data_url = is_null ( $val->img_data_url ) ? "" : $val->img_data_url;
		array_push ( $obj->deal_data_img, $obj_data_img );
	}
}

if (! empty ( $result_event_list ) && ! $result_event_list [0]->id == "") {
	$obj->investor_record = array ();
	foreach ( $result_event_list as $k => $v ) {
		$obj_record = new stdClass ();
		$obj_record->id = is_null ( $v->id ) ? "" : $v->id;
		$sql_latest_period_name = "select name from cixi_deal_period where id='{$v->period}'";
		$latest_period_name = PdbcTemplate::query ( $sql_latest_period_name, null, PDO::FETCH_OBJ, 1 );
		if (! empty ( $latest_period_name )) {
			$obj_record->period = is_null ( $latest_period_name->name ) ? "" : $latest_period_name->name;
		}
		$obj_record->investor_time = is_null ( $v->investor_time ) ? "" : $v->investor_time;
		$obj_record->investor_amount = is_null ( number_format ( $v->investor_amount ) ) ? "" : number_format ( $v->investor_amount );
		
		/**
		 * ********获取投资机构名称*****************
		 */
		$sql_event_investor = "	
					            select short_name,relation.is_csdk_fund as is_csdk_fund from cixi_deal_trade_fund_relation as  relation,cixi_fund as fund
where 
fund.id=relation.fund_id and relation.deal_id=? and relation.deal_trade_event_id=$v->id
order by create_time desc  ,relation.id desc  ";
		$para_deal = array (
				$deal_id 
		);
		$result_event_investor = PdbcTemplate::query ( $sql_event_investor, $para_deal );
		$obj_record->investor_organization = array ();
		if (! empty ( $result_event_investor )) {
			foreach ( $result_event_investor as $k => $v ) {
				$obj_event_investor = new stdClass ();
				$obj_event_investor->organization_name = is_null ( $v->short_name ) ? "" : $v->short_name;
				$obj_event_investor->is_csdk_partake = is_null ( $v->is_csdk_fund ) ? "" : $v->is_csdk_fund;
				if ($v->is_csdk_fund == 1) {
					$obj_record->fund_short_name = is_null ( $v->short_name ) ? "" : "磁斯达克";
				}
				array_push ( $obj_record->investor_organization, $obj_event_investor );
			}
		}
		array_push ( $obj->investor_record, $obj_record );
	}
}

CommonUtil::return_info ( $obj );
?>