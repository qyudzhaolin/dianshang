<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class dealsModule extends BaseModule
{
	public function __construct(){
		parent::__construct();
		parent::is_login();
		if(!$GLOBALS['user_info']){
			app_redirect(url("index"));
		}
		require_once APP_ROOT_PATH."system/libs/user.php";
		$id = $GLOBALS['user_info']['id'];
	  	$is_exist_sub_user= is_exist(DB_PREFIX."sub_user","user_id={$id}");
		$GLOBALS['tmpl']->assign("is_exist_sub_user",$is_exist_sub_user);
	}
	public function index()
	{	
		$GLOBALS['tmpl']->assign("page_title","精品项目");
		$is_review = $GLOBALS['user_info']['is_review'];
		if($is_review != 1 || $GLOBALS['user_info']['user_type']==0)	{	
			app_redirect(url("index"));
			return;
		}
		/*$r = strim($_REQUEST['r']);   //推荐类型
		$GLOBALS['tmpl']->assign("p_r",$r);
		$id = intval($_REQUEST['id']);  //分类id
		$GLOBALS['tmpl']->assign("p_id",$id);
		$loc = strim($_REQUEST['loc']);  //地区
		$GLOBALS['tmpl']->assign("p_loc",$loc);
		$tag = strim($_REQUEST['tag']);  //标签
		$GLOBALS['tmpl']->assign("p_tag",$tag);
		$kw = strim($_REQUEST['k']);    //关键词
		$GLOBALS['tmpl']->assign("p_k",$kw);
		
		if(intval($_REQUEST['redirect'])==1)
		{
			$param = array();
			if($r!="")
			{
				$param = array_merge($param,array("r"=>$r));
			}
			if($id>0)
			{
				$param = array_merge($param,array("id"=>$id));
			}
			if($loc!="")
			{
				$param = array_merge($param,array("loc"=>$loc));
			}
			if($tag!="")
			{
				$param = array_merge($param,array("tag"=>$tag));
			}
			if($kw!="")
			{
				$param = array_merge($param,array("k"=>$kw));
			}
			app_redirect(url("deals",$param));
		}
		
		$image_list = load_dynamic_cache("INDEX_IMAGE_LIST");
		if($image_list===false)
		{
			$image_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."index_image order by sort asc");
			set_dynamic_cache("INDEX_IMAGE_LIST",$image_list);
		}
		$GLOBALS['tmpl']->assign("image_list",$image_list);
		
		
		$cate_result = load_dynamic_cache("INDEX_CATE_LIST");
		if($cate_result===false)
		{
			$cate_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cate order by sort asc");
			$cate_result= array();
			foreach($cate_list as $k=>$v)
			{
				$cate_result[$v['id']] = $v;
			}
			set_dynamic_cache("INDEX_CATE_LIST",$cate_result);
		}
		$GLOBALS['tmpl']->assign("cate_list",$cate_result);
		
		$page_size = DEAL_PAGE_SIZE;
		$step_size = DEAL_STEP_SIZE;
		
		$step = intval($_REQUEST['step']);
		if($step==0)$step = 1;
		$page = intval($_REQUEST['p']);
		if($page==0)$page = 1;		
		$limit = (($page-1)*$page_size+($step-1)*$step_size).",".$step_size	;
		
		$GLOBALS['tmpl']->assign("current_page",$page);
		
		$condition = " is_delete = 0 and is_effect = 1 "; 
		if($r!="")
		{
			if($r=="new")
			{
				$condition.=" and ".NOW_TIME." - begin_time < ".(24*3600)." and ".NOW_TIME." - begin_time > 0 ";  //上线不超过一天
				$GLOBALS['tmpl']->assign("page_title","最新上线");
			}
			if($r=="nend")
			{
				$condition.=" and end_time - ".NOW_TIME." < ".(24*3600)." and end_time - ".NOW_TIME." > 0 ";  //当天就要结束
				$GLOBALS['tmpl']->assign("page_title","即将结束");
			}
			if($r=="classic")
			{
				$condition.=" and is_classic = 1 ";
				$GLOBALS['tmpl']->assign("page_title","经典项目");
			}
		}
		if($id>0)
		{
			$condition.= " and cate_id = ".$id;
			$GLOBALS['tmpl']->assign("page_title",$cate_result[$id]['name']);			
		}
		
		if($loc!="")
		{
			$condition.=" and (province = '".$loc."' or city = '".$loc."') ";
			$GLOBALS['tmpl']->assign("page_title",$loc);
		}
		
		if($tag!="")
		{
			$unicode_tag = str_to_unicode_string($tag);
			$condition.=" and match(tags_match) against('".$unicode_tag."'  IN BOOLEAN MODE) ";
			$GLOBALS['tmpl']->assign("page_title",$tag);
		}
		
		if($kw!="")
		{		
			$kws_div = div_str($kw);
			foreach($kws_div as $k=>$item)
			{
				
				$kws[$k] = str_to_unicode_string($item);
			}
			$ukeyword = implode(" ",$kws);
			$condition.=" and (match(name_match) against('".$ukeyword."'  IN BOOLEAN MODE) or match(tags_match) against('".$ukeyword."'  IN BOOLEAN MODE)  or name like '%".$kw."%') ";

			$GLOBALS['tmpl']->assign("page_title",$kw);
		}
		
		$deal_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal where ".$condition." order by sort asc limit ".$limit);
		$deal_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where ".$condition);
		foreach($deal_list as $k=>$v)
		{
			$deal_list[$k]['remain_days'] = floor(($v['end_time'] - NOW_TIME)/(24*3600));
			$deal_list[$k]['percent'] = round($v['support_amount']/$v['pe_amount_plan']*100);
		}
		$GLOBALS['tmpl']->assign("deal_list",$deal_list);
		
		$page = new Page($deal_count,$page_size);   //初始化分页对象 	
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);		
		
		$GLOBALS['tmpl']->display("deals.html");
		*/
		
		
		
		
		
				
		
//下一页功能
		define("DEAL_PAGE_CUS_SIZE", 6);


	/*********获取筛选数据****************/
	/*Filter parameters used in Filter list, there are three dimension filter which are cate, district, period*/
		$cate 		= isset($_REQUEST['cates'])? 		trim($_REQUEST['cates'])		:NULL;
		$district 	= isset($_REQUEST['district'])? 	trim($_REQUEST['district'])	:NULL;
		$period_id 	= isset($_REQUEST['periods'])? 	trim($_REQUEST['periods'])	:NULL;
		
	// $cate = substr($cate,0,-1);
		
	/*Parameters for focus List, if it is equal to 1, which means the user is going to focus List page*/
		$focus 	= isset($_REQUEST['focus'])? trim($_REQUEST['focus']):NULL;

		$uid	 	= $GLOBALS['user_info']['id'];
			
	/*Parameters for investation intend List, if it is equal to 1, which means the user is going to investation intend List page*/
		$intend 	= isset($_REQUEST['intend'])? trim($_REQUEST['intend']):NULL;

		if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(!is_null($focus)) && (is_null($intend)) || (is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)) && (!is_null($intend))){
			$page_size=4;
		}else{
			$page_size = DEAL_PAGE_CUS_SIZE;
		}
//页数
		$page_num = intval($_REQUEST['p']);
		
		
		if($page_num==0)
			$page_num = 1;	
		$page_offset= $page_size*$page_num - $page_size;
		$page_rows 	= $page_size;

		$limit = $page_offset.", ".$page_rows;
		//$page_num++;
		//$GLOBALS['tmpl']->assign("page_num",$page_num);

	
/*****Initializing Variables Which Used In Following Sql Statement *****/
	$sql_select	= "";
	$sql_from	= "";
	$sql_where 	= "";
	$condition 	= "";
	$sql_final 	= "";
	$para_final = array();
/*************************/
// $sql_select = "
				// select 	 distinct initiator.id 		initiator_id
						// ,initiator.is_review		initiator_isRview
						// ,initiator.province		initiator_province
						// ,project.cate_choose		project_cate			/******分类标签（返回分类标签）*********/
						// ,project.id 				project_id
						// ,project.img_deal_logo 	image_project_url
						// ,project.name			name
						// ,project.deal_sign 			summary	
						// ,project.view_count		view_number
						// ,project.focus_count	focus_number
						// ,project.period_id	period_id
						
				// ";
/* pc is_effcct=1 去掉，暂时没有这个逻辑*/			
$sql_select ="	select project.* ";
$sql_where 	= "
				where 	project.user_id 		= initiator.id 					/*** ALWAYS THERE *********************/
				and   	initiator.is_review = 1  
				and  	project.is_delete = 0
				and  	project.is_effect = 2
			  ";
$sql_order_page	="
				order by 	project.sort desc, project.id desc
				limit 		$page_offset, $page_rows
				";
				
$sql_from 	= "		from ".DB_PREFIX."deal project, ".DB_PREFIX."user initiator";


/*********筛选项目列表****************/
if((!is_null($cate)||!is_null($district)||!is_null($period_id))&&(is_null($focus) && is_null($intend)))
{
	if(!is_null($cate))
	{
		/*********Deal with Categories ***********************/
		$cate_array 	= explode('_',$cate);
		$cates_condition 		= '';
		foreach($cate_array as $item_in_array)
		{		
			$cates_condition 	= $cates_condition.$item_in_array.',';	
		}
		$cate_condition=substr($cates_condition,0,-1);
		$subQuery_get_project_id_list = "select deal_id from cixi_deal_select_cate where cate_id in ($cate_condition)";
		$condition .= "and project.id in ($subQuery_get_project_id_list)";/******筛选列表条件（方向）*********/
	}
	if(!is_null($district))
	{
		/*********Deal with district***********************/
		$district_array = explode('_',$district);
		/******筛选列表条件（地区分类,省市）*********/
		if (empty($district_array[1])) {
			$condition .= "and project.province = ".$district_array[0]." ";
		}else{
			$condition .= "and project.province = ".$district_array[0]." and project.city = $district_array[1] ";
			$province_city=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."region_conf where pid=".$district_array[0]." and id=".$district_array[1]);
			$GLOBALS['tmpl']->assign("province_city",$province_city);

		}
		

		$province=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."region_conf where pid=1 and id=".$district_array[0]);
		$city=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid=".$district_array[0]);
		$GLOBALS['tmpl']->assign("province",$province);
		$GLOBALS['tmpl']->assign("region_lv3",$city);
		$GLOBALS['tmpl']->assign("district_array",$district_array);
	}
	if(!is_null($period_id))
	{
		$period_id_array 	= explode('_',$period_id);
		$period_id_condition 		= '';
		foreach($period_id_array as $item_in_array)
		{		
			$period_id_condition 	= $period_id_condition.$item_in_array.',';	
		}
		$period_id_condition=substr($period_id_condition,0,-1);

		/******筛选列表条件（阶段分类）*********/
		$condition .= "and project.period_id in (".$period_id_condition.")";
	}
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_page;	
	
}
/*******关注列表***********/
if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(!is_null($focus)) && (is_null($intend)))
{
	// $sql_from = "
				// from 
						// cixi_deal 				project
						// ,cixi_user 				initiator
						// ,cixi_deal_focus_log 	focus
				// ";
				
	$sql_from = "from ".DB_PREFIX."deal project, ".DB_PREFIX."user initiator, ".DB_PREFIX."deal_focus_log focus";
	// $condition = "
				// and 	focus.deal_id	= project.id	
				// and   	focus.user_id 	= ?	";		/******关注列表条件****/
	
	$condition = "	and focus.deal_id	= 	project.id and focus.user_id =".$uid;
	//var_dump($condition);
	// $para_final[] = $uid;
	$sql_order_page	="
				order by 	focus.create_time desc
				limit 		$page_offset, $page_rows
				";
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_page;
}
/*******投资意向列表***********/
if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)) && (!is_null($intend)))
{
	$sql_select .= ", intend.create_time   intend_time";
	// $sql_from 	= " 
					// from 
						// cixi_deal 				project
						// ,cixi_user 				initiator
						// ,cixi_deal_intend_log 	intend
						
					// ";
					
	$sql_from = " from ".DB_PREFIX."deal project, ".DB_PREFIX."user initiator, ".DB_PREFIX."deal_intend_log intend";
	// $condition = "
					// and intend.deal_id = project.id
					// and intend.user_id = ?
					// ";
	$condition = "and intend.deal_id = project.id and intend.user_id =".$uid;				
	$sql_order_page	="
				order by 	intend.create_time desc
				limit 		$page_offset, $page_rows
				";
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_page;
	//$obj->r	= "您目前没有对任何项目有投资意向";
}
/*******默认列表************/

if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)) && (is_null($intend)))
{
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$sql_order_page;
	//$obj->r	= "没有更多项目";
}

//获取项目分类信息---筛选条件
		
		$deal_cate =$GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cate");
		if (!empty($cate_array)) {
			foreach ($deal_cate as $key => $value) {
				if(in_array($value['id'], $cate_array)){
					$deal_cate[$key]['is_check']=1;
				}else{
					$deal_cate[$key]['is_check']=0;
				}
			}
		}
		$GLOBALS['tmpl'] ->assign("deal_cate", $deal_cate);
		
//获取项目投资阶段信息---筛选条件
		
		$deal_period =$GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_period");
		if (!empty($deal_period)) {
			foreach ($deal_period as $key => $value) {
				if(in_array($value['id'], $period_id_array)){
					$deal_period[$key]['is_check']=1;
				}else{
					$deal_period[$key]['is_check']=0;
				}
			}
		}
		$GLOBALS['tmpl'] ->assign("deal_period", $deal_period);
		
		
//获取项目地区信息---筛选条件
	
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 ");  //二级地址
		$GLOBALS['tmpl'] ->assign("region_lv2", $region_lv2);
		//var_dump($region_lv2);
		
		
//获取项目列表数据
		//var_dump($sql_final);
		$deal_list_item = $GLOBALS['db']->getAll($sql_final);		

//拼接数据
//
//
		$project_info = array();

		foreach($deal_list_item as $key =>$val)
		{
			$project_id = $deal_list_item[$key]["id"];
			$deal_sign_point = $GLOBALS['db'] ->getAll("select point_info from ".DB_PREFIX."deal_sign_point where deal_id = ".$project_id." order by id asc");
			$deal_brief_point= $GLOBALS['db'] ->getAll("select point_info from ".DB_PREFIX."deal_brief_point where deal_id = ".$project_id." order by id asc");
			$deal_cate = $GLOBALS['db'] ->getAll("select cate.name from ".DB_PREFIX."deal project, ".DB_PREFIX."deal_cate cate, ".DB_PREFIX."deal_select_cate selected where selected.deal_id = project.id and selected.cate_id = cate.id and project.id = ".$project_id." ");
			$deal_period =  $GLOBALS['db'] ->getOne("select period.name as name  from ".DB_PREFIX."deal project,".DB_PREFIX."deal_period period where project.period_id = period.id and project.id = ".$project_id." ");
			$deal_province = $GLOBALS['db'] -> getOne("select name from ".DB_PREFIX."region_conf where pid = 1 and id = ".$deal_list_item[$key]["province"]);
			$deal_time = $GLOBALS['db'] ->getOne("select create_time from ".DB_PREFIX."deal_intend_log where deal_id = ".$project_id." and user_id = ".$uid." ");
			//var_dump("select create_time from ".DB_PREFIX."deal_intend_log where deal_id = ".$project_id." and user_id = ".$uid." ");
			if(strim($val['img_deal_logo']))
			{
				$val['img_deal_logo']=getQiniuPath($val['img_deal_logo'],'img');
				$val['img_deal_logo']=$val['img_deal_logo']."?imageView2/0/w/309/h/254";
			}
			/*if(strim($val['bp_url']))
			{
				//$val['bp_url']=getQiniuPath($val['bp_url'],'bp');
				$val['bp_url']=getQiniuPath($val['bp_url'],'bp');
				$val['bp_url']=APP_ROOT."bp_viewer/bp_view_web.php?key=".$val['bp_url'];
			}*/
			array_push($project_info,$val);
			$project_info[$key]["point_info_array"] = $deal_sign_point;
			$project_info[$key]["brief_info_array"] = $deal_brief_point;
			$project_info[$key]["deal_cate_array"] = $deal_cate;
			$project_info[$key]["deal_period"] = $deal_period;
			$project_info[$key]["deal_province"] = $deal_province;
            $project_info[$key]['time']=date("Y.m.d",$deal_time);
		}
		$GLOBALS['tmpl']->assign("project_info",$project_info);

		//总数量，总页数
		$totals_select=$GLOBALS['db']->getOne("select count(*) ".$sql_from." ".$sql_where." ".$condition);
		$GLOBALS['tmpl']->assign("totals_select",$totals_select);
		$totals_page_select=ceil($totals_select/$page_size);
		/*if($page_num<$totals_page_select){
			$page_num++;
			$GLOBALS['tmpl']->assign("page_num",$page_num);
		}
		
		$page_array=array();
		for($i=1;$i<=$totals_page_select;$i++){
			$page_array[]=$i;
		}
		$GLOBALS['tmpl']->assign("page_array",$page_array);*/

		$page = new Page($totals_select,$page_size);   //初始化分页对象 	
		$p  =  $page->csdk_show();
		$GLOBALS['tmpl']->assign('pages',$p);
//筛选列表

		if((!is_null($cate)||!is_null($district)||!is_null($period_id))&&(is_null($focus) && is_null($intend)))
		{
			$GLOBALS['tmpl']->assign("flag","deals_select_show");
			$GLOBALS['tmpl']->assign("period_id_array",$period_id_array);
			$GLOBALS['tmpl']->display("deals.html");
		}
//默认列表
		if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)) && (is_null($intend)))
		{
			$GLOBALS['tmpl']->assign("efp","efp");
			$GLOBALS['tmpl']->display("deals.html");
		}
//关注列表	

		if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(!is_null($focus)) && (is_null($intend)))
		{	
			$uid = $_REQUEST['uid'];
			$project_info["uid"] = $uid;
			$project_info["focus"] = 1;
			$GLOBALS['tmpl']->assign("project",$project_info);
			$GLOBALS['tmpl']->display("deals_focus.html");		
			//http://www.test_pc.com/index.php?ctl=deals&act=index&focus=1&uid=414&page=0
		}
//投资意向
		if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)) && (!is_null($intend)))
		{
			//var_dump($project_info);
			


			//var_dump($project_info[$key]['time']);
			$uid = $_REQUEST['uid'];
			$project_info["uid"] = $uid;
			$project_info["intend"] = 1;
			$GLOBALS['tmpl']->assign("project",$project_info);
			$GLOBALS['tmpl']->display("deals_intend.html");		
		}


	}
}
		
?>