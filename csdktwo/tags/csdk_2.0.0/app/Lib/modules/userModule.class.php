<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------


class userModule extends BaseModule
{

	
	public function register_check()
	{
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据	

		$user_name = strim($_REQUEST['user_name']);
		$res=check_len($user_name,'14',1,'真实姓名');
		if($res['status']==0){
          	ajax_return($res);
		}
		$mobile = strim($_REQUEST['mobile']);
		$res=check_mobile_web($mobile,1);
		if($res['status']==0){
          	ajax_return($res);
		}
		$user_pwd = strim($_REQUEST['user_pwd']);
		$res=check_pwd($user_pwd);
		if($res['status']==0){
          	ajax_return($res);
		}
		$verify_code = strim($_REQUEST['verify_code']);
		$res=check_verify_code($verify_code);
		if($res['status']==0){
          	ajax_return($res);
		}
		$img_verify = strim($_REQUEST['img_verify']);
		if(es_session::get("verify") != md5($img_verify)) {
			$field_item['field_name'] = 'img_verify';
			$field_item['error']	=	ACCOUNT_PASSWORD_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			//ajax_return($res);
		}
		
		/*if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".trim($mobile)."'")>0)
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
		}*/
		//验证手机号
		require_once APP_ROOT_PATH."system/libs/user.php";
		$res=check_user('mobile',$mobile);
		if($res['status']==1) {
			$code=$GLOBALS['db']->getOne("select code from ".DB_PREFIX."deal_msg_list where mobile_num = '".trim($mobile)." ' order by id desc");
			if ($code!=$verify_code) {
				$field_item['field_name'] = 'verify_code';
				$field_item['error']	=	ACCOUNT_PASSWORD_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
			}
		}
		$result = $res;
		if($result['status']==0)
		{
			if($result['data']['field_name']=='mobile')
			{
				$field_name = "手机号";
			}
			if($result['data']['field_name']=='img_verify')
			{
				$field_name = "图形验证码";
			}
			if($result['data']['field_name']=='verify_code')
			{
				$field_name = "验证码";
			}
			if($result['data']['error']==EXIST_ERROR)
			{
				$error = "已注册";
			}
			if($result['data']['error']==ACCOUNT_PASSWORD_ERROR)
			{
				$error = "错误";
			}
			$return = array('status'=>0,"info"=>$field_name.$error,"field"=>$result['data']['field_name']);
			ajax_return($return);
		}
		else
		{
			$return = array('status'=>1);
			ajax_return($return);
		}
		
		
	}
	public function do_login()
	{
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$mobile_login = strim($_REQUEST['mobile_login']);
		$res=check_mobile_web($mobile_login,1);
		if($res['status']==0){
          	ajax_return($res);
		}
		$user_pwd_login = strim($_REQUEST['user_pwd_login']);
		$res=check_pwd($user_pwd_login);
		if($res['status']==0){
          	ajax_return($res);
		}

		require_once APP_ROOT_PATH."system/libs/user.php";
		$result = do_login_user($mobile_login,$user_pwd_login);
		if($result['status']==0)
		{
			if($result['data']['field_name']=='mobile_login')
			{
				$field_name = "手机号";
			}
			else if($result['data']['field_name']=='user_pwd_login')
			{
				$field_name = "密码";
			}
			else
			{
				$field_name="";
			}
			if($result['data']['error']==ACCOUNT_NO_EXIST_ERROR)
			{
				$error = "没有注册过，请先注册";
			}
			else if($result['data']['error']==ACCOUNT_PASSWORD_ERROR)
			{
				$error = "与手机号不匹配";
			}
			else if($result['data']['error']==ACCOUNT_NO_VERIFY_ERROR)
			{
				$error = "未激活";
			}
			else
			{
				$error = "登录失败";
			}
			$return = array('status'=>0,"info"=>$field_name.$error,"field"=>$result['data']['field_name']);
			ajax_return($return);
		}
		else
		{
			$is_review=$GLOBALS['user_info']['is_review'];
			$user_type=$GLOBALS['user_info']['user_type'];
			$res = array('status'=>1,'info'=>'登录成功','is_review'=>$is_review,'user_type'=>$user_type); //用于返回的数据
			$return = $res;
			ajax_return($return);
			
		}
	}

	public function do_register()
	{	
		$user_data = $_POST;

		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		$res=check_len($user_data['user_name'],'14',1,'真实姓名');
		if($res['status']==0){
          	ajax_return($res);
		}

		$res=check_mobile_web($user_data['mobile'],1);
		if($res['status']==0){
          	ajax_return($res);
		}
		$res=check_pwd($user_data['user_pwd']);
		if($res['status']==0){
          	ajax_return($res);
		}

		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".strim($user_data['mobile'])."'")>0)
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
		}

		$user['create_time'] = time();
		$user['update_time'] = time();
		$user['user_type'] = strim($user_data['user_type']);
		$user['user_name'] = strim($user_data['user_name']);
		$user['mobile'] = strim($user_data['mobile']);
		$user['user_pwd'] = md5($user_data['user_pwd']);
		$user['is_review'] = 0;
		$user['is_effect'] = 1;
		$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user);
		$user['id'] = $GLOBALS['db']->insert_id();
		if($user['id'])
		{
			es_session::set("user_info",$user);
			//发消息
			$user_notify['user_id']=$user['id'];
			$user_notify['log_info']="欢迎您使用磁斯达克-企业股权融资全程服务平台，您可以在磁斯达克网站（www.cisdaq.com ）的帮助中心内查看我们为您提供的服务。";
			$user_notify['log_time']=time();
			$user_notify['is_read']=0;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_notify",$user_notify);
		}
		else
		{
			$field_item['info']	="注册失败";
			$res['status'] = 0;
			$res['data'] = $field_item;
		}
		ajax_return($res);

						
	}
	public function loginout()
	{		

		$ajax = intval($_REQUEST['ajax']);
		require_once APP_ROOT_PATH."system/libs/user.php";

		$result = loginout_user();
		if($result['status'])
		{
			es_cookie::delete("email");
			es_cookie::delete("user_pwd");
			es_cookie::delete("hide_user_notify");
			if($ajax==1)
			{
				$return['status'] = 1;
				$return['info'] = "登出成功";
				$return['data'] = $result['msg'];
				$return['jump'] = get_gopreview();					
				ajax_return($return);
			}
			else
			{
				$GLOBALS['tmpl']->assign('integrate_result',$result['msg']);
				if(trim(app_conf("INTEGRATE_CODE"))=='')
				{
					app_redirect_preview();
				}
				else
				showSuccess("登出成功",0,get_gopreview());
			}
		}
		else
		{
			if($ajax==1)
			{
				$return['status'] = 1;
				$return['info'] = "登出成功";
				$return['jump'] = get_gopreview();					
				ajax_return($return);
			}
			else
			app_redirect(get_gopreview());		
		}
	}
	public function get_img_verify(){
		error_reporting(0);
		if(!defined('APP_ROOT_PATH')) 
		//define('APP_ROOT_PATH', str_replace('verify.php', '', str_replace('\\', '/', __FILE__)));
		require APP_ROOT_PATH."system/utils/es_session.php";
		es_session::start();
		require APP_ROOT_PATH."system/utils/es_image.php";
		es_image::buildImageVerify(4,1,'gif',110,36,'verify','web');
		
	}
	public function get_pwd_img_verify(){
		error_reporting(0);
		if(!defined('APP_ROOT_PATH')) 
		//define('APP_ROOT_PATH', str_replace('verify.php', '', str_replace('\\', '/', __FILE__)));
		require APP_ROOT_PATH."system/utils/es_session.php";
		es_session::start();
		require APP_ROOT_PATH."system/utils/es_image.php";
		es_image::buildImageVerify(4,1,'gif',110,36,'verify_pwd','web');
		
	}
	public function check_img_verify(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		if(isset($_REQUEST['source'])&&$_REQUEST['source']=='sms'){
			require_once APP_ROOT_PATH."system/libs/user.php";
			$res=check_user('mobile',$_REQUEST['mobile']);
			if($res['status']==0){
				if($res['data']['error']==3){
					$res['info']="手机号已注册";
				}elseif ($res['data']['error']==2) {
					$res['info']="手机号格式不正确";
				}
				ajax_return($res);
			}
		}elseif (isset($_REQUEST['source'])&&$_REQUEST['source']=='get_pwd') {
			require_once APP_ROOT_PATH."system/libs/user.php";
			$res=check_user('mobile',$_REQUEST['mobile']);
			if($res['status']==0&&$res['data']['error']==2){
				$res['info']="手机号格式不正确";
				ajax_return($res);
			}else{
				$res['info']="手机号未注册";
				ajax_return($res);
			}
		}
		if(es_session::get('verify') != md5($_REQUEST['img_verify'])) {
			$res['status']=2;
			$res['info']='图形验证码错误';
			ajax_return($res);
		}
        ajax_return($res);
	}
	public function check_user_exist(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		//验证手机号
		require_once APP_ROOT_PATH."system/libs/user.php";
		$res=check_user('mobile',$_REQUEST['mobile']);
		if($res['status']==0){
			if($res['data']['error']==3){
				$res['info']="手机号已注册";
			}elseif ($res['data']['error']==2) {
				$res['info']="手机号格式不正确";
			}elseif ($res['data']['error']==4) {
				$res['info']="手机号未激活";
			}
		}
        ajax_return($res);
	}
	public function check_user_message(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		//验证手机号
		$verify_code = strim($_REQUEST['verify_code']);
		$mobile = strim($_REQUEST['mobile']);
		$res=check_verify_code($verify_code);
		if($res['status']==0){
          	ajax_return($res);
		}
		if($res['status']==1) {
			$code=$GLOBALS['db']->getOne("select code from ".DB_PREFIX."deal_msg_list where mobile_num = '".trim($mobile)." ' order by id desc");
			if ($code!=$verify_code) {
				$res['status'] = 0;
				$res['info'] = "验证码错误";
			}
		}
        ajax_return($res);
	}
	public function get_pwd(){
		$GLOBALS['tmpl']->assign("mobile",$_REQUEST['mobile']);
		$GLOBALS['tmpl']->display("get_pwd.html");
	}
	public function get_pwd_second(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$mobile = strim($_REQUEST['mobile']);
		$res=check_mobile_web($mobile,1);
		if($res['status']==0){
          	ajax_return($res);
		}
		//验证手机号
		require_once APP_ROOT_PATH."system/libs/user.php";
		if(!$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$mobile."'"))
		{
			$res['status'] = 0;
			$res['field'] = "get_pwd_mobile";
			$res['info'] = "手机号未注册";
			ajax_return($res);
		}
		$img_verify = strim($_REQUEST['img_verify']);
		if(es_session::get("verify_pwd") != md5($img_verify)) {
			$res['status'] = 0;
			$res['field'] = "img_verify_pwd";
			$res['info'] ="图形验证码错误";
			ajax_return($res);
		}
		ajax_return($res);
	}
	public function get_pwd_third(){
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$mobile = strim($_REQUEST['mobile']);
		$res=check_mobile_web($mobile,1);
		if($res['status']==0){
          	ajax_return($res);
		}
		$verify_code = strim($_REQUEST['sms_code']);
		$res=check_verify_code($verify_code);
		if($res['status']==0){
          	ajax_return($res);
		}
		//验证手机号
		require_once APP_ROOT_PATH."system/libs/user.php";
		if(!$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$mobile."'"))
		{
			$res['status'] = 0;
			$res['info'] = "手机号未注册";
			ajax_return($res);
		}
		$code=$GLOBALS['db']->getOne("select code from ".DB_PREFIX."deal_msg_list where mobile_num = '".trim($mobile)." ' order by id desc");
		if ($code!=$verify_code) {
			$res['status'] = 0;
			$res['info'] = "验证码错误";
			ajax_return($res);
			
		}
		ajax_return($res);
	}
	public function reset_pwd()
	{	
		$user_data = $_POST;

		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		$res=check_mobile_web($user_data['mobile'],1);
		if($res['status']==0){
          	ajax_return($res);
		}
		$res=check_pwd($user_data['user_pwd']);
		if($res['status']==0){
          	ajax_return($res);
		}
		$res=check_pwd($user_data['user_pwd_confirm']);
		if($res['status']==0){
          	ajax_return($res);
		}
		if ($user_data['user_pwd']!=$user_data['user_pwd_confirm']) {
			$res['status'] = 0;
			$res['info'] = "密码不一致";
			ajax_return($res);
		}

		if(!$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".strim($user_data['mobile'])."'"))
		{
			$res['status'] = 0;
			$res['info'] = "手机号未注册";
			ajax_return($res);
		}
		$user['user_pwd'] = md5($user_data['user_pwd']);
		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","mobile=".$user_data['mobile'],"SILENT");
		
		if (!$result) {
			$res['status'] = 0;
			$res['info'] = "更新密码失败";
			ajax_return($res);
		}
		ajax_return($res);
	}

}
?>