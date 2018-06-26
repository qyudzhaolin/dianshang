<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class homeModule extends BaseModule
{
	public function index()
	{	
 
		//个人资料
		$GLOBALS['tmpl']->assign("page_title","我的信息");
		$id = $GLOBALS['user_info']['id'];
		$home_user_info =  $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = {$id}");
		
		if ($home_user_info['user_type']=='1') {
			$home_user_info['user_type']='投资人';}
		if(strim($home_user_info['img_user_logo'])){
			$home_user_info['real_img']=getQiniuPath($home_user_info['img_user_logo'],'img');
			$home_user_info['real_img']=$home_user_info['real_img']."?imageView2/1/w/52/h/52";
		}
		if(strim($home_user_info['img_card_logo'])){
			$home_user_info['card_real_img']=getQiniuPath($home_user_info['img_card_logo'],'img');
			$home_user_info['card_real_img']=$home_user_info['card_real_img']."?imageView2/1/w/280/h/180";
		}
		if(strim($home_user_info['id_cardz_logo'])){
			$home_user_info['cardz_real_img']=getQiniuPath($home_user_info['id_cardz_logo'],'img');
			$home_user_info['cardz_real_img']=$home_user_info['cardz_real_img']."?imageView2/1/w/280/h/180";
		}
		if(strim($home_user_info['id_cardf_logo'])){
			$home_user_info['cardf_real_img']=getQiniuPath($home_user_info['id_cardf_logo'],'img');
			$home_user_info['cardf_real_img']=$home_user_info['cardf_real_img']."?imageView2/1/w/280/h/180";
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
		$per_degree_list =  $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."education_degree order by sort desc ");
		$GLOBALS['tmpl']->assign("per_degree_list",$per_degree_list);
		$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
		$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		$GLOBALS['tmpl']->assign("home_user_info",$home_user_info);
		
		//公司信息
		$investor_org = $GLOBALS['db']->getRow("select org_name,org_title,org_linkman,org_mobile,org_url,org_desc,vip_money,vip_begin_time,vip_end_time  from ".DB_PREFIX."user_ex_investor  where user_id = {$home_user_info['id']}");
// 		$investor_org_vip_begin_time = date ( "Y年m月d日", $investor_org ['vip_begin_time']);
// 		if ($investor_org_vip_begin_time) {
// 		$GLOBALS ['tmpl']->assign ( "investor_org_vip_begin_time", $investor_org_vip_begin_time );}
		
// 		$investor_org_vip_end_time = date ( "Y年m月d日", $investor_org ['vip_end_time']);
// 		if ($investor_org_vip_end_time) {
// 			$GLOBALS ['tmpl']->assign ( "investor_org_vip_end_time", $investor_org_vip_end_time );}
		
		$msgNum = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."user_notify where user_id = {$id} and is_read=0");

		//项目行业
		$dealcate = $GLOBALS['db']->getAll("select id,name  from ".DB_PREFIX."deal_cate  order by sort asc" );

        //投资倾向
       // 项目已选择的分类
        $deal_select_cate = $GLOBALS['db']->getAll("select id,user_id,cate_id from ".DB_PREFIX."user_select_cate where user_id=$id");
        // 项目分类
        foreach ($dealcate as $k => $v) {
            foreach ($deal_select_cate as $vk =>$vs) {
                if ($vs['cate_id'] == $v['id']) {
                    $dealcate[$k]['check'] = 1;
                }
            }
        }
		$GLOBALS['tmpl']->assign("msgNum",$msgNum);
		$GLOBALS['tmpl']->assign("investor_org",$investor_org);
		$GLOBALS['tmpl']->assign("dealcate",$dealcate);
		$GLOBALS ['tmpl']->assign("pageType",PAGE_MENU_USER_CENTER);
		$GLOBALS ['tmpl']->assign("sideType",SIDE_MENU_HOME);
		$GLOBALS['tmpl']->display("personal.html");
		 
	}
	public function personal_update(){
		
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$user['img_user_logo'] = strim($_REQUEST['img_user_logo']);
		$res=check_region($user['img_user_logo'],"真实头像");
		if($res['status']==0){
          	ajax_return($res);
		}
		$user['province'] = strim($_REQUEST['province']);
		$res=check_region($user['province'],"地区");
		if($res['status']==0){
          	ajax_return($res);
		}
  		$user['city'] = strim($_REQUEST['city']);
		$res=check_region($user['city'],"地区");
		if($res['status']==0){
        ajax_return($res);
		}
		$user['per_degree'] = strim($_REQUEST['degree']);
		$res=check_region($user['per_degree'],"学历");
		if($res['status']==0){
			ajax_return($res);
		}
		$user['email'] = strim($_REQUEST['email']);
		if(trim($user['email'])!=""){
		$res=check_email($user['email'] ,"邮箱");
		if($res['status']==0){
			ajax_return($res);
		}
		}
		$user['id'] = $GLOBALS['user_info']['id'];
		 
 		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$user['id'],"SILENT");

		if(!$result){
			$res['status'] = 0;
			$res['info'] = "更新会员信息失败";
		}
		ajax_return($res);
	}
 
	public function investor_org_update(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
  
		$user['org_name'] = strim($_REQUEST['org_name']);
		$res=check_len($user['org_name'] ,20,1,"公司名称");
		if($res['status']==0){
          	ajax_return($res);
		}
		
		$user['org_title'] = strim($_REQUEST['org_title']);
		$res=check_len($user['org_title'],8,1,"担任职务");
		if($res['status']==0){
			ajax_return($res);
		}

		$user['org_desc'] = strim($_REQUEST['org_desc']);
		if(trim($user['org_desc'])!=""){
		$res=check_len($user['org_desc'] ,200,1,"公司简介");
		if($res['status']==0){
          	ajax_return($res);
		}
		}

		$user['org_linkman'] = strim($_REQUEST['org_linkman']);
		if(trim($user['org_linkman'])!=""){
		$res=check_len($user['org_linkman'] ,6,1,"联系人");
		if($res['status']==0){
          	ajax_return($res);
		}
		}

		$user['org_mobile'] = $_REQUEST['org_mobile'];
// 		if(trim($user['org_mobile'])!=""){
// 		$res=check_tel($user['org_mobile']);
// 		if($res['status']==0){
//           	ajax_return($res);
// 		}
// 		}
		//机构网址
		$user['org_url'] = $_REQUEST['org_url'];
		if(trim($user['org_url'])!=""){
			$res=check_url($user['org_url']);
			if($res['status']==0){
	          	ajax_return($res);
			}
		}
		
 
		$user['user_id'] = $GLOBALS['user_info']['id'];
	
	 
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
		 
		ajax_return($res);

	}
	
	public function personal_review(){
	
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$user['img_user_logo'] = strim($_REQUEST['img_user_logo']);
		$res=check_region($user['img_user_logo'],"真实头像");
		if($res['status']==0){
			ajax_return($res);
		}
		$user['province'] = strim($_REQUEST['province']);
		$res=check_region($user['province'],"地区");
		if($res['status']==0){
			ajax_return($res);
		}
		$user['city'] = strim($_REQUEST['city']);
		$res=check_region($user['city'],"地区");
		if($res['status']==0){
			ajax_return($res);
		}
		$user['per_degree'] = strim($_REQUEST['degree']);
		$res=check_region($user['per_degree'],"学历");
		if($res['status']==0){
			ajax_return($res);
		}
		$user_ex_investor['org_name'] = strim($_REQUEST['org_name']);
		$res=check_len($user_ex_investor['org_name'] ,20,1,"公司名称");
		if($res['status']==0){
          	ajax_return($res);
		}
		$user_ex_investor['org_title'] = strim($_REQUEST['org_title']);
		$res=check_len($user_ex_investor['org_title'],8,1,"担任职务");
		if($res['status']==0){
			ajax_return($res);
		}
		$user['id'] = $GLOBALS['user_info']['id'];
		$user['user_id']=$user['id'];
		$user_ex_investor['user_id']=$user['id'];
		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$user['id'],"SILENT");
		$investor = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."user_ex_investor where user_id = ".$user['user_id']);
		if ($investor) {
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user_ex_investor",$user_ex_investor,"UPDATE","user_id=".$user['user_id'],"SILENT");
		}else{
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user_ex_investor",$user_ex_investor,"INSERT","SILENT");
		}
		if(!$result){
			$res['status'] = 0;
			$res['info'] = "更新会员信息失败";
		}
		ajax_return($res);
	}
	public function user_update(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$user['id_cardz_logo'] = strim($_REQUEST['img_cardz_logo']);
		if(trim($user['id_cardz_logo'])!=""){
			$res=check_region($user['id_cardz_logo'] ,"身份证正面");
			if($res['status']==0){
				ajax_return($res);
			}
		}
		$user['id_cardf_logo'] = strim($_REQUEST['img_cardf_logo']);
		if(trim($user['id_cardf_logo'])!=""){
			$res=check_region($user['id_cardf_logo'] ,"身份证反面");
			if($res['status']==0){
				ajax_return($res);
			}
		}
		$user['img_card_logo'] = strim($_REQUEST['img_card_logo']);
		if(trim($user['img_card_logo'])!=""){
			$res=check_region($user['img_card_logo'] ,"个人名片");
			if($res['status']==0){
				ajax_return($res);
			}
		}
		$user['is_review'] =2;
		$user['id'] = $GLOBALS['user_info']['id'];
		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$user['id'],"SILENT");
	
		if(!$result){
			$res['status'] = 0;
			$res['info'] = "更新会员信息失败";
		}
		ajax_return($res);
	}
	
	public function user_pwd_update(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$user_old_pwd = strim($_REQUEST['user_old_pwd']);
		$user_old_pwd_db = strim($_REQUEST['user_old_pwd_db']);
		$res=check_pwd($user_old_pwd,"旧密码");
		if($res['status']==0){
			ajax_return($res);
		}
		if (md5($user_old_pwd) != $user_old_pwd_db) {
			$res ['status'] = 0;
			$res ['info'] = "与旧密码不一致";
			ajax_return ( $res);
		}
		$user_new_pwd = strim($_REQUEST['user_new_pwd']);
		$res=check_pwd($user_new_pwd,"新密码");
		if($res['status']==0){
			ajax_return($res);
		}
		$user_new_pwd_confirm = strim($_REQUEST['user_new_pwd_confirm']);
		$res=check_pwd($user_new_pwd_confirm,"新密码确认");
		if($res['status']==0){
			ajax_return($res);
		}
		if ($user_new_pwd != $user_new_pwd_confirm) {
			$res ['status'] = 0;
			$res ['info'] = "新密码不一致";
			ajax_return ( $res);
		}
		$user['id'] = $GLOBALS['user_info']['id'];
		$user['user_pwd'] = md5 ($user_new_pwd);
		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$user['id'],"SILENT");
		
		if(!$result){
			$res['status'] = 0;
			$res['info'] = "更新会员信息失败";
		}
		ajax_return($res);
	}
	public function reset_pwd()
	{
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
	
		$old_mobile = strim($_REQUEST['old_mobile']);
		$res=check_mobile_web($old_mobile,1);
		if($res['status']==0){
          	ajax_return($res);
		}
		$verify_old_code = strim($_REQUEST['old_sms_code']);
		$res=check_verify_code($verify_old_code);
		if($res['status']==0){
          	ajax_return($res);
		}

		
		$new_mobile = strim($_REQUEST['new_mobile']);
		$res=check_mobile_web($new_mobile,1);
		if($res['status']==0){
			ajax_return($res);
		}
		
		$verify_new_code = strim($_REQUEST['new_sms_code']);
		$res=check_verify_code($verify_new_code);
		if($res['status']==0){
			ajax_return($res);
		}
		
		$old_code=$GLOBALS['db']->getOne("select code from ".DB_PREFIX."deal_msg_list where mobile_num = '".trim($old_mobile)." ' order by id desc");
		if ($old_code!=$verify_old_code) {
			$res['status'] = 0;
			$res['info'] = "验证码错误";
			$res['error_msg'] = "#get_pwd_msg";
			ajax_return($res);
				
		} 
		if($GLOBALS['db']->getOne("select count(id) from ".DB_PREFIX."user where mobile = '".$new_mobile."'"))
		{
			$res['status'] = 0;
			$res['info'] = "手机号已注册";
			$res['error_msg'] = "#get_news_mobile_msg";
			ajax_return($res);
		}
		$new_code=$GLOBALS['db']->getOne("select code from ".DB_PREFIX."deal_msg_list where mobile_num = '".trim($new_mobile)." ' order by id desc");
		if ($new_code!=$verify_new_code) {
			$res['status'] = 0;
			$res['info'] = "验证码错误";
			$res['error_msg'] = "#get_pwd_second_msg";
			ajax_return($res);
		}
		$user['id'] = $GLOBALS['user_info']['id'];
		$user['mobile'] = $new_mobile;
		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$user['id'],"SILENT");
	
		if (!$result) {
			$res['status'] = 0;
			$res['info'] = "换绑手机号失败";
			$res['error_msg'] = "#get_pwd_second_msg";
			ajax_return($res);
		}
		ajax_return($res);
	}
	//投资倾向
	public function deal_cate_save(){
	  $user_info = $GLOBALS['user_info'];
      $user_id = $user_info['id'];
       // 用于返回的数据
      $res = array(
            'status' =>1,
            'info' => '',
            'data' => ''
        ); 
        $dealcateid = trim($_REQUEST['deal_cate'], ',');
        $cateidresult = explode(',', $dealcateid, 5);
        if (empty($cateidresult)) {
            $res['status'] == 0;
            ajax_return($res);
        }
           if ($user_id) {
                foreach ($cateidresult as $value) {
                $values .= "('',{$user_id},{$value}),";
            }
              $values = rtrim($values, ',');
              $GLOBALS['db']->query("delete from ".DB_PREFIX."user_select_cate  where user_id ={$user_id}");
              $cateresult = $GLOBALS['db']->query("iNSERT INTO " . DB_PREFIX . "user_select_cate VALUES {$values}");
             }
           if ($cateresult) {
           	$data=str_replace(",","_",$dealcateid);
           $GLOBALS['db']->query("update ".DB_PREFIX."user_ex_investor set cate_choose ='{$data}' where user_id ={$user_id}");
           }else{
            $res['status'] = 0;
            $res['info'] = "保存失败";
           }
        ajax_return($res);
	}
	
}
?>