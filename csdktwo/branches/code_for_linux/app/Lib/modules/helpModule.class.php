<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------
class helpModule extends BaseModule
{
		//帮助中心
		public function index()
	  {		
          $url=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          if(strstr($url,"Newbie")){
          $GLOBALS['tmpl']->assign("id",$id=1); 
             } 
          if(strstr($url,"ToS")){
          $GLOBALS['tmpl']->assign("id",$id=2); 
             } 
         if(strstr($url,"FAQ")){
         $GLOBALS['tmpl']->assign("id",$id=3); 
             } 
      	$new_hand= $GLOBALS['db']->getRow("select id,content from ".DB_PREFIX."help where id=6");
      	$service_terms= $GLOBALS['db']->getRow("select id,content from ".DB_PREFIX."help where id=1");
      	$faq = $GLOBALS['db']->getRow("select id,content from ".DB_PREFIX."help where id=7");
        $GLOBALS['tmpl']->assign("new_hand",$new_hand);
        $GLOBALS['tmpl']->assign("service_terms",$service_terms);
        $GLOBALS['tmpl']->assign("faq",$faq);

		$GLOBALS['tmpl']->display("help_center.html");
	}
	// 关于我们
    public function about_us()
	  {		
      	$about_us = $GLOBALS['db']->getRow("select id,content from ".DB_PREFIX."help  where id=4 ");
        $GLOBALS['tmpl']->assign("about_us",$about_us);
		$GLOBALS['tmpl']->display("about_us.html");
	}
    // 联系我们
    public function contact_us()
	  {		
      	$contact_us = $GLOBALS['db']->getRow("select id,content from ".DB_PREFIX."help  where id=5 ");
        $GLOBALS['tmpl']->assign("contact_us",$contact_us);
		$GLOBALS['tmpl']->display("contact_us.html");
	}
}
?>