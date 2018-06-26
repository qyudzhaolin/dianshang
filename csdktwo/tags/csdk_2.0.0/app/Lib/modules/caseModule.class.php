<?php
/**
* 成功案例
*/
class caseModule extends BaseModule
{
 	public function index()
	{	
		$GLOBALS['tmpl']->assign("page_title","成功案例");
		$GLOBALS['tmpl']->display("success_case_01.html");		
	}

	public function east()
	{	
		$GLOBALS['tmpl']->assign("page_title","成功案例");
		$GLOBALS['tmpl']->display("success_case_02.html");		
	}

	public function car()
	{	
		$GLOBALS['tmpl']->assign("page_title","成功案例");
		$GLOBALS['tmpl']->display("success_case_03.html");		
	}
}

?>
