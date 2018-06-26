<?php
/**
* APP下载页面-SC
*/
class downloadModule extends BaseModule{
 	public function index(){
 		$GLOBALS ['tmpl']->assign ( "pageType", PAGE_MENU_APP);
		$GLOBALS['tmpl']->assign("page_title","APP");
		$GLOBALS['tmpl']->display("download.html");		
	}
}

?>
