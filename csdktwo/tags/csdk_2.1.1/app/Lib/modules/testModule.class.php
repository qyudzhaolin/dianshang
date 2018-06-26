<?php


class testModule extends BaseModule
{
	public function index()
	{	
		$t = $_REQUEST ['t'];	
		$GLOBALS['tmpl']->display($t.".html");
	}	
	
}
?>