<?php
// +----------------------------------------------------------------------
// | 个人中心-我的消息
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class messageModule extends BaseModule{
	
	private $rows = 5;
	
	public function index(){
		$user_id = $GLOBALS['user_info']['id'];
		
		$msg_list     = $GLOBALS['db']->getAll("select id,log_info,url,log_time,is_read from ".DB_PREFIX."user_notify where user_id = {$user_id} order by is_read,log_time desc limit {$this->rows}");
		$msg_count    = $GLOBALS['db']->getOne("select count(1) from ".DB_PREFIX."user_notify where user_id = {$user_id}");
		$msg_list_more = count($msg_list) < $msg_count ? 1 : 0;
		
		// 更新当前用户的所有消息为已读状态
		$user_notify['is_read'] = 1;
		$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user_notify",$user_notify,"UPDATE","user_id={$user_id}","SILENT");
		
		$GLOBALS['tmpl']->assign("msg_list",$msg_list);
		$GLOBALS['tmpl']->assign("msg_list_more",$msg_list_more);
		$GLOBALS ['tmpl']->assign("pageType",PAGE_MENU_USER_CENTER);
		$GLOBALS ['tmpl']->assign("sideType",SIDE_MENU_MESSAGE);
		$GLOBALS['tmpl']->assign("page_title","我的消息");
		$GLOBALS['tmpl']->display("message.html");
	}
	
	public function more(){
		$user_id = $GLOBALS['user_info']['id'];
		$res = array('status'=>99,'info'=>0,'data'=>'','more'=>''); //用于返回的数据
		
		if($user_id){
			$counter     =  isset($_POST['counter'])? intval($_POST['counter']) : 0;
			$msg_list    = $GLOBALS['db']->getAll("select id,log_info,url,log_time,is_read from ".DB_PREFIX."user_notify where user_id = {$user_id} order by is_read,log_time desc limit {$counter},{$this->rows}");
				
			if($msg_list){
			    
				foreach($msg_list as &$v){
					$v['log_time'] = to_date($v['log_time'],'Y年m月d日');
				}
				unset($v);
				
			    $msg_count   = $GLOBALS['db']->getOne("select count(1) from ".DB_PREFIX."user_notify where user_id = {$user_id}");
			    $res['more'] = count($msg_list) + $counter < $msg_count ? 1 : 0;
				$res['data'] = $msg_list;
			}else{
				$res['status'] 	= 1;
				$res['info'] 	= "没有数据";
			}
		}else{
			$res['status'] 	= 0;
			$res['info'] 	= "Error:用户丢失";
		}
		ajax_return($res);
	}
	
	/*public function index()
	{
		if(!$GLOBALS['user_info'])
		{
			app_redirect(url("user#login"));
		}
		
		
		$page_size = 20;
		$page = intval($_REQUEST['p']);
		if($page==0)$page = 1;		
		$limit = (($page-1)*$page_size).",".$page_size	;

		$sql = "select max(id) as id,count(id) as total,dest_user_id from ".DB_PREFIX."user_message  where user_id = ".intval($GLOBALS['user_info']['id'])." group by dest_user_id order by create_time desc limit ".$limit;
		$sql_count = "select count(distinct(dest_user_id)) from ".DB_PREFIX."user_message where user_id = ".intval($GLOBALS['user_info']['id']);

		$contact_list = $GLOBALS['db']->getAll($sql);
		$contact_count = $GLOBALS['db']->getOne($sql_count);	

		foreach($contact_list as $k=>$v)
		{
			$contact_list[$k] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_message where id = ".$v['id']);
			$contact_list[$k]['has_new'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_message where is_read = 0 and dest_user_id = ".$v['dest_user_id']." and user_id = ".intval($GLOBALS['user_info']['id']));
			$contact_list[$k]['total'] = $v['total'];
		}

		$GLOBALS['tmpl']->assign("contact_count",$contact_count);
		$GLOBALS['tmpl']->assign("contact_list",$contact_list);
		
		require APP_ROOT_PATH.'app/Lib/page.php';
		$page = new Page($contact_count,$page_size);   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);	
		
		$GLOBALS['tmpl']->display("message_index.html");
	}
	
	public function history()
	{
		if(!$GLOBALS['user_info'])
		{
			app_redirect(url("user#login"));
		}
		
		$dest_user_id = intval($_REQUEST['id']);
		$dest_user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$dest_user_id." and is_effect = 1");
		if(!$dest_user_info)
		{
			app_redirect(url("message"));
		}
		
		$GLOBALS['db']->query("update ".DB_PREFIX."user_message set is_read = 1 where user_id = ".intval($GLOBALS['user_info']['id'])." and dest_user_id = ".$dest_user_id);
		$page_size = 20;
		$page = intval($_REQUEST['p']);
		if($page==0)$page = 1;		
		$limit = (($page-1)*$page_size).",".$page_size	;

		$sql = "select * from ".DB_PREFIX."user_message  where user_id = ".intval($GLOBALS['user_info']['id'])." and dest_user_id = ".$dest_user_id." order by create_time desc limit ".$limit;
		$sql_count = "select count(*) from ".DB_PREFIX."user_message where user_id = ".intval($GLOBALS['user_info']['id'])." and dest_user_id = ".$dest_user_id;

		$message_list = $GLOBALS['db']->getAll($sql);
		$message_count = $GLOBALS['db']->getOne($sql_count);	

		$GLOBALS['tmpl']->assign("dest_user_info",$dest_user_info);
		$GLOBALS['tmpl']->assign("message_list",$message_list);
		
		require APP_ROOT_PATH.'app/Lib/page.php';
		$page = new Page($message_count,$page_size);   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);	
		
		$GLOBALS['tmpl']->display("message_history.html");
	}
	
	public function send()
	{
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		$receive_user_id = intval($_REQUEST['id']);		
		$send_user_id = intval($GLOBALS['user_info']['id']);
		if($receive_user_id==$send_user_id)
		{
			showErr("不能向自己发私信",$ajax);
		}
		else
		{
			$receive_user_info = $GLOBALS['db']->getRow("select user_name from ".DB_PREFIX."user where is_effect = 1 and id = ".$receive_user_id);
			if(!$receive_user_info)
			{
				showErr("收信人不存在",$ajax);
			}
			
			//发私信：生成发件与收件
			//1.生成发件
			$data = array();
			$data['create_time'] = NOW_TIME;
			$data['message'] = strim($_REQUEST['message']);
			$data['user_id'] = $send_user_id;
			$data['dest_user_id'] = $receive_user_id;
			$data['send_user_id'] = $send_user_id;
			$data['receive_user_id'] = $receive_user_id; 
			$data['user_name'] = $GLOBALS['user_info']['user_name'];
			$data['dest_user_name'] = $receive_user_info['user_name']; 
			$data['send_user_name'] = $GLOBALS['user_info']['user_name'];
			$data['receive_user_name'] = $receive_user_info['user_name']; 
			$data['message_type'] = "outbox";
			$data['is_read'] = 1;

			$GLOBALS['db']->autoExecute(DB_PREFIX."user_message",$data);
			
			//2.生成收件
			$data = array();
			$data['create_time'] = NOW_TIME;
			$data['message'] = strim($_REQUEST['message']);
			$data['user_id'] = $receive_user_id;
			$data['dest_user_id'] = $send_user_id;
			$data['send_user_id'] = $send_user_id;
			$data['receive_user_id'] = $receive_user_id; 
			$data['user_name'] = $receive_user_info['user_name'];
			$data['dest_user_name'] = $GLOBALS['user_info']['user_name'];
			$data['send_user_name'] = $GLOBALS['user_info']['user_name'];
			$data['receive_user_name'] = $receive_user_info['user_name']; 
			$data['message_type'] = "inbox";
	
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_message",$data);
			
			showSuccess("发送成功",$ajax);
		}
	}
	
	public function delcontact()
	{
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		$dest_user_id = intval($_REQUEST['id']);
		$user_id = intval($GLOBALS['user_info']['id']);
		$GLOBALS['db']->query("delete from ".DB_PREFIX."user_message where user_id = ".$user_id." and dest_user_id = ".$dest_user_id);
		
		showSuccess("",$ajax,get_gopreview());
	}
	
	public function delmessage()
	{
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		$id = intval($_REQUEST['id']);
		$user_id = intval($GLOBALS['user_info']['id']);
		$GLOBALS['db']->query("delete from ".DB_PREFIX."user_message where user_id = ".$user_id." and id = ".$id);
		
		showSuccess("",$ajax,get_gopreview());
	}
	*/
}
?>