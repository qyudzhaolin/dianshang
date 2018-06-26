<?php


class commonModule extends BaseModule
{
	public function __construct(){
		parent::__construct();
	}
	public function index()
	{	
	}	
	
	public function add_dcom()
	{
		$file_name = $_REQUEST['file'];
		$len = intval($_REQUEST['len'])+1;
		$GLOBALS['tmpl']->assign("len",$len);
		$GLOBALS['tmpl']->display($file_name.'.html');
	}
}
?>