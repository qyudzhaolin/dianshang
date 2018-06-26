<?php
class errorModule extends BaseModule
{
	
	public function index()
	{
		$GLOBALS['tmpl']->assign("page_title","404");
		$GLOBALS['tmpl']->display("error404.html");		
	}
	
	
}
?>