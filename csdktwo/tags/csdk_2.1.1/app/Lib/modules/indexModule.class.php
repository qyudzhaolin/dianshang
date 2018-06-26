<?php
// +----------------------------------------------------------------------
// | 资讯相关
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class indexModule extends BaseModule{
	
	private $news_rows = 10; # 资讯列表显示行数
	private $news_slide_rows = 5; # 资讯侧栏列表显示行数
	
	/*
	 * 首页默认方法
	 */
	public function index(){
		
		
		
		// 首页轮播
		$field = "id,b_title,b_url,b_pc_img";
		$channel = app_enum_conf('BANNER_CHANNEL');
		$group = app_enum_conf('BANNER_GROUP');
		$carousel = $GLOBALS['db']->getAll("select {$field} from ".DB_PREFIX."banner where b_channel in({$channel[1]['id']},{$channel[2]['id']}) and b_bygroup = {$group[0]['id']} and b_publish_state = 2 order by b_sort desc limit 6");
		foreach($carousel as &$v){
		    $v['b_url'] = $v['b_url'] ? (stristr($v['b_url'],'http://') ? $v['b_url'] : 'http://'.$v['b_url']) : '';
		    $v['b_pc_img'] = getQiniuPath($v['b_pc_img'],'img')."?imageView2/1/w/1000/h/360";
		}
		unset($v);
		
		// 首页tab-全部
		$field = "id,n_code,n_title,n_brief,n_list_img,n_app_img,n_source,create_time";
		$channel = app_enum_conf('NEWS_CHANNEL');
		$new_all = $GLOBALS['db']->getAll("select {$field} from ".DB_PREFIX."news where n_channel in({$channel[1]['id']},{$channel[2]['id']}) and n_publish_state = 2 order by create_time desc limit {$this->news_rows}");
		$new_all_count = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."news where n_channel in({$channel[1]['id']},{$channel[2]['id']}) and n_publish_state = 2");
		
		// 首页新闻资讯
		$class = app_enum_conf('NEWS_CLASS');
		$news_out = $GLOBALS['db']->getAll("select {$field} from ".DB_PREFIX."news where n_channel in({$channel[1]['id']},{$channel[2]['id']}) and n_class = {$class[0]['id']} and n_publish_state = 2 order by create_time desc limit {$this->news_slide_rows}");
		
		// 首页磁斯达克动态
		$news_in = $GLOBALS['db']->getAll("select {$field} from ".DB_PREFIX."news where n_channel in({$channel[1]['id']},{$channel[2]['id']}) and n_class = {$class[1]['id']} and n_publish_state = 2 order by create_time desc limit {$this->news_slide_rows}");
		
		$GLOBALS['tmpl']->assign("carousel",$carousel);
		$GLOBALS['tmpl']->assign("new_all",$new_all);
		$GLOBALS['tmpl']->assign("new_all_more",count($new_all) < $new_all_count ? 1 : 0);	  # 加载更多按钮显示(1)隐藏(0)标识位
		$GLOBALS['tmpl']->assign("news_out",$news_out);
		$GLOBALS['tmpl']->assign("news_in",$news_in);
		$GLOBALS['tmpl']->assign("corner",app_enum_conf('NEWS_CORNER'));
		$GLOBALS['tmpl']->assign("class",$class);
		$GLOBALS ['tmpl']->assign ("pageType", PAGE_MENU_INDEX);
		$GLOBALS['tmpl']->display("index.html");
	}
	
	/*
	 * 首页资讯加载更多接口
	 * @param int $conner 资讯类型，0 全部 1热点 2推荐 3项目咨询（指已关联项目的资讯）
	 * @param int $counter 资讯条数计数器
	 */	
	public function news_tab(){
		$corner 	=  isset($_POST['corner'])? intval($_POST['corner']) : false;
		$counter 	=  isset($_POST['counter'])? intval($_POST['counter']) : 0;
		
		if($corner !== false){
			$res 		= array('status'=>1,'info'=>0,'data'=>'','more'=>''); //用于返回的数据
			$channel 	= app_enum_conf('NEWS_CHANNEL');
			
			$field 	= "id,n_code,n_title,n_brief,n_list_img,n_app_img,n_source,create_time";
			$sql 	= "select {$field} from ".DB_PREFIX."news";
			$map    = "  where n_channel in({$channel[1]['id']},{$channel[2]['id']}) and n_publish_state = 2";
			if($corner == 3){
				$map .= " and n_deal <> 0";
			}else if($corner > 0){
				$map .= " and n_corner = {$corner}";
			}
			$sql_    = " order by create_time desc limit {$counter},{$this->news_rows}";
			$news    = $GLOBALS['db']->getAll($sql.$map.$sql_);
			
			if(empty($news)){
			    $res['status'] 	= 99;
			    $res['info']    = '暂无相关资讯';
			}else{
				foreach ($news as $k=>$v) {
					$news[$k]['n_title'] 		= msubstr($v['n_title'],0,20,'utf-8',true);
					$news[$k]['n_list_img'] 	= getQiniuPath($v['n_list_img'],'img').'?imageView2/1/w/200/h/200';
					$news[$k]['n_brief'] 		= msubstr($v['n_brief'],0,75);
					$news[$k]['create_time'] 	= to_date($v['create_time'],"Y年m月d日");
					$news[$k]['url'] 			= url('news',array('id'=>$v['id']));
				}
				
				$news_count     = $GLOBALS['db']->getOne("select count(`id`) from ".DB_PREFIX."news".$map);
				$res['more']    = count($news) + $counter < $news_count ? 1 : 0;
				$res['data']    = $news;
			}
		}else{
			$res['status'] 	= 0;
			$res['info'] 	= "Error:参数丢失";
		}
		ajax_return($res);
	}
}
?>