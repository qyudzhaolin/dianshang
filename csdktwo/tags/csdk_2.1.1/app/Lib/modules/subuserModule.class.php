<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
class subuserModule extends BaseModule
{
	public function __construct(){
		parent::__construct();
		parent::is_login();
	}
	public function index()
	{		
	
	}
	
	public function investor_account(){
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$is_review = $GLOBALS['user_info']['is_review'];
		if($is_review != 1)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];
		$investor = $GLOBALS['user_info'];
		
		$GLOBALS['tmpl']->assign("investor",$investor);

		$GLOBALS['tmpl']->assign("investor_sub_account","investor_sub_account");
		if("1"==$GLOBALS['user_info']['user_type']){
			$GLOBALS['tmpl']->display("subuser_investor_account_show.html");
		}else{
			$GLOBALS['tmpl']->display("subuser_estp_account_show.html");
		}
		

	}
	public function add_account_show(){
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$is_review = $GLOBALS['user_info']['is_review'];
		if($is_review != 1)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];
		$investor = $GLOBALS['user_info'];

		$extra_array=array("1","2","3");
		$sub_user_all = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."sub_user where user_id = ".$id);
		foreach ($sub_user_all as $key => $value) {
			$n=$key+1;
			$sub_user['mobile'.$n]=$value['sub_mobile'];
			array_pop ($extra_array);
		}

		//$GLOBALS['tmpl']->assign("investor",$investor);
		$GLOBALS['tmpl']->assign("extra_num",3-$n);
		$GLOBALS['tmpl']->assign("extra_array",$extra_array);
		$GLOBALS['tmpl']->assign("add_account_show","add_account_show");
		
		$GLOBALS['tmpl']->assign("investor",$investor);

		$GLOBALS['tmpl']->assign("investor_sub_account","investor_sub_account");
		if (intval($GLOBALS['user_info']['user_type'])==1) {
			$GLOBALS['tmpl']->display("subuser_investor_continue_impower_account_show.html");
		}else{
			$GLOBALS['tmpl']->display("subuser_estp_continue_impower_account_show.html");
		}
		
		

	}
	public function investor_account_update(){

		$id = $GLOBALS['user_info']['id'];
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		$user_id=$id;
		$sub_user['mobile1']=strim($_REQUEST['mobile1']);
		$sub_user['mobile2']=strim($_REQUEST['mobile2']);
		$sub_user['mobile3']=strim($_REQUEST['mobile3']);
		if(empty($sub_user['mobile1'])&&empty($sub_user['mobile2'])&&empty($sub_user['mobile3'])){
			$res['status'] = 0;
			$res['info'] = "请输入手机号";
			ajax_return($res);
		}
		if(($sub_user['mobile1']==$sub_user['mobile2']&&$sub_user['mobile1']!='')||($sub_user['mobile1']==$sub_user['mobile3']&&$sub_user['mobile1']!='')||($sub_user['mobile2']==$sub_user['mobile3']&&$sub_user['mobile3']!='')){
            $res['status'] = 0;
			$res['info'] = "不能重复添加手机号";
			ajax_return($res);
        } 
		$res=check_mobile_web($sub_user['mobile1'],0);
		if($res['status']==0){
          	ajax_return($res);
		}
		$res=check_mobile_web($sub_user['mobile2'],0);
		if($res['status']==0){
          	ajax_return($res);
		}
		$res=check_mobile_web($sub_user['mobile3'],0);
		if($res['status']==0){
          	ajax_return($res);
		}

		$user['sub_user_pwd']=strim($_REQUEST['user_pwd']);
		$res=check_pwd($user['sub_user_pwd']);
		if($res['status']==0){
          	ajax_return($res);
		}

		//手机验证
		require_once APP_ROOT_PATH."system/libs/user.php";
		foreach ($sub_user as $key => $value) {
			if(!empty($value)){
				$result=check_user('mobile',$value);
				if ($result['status']==0) {
					ajax_return($result);
				}
			}
		}
		$result=$GLOBALS['db']->query("delete from ".DB_PREFIX."sub_user where user_id = ".$id);
		if(!$result){
				$res['status'] = 0;
				ajax_return($res);
		}
		foreach ($sub_user as $key => $value) {
			if(!empty($value)){
				$sub_one['user_id']=$user_id;
				$sub_one['sub_mobile']=$value;
				$result=$GLOBALS['db']->autoExecute(DB_PREFIX."sub_user",$sub_one,"INSERT","SILENT");
				if(!$result){
						$res['status'] = 0;
						$res['info'] = "添加子账号失败";
						ajax_return($res);
				}
			}
		}
		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		if(!$result){
				$res['status'] = 0;
				$res['info'] = "更新会员信息失败";
				ajax_return($res);
		}
		ajax_return($res);

	}
	public function manage_account(){
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$is_review = $GLOBALS['user_info']['is_review'];
		if($is_review != 1)	{	
			app_redirect(url("home"));
			return;
		}
		$id =$GLOBALS['user_info']['id'];

		//$investor = $GLOBALS['user_info'];
	
		$sub_user_all = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."sub_user where user_id = ".$id);
		foreach ($sub_user_all as $key => $value) {
			$n=$key+1;
			$sub_user['mobile'.$n]=$value['sub_mobile'];
		}

		//$GLOBALS['tmpl']->assign("investor",$investor);
		$GLOBALS['tmpl']->assign("extra_num",3-$n);
		$GLOBALS['tmpl']->assign("n",$n);
		$GLOBALS['tmpl']->assign("sub_user",$sub_user);
		$GLOBALS['tmpl']->assign("manage_account","manage_account");

		$GLOBALS['tmpl']->assign("investor_manage_sub_account","investor_manage_sub_account");
		if($GLOBALS['user_info']['user_type']){
			$GLOBALS['tmpl']->display("subuser_investor_finish_impower_account_show.html");
		}else{
			$GLOBALS['tmpl']->display("subuser_estp_finish_impower_account_show.html");
		}
		

	}
	public function del_account(){
		$id = $GLOBALS['user_info']['id'];
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		//$field_name=$_REQUEST['field_name'];
		$mobile=strim($_REQUEST['mobile']);
		$res=check_mobile_web($mobile,1);
		if($res['status']==0){
          	ajax_return($res);
		}
		$result=$GLOBALS['db']->query("delete from ".DB_PREFIX."sub_user where user_id = ".$id." and sub_mobile='".$mobile."'");
		
		if(!$result){
				$res['status'] = 0;
				$res['info'] = "删除子账号失败";
				ajax_return($res);
				
		}
		
		$res['info']=$num;
		ajax_return($res);

	}
	public function add_account(){
		$id = $GLOBALS['user_info']['id'];
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		//$field_name='mobile3';
		$mobile=$_REQUEST['mobile'];
		$mobile_unique_num =count(array_unique($mobile));
		$mobile_num =count($mobile);
		if ($mobile_unique_num != $mobile_num) {
			$res['status']=0;
			$res['info']='不能重复添加手机号';
			ajax_return($res);
		}
		foreach ($mobile as $key => $value) {
			//手机验证
			require_once APP_ROOT_PATH."system/libs/user.php";
			$result=check_user('mobile',$value);
			if ($result['status']==0) {
				ajax_return($result);
			}
		}
		foreach ($mobile as $key => $value) {
			if (trim($value)!='') {
				$res=check_mobile_web($value,1);
				if($res['status']==0){
		          	ajax_return($res);
				}
						//$field_value='1390';
				$user_sub_account = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."sub_user where user_id = ".$id);
				if ($user_sub_account>=3) {
					$res['status']=0;
					$res['info']='只能添加3条';
					ajax_return($res);
				}
				
				$sub_one['user_id']=$id;
				$sub_one['sub_mobile']=$value;
				$result=$GLOBALS['db']->autoExecute(DB_PREFIX."sub_user",$sub_one,"INSERT","SILENT");
				$res['info']=$user_sub_account+1;
				}
			
		}
		ajax_return($res);

	}

	public function update_account_pwd(){
		$id =$GLOBALS['user_info']['id'];
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		$sub_user['sub_user_pwd']=strim($_REQUEST['sub_user_pwd']);
		$res=check_pwd($sub_user['sub_user_pwd']);
		if($res['status']==0){
          	ajax_return($res);
		}

		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$sub_user,"UPDATE","id=".$id,"SILENT");
		if(!$result){
				$res['status'] = 0;
				$res['info'] = "更新子账号密码失败";
				ajax_return($res);
		}
		ajax_return($res);

	}

	public function estp_account(){
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$is_review = $GLOBALS['user_info']['is_review'];
		if($is_review != 1)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];

		$investor = $GLOBALS['user_info'];

		$GLOBALS['tmpl']->assign("investor",$investor);

		$GLOBALS['tmpl']->assign("estp_sub_account","estp_sub_account");
		$GLOBALS['tmpl']->display("subuser_estp_account_show.html");

	}
	/*public function estp_manage_account(){

		$id =$GLOBALS['user_info']['id'];	
		$sub_user_all = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."sub_user where user_id = ".$id);
		foreach ($sub_user_all as $key => $value) {
			$n=$key+1;
			$sub_user['mobile'.$n]=$value['sub_mobile'];
		}

		//$GLOBALS['tmpl']->assign("investor",$investor);
		$GLOBALS['tmpl']->assign("extra_num",3-$n);
		$GLOBALS['tmpl']->assign("n",$n);
		$GLOBALS['tmpl']->assign("sub_user",$sub_user);

		$GLOBALS['tmpl']->assign("estp_manage_sub_account","estp_manage_sub_account");
		$GLOBALS['tmpl']->display("subuser_estp_finish_impower_account_show.html");

	}*/
}
?>