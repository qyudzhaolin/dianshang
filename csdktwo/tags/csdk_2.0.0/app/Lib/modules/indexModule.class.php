<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class indexModule extends BaseModule
{
	public function index()
	{	
		//$user_info = es_session::get("user_info");
		//$GLOBALS['tmpl']->assign('user_info',$user_info);
		$GLOBALS['tmpl']->display("index.html");
	}
	

	
	
}
?>