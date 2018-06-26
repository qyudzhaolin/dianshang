<?php
// +----------------------------------------------------------------------
// | 登录、注册、找回密码相关
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
class userModule extends BaseModule {
	
	const EFFECT_USER = 1;
	const DEFECT_USER = 0;
	
	private $code_signup_key = 'verify_signup';
	private $code_findpass_key = 'verify_findpass';
	/*
	 * 登录页面
	 */
	public function signin() {
		$url_referer     = isset( $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$backurl = $_REQUEST['backurl'] ? htmlspecialchars($_REQUEST['backurl']) : $url_referer;
		
		// 已登录，跳转首页
		if(es_session::get(USER_SESSION_KEY)){
			app_redirect(url("index"));
		}
		
		// 如果是从用户登录、注册、找回密码模块跳转过来的 处理backurl
		if(strrpos($backurl,'user')){
			$backurl = url("index");
		}
		
		// 如果是从退出登录跳转过来
		if(strrpos($url_referer,'sign_out')){
		    $backurl = url("index");
		}
		
		$GLOBALS ['tmpl']->assign ( "pageType", PAGE_MENU_LOGIN);
		$GLOBALS ['tmpl']->assign ( "backurl", $backurl );
		$GLOBALS ['tmpl']->assign ( "page_title", "用户登录" );
		$GLOBALS ['tmpl']->display ( "signin.html" );
	}
	
	/*
	 * 注册页面
	 */
	public function signup() {
	    // 已登录，跳转首页
	    if(es_session::get(USER_SESSION_KEY)){
	        app_redirect(url("index"));
	    }
		$GLOBALS ['tmpl']->assign ( "pageType", PAGE_MENU_REGISTER);
		$GLOBALS ['tmpl']->assign ( "page_title", "用户注册" );
		$GLOBALS ['tmpl']->display ( "signup.html" );
	}
	
	/*
	 * 找回密码界面
	 */
	public function findPassword(){
	    // 已登录，跳转首页
	    if(es_session::get(USER_SESSION_KEY)){
	        app_redirect(url("index"));
	    }
		$GLOBALS ['tmpl']->assign ( "pageType", PAGE_MENU_LOGIN);
		$GLOBALS ['tmpl']->assign ( "page_title", "找回密码" );
		$GLOBALS ['tmpl']->display ( "find_password.html" );
	}
	
	/*
	 * 退出登录
	*/
	public function sign_out(){
		$user_info = es_session::get(USER_SESSION_KEY);
		if($user_info){
			es_session::delete(USER_SESSION_KEY);
		}
// 		app_redirect(url('user#signin'));	#跳转登陆页
        // 采用script 不然不带referer
        echo "<script type='text/javascript'>location.href='".url('user#signin')."'</script>";
        exit();
	}
	
################ api #####################################################

	/*
	 * 登录验证
	 */
	public function dosignin() {
		
		$res = array ('status' => 1,'info' => '','data' => ''); // 用于返回的数据
		
		// 用户名密码数据格式验证
		$mobile = strim ( $_POST ['mobile'] );
		$re = check_mobile_web ( $mobile, 1 );
		if ($re ['status'] == 0) {
		    $res ['status']   = 0;
		    $res ['info']     = $re['info'];
		    $res ['data']     = 'mobile';
			ajax_return ( $res );
		}
		// 登录的时候只验证密码是否输入
		$pwd = strim ( $_POST ['pwd'] );
		if(empty($pwd)){
		    $res ['data']    = 'pwd';
			$res ['status']  = 0;
			$res ['info']    = '请输入密码';
			ajax_return ( $res );
		}
		
		// 恶意请求限制 同一个IP一段时间内的访问次数不能过多，暂时放session了
		$ip 		= get_client_ip();
		$signinNum	= (int)es_session::get($mobile.$ip."signin");
		
		if ($signinNum && $signinNum > 4) {
			$res['status'] = 0;
			$res['info'] = '您的账号已被锁定，请稍后再试！';
			ajax_return ( $res );
		} else {
			$signinNum += 1;
			es_session::set($mobile.$ip."signin",$signinNum);
		}
		
		// 用户名密码检测，不考虑子帐户相关所有信息
		$sql="select id,mobile,user_pwd,is_effect,user_type,user_name from ".DB_PREFIX."user where mobile='{$mobile}'";
		$userinfo = $GLOBALS['db']->getRow($sql);
		if($userinfo){
			if($userinfo['mobile'] == $mobile && $userinfo['user_pwd'] != md5($pwd)){
				$res['status']  = 0;
			    $res ['data']   = 'pwd';
				if($signinNum == 3){
    				$res['info']    = '密码连续输入错误5次，账号将被锁定，请谨慎操作！';
				}else{
    				$res['info']    = '密码不正确';
				}
			}elseif($userinfo['is_effect'] != 1){
			    $res ['data']   = 'mobile';
				$res['status']  = 0;
				$res['info']    = "该账号已被禁用";
			}else{
				$user_info['id'] = $userinfo['id'];
				$user_info['user_type'] = $userinfo['user_type'];
				$user_info['user_name'] = $userinfo['user_name'];
				
				es_session::set(USER_SESSION_KEY,$user_info);
				es_session::delete($mobile.$ip."signin");   # 清除登录次数限制
				
				// 记录日志
// 				require_once APP_ROOT_PATH.'system/utils/Logger.php';
// 				$Logs = new logger();
// 				$msg = array($userinfo['id'],$userinfo['user_name'],$userinfo['mobile'],date("Y-m-d H:i:s"),'pc');
// 				$Logs::write($msg,$Logs::INFO);
				$user_login_log = array(
				    'user_id'       => $userinfo['id'],
				    'login_time'    => time(),
				    'login_ip'      => $ip,
				    'login_type'    => 1,
				);
				$GLOBALS ['db']->autoExecute ( DB_PREFIX . "user_login_log", $user_login_log );
				
				$res['status']  = 1;
				$res['info']    = "登录成功";
			}
		}else{
			$res['status']   = 0;
			$res ['data']    = 'mobile';
			$res['info']     = "该手机号尚未注册";
		}
		
		ajax_return ( $res );
	}
	
	/*
	 * 注册验证
	 */
	public function dosignup(){
		$res = array ('status' => 1,'info' => '','data' => ''); // 用于返回的数据
		
		// 数据格式验证
		$userinfo = $_POST;
		$res = check_mobile_web ( $userinfo ['mobile'], 1 );
		if ($res ['status'] == 0) {
			$res ['data'] = 'mobile';
			ajax_return ( $res );
		}
		
		$user_rst = $GLOBALS ['db']->getRow ( "select is_effect from " . DB_PREFIX . "user where mobile = '" . strim ( $userinfo ['mobile'] ) . "'" );
		if($user_rst){
			if($user_rst['is_effect'] == self::DEFECT_USER){
				$res ['status'] = 0;
				$res ['data'] = 'mobile';
				$res ['info'] = "该手机号码已禁用";
				ajax_return ( $res );
			}else{
				$res ['status'] = 0;
				$res ['data'] = 'mobile';
				$res ['info'] = "该手机号码已注册";
				ajax_return ( $res );
			}
		}
		
		$res = check_pwd ( $userinfo ['pwd'] );
		if ($res ['status'] == 0) {
			$res ['data'] = 'pwd';
			ajax_return ( $res );
		}
		if(empty($userinfo ['pwd_confirm']) || $userinfo ['pwd_confirm'] != $userinfo ['pwd']){
			$res ['status'] = 0;
			$res ['info'] = "密码输入不一致";
			$res ['data'] = 'pwd_confirm';
			ajax_return ( $res );
		}
		$res = check_len ( $userinfo ['username'], '6', 1, '真实姓名' );
		if ($res ['status'] == 0) {
			$res ['data'] = 'username';
			ajax_return ( $res );
		}
		
		if(empty($userinfo ['code'])){
			$res ['status'] = 0;
			$res ['info'] = '图形验证码不能为空';
		}else if (es_session::get ( $this->code_signup_key ) != md5 ( $userinfo ['code'] )) {
			$res ['status'] = 0;
			$res ['info'] = '图形验证码错误';
		}
		if ($res ['status'] == 0) {
			$res ['data'] = 'code';
			ajax_return ( $res );
		}
		
		if(empty($userinfo ['sms'])){
			$res ['status'] = 0;
			$res ['info'] = '短信验证码不能为空';
			$res ['data'] = 'sms';
			ajax_return ( $res );
		}
		$sql = "select id from ". DB_PREFIX . "deal_msg_list where mobile_num='".strim($userinfo ['mobile'])."'  and code= '".strim($userinfo ['sms'])."'";
		$result = $GLOBALS ['db']->getOne ($sql);
		if(empty($result)){
			$res ['status'] = 0;
			$res ['info'] = '短信验证码错误';
			$res ['data'] = 'sms';
			ajax_return ( $res );
		}
		
		$user ['create_time'] = time ();
		$user ['update_time'] = time ();
		$user ['user_type'] = 1;
		$user ['user_name'] = strim ( $userinfo ['username'] );
		$user ['mobile'] = strim ( $userinfo ['mobile'] );
		$user ['user_pwd'] = md5 ( $userinfo ['pwd'] );
		$user ['is_review'] = 0;
		$user ['is_effect'] = 1;
		$GLOBALS ['db']->autoExecute ( DB_PREFIX . "user", $user );
		$user ['id'] = $GLOBALS ['db']->insert_id ();
		if ($user ['id']) {
			// 发消息
			$user_notify ['user_id'] = $user ['id'];
			$user_notify ['log_info'] = "欢迎您使用磁斯达克-股权交易信息服务平台，您可以在磁斯达克网站（www.cisdaq.com ）的帮助中心内查看我们为您提供的服务。";
			$user_notify ['log_time'] = time ();
			$user_notify ['is_read'] = 0;
			$GLOBALS ['db']->autoExecute ( DB_PREFIX . "user_notify", $user_notify );
			// 自动登陆
			$user_info['id'] = $user['id'];
			$user_info['user_type'] = 1;
			$user_info['user_name'] = $user['user_name'];
			es_session::set(USER_SESSION_KEY,$user_info);
			// 记录日志
// 			require_once APP_ROOT_PATH.'system/utils/Logger.php';
// 			$Logs = new logger();
// 			$msg = array("{$user['id']}",$user['user_name'],$user['mobile'],date("Y-m-d H:i:s"),'pc');
// 			$Logs::write($msg,$Logs::INFO);
			$user_login_log = array(
			    'user_id'       => $user['id'],
			    'login_time'    => time(),
			    'login_ip'      => get_client_ip(),
			    'login_type'    => 1,
			);
			$GLOBALS ['db']->autoExecute ( DB_PREFIX . "user_login_log", $user_login_log );
			
			$res ['info'] = "注册成功";
			$res ['data'] = url("index#index");
		} else {
			$res ['info'] = "注册失败";
			$res ['status'] = 0;
			$res ['data'] = "sms";
		}
		ajax_return ( $res );
	}

	/*
	 * 找回密码验证_step1
	 */
	function dofindpass_step1($return = true){
		$res = array ('status' => 1,'info' => '','data' => ''); // 用于返回的数据
	
		$mobile = strim ( $_POST ['mobile'] );
		$res = check_mobile_web ( $mobile, 1 );
		if ($res ['status'] == 0) {
		    $res ['data'] = "mobile";
			ajax_return ( $res );
		}
		$user = $GLOBALS ['db']->getRow ( "select id,user_pwd from " . DB_PREFIX . "user where mobile = '" . $mobile . "'" );
		if (empty($user)) {
		    $res ['data'] = "mobile";
			$res ['status'] = 0;
			$res ['info'] = "输入的手机号码未注册";
			ajax_return ( $res );
		}
		
		$code = strim ( $_POST ['code'] );
		if(empty($code)){
			$res ['status'] = 99;
			$res ['info'] = '图形验证码不能为空';
		}else if (es_session::get ( $this->code_findpass_key ) != md5 ( $code )) {
			$res ['status'] = 99;
			$res ['info'] = '图形验证码输入错误';
		}
		if ($res ['status'] == 99) {
			$res ['data'] = 'code';
			ajax_return ( $res );
		}
		
		$sms = strim ( $_POST ['sms'] );
		if(empty($sms)){
		    $res ['data'] = 'sms';
			$res ['status'] = 0;
			$res ['info'] = '短信验证码不能为空';
			ajax_return ( $res );
		}
		$sql = "select id from ". DB_PREFIX . "deal_msg_list where mobile_num='".$mobile."'  and code= '".$sms."'";
		$result = $GLOBALS ['db']->getOne ($sql);
		if(empty($result)){
			$res ['status'] = 0;
			$res ['info'] = '输入的短信验证码不正确';
			$res ['data'] = 'sms';
			ajax_return ( $res );
		}
		
		if($return){
		    ajax_return ( $res );
		}else{
		    return $user;
		}
	}

	/*
	 * 找回密码验证_step2
	*/
	function dofindpass_step2(){
		$res = array ('status' => 1,'info' => '','data' => '');
		
		// 验证step1 数据
		$user = $this->dofindpass_step1(false);
		
		$pwd = strim ( $_POST ['pwd'] );
		$res = check_pwd ( $pwd );
		if ($res ['status'] == 0) {
		    $res ['data'] = 'pwd';
			ajax_return ( $res );
		}
		
		$pwd_confirm = strim ( $_POST ['pwd_confirm'] );
		$res = check_pwd ( $pwd_confirm );
		if ($res ['status'] == 0) {
		    $res ['data'] = 'pwd';
			ajax_return ( $res );
		}
		if ($pwd != $pwd_confirm) {
		    $res ['data'] = 'pwd_confirm';
			$res ['status'] = 0;
			$res ['info'] = "密码输入不一致";
			ajax_return ( $res );
		}
		
		if($user['user_pwd'] == md5($pwd)){
		    $res ['status'] = 0;
		    $res ['info'] = "新密码不能和旧密码相同";
		    ajax_return ( $res );
		}
		
		$mobile = strim ( $_POST ['mobile'] );
		$user ['user_pwd'] = md5 ( $pwd );
		$result = $GLOBALS ['db']->autoExecute ( DB_PREFIX . "user", $user, "UPDATE", "mobile=" . $mobile, "SILENT" );
		if($result){
		    $ip       = get_client_ip();
		    $loginNum = (int)es_session::get($mobile.$ip."signin");
		    if($loginNum){
    		    es_session::delete($mobile.$ip."signin");   # 清除登录次数限制
		    }
		}else{
			$res ['status'] = 0;
			$res ['info'] = "更新密码失败";
		}
		ajax_return ( $res );
	}
	
	/*
	 * 获取验证码
	*/
	public function check_code(){
		$res = array('status'=>1,'info'=>'');
		$code = strim ( $_REQUEST ['code'] );
		if(empty($code)){
			$res ['status'] = 0;
			$res ['info'] = '图形验证码不能为空';
		}else if (es_session::get ( $this->code_signup_key ) != md5 ( $code )) {
			$res ['status'] = 0;
			$res ['info'] = '图形验证码错误';
		}
		ajax_return ( $res );
	}
	
############### function ###################################################
	
	/*
	 * 生成验证码_signup
	*/
	public function get_signup_code(){
		error_reporting ( 0 );
		if (! defined ( 'APP_ROOT_PATH' ))
			require APP_ROOT_PATH . "system/utils/es_session.php";
		es_session::start ();
		require APP_ROOT_PATH . "system/utils/es_image.php";
		es_image::buildImageVerify ( 4, 1, 'gif', 110, 36, $this->code_signup_key, 'web' );
	}
	
	/*
	 * 生成验证码_findpassword
	*/
	public function get_findpassword_code(){
		error_reporting ( 0 );
		if (! defined ( 'APP_ROOT_PATH' ))
			require APP_ROOT_PATH . "system/utils/es_session.php";
		es_session::start ();
		require APP_ROOT_PATH . "system/utils/es_image.php";
		es_image::buildImageVerify ( 4, 1, 'gif', 110, 36, $this->code_findpass_key, 'web' );
	}
	
################# old code #################################################
	
	public function register_check() {
		$res = array (
				'status' => 1,
				'info' => '',
				'data' => '' 
		); // 用于返回的数据
		
		$user_name = strim ( $_REQUEST ['user_name'] );
		$res = check_len ( $user_name, '14', 1, '真实姓名' );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		$mobile = strim ( $_REQUEST ['mobile'] );
		$res = check_mobile_web ( $mobile, 1 );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		$user_pwd = strim ( $_REQUEST ['user_pwd'] );
		$res = check_pwd ( $user_pwd );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		$verify_code = strim ( $_REQUEST ['verify_code'] );
		$res = check_verify_code ( $verify_code );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		$img_verify = strim ( $_REQUEST ['img_verify'] );
		if (es_session::get ( "verify" ) != md5 ( $img_verify )) {
			$field_item ['field_name'] = 'img_verify';
			$field_item ['error'] = ACCOUNT_PASSWORD_ERROR;
			$res ['status'] = 0;
			$res ['data'] = $field_item;
			// ajax_return($res);
		}
		
		/*
		 * if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user
		 * where mobile = '".trim($mobile)."'")>0) { $field_item['field_name'] =
		 * 'mobile'; $field_item['error']	=	EXIST_ERROR; $res['status'] = 0;
		 * $res['data'] = $field_item; }
		 */
		// 验证手机号
		require_once APP_ROOT_PATH . "system/libs/user.php";
		$res = check_user ( 'mobile', $mobile );
		if ($res ['status'] == 1) {
			$code = $GLOBALS ['db']->getOne ( "select code from " . DB_PREFIX . "deal_msg_list where mobile_num = '" . trim ( $mobile ) . " ' order by id desc" );
			if ($code != $verify_code) {
				$field_item ['field_name'] = 'verify_code';
				$field_item ['error'] = ACCOUNT_PASSWORD_ERROR;
				$res ['status'] = 0;
				$res ['data'] = $field_item;
			}
		}
		$result = $res;
		if ($result ['status'] == 0) {
			if ($result ['data'] ['field_name'] == 'mobile') {
				$field_name = "手机号";
			}
			if ($result ['data'] ['field_name'] == 'img_verify') {
				$field_name = "图形验证码";
			}
			if ($result ['data'] ['field_name'] == 'verify_code') {
				$field_name = "验证码";
			}
			if ($result ['data'] ['error'] == EXIST_ERROR) {
				$error = "已注册";
			}
			if ($result ['data'] ['error'] == ACCOUNT_PASSWORD_ERROR) {
				$error = "错误";
			}
			$return = array (
					'status' => 0,
					"info" => $field_name . $error,
					"field" => $result ['data'] ['field_name'] 
			);
			ajax_return ( $return );
		} else {
			$return = array (
					'status' => 1 
			);
			ajax_return ( $return );
		}
	}
	public function do_login() {
		$res = array (
				'status' => 1,
				'info' => '',
				'data' => '' 
		); // 用于返回的数据
		$mobile_login = strim ( $_REQUEST ['mobile_login'] );
		$res = check_mobile_web ( $mobile_login, 1 );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		$user_pwd_login = strim ( $_REQUEST ['user_pwd_login'] );
		$res = check_pwd ( $user_pwd_login );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		
		require_once APP_ROOT_PATH . "system/libs/user.php";
		$result = do_login_user ( $mobile_login, $user_pwd_login );
		if ($result ['status'] == 0) {
			if ($result ['data'] ['field_name'] == 'mobile_login') {
				$field_name = "手机号";
			} else if ($result ['data'] ['field_name'] == 'user_pwd_login') {
				$field_name = "密码";
			} else {
				$field_name = "";
			}
			if ($result ['data'] ['error'] == ACCOUNT_NO_EXIST_ERROR) {
				$error = "没有注册过，请先注册";
			} else if ($result ['data'] ['error'] == ACCOUNT_PASSWORD_ERROR) {
				$error = "与手机号不匹配";
			} else if ($result ['data'] ['error'] == ACCOUNT_NO_VERIFY_ERROR) {
				$error = "未激活";
			} else {
				$error = "登录失败";
			}
			$return = array (
					'status' => 0,
					"info" => $field_name . $error,
					"field" => $result ['data'] ['field_name'] 
			);
			ajax_return ( $return );
		} else {
			$is_review = $GLOBALS ['user_info'] ['is_review'];
			$user_type = $GLOBALS ['user_info'] ['user_type'];
			$res = array (
					'status' => 1,
					'info' => '登录成功',
					'is_review' => $is_review,
					'user_type' => $user_type 
			); // 用于返回的数据
			$return = $res;
			ajax_return ( $return );
		}
	}
	public function do_register() {
		$userinfo = $_POST;
		
		$res = array (
				'status' => 1,
				'info' => '',
				'data' => '' 
		); // 用于返回的数据
		
		$res = check_len ( $userinfo ['user_name'], '14', 1, '真实姓名' );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		
		$res = check_mobile_web ( $userinfo ['mobile'], 1 );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		$res = check_pwd ( $userinfo ['user_pwd'] );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		
		if ($GLOBALS ['db']->getOne ( "select count(*) from " . DB_PREFIX . "user where mobile = '" . strim ( $userinfo ['mobile'] ) . "'" ) > 0) {
			$field_item ['field_name'] = 'mobile';
			$field_item ['error'] = EXIST_ERROR;
			$res ['status'] = 0;
			$res ['data'] = $field_item;
		}
		
		$user ['create_time'] = time ();
		$user ['update_time'] = time ();
		$user ['user_type'] = strim ( $userinfo ['user_type'] );
		$user ['user_name'] = strim ( $userinfo ['user_name'] );
		$user ['mobile'] = strim ( $userinfo ['mobile'] );
		$user ['user_pwd'] = md5 ( $userinfo ['user_pwd'] );
		$user ['is_review'] = 0;
		$user ['is_effect'] = 1;
		$GLOBALS ['db']->autoExecute ( DB_PREFIX . "user", $user );
		$user ['id'] = $GLOBALS ['db']->insert_id ();
		if ($user ['id']) {
			es_session::set ( "user_info", $user );
			// 发消息
			$user_notify ['user_id'] = $user ['id'];
			$user_notify ['log_info'] = "欢迎您使用磁斯达克-企业股权融资全程服务平台，您可以在磁斯达克网站（www.cisdaq.com ）的帮助中心内查看我们为您提供的服务。";
			$user_notify ['log_time'] = time ();
			$user_notify ['is_read'] = 0;
			$GLOBALS ['db']->autoExecute ( DB_PREFIX . "user_notify", $user_notify );
		} else {
			$field_item ['info'] = "注册失败";
			$res ['status'] = 0;
			$res ['data'] = $field_item;
		}
		ajax_return ( $res );
	}
	public function loginout() {
		$ajax = intval ( $_REQUEST ['ajax'] );
		require_once APP_ROOT_PATH . "system/libs/user.php";
		
		$result = loginout_user ();
		if ($result ['status']) {
			es_cookie::delete ( "email" );
			es_cookie::delete ( "user_pwd" );
			es_cookie::delete ( "hide_user_notify" );
			if ($ajax == 1) {
				$return ['status'] = 1;
				$return ['info'] = "登出成功";
				$return ['data'] = $result ['msg'];
				$return ['jump'] = get_gopreview ();
				ajax_return ( $return );
			} else {
				$GLOBALS ['tmpl']->assign ( 'integrate_result', $result ['msg'] );
				if (trim ( app_conf ( "INTEGRATE_CODE" ) ) == '') {
					app_redirect_preview ();
				} else
					showSuccess ( "登出成功", 0, get_gopreview () );
			}
		} else {
			if ($ajax == 1) {
				$return ['status'] = 1;
				$return ['info'] = "登出成功";
				$return ['jump'] = get_gopreview ();
				ajax_return ( $return );
			} else
				app_redirect ( get_gopreview () );
		}
	}


	public function check_user_exist() {
		$res = array (
				'status' => 1,
				'info' => '',
				'data' => '' 
		); // 用于返回的数据
		                                                 
		// 验证手机号
		require_once APP_ROOT_PATH . "system/libs/user.php";
		$res = check_user ( 'mobile', $_REQUEST ['mobile'] );
		if ($res ['status'] == 0) {
			if ($res ['data'] ['error'] == 3) {
				$res ['info'] = "手机号已注册";
			} elseif ($res ['data'] ['error'] == 2) {
				$res ['info'] = "手机号格式不正确";
			} elseif ($res ['data'] ['error'] == 4) {
				$res ['info'] = "手机号未激活";
			}
		}
		ajax_return ( $res );
	}
	public function check_user_message() {
		$res = array (
				'status' => 1,
				'info' => '',
				'data' => '' 
		); // 用于返回的数据
		                                                 
		// 验证手机号
		$verify_code = strim ( $_REQUEST ['verify_code'] );
		$mobile = strim ( $_REQUEST ['mobile'] );
		$res = check_verify_code ( $verify_code );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		if ($res ['status'] == 1) {
			$code = $GLOBALS ['db']->getOne ( "select code from " . DB_PREFIX . "deal_msg_list where mobile_num = '" . trim ( $mobile ) . " ' order by id desc" );
			if ($code != $verify_code) {
				$res ['status'] = 0;
				$res ['info'] = "验证码错误";
			}
		}
		ajax_return ( $res );
	}
	public function get_pwd() {
		$GLOBALS ['tmpl']->assign ( "mobile", $_REQUEST ['mobile'] );
		$GLOBALS ['tmpl']->display ( "get_pwd.html" );
	}
	public function get_pwd_second() {
		$res = array (
				'status' => 1,
				'info' => '',
				'data' => '' 
		); // 用于返回的数据
		$mobile = strim ( $_REQUEST ['mobile'] );
		$res = check_mobile_web ( $mobile, 1 );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		// 验证手机号
		require_once APP_ROOT_PATH . "system/libs/user.php";
		if (! $GLOBALS ['db']->getOne ( "select count(*) from " . DB_PREFIX . "user where mobile = '" . $mobile . "'" )) {
			$res ['status'] = 0;
			$res ['field'] = "get_pwd_mobile";
			$res ['info'] = "手机号未注册";
			ajax_return ( $res );
		}
		$img_verify = strim ( $_REQUEST ['img_verify'] );
		if (es_session::get ( "verify_pwd" ) != md5 ( $img_verify )) {
			$res ['status'] = 0;
			$res ['field'] = "img_verify_pwd";
			$res ['info'] = "图形验证码错误";
			ajax_return ( $res );
		}
		ajax_return ( $res );
	}
	public function get_pwd_third() {
		$res = array (
				'status' => 1,
				'info' => '',
				'data' => '' 
		); // 用于返回的数据
		$mobile = strim ( $_REQUEST ['mobile'] );
		$res = check_mobile_web ( $mobile, 1 );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		$verify_code = strim ( $_REQUEST ['sms_code'] );
		$res = check_verify_code ( $verify_code );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		// 验证手机号
		require_once APP_ROOT_PATH . "system/libs/user.php";
		if (! $GLOBALS ['db']->getOne ( "select count(*) from " . DB_PREFIX . "user where mobile = '" . $mobile . "'" )) {
			$res ['status'] = 0;
			$res ['info'] = "手机号未注册";
			ajax_return ( $res );
		}
		$code = $GLOBALS ['db']->getOne ( "select code from " . DB_PREFIX . "deal_msg_list where mobile_num = '" . trim ( $mobile ) . " ' order by id desc" );
		if ($code != $verify_code) {
			$res ['status'] = 0;
			$res ['info'] = "验证码错误";
			ajax_return ( $res );
		}
		ajax_return ( $res );
	}
	public function reset_pwd() {
		$userinfo = $_POST;
		
		$res = array (
				'status' => 1,
				'info' => '',
				'data' => '' 
		); // 用于返回的数据
		
		$res = check_mobile_web ( $userinfo ['mobile'], 1 );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		$res = check_pwd ( $userinfo ['user_pwd'] );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		$res = check_pwd ( $userinfo ['user_pwd_confirm'] );
		if ($res ['status'] == 0) {
			ajax_return ( $res );
		}
		if ($userinfo ['user_pwd'] != $userinfo ['user_pwd_confirm']) {
			$res ['status'] = 0;
			$res ['info'] = "密码不一致";
			ajax_return ( $res );
		}
		
		if (! $GLOBALS ['db']->getOne ( "select count(*) from " . DB_PREFIX . "user where mobile = '" . strim ( $userinfo ['mobile'] ) . "'" )) {
			$res ['status'] = 0;
			$res ['info'] = "手机号未注册";
			ajax_return ( $res );
		}
		$user ['user_pwd'] = md5 ( $userinfo ['user_pwd'] );
		$result = $GLOBALS ['db']->autoExecute ( DB_PREFIX . "user", $user, "UPDATE", "mobile=" . $userinfo ['mobile'], "SILENT" );
		
		if (! $result) {
			$res ['status'] = 0;
			$res ['info'] = "更新密码失败";
			ajax_return ( $res );
		}
		ajax_return ( $res );
	}
	
	public function get_img_verify() {
		error_reporting ( 0 );
		if (! defined ( 'APP_ROOT_PATH' ))
			// define('APP_ROOT_PATH', str_replace('verify.php', '',
			// str_replace('\\', '/', __FILE__)));
			require APP_ROOT_PATH . "system/utils/es_session.php";
		es_session::start ();
		require APP_ROOT_PATH . "system/utils/es_image.php";
		es_image::buildImageVerify ( 4, 1, 'gif', 110, 36, 'verify', 'web' );
	}
	public function get_pwd_img_verify() {
		error_reporting ( 0 );
		if (! defined ( 'APP_ROOT_PATH' ))
			// define('APP_ROOT_PATH', str_replace('verify.php', '',
			// str_replace('\\', '/', __FILE__)));
			require APP_ROOT_PATH . "system/utils/es_session.php";
		es_session::start ();
		require APP_ROOT_PATH . "system/utils/es_image.php";
		es_image::buildImageVerify ( 4, 1, 'gif', 110, 36, 'verify_pwd', 'web' );
	}
	
	public function check_img_verify() {
		$res = array (
				'status' => 1,
				'info' => '',
				'data' => ''
		); // 用于返回的数据
	
		if (isset ( $_REQUEST ['source'] ) && $_REQUEST ['source'] == 'sms') {
			require_once APP_ROOT_PATH . "system/libs/user.php";
			$res = check_user ( 'mobile', $_REQUEST ['mobile'] );
			if ($res ['status'] == 0) {
				if ($res ['data'] ['error'] == 3) {
					$res ['info'] = "手机号已注册";
				} elseif ($res ['data'] ['error'] == 2) {
					$res ['info'] = "手机号格式不正确";
				}
				ajax_return ( $res );
			}
		} elseif (isset ( $_REQUEST ['source'] ) && $_REQUEST ['source'] == 'get_pwd') {
			require_once APP_ROOT_PATH . "system/libs/user.php";
			$res = check_user ( 'mobile', $_REQUEST ['mobile'] );
			if ($res ['status'] == 0 && $res ['data'] ['error'] == 2) {
				$res ['info'] = "手机号格式不正确";
				ajax_return ( $res );
			} else {
				$res ['info'] = "手机号未注册";
				ajax_return ( $res );
			}
		}
		if (es_session::get ( 'verify' ) != md5 ( $_REQUEST ['img_verify'] )) {
			$res ['status'] = 2;
			$res ['info'] = '图形验证码错误';
			ajax_return ( $res );
		}
		ajax_return ( $res );
	}
}
?>