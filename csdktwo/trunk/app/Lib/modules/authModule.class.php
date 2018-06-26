<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
class authModule extends BaseModule
{
	public function __construct(){
		parent::__construct();
		parent::is_login();
	}
	public function index()
	{		
	
	}

	public function investor_show()
	{	
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step < 4)	{	
			app_redirect(url("home"));
			return;
		}	
		$id = $GLOBALS['user_info']['id'];
		$is_review=$GLOBALS['user_info']['is_review'];
		$user['side_step']= strim($_REQUEST['side_step']);

		
		if($user['side_step'] == 5&&$is_review==0){
			$user['is_review'] = 2;
			$user['side_step'] = 5;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
			$GLOBALS['tmpl']->assign("is_review",$user['is_review']);
			$GLOBALS['tmpl']->assign("side_step",$user['side_step']);
		}
		$GLOBALS['tmpl']->assign("auth_investor","auth_investor");
		$GLOBALS['tmpl']->display("auth_investor.html");
	}
/*	public function investor_update(){
		$id =$GLOBALS['user_info']['id'];
		
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		$user['is_review'] = 2;

		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		if(!$result){
				$res['status'] = 0;
				ajax_return($res);
		}
		ajax_return($res);
	}*/
	
	public function estp_show()
	{
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step < 4)	{	
			app_redirect(url("home"));
			return;
		}	
		$GLOBALS['tmpl']->assign("auth_estp","auth_estp");
		$GLOBALS['tmpl']->display("auth_estp.html");


	}
	public function estp_update(){
		$id =$GLOBALS['user_info']['id'];
		
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据

		$user['is_review'] = 2;

		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		if(!$result){
				$res['status'] = 0;
				ajax_return($res);
		}
		ajax_return($res);
	}
	
}
?>