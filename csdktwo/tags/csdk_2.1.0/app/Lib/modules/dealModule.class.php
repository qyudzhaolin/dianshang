<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

function init_deal_page($deal_info)
{
	$GLOBALS['tmpl']->assign("page_title",$deal_info['name']);
 	if($deal_info['seo_title']!="")
	$GLOBALS['tmpl']->assign("seo_title",$deal_info['seo_title']);
	if($deal_info['seo_keyword']!="")
	$GLOBALS['tmpl']->assign("seo_keyword",$deal_info['seo_keyword']);
	if($deal_info['seo_description']!="")
	$GLOBALS['tmpl']->assign("seo_description",$deal_info['seo_description']);
 	$deal_info['tags_arr'] = preg_split("/[ ,]/",$deal_info['tags']);		

 }

class dealModule extends BaseModule
{	
	
	
	
	public function __construct(){
		//预览参数判断
		$preview = strim($_REQUEST['preview']);
		if (!$preview) {
        parent::__construct();
		parent::is_login();
		}
 	}
	
	public function index()
	{		
		//预览参数判断
		$preview = strim($_REQUEST['preview']);
		if (!$preview) {
        $this->show();
		}
	}
 	

	// 公司信息
	public function company_org()
	{	
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step <= 3)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];
		//$company = $GLOBALS['user_info'];
		$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal  where user_id = ".$id);
		//公司团队
		
		if(!empty($deal['user_id'])){		
			$deal_team = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_team where deal_id = ".$deal['id']." order by id");
		}
		if($GLOBALS['user_info']['is_review']!=0){
			$deal['company_brief']=nl2br($deal['company_brief']); 
		}
		//$GLOBALS['tmpl']->assign("company",$company);
		$GLOBALS['tmpl']->assign("deal_team",$deal_team);
		$GLOBALS['tmpl']->assign("deal",$deal);
		$GLOBALS['tmpl']->assign("company_org","company_org");
		//$GLOBALS['tmpl']->assign("user_id",$id);
		$GLOBALS['tmpl']->display("deal_company.html");
	}
	public function company_org_msg(){

		$id = $GLOBALS['user_info']['id'];
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step<5){
		$user['side_step'] = 5;
		$user['is_review'] = 2;}
		$in_updeta = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal  where user_id = ".$id);
		
		$deal_company['company_name'] = strim($_REQUEST['company_name']);
		$res=check_len($deal_company['company_name'],14,1,"公司名称");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal_company['company_brief'] = strim($_REQUEST['company_brief']);
		$res=check_len($deal_company['company_brief'],100,1,"公司简介");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal_company['company_title'] = strim($_REQUEST['company_title']);
		$res=check_len($deal_company['company_title'],14,1,"您担任的职务");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal_company['company_scope'] = strim($_REQUEST['company_scope']);
		$res=check_len($deal_company['company_scope'],28,1,"您的工作范围");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal_company['user_id'] = $id;
        $deal_company['is_effect'] = 0;	
		$deal_company['is_delete'] = 0;	
		$deal_company['create_time'] = time();
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		if(empty($in_updeta)){
			$result_ex=$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal_company,"INSERT","SILENT");
 			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}else{
			$result_ex=$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal_company,"UPDATE","user_id=".$id,"SILENT");
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}
		$in_updeta = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal  where user_id = ".$id);
		
		//项目团队
		$deal['name'] = $_REQUEST['team_name'];
		$deal['title'] =$_REQUEST['team_title'];
		$deal['intro'] =$_REQUEST['team_intro'];

		$res_point=$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_team where deal_id = ".$in_updeta['id']);
 
		foreach ($deal['name'] as $key => $value) {
			if(!empty($value) && !empty($deal['title'][$key]) && !empty($deal['intro'][$key])){
				$deal_team=array();
				$deal_team['deal_id']=$in_updeta['id'];
				$deal_team['name']=$value;
				$deal_team['title']=trim($deal['title'][$key]);
				$deal_team['brief']=trim($deal['intro'][$key]);
				$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal_team",$deal_team,"INSERT","SILENT");
				if(!$result){
					$res['status'] = 0;
					ajax_return($res);
				}				
			}
		}
		if(!$result_ex){
			$res['status'] = 0;
			ajax_return($res);
		}
		ajax_return($res);
	}

	public function preview_org_msg(){

		$id = $GLOBALS['user_info']['id'];
		 
		$in_updeta = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal  where user_id = ".$id);
		
		$deal_company['company_name'] = strim($_REQUEST['company_name']);
		$res=check_len($deal_company['company_name'],14,1,"公司名称");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal_company['company_brief'] = strim($_REQUEST['company_brief']);
		$res=check_len($deal_company['company_brief'],100,1,"公司简介");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal_company['company_title'] = strim($_REQUEST['company_title']);
		$res=check_len($deal_company['company_title'],14,1,"您担任的职务");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal_company['company_scope'] = strim($_REQUEST['company_scope']);
		$res=check_len($deal_company['company_scope'],28,1,"您的工作范围");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal_company['user_id'] = $id;
        $deal_company['is_effect'] = 0;	
		$deal_company['is_delete'] = 0;	
		$deal_company['create_time'] = time();
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		if(empty($in_updeta)){
			$result_ex=$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal_company,"INSERT","SILENT");
 			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}else{
			$result_ex=$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal_company,"UPDATE","user_id=".$id,"SILENT");
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}
		$in_updeta = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal  where user_id = ".$id);
		
		//项目团队
		$deal['name'] = $_REQUEST['team_name'];
		$deal['title'] =$_REQUEST['team_title'];
		$deal['intro'] =$_REQUEST['team_intro'];

		$res_point=$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_team where deal_id = ".$in_updeta['id']);
 
		foreach ($deal['name'] as $key => $value) {
			if(!empty($value) && !empty($deal['title'][$key]) && !empty($deal['intro'][$key])){
				$deal_team=array();
				$deal_team['deal_id']=$in_updeta['id'];
				$deal_team['name']=$value;
				$deal_team['title']=trim($deal['title'][$key]);
				$deal_team['brief']=trim($deal['intro'][$key]);
				$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal_team",$deal_team,"INSERT","SILENT");
				if(!$result){
					$res['status'] = 0;
					ajax_return($res);
				}				
			}
		}
		if(!$result_ex){
			$res['status'] = 0;
			ajax_return($res);
		}
		ajax_return($res);
	}

/*
		public function index_iniator()
	{		
		$GLOBALS['tmpl']->assign("home_user_info",$GLOBALS['user_info']);
		
		$GLOBALS['tmpl']->display("deal_edit.html");
	}*/
	 public function deal_show()
	{	
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step <= 1)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];
		$deal = $GLOBALS['user_info'];
		$home_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal  where user_id = ".$id);
		 
	 	//项目图片
        if(strim($home_user_info['img_deal_logo'])){
			$home_user_info['deal_real_img']=getQiniuPath($home_user_info['img_deal_logo'],'img');
			$home_user_info['deal_real_img']=$home_user_info['deal_real_img']."?imageView2/1/w/75/h/45";
		}
        //BP地址
        if(strim($home_user_info['bp_url'])){
			//$home_user_info['real_url_bp']=getQiniuPath($home_user_info['bp_url'],'bp');
			//使用pdfobject
			$home_user_info['real_url_bp']=APP_ROOT."/bp_viewer/get_bp.php?key=".$home_user_info['bp_url'];
			//var_dump($home_user_info['bp_url'],$home_user_info['real_url_bp']);
		}

		if(!empty($home_user_info['id'])){
		//项目亮点
		$deal_sign_point = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_sign_point where deal_id = ".$home_user_info[id]." order by id");
		//项目大事记
		$deal_event = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_event where deal_id = ".$home_user_info[id]." order by create_time desc");
		}
		

		foreach($deal_event as $k=>$v){
			$deal_event_list[ $k]['create_time']=date("Y-m-d",$v['create_time']);
			$deal_event_list[ $k]['brief']=$v['brief'];
		}
 
		//阶段
		$deal_period = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_period order by sort desc");
		//方向
		$deal_cate = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cate order by sort desc");

 
        if(!empty($home_user_info['cate_choose'])){
			$cate_choose=explode("_", $home_user_info['cate_choose']);
		}
		foreach ($deal_cate as $key => $value) {
			if(in_array($value['id'], $cate_choose)){
				$deal_cate[$key]['is_check']=1;
			}else{
				$deal_cate[$key]['is_check']=0;
			}
		}
		$user_ex_deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal  where user_id = ".$id);
	/*	 
		if($deal['img_card_logo']){
			$user_ex_deal['card_real_img']=getQiniuPath($deal['img_card_logo'],'img');
		}

		if($user_ex_deal['img_org_logo']){
			$user_ex_deal['logo_real_img']=getQiniuPath($user_ex_deal['img_org_logo'],'img');
		}*/
		if($deal['is_review']!=0){
			$home_user_info['deal_brief']=nl2br($home_user_info['deal_brief']); 
			$home_user_info['business_mode']=nl2br($home_user_info['business_mode']); 
		}
		//地区
		$region_pid = 0;
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['id'] == $home_user_info['province'])
			{
				$region_lv2[$k]['selected'] = 1;
				$region_pid = $region_lv2[$k]['id'];  //var_dump($region_lv2[$k]);
				$region_pname_province = $region_lv2[$k]['name'];  //var_dump($region_lv2[$k]);
				break;
			}
		}
		$region_lv3=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 3 and pid=".$region_pid); 
		foreach($region_lv3 as $k=>$v)
		{
			if($v['id'] == $home_user_info['city'])
			{
				$region_lv3[$k]['selected'] = 1;
				$region_pid = $region_lv3[$k]['id'];  //var_dump($region_lv2[$k]);
				$region_pid = $region_lv3[$k]['id'];  //var_dump($region_lv2[$k]);
				$region_pname_city = $region_lv3[$k]['name']; 
				break;
			}
		}  //二级地址
		$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
		//$home_user_info['city']=is_null($home_user_info['city'])? 0: $home_user_info['city'];
	    
		//var_dump($home_user_info);die();
		$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		$GLOBALS['tmpl']->assign("region_pname_province",$region_pname_province);
		$GLOBALS['tmpl']->assign("region_pname_city",$region_pname_city);

		$GLOBALS['tmpl']->assign("deal",$deal);
		$GLOBALS['tmpl']->assign("deal_event_list",$deal_event_list);
		$GLOBALS['tmpl']->assign("deal_sign_point",$deal_sign_point);
		 
		$GLOBALS['tmpl']->assign("user_select_cate",$user_select_cate);
		$GLOBALS['tmpl']->assign("deal_period",$deal_period);
		$GLOBALS['tmpl']->assign("deal_cate",$deal_cate);
		$GLOBALS['tmpl']->assign("user_ex_deal",$user_ex_deal);
		$GLOBALS['tmpl']->assign("home_user_info",$home_user_info);
		$GLOBALS['tmpl']->assign("deal_show","deal_show");
		$GLOBALS['tmpl']->assign("br","\r\n");

		$GLOBALS['tmpl']->display("deal_edit.html");
		
			 
		 
	}

	public function deal_org_msg(){
		 
		$id = $GLOBALS['user_info']['id'];
 		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$deal['img_deal_logo'] = strim($_REQUEST['img_deal_logo']);
		$deal['name'] = strim($_REQUEST['dealname']);
		$res=check_len($deal['name'],14,1,"项目名称");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal['deal_sign'] = strim($_REQUEST['deal_sign']);
		$res=check_len($deal['deal_sign'],34,1,"项目简介");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal['period_id'] = implode("_",$_REQUEST['user_select_periods']);
		/*if(empty($deal['period_id'])){
			$res['status'] = 0;
			$data['info']="选择阶段不能为空";
			ajax_return($res);
		}*/
		$deal['province'] = strim($_REQUEST['province']);
		$res=check_region($deal['province'],"地区");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal['city'] = strim($_REQUEST['city']);
		/*$res=check_region($deal['city'],"地区");
		if($res['status']==0){
          	ajax_return($res);
		}*/
		$deal['deal_url'] = strim($_REQUEST['deal_url']);
		$res=check_url($deal['deal_url']);
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal['deal_brief'] = strim($_REQUEST['deal_brief']);
		$res=check_len($deal['deal_brief'],200,1,"项目详细");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal['business_mode'] = strim($_REQUEST['business_mode']);
		$res=check_len($deal['business_mode'],100,1,"商业模式");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal['bp_url'] = strim($_REQUEST['bp_url']);
		$res=check_region($deal['bp_url'],"bp");
		if($res['status']==0){
          	ajax_return($res);
		}

        $deal['cate_choose'] = implode("_",$_REQUEST['deal_select_cate']);
        /*if(empty($deal['cate_choose'])){
			$res['status'] = 0;
			$data['info']="选择方向不能为空";
			ajax_return($res);			
		}*/
		$deal['user_id'] = $id;
        $deal['is_effect'] = 0;	
		$deal['is_delete'] = 0;	
		$deal['create_time'] = time();


		$in_updeta = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal  where user_id = ".$id);
		$side_step = intval($GLOBALS['user_info']['side_step']);
		if($side_step<3){$user['side_step']=3;}else{$user['side_step']=$side_step;}
 		if (empty($in_updeta)) {
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal,"INSERT","SILENT");
 			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}else{
		    $result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal,"UPDATE","user_id=".$id,"SILENT");
		    $result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}
		$in_updeta = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal  where user_id = ".$id);
		
        //方向 
		$deal['deal_select_cate'] = $_REQUEST['deal_select_cate'];
		$res_period=$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_select_cate where deal_id = ".$in_updeta[id]);
		if(count($deal['deal_select_cate']) == 0){
			$res['status'] = 0;
			ajax_return($res);
		}
		foreach ($deal['deal_select_cate'] as $key => $value) {
			if(!empty($value)){								
				$deal_select_cate=array();
				$deal_select_cate['deal_id']=$in_updeta[id];
				$deal_select_cate['cate_id']=$value;
				$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal_select_cate",$deal_select_cate,"INSERT","SILENT");
				if(!$result){
					$res['status'] = 0;
					$res['info'] = '增加项目分类失败';
					ajax_return($res);
				}			
			}
		}
		//用户亮点
		$deal['deal_sign_point'] = $_REQUEST['point_info'];
		$res_point=$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_sign_point where deal_id = ".$in_updeta[id]);
		foreach ($deal['deal_sign_point'] as $key => $value) {
			if(!empty($value)){
			$deal_sign_point=array();
			$deal_sign_point['deal_id']=$in_updeta[id];
			$deal_sign_point['point_info']=$value;
			$deal_sign_point['create_time']=time();
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal_sign_point",$deal_sign_point,"INSERT","SILENT");
			if(!$result){
				$res['status'] = 0;
				$res['info'] = '增加项目亮点失败';
				ajax_return($res);
			}
			}
		}
		//项目大事记
		$deal['deal_create_time'] = $_REQUEST['create_time_event'];
		$deal['brief_event'] = $_REQUEST['brief_event'];
		$res_point=$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_event where deal_id = ".$in_updeta[id]);
		foreach ($deal['deal_create_time'] as $key => $value) {
			if(!empty($deal['brief_event'][$key]) && !empty($value)){
			$deal_log=array();
			$deal_log['deal_id']=$in_updeta[id];
			$deal_log['brief']=trim($deal['brief_event'][$key]);
			$deal_log['create_time']=strtotime(trim($value));
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal_event",$deal_log,"INSERT","SILENT");
			if(!$result){
				$res['status'] = 0;
				$res['info'] = '增加项目进程失败';
				ajax_return($res);

			}
			}
		}

		if(!$result){
			$res['status'] = 0;
			$res['info'] = '更新项目信息失败';
			ajax_return($res);
		}
		ajax_return($res);
	}

  

	public function estp_finacing(){
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step <= 2)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];
		$user_info = $GLOBALS['user_info'];
		$deal = $GLOBALS['db']->getRow("select id,user_id,pe_amount_plan,pe_sell_scale,pe_least_amount from ".DB_PREFIX."deal where user_id = ".$id);
		//判断deal_id是否存在
		if(!empty($deal['user_id'])){		
			$deal_trade_event = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_trade_event where deal_id = ".$deal['id'] ." order by create_time desc");
		}
		foreach($deal_trade_event as $k=>$v){
			$deal_trade_event[ $k]['create_time']=date("Y-m-d",$v['create_time']);
			$deal_trade_event[ $k]['log_info']=$v['log_info'];
			$deal_trade_event[ $k]['price']=$v['price'];
		}
		$deal['pe_evaluate']=$deal['pe_amount_plan']/$deal['pe_sell_scale']*100;
		$len=strpos($deal['pe_evaluate'],".");
		if ($len) {
			$deal['pe_evaluate']=substr($deal['pe_evaluate'], 0, $len);
		}

		$GLOBALS['tmpl']->assign("deal",$deal);
		$GLOBALS['tmpl']->assign("deal_trade_event",$deal_trade_event);
		$GLOBALS['tmpl']->assign("user_info",$user_info);
		$GLOBALS['tmpl']->assign("estp_finacing","estp_finacing");
		$GLOBALS['tmpl']->display("deal_pe.html");
	}

	public function add_estp_finacing(){
		$id = $GLOBALS['user_info']['id'];
		$side_step = $GLOBALS['user_info']['side_step'];
		 
		if($side_step<4){$user['side_step']=4;}else{$user['side_step']=$side_step;}
		$deal_id = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal where user_id = ".$id);
		//增加deal融资信息
		//var_dump($id);
		$deal['user_id'] = $id;
		$deal['pe_amount_plan'] = strim($_REQUEST['pe_amount_plan']);
		$res=check_region($deal['pe_amount_plan'],"融资金额");
		if($res['status']==0){
          	ajax_return($res);
		}
		$deal['pe_least_amount'] = strim($_REQUEST['pe_least_amount']);
		$res=check_region($deal['pe_least_amount'],"单笔最低投资额度");
		if($res['status']==0 || $deal['pe_least_amount']<100){
          	ajax_return($res);
		}
		$deal['pe_sell_scale'] = strim($_REQUEST['pe_sell_scale']);
		$res=check_region($deal['pe_sell_scale'],"出让股权比例");
		if($res['status']==0){
          	ajax_return($res);
		}
		$res=check_len($deal['pe_sell_scale'] ,2,1,"出让股权比例");
		if($res['status']==0){
          	ajax_return($res);
		}
		
		/*$deal['pe_evaluate'] = strim($_REQUEST['pe_evaluate']);
		$res=check_region($deal['pe_evaluate'],"融资前估算");
		if($res['status']==0){
          	ajax_return($res);
		}*/
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		if(empty($deal_id)){
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal,"INSERT","SILENT");
 			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}else{
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal,"UPDATE","id=".$deal_id['id'],"SILENT");
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}
		$deal_id = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal where user_id = ".$id);
		if(!$result){
			$res['status'] = 0;
			$res['info'] = '更新融资信息失败';
			ajax_return($res);
		}

 		//增加deal_trade_event
		$deal_info['create_time'] = $_REQUEST['create_time_event'];
		/*$res=check_len($deal_info['create_time'],10,0,"时间");
		if($res['status']==0){
          	ajax_return($res);
		}*/
		$deal_info['period'] = $_REQUEST['deal_trade_period'];

		$deal_info['brief'] = $_REQUEST['deal_trade_brief'];
		/*$res=check_len($deal_info['brief_event'],14,0,"机构名称");
		if($res['status']==0){
          	ajax_return($res);
		}*/
		$deal_info['price'] = $_REQUEST['invest_price'];
		$res_point=$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_trade_event where deal_id = ".$deal_id['id']);
		foreach ($deal_info['create_time'] as $key => $value) {
			if(!empty($deal_info['brief'][$key])&&!empty($deal_info['price'][$key]) && !empty($deal_info['period'][$key]) && !empty($value)){
				$deal_log=array();
				$deal_log['deal_id']=$deal_id['id'];
				$deal_log['period']=trim($deal_info['period'][$key]);
				$deal_log['price']=trim($deal_info['price'][$key]);
				$deal_log['brief']=trim($deal_info['brief'][$key]);
				$deal_log['create_time']=strtotime(trim($value));
				$result=$GLOBALS['db']->autoExecute(DB_PREFIX."deal_trade_event",$deal_log,"INSERT","SILENT");
				if(!$result){
					$res['status'] = 0;
					$res['info'] = '增加融资事件失败';
					ajax_return($res);
				}
			}
		}

		if(!$result){
			$res['status'] = 0;
			ajax_return($res);
		}
		ajax_return($res);

	}
}
?>