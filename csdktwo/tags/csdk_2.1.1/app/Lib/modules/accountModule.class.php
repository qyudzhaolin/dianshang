<?php
class accountModule extends BaseModule
{
	const EFFECT_USER = 1; // 用户启用状态
	const DEFECT_USER = 0; // 用户禁用状态
	
	public function index()
	{	
		//个人资料
		$GLOBALS['tmpl']->assign("page_title","账户安全");
		$id = $GLOBALS['user_info']['id'];
		$home_user_info =  $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = {$id}");
		if(strim($home_user_info['img_card_logo'])){
			$home_user_info['card_real_img']=getQiniuPath($home_user_info['img_card_logo'],'img');
			$home_user_info['card_real_img']=$home_user_info['card_real_img']."?imageView2/1/w/280/h/180";
		}
		$GLOBALS['tmpl']->assign("home_user_info",$home_user_info);
		$GLOBALS ['tmpl']->assign("pageType",PAGE_MENU_USER_CENTER);
		$GLOBALS ['tmpl']->assign("sideType",SIDE_MENU_ACCOUNT);
		$GLOBALS['tmpl']->display("account_security.html");
	}
		 
  
	public function user_update(){
	
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$user['user_name'] = strim($_REQUEST['user_name']);
		$res=check_len($user['user_name'] ,6,1,"真实姓名");
		if($res['status']==0){
			ajax_return($res);
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
		$user_info['id'] = $user['id'];
		$user_info['user_type'] = $user['user_type'];
		$user_info['user_name'] = $user['user_name'];
		es_session::set(USER_SESSION_KEY,$user_info);
	
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
		$user_info = es_session::get(USER_SESSION_KEY);
		if($user_info){
			es_session::delete(USER_SESSION_KEY);
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
		$user_rst = $GLOBALS ['db']->getRow ("select is_effect from ".DB_PREFIX."user where mobile = '".$new_mobile."'");
		if($user_rst){
			if($user_rst['is_effect'] == self::DEFECT_USER){
				$res['status'] = 0;
				$res['info'] = "该手机号码已经禁用";
				$res['error_msg'] = "#get_news_mobile_msg";
				ajax_return($res);
			}else{
				$res['status'] = 0;
				$res['info'] = "手机号已注册";
				$res['error_msg'] = "#get_news_mobile_msg";
				ajax_return($res);
			}
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
		$user_info = es_session::get(USER_SESSION_KEY);
		if($user_info){
			es_session::delete(USER_SESSION_KEY);
		}
		ajax_return($res);
	}
	
	
}
?>