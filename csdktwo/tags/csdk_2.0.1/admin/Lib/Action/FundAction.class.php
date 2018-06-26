<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class FundAction extends CommonAction{

	public function index()
	{	
		if(trim($_REQUEST['name'])!=''){
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		if(trim($_REQUEST['short_name'])!=''){
			$map['short_name'] = array('like','%'.trim($_REQUEST['short_name']).'%');
		}
		if(trim($_REQUEST['manager'])!=''){
			$map['manager'] = array('like','%'.trim($_REQUEST['manager']).'%');
		}
		if(trim($_REQUEST['status']) != '' && trim($_REQUEST['status']) != '0'){
			$map['status'] = $_REQUEST['status'];
		}
	
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map['is_delete'] = 1; // 只查有效
		$name=$this->getActionName();
		$model = D ($name);
		
		if (! empty ( $model )) {
			$list=$this->_list ( $model, $map );
			foreach($list as $k=>$v){
				if($v['status']==1){
					$list[$k]['status_value']='未发布';
					$list[$k]['button_list']='<a href="javascript:edit('.$list[$k]['id'].')">编辑</a>&nbsp;<a href="javascript: del('.$list[$k]['id'].')">删除</a>&nbsp;<a href="javascript: fund_investor('.$list[$k]['id'].')">管理基金投资人</a>&nbsp;<a href="javascript: fund_relation('.$list[$k]['id'].')">管理基金投资项目</a>&nbsp;<a href="javascript: fund_detail('.$list[$k]['id'].')">基金详情</a>';
				}elseif($v['status']==2){
					$list[$k]['status_value']='已发布';
					$list[$k]['button_list']='<a href="javascript: fund_investor('.$list[$k]['id'].')">管理基金投资人</a>&nbsp;<a href="javascript: fund_relation('.$list[$k]['id'].')">管理基金投资项目</a>&nbsp;<a href="javascript: fund_detail('.$list[$k]['id'].')">基金详情</a>&nbsp;<a href="javascript: revoke('.$list[$k]['id'].')">撤回</a>';
				}elseif($v['status']==3){
					$list[$k]['status_value']='已撤回';
					$list[$k]['button_list'] = '<a href="javascript:edit('.$list[$k]['id'].')">编辑</a>&nbsp;<a href="javascript: del('.$list[$k]['id'].')">删除</a>&nbsp;<a href="javascript: fund_investor('.$list[$k]['id'].')">管理基金投资人</a>&nbsp;<a href="javascript: fund_relation('.$list[$k]['id'].')">管理基金投资项目</a>&nbsp;<a href="javascript: fund_detail('.$list[$k]['id'].')">基金详情</a>';
				}
				
				if($v['status']==1 || $v['status']==3){
					// 投资人
					$investor_list = M(user_fund_relation)->where('fund_id='.$list[$k]['id'])->findAll();
					// 投资项目
					$relation_list = M(fund_deal_relation)->where('fund_id='.$list[$k]['id'])->findAll();
					if(count($investor_list)> 0 && count($relation_list)> 0){
						$list[$k]['button_list'] .='&nbsp;<a href="javascript: publish('.$list[$k]['id'].')">发布</a>';
					}
				}
				
				$list[$k]['establish_date'] = date('Y-m-d',$v['establish_date']);
			}
			$this->assign("list",$list);
		}
		// 初始化状态列表
		$status_list = array(
			array("id"=>1,"name"=>"未发布"),
			array("id"=>2,"name"=>"已发布"),
			array("id"=>3,"name"=>"撤回")
		);		
		
		$this->assign("status_list",$status_list);
		
		$this->display ();
	}

	public function get_list($map,$action_name)
	{
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$list=$this->_list ( $model, $map );
			foreach($list as $k=>$v){
				if($v['is_effect']=='0'){
					$list[$k]['is_effect_zh']='未认证';
				}elseif($v['is_effect']=='1'){
					$list[$k]['is_effect_zh']='已认证';
				}elseif($v['is_effect']=='2'){
					$list[$k]['is_effect_zh']='上线';
				}
				elseif($v['is_effect']=='3'){
					$list[$k]['is_effect_zh']='待上线';
				}
			}
			$this->assign("list",$list);
		}
		
		$period_list = M("Deal_period")->findAll();
		$this->assign("period_list",$period_list);
		$this->assign("action_name",$action_name);
		$this->display ('submit_index');
	}

	public function un_auth_index(){
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['period_id'])>0)
		 {
			 $map['period_id'] = intval($_REQUEST['period_id']);
		 }
		
		if(intval($_REQUEST['user_id'])>0)
		{
			$map['user_id'] = intval($_REQUEST['user_id']);
		}
		
		$map['is_effect'] = 0;
		
		$map['f_delete'] = 0;
		$this->get_list($map,"un_auth_index");
		
	}
	public function auth_index(){
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['period_id'])>0)
		 {
			 $map['period_id'] = intval($_REQUEST['period_id']);
		 }
		
		if(intval($_REQUEST['user_id'])>0)
		{
			$map['user_id'] = intval($_REQUEST['user_id']);
		}
		
		$map['is_effect'] = 1;
		
		$map['f_delete'] = 0;
		$this->get_list($map,"auth_index");
	}
	public function online_index(){
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['period_id'])>0)
		 {
			 $map['period_id'] = intval($_REQUEST['period_id']);
		 }
		
		if(intval($_REQUEST['user_id'])>0)
		{
			$map['user_id'] = intval($_REQUEST['user_id']);
		}
		
		$map['is_effect'] = 2;
		
		$map['f_delete'] = 0;
		$this->get_list($map,"online_index");
	}
	public function ready_index(){
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['period_id'])>0)
		 {
			 $map['period_id'] = intval($_REQUEST['period_id']);
		 }
		
		if(intval($_REQUEST['user_id'])>0)
		{
			$map['user_id'] = intval($_REQUEST['user_id']);
		}
		
		$map['is_effect'] = 3;
		
		$map['f_delete'] = 0;
		$this->get_list($map,"ready_index");
	}
	public function intend_list()
	{
		$result = $GLOBALS['db']->getAll("select intend.id as id,intend.create_time,user.id as user_id,user.user_name,user.mobile,deal.id as deal_id,deal.name as deal_name from ".DB_PREFIX."deal_intend_log as intend,".DB_PREFIX."user as user,".DB_PREFIX."deal as deal where  intend.user_id=user.id and intend.deal_id=deal.id and intend.create_time is not null and intend.create_time<>0 and user.is_review=1 and user.is_effect=1 and deal.is_effect=2 order by intend.create_time desc"); 
		//var_dump($result);
		$this->assign("list",$result);
		$this->display ('intend_list');
	}
	public function delete_index()
	{

		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			$map['cate_id'] = intval($_REQUEST['cate_id']);
		}
		
		if(intval($_REQUEST['user_id'])>0)
		{
			$map['user_id'] = intval($_REQUEST['user_id']);
		}
		

		$map['f_delete'] = 1;		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$cate_list = M("DealCate")->findAll();
		$this->assign("cate_list",$cate_list);
		$this->display ();
	}

	public function add()
	{ 
		$this->display();
	}
	public function insert() {

	  //var_dump($_REQUEST['team_name']);die();
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();
	
		/***********ADD   get initiator user id which will be insert into cixi_deal table******************************/
 		 
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		
		/*if(!check_empty($data['code']))
		{
			$this->error("请输入基金编码"); 
		}*/
		if(!check_empty($data['name']))
		{
			$this->error("请输入基金名称"); 
		}
		if(utf8_strlen($data['name'])>30)
		{
			$this->error("请确保基金名称在30个字以内"); 
		}
		if(!check_empty($data['short_name']))
		{
			$this->error("请输入基金简称"); 
		}
		if(utf8_strlen($data['short_name'])>6)
		{
			$this->error("请确保基金简称在6个字以内"); 
		}
		if(!check_empty($data['manager']))
		{
			$this->error("请输入基金管理人");
		}
 	    if(!check_empty($data['total_amount']))
		{
			$this->error("请输入基金规模"); 
		}
 		if(!check_empty($data['establish_date']))
		{
			$this->error("请选择成立日期"); 
		}
 		if(!check_empty($data['deadline']))
		{
			$this->error("请输入基金期限"); 
		}
		if(!check_empty($data['summary']))
		{
			$this->error("请输入基金简介"); 
		}
		if(!check_empty($data['max_payback']))
		{
			$this->error("请输入已投资项目单个最高回报"); 
		}
		if(!check_empty($data['average_payback']))
		{
			$this->error("请输入已投资项目总体平均回报"); 
		}
		if(!check_empty($data['total_payback']))
		{
			$this->error("请输入基金整体回报"); 
		}
		
		$data['establish_date'] = trim($data['establish_date'])==''?0:to_timespan($data['establish_date']);
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$data['operator'] = $adm_name;
 		$data['update_time'] = time();
		$data['is_delete'] = 1;
 		// 添加基金	
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			// 存储team信息
			foreach($_REQUEST['team_name'] as $k =>$v){
				if(trim($v) != "" && trim($_REQUEST['title'][$k]) != '' && trim($_REQUEST['brief'][$k]) != ''){
					$manage_team = array();
					$manage_team['fund_id'] = $list;
					$manage_team['name'] = trim($v); 
					$manage_team['position'] = trim($_REQUEST['title'][$k]);
					$manage_team['summary'] = trim($_REQUEST['brief'][$k]);
					$manage_team['head_logo'] = trim($_REQUEST['image_key'][$k]);
					 M("fund_manage_team")->add($manage_team);
				}
			}
			//成功提示
 			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
 			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$vo['establish_date'] = $vo['establish_date']!=0?date("Y-m-d",$vo['establish_date']):'';
		$vo['max_payback'] = floatval($vo['max_payback']);
		$vo['average_payback'] = floatval($vo['average_payback']);
		$vo['total_payback'] = floatval($vo['total_payback']);

		$qa_list = M("fund_manage_team")->where("fund_id=".$id)->findAll();
		// echo var_dump($qa_list);
		foreach($qa_list as $k=>$v){
			if(trim($v['head_logo']) != ''){
				//获取缩略图完整url地址
				include_once  APP_ROOT_PATH."system/common.php";
				$real_url=trim($v['head_logo']);
				$qa_list[$k]['real_url']=getQiniuPath($real_url,"img");
				// echo var_dump($qa_list[$k]['real_url'],$v['head_logo']);
			}
		}
		$this->assign ( 'vo', $vo );
		$this->assign ( 'qa_list', $qa_list );
		$this->display ();
	}
	
public function fund_detail() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$vo['establish_date'] = $vo['establish_date']!=0?date("Y-m-d",$vo['establish_date']):'';
		$vo['max_payback'] = floatval($vo['max_payback']);
		$vo['average_payback'] = floatval($vo['average_payback']);
		$vo['total_payback'] = floatval($vo['total_payback']);

		$qa_list = M("fund_manage_team")->where("fund_id=".$id)->findAll();
		// echo var_dump($qa_list);
		foreach($qa_list as $k=>$v){
			if(trim($v['head_logo']) != ''){
				//获取缩略图完整url地址
				include_once  APP_ROOT_PATH."system/common.php";
				$real_url=trim($v['head_logo']);
				$qa_list[$k]['real_url']=getQiniuPath($real_url,"img");
				// echo var_dump($qa_list[$k]['real_url'],$v['head_logo']);
			}
		}
		$this->assign ( 'vo', $vo );
		$this->assign ( 'qa_list', $qa_list );
		$this->display ();
	}


public function update() {
		/*var_dump($_REQUEST);die();*/
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		
		/*if(!check_empty($_REQUEST['code']))
		{
			$this->error("请输入基金编码"); 
		}*/
		if(!check_empty($_REQUEST['name']))
		{
			$this->error("请输入基金名称"); 
		}
		if(utf8_strlen($_REQUEST['name'])>30)
		{
			$this->error("请确保基金名称在30个字以内"); 
		}
		if(!check_empty($_REQUEST['short_name']))
		{
			$this->error("请输入基金简称"); 
		}
		if(utf8_strlen($_REQUEST['short_name'])>6)
		{
			$this->error("请确保基金简称在6个字以内"); 
		}
		if(!check_empty($_REQUEST['manager']))
		{
			$this->error("请输入基金管理人");
		}
 	    if(!check_empty($_REQUEST['total_amount']))
		{
			$this->error("请输入基金规模"); 
		}
 		if(!check_empty($_REQUEST['establish_date']))
		{
			$this->error("请选择成立日期"); 
		}
 		if(!check_empty($_REQUEST['deadline']))
		{
			$this->error("请输入基金期限"); 
		}
		if(!check_empty($_REQUEST['summary']))
		{
			$this->error("请输入基金简介"); 
		}
		if(!check_empty($_REQUEST['max_payback']))
		{
			$this->error("请输入已投资项目单个最高回报"); 
		}
		if(!check_empty($_REQUEST['average_payback']))
		{
			$this->error("请输入已投资项目总体平均回报"); 
		}
		if(!check_empty($_REQUEST['total_payback']))
		{
			$this->error("请输入基金整体回报"); 
		}
	 
	 	// $data['code'] = trim($_REQUEST['code']);
		$data['name'] = trim($_REQUEST['name']);
		$data['short_name'] = trim($_REQUEST['short_name']);
		$data['manager'] = trim($_REQUEST['manager']);
		$data['total_amount'] = $_REQUEST['total_amount'];
		$data['deadline'] = trim($_REQUEST['deadline']);
		$data['summary'] =  trim($_REQUEST['summary']);
		$data['max_payback'] = trim($_REQUEST['max_payback']);
		$data['average_payback'] = trim($_REQUEST['average_payback']);
		$data['total_payback'] = trim($_REQUEST['total_payback']);
		$data['status'] = trim($_REQUEST['status']);
		
		$data['establish_date'] = trim($data['establish_date'])==''?0:to_timespan($data['establish_date']);
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$data['operator'] = $adm_name;
 		$data['update_time'] = time();

		M("fund_manage_team")->where('fund_id='.$data['id'])->delete();
		foreach($_REQUEST['team_name'] as $k =>$v){
			if(trim($v) != "" && trim($_REQUEST['title'][$k]) != '' && trim($_REQUEST['brief'][$k]) != ''){
				$manage_team = array();
				$manage_team['fund_id'] = $data['id'];
				$manage_team['name'] = trim($v); 
				$manage_team['position'] = trim($_REQUEST['title'][$k]);
				$manage_team['summary'] = trim($_REQUEST['brief'][$k]);
				$manage_team['head_logo'] = trim($_REQUEST['image_key'][$k]);
				M("fund_manage_team")->add($manage_team);
			}
		}
		$list=M(MODULE_NAME)->save($data);
		
		if (false !== $list) {
			//成功提示			
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
}

public function update_fund_status() {
		//更新状态
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		$status =  $_REQUEST ['status'];
		

		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where( $condition )->setField("status",$status);		
				if ($list!==false) {
					save_log($info."更新状态成功",1);
					$this->success ("更新状态成功",$ajax);
				} else {
					save_log($info."更新状态出错",0);					
					$this->error ("更新状态出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}

public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M("Deal")->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M("Deal")->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function delete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where( $condition )->setField("is_delete",0);		
				if ($list!==false) {
					save_log($info."成功移到回收站",1);
					$this->success ("成功移到回收站",$ajax);
				} else {
					save_log($info."移到回收站出错",0);					
					$this->error ("移到回收站出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
public function find_investor() {
		//查找投资人
		$user_name = $_REQUEST ['linkValue'];
		$fund_id = $_REQUEST ['fund_id'];
		if (isset ( $user_name )) {
				$rel_data = M(user)->where("user_name like '%$user_name%' and is_effect=1 and is_review=1 and user_type=1 and id not in (SELECT fund.user_id from cixi_user_fund_relation as fund WHERE fund.fund_id=$fund_id)")->findAll();
				if ($rel_data!==false) {
					 // $return_datas = array();
					 if($rel_data != null){
					 	foreach($rel_data as $sub_rel_data){
					 		$return_data['id'] = $sub_rel_data['id'];
					 		$return_data['name'] = $sub_rel_data['user_name'];
					 		$return_data['third_data'] =  $sub_rel_data['mobile'];
					 		$return_datas[] = $return_data;
					 	}
					 }
					 $this->ajaxReturn($return_datas,'',1);
				} else {
					 $this->ajaxReturn('','未查询到数据',0);
				}
			} else {
				 $this->ajaxReturn('','没有输入数据',0);
		}
	}
	
	public function find_deal() {
		//查找投资项目
		$deal_name = $_REQUEST ['linkValue'];
		$fund_id = $_REQUEST ['fund_id'];
		if (isset ( $deal_name )) {
				$rel_data = M(deal)->where("name like '%$deal_name%' and is_delete=0 and is_effect=3 and id not in (SELECT relation.deal_id from cixi_fund_deal_relation as relation WHERE relation.fund_id=$fund_id )")->findAll();	
				if ($rel_data!==false) {
					 $return_datas = array();
					 if($rel_data != null){
					 	foreach($rel_data as $sub_rel_data){
					 		$return_data['id'] = $sub_rel_data['id'];
					 		$return_data['name'] = $sub_rel_data['name'];
					 		$return_data['third_data'] =  $sub_rel_data['s_name'];
					 		$return_datas[] = $return_data;
					 	}
					 }
					 $this->ajaxReturn($return_datas,'',1);
				} else {
					 $this->ajaxReturn('','未查询到数据',0);
				}
			} else {
				 $this->ajaxReturn('','没有输入数据',0);
		}
	}
	
	public function restore() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField("f_delete",0);				
				if ($list!==false) {
					save_log($info."恢复成功",1);
					$this->success ("恢复成功",$ajax);
				} else {
					save_log($info."恢复出错",0);
					$this->error ("恢复出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	 
	 
	
	public function deal_item()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Deal")->getById($deal_id);
		$this->assign("deal_info",$deal_info);
		
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];		
			if (method_exists ( $this, '_filter' )) {
				$this->_filter ( $map );
			}
			$name=$this->getActionName();
			$model = D ("DealItem");
			if (! empty ( $model )) {
				$this->_list ( $model, $map );
			}
		}
		
		$this->display();
	}
	
	public function add_deal_item()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Deal")->getById($deal_id);
		$this->assign("deal_info",$deal_info);
		$this->display();
	}
	
	
	//管理投资人
	public function investor_list()
	{	
		if(trim($_REQUEST['name']) != ''){
			$map['name'] = trim($_REQUEST['name']);
		}
		if(trim($_REQUEST['mobile']) != ''){
			$map['mobile'] = trim($_REQUEST['mobile']);
		}
		
		$id = intval($_REQUEST['fund_id']);
		
		
		$list_sql = "select a.id as id,a.user_id as user_id,a.investor_amount as investor_amount,a.investor_rate as investor_rate,a.remark as remark,a.create_time as create_time,b.user_name as user_name,b.mobile as mobile "
					." from ".DB_PREFIX."user_fund_relation as a,".DB_PREFIX."user as b "
					." where b.id=a.user_id and a.fund_id=".$id;
					
		$count_sql = "select count(1) "
					." from ".DB_PREFIX."user_fund_relation as a,".DB_PREFIX."user as b "
					." where b.id=a.user_id and a.fund_id=".$id;
					
		if($map['name'] != ''){
			$list_sql .= " and b.user_name like '%".$map['name']."%' ";			
			$count_sql .= " and b.user_name like '%".$map['name']."%' ";	
		}
		if($map['mobile'] != ''){
			$list_sql .= " and b.mobile like '%".$map['mobile']."%' ";	
			$count_sql .= " and b.mobile like '%".$map['mobile']."%' ";	
		}
		$list_sql .= " ORDER BY a.id desc ";
		
		//		$id = intval($_REQUEST['id']);
		//		$result = $GLOBALS['db']->getAll("select a.id as id,a.user_id as user_id,a.investor_amount as investor_amount,a.investor_rate as investor_rate,a.remark as remark,b.user_name as user_name,b.mobile as mobile from ".DB_PREFIX."user_fund_relation as a,".DB_PREFIX."user as b where b.id=a.user_id and a.fund_id=".$id." ORDER BY a.id asc"); 
		// 获取数据结果
		$result = $this->_list_multi_table($count_sql, $list_sql, $map);
		foreach ($result as $k=>$v){
			$result[$k]['create_time'] = to_date($v['create_time'],'Y-m-d');
		}
		
		$this->assign("list",$result);
		$this->display('investor_list');
	} 
 	
 	public function investor_add()
	{ 	
		$fund_id = intval($_REQUEST['fund_id']);
 		$investor_list = $GLOBALS['db']->getAll("select id,user_name from ".DB_PREFIX."user where is_review = 1 and is_effect=1 order by id desc");  //二级地址
		$this->assign("investor_list",$investor_list);
		$this->assign("fund_id",$fund_id);
		$this->display('investor_add');
	}

	public function investor_insert() {

	  //var_dump($_REQUEST['team_name']);die();
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(user_fund_relation)->create ();
	
		/***********ADD   get initiator user id which will be insert into cixi_deal table******************************/
 		 
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/investor_add",array("fund_id"=>$_REQUEST['fund_id'])));
		
		if(!check_empty($data['investor_amount']))
		{
			$this->error("请输入购买份额"); 
		}
	 
		if(!check_empty($data['investor_rate']))
		{
			$this->error("请输入购买占比"); 
		}
		$data['create_time'] = to_timespan($data['create_time'],'Y-m-d');
 		$data['update_time'] = time();
		$data['fund_id'] = $data['fund_id'];
		$data['user_id'] = $data['user_id'];
 				
		$list=M(user_fund_relation)->add($data);
		
		if (false !== $list) {
			//成功提示
 			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
 			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	function investor_edit(){
		$id = intval($_REQUEST ['id']);
		// 准表单数据
		$vo =M(user_fund_relation)->where("id=".$id)->find();
		// 准备用户下拉列表数据
		$investor = M(user)->where('id='.$vo['user_id'])->find();
		$vo['user_name'] = $investor['user_name'];
		$vo['mobile'] = $investor['mobile'];
		$vo['create_time'] = to_date($vo['create_time'],'Y-m-d');
		$this->assign ( 'vo', $vo );
		$this->display('investor_edit');
	}
	
	function investor_update(){
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(user_fund_relation)->create ();
	
		/***********ADD   get initiator user id which will be insert into cixi_deal table******************************/
 		 
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/investor_edit",array("id"=>$data['id'])));
		
		if(!check_empty($data['investor_amount']))
		{
			$this->error("请输入购买份额"); 
		}
	 
		if(!check_empty($data['investor_rate']))
		{
			$this->error("请输入购买占比"); 
		}
		$data['create_time'] = to_timespan($data['create_time'],'Y-m-d');
 		$data['update_time'] = time();
		$data['fund_id'] = $data['fund_id'];
		$data['user_id'] = $data['user_id'];
 				
		$list=M(user_fund_relation)->save($data);
		
		if (false !== $list) {
			//成功提示
 			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
 			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"));
		}
	}
	
	function investor_del(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(user_fund_relation)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(user_fund_relation)->where( $condition )->delete();		
				if ($list!==false) {
					save_log($info."成功移到回收站",1);
					$this->success ("成功移到回收站",$ajax);
				} else {
					save_log($info."移到回收站出错",0);					
					$this->error ("移到回收站出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}



	//管理投资项目
 
	public function relation_list()
	{	
		
		if(trim($_REQUEST['short_name']) != ''){
			$map['short_name'] = trim($_REQUEST['short_name']);
		}
		if(trim($_REQUEST['name']) != ''){
			$map['name'] = trim($_REQUEST['name']);
		}
		
		$id = intval($_REQUEST['fund_id']);
		
		$list_sql = " SELECT cfdr.id as id, cd.`name` AS name,cd.s_name AS short_name,cfdr.investor_amount AS investor_amount,cfdr.investor_date AS investor_date,cfdr.remark AS remark "
					." FROM cixi_deal AS cd, cixi_fund_deal_relation AS cfdr "
					." WHERE cd.id = cfdr.deal_id AND cfdr.fund_id = $id " ;
		$count_sql = " SELECT count(1) "
					." FROM cixi_deal AS cd, cixi_fund_deal_relation AS cfdr "
					." WHERE cd.id = cfdr.deal_id AND cfdr.fund_id = $id " ;
					
		if($map['name'] != ''){
			$list_sql .= " and cd.name like '%".$map['name']."%' ";			
			$count_sql .= " and cd.name like '%".$map['name']."%' ";	
		}
		if($map['short_name'] != ''){
			$list_sql .= " and cd.s_name like '%".$map['short_name']."%' ";			
			$count_sql .= " and cd.s_name like '%".$map['short_name']."%' ";	
		}
					
		$result = $this->_list_multi_table($count_sql, $list_sql, $map);
		foreach ($result as $k=>$v){
			$result[$k]['investor_date'] = to_date($v['investor_date'],'Y-m-d');
		}
 		$this->assign("list",$result);
		$this->display ('relation_list');
	} 
 	
 	public function relation_add()
	{ 	$fund_id = intval($_REQUEST['fund_id']);
 		$deal_list = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."deal where is_effect in (2,3) and is_delete=0 order by id desc");  //二级地址
		$this->assign("deal_list",$deal_list);
		$this->assign("fund_id",$fund_id);
		$this->display ('relation_add');
	}

	public function relation_insert() {

		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(fund_deal_relation)->create ();
	
		/***********ADD   get initiator user id which will be insert into cixi_deal table******************************/
 		 
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/relation_add",array('fund_id'=>$_REQUEST['fund_id'])));
		
		if(!check_empty($data['investor_amount']))
		{
			$this->error("请输入投资金额"); 
		}
	 
		if(!check_empty($data['investor_date']))
		{
			$this->error("请输入投资日期:"); 
		}
		
		$data['investor_date'] = to_timespan($data['investor_date']);
 	    $data['deal_id'] = $data['deal_id'];
 		$data['create_time'] = time();
		$data['fund_id'] = $data['fund_id'];
 				
		$list=M(fund_deal_relation)->add($data);
		//$list_1=M('user')->add($user_data);
		if (false !== $list) {
			//成功提示
 			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
 			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	function relation_edit(){
		$id = intval($_REQUEST ['id']);
		// 准表单数据
		$vo =M(fund_deal_relation)->where("id=".$id)->find();
		
		$vo['investor_date'] = date("Y-m-d",$vo['investor_date']);
		// 准备项目下拉列表数据
		$deal = M(deal)->where("is_effect=3 and is_delete=0 and id=".$vo['deal_id'])->find();
		$vo['deal_name'] = $deal['name'];
		$vo['deal_short_name'] = $deal['s_name'];
		$this->assign ( 'vo', $vo );
		// $this->assign ( 'deal_list', $deal_list);
		$this->display('relation_edit');
	}
	
	function relation_update(){
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(fund_deal_relation)->create ();
	
		/***********ADD   get initiator user id which will be insert into cixi_deal table******************************/
 		 
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/relation_edit",array("id"=>$data['id'])));
		
		if(!check_empty($data['investor_amount']))
		{
			$this->error("请输入投资金额"); 
		}
	 
		if(!check_empty($data['investor_date']))
		{
			$this->error("请输入投资日期"); 
		}
		$data['investor_date'] = to_timespan($data['investor_date']);
 		$data['update_time'] = time();
		$data['fund_id'] = $data['fund_id'];
		$data['deal_id'] = $data['deal_id'];
 				
		$list=M(fund_deal_relation)->save($data);
		
		if (false !== $list) {
			//成功提示
 			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
 			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"));
		}
	}
	
	function relation_del(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(fund_deal_relation)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(fund_deal_relation)->where( $condition )->delete();		
				if ($list!==false) {
					save_log($info."成功移到回收站",1);
					$this->success ("成功移到回收站",$ajax);
				} else {
					save_log($info."移到回收站出错",0);					
					$this->error ("移到回收站出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	

	 
}
?>