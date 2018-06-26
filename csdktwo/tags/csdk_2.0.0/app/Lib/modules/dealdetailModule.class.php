<?php
//项目详情项目预览
//控制器2015年9月2日14:22:28

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

class dealdetailModule extends BaseModule
{	
	public function __construct(){
		//预览参数判断
		$preview = strim($_REQUEST['preview']);
		if (!$preview) {
        parent::__construct();
		parent::is_login();
		}
 	}
 	 
   
    public function index()
	{		
		// if(!$GLOBALS['user_info'])
		// app_redirect(url("user#login"));
		// $id = intval($_REQUEST['id']);
		//$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$id." and is_effect = 0 and is_delete = 0 and user_id = ".intval($GLOBALS['user_info']['id']));
		//var_dump("11");
		//die;

		$id = intval($_REQUEST['id']);
		$user_id = $GLOBALS['user_info']['id'];
		//预览参数判断
		$preview = strim($_REQUEST['preview']);
		if ($preview) {
			$session_name='csdk'.md5('csdk');
			$adm_session = $_SESSION[$session_name];
			if(!isset($adm_session)){
				app_redirect(url("index"));
				return;
			}
			$preview_end='disabled="disabled"';
	 		$GLOBALS['tmpl']->assign("preview_end",$preview_end);
		}else{
			$is_review = $GLOBALS['user_info']['is_review'];
			if($is_review != 1 || $GLOBALS['user_info']['user_type']==0)	{	
				app_redirect(url("index"));
				return;
			}
		}
		$GLOBALS['tmpl']->assign("project_id",$id);
		$GLOBALS['tmpl']->assign("user_id",$user_id);
		$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal 
						where id = ".$id."  
						and is_delete = 0");
 		//增加浏览
		if (!$preview) {
		$focus_log = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal_visit_log  where user_id = ".$user_id." and deal_id=".$id."");
		if (empty($focus_log)&&$deal_item['user_id']!=$user_id){
			$client_ip=$_SERVER['REMOTE_ADDR'];
			$time=time();
			$insert_result = $GLOBALS['db']->query("insert into ".DB_PREFIX."deal_visit_log (user_id,deal_id,client_ip,create_time) values (".$user_id.",".$id.",'".$client_ip."',".$time.")");
		}
		if($insert_result)
		{
				$update_deal = $GLOBALS['db']->query("update ".DB_PREFIX."deal set view_count = view_count+1 where id=".$id." ");
		}

		}

  		if($deal_item)
		{
			//var_dump($deal_item);
			$GLOBALS['tmpl']->assign("page_title",$deal_item['name']);
			
			
			/*$region_pid = 0;
			$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
			foreach($region_lv2 as $k=>$v)
			{
				if($v['name'] == $deal_item['province'])
				{
					$region_lv2[$k]['selected'] = 1;
					$region_pid = $region_lv2[$k]['id'];
					break;
				}
			}
			$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
			
			
			if($region_pid>0)
			{
				$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$region_pid." order by py asc");  //三级地址
				foreach($region_lv3 as $k=>$v)
				{
					if($v['name'] == $deal_item['city'])
					{
						$region_lv3[$k]['selected'] = 1;
						break;
					}
				}
				$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
			}
			
			$deal_item['faq_list'] = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_faq where deal_id = ".$deal_item['id']." order by sort asc");
			$cate_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cate order by sort asc");
			$GLOBALS['tmpl']->assign("cate_list",$cate_list);*/
			
			
			//项目图片处理
		
			if(strim($deal_item['bp_url']))
			{
				//var_dump($deal_item['bp_url']);
				//$deal_item['bp_url']=getQiniuPath($deal_item['bp_url'],'bp');
				$deal_item['bp_url']=APP_ROOT."/bp_viewer/get_bp.php?key=".$deal_item['bp_url'];
				//var_dump($deal_item['bp_url']);
			}
		
			if(strim($deal_item['img_deal_cover']))
			{
				$deal_item['img_deal_cover']=getQiniuPath($deal_item['img_deal_cover'],'img');
				$deal_item['img_deal_cover']=$deal_item['img_deal_cover']."?imageView2/1/w/1920/h/700";
			}
			//成绩IMG
			if(strim($deal_item['img_achievement']))
			{
				$deal_item['img_achievement']=getQiniuPath($deal_item['img_achievement'],'img');
				$deal_item['img_achievement']=$deal_item['img_achievement']."?imageView2/1/w/650/h/500";
			}
			//远景IMG
			if(strim($deal_item['img_vision']))
			{
				$deal_item['img_vision']=getQiniuPath($deal_item['img_vision'],'img');
				$deal_item['img_vision']=$deal_item['img_vision']."?imageView2/1/w/650/h/500";
			}

			$deal_item['business_mode'] = nl2br($deal_item['business_mode']);
			$deal_item['company_name'] = nl2br($deal_item['company_name']);
			$deal_item['deal_brief'] = nl2br($deal_item['deal_brief']);
			$deal_item['entry_info'] = nl2br($deal_item['entry_info']);
			$deal_item['solve_pain_info'] = nl2br($deal_item['solve_pain_info']);
			$deal_item['recommend_reason'] = nl2br($deal_item['recommend_reason']);
			$deal_item['profession_info'] = nl2br($deal_item['profession_info']);
			$deal_item['operation_info'] = nl2br($deal_item['operation_info']);
			$deal_item['mark_data_info'] = nl2br($deal_item['mark_data_info']);
			$deal_item['achievement_info'] = nl2br($deal_item['achievement_info']);
			$deal_item['vision_info'] = nl2br($deal_item['vision_info']);
			$GLOBALS['tmpl']->assign("deal_item",$deal_item);
			//预览参数判断
			if (!$preview) {
			//关注功能
			$project_follow = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_focus_log where user_id = ".$user_id." and deal_id =".$id." ");
			$GLOBALS['tmpl']->assign("project_follow",$project_follow);
			//投资意向功能
			$project_invest = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal_intend_log where user_id = ".$user_id." and deal_id =".$id." ");
			$GLOBALS['tmpl']->assign("project_invest",$project_invest);
			}
			//收到投资意向次数
			$count_invest = $GLOBALS['db']->getOne("select count(id) from ".DB_PREFIX."deal_intend_log where  deal_id =".$id." ");
			$GLOBALS['tmpl']->assign("count_invest",$count_invest);
		
			
			// $deal_milestone = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_sign_point where deal_id = ".$id.");  //获取项目亮点（运营编辑的亮点）
		
			//项目详情上方后台编辑的亮点
			$deal_milestone = $GLOBALS['db'] ->getAll("select point_info from ".DB_PREFIX."deal_sign_point where deal_id = ".$id." order by id asc");
			if($deal_milestone){
				$GLOBALS['tmpl']->assign("deal_milestone",$deal_milestone);
			}
				
			//亮点数据
			$deal_brieftone = $GLOBALS['db'] ->getAll("select point_info from ".DB_PREFIX."deal_brief_point where deal_id = ".$id." order by id asc");
			if($deal_brieftone){
				$GLOBALS['tmpl']->assign("deal_brieftone",$deal_brieftone);
			}

			//行业调查数据
			$deal_profession_data = $GLOBALS['db'] ->getAll("select data_info from ".DB_PREFIX."deal_profession_data where deal_id = ".$id."  order by id asc");
			if($deal_profession_data){
				$GLOBALS['tmpl']->assign("deal_profession_data",$deal_profession_data);
			}

			//使用步骤
			$deal_operation_steps = $GLOBALS['db'] ->getAll("select img_deal_opera_steps,opera_steps_name,opera_steps_brief from ".DB_PREFIX."deal_operation_steps where deal_id = ".$id."  order by id asc");
			foreach($deal_operation_steps as $k=>$v){
			if(trim($v['img_deal_opera_steps'])){
			//获取缩略图完整url地址
			include_once  APP_ROOT_PATH."system/common.php";
			$img_logo=trim($v['img_deal_opera_steps']);
			$deal_operation_steps[$k]['real_url']=getQiniuPath($img_logo,"img");
			$deal_operation_steps[$k]['real_url']=$deal_operation_steps[$k]['real_url']."?imageView2/1/w/230/h/390";
			}	
			}
			if($deal_operation_steps){
 				$GLOBALS['tmpl']->assign("deal_operation_steps",$deal_operation_steps);
			}

			//数据图片
			$deal_data_img = $GLOBALS['db'] ->getAll("select img_data_url from ".DB_PREFIX."deal_data_img where deal_id = ".$id."  order by id asc");
			foreach($deal_data_img as $k=>$v){
			if(trim($v['img_data_url'])){
			//获取缩略图完整url地址
			include_once  APP_ROOT_PATH."system/common.php";
			$img_data_url=trim($v['img_data_url']);
			$deal_data_img[$k]['img_data_url']=getQiniuPath($img_data_url,"img");
			$deal_data_img[$k]['img_data_url']=$deal_data_img[$k]['img_data_url']."?imageView2/1/w/300/h/300";
			}	
			}
			if($deal_data_img){
				$GLOBALS['tmpl']->assign("deal_data_img",$deal_data_img);
			}

			//知名人士、机构推荐
			$deal_recommend = $GLOBALS['db'] ->getAll("select recommend_person,recommend_info from ".DB_PREFIX."deal_recommend where deal_id = ".$id."  order by id asc");
			if($deal_recommend){
				$GLOBALS['tmpl']->assign("deal_recommend",$deal_recommend);
			}

			//历史投资人
			$deal_history_investor = $GLOBALS['db'] ->getAll("select img_logo,name,info from ".DB_PREFIX."deal_history_investor where deal_id = ".$id."  order by id asc");
			foreach($deal_history_investor as $k=>$v){
				if(trim($v['img_logo'])){
				//获取缩略图完整url地址
				include_once  APP_ROOT_PATH."system/common.php";
				$img_logo=trim($v['img_logo']);
				$deal_history_investor[$k]['img_logo']=getQiniuPath($img_logo,"img");
				$deal_history_investor[$k]['img_logo']=$deal_history_investor[$k]['img_logo']."?imageView2/1/w/80/h/80";
				}	
			}
			$deal_history_investor_count=count($deal_history_investor)-1;
			$GLOBALS['tmpl']->assign("deal_history_investor_count",$deal_history_investor_count);
			$GLOBALS['tmpl']->assign("deal_history_investor",$deal_history_investor);

			//采访问题
			$deal_interviem = $GLOBALS['db'] ->getAll("select problem_info,answer_info from ".DB_PREFIX."deal_interviem where deal_id = ".$id."  order by id asc");
			if($deal_interviem){
				foreach ($deal_interviem as $k=> $v) {
					$deal_interviem[$k]['answer_info']=nl2br($v['answer_info']);
				}
				$deal_interviem_count=count($deal_interviem)-1;
				$GLOBALS['tmpl']->assign("deal_interviem_count",$deal_interviem_count);
				$GLOBALS['tmpl']->assign("deal_interviem",$deal_interviem);
			}
			$deal_interviem_time = date("Y年m月d日",$deal_item['interview_time']);
			if($deal_interviem_time){
				$GLOBALS['tmpl']->assign("deal_interviem_time",$deal_interviem_time);
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
			
			//融资目标
			// $invest_already_total = $GLOBALS['db']-> getOne("select sum(price) price from ".DB_PREFIX."deal_trade_event where deal_id =".$id." ");
			// if(is_null($invest_already_total)){
			// 	$invest_already_total=0;
			// }
			// $GLOBALS['tmpl']->assign("invest_already_total",$invest_already_total);

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
					$create_time = date("Y\年m\月",$value['create_time']);
					array_push($trade_event_time_array, $create_time);
				}
				$deal_trade_event=array_reverse($deal_trade_event);
				$trade_event_time_array=array_reverse($trade_event_time_array);
				
				$GLOBALS['tmpl']->assign("deal_trade_event",$deal_trade_event);
				$GLOBALS['tmpl']->assign("trade_event_time_array",$trade_event_time_array);
			}
			//项目里程碑
			$deal_event = $GLOBALS['db'] ->getAll("select * from ".DB_PREFIX."deal_event where deal_id = ".$id." order by create_time desc");
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
			$GLOBALS['tmpl']->display("deal.html");
		}	
		else
		{
			//var_dump(111);
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
			$in_updeta = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal_focus_log  where user_id = ".$uid." and deal_id=".$project_id."");
			if (empty($in_updeta)){
			$insert_result = $GLOBALS['db']->query("insert into ".DB_PREFIX."deal_focus_log (deal_id,user_id,create_time) values (".$project_id.",".$uid.",".$time.")");
			}
			if($insert_result)
			{
				$update_deal = $GLOBALS['db']->query("update ".DB_PREFIX."deal set focus_count = focus_count+1 where id=".$project_id." ");
			}
			else
			{
				showErr("关注失败",$ajax);
			}
		}else{
			showErr("关注失败",$ajax);
		}
	}
	
	public function add_invest()
	{
		$uid		= $GLOBALS['user_info']['id'];
		$project_id =  isset($_POST['project_id'])? trim($_POST['project_id']):NULL;

		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		$time=time();
		if(!is_null($project_id)&&!is_null($uid)){
            $in_updeta = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal_intend_log  where user_id = ".$uid." and deal_id=".$project_id."");
			if (empty($in_updeta)){
			$insert_result = $GLOBALS['db']->query("insert into ".DB_PREFIX."deal_intend_log (deal_id,user_id,create_time) values (".$project_id.",".$uid.",".$time.")");
			}
			if($insert_result)
			{
				$update_deal = $GLOBALS['db']->query("update ".DB_PREFIX."user set intend_count = intend_count+1 where id=".$uid." ");
			}
			else{
	            $res['status'] = 0;
				$res['info'] = "发送投资意向失败";
 			}
		}else{
			$res['status'] = 0;
			$res['info'] = "参数丢失";
		}
		ajax_return($res);
	}
 
}
?>