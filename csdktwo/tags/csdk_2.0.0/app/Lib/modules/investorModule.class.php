<?php
// +----------------------------------------------------------------------
// | CSDK PC CODE
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.cisdag.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhuwen(569741996@qq.com)
// +----------------------------------------------------------------------


class investorModule extends BaseModule
{
	public function __construct(){
		parent::__construct();
		parent::is_login();
	}
 
	
	public function index()
	{		
		$is_review = $GLOBALS['user_info']['is_review'];
		if($is_review != 1 || $GLOBALS['user_info']['user_type']==1)	{	
			app_redirect(url("index"));
			return;
		}
		$id = intval($_REQUEST['id']);
		$user_id = $GLOBALS['user_info']['id'];
		$GLOBALS['tmpl']->assign("project_id",$id);
		$GLOBALS['tmpl']->assign("user_id",$user_id);
		$invest_detail = $GLOBALS['db']->getRow("select user_name,per_sign,province,per_brief,img_user_logo from ".DB_PREFIX."user where id = ".$id." and is_effect = 1 and is_review = 1");	
		$GLOBALS['tmpl']->assign("page_title",$invest_detail['user_name']);
		// die;
 		if($invest_detail)
		{
			if(strim($invest_detail['img_user_logo']))
 			{
				$invest_detail['img_user_logo']=getQiniuPath($invest_detail['img_user_logo'],'img');
				$invest_detail['img_user_logo']=$invest_detail['img_user_logo']."?imageView2/1/w/162/h/162";

			}
			//机构信息
			$invest_detail_ex = $GLOBALS['db']->getRow("select img_user_cover,org_desc,org_url,org_name from ".DB_PREFIX."user_ex_investor where user_id = ".$id."");	
			if(strim($invest_detail_ex['img_user_cover']))
			{
				/*$invest_detail_ex['img_org_logo']=getQiniuPath($invest_detail_ex['img_org_logo'],'img');
				$invest_detail_ex['img_org_logo']=$invest_detail_ex['img_org_logo']."?imageView2/1/w/48/h/48";*/
				// $invest_detail_ex['img_user_cover']=getQiniuPath($invest_detail_ex['img_user_cover'],'img');
				// $invest_detail_ex['img_user_cover']=$invest_detail_ex['img_user_cover']."?imageView2/1/w/1920/h/700";

			}
			$invest_detail_ex['org_desc'] = nl2br($invest_detail_ex['org_desc']);
			//阶段
		    $deal_period = $GLOBALS['db']->getAll("select name from ".DB_PREFIX."deal_period deal_period , ".DB_PREFIX."user_select_period user_select_period where deal_period.id=user_select_period.period_id and user_select_period.user_id= ".$id." ");
		    //方向
		    $deal_cate = $GLOBALS['db']->getAll("select name from ".DB_PREFIX."deal_cate deal_cate , ".DB_PREFIX."user_select_cate user_select_cate where deal_cate.id=user_select_cate.cate_id and user_select_cate.user_id= ".$id." ");
			
			//投资亮点
			$investor_point = $GLOBALS['db']->getALL("select point_info from ".DB_PREFIX."investor_point where user_id = ".$id."");
			//投资成绩
			$invest_detail_mark = $GLOBALS['db']->getALL("select mark_info from ".DB_PREFIX."investor_mark where user_id = ".$id."");
			//var_dump($invest_detail_mark);
			//投资风格
			$invest_detail_style = $GLOBALS['db']->getALL("select style_info from ".DB_PREFIX."investor_style where user_id = ".$id."");
			//关注功能	
		    $project_intend = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_intend_log where  user_id=".$id);
		     
	     	$GLOBALS['tmpl']->assign("project_intend",$project_intend);
	     	
			//地区
		    $region_lv2 = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where region_level = 2 and id= ".$invest_detail['province']);  //二级地址
		    $GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
			//$GLOBALS['tmpl']->assign("page_title",$invest_detail['name']);
			$GLOBALS['tmpl']->assign("invest_detail",$invest_detail);
			$GLOBALS['tmpl']->assign("invest_detail_ex",$invest_detail_ex);
			$GLOBALS['tmpl']->assign("investor_point",$investor_point);
			$GLOBALS['tmpl']->assign("invest_detail_mark",$invest_detail_mark);
			$GLOBALS['tmpl']->assign("invest_detail_style",$invest_detail_style);
			$GLOBALS['tmpl']->assign("deal_period",$deal_period);
			$GLOBALS['tmpl']->assign("deal_cate",$deal_cate);
			$GLOBALS['tmpl']->display("investor.html");


		}	
		else
		{$GLOBALS['tmpl']->display("investor.html");
			app_redirect_preview();
 		}		
		
	}

	public function add_focus()
	{
		//$uid		= isset($_POST['user_id'])? trim($_POST['user_id']):NULL;
		$uid		= $GLOBALS['user_info']['id'];
		$project_id =  isset($_POST['project_id'])? trim($_POST['project_id']):NULL;

		$time=time();
		if(!is_null($project_id)&&!is_null($uid)){
			$in_updeta = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."user_focus_log  where focus_user_id=".$project_id." and user_id = ".$uid);
			if (empty($in_updeta)){
			$insert_result = $GLOBALS['db']->query("insert into ".DB_PREFIX."user_focus_log (user_id,focus_user_id,create_time) values (".$uid.",".$project_id.",".$time.")");
			}
			if($insert_result)
			{
				$update_deal = $GLOBALS['db']->query("update ".DB_PREFIX."user set focus_count = focus_count+1 where id=".$project_id." ");
			}
			else
			{
				showErr("关注失败",$ajax);
			}
		}else{
			showErr("关注失败",$ajax);
		}
	}
 
 
	
}
?>