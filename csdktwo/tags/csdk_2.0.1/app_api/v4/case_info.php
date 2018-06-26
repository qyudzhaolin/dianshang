<?php
require_once ('base.php');
require_once ('Cache/Lite.php');
// $user_status = CommonUtil::verify_user ();
// CommonUtil::check_status ( $user_status );
$obj = new stdClass ();
$obj->status = 500;
$deal_id = isset ( $_POST ['deal_id'] ) ? trim ( $_POST ['deal_id'] ) : NULL;
$version = isset ( $_POST ['version'] ) ? trim ( $_POST ['version'] ) : NULL;
//$uid		= trim($_POST['uid']);
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
				,entry_info 			entry_info
				,profession_info 			profession_info
				,operation_info             operation_info
				,vision_info                vision_info
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
 
/**********获取融资记录信息******************/
$event_list_sql="
			SELECT id,period,investor_amount,investor_time,is_csdk_partake,partake_fund FROM cixi_deal_trade_event AS event  WHERE `event`.deal_id=?  ORDER BY investor_time asc,id asc ";
$event_list_final = array ($deal_id);
$result_event_list = PdbcTemplate::query($event_list_sql,$event_list_final,PDO::FETCH_OBJ,0);
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
	/**********获取行业名称******************/
	$sql_deal_cates="  select cate.name as cate_name from cixi_deal project, cixi_deal_cate cate, cixi_deal_select_cate selected where selected.deal_id = project.id and selected.cate_id = cate.id and project.id = ".$r_deal->deal_id." "; 
	$result_deal_cates= PdbcTemplate::query($sql_deal_cates);
	if(!empty($result_deal_cates)){
		$deal_cates_string = array ();
		foreach ($result_deal_cates as $k => $v) {
			$obj_deal_cates 	= new stdClass ;
			$obj_deal_cates = $v->cate_name.'  ';
			array_push ( $deal_cates_string, $obj_deal_cates );
		}
	}
	$obj->deal_cates = implode($deal_cates_string);
	$obj->capital = is_null ( $r_deal->capital ) ? "" : $r_deal->capital;
	$obj->province = is_null ( $r_deal->province ) ? 0 : $r_deal->province;
	$obj->com_time = is_null ( $r_deal->com_time ) ? "" : $r_deal->com_time;
	$obj->deal_brief = is_null ( $r_deal->deal_brief ) ? "" : $r_deal->deal_brief;
	$obj->deal_url = is_null ( $r_deal->deal_url ) ? "" : $r_deal->deal_url;
	$obj->entry_info = is_null ( $r_deal->entry_info ) ? "" : $r_deal->entry_info;
	$obj->profession_info = is_null ( $r_deal->profession_info ) ? "" : $r_deal->profession_info;
	$obj->operation_info = is_null ( $r_deal->operation_info ) ? "" : $r_deal->operation_info;
	$obj->vision_info = is_null ( $r_deal->vision_info ) ? "" : $r_deal->vision_info;
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
if (! empty ( $r_data_img )  ) {
	$obj->deal_data_img = array ();
	foreach ( $r_data_img as $key => $val ) {
		$obj_data_img = new stdClass ();
		$obj_data_img->img_data_url = is_null ( $val->img_data_url ) ? "" : $val->img_data_url;
		array_push ( $obj->deal_data_img, $obj_data_img );
	}
}
$obj->investor_record = array();
if(!empty($result_event_list)){
	foreach ($result_event_list as $k => $v) {
		$obj_record 	= new stdClass ;
		$obj_record->id = is_null($v->id) ? ""		: $v->id;
		$obj_record->period = is_null($v->period) ? ""		: $v->period;
		$obj_record->investor_amount = is_null(number_format($v->investor_amount)) ? ""		: number_format($v->investor_amount);
		$obj_record->investor_time = is_null($v->investor_time) ? ""		: $v->investor_time;
		$obj_record->is_csdk_partake = is_null($v->is_csdk_partake) ? ""		: $v->is_csdk_partake;
		if ($v->is_csdk_partake==1){
			$sql_case_fund_name="
				select 	short_name  from cixi_fund  where  id=".$v->partake_fund."";
			$result_case_fund_name = PdbcTemplate::query ( $sql_case_fund_name, null, PDO::FETCH_OBJ, 1 );
			$obj_record->fund_short_name = is_null ( $result_case_fund_name->short_name ) ? "" : $result_case_fund_name->short_name;
		}
								/**********获取投资机构名称******************/
					            $sql_event_investor="	
					            select s_name from cixi_deal_event_investor where  event_id=".$obj_record->id." order by create_time desc ,investor_amount desc ,id desc";  
					            $result_event_investor= PdbcTemplate::query($sql_event_investor);
								$obj_record->investor_organization = array(); 
								if(!empty($result_event_investor)){
									foreach ($result_event_investor as $k => $v) {
										$obj_event_investor 	= new stdClass ;
 										$obj_event_investor->organization_name = is_null($v->s_name) ? ""		: $v->s_name;
										array_push($obj_record->investor_organization, $obj_event_investor) ;
										}
								}
		array_push($obj->investor_record, $obj_record) ;
	}
}
 
CommonUtil::return_info ( $obj );
?>