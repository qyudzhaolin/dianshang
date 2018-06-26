<?php
require_once('base.php');
$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$obj = new stdClass;
$obj->status = 500;

$uid		= trim($_POST["uid"]) ;
$sign_sn   	= trim($_POST["sign_sn"]) ;
$deal_id = isset($_POST['deal_id'])?trim($_POST['deal_id']):NULL;
$type = isset($_POST['type'])? trim($_POST['type']):NULL;


if(is_null($type)){
	$obj->r = "type不能为空";
	CommonUtil::return_info($obj);
	return;	
}
if(is_null($deal_id)){
	$obj->r = "项目ID不能为空";
	CommonUtil::return_info($obj);
	return;	
}

$type_array = explode('#',$type);
$para_ex[]=$deal_id;
if(in_array("deal_history_investor",$type_array)){
	$sql_ex="select img_logo,name,info from cixi_deal_history_investor  where deal_id = ? order by id asc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->deal_history_investor=array();
		foreach ($result_ex as $key => $val) {
				$data = new stdClass ;
				$data->img_logo =is_null($val->img_logo) ? ""	:$val->img_logo;
				$data->name =is_null($val->name) ? ""	:$val->name;
				$data->info =is_null($val->info) ? ""	:$val->info;
				array_push($obj->deal_history_investor, $data) ;
		}
	}
		
}
if(in_array("deal_interviem",$type_array)){
	$sql_ex="select problem_info,answer_info from cixi_deal_interviem  where deal_id = ? order by id asc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->deal_interviem=array();
		foreach ($result_ex as $key => $val) {
			$data = new stdClass ;
			$data->problem_info =is_null($val->problem_info) ? ""	:$val->problem_info;
			$data->answer_info =is_null($val->answer_info) ? ""	:$val->answer_info;
			array_push($obj->deal_interviem, $data) ;
		}
	}
			
}
if(in_array("deal_operation_steps",$type_array)){
	$sql_ex="select img_deal_opera_steps,opera_steps_name,opera_steps_brief from cixi_deal_operation_steps  where deal_id = ? order by id asc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->deal_operation_steps=array();
		foreach ($result_ex as $key => $val) {
			$data = new stdClass ;
			$data->img_deal_operation_steps =is_null($val->img_deal_opera_steps) ? ""	:$val->img_deal_opera_steps;
			$data->operation_steps_name =is_null($val->opera_steps_name) ? ""	:$val->opera_steps_name;
			$data->operation_steps_brief =is_null($val->opera_steps_brief) ? ""	:$val->opera_steps_brief;
			array_push($obj->deal_operation_steps, $data) ;
		}
	}
}
if(in_array("deal_profession_data",$type_array)){
	$sql_ex="select data_info from cixi_deal_profession_data  where deal_id = ? order by id asc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->deal_profession_data=array();
		foreach ($result_ex as $key => $val) {
			$data = new stdClass ;
			$data->data_info =is_null($val->data_info) ? ""	:$val->data_info;
			array_push($obj->deal_profession_data, $data) ;
		}
	}
}
if(in_array("deal_recommend",$type_array)){
	$sql_ex="select recommend_person,recommend_info from cixi_deal_recommend  where deal_id = ? order by id asc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->deal_recommend=array();
		foreach ($result_ex as $key => $val) {
			$data = new stdClass ;
			$data->recommend_person =is_null($val->recommend_person) ? ""	:$val->recommend_person;
			$data->recommend_info =is_null($val->recommend_info) ? ""	:$val->recommend_info;
			array_push($obj->deal_recommend, $data) ;
		}
	}
		
}
if(in_array("deal_team",$type_array)){
	$sql_ex="select img_logo,name,title,brief from cixi_deal_team  where deal_id = ? order by id asc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->deal_team=array();
		foreach ($result_ex as $key => $val) {
			$data = new stdClass ;
			$data->img_logo =is_null($val->img_logo) ? ""	:$val->img_logo;
			$data->name =is_null($val->name) ? ""	:$val->name;
			$data->title =is_null($val->title) ? ""	:$val->title;
			$data->brief =is_null($val->brief) ? ""	:$val->brief;
			array_push($obj->deal_team, $data) ;
		}
	}
}
if(in_array("deal_event",$type_array)){
	$sql_ex="select create_time,brief from cixi_deal_event  where deal_id = ? order by create_time desc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->deal_event=array();
		foreach ($result_ex as $key => $val) {
			$data = new stdClass ;
			$data->create_time =is_null($val->create_time) ? ""	:$val->create_time;
			$data->brief =is_null($val->brief) ? ""	:$val->brief;
			array_push($obj->deal_event, $data) ;
		}
	}

}
if(in_array("deal_trade_event",$type_array)){
	$sql_ex="select create_time,brief,price,period from cixi_deal_trade_event  where deal_id = ? order by create_time asc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->trade_event=array();
		foreach ($result_ex as $key => $val) {
			$data = new stdClass ;
			$data->create_time =is_null($val->create_time) ? ""	:$val->create_time;
			$data->brief =is_null($val->brief) ? ""	:$val->brief;
			$data->price =is_null($val->price) ? ""	:$val->price;
			$data->period =is_null($val->period) ? ""	:$val->period;
			array_push($obj->trade_event, $data) ;
		}
	}
		
}
if(in_array("deal_brief_point",$type_array)){
	$sql_ex="select point_info from cixi_deal_brief_point  where deal_id = ? order by id asc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->deal_brief_point=array();
		foreach ($result_ex as $key => $val) {
			$data = new stdClass ;
			$data->title =is_null($val->point_info) ? ""	:$val->point_info;
			array_push($obj->deal_brief_point, $data) ;
		}
	}
}
if(in_array("deal_data_img",$type_array)){
	$sql_ex="select img_data_url from cixi_deal_data_img  where deal_id = ? order by id asc";
	$result_ex = PdbcTemplate::query($sql_ex,$para_ex);
	if (!empty($result_ex)) {
		$obj->deal_data_img=array();
		foreach ($result_ex as $key => $val) {
			$data = new stdClass ;
			$data->img_data_url =is_null($val->img_data_url) ? ""	:$val->img_data_url;
			array_push($obj->deal_data_img, $data) ;
		}
	}
}
$obj->status = 200;
CommonUtil::return_info($obj);
?>