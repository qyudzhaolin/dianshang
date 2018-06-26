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
		    $mids = M('fund_managers')->where('name like "%'.trim($_REQUEST['manager']).'%"')->getField('id,id');
		    $mids_set = true;
		}
		if(trim($_REQUEST['status']) != '' && trim($_REQUEST['status']) != '0'){
			$map['status'] = $_REQUEST['status'];
		}
		
		if(isset($mids_set)){
		    if(empty($mids)){
		        // 设置，为空，没有数据
		        $s = false;
		    }else{
		        // 设置，不为空，添加查询条件
		        $map['managers_id'] = array('in',implode(',',$mids));
		        $s = true;
		    }
		}else{
		    // 没设置，正常查询
		    $s = true;
		}
		
		if($s){
    	    if (method_exists ( $this, '_filter' )) {
    	        $this->_filter ( $map );
    	    }
    	    $map['is_csdk_fund'] = 1;//磁斯达克基金
    	    $map['is_delete'] = 1; // 只查有效
    	    $name=$this->getActionName();
    	    $model = D ($name);
    	    
    	    if (! empty ( $model )) {
    	        $list=$this->_list ( $model, $map );
    	        foreach($list as $k=>$v){
    	            if($v['status']==1){
    	                $list[$k]['status_value']='未发布';
    	                $list[$k]['button_list']='<a href="javascript:edit('.$list[$k]['id'].')">编辑</a>&nbsp;<a href="javascript: del('.$list[$k]['id'].')">删除</a>';
    	            }elseif($v['status']==2){
    	                $list[$k]['status_value']='已发布';
    	                $list[$k]['button_list']='<a href="javascript: revoke('.$list[$k]['id'].')">撤回</a>';
    	            }elseif($v['status']==3){
    	                $list[$k]['status_value']='已撤回';
    	                $list[$k]['button_list'] = '<a href="javascript:edit('.$list[$k]['id'].')">编辑</a>&nbsp;<a href="javascript: del('.$list[$k]['id'].')">删除</a>';
    	            }
    	            if(in_array($v['status'],array(1,3))){
    	                // 有投资人 + 有投资项目
    	                $tmp1 = M('user_fund_relation')->where('fund_id='.$v['id'].' and user_type = 1')->find();
//     	                $tmp2 = M('deal_trade_fund_relation')->where('fund_id='.$v['id'].' and is_csdk_fund = 1')->find();
    	                if(!empty($tmp1)){
            	            $list[$k]['button_list'] .= '&nbsp;<a href="javascript: publish('.$list[$k]['id'].')">发布</a>';
    	                }
    	            }
    	            $list[$k]['button_list'] .= '&nbsp;<a href="javascript: fund_investor('.$list[$k]['id'].')">管理基金投资人</a>&nbsp;<a href="javascript: investment_details('.$list[$k]['id'].')">投资详情</a>&nbsp;<a href="javascript: fund_detail('.$list[$k]['id'].')">基金详情</a>&nbsp;<a href="javascript:attachment_index('.$list[$k]['id'].')">信息披露</a>';
    	            $list[$k]['total_amount'] = number_format($v['total_amount']);
    	            $list[$k]['establish_date'] = date('Y-m-d',$v['establish_date']);
    	            $list[$k]['managers_id_cn'] = M('fund_managers')->where('id='.$v['managers_id'])->getField('short_name');
    	        }
    	    }
		}else{
		    $list = array();
		}
		
		// 初始化状态列表
		$status_list = array(
			array("id"=>1,"name"=>"未发布"),
			array("id"=>2,"name"=>"已发布"),
			array("id"=>3,"name"=>"已撤回")
		);		
		
		$this->assign("status_list",$status_list);
		$this->assign("list",$list);
		$this->display ();
	}
	
	public function delete_fund()
	{	
	    
		if(trim($_REQUEST['name'])!=''){
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		if(trim($_REQUEST['short_name'])!=''){
			$map['short_name'] = array('like','%'.trim($_REQUEST['short_name']).'%');
		}
		if(trim($_REQUEST['manager'])!=''){
		    $mids = M('fund_managers')->where('name like "%'.trim($_REQUEST['manager']).'%"')->getField('id,id');
		    $mids_set = true;
		}
		if(trim($_REQUEST['status']) != '' && trim($_REQUEST['status']) != '0'){
			$map['status'] = $_REQUEST['status'];
		}
		
		if(isset($mids_set)){
		    if(empty($mids)){
		        // 设置，为空，没有数据
		        $s = false;
		    }else{
		        // 设置，不为空，添加查询条件
		        $map['managers_id'] = array('in',implode(',',$mids));
		        $s = true;
		    }
		}else{
		    // 没设置，正常查询
		    $s = true;
		}
		
		if($s){
    	    if (method_exists ( $this, '_filter' )) {
    	        $this->_filter ( $map );
    	    }
    	    $map['is_csdk_fund'] = 1;//磁斯达克基金
    	    $map['is_delete'] = 0; // 只查删除的
    	    $name=$this->getActionName();
    	    $model = D ($name);
    	    
    	    if (! empty ( $model )) {
    	        $list=$this->_list ( $model, $map );
    	        foreach($list as $k=>$v){
    	            if($v['status']==1){
    	                $list[$k]['status_value']='未发布';
    	            }elseif($v['status']==2){
    	                $list[$k]['status_value']='已发布';
    	            }elseif($v['status']==3){
    	                $list[$k]['status_value']='已撤回';
    	            }
    	            $list[$k]['button_list'] = '<a href="javascript:fund_restore('.$list[$k]['id'].')">恢复</a>';
    	            $list[$k]['total_amount'] = number_format($v['total_amount']);
    	            $list[$k]['establish_date'] = date('Y-m-d',$v['establish_date']);
    	            $list[$k]['managers_id_cn'] = $v['managers_id'] ? M('fund_managers')->where('id='.$v['managers_id'])->getField('short_name') : '无';
    	        }
    	    }
		}else{
		    $list = array();
		}
		
		// 初始化状态列表
		$status_list = array(
			array("id"=>1,"name"=>"未发布"),
// 			array("id"=>2,"name"=>"已发布"),
			array("id"=>3,"name"=>"已撤回")
		);
		
		$this->assign("status_list",$status_list);
		$this->assign("list",$list);
		$this->display ();
	}

	/*
	 * 基金增加页面
	 */
	public function add()
	{
	    // 基金管理人
	    $manages = M('fund_managers')->where('is_del = 1')->select();
	    $this->assign('manages',$manages);
	    
		$this->display();
	}
	/*
	 * 基金增加->基金管理团队，查找基金管理公司中的团队成员方法
	 */
	public function fund_add_team_search(){
	    
	    if (intval($_REQUEST['mid']) > 0) {
	        
	        $map = " and u.id = m.user_id and m.managers_id = ".intval($_REQUEST['mid']);
	         
	        if (trim($_REQUEST['name']) != '') {
	            $map .= " and u.user_name like '%{$_REQUEST['name']}%'";
	        }
	        if (trim($_REQUEST['title']) != '') {
	            $map .= " and m.title like '%{$_REQUEST['title']}%'";
	        }
	        
	        $map .= " and m.is_del = 1";
	        
	        $count_sql = "select count(1) from ".DB_PREFIX."user as u,".DB_PREFIX."fund_managers_team as m where 1 {$map}";
	        $list_sql = "select u.id as user_id,u.mobile,u.user_name,m.title,m.code,m.id from ".DB_PREFIX."user as u,".DB_PREFIX."fund_managers_team as m where 1 {$map} ";
	        $result = $this->_list_multi_table($count_sql, $list_sql, $map, 'u.id ');
	        $this->assign("list", $result);
	        $this->display();
	        
	    }else{
	        echo '请选择基金管理人';
	    }
	}
	/*
	 * 基金增加->基金管理团队，选择团队成员
    */
	public function get_manage_team_info(){
	    if (intval($_REQUEST['id']) > 0) {
	        $info = M("fund_managers_team")->getById(intval($_REQUEST['id']));
	        if ($info) {
	            $info['real_logo'] = getQiniuPath($info['user_logo'], "img");
	            $info['name'] = M('user')->where('id='.$info['user_id'])->getField('user_name');
	            $result['data'] = $info;
	            $result['status'] = true;
	            ajax_return($result);
	        } else {
	            $result['status'] = false;
	            ajax_return($result);
	        }
	    }
	    $result['status'] = false;
	    ajax_return($result);
	}
	public function insert() {

		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();
	
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
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
		if(!check_empty($data['managers_id']))
		{
			$this->error("请选择基金管理人");
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
		
		$data['establish_date'] = strtotime($data['establish_date']);
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$data['operator'] = $adm_session['adm_name'];
 		$data['update_time'] = time();
		$data['is_delete'] = 1;
		$data['status'] = 1;
		
 		// 添加基金	
		$list = M(MODULE_NAME)->add($data);
		if (false !== $list) {
			// 存储team信息
			foreach($_REQUEST['managers_team_id'] as $k =>$v){
				if(trim($v) != "" && trim($_REQUEST['position'][$k]) != '' && trim($_REQUEST['brief'][$k]) != ''){
					$manage_team = array();
					$manage_team['user_id'] = trim($_REQUEST['user_id'][$k]);
					$manage_team['user_type'] = 4; #投资总监
					$manage_team['fund_id'] = $list;
					$manage_team['managers_team_id'] = trim($v); 
					$manage_team['position'] = trim($_REQUEST['position'][$k]);
					$manage_team['brief'] = trim($_REQUEST['brief'][$k]);
					$manage_team['is_director'] = trim($_REQUEST['is_director'][$k]);
					$manage_team['update_time'] = time();
					$manage_team['operator'] = $adm_session['adm_id'];
					M("user_fund_relation")->add($manage_team);
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
		
		$vo['establish_date'] = date("Y-m-d",$vo['establish_date']);
		$vo['max_payback'] = floatval($vo['max_payback']);
		$vo['average_payback'] = floatval($vo['average_payback']);
		$vo['total_payback'] = floatval($vo['total_payback']);

		// 基金管理人
		$manages = M('fund_managers')->where('is_del = 1')->select();
		$this->assign('manages',$manages);
		
		// 基金管理团队
		$team_list = M("user_fund_relation")->where("fund_id=".$id." and user_type=4")->findAll();
		foreach($team_list as $k=>$v){
		    // 获取头像和名称
		    $mt = M('fund_managers_team')->where('id='.$v['managers_team_id'])->find();
		    $team_list[$k]['user_logo'] = getQiniuPath($mt['user_logo'],"img");
		    $team_list[$k]['name'] = M('user')->where('id='.$mt['user_id'])->getField('user_name');
		}
		$this->assign ( 'vo', $vo );
		$this->assign ( 'team_list', $team_list );
		$this->display ();
	}
	
    public function fund_detail() {	
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$vo['establish_date'] = date("Y-m-d",$vo['establish_date']);
		$vo['max_payback'] = floatval($vo['max_payback']);
		$vo['average_payback'] = floatval($vo['average_payback']);
		$vo['total_payback'] = floatval($vo['total_payback']);
		
        // 基金管理人
		$manages = M('fund_managers')->where('is_del = 1')->select();
		$this->assign('manages',$manages);
		
		// 基金管理团队
		$team_list = M("user_fund_relation")->where("fund_id=".$id." and user_type=4")->findAll();
		foreach($team_list as $k=>$v){
		    // 获取头像和名称
		    $mt = M('fund_managers_team')->where('id='.$v['managers_team_id'])->find();
		    $team_list[$k]['user_logo'] = getQiniuPath($mt['user_logo'],"img");
		    $team_list[$k]['name'] = M('user')->where('id='.$mt['user_id'])->getField('user_name');
		}
		$this->assign ( 'vo', $vo );
		$this->assign ( 'team_list', $team_list );
		$this->display ();
	}

    public function update() {
		
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
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
		if(!check_empty($data['managers_id']))
		{
			$this->error("请选择基金管理人");
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

		M("user_fund_relation")->where('fund_id='.$data['id']." and user_type = 4")->delete();
        foreach($_REQUEST['managers_team_id'] as $k =>$v){
			if(trim($v) != "" && trim($_REQUEST['position'][$k]) != '' && trim($_REQUEST['brief'][$k]) != ''){
				$manage_team = array();
				$manage_team['user_id'] = trim($_REQUEST['user_id'][$k]);
				$manage_team['user_type'] = 4; #投资总监
				$manage_team['fund_id'] = $data['id'];
				$manage_team['managers_team_id'] = trim($v); 
				$manage_team['position'] = trim($_REQUEST['position'][$k]);
				$manage_team['brief'] = trim($_REQUEST['brief'][$k]);
				$manage_team['is_director'] = trim($_REQUEST['is_director'][$k]);
				$manage_team['update_time'] = time();
				$manage_team['operator'] = $adm_session['adm_id'];
				M("user_fund_relation")->add($manage_team);
			}
		}
		
		$data['name'] = trim($_REQUEST['name']);
		$data['short_name'] = trim($_REQUEST['short_name']);
		$data['managers_id'] = trim($_REQUEST['managers_id']);
		$data['total_amount'] = $_REQUEST['total_amount'];
		$data['establish_date'] = strtotime($data['establish_date']);
		$data['deadline'] = trim($_REQUEST['deadline']);
		$data['summary'] =  trim($_REQUEST['summary']);
// 		$data['status'] = trim($_REQUEST['status']);
		
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_name = $adm_session['adm_name'];
		$data['operator'] = $adm_name;
		$data['update_time'] = time();
		
		$list = M(MODULE_NAME)->save($data);
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
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

	/*
	 * 项目融资记录与基金关系表排序
	 */
    public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		// 修改所有项目的排序为同一个值
		M("deal_trade_fund_relation")->where("deal_id=".$id)->setField("sort",$sort);
		save_log("项目融资记录与基金关系数据".l("SORT_SUCCESS"),1);
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
	
	public function fund_restore(){
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
	        $list = M(MODULE_NAME)->where( $condition )->setField("is_delete",1);
	        if ($list!==false) {
	            save_log($info."成功移出回收站",1);
	            $this->success ("成功移出回收站",$ajax);
	        } else {
	            save_log($info."移出回收站出错",0);
	            $this->error ("移出回收站出错",$ajax);
	        }
	    } else {
	        $this->error (l("INVALID_OPERATION"),$ajax);
	    }
	}
	
    public function find_investor() {
		//查找投资人
		$user_name = trim($_REQUEST ['linkValue']);
		$fund_id = (int)$_REQUEST ['fund_id'];
		if (!empty ( $user_name )) {
				$rel_data = M('user')->where("user_name like '%{$user_name}%' and is_effect=1 and is_review=1 and user_type=1 and id not in (SELECT user_id from cixi_user_fund_relation WHERE fund_id={$fund_id} and user_type = 1)")->findAll();
				if ($rel_data!==false) {
					 if($rel_data != null){
					 	foreach($rel_data as $sub_rel_data){
					 		$return_data['id'] = $sub_rel_data['id'];
					 		$return_data['name'] = $sub_rel_data['user_name'].' （'.$sub_rel_data['mobile'].'）';
					 		$return_data['third_data'] =  $sub_rel_data['mobile'];
					 		$return_datas[] = $return_data;
					   }
                     $this->ajaxReturn($return_datas, '', 1);
                }else{
                     $this->ajaxReturn($return_datas, '', 0);
                   }
				} else {
					 $this->ajaxReturn('','未查询到数据',2);
				}
			} else {
				 $this->ajaxReturn('','没有输入数据',3);
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
					 $this->ajaxReturn($return_datas,'',0);
				} else {
					 $this->ajaxReturn('','未查询到数据',2);
				}
			} else {
				 $this->ajaxReturn('','没有输入数据',3);
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
	
	
	// 管理投资人
	public function investor_list()
	{	
		$id = intval($_REQUEST['fund_id']);
		$field = "a.id,a.user_id,a.investor_amount,a.investor_rate,a.remark,a.partner_user_id,b.user_name,b.mobile";
		$map = "a.fund_id = {$id} and a.user_type = 1 and b.id = a.user_id";
		
		if(trim($_REQUEST['name']) != ''){
		    $map .= " and b.user_name like '%".$_REQUEST['name']."%' ";			
		}
		if(trim($_REQUEST['mobile']) != ''){
		    $map .= " and b.mobile = ".(int)$_REQUEST['mobile'];	
		}
		if(trim($_REQUEST['partner_id']) != ''){
		    $map .= " and a.partner_user_id = ".(int)$_REQUEST['partner_id'];	
		}
		
		$list_sql = "select {$field} "
		             ." from ".DB_PREFIX."user_fund_relation as a,".DB_PREFIX."user as b "
	                 ."where {$map} ORDER BY a.id desc ";
					
		$count_sql = "select count(1) "
					." from ".DB_PREFIX."user_fund_relation as a,".DB_PREFIX."user as b "
					." where {$map}";
					
		// 获取数据结果
		$result = $this->_list_multi_table($count_sql, $list_sql, $map);
		foreach ($result as $k=>$v){
		    if($v['partner_user_id']){
                $result[$k]['partner_user_name'] = M('user')->where('id='.$v['partner_user_id'])->getField('user_name');
		    }else{
                $result[$k]['partner_user_name'] = '无';
		    }
		}
		
		// 渠道合伙人
		$partners = M('user')->where('role = 1 and is_effect = 1 and is_review = 1')->getField('id,user_name');
		
		$this->assign("list",$result);
		$this->assign("partners",$partners);
		$this->display('investor_list');
	} 
 	
 	public function investor_add()
	{ 	
		$fund_id = intval($_REQUEST['fund_id']);
		$this->assign("fund_id",$fund_id);
		
		// 渠道合伙人
		$partners = M('user')->where('role = 1 and is_effect = 1 and is_review = 1')->getField('id,user_name');
		$this->assign("partners",$partners);
		
		// 基金规模
		$total_amount = M('fund')->where('id='.$fund_id)->getField('total_amount');
		$this->assign("total_amount",$total_amount);
		
		$this->display('investor_add');
	}

	public function investor_insert() {

		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M('user_fund_relation')->create ();
 		 
		//开始验证有效性
		if(!check_empty($data['investor_amount']))
		{
			$this->error("请输入购买份额"); 
		}
	 
		if(!check_empty($data['investor_rate']))
		{
			$this->error("请输入购买占比"); 
		}
		
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$data['create_time'] = to_timespan($data['create_time'],'Y-m-d');
 		$data['update_time'] = time();
		$data['user_type'] = 1;
		$data['operator'] = $adm_session['adm_id'];
		
		M()->startTrans();
		
		// 是否为投资人默认基金，只能有一个默认基金
		// 后台不在做操作is_default_fund，统一APP端操作该字段 sunerju 2016-08-05
// 		if(M('user_fund_relation')->where('user_id='.$data['user_id'].' and is_default_fund = 1')->find()){
// 		    M('user_fund_relation')->where('user_id='.$data['user_id'])->setField('is_default_fund',0);
// 		}
		
	    // 作为投资人
	    $data['user_type'] = 1;
	    $re = M('user_fund_relation')->add($data);
	    if(!$re){
	        M()->rollback();
	        // 错误提示
	        save_log("添加投资人".L("INSERT_FAILED"),1);
	        $this->error("添加投资人".L("INSERT_FAILED"));
	        exit();
	    }
	    
	    // 作为渠道合伙人
		if($data['partner_user_id']){
	        // 不存在，添加
		    $new_p = M('user_fund_relation')->where('user_id= '.$data['partner_user_id'].'and user_type= 3 and fund_id = '.$data['fund_id'])->find();
		    if(empty($new_p)){
    		    $data2['user_id'] = $data['partner_user_id'];
    		    $data2['fund_id'] = $data['fund_id'];
    		    $data2['update_time'] = time();
    		    $data2['operator'] = $data['operator'];
    		    $data2['user_type'] = 3;
    		    $re = M('user_fund_relation')->add($data2);
    		    if(!$data2){
    		        M()->rollback();
    		        // 错误提示
    		        save_log("添加投资人，渠道合伙人".L("INSERT_FAILED"),1);
    		        $this->error("添加投资人，渠道合伙人".L("INSERT_FAILED"));
    		        exit();
    		    }
		    }
		}
		
		// 发送短信
		$fname    = M('fund')->where('id='.$data['fund_id'])->getField('short_name');
		$msg      = getSendSmsTemplate("admin_fund_bind_investor",array($fname,$data['investor_amount'],$data['investor_rate']));
		$params = array(
		    "mobile"    => $_REQUEST['user_mobile'],
		    "content"   => $msg,
		    "type"      => getSendSmsType("admin_fund_bind_investor"),
		);
		$result = request_service_api("Common.Sms.sendSms",$params);
		
		// 发送站内信
		$user_notify['user_id']     = $data['user_id'];
		$user_notify['log_info']    = $msg;
		$user_notify['url']         = "";
		$user_notify['log_time']    = time();
		$user_notify['is_read']     = 0;
		M("user_notify")->add($user_notify);
		
		M()->commit();
		
		//成功提示
		$this->assign("jumpUrl",u(MODULE_NAME."/investor_list",array("fund_id"=>$_REQUEST['fund_id'])));
		save_log($log_info.L("INSERT_SUCCESS"),1);
		$this->success(L("INSERT_SUCCESS"));
	}	
	
	function investor_edit(){
		$id = intval($_REQUEST ['id']);
		// 准表单数据
		$vo = M('user_fund_relation')->where("id=".$id)->find();
		$investor = M('user')->where('id='.$vo['user_id'])->find();
		$vo['user_name'] = $investor['user_name'];
		$vo['mobile'] = $investor['mobile'];
		$vo['create_time'] = to_date($vo['create_time'],'Y-m-d');
		
		// 渠道合伙人，排除自身
		$partners = M('user')->where('role = 1 and is_effect = 1 and is_review = 1 and id <> '.$vo['user_id'])->getField('id,user_name');
		$this->assign("partners",$partners);
		
		// 基金规模
		$total_amount = M('fund')->where('id='.$vo['fund_id'])->getField('total_amount');
		$this->assign("total_amount",$total_amount);
		
		$this->assign ( 'vo', $vo );
		$this->display('investor_edit');
	}
	
	function investor_update(){
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(user_fund_relation)->create ();
	
		//开始验证有效性
		if(!check_empty($data['investor_amount']))
		{
			$this->error("请输入购买份额"); 
		}
	 
		if(!check_empty($data['investor_rate']))
		{
			$this->error("请输入购买占比"); 
		}
		
		M()->startTrans();
		
		// 作为投资人
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$data['create_time'] = to_timespan($data['create_time'],'Y-m-d');
		$data['update_time'] = time();
		$data['user_type'] = 1;
		$data['operator'] = $adm_session['adm_id'];
		$list = M('user_fund_relation')->save($data);
		if(!$list){
		    M()->rollback();
		    // 错误提示
		    save_log("修改投资人".$data['id'].L("UPDATE_SUCCESS"),1);
		    $this->error("修改投资人".L("UPDATE_SUCCESS"));
		    exit();
		}
		
		// 作为渠道合伙人，存在新的合伙人
		if($data['partner_user_id']){
		    if($data['partner_user_id'] == $_REQUEST['partner_user_id_old']){
		        // 没有修改合伙人，无后续操作
		        
		    }else{
		        // 修改合伙人，原来的删除（原来存在合伙人的情况下）
		        if($_REQUEST['partner_user_id_old']){
		            // 一个人可以是多个投资人的合伙人,所以需要判断他是否还是别人的合伙人
		            $other_p = M('user_fund_relation')->where('user_type = 1 and partner_user_id = '.$_REQUEST['partner_user_id_old'].' and fund_id = '.$data['fund_id'])->find();
		            if(empty($other_p)){
                        M('user_fund_relation')->where('user_id = '.$_REQUEST['partner_user_id_old'].' and user_type= 3 and fund_id = '.$data['fund_id'])->delete();
		            }
		        }
		        
                // 新的合伙人是否已存在，不存在，添加新的
                $new_p = M('user_fund_relation')->where('user_id = '.$data['partner_user_id'].' and user_type= 3 and fund_id = '.$data['fund_id'])->find();
                if(empty($new_p)){
                    $data2['user_id'] = $data['partner_user_id'];
    		        $data2['fund_id'] = $data['fund_id'];
    		        $data2['update_time'] = time();
    		        $data2['operator'] = $data['operator'];
    		        $data2['user_type'] = 3;
    		        $re = M('user_fund_relation')->add($data2);
    		        if(!$re){
    		            M()->rollback();
    		            // 错误提示
    		            save_log("修改投资人，添加渠道合伙人".L("UPDATE_FAILED"),1);
    		            $this->error("修改投资人，添加渠道合伙人".L("UPDATE_FAILED"));
    		            exit();
    		        }
                }
		    }
		}else{
		    // 原来的删除（原来存在合伙人的情况下）
		    if($_REQUEST['partner_user_id_old']){
		        // 一个人可以是多个投资人的合伙人,所以需要判断他是否还是别人的合伙人
		        $other_p = M('user_fund_relation')->where('user_type = 1 and partner_user_id = '.$_REQUEST['partner_user_id_old'].' and fund_id = '.$data['fund_id'])->find();
		        if(empty($other_p)){
		            M('user_fund_relation')->where('user_id = '.$_REQUEST['partner_user_id_old'].' and user_type= 3 and fund_id = '.$data['fund_id'])->delete();
		        }
		    }
		}

		M()->commit();
		
		//成功提示
		save_log($log_info.L("UPDATE_SUCCESS"),1);
		$this->success(L("UPDATE_SUCCESS"));
	}
	
	function investor_del(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = (int)$_REQUEST ['id'];
		
		if ($id) {
			$rel_data = M('user_fund_relation')->where("id = {$id}")->find();
			// 删除投资人
			$re = M('user_fund_relation')->where("id = {$id}")->delete();
			if ($re!==false) {
			    // 删除渠道合伙人
			    if($rel_data['partner_user_id']){
			        // 一个人可以是多个投资人的合伙人,所以需要判断他是否还是别人的合伙人
			        $other_p = M('user_fund_relation')->where('user_type = 1 and partner_user_id = '.$rel_data['partner_user_id'].' and fund_id = '.$rel_data['fund_id'])->find();
			        if(empty($other_p)){
			            M('user_fund_relation')->where('user_id = '.$rel_data['partner_user_id'].' and user_type= 3 and fund_id = '.$rel_data['fund_id'])->delete();
			        }
			    }
				save_log($rel_data['name']."成功移到回收站",1);
				$this->success ("成功移到回收站",$ajax);
			} else {
				save_log($rel_data['name']."移到回收站出错",0);					
				$this->error ("移到回收站出错",$ajax);
			}
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}

	//投资详情
	public function relation_list()
	{	
		$id = intval($_REQUEST['fund_id']);
		
		$field = "deal.id,relation.id as rid,deal.name,deal.s_name,event.period,relation.investor_amount,relation.investor_date,relation.remark,relation.sort";
		$table = DB_PREFIX."deal_trade_fund_relation as relation,".DB_PREFIX."deal as deal,".DB_PREFIX."deal_trade_event as event";
		$map = "relation.fund_id = {$id} and relation.deal_trade_event_id = event.id and relation.deal_id = deal.id and deal.id = event.deal_id"; # and event.investor_record_type = 2 and deal.is_publish = 2 and deal.is_effect <> 1
        
        if(trim($_REQUEST['s_name']) != ''){
			$map .= " and deal.s_name like '%".$_REQUEST['s_name']."%' ";	
		}
		if(trim($_REQUEST['name']) != ''){
			$map .= " and deal.name like '%".$_REQUEST['name']."%' ";			
		}
		
		$list_sql = "select {$field} from {$table} where {$map}";
		$count_sql = "select count(1) from {$table} where {$map}";
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$result = $this->_list_multi_table($count_sql, $list_sql, $map,"relation.sort asc,relation.investor_amount desc,relation.investor_date ");

		//融资轮次
		$period = M('deal_period')->getField('id,name');
		foreach ($result as $k => $v){
			$result[$k]['investor_date'] = to_date($v['investor_date'],'Y-m-d');
			$result [$k] ['period'] = $period [$v ['period']];
			$result [$k] ['investor_amount'] = number_format ($v ['investor_amount']);
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
	
//------------------------------------- 基金披露信息相关 ------------------------------------------------

	function attachment_index(){
	    
	    $fid = (int)$_REQUEST['fid'];
	    if(empty($fid)){
	        $this->error("非法操作");
	    }
	    
	    $map['fund_id'] = $fid;
	    
	    if(trim($_REQUEST['title'])!=''){
	        $map['title'] = array('like','%'.trim($_REQUEST['title']).'%');
	    }
	    if(trim($_REQUEST['s_time'])!='' && trim($_REQUEST['e_time'])!=''){
	        $map['publish_time'] = array('BETWEEN',array(strtotime(trim($_REQUEST['s_time'])),strtotime(trim($_REQUEST['e_time']))));
	    }
	    if (method_exists ( $this, '_filter' )) {
	        $this->_filter ( $map );
	    }
	    
	    $map['is_del'] = 1;
	    $model = D ("fund_attachment");
	    
	    if (! empty ( $model )) {
	        $list=$this->_list ( $model, $map );
	        foreach($list as $k=>$v){
	            if($v['type'] == 1){
	                $tmp_href = getQiniuPath($v['attachment'], "img");
	            }elseif($v['type'] == 2){
	                $tmp_href = getQiniuPath($v['attachment'], "bp");
	            }
	            
	            $list[$k]['publish_time'] = date('Y-m-d',$v['publish_time']);
	            $list[$k]['button_list'] = '<a href="javascript:attach_edit('.$list[$k]['id'].')">编辑</a>&nbsp;<a href="javascript:attach_del('.$list[$k]['id'].')">删除</a>&nbsp;<a href="'.$tmp_href.'" target="_blank">查看附件</a>&nbsp;';
	        }
	        $this->assign("list",$list);
	    }
	    
	    $this->assign('fid', $fid);
	    $this->display ();
	}
	
	function attachment_add(){

	    $fid = (int)$_REQUEST['fid'];
	    if(empty($fid)){
	        $this->error("非法操作");
	    }
	    
	    // 生成披露信息
	    $max_id = M('fund_attachment')->max('id');
	    $max_id = generalBusinessCode('fund_attachment',$max_id + 1);
	    
	    $this->assign('code',$max_id);
	    $this->assign('fid', $fid);
	    $this->display();
	}
	
	function do_attachment_add(){
	    B('FilterString');
	    $data = M('fund_attachment')->create ();
	    
	    // 数据验证
	    if(!check_empty($data['fund_id'])){
	        $this->error("非法操作");
	    }
	    if(!check_empty($data['title'])){
	        $this->error("请输入信息标题");
	    }
	    if(utf8_strlen($data['title']) > 30){
	        $this->error("请确保信息标题在30个字以内");
	    }
	    if(!check_empty($data['attachment'])){
	        $this->error("请上传附件");
	    }
	    if(!check_empty($data['publish_time'])){
	        $this->error("请输入披露日期");
	    }
	    if(!check_empty($data['remark'])){
	        $this->error("请输入备注");
	    }
	    if(utf8_strlen($data['remark']) > 200){
	        $this->error("请确保备注200个字以内");
	    }
	    
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
	    
	    $data['publish_time'] = strtotime($data['publish_time']);
	    $data['create_time'] = time();

	    $re = M('fund_attachment')->add($data);
	    if($re){
	        $this->assign("jumpUrl",u(MODULE_NAME."/attachment_index",array('fid'=>$data['fund_id'])));
	        //成功提示
	        save_log('基金披露信息'.$re.L("INSERT_SUCCESS"),1);
	        $this->success(L("INSERT_SUCCESS"));
	    }else{
	        //错误提示
	        save_log('基金披露信息'.L("INSERT_FAILED"),0);
	        $this->error(L("INSERT_FAILED"));
	    }
	}
	
	function attachment_edit(){

	    $id = (int)$_REQUEST['id'];
	    
	    if(empty($id)){
	        $this->error("非法操作");
	    }
	    
	    $info = M('fund_attachment')->where('id='.$id)->find();
	    
	    if($info['is_del'] == 2){
	        $this->error("该基金披露信息已删除");
	    }
	    
	    if($info['type'] == 1){
	        $info['real_attachment'] = getQiniuPath($info['attachment'], "img");
	    }else{
	        $info['real_attachment'] = TEMPLATE_PATH."/Common/images/bp.png";
	    }
	    
	    $info['publish_time'] = date("Y-m-d",$info['publish_time']);
	    $this->assign('info',$info);
	    
	    $this->display();
	}
	
	function do_attachment_update(){
	    B('FilterString');
	    $data = M('fund_attachment')->create ();
	    
        // 数据验证
	    if(!check_empty($data['title'])){
	        $this->error("请输入信息标题");
	    }
	    if(utf8_strlen($data['title']) > 30){
	        $this->error("请确保信息标题在30个字以内");
	    }
	    if(!check_empty($data['attachment'])){
	        $this->error("请上传附件");
	    }
	    if(!check_empty($data['publish_time'])){
	        $this->error("请输入披露日期");
	    }
	    if(!check_empty($data['remark'])){
	        $this->error("请输入备注");
	    }
	    if(utf8_strlen($data['remark']) > 200){
	        $this->error("请确保备注200个字以内");
	    }
	    
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
	    
	    $data['publish_time'] = strtotime($data['publish_time']);
	    $data['update_time'] = time();

	    $re = M('fund_attachment')->save($data);
	    if($re){
	        //成功提示
	        save_log('基金披露信息'.$data['id'].L("UPDATE_SUCCESS"),1);
	        $this->success(L("UPDATE_SUCCESS"));
	    }else{
	        //错误提示
	        save_log('基金披露信息'.$data['id'].L("UPDATE_FAILED"),0);
	        $this->error(L("UPDATE_FAILED"));
	    }
	}
	
    function attachment_del(){

	    $id = (int)$_REQUEST['id'];
	    
	    if(empty($id)){
	        $this->error("非法操作",1);
	    }
	    
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data = array(
	        'is_del' => 2,
	        'update_time' => time(),
	        'operater' => $adm_session['adm_id'].'-'.$adm_session['adm_name'],
	    );
	    $re = M('fund_attachment')->where('id='.$id)->save($data);
	    if($re){
	        //成功提示
	        save_log('基金披露信息'.$id.L("DELETE_SUCCESS"),1);
	        $this->success(L("DELETE_SUCCESS"),1);
	    }else{
	        //错误提示
	        save_log('基金披露信息'.$id.L("DELETE_FAILED"),0);
	        $this->error(L("DELETE_FAILED"),1);
	    }
	}
	
//------------------------------------- 基金管理公司相关 ------------------------------------------------

	/*
	 * 基金管理公司列表
	 */
	function manageCo_index(){
	    
	    if(trim($_REQUEST['name'])!=''){
	        $map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
	    }
	    if(trim($_REQUEST['short_name'])!=''){
	        $map['short_name'] = array('like','%'.trim($_REQUEST['short_name']).'%');
	    }
	    if (method_exists ( $this, '_filter' )) {
	        $this->_filter ( $map );
	    }
	    
	    $map['is_del'] = 1;
	    $model = D ("fund_managers");
	    
	    if (! empty ( $model )) {
	        $list=$this->_list ( $model, $map );
	        foreach($list as $k=>$v){
	            $list[$k]['reg_found'] = number_format($v['reg_found'],2);
	            $list[$k]['com_time'] = date('Y-m-d',$v['com_time']);
	        }
	        $this->assign("list",$list);
	    }
	    
	    $this->display ();
	}
	
	/*
	 * 基金管理公司回收站列表
	 */
	function delete_manageCo(){
	    
	    if(trim($_REQUEST['name'])!=''){
	        $map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
	    }
	    if(trim($_REQUEST['short_name'])!=''){
	        $map['short_name'] = array('like','%'.trim($_REQUEST['short_name']).'%');
	    }
	    if (method_exists ( $this, '_filter' )) {
	        $this->_filter ( $map );
	    }
	    
	    $map['is_del'] = 2;
	    $model = D ("fund_managers");
	    
	    if (! empty ( $model )) {
	        $list=$this->_list ( $model, $map );
	        foreach($list as $k=>$v){
	            $list[$k]['reg_found'] = number_format($v['reg_found']);
	            $list[$k]['com_time'] = date('Y-m-d',$v['com_time']);
	        }
	        $this->assign("list",$list);
	    }
	    
	    $this->display ('manageCo_delete');
	}
	
	function manageCo_add(){

	    // 生成公司编码
	    $max_id = M('fund_managers')->max('id');
	    $max_id = generalBusinessCode('company',$max_id+1);
	    
	    $this->assign('code',$max_id);
	    $this->display();
	}
	
	function do_manageCo_add(){
	    B('FilterString');
	    $data = M('fund_managers')->create ();
	    
	    // 数据验证
	    if(!check_empty($data['name'])){
	        $this->error("请输入公司全称");
	    }
	    if(utf8_strlen($data['name']) > 50){
	        $this->error("请确保公司全称在50个字以内");
	    }
	    if(!check_empty($data['short_name'])){
	        $this->error("请输入公司简称");
	    }
	    if(utf8_strlen($data['short_name']) > 6){
	        $this->error("请确保公司简称在6个字以内");
	    }
	    if(!check_empty($data['legal_person'])){
	        $this->error("请输入法定代表人");
	    }
	    if(utf8_strlen($data['legal_person']) > 20){
	        $this->error("请确保法定代表人在20个字以内");
	    }
	    if(!check_empty($data['reg_found'])){
	        $this->error("请输入注册资本");
	    }
	    if(!check_empty($data['reg_found'])){
	        $this->error("请输入成立日期");
	    }
	    if(!check_empty($data['registration_no'])){
	        $this->error("请输入工商注册号");
	    }
	    if(utf8_strlen($data['registration_no']) > 20){
	        $this->error("请确保工商注册号20个字以内");
	    }
	    if(!check_empty($data['registration_address'])){
	        $this->error("请输入办公地址");
	    }
	    if(utf8_strlen($data['registration_address']) > 100){
	        $this->error("请确保办公地址100个字以内");
	    }
	    
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
	    
	    $data['com_time'] = strtotime($data['com_time']);
	    $data['create_time'] = time();

	    $re = M('fund_managers')->add($data);
	    if($re){
	        $this->assign("jumpUrl",u(MODULE_NAME."/manageCo_index"));
	        //成功提示
	        save_log('基金管理公司'.$re.L("INSERT_SUCCESS"),1);
	        $this->success(L("INSERT_SUCCESS"));
	    }else{
	        //错误提示
	        save_log('基金管理公司'.L("INSERT_FAILED"),0);
	        $this->error(L("INSERT_FAILED"));
	    }
	}
	
	function manageCo_edit(){

	    $id = (int)$_REQUEST['id'];
	    
	    if(empty($id)){
	        $this->error("非法操作");
	    }
	    
	    $info = M('fund_managers')->where('id='.$id)->find();
	    
	    if($info['is_del'] == 2){
	        $this->error("该基金管理公司已删除");
	    }
	    
	    $info['com_time'] = date("Y-m-d",$info['com_time']);
	    $this->assign('info',$info);
	    
	    $this->display();
	}
	
	function do_manageCo_update(){
	    B('FilterString');
	    $data = M('fund_managers')->create ();
	    
	    // 数据验证
	    if(!check_empty($data['id'])){
	        $this->error("非法操作");
	    }
	    if(!check_empty($data['name'])){
	        $this->error("请输入公司全称");
	    }
	    if(utf8_strlen($data['name']) > 50){
	        $this->error("请确保公司全称在50个字以内");
	    }
	    if(!check_empty($data['short_name'])){
	        $this->error("请输入公司简称");
	    }
	    if(utf8_strlen($data['short_name']) > 6){
	        $this->error("请确保公司简称在6个字以内");
	    }
	    if(!check_empty($data['legal_person'])){
	        $this->error("请输入法定代表人");
	    }
	    if(utf8_strlen($data['legal_person']) > 20){
	        $this->error("请确保法定代表人在20个字以内");
	    }
	    if(!check_empty($data['reg_found'])){
	        $this->error("请输入注册资本");
	    }
	    if(!check_empty($data['reg_found'])){
	        $this->error("请输入成立日期");
	    }
	    if(!check_empty($data['registration_no'])){
	        $this->error("请输入工商注册号");
	    }
	    if(utf8_strlen($data['registration_no']) > 20){
	        $this->error("请确保工商注册号13个字以内");
	    }
	    if(!check_empty($data['registration_address'])){
	        $this->error("请输入办公地址");
	    }
	    if(utf8_strlen($data['registration_address']) > 100){
	        $this->error("请确保办公地址100个字以内");
	    }
	    
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
	    
	    $data['com_time'] = strtotime($data['com_time']);
	    $data['update_time'] = time();

	    $re = M('fund_managers')->save($data);
	    if($re){
	        //成功提示
	        save_log('基金管理公司'.$data['id'].L("UPDATE_SUCCESS"),1);
	        $this->success(L("UPDATE_SUCCESS"));
	    }else{
	        //错误提示
	        save_log('基金管理公司'.$data['id'].L("UPDATE_FAILED"),0);
	        $this->error(L("UPDATE_FAILED"));
	    }
	}
	
    function manageCo_del(){

	    $id = (int)$_REQUEST['id'];
	    
	    if(empty($id)){
	        $this->error("非法操作",1);
	    }
	    
	    // 当该公司信息被基金管理界面的“基金管理人”引用时，是不允许删除的
	    $tmp = M('fund')->where('managers_id='.$id)->getField('short_name');
	    if($tmp){
	        $this->error("该公司已经被{$tmp}基金引用，不可以删除",1);
	    }
	    
	    // 基金公司团队、股份构成有数据时，是不允许删除的
	    if(M('fund_managers_team')->where('managers_id='.$id.' and is_del = 1')->find()){
	        $this->error("该公司的团队成员页面仍有数据，不可以删除",1);
	    }
	    if(M('fund_managers_share')->where('managers_id='.$id.' and is_del = 1')->find()){
	        $this->error("该公司的股份构成页面仍有数据，不可以删除",1);
	    }
	    
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data = array(
	        'is_del' => 2,
	        'update_time' => time(),
	        'operater' => $adm_session['adm_id'].'-'.$adm_session['adm_name'],
	    );
	    $re = M('fund_managers')->where('id='.$id)->save($data);
	    if($re){
	        //成功提示
	        save_log('基金管理公司'.$id.L("DELETE_SUCCESS"),1);
	        $this->success(L("DELETE_SUCCESS"),1);
	    }else{
	        //错误提示
	        save_log('基金管理公司'.$id.L("DELETE_FAILED"),0);
	        $this->error(L("DELETE_FAILED"),1);
	    }
	}
	
    function manageCo_restore(){

	    $id = (int)$_REQUEST['id'];
	    
	    if(empty($id)){
	        $this->error("非法操作",1);
	    }
	    
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data = array(
	        'is_del' => 1,
	        'update_time' => time(),
	        'operater' => $adm_session['adm_id'].'-'.$adm_session['adm_name'],
	    );
	    $re = M('fund_managers')->where('id='.$id)->save($data);
	    if($re){
	        //成功提示
	        save_log('基金管理公司'.$id.'成功移除回收站',1);
	        $this->success('成功移除回收站',1);
	    }else{
	        //错误提示
	        save_log('基金管理公司'.$id."移除回收站失败",0);
	        $this->error("移除回收站失败",1);
	    }
	}
	
//------------------------------------- 基金管理公司股份构成相关 ------------------------------------------------

	/*
	 * 基金管理公司股份构成列表
	 */
	function manageShare_index(){
	     
	    $mid = (int)$_REQUEST['mid'];
	    if(empty($mid)){
	        $this->error("非法操作");
	    }
	    
	    $map['managers_id'] = $mid;
	    $map['is_del'] = 1;
	    $model = D ("fund_managers_share");
	     
	    if (! empty ( $model )) {
	        $list = $this->_list ( $model, $map );
	        foreach($list as &$v){
	            $v['share'] = $v['share'].'%';
	        }
	        $this->assign("list",$list);
	    }
	     
	    $this->assign('mid',$mid);
	    $this->display ();
	}
	
	function manageShare_add(){
	
	    $mid = (int)$_REQUEST['mid'];
	    if(empty($mid)){
	        $this->error("非法操作");
	    }
	    
	    // 生成公司股份构成编码
	    $max_id = M('fund_managers_share')->max('id');
	    $max_id = generalBusinessCode('company_share',$max_id + 1);
	     
	    $this->assign('code',$max_id);
	    $this->assign('mid',$mid);
	    $this->display();
	}
	
	function do_manageShare_add(){
	    B('FilterString');
	    $data = M('fund_managers_share')->create ();
	    // 数据验证
	    if(!check_empty($data['managers_id'])){
	        $this->error("非法操作");
	    }
	    if(!check_empty($data['name'])){
	        $this->error("请输入股东名称");
	    }
	    if(utf8_strlen($data['name']) > 20){
	        $this->error("请确保股东名称在20个字以内");
	    }
	    if(!check_empty($data['share'])){
	        $this->error("请输入股份占比");
	    }
	    if(!check_empty($data['remark'])){
	        $this->error("请输入备注");
	    }
	    if(utf8_strlen($data['remark']) > 50){
	        $this->error("请确保备注在50个字以内");
	    }
	     
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
	    $data['create_time'] = time();
	
	    $re = M('fund_managers_share')->add($data);
	    if($re){
	        $this->assign("jumpUrl",u(MODULE_NAME."/manageShare_index",array('mid'=>$data['managers_id'])));
	        //成功提示
	        save_log('基金管理公司股份占比'.$re.L("INSERT_SUCCESS"),1);
	        $this->success(L("INSERT_SUCCESS"));
	    }else{
	        //错误提示
	        save_log('基金管理公司股份占比'.L("INSERT_FAILED"),0);
	        $this->error(L("INSERT_FAILED"));
	    }
	}
	
	function manageShare_edit(){
	
	    $id = (int)$_REQUEST['id'];
	     
	    if(empty($id)){
	        $this->error("非法操作");
	    }
	    
	    $info = M('fund_managers_share')->where('id='.$id)->find();
	     
	    if($info['is_del'] == 2){
	        $this->error("该基金管理公司股份构成已删除");
	    }
	    
	    $this->assign('info',$info);
	    $this->display();
	}
	
	function do_manageShare_update(){
	    B('FilterString');
	    $data = M('fund_managers_share')->create ();
	    // 数据验证
	    if(!check_empty($data['id'])){
	        $this->error("非法操作");
	    }
	    if(!check_empty($data['name'])){
	        $this->error("请输入股东名称");
	    }
	    if(utf8_strlen($data['name']) > 20){
	        $this->error("请确保股东名称在20个字以内");
	    }
	    if(!check_empty($data['share'])){
	        $this->error("请输入股份占比");
	    }
	    if(!check_empty($data['remark'])){
	        $this->error("请输入备注");
	    }
	    if(utf8_strlen($data['remark']) > 50){
	        $this->error("请确保备注在50个字以内");
	    }
	     
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
	     
	    $data['update_time'] = time();
	
	    $re = M('fund_managers_share')->save($data);
	    if($re){
	        //成功提示
	        save_log('基金管理公司股份占比'.$data['id'].L("UPDATE_SUCCESS"),1);
	        $this->success(L("UPDATE_SUCCESS"));
	    }else{
	        //错误提示
	        save_log('基金管理公司股份占比'.$data['id'].L("UPDATE_FAILED"),0);
	        $this->error(L("UPDATE_FAILED"));
	    }
	}
	
	function manageShare_del(){
	
	    $id = (int)$_REQUEST['id'];
	     
	    if(empty($id)){
	        $this->error("非法操作",1);
	    }
	     
// 	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
// 	    $data = array(
// 	        'is_del' => 2,
// 	        'update_time' => time(),
// 	        'operater' => $adm_session['adm_id'].'-'.$adm_session['adm_name'],
// 	    );
// 	    $re = M('fund_managers_share')->where('id='.$id)->save($data);
        // 直接删除数据
	    $re = M('fund_managers_share')->where('id='.$id)->delete();
	    if($re){
	        //成功提示
	        save_log('基金管理公司股份占比'.$id.L("DELETE_SUCCESS"),1);
	        $this->success(L("DELETE_SUCCESS"),1);
	    }else{
	        //错误提示
	        save_log('基金管理公司股份占比'.$id.L("DELETE_FAILED"),0);
	        $this->error(L("DELETE_FAILED"),1);
	    }
	}
	
//------------------------------------- 基金管理公司 管理团队 相关 ------------------------------------------------

	/*
	 * 基金管理公司管理团队列表
	 */
	function manageTeam_index(){
	     
	    $mid = (int)$_REQUEST['mid'];
	    if(empty($mid)){
	        $this->error("非法操作");
	    }
	    
	    $map = " and u.id = t.user_id and t.managers_id = {$mid}";
	     
	    if (trim($_REQUEST['title']) != '') {
	        $map .= " and t.title like '%{$_REQUEST['title']}%'";
	    }
	    if (trim($_REQUEST['name']) != '') {
	        $map .= " and u.user_name like '%{$_REQUEST['name']}%'";
	    }
	    
	    $map .= " and t.is_del = 1";
	    
	    $count_sql = "select count(1) from ".DB_PREFIX."user as u,".DB_PREFIX."fund_managers_team as t where 1 {$map}";
	    $list_sql = "select u.user_name as name,t.* from ".DB_PREFIX."user as u,".DB_PREFIX."fund_managers_team as t where 1 {$map} ";
	    $list = $this->_list_multi_table($count_sql, $list_sql, $map, 't.id ');
        foreach($list as &$v){
            $v['education_degree_cn'] = M('education_degree')->where('id='.$v['education_degree'])->getField('name');
        }
        $this->assign("list",$list);
	    $this->assign('mid',$mid);
	    $this->display ();
	}
	
	function manageTeam_add(){
	
	    $mid = (int)$_REQUEST['mid'];
	    if(empty($mid)){
	        $this->error("非法操作");
	    }
	    
	    // 生成管理团队构成编码
	    $max_id = M('fund_managers_team')->max('id');
	    $max_id = generalBusinessCode('company_team',$max_id + 1);
	    
	    // 查询用户学历
	    $education_degrees = M('education_degree')->findAll();
	    $this->assign("per_degree_list", $education_degrees);
	     
	    $this->assign('code',$max_id);
	    $this->assign('mid',$mid);
	    $this->display();
	}
	
	function manageTeam_add_user_search(){
	    
	    $map = " on u.id = e.user_id where 1";
	    
	    if (trim($_REQUEST['user_name']) != '') {
	        $map .= " and u.user_name like '%{$_REQUEST['user_name']}%'";
        }
	    if (trim($_REQUEST['org_title']) != '') {
	        $map .= " and e.org_title like '%{$_REQUEST['org_title']}%'";
        }
        
        $map .= " and u.user_type = 1";
        $map .= " and u.is_effect = 1";
        $map .= " and u.is_review = 1";
        
        $count_sql = "select count(1) from ".DB_PREFIX."user as u left join ".DB_PREFIX."user_ex_investor as e {$map}";
        $list_sql = "select u.id,u.mobile,u.user_name,e.org_title from ".DB_PREFIX."user as u left join ".DB_PREFIX."user_ex_investor as e {$map} ";
        $result = $this->_list_multi_table($count_sql, $list_sql, $map, 'u.id ');
        
        // cixi_fund_managers_team 是否存在
        if($result){
            $managers_id = (int)$_REQUEST['mid'];
            foreach($result as $k=>$v){
                $minfo = M('fund_managers_team')->where('managers_id='.$managers_id.' and user_id='.$v['id'])->find();
                $tmp = $minfo ? ($minfo['is_del'] == 1 ? 'has' : $minfo['id']) : 0;
                $result[$k]['button_list'] = '<a href="javascript:void(0)" onclick="mt_user_select('.$v['id'].',\''.$tmp.'\',this)">选择</a>';
            }
        }
        $this->assign("list", $result);
        $this->display();
	}
	
	function get_manageTeam_info_by_id(){
	    $id = (int)$_REQUEST['id'];
	    
	    if(empty($id)){
	        $this->error("非法操作",1);
	    }
	     
	    $info = M('fund_managers_team')->where('id='.$id)->find();
	    $info['user_name'] = M('user')->where('id='.$info['user_id'])->getField('user_name');
	    $info['real_logo'] = getQiniuPath($info['user_logo'], "img");
	     
	    $this->success($info,1);
	}
	
	function do_manageTeam_add(){
	    
	    B('FilterString');
	    $data = M('fund_managers_team')->create ();
	    
	    // 数据验证
	    if(!check_empty($data['managers_id']) || !check_empty($data['user_id'])){
	        $this->error("非法操作");
	    }
	    if(!check_empty($data['user_logo'])){
	        $this->error("请上传头像");
	    }
	    if(!check_empty($data['title'])){
	        $this->error("请输入职务名称");
	    }
	    if(utf8_strlen($data['title']) > 6){
	        $this->error("请确保职务名称在6个字以内");
	    }
	    if(!check_empty($data['graduate_university'])){
	        $this->error("请输入毕业院校");
	    }
	    if(utf8_strlen($data['graduate_university']) > 20){
	        $this->error("请确保毕业院校在20个字以内");
	    }
	    if(!check_empty($data['education_degree'])){
	        $this->error("请输入最高学历");
	    }
	    if(!check_empty($data['brief'])){
	        $this->error("请输入毕业院校");
	    }
	    if(utf8_strlen($data['brief']) > 200){
	        $this->error("请确保毕业院校在200个字以内");
	    }
	    
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
	    
	    
	    $id_exist = (int)$_REQUEST['id_exist'];
	    if($id_exist){
	        // 恢复用户，更新数据
	        $data['update_time'] = time();
	        $data['is_del'] = 1;
	        $re = M('fund_managers_team')->where('id='.$id_exist)->save($data);
	        $re = $re ? $id_exist : $re;
	    }else{
	        // 添加新数据
	        $data['create_time'] = time();
    	    $re = M('fund_managers_team')->add($data);
	    }
	    
	    if($re){
	        $this->assign("jumpUrl",u(MODULE_NAME."/manageTeam_index",array('mid'=>$data['managers_id'])));
	        //成功提示
	        save_log('基金管理公司团队成员'.$re.L("INSERT_SUCCESS"),1);
	        $this->success(L("INSERT_SUCCESS"));
	    }else{
	        //错误提示
	        save_log('基金管理公司团队成员'.L("INSERT_FAILED"),0);
	        $this->error(L("INSERT_FAILED"));
	    }
	}
	
	function manageTeam_edit(){
	
	    $id = (int)$_REQUEST['id'];
	     
	    if(empty($id)){
	        $this->error("非法操作");
	    }
	    
	    $info = M('fund_managers_team')->where('id='.$id)->find();
	    
	    if($info['is_del'] == 2){
	        $this->error("该基金管理公司团队成员已删除");
	    }
	    
	    $info['name'] = M('user')->where('id='.$info['user_id'])->getField('user_name');
	    $info['real_user_logo'] = getQiniuPath($info['user_logo'], "img");
	    
	    // 查询用户学历
	    $education_degrees = M('education_degree')->findAll();
	    $this->assign("per_degree_list", $education_degrees);
	    
	    $this->assign('info',$info);
	    $this->display();
	}
	
	function do_manageTeam_update(){
	    B('FilterString');
	    $data = M('fund_managers_team')->create ();
	    
	    // 数据验证
	    // 	    $this->assign("jumpUrl",u(MODULE_NAME."/manageCo_add"));
	    if(!check_empty($data['id'])){
	        $this->error("非法操作");
	    }
	    if(!check_empty($data['user_logo'])){
	        $this->error("请上传头像");
	    }
	    if(!check_empty($data['title'])){
	        $this->error("请输入职务名称");
	    }
	    if(utf8_strlen($data['title']) > 6){
	        $this->error("请确保职务名称在6个字以内");
	    }
	    if(!check_empty($data['graduate_university'])){
	        $this->error("请输入毕业院校");
	    }
	    if(utf8_strlen($data['graduate_university']) > 20){
	        $this->error("请确保毕业院校在20个字以内");
	    }
	    if(!check_empty($data['education_degree'])){
	        $this->error("请输入最高学历");
	    }
	    if(!check_empty($data['brief'])){
	        $this->error("请输入毕业院校");
	    }
	    if(utf8_strlen($data['brief']) > 200){
	        $this->error("请确保毕业院校在200个字以内");
	    }
	     
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
	     
	    $data['update_time'] = time();
	
	    $re = M('fund_managers_team')->save($data);
	    if($re){
	        //成功提示
	        save_log('基金管理公司团队成员'.$data['id'].L("UPDATE_SUCCESS"),1);
	        $this->success(L("UPDATE_SUCCESS"));
	    }else{
	        //错误提示
	        save_log('基金管理公司团队成员'.$data['id'].L("UPDATE_FAILED"),0);
	        $this->error(L("UPDATE_FAILED"));
	    }
	}
	
	function manageTeam_del(){
	
	    $id = (int)$_REQUEST['id'];
	     
	    if(empty($id)){
	        $this->error("非法操作",1);
	    }
	    
	    $use = M('user_fund_relation')->where('managers_team_id='.$id.' and user_type = 4')->find();
	    if($use){
	        $name = M('fund')->where('id='.$use['fund_id'])->getField('short_name');
	        $this->error("该成员在{$name}基金中的团队成员中有引用，不可删除！",1);
	    }
	    
	    $adm_session = es_session::get(md5(conf("AUTH_KEY")));
	    $data = array(
	        'is_del' => 2,
	        'update_time' => time(),
	        'operater' => $adm_session['adm_id'].'-'.$adm_session['adm_name'],
	    );
	    $re = M('fund_managers_team')->where('id='.$id)->save($data);
	    if($re){
	        //成功提示
	        save_log('基金管理公司团队成员'.$id.L("DELETE_SUCCESS"),1);
	        $this->success(L("DELETE_SUCCESS"),1);
	    }else{
	        //错误提示
	        save_log('基金管理公司团队成员'.$id.L("DELETE_FAILED"),0);
	        $this->error(L("DELETE_FAILED"),1);
	    }
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
}
?>