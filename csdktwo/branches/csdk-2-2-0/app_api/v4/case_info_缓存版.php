<?php
require_once ('base.php');
require_once ('Cache/Lite.php');
$user_status = CommonUtil::verify_user ();
CommonUtil::check_status ( $user_status );

$obj = new stdClass ();
$obj->status = 500;

$deal_id = isset ( $_POST ['deal_id'] ) ? trim ( $_POST ['deal_id'] ) : NULL;
$version = isset ( $_POST ['version'] ) ? trim ( $_POST ['version'] ) : NULL;
$uid		= trim($_POST['uid']);
if (is_null ( $deal_id )) {
	$obj->r = "项目ID为空";
	CommonUtil::return_info ( $obj );
	return;
}
// 查看服务器有没有缓存
$version_db = check_version ( $deal_id );

$options = array (
		'cacheDir' => 'Cache/case/',
		'lifeTime' => 3600 * 24 * 30 * 365 
);
$cache = new Cache_Lite ( $options );
// var_dump($cache);

$deal_info_cache = $cache->get ( $deal_id );

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
				select id 	        deal_id
			    ,img_deal_logo 	    image_deal_logo
				,cate_choose 		deal_cates
			    ,name			    deal_name
			   	,com_time		    com_time
			    ,province		    province
			    ,capital        capital 
				,deal_brief  	deal_brief
				,deal_url 			deal_url
				,entry_info 			entry_info
				,solve_pain_info 			solve_pain_info
				,version 			version
				";
$sql_deal_rest = "
		from	cixi_deal	deal
		where 	is_case=1 and id = ?		";
$sql_deal = $sql_select . " " . $sql_deal_rest;
$para_deal = array ($deal_id);
$r_deal = PdbcTemplate::query ( $sql_deal, $para_deal, PDO::FETCH_OBJ, 1 );
/**
 * ********操作步骤信息*****************
 */
$sql_opera = "select img_deal_opera_steps,opera_steps_name,opera_steps_brief from cixi_deal_operation_steps  where deal_id = ? order by id asc";
$r_opera = PdbcTemplate::query ( $sql_opera, $para_deal );

// 增加用户的项目亮点
$sql_point = "select point_info from cixi_deal_brief_point where deal_id = ? order by id asc";
$result_brief_point = PdbcTemplate::query ( $sql_point, $para_deal );

/**********获取融资记录信息******************/
$event_list_sql="
			SELECT investor_payback,period,investor_amount,investor_time,is_csdk_partake FROM cixi_deal_trade_event AS event  WHERE `event`.deal_id=?  ORDER BY investor_time asc,id asc ";
$event_list_final = array ($deal_id);
$result_event_list = PdbcTemplate::query($event_list_sql,$event_list_final,PDO::FETCH_OBJ,0);
/**
 * ********获取团队信息*****************
 */

$sql_team = "
				select img_logo	img_logo
					,name	name
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
 * **********拼接数据**************************
 */

if (! empty ( $r_deal )) {
	$obj->status = 200;
	$obj->deal_id = is_null ( $r_deal->deal_id ) ? "" : $r_deal->deal_id;
	$obj->deal_name = is_null ( $r_deal->deal_name ) ? "" : $r_deal->deal_name;
	$obj->image_deal_logo = is_null ( $r_deal->image_deal_logo ) ? "" : $r_deal->image_deal_logo;
	$obj->deal_cates = is_null ( $r_deal->deal_cates ) ? "" : $r_deal->deal_cates;
	$obj->capital = is_null ( $r_deal->capital ) ? "" : $r_deal->capital;
	$obj->province = is_null ( $r_deal->province ) ? 0 : $r_deal->province;
	$obj->com_time = is_null ( $r_deal->com_time ) ? "" : $r_deal->com_time;
	
	$obj->deal_brief = is_null ( $r_deal->deal_brief ) ? "" : $r_deal->deal_brief;
	
	$obj->deal_url = is_null ( $r_deal->deal_url ) ? "" : $r_deal->deal_url;
	$obj->entry_info = is_null ( $r_deal->entry_info ) ? "" : $r_deal->entry_info;
	$obj->solve_pain_info = is_null ( $r_deal->solve_pain_info ) ? "" : $r_deal->solve_pain_info;
	
	$obj->version = is_null ( $r_deal->version ) ? "" : $r_deal->version;
	
	// 项目亮点
	$sql_point = "select point_info from cixi_deal_sign_point where deal_id =" . $r_deal->deal_id . " order by id asc limit 0,3";
	$result_point = PdbcTemplate::query ( $sql_point );
	$obj->deal_sign_point = array ();
	if (! empty ( $result_point )) {
		foreach ( $result_point as $k => $v ) {
			$point_info = new stdClass ();
			$point_info->title = is_null ( $v->point_info ) ? "" : $v->point_info;
			if ($point_info->title != "") {
				array_push ( $obj->deal_sign_point, $point_info );
			}
		}
	}
} else {
	$obj->r = "项目不存在";
	CommonUtil::return_info ( $obj );
	return;
}

if (! empty ( $r_team )) {
	$obj->deal_team = array ();
	foreach ( $r_team as $key => $val ) {
		$obj_team = new stdClass ();
		$obj_team->img_logo = is_null ( $val->img_logo ) ? "" : $val->img_logo;
		$obj_team->name = is_null ( $val->name ) ? "" : $val->name;
		$obj_team->title = is_null ( $val->title ) ? "" : $val->title;
		$obj_team->brief = is_null ( $val->brief ) ? "" : $val->brief;
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

if (! empty ( $result_brief_point )) {
	$obj->deal_brief_point = array ();
	foreach ( $result_brief_point as $k => $v ) {
		$point_info = new stdClass ();
		$point_info->title = is_null ( $v->point_info ) ? "" : $v->point_info;
		array_push ( $obj->deal_brief_point, $point_info );
	}
}

$obj->investor_record = array();
if(!empty($result_event_list)){
	foreach ($result_event_list as $k => $v) {
		$obj_record 	= new stdClass ;
		$obj_record->period = is_null($v->period) ? ""		: $v->period;
		$obj_record->investor_amount = is_null(number_format($v->investor_amount)) ? ""		: number_format($v->investor_amount);
		$obj_record->investor_payback = is_null(floatval($v->investor_payback)) ? ""		: (string)(floatval($v->investor_payback));
		$obj_record->investor_time = is_null($v->investor_time) ? ""		: $v->investor_time;
		$obj_record->is_csdk_partake = is_null($v->is_csdk_partake) ? ""		: $v->is_csdk_partake;
		array_push($obj->investor_record, $obj_record) ;
	}
}



{
	// 增加浏览次数
	$sql = "select id from cixi_deal_visit_log where deal_id = ? and user_id = ?"; // and ".time()." - create_time < 600";
	$para_value [] = $deal_id;
	$para_value [] = $uid;
	$result = PdbcTemplate::query ( $sql, $para_value, PDO::FETCH_OBJ, 1 );
	if (empty ( $result )) {
		$sql = "insert into cixi_deal_visit_log (deal_id,user_id,client_ip,create_time) values (?,?,?,?)";
		$param_visit [] = $deal_id;
		$param_visit [] = $uid;
		$param_visit [] = ""; // get_client_ip();
		$param_visit [] = time ();
		$result_visit = PdbcTemplate::execute ( $sql, $param_visit, PDO::FETCH_OBJ, 1 );
		// var_dump($result_visit);
		if ($result_visit [0] == true) {
			$sql = "update cixi_deal set view_count = view_count + 1 where id = ?";
			$para [] = $deal_id;
			PdbcTemplate::execute ( $sql, $para, PDO::FETCH_OBJ, 1 );
		}
	}
}

CommonUtil::return_info ( $obj );
if (! is_null ( $version )) {
	// 写入缓存文件
	$obj = json_encode ( $obj );
	$r = $cache->save ( $obj, $deal_id );
}

?>