<?php
class appnewsModule extends BaseModule
{
	 
	public function index(){
 

	 	$GLOBALS['tmpl']->assign("page_title","新闻");

	 	$id = intval($_REQUEST ['id']);

 	 	$appnews = $GLOBALS['db']->getRow("select id,n_title,n_app_img,n_desc,n_source,create_time from ".DB_PREFIX."news  where id = ".$id); 
 		
 		if(strim($appnews['n_app_img']))
			{
				$appnews['n_app_img']=getQiniuPath($appnews['n_app_img'],'img');
				$appnews['n_app_img']=$appnews['n_app_img']."?imageView2/1/";
			}
 	 	$appnews['n_title']=urldecode($appnews['n_title']);
 	 	$appnews['n_source']=urldecode($appnews['n_source']);
	 	$appnews['n_desc']=nl2br($appnews['n_desc']); 
	 	$appnews['create_time']=date("Y年m月d日 H:i",$appnews['create_time']);
		$GLOBALS['tmpl']->assign("appnews",$appnews);
		$GLOBALS['tmpl']->display("appnews.html"); 
	}
 
}
?>