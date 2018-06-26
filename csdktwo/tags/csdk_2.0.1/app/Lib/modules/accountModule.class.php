<?php
class accountModule extends BaseModule
{
	public function __construct(){
		parent::__construct();
		parent::is_login();
	}
	public function index(){		
		//$GLOBALS['tmpl']->assign("gid",$gid);
		//$GLOBALS['tmpl']->display("vip_account_introduction.html");			
	}
	public function vip_account_introduction(){
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step <= 3)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];
		$vip_account = $GLOBALS['db']->getRow("select vip_money,vip_begin_time,vip_end_time from ".DB_PREFIX."user_ex_investor where user_id = ".$id);
		if($vip_account['vip_money'] && $vip_account['vip_begin_time'] && $vip_account['vip_end_time']){
			$is_exist_vip_account=1;
		}
		
		$source = $_REQUEST['source'];
		if($is_exist_vip_account == 1 && empty($source)){
			$this->vip_account_account();
		}else{
			$GLOBALS['tmpl']->assign("vip_account_introduction","vip_account_introduction");
			$GLOBALS['tmpl']->display("vip_account_introduction.html");
		}
	}
	public function vip_account_fee(){
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step <= 3)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];
		$count_id = strlen($id);
		$count_inc = substr("0000000",0,7-$count_id);
		$ident_code = "A".$count_inc.$id;	
		$GLOBALS['tmpl']->assign("ident_code",$ident_code);
		$id = $GLOBALS['user_info']['id'];
		$vip_account = $GLOBALS['db']->getRow("select vip_money,vip_begin_time,vip_end_time from ".DB_PREFIX."user_ex_investor where user_id = ".$id);
		if($vip_account['vip_money'] && $vip_account['vip_begin_time'] && $vip_account['vip_end_time']){
			$is_exist_vip_account=1;
		}

		$source = $_REQUEST['source'];
		if($is_exist_vip_account == 1 && empty($source)){
			$this->vip_account_account();
		}else{
			$GLOBALS['tmpl']->assign("vip_account_fee","vip_account_fee");
			$GLOBALS['tmpl']->display("vip_account_fee.html");
		}
	}
	public function vip_account_account(){
		$GLOBALS['tmpl']->assign("page_title","个人中心");
		$side_step = $GLOBALS['user_info']['side_step'];
		if($side_step <= 3)	{	
			app_redirect(url("home"));
			return;
		}
		$id = $GLOBALS['user_info']['id'];
		$vip_account = $GLOBALS['db']->getRow("select vip_money,vip_begin_time,vip_end_time from ".DB_PREFIX."user_ex_investor where user_id = ".$id);
		if($vip_account['vip_money']  == 10 && $vip_account['vip_begin_time'] && $vip_account['vip_end_time']){
			$vip_money = 1;
			$vip_account['vip_begin_time'] = date("Y-m-d",$vip_account['vip_begin_time']);
			$vip_account['vip_end_time'] = date("Y-m-d",$vip_account['vip_end_time']);
		}else{
			$vip_money = 0;
		}
		$user['side_step']= strim($_REQUEST['side_step']);
		if($user['side_step'] == 5){
			$user['is_review'] = 2;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
		}		
		$GLOBALS['tmpl']->assign("vip_account",$vip_account);
		$GLOBALS['tmpl']->assign("vip_money",$vip_money);
		$GLOBALS['tmpl']->assign("vip_account_account","vip_account_account");
		$GLOBALS['tmpl']->display("vip_account_account.html");
	}
}
?>