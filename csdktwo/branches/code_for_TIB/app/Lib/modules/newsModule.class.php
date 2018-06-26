<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class newsModule extends BaseModule{
    
    private $rows = 10; # 列表显示行数
    
	/*
	 * 资讯详情页
	 * @param int $id 资讯ID
	 * 
	*/
	public function index(){
		 
		$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		 
		if ($id == 0) {
			// 404
			app_redirect(url("error#index"));
		}
		$field    = "id,n_code,n_title,n_list_img,n_app_img,n_desc,n_source,create_time";
		$info     = $GLOBALS['db']->getRow("select {$field} from ".DB_PREFIX."news where id = {$id} and n_publish_state = 2");
		if (empty($info)) {
			// 404
			app_redirect(url("error#index"));
		}
		$GLOBALS['tmpl']->assign("page_title","资讯内容");
		$GLOBALS['tmpl']->assign("info",$info);
		$GLOBALS['tmpl']->assign ("pageType", PAGE_MENU_INDEX);
		$GLOBALS['tmpl']->display("news_details.html");
	}
	/*
	 * 资讯列表页
	 * 
	*/
	public function lists(){
		$id       =  isset($_GET['id'])? intval($_GET['id']) : 1;
		$class    = app_enum_conf('NEWS_CLASS');
		if(!in_array($id,array($class[0]['id'],$class[1]['id']))){
			$id = 1;
		}
		//新闻资讯
		$channel      = app_enum_conf('NEWS_CHANNEL');
		$field        = "id,n_title,n_list_img,n_desc,n_brief,n_source,create_time";
		$map          = "n_channel in({$channel[1]['id']},{$channel[2]['id']}) and n_class = {$id} and n_publish_state = 2";
		$list         = $GLOBALS['db']->getAll("select {$field} from ".DB_PREFIX."news where {$map} order by create_time desc limit {$this->rows}");
		$list_count   = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."news where {$map}");
		
		$GLOBALS['tmpl']->assign("list",$list);
		$GLOBALS['tmpl']->assign("class",$class);
		$GLOBALS['tmpl']->assign("id",$id);
		$GLOBALS['tmpl']->assign("list_more",count($list) < $list_count ? 1 : 0);	  # 加载更多按钮显示(1)隐藏(0)标识位
		$GLOBALS['tmpl']->assign("pageType", PAGE_MENU_INDEX);
		$GLOBALS['tmpl']->display("news_lists.html");
	}
	
	/*
	 * 资讯列表页-显示更多
	 * 
	*/
	public function newslist_tab(){
		$channel      = app_enum_conf('NEWS_CHANNEL');
		$corner       = isset($_POST['corner']) ?  intval($_POST['corner']) : 0;
		$counter      = isset($_POST['counter'])?  intval($_POST['counter']) : 0;
		$serchtitle   = isset($_POST['serchtitle'])?  mysql_real_escape_string(trim($_POST['serchtitle'])) : "";
		
		if($corner){
			$res =array('status'=>1,'info'=>'','data'=>'','more'=>'');
			
			$field = "id,n_title,n_list_img,n_desc,n_brief,n_source,create_time";
			$map   = "";
			if($serchtitle){
			    $map .= " n_title like '%{$serchtitle}%' and";
			}
			$map .= " n_channel in({$channel[1]['id']},{$channel[2]['id']}) and n_class = {$corner} and n_publish_state = 2";
			
			$sql     = "select {$field} from ".DB_PREFIX."news where {$map} order by create_time desc limit {$counter},{$this->rows}";
			$list    = $GLOBALS['db']->getAll($sql);
			if(empty($list)){
			    $res['status'] 	= 99;
			    $res['info']    = '暂无相关资讯';
			}else{
			    foreach ($list as $k=>$v) {
			        $list[$k]['n_title']      = msubstr($v['n_title'],0,20,'utf-8',false);
			        $list[$k]['n_list_img']   = getQiniuPath($v['n_list_img'],'img').'?imageView2/1/w/200/h/200';
			        $list[$k]['n_brief']      = msubstr($v['n_brief'],0,70);
			        $list[$k]['create_time']  = to_date($v['create_time'],"Y年m月d日");
			    }
			    
			    $list_count  = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."news where {$map}");
			    $res['more'] = count($list) + $counter < $list_count ? 1 : 0;
			    $res['data'] = $list;
			}
		}else{
			$res['status']   = 0;
			$res['info']     = "Error:参数丢失";
		}
		
		ajax_return($res);
	}
}
?>