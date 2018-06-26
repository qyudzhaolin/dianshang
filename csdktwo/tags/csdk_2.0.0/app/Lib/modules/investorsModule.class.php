<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class investorsModule extends BaseModule
{
	public function __construct(){
		parent::__construct();
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
		if($is_review != 1 || $GLOBALS['user_info']['user_type']==1)	{	
			app_redirect(url("index"));
			return;
		}

 		$GLOBALS['tmpl']->assign("page_title","投资人");
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
			
	 

		if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(!is_null($focus))  || (is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus))){
			$page_size=6;
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
				
$sql_select ="	select project.*";
$sql_where 	= "
				where  	project.id= user_ex.user_id
				and project.user_type= 1
				and   	project.is_review = 1
				and 	project.is_effect = 1
			  ";
$sql_order_page	="
				order by 	id desc
				limit 		$page_offset, $page_rows
				";
				
$sql_from 	= "		from ".DB_PREFIX."user project, ".DB_PREFIX."user_ex_investor user_ex";



/*********筛选项目列表****************/
if((!is_null($cate)||!is_null($district)||!is_null($period_id))&&(is_null($focus)))
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
		$subQuery_get_project_id_list = "select distinct user_id from cixi_user_select_cate where cate_id in ($cate_condition)";
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
		$subQuery_get_period_id_list = "select distinct user_id from cixi_user_select_period where period_id in ($period_id_condition)";
		$condition .= "and project.id in ($subQuery_get_period_id_list)";/******筛选列表条件（方向）*********/
		//$condition .= "and user_ex.period_choose in (".$period_id_condition.")";
	}
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_page;	
	//var_dump($sql_final);
}
/*******关注列表***********/
if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(!is_null($focus)))
{
	$sql_from = "from ".DB_PREFIX."user project, ".DB_PREFIX."user_focus_log focus";
	$sql_where 	= "
				where  	project.user_type=1
 				and   	project.is_review = 1
				and 	project.is_effect = 1
			  ";
 
 	$condition = "	and project.id = focus.focus_user_id and focus.user_id =".$uid;
 	$sql_order_page	="
				order by focus.create_time desc
				limit 		$page_offset, $page_rows
				";
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$condition." ".$sql_order_page;
	//var_dump($sql_final);
}
 
/*******默认列表************/

if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)))
{
	$sql_final = $sql_select." ".$sql_from." ".$sql_where." ".$sql_order_page;
	
	//$obj->r	= "没有更多项目";
}

//获取项目分类信息---筛选条件
		
		/*$deal_cate =$GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cate");
		if (!empty($cate_array)) {
			foreach ($deal_cate as $key => $value) {
				if(in_array($value['id'], $cate_array)){
					$deal_cate[$key]['is_check']=1;
				}else{
					$deal_cate[$key]['is_check']=0;
				}
			}
		}
		$GLOBALS['tmpl'] ->assign("deal_cate", $deal_cate);*/
		
//获取项目投资阶段信息---筛选条件
		
		/*$deal_period =$GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_period");
		if (!empty($deal_period)) {
			foreach ($deal_period as $key => $value) {
				if(in_array($value['id'], $period_id_array)){
					$deal_period[$key]['is_check']=1;
				}else{
					$deal_period[$key]['is_check']=0;
				}
			}
		}
		$GLOBALS['tmpl'] ->assign("deal_period", $deal_period);*/
		
		
//获取项目地区信息---筛选条件
	
		/*$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 ");  //二级地址
		$GLOBALS['tmpl'] ->assign("region_lv2", $region_lv2);*/
		//var_dump($region_lv2);
		
		
//获取项目列表数据
		//var_dump($sql_final);
		$deal_list_item = $GLOBALS['db']->getAll($sql_final);		

//拼接数据
		$project_info = array();

		foreach($deal_list_item as $key =>$val)
		{
			$project_id = $deal_list_item[$key]["id"];
			//投资成绩
			$invest_mark = $GLOBALS['db'] ->getAll("select mark_info from ".DB_PREFIX."investor_mark where user_id = ".$project_id." ");
			//投资风格
			$invest_style = $GLOBALS['db'] ->getAll("select style_info from ".DB_PREFIX."investor_style where user_id = ".$project_id." ");
			//投资亮点
			$invest_point = $GLOBALS['db'] ->getAll("select point_info from ".DB_PREFIX."investor_point where user_id = ".$project_id." ");
			$invest_cate =  $GLOBALS['db'] ->getAll("select cate.name as name  from ".DB_PREFIX."user project,".DB_PREFIX."deal_cate cate, ".DB_PREFIX."user_select_cate selected where selected.user_id = project.id and selected.cate_id = cate.id and project.id = ".$project_id." ");
			$invest_period =  $GLOBALS['db'] ->getAll("select period.name as name  from ".DB_PREFIX."user project,".DB_PREFIX."deal_period period, ".DB_PREFIX."user_select_period selected where selected.user_id = project.id and selected.period_id = period.id and project.id = ".$project_id." ");
			 

			$deal_province = $GLOBALS['db'] -> getOne("select name from ".DB_PREFIX."region_conf where pid = 1 and id = ".$deal_list_item[$key]["province"]);
			//投资人机构名称			
			$investor_org_name = $GLOBALS['db'] -> getOne("select org_name from ".DB_PREFIX."user_ex_investor where user_id = ".$project_id." ");			
			 
			if(strim($val['img_user_logo']))
			{
				$val['img_user_logo']=getQiniuPath($val['img_user_logo'],'img');
				$val['img_user_logo']=$val['img_user_logo']."?imageView2/1/w/309/h/254";
			}
			array_push($project_info,$val);
			$project_info[$key]["mark_info_array"] = $invest_mark;
			$project_info[$key]["style_info_array"] = $invest_style;
			$project_info[$key]["point_info_array"] = $invest_point;
		    $project_info[$key]["invest_period_array"] = $invest_period;
			$project_info[$key]["invest_cate_array"] = $invest_cate;
			$project_info[$key]["deal_province"] = $deal_province;
			$project_info[$key]["investor_org_name"] = $investor_org_name;
			
            
		}
		$GLOBALS['tmpl']->assign("project_info",$project_info);

		//总数量，总页数
		$totals_select=$GLOBALS['db']->getOne("select count(*) ".$sql_from." ".$sql_where." ".$condition);
		/*$totals_page_select=ceil($totals_select/$page_size);
		if($page_num<$totals_page_select){
			$page_num++;
			$GLOBALS['tmpl']->assign("page_num",$page_num);
		}
		$GLOBALS['tmpl']->assign("totals_select",$totals_select);
		$page_array=array();
		for($i=1;$i<=$totals_page_select;$i++){
			$page_array[]=$i;
		}
		$GLOBALS['tmpl']->assign("page_array",$page_array);*/
		//var_dump($project_info);
		$page = new Page($totals_select,$page_size);   //初始化分页对象 	
		$p  =  $page->csdk_show();
		$GLOBALS['tmpl']->assign('pages',$p);
//筛选列表

		if((!is_null($cate)||!is_null($district)||!is_null($period_id))&&(is_null($focus)))
		{
			$GLOBALS['tmpl']->assign("flag","deals_select_show");
			$GLOBALS['tmpl']->assign("period_id_array",$period_id_array);
			$GLOBALS['tmpl']->display("investors.html");
		}
//默认列表
		if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(is_null($focus)))
		{
			$GLOBALS['tmpl']->assign("efp","efp");
			$GLOBALS['tmpl']->display("investors.html");
		}
//关注列表	

		if((is_null($cate)&&is_null($district)&&is_null($period_id))&&(!is_null($focus)))
		{	
			$uid = $_REQUEST['uid'];
			$project_info["uid"] = $uid;
			$project_info["focus"] = 1;
			$GLOBALS['tmpl']->assign("project",$project_info);
			$GLOBALS['tmpl']->display("investors_focus.html");		
			//http://www.test_pc.com/index.php?ctl=deals&act=index&focus=1&uid=414&page=0
		}
 

	}
}
		
?>
