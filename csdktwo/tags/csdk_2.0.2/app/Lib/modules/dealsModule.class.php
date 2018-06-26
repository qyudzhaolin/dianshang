<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2016 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class dealsModule extends BaseModule{
	
	public function index(){
		$GLOBALS['tmpl']->assign("page_title","平台项目");
 
		//行业列表
		$cate_result = load_dynamic_cache("INDEX_CATE_LIST");
 		if($cate_result===false)
		{
			$cate_list = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."deal_cate order by sort asc");
			$cate_result= array();
			foreach($cate_list as $k=>$v)
			{
				$cate_result[$v['id']] = $v;
			}
			set_dynamic_cache("INDEX_CATE_LIST",$cate_result);
		}
		$GLOBALS['tmpl']->assign("cate_list",$cate_result);
		
		//轮次映射列表
		$period_result = load_dynamic_cache("INDEX_PERIOD_LIST");
		if($period_result===false)
		{
			$period_list = $GLOBALS['db']->getAll("select id,mapname from ".DB_PREFIX."deal_period GROUP BY mapname 
		ORDER BY sort asc");
			$period_result= array();
			foreach($period_list as $k=>$v)
			{
				$period_result[$v['id']] = $v;
			}
			set_dynamic_cache("INDEX_PERIOD_LIST",$period_result);
		}
		$GLOBALS['tmpl']->assign("period_list",$period_result);
		
		//地区一二级列表
		$region_pid = 0;
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['id'] == $home_user_info['province'])
			{
				$region_lv2[$k]['selected'] = 1;
				$region_pid = $region_lv2[$k]['id'];  //var_dump($region_lv2[$k]);
				$region_pname_province = $region_lv2[$k]['name'];  //var_dump($region_lv2[$k]);
				break;
			}
		}
		$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid=".$region_pid);
		foreach($region_lv3 as $k=>$v)
		{
			if($v['id'] == $home_user_info['city'])
			{
				$region_lv3[$k]['selected'] = 1;
				$region_pid = $region_lv3[$k]['id'];  //var_dump($region_lv2[$k]);
				$region_pid = $region_lv3[$k]['id'];  //var_dump($region_lv2[$k]);
				$region_pname_city = $region_lv3[$k]['name'];
				break;
			}
		}  //二级地址
		 
		$GLOBALS['tmpl']->assign("per_degree_list",$per_degree_list);
		$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
		$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		$GLOBALS ['tmpl']->assign ( "pageType", PAGE_MENU_DEAL );
		$GLOBALS['tmpl']->display("deals.html");
	}
	
	
	
	
	/*                      项目筛选                           */
	/* 平台列表显示个数 */
	private $deal_rows = 5;
	public function deal_choose(){

		/* 提取用户身份 */
		$user_id = $GLOBALS['user_info']['id'];
		if($user_id){
			$user_review =  $GLOBALS['db']->getOne("select is_review from ".DB_PREFIX."user where id =$user_id");
		}else{
			$user_review = false;
		}
		
		/* 筛选条件 */
		$counter 	=  isset($_POST['counter'])? intval($_POST['counter']) : 0;
		$cate 		= isset($_REQUEST['cate_choose'])? 		trim($_REQUEST['cate_choose'])		:NULL;
		$district 	= isset($_REQUEST['district'])? 	trim($_REQUEST['district'])	:NULL;
		$period_id 	= isset($_REQUEST['period_id'])? 	trim($_REQUEST['period_id'])	:NULL;
// 		$focus_filter 	= isset($_REQUEST['focus_filter'])? 	trim($_REQUEST['focus_filter'])	:NULL;
// 		$update_time 	= isset($_REQUEST['update_time'])? 	trim($_REQUEST['update_time'])	:NULL;
// 		$intend  	= isset($_REQUEST['intend'])? 	trim($_REQUEST['intend'])	:NULL;
		$sort  	= isset($_REQUEST['sort'])? 	trim($_REQUEST['sort'])	:NULL;
		
		$sql_select = "";
		$sql_from = "";
		$sql_where = "";
		$condition = "";
		$sql_order_by_condition = "";
		$sql_final = "";
		$sql_select ="
				      select cate_choose 		deal_cates
				      ,id 		   		id
					  ,img_deal_logo 	image_deal_logo
					  ,name				deal_name
					  ,deal_sign		deal_sign
					  ,comment_count	comment_count
					  ,period_id 		period_id
					  ,province			province
					  ,city				city
					  ,is_effect		is_effect
				";
		$sql_from =" from ".DB_PREFIX."deal deal ";
		 
		$sql_where = "
				where   is_publish=2
				";

		if (! empty ( $cate )) {
			$subQuery_get_deal_id_list = "select deal_id from cixi_deal_select_cate where cate_id = {$cate}";
			$condition .= "and id in ($subQuery_get_deal_id_list)";
		}
		
		if(!empty($district)){
			$district = explode('_',$district);
// 			var_dump($district);
			if($district[0]){
				$condition .= " and province = {$district[0]}";
			}
			if($district[1]){
				$condition .= " and city = {$district[1]}";
			}
		}
	 
		if (! empty ( $period_id )) {
			$subQuery_get_period_id_list = "select 	id 		from	".DB_PREFIX."deal_period where mapname=(SELECT mapname from cixi_deal_period where id=$period_id)";
			$condition .= " and period_id in ($subQuery_get_period_id_list)";
		}
		
		$condition .= " and is_delete=0";
		
		if(!empty($sort)){
			switch($sort){
				case 2:
					$sql_order_by_condition = 'order by is_effect asc,comment_count desc, update_time desc';
					break;
				case 1:
					$sql_order_by_condition = 'order by is_effect asc,update_time desc, create_time desc';
					break;
				default:
					$sql_order_by_condition = 'order by is_effect asc,sort asc, update_time desc';
			}
		}
		
		$sql_limit ="limit {$counter},{$this->deal_rows}";
		$sql_final = $sql_select . " " . $sql_from . " " . $sql_where . " " . $condition . " " . $sql_order_by_condition ." " . $sql_limit ;
		//var_dump($sql_final);
		$deal_list_item = $GLOBALS['db']->getAll($sql_final);
		
		//取出项目个数
		$sql_final_last_deal = $sql_select . " " . $sql_from . " " . $sql_where . " " . $condition . " " . $sql_order_by_condition  ;
		$deal_list_last = $GLOBALS['db']->getAll($sql_final_last_deal);
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		if (!empty($deal_list_item)){
		$project_info = array();
		foreach($deal_list_item as $key =>$val)
		{
			$project_id = $deal_list_item[$key]["id"];
			$deal_period =  $GLOBALS['db'] ->getOne("select period.name as name  from ".DB_PREFIX."deal project,".DB_PREFIX."deal_period period where project.period_id = period.id and project.id = ".$project_id." ");
			$deal_province = $GLOBALS['db'] -> getOne("select name from ".DB_PREFIX."region_conf where pid = 1 and id = ".$deal_list_item[$key]["province"]);
			if(strim($val['image_deal_logo']))
			{
				$val['image_deal_logo']=getQiniuPath($val['image_deal_logo'],'img');
				$val['image_deal_logo']=$val['image_deal_logo']."?imageView2/1/w/200/h/200";
			}
				//关注状态判断
				$user_focus_log['status'] =2;
				if($user_id){
					$user_focus_log = $GLOBALS ['db']->getOne ( "select id from " . DB_PREFIX . "deal_focus_log where user_id=$user_id  and deal_id=$project_id ");
					if ($user_focus_log) {
						$user_focus_log['status']= 1;
					}
				}
				
			array_push($project_info,$val);
			$project_info[$key]["deal_period"] = $deal_period;
			$project_info[$key]["deal_province"] = $deal_province;
			$project_info[$key]["user_focus_log"] = $user_focus_log['status'];
			$project_info[$key]["url"] = url("dealdetails#index",array('id'=>$project_id));
		}
			$res['data'] = $project_info;
			$res['info'] = count($deal_list_item);
			$res['count'] = count($deal_list_last);
		}else 
		{
			$res['status'] = 0;
			$res['info'] = "没有更多项目";
 
		}
 
		ajax_return($res);
 
	}
	
	
}

?>