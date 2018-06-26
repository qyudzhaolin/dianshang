<?php
class aboutModule extends BaseModule
{
	
	public function index()
	{
		$GLOBALS['tmpl']->assign("page_title","帮助中心");
		$id = intval($_REQUEST ['id']);
		$gid = intval($_REQUEST ['gid']);
		$intro = $GLOBALS['db']->getRow("select content from ".DB_PREFIX."help  where id = ".$id);

		$intro_term = $GLOBALS['db']->getRow("select content from ".DB_PREFIX."help  where id = 1");
		$intro_term_show = $intro_term["content"];
		$GLOBALS['tmpl']->assign("intro_term_show",$intro_term_show);

		$intro_faq = $GLOBALS['db']->getRow("select content from ".DB_PREFIX."help  where id = 9");
		$intro_faq_show = $intro_faq["content"];
		$GLOBALS['tmpl']->assign("intro_faq_show",$intro_faq_show);
		if($id == 4)
		{
  			$intro_about_show = $intro["content"];
			$GLOBALS['tmpl']->assign("id",$id);
			$GLOBALS['tmpl']->assign("intro_about_show",$intro_about_show);
			$GLOBALS['tmpl']->assign("intro_about","intro_about");
			//$GLOBALS['tmpl']->display("intro_about.html");			
		}
		if($id == 5)
		{
 			$intro_contact_show = $intro["content"];
			$GLOBALS['tmpl']->assign("id",$id);
			$GLOBALS['tmpl']->assign("intro_contact_show",$intro_contact_show);
			$GLOBALS['tmpl']->assign("intro_contact","intro_contact");
			//$GLOBALS['tmpl']->display("intro_about.html");			
		}
		if($id == 6)
		{
 			$intro_help_show = $intro["content"];
			$GLOBALS['tmpl']->assign("id",$id);
			$GLOBALS['tmpl']->assign("intro_help_show",$intro_help_show);
			$GLOBALS['tmpl']->assign("intro_help","intro_help");
						
		}

		$GLOBALS['tmpl']->assign("gid",$gid);
		$GLOBALS['tmpl']->display("intro_about.html");		

	
	}
	
	
}
?>