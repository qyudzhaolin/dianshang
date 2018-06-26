
<?php
//项目详情审核认证前预览
//控制器2015年10月13日

function init_deal_page($deal_info)
{
	$GLOBALS['tmpl']->assign("page_title",$deal_info['name']);
 	if($deal_info['seo_title']!="")
	$GLOBALS['tmpl']->assign("seo_title",$deal_info['seo_title']);
	if($deal_info['seo_keyword']!="")
	$GLOBALS['tmpl']->assign("seo_keyword",$deal_info['seo_keyword']);
	if($deal_info['seo_description']!="")
	$GLOBALS['tmpl']->assign("seo_description",$deal_info['seo_description']);
 	$deal_info['tags_arr'] = preg_split("/[ ,]/",$deal_info['tags']);		
  }

class dealpreviewModule extends BaseModule
{	
      public function index()
	{		
		// if(!$GLOBALS['user_info'])
		// app_redirect(url("user#login"));
		// $id = intval($_REQUEST['id']);
		//$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$id." and is_effect = 0 and is_delete = 0 and user_id = ".intval($GLOBALS['user_info']['id']));
		 

		$id = intval($_REQUEST['id']);
		$user_id = $GLOBALS['user_info']['id'];
		 
		$GLOBALS['tmpl']->assign("project_id",$id);
		$GLOBALS['tmpl']->assign("user_id",$user_id);
		$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal 
						where id = ".$id."  
						and is_delete = 0");
 		 

  		if($deal_item)
		{
			//var_dump($deal_item);
			$GLOBALS['tmpl']->assign("page_title",$deal_item['name']);
  			
			//项目图片处理
		
			if(strim($deal_item['bp_url']))
			{
				//var_dump($deal_item['bp_url']);
				//$deal_item['bp_url']=getQiniuPath($deal_item['bp_url'],'bp');
				$deal_item['bp_url']=APP_ROOT."/bp_viewer/get_bp.php?key=".$deal_item['bp_url'];
				//var_dump($deal_item['bp_url']);
			}
		
			if(strim($deal_item['img_deal_logo']))
			{
				$deal_item['img_deal_logo']=getQiniuPath($deal_item['img_deal_logo'],'img');
				$deal_item['img_deal_logo']=$deal_item['img_deal_logo']."?imageView2/1/w/700/h/530";
			}
		 
			$deal_item['business_mode'] = nl2br($deal_item['business_mode']);
			$deal_item['company_name'] = nl2br($deal_item['company_name']);
			$deal_item['deal_brief'] = nl2br($deal_item['deal_brief']);
			 
 			$GLOBALS['tmpl']->assign("deal_item",$deal_item);
			 
			 
		
 			// $deal_milestone = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_sign_point where deal_id = ".$id.");  //获取项目亮点（运营编辑的亮点）
		
			//项目详情上方后台编辑的亮点
			$deal_milestone = $GLOBALS['db'] ->getAll("select point_info from ".DB_PREFIX."deal_sign_point where deal_id = ".$id." order by id asc");
			if($deal_milestone){
				$GLOBALS['tmpl']->assign("deal_milestone",$deal_milestone);
			}
	 
		
			//项目标签
			$deal_cate = $GLOBALS['db'] ->getAll("select cate.name from ".DB_PREFIX."deal project, ".DB_PREFIX."deal_cate cate, ".DB_PREFIX."deal_select_cate selected where selected.deal_id = project.id and selected.cate_id = cate.id and project.id = ".$id." ");
			if($deal_cate){
				$GLOBALS['tmpl']->assign("deal_cate",$deal_cate);
			}
 		
			//项目团队
			$deal_teammember = $GLOBALS['db']-> getAll("select * from ".DB_PREFIX."deal_team where deal_id = ".$id."  order by id asc");
			foreach($deal_teammember as $k=>$v){
				if(trim($v['img_logo'])){
				//获取缩略图完整url地址
				include_once  APP_ROOT_PATH."system/common.php";
				$img_logo=trim($v['img_logo']);
				$deal_teammember[$k]['img_logo']=getQiniuPath($img_logo,"img");
				$deal_teammember[$k]['img_logo']=$deal_teammember[$k]['img_logo']."?imageView2/1/w/80/h/80";
                }	
				$deal_teammember[$k]['brief'] = nl2br($deal_teammember[$k]['brief']);
			}
			$deal_teammember_count=count($deal_teammember)-1;
			$GLOBALS['tmpl']->assign("deal_teammember_count",$deal_teammember_count);
			$GLOBALS['tmpl']->assign("deal_teammember",$deal_teammember);
			
		
			//项目阶段
			$deal_period =  $GLOBALS['db'] ->getOne("select period.name as name  from ".DB_PREFIX."deal project,".DB_PREFIX."deal_period period where project.period_id = period.id and project.id = ".$id." ");
			if($deal_period){
				$GLOBALS['tmpl']->assign("deal_period",$deal_period);
			}
			//项目地点（省）
			$deal_province = $GLOBALS['db'] -> getOne("select name from ".DB_PREFIX."region_conf where pid = 1 and id = ".$deal_item['province']);
			if($deal_province){
				$GLOBALS['tmpl']->assign("deal_province",$deal_province);
			}
 
			//投资人人数
			$deal_history_investor_num = $GLOBALS['db'] ->getOne("select count(id) from ".DB_PREFIX."deal_history_investor where deal_id = ".$id." ");
			if($deal_history_investor_num){
				$GLOBALS['tmpl']->assign("deal_history_investor_num",$deal_history_investor_num);
			}
			
			//融资进度
			//$invest_jindu2 = $GLOBALS['db']-> getOne("select sum(price) price from ".DB_PREFIX."deal_trade_event where deal_id =".$id." ");
			$invest_jindu2 = $GLOBALS['db']-> getOne("select financing_amount  from ".DB_PREFIX."deal where id =".$id." ");
			$invest_jindu3 = $GLOBALS['db']-> getOne("select pe_amount_plan  from ".DB_PREFIX."deal where id =".$id." ");
	
			if($invest_jindu3 == 0) {
				$invest_jindu="100%";
		    }else{
		    	$invest_jindu=round(($invest_jindu2/$invest_jindu3)*100).'%';
		    }
			$GLOBALS['tmpl']->assign("invest_jindu",$invest_jindu);

			//融资后估值
			$invest_guzhi = $GLOBALS['db']-> getOne("select pe_amount_plan from ".DB_PREFIX."deal where id =".$id." ");
		    $invest_guzhi2 = $GLOBALS['db']-> getOne("select pe_sell_scale from ".DB_PREFIX."deal where id =".$id." ");
		    $pe_evaluate=$invest_guzhi/$invest_guzhi2*100;
			$len=strpos($pe_evaluate,".");
			if ($len) {
			$pe_evaluate=substr($pe_evaluate, 0, $len);
			}
			$GLOBALS['tmpl']->assign("pe_evaluate",$pe_evaluate);
		
			//项目融资事件
			$deal_trade_event = $GLOBALS['db']-> getAll("select * from ".DB_PREFIX."deal_trade_event where deal_id = ".$id." order by create_time desc limit 0,3" );
		
			if($deal_trade_event)
			{	
				
				$trade_event_time_array = array();
				foreach($deal_trade_event as $trade_event_key=>$value)
				{
					$create_time = date("Y.m.d",$value['create_time']);
					array_push($trade_event_time_array, $create_time);
				}
				$deal_trade_event=array_reverse($deal_trade_event);
				$trade_event_time_array=array_reverse($trade_event_time_array);
				
				$GLOBALS['tmpl']->assign("deal_trade_event",$deal_trade_event);
				$GLOBALS['tmpl']->assign("trade_event_time_array",$trade_event_time_array);
			}
			//项目里程碑
			$deal_event = $GLOBALS['db'] ->getAll("select * from ".DB_PREFIX."deal_event where deal_id = ".$id." order by create_time asc");
			if($deal_event)
			{
				$deal_event_time = $deal_event[0]['create_time'];
				$deal_event_time_array = array();
				foreach($deal_event as $event_key=>$value)
				{
					$create_time = date("Y.m.d",$value['create_time']);
					array_push($deal_event_time_array, $create_time);
				}
				$GLOBALS['tmpl']->assign("deal_event_time_array",$deal_event_time_array);
				$GLOBALS['tmpl']->assign("deal_event",$deal_event);
			}	
			$GLOBALS['tmpl']->display("deal_preview.html");
		}	
		else
		{
			//var_dump(111);
			app_redirect_preview();
		}		
		
	}
 
 
}
?>