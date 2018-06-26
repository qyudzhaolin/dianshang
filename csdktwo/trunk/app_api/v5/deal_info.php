<?php
/**
 * 项目详情----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * |
 */
require_once ('base.php');
require_once ('../Cache/Lite.php');

$obj = new stdClass ();
$obj->status = 500;
$deal_id = isset ( $_POST ['deal_id'] ) ? trim ( $_POST ['deal_id'] ) : NULL;
$version = isset ( $_POST ['version'] ) ? trim ( $_POST ['version'] ) : NULL;
$deal_version = isset ( $_POST ['deal_version'] ) ? trim ( $_POST ['deal_version'] ) : NULL;
$uid = isset ( $_POST ['uid'] ) ? trim ( $_POST ['uid'] ) : '';
$sign_sn = isset ( $_POST ['sign_sn'] ) ? trim ( $_POST ['sign_sn'] ) : '';
$dealword = isset ($_POST ['dealword']) ? trim ($_POST ['dealword']) : NULL;
// 身份验证
if ($uid&&$sign_sn) {
	$user_type = "select is_review from cixi_user where user_type = 1 and id=? ";
	$para_user_type = array (
			$uid 
	);
	$result_check_user = PdbcTemplate::query ( $user_type, $para_user_type );
	$is_user_review = $result_check_user [0]->is_review;
	if($is_user_review==1){
	$user_status = CommonUtil::verify_user ();
    CommonUtil::check_status ( $user_status );
    }
} else {
	$is_user_review = "0";
}

// $type = isset($_POST['app_type'])? trim($_POST['app_type']):NULL;
if (is_null ( $deal_id )) {
	$obj->r = "项目ID为空";
	CommonUtil::return_info ( $obj );
	return;
}



// 查看服务器有没有缓存
$version_db = check_version ( $deal_id );
$options = array (
		'cacheDir' => '../Cache/test/',
		'lifeTime' => 3600 * 24 * 30 * 365 
);
$cache = new Cache_Lite ( $options );
// var_dump($cache);
$deal_info_cache = $cache->get ( $deal_id, $is_user_review );
if (! is_null ( $version )) {
	if ($deal_info_cache) {
		if ($version_db == $version) {
			// 没有新版本，直接返回300
			$obj->status = 300;
			CommonUtil::return_info ( $obj );
			return;
		} else {
			// 解析cache里的version
			$deal_info_cache = json_decode ( $deal_info_cache );
			$version_cache = $deal_info_cache->version;
			if ($version_db == $version_cache) {
				CommonUtil::return_info ( $deal_info_cache );
				return;
			}
		}
	}
}
/**
 * ********获取项目信息*****************
 */
$sql_select = "
		select id 	    deal_id
	    ,img_deal_app_cover 	image_deal_logo
		,cate_choose 		deal_cates
	    ,name			deal_name
	    ,period_id 		period_id
	    ,province		province
		,is_effect		is_effect
		,deal_brief  	deal_brief
		,deal_url 		 deal_url
        ,entry_info 	 entry_info
		,spot_app_img    entry_info_img
		,spot_app_img_scale   entry_info_img_scale
		,profession_info 			profession_info
		,profession_app_img         profession_info_img
		,profession_app_img_scale   profession_info_img_scale
		,operation_info 			operation_info  
		,vision_info 			vision_info
		,vision_app_img         vision_info_img
		,vision_app_img_scale   vision_info_img_scale
		,version 			version
		,is_case            is_case
				";
$sql_deal_rest = "
		from	cixi_deal	deal
		where 	id = ?
		";
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
 * ********数据图片信息*****************
 */
$sql_data_img = "select img_data_url from cixi_deal_data_img  where deal_id = ? order by id asc";
$r_data_img = PdbcTemplate::query ( $sql_data_img, $para_deal );
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
 * ********判断此项目是否已经被关注*****************
 */
$followed_exists = "
					select 	'x'
					from   	cixi_deal_focus_log
					where 	deal_id = ?
					and 	user_id = ?
					";
$para_followed_exists = array (
		$deal_id,
		$uid 
);
$r_followed_exists = PdbcTemplate::query ( $followed_exists, $para_followed_exists );
if (! empty ( $r_followed_exists )) {
	$obj->is_focus = 1;
} else {
	$obj->is_focus = 0;
}
/**
 * **********拼接数据**************************
 */
if (! empty ( $r_deal )) {
	$obj->status = 200;
	$obj->is_review = $is_user_review;
	$obj->deal_id = is_null ( $r_deal->deal_id ) ? "" : $r_deal->deal_id;
	$obj->deal_name = is_null ( $r_deal->deal_name ) ? "" : $r_deal->deal_name;
	$obj->image_deal_logo = is_null ( $r_deal->image_deal_logo ) ? "" : $r_deal->image_deal_logo;
	 
	$sql_period_name = "select name from cixi_deal_period where id='{$r_deal->period_id}'";
	$period_name = PdbcTemplate::query ( $sql_period_name, null, PDO::FETCH_OBJ, 1 );
	$obj->period_name = is_null ( $period_name->name ) ? "" : $period_name->name;
	 
	
	if ($dealword==1){
		//省市
		$province_sql = "select name from cixi_region_conf where id={$r_deal->province} ";
		$province_name = PdbcTemplate::query ( $province_sql, null, PDO::FETCH_OBJ, 1 );
		$obj->province = is_null ( $province_name->name ) ? "" : $province_name->name; 
		//关键词
		$case_sql = "select cate.name as cate_name from cixi_deal project, cixi_deal_cate cate, cixi_deal_select_cate selected where selected.deal_id = project.id and selected.cate_id = cate.id and project.id = {$r_deal->deal_id} ";
		$case_list = PdbcTemplate::query ( $case_sql, null, PDO::FETCH_OBJ, 0 ); 
		if (! empty ( $case_list )) {
			foreach ( $case_list as $k => $v ) {
				$obj_deal_cates [$k] = $v ->cate_name . '  ';
			}
		}
		$obj->deal_cates   = implode ( $obj_deal_cates );
    	}else{
		$obj->province = is_null ( $r_deal->province ) ? 0 : $r_deal->province;
		$obj->deal_cates = is_null ( $r_deal->deal_cates ) ? "" : $r_deal->deal_cates;
	    } 
	$obj->is_effect = is_null ( $r_deal->is_effect ) ? 0 : $r_deal->is_effect;
	$obj->deal_brief = is_null ( $r_deal->deal_brief ) ? "" : $r_deal->deal_brief;
	$obj->deal_url = is_null ( $r_deal->deal_url ) ? "" : $r_deal->deal_url;
	$obj->entry_info = is_null ( $r_deal->entry_info ) ? "" : $r_deal->entry_info;
	$obj->entry_info_img = is_null ( $r_deal->entry_info_img ) ? "" : $r_deal->entry_info_img;
	$obj->entry_info_img_scale = is_null ( $r_deal->entry_info_img_scale ) ? "" : $r_deal->entry_info_img_scale;
	$obj->operation_info = is_null ( $r_deal->operation_info ) ? "" : $r_deal->operation_info;
	//旧版本返回数据----空串开始
	$obj->financing_info ="";
	//旧版本返回数据----空串结束
	if ("1" == $is_user_review || $r_deal->is_case == '1') {
		$obj->vision_info = empty ( $r_deal->vision_info ) ? "" : $r_deal->vision_info;
		$obj->vision_info_img = is_null ( $r_deal->vision_info_img ) ? "" : $r_deal->vision_info_img;
		$obj->vision_info_img_scale = is_null ( $r_deal->vision_info_img_scale ) ? "" : $r_deal->vision_info_img_scale;
	} else {
		    if(empty($r_deal->vision_info )&&empty($r_deal->vision_info_img)){
		    $obj->vision_info = "";
		    $obj->vision_info_img ="";
		    $obj->vision_info_img_scale ="0.00";}
		    else{
		    $obj->vision_info = "认证后登录可查看更多内容";
		    $obj->vision_info_img ="";
		    $obj->vision_info_img_scale ="0.00";}
	}
	// var_dump($obj->vision_info);
	if (("1" == $is_user_review && $r_deal->is_effect == 2)) {

		//获取项目的最新融资轮次
		$sql_trade_event_last = "select period,pe_amount_plan,pe_sell_scale,investor_after_evalute  from cixi_deal_trade_event  where deal_id = ? and investor_record_type=1 order by id desc limit 0,1";
		$trade_event_last = PdbcTemplate::query ( $sql_trade_event_last, $para_deal, PDO::FETCH_OBJ, 1 );
		if (! empty ( $trade_event_last )) 
		{
			$sql_latest_period_name = "select name from cixi_deal_period where id='{$trade_event_last->period}'";
			$latest_period_name = PdbcTemplate::query ( $sql_latest_period_name, null, PDO::FETCH_OBJ, 1 );
			$obj->latest_period  = is_null ( $latest_period_name->name ) ? "" : $latest_period_name->name;
			$obj->pe_amount_plan = is_null ( number_format ( $trade_event_last->pe_amount_plan ) ) ? "" : number_format ( $trade_event_last->pe_amount_plan );
			$obj->pe_sell_scale = is_null ( ( string ) (floatval ( $trade_event_last->pe_sell_scale )) ) ? "" : ( string ) (floatval ( $trade_event_last->pe_sell_scale ));
			$pe_evaluate = $trade_event_last->pe_amount_plan / $trade_event_last->pe_sell_scale * 100;
		    $len = strpos ( $pe_evaluate, "." );
		    if ($len) {
			$pe_evaluate = substr ( $pe_evaluate, 0, $len );
		    }
		    $obj->pe_evaluate = is_null ( number_format ( $pe_evaluate ) ) ? "" : number_format ( $pe_evaluate );
		}
		else {
			$obj->latest_period ="";
			$obj->pe_amount_plan="";
			$obj->pe_sell_scale="";
			$obj->investor_after_evalute="";
		}
		$obj->pe_least_amount = "-";
		$obj->financing_amount = "-";
		  
	} else {
		$obj->investing_plan = "认证后登录可查看更多内容";
	}
	
	$obj->profession_info = is_null ( $r_deal->profession_info ) ? "" : $r_deal->profession_info;
	$obj->profession_info_img = is_null ( $r_deal->profession_info_img ) ? "" : $r_deal->profession_info_img;
	$obj->profession_info_img_scale = is_null ( $r_deal->profession_info_img_scale ) ? "" : $r_deal->profession_info_img_scale;
	$obj->version = is_null ( $r_deal->version ) ? "" : $r_deal->version;
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
		$obj_team->brief = is_null ( $val->brief ) ? "" : '●'.' '.str_replace("\n","\n".'●'.' ',$val->brief);
		
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
if ((! empty ( $r_data_img ) && "1" == $is_user_review) || $r_deal->is_case == '1') {
	$obj->deal_data_img = array ();
	foreach ( $r_data_img as $key => $val ) {
		$obj_data_img = new stdClass ();
		$obj_data_img->img_data_url = is_null ( $val->img_data_url ) ? "" : $val->img_data_url;
		array_push ( $obj->deal_data_img, $obj_data_img );
	}
} else {
	$obj->deal_data_img = "认证后登录可查看更多内容";
}
 
CommonUtil::return_info ( $obj );
if (! is_null ( $version )) {
	// 写入缓存文件
	$obj = json_encode ( $obj );
	$r = $cache->save ( $obj, $deal_id, $is_user_review );
}
?>