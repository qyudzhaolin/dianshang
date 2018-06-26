<?php
class choiceModule extends BaseModule
{
	
	public function index()
	{	
		$user_type = $GLOBALS['user_info']['user_type'];
		if($user_type == 0 )	{	
			app_redirect(url("home"));
			return;
		}
		$GLOBALS['tmpl']->assign("page_title","注册成功");
 		$GLOBALS['tmpl']->display("choice.html");		

 	}
 	public function apply_help_update(){
		$id =$GLOBALS['user_info']['id'];
		
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$apply_help_time = $GLOBALS['db']->getOne("select apply_help_time from ".DB_PREFIX."user  where id = ".$id);
		
		if(is_null($apply_help_time)){
			$user['apply_help_time'] = time(); 
			$result=$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,"UPDATE","id=".$id,"SILENT");
			if(!$result){
					$res['status'] = 0;
					ajax_return($res);
			}
		}
		ajax_return($res);
	}
	
	
}
?>