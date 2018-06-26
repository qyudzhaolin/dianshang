<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class homeModule extends BaseModule
{
	public function __construct(){
		parent::__construct();
		parent::is_login();
	}
	public function index()
	{		
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$id = $GLOBALS['user_info']['id'];
		
		$home_user_info = $GLOBALS['user_info'];
	
		if(strim($home_user_info['img_user_logo'])){
			$home_user_info['real_img']=getQiniuPath($home_user_info['img_user_logo'],'img');
			$home_user_info['real_img']=$home_user_info['real_img']."?imageView2/1/w/54/h/54";
		}

		if(strim($home_user_info['img_card_logo'])){
			$home_user_info['card_real_img']=getQiniuPath($home_user_info['img_card_logo'],'img');
			$home_user_info['card_real_img']=$home_user_info['card_real_img']."?imageView2/1/w/75/h/45";
		}
		if($home_user_info['is_review']!=0){
			$home_user_info['edu_history']=nl2br($home_user_info['edu_history']); 
			$home_user_info['per_history']=nl2br($home_user_info['per_history']); 
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
		$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid=".$region_pid);
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
		$GLOBALS['tmpl']->assign("region_pname_province",$region_pname_province);
		$GLOBALS['tmpl']->assign("region_pname_city",$region_pname_city);
		$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		//$home_user_info['city']=is_null($home_user_info['city'])? 0: $home_user_info['city'];
		//var_dump($home_user_info);
		//$GLOBALS['tmpl']->assign("city",$home_user_info['city']);
		$GLOBALS['tmpl']->assign("home_user_info",$home_user_info);
		
		
		$GLOBALS['tmpl']->assign("br","\r\n");		
		//$GLOBALS['tmpl']->assign("user_id",$id);
		//$GLOBALS['tmpl']->assign("is_review",$home_user_info['is_review']);
		$GLOBALS['tmpl']->assign("user","user");
 		 
		if ($home_user_info['user_type']==0) {
				$GLOBALS['tmpl']->display("home_estp_info_show.html");
			}else{
				$GLOBALS['tmpl']->display("home_investor_info_show.html");
			}
	}
	public function personal_update(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$user['img_user_logo'] = strim($_REQUEST['img_user_logo']);
		$res=check_region($user['img_user_logo'],"真实头像");
		if($res['status']==0){
          	ajax_return($res);
		}
		
		/*if ($GLOBALS['user_info']['user_type']==0) {
			$user['img_card_logo'] = strim($_REQUEST['img_card_logo']);
			$res=check_region($user['img_card_logo'],"个人名片");
			if($res['status']==0){
	          	ajax_return($res);
			}
		}*/
		
		$user['user_name'] = strim($_REQUEST['user_name']);
		$res=check_len($user['user_name'] ,14,1,"真实姓名");
		if($res['status']==0){
          	ajax_return($res);
		}
		
		if($GLOBALS['user_info']['user_type']==1){
			$user['per_sign'] = strim($_REQUEST['per_sign']);
			$res=check_len($user['per_sign'] ,34,1,"个人签名");
			if($res['status']==0){
	          	ajax_return($res);
			}
		}
		

		$user['per_brief'] = strim($_REQUEST['per_brief']);
		$res=check_len($user['per_brief'] ,28,1,"个人简介");
		if($res['status']==0){
          	ajax_return($res);
		}
		//$user['intro'] = strim($_REQUEST['intro']);
		$user['province'] = strim($_REQUEST['province']);
		$res=check_region($user['province'],"地区");
		if($res['status']==0){
          	ajax_return($res);
		}
  		// $user['city'] = strim($_REQUEST['city']);
		// $res=check_region($user['city'],"地区");
		// if($res['status']==0){
        // ajax_return($res);
		// }

		$user['id'] = $GLOBALS['user_info']['id'];

		$user['edu_history'] = strim($_REQUEST['edu_history']);
		$res=check_len($user['edu_history'] ,100,0,"教育经历");
		if($res['status']==0){
          	ajax_return($res);
		}

		$user['per_history'] = strim($_REQUEST['per_history']);
		$res=check_len($user['per_history'] ,100,0,"个人履历");
		if($res['status']==0){
          	ajax_return($res);
		}
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step<2){$user['side_step'] = 2;}
 		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$user['id'],"SILENT");
		if(!$result){
			$res['status'] = 0;
			$res['info'] = "更新会员信息失败";
		}
		ajax_return($res);

	}
	public function investor_org()
	{	
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step <= 1)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];

		$investor = $GLOBALS['user_info'];
		
		$user_ex_investor = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_ex_investor  where user_id = ".$id);

		if($investor['is_review']!=0){
			$user_ex_investor['org_desc']=nl2br($user_ex_investor['org_desc']); 
		}
		
		if(strim($investor['img_card_logo'])){
			$user_ex_investor['card_real_img']=getQiniuPath($investor['img_card_logo'],'img');
			$user_ex_investor['card_real_img']=$user_ex_investor['card_real_img']."?imageView2/1/w/75/h/45";
		}

		if(strim($user_ex_investor['img_org_logo'])){
			$user_ex_investor['logo_real_img']=getQiniuPath($user_ex_investor['img_org_logo'],'img');
			$user_ex_investor['logo_real_img']=$user_ex_investor['logo_real_img']."?imageView2/1/w/75/h/45";
		}
		$GLOBALS['tmpl']->assign("investor",$investor);
		$GLOBALS['tmpl']->assign("user_ex_investor",$user_ex_investor);
		//$GLOBALS['tmpl']->assign("user_id",$id);
		//$GLOBALS['tmpl']->assign("is_review",$investor['is_review']);
		$GLOBALS['tmpl']->assign("investor_org","investor_org");
		$GLOBALS['tmpl']->display("home_investor_org_show.html");
	}
	public function investor_org_update(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
 		$user['img_org_logo'] = strim($_REQUEST['img_org_logo']);
		/*$res=check_region($user['img_org_logo'],"机构logo");
		if($res['status']==0){
          	ajax_return($res);
		}*/

		$user['org_name'] = strim($_REQUEST['org_name']);
		$res=check_len($user['org_name'] ,14,1,"机构名称");
		if($res['status']==0){
          	ajax_return($res);
		}

		$user['org_desc'] = strim($_REQUEST['org_desc']);
		$res=check_len($user['org_desc'] ,100,1,"机构简介");
		if($res['status']==0){
          	ajax_return($res);
		}

		$user['org_linkman'] = strim($_REQUEST['org_linkman']);
		$res=check_len($user['org_linkman'] ,14,1,"联系人");
		if($res['status']==0){
          	ajax_return($res);
		}

		$user['org_mobile'] = $_REQUEST['org_mobile'];
		$res=check_tel($user['org_mobile']);
		if($res['status']==0){
          	ajax_return($res);
		}

		//机构网址
		$user['org_url'] = $_REQUEST['org_url'];
		if(trim($user['org_url'])!=""){
			$res=check_url($user['org_url']);
			if($res['status']==0){
	          	ajax_return($res);
			}
		}
		
		$user_info['img_card_logo'] = strim($_REQUEST['img_card_logo']);
		$res=check_region($user_info['img_card_logo'],"个人名片");
		if($res['status']==0){
          	ajax_return($res);
		}

		$user['user_id'] = $GLOBALS['user_info']['id'];
		//var_dump($user);
		
		$investor = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."user_ex_investor where user_id = ".$user['user_id']);
		if ($investor) {
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user_ex_investor",$user,"UPDATE","user_id=".$user['user_id'],"SILENT");
		}else{
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user_ex_investor",$user,"INSERT","SILENT");
		}
		if(!$result){
			$res['status'] = 0;
			$res['info'] = "更新投资人信息失败";
		}
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step<3){$user_info['side_step'] = 3;}
		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info,"UPDATE","id=".$user['user_id'],"SILENT");
		if(!$result){
			$res['status'] = 0;
			$res['info'] = "更新会员信息失败";
		}
		ajax_return($res);

	}
/*
    public function deal_show()
	{	
		$id = $GLOBALS['user_info']['id'];
		$deal = $GLOBALS['user_info'];
		$home_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal  where user_id = ".$id);
		 
	 	//项目图片
        if($home_user_info['img_deal_logo']){
			$home_user_info['deal_real_img']=getQiniuPath($home_user_info['img_deal_logo'],'img');
		}
        //BP地址
        if($home_user_info['bp_url']){
			$home_user_info['real_url_bp']=getQiniuPath($home_user_info['real_url_bp'],'bp');
		}

		if(!empty($home_user_info['id'])){
		//项目亮点
		$deal_brief_point = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_brief_point where deal_id = ".$home_user_info[id]);
		//项目大事记
		$deal_event = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_event where deal_id = ".$home_user_info[id]);
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
		 
		if($deal['img_card_logo']){
			$user_ex_deal['card_real_img']=getQiniuPath($deal['img_card_logo'],'img');
		}

		if($user_ex_deal['img_org_logo']){
			$user_ex_deal['logo_real_img']=getQiniuPath($user_ex_deal['img_org_logo'],'img');
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
				break;
			}
		}
		$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
		$home_user_info['city']=is_null($home_user_info['city'])? 0: $home_user_info['city'];
	    $region_lv3=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."region_conf where region_level = 3 and pid=".$region_pid." and id=".$home_user_info['city']); 
		//var_dump($home_user_info);die();
		$GLOBALS['tmpl']->assign("city",$region_lv3);
		$GLOBALS['tmpl']->assign("home_user_info",$home_user_info);

		$GLOBALS['tmpl']->assign("deal",$deal);
		$GLOBALS['tmpl']->assign("deal_event_list",$deal_event_list);
		$GLOBALS['tmpl']->assign("deal_brief_point",$deal_brief_point);
		 
		$GLOBALS['tmpl']->assign("user_select_cate",$user_select_cate);
		$GLOBALS['tmpl']->assign("deal_period",$deal_period);
		$GLOBALS['tmpl']->assign("deal_cate",$deal_cate);
		$GLOBALS['tmpl']->assign("user_ex_deal",$user_ex_deal);
		$GLOBALS['tmpl']->assign("home_user_info",$home_user_info);
		$GLOBALS['tmpl']->assign("deal_org","deal_org");
		$GLOBALS['tmpl']->display("deal.html");
			 
		 
	}
*/
	public function invest_about()
	{	
		$GLOBALS['tmpl']->assign("page_title","个人中心");	
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step <= 2)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];

		$investor = $GLOBALS['user_info'];
		/*if(!$investor)
		{
			app_redirect(url("index"));	
		}*/
		//投资成绩
		$investor_mark = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."investor_mark where user_id = ".$id);
		//投资风格
		$investor_style = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."investor_style where user_id = ".$id);
		//投资偏好-阶段
		$user_select_period = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_select_period where user_id = ".$id);
		//投资偏好-方向
		$user_select_cate = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_select_cate where user_id = ".$id);
		//阶段
		$deal_period = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_period order by sort desc");
		//方向
		$deal_cate = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cate order by sort desc");
		//已选
		$user_ex_investor = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_ex_investor where user_id=".$id);
		if(!empty($user_ex_investor['period_choose'])){
			$period_choose=explode("_", $user_ex_investor['period_choose']);
		}
		foreach ($deal_period as $key => $value) {
			if(in_array($value['id'], $period_choose)){
				$deal_period[$key]['is_check']=1;
			}else{
				$deal_period[$key]['is_check']=0;
			}
		}
		if(!empty($user_ex_investor['cate_choose'])){
			$cate_choose=explode("_", $user_ex_investor['cate_choose']);
		}
		foreach ($deal_cate as $key => $value) {
			if(in_array($value['id'], $cate_choose)){
				$deal_cate[$key]['is_check']=1;
			}else{
				$deal_cate[$key]['is_check']=0;
			}
		}
		$GLOBALS['tmpl']->assign("investor",$investor);
		$GLOBALS['tmpl']->assign("investor_mark",$investor_mark);
		$GLOBALS['tmpl']->assign("investor_style",$investor_style);
		$GLOBALS['tmpl']->assign("user_select_period",$user_select_period);
		$GLOBALS['tmpl']->assign("user_select_cate",$user_select_cate);
		$GLOBALS['tmpl']->assign("deal_period",$deal_period);
		$GLOBALS['tmpl']->assign("deal_cate",$deal_cate);
		//$GLOBALS['tmpl']->assign("user_id",$id);
		//$GLOBALS['tmpl']->assign("is_review",$investor['is_review']);
		$GLOBALS['tmpl']->assign("invest_about","invest_about");

		$GLOBALS['tmpl']->display("home_investor_about_show.html");
	}
	
	public function invest_about_update(){
		//var_dump($_REQUEST);
		$id = $GLOBALS['user_info']['id'];
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step<4){$user['side_step']=4;}else{$user['side_step']=$side_step;}
		
		//投资成绩
		$user['investor_mark'] = $_REQUEST['mark_info'];
		$res_mark=$GLOBALS['db']->query("delete from ".DB_PREFIX."investor_mark where user_id = ".$id);
		foreach ($user['investor_mark'] as $key => $value) {
			$value=strim($value);
			if (!empty($value)) {
				$investor_mark=array();
				$investor_mark['user_id']=$id;
				$investor_mark['mark_info']=$value;
				$investor_mark['create_time']=time();
				$result=$GLOBALS['db']->autoExecute(DB_PREFIX."investor_mark",$investor_mark,"INSERT","SILENT");
				if(!$result){
					$res['status'] = 0;
					$res['info'] = "更新投资成绩失败";
					ajax_return($res);
				}
			}
			
		}
		//投资风格
		$user['investor_style'] =  $_REQUEST['style_info'];
		$res_style=$GLOBALS['db']->query("delete from ".DB_PREFIX."investor_style where user_id = ".$id);
		foreach ($user['investor_style'] as $key => $value) {
			$value=strim($value);
			if (!empty($value)) {
				$investor_style=array();
				$investor_style['user_id']=$id;
				$investor_style['style_info']=$value;
				$investor_style['create_time']=time();
				$result=$GLOBALS['db']->autoExecute(DB_PREFIX."investor_style",$investor_style,"INSERT","SILENT");
				if(!$result){
					$res['status'] = 0;
					$res['info'] = "更新投资风格失败";
					ajax_return($res);
				}
			}
			
		}
		//阶段
		$user['user_select_period'] =  $_REQUEST['user_select_period'];
		$res_period=$GLOBALS['db']->query("delete from ".DB_PREFIX."user_select_period where user_id = ".$id);
		foreach ($user['user_select_period'] as $key => $value) {
			$user_select_period=array();
			$user_select_period['user_id']=$id;
			$user_select_period['period_id']=$value;
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user_select_period",$user_select_period,"INSERT","SILENT");
			if(!$result){
				$res['status'] = 0;
				$res['info'] = "更新投资偏好失败";
				ajax_return($res);
			}
		}
		//方向 
		$user['user_select_cate'] = $_REQUEST['user_select_cate'];
		$res_period=$GLOBALS['db']->query("delete from ".DB_PREFIX."user_select_cate where user_id = ".$id);
		foreach ($user['user_select_cate'] as $key => $value) {
			$user_select_cate=array();
			$user_select_cate['user_id']=$id;
			$user_select_cate['cate_id']=$value;
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user_select_cate",$user_select_cate,"INSERT","SILENT");
			if(!$result){
				$res['status'] = 0;
				$res['info'] = "更新投资偏好失败";
				ajax_return($res);
			}
		}
		$user_ex_investor['period_choose']=implode("_", $user['user_select_period']);
		$user_ex_investor['cate_choose']=implode("_", $user['user_select_cate']);
		$user_ex_investor['user_id']=$id;

		$res_ex=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_ex_investor where user_id = ".$id);
		if ($res_ex) {
			$result_ex=$GLOBALS['db']->autoExecute(DB_PREFIX."user_ex_investor",$user_ex_investor,"UPDATE","user_id=".$id,"SILENT");# code...
			$result_ex=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}else{
			$result_ex=$GLOBALS['db']->autoExecute(DB_PREFIX."user_ex_investor",$user_ex_investor,"INSERT","SILENT");
			$result_ex=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}
		
		if(!$result_ex){
			$res['status'] = 0;
			$res['info'] = "更新投资人信息失败";
			ajax_return($res);
		}
		ajax_return($res);
	}



	
}
?>