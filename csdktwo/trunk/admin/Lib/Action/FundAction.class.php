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
		if(trim($_REQUEST['period']) != '' && trim($_REQUEST['period']) != '0'){
			$map['fund_period'] = $_REQUEST['period'];
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
    	        $fund_period = C('FUND_PERIOD');
    	        foreach($list as $k=>$v){
    	            $status = $v['status'];
    	            if($status==1){
    	                $list[$k]['status']  = '未发布';
    	                $list[$k]['button_list']   = '<a href="javascript:edit('.$list[$k]['id'].')">编辑</a>&nbsp;<a href="javascript: del('.$list[$k]['id'].')">删除</a>';
    	            }elseif($status==2){
    	                $list[$k]['status']  = '已发布';
    	                $list[$k]['button_list']   = '<a href="javascript: revoke('.$list[$k]['id'].')">撤回</a>';
    	            }elseif($status==3){
    	                $list[$k]['status']  = '已撤回';
    	                $list[$k]['button_list']   = '<a href="javascript:edit('.$list[$k]['id'].')">编辑</a>&nbsp;<a href="javascript: del('.$list[$k]['id'].')">删除</a>';
    	            }
    	            
	                // 未发布，已撤回，显示发布按钮
    	            if(in_array($status,array(1,3))){
        	            $list[$k]['button_list'] .= '&nbsp;<a href="javascript: publish('.$list[$k]['id'].')">发布</a>';
    	            }
    	            
    	            // 投资详情
    	            $list[$k]['button_list'] .= '&nbsp;<a href="javascript: investment_details('.$list[$k]['id'].')">投资详情</a>';

    	            // 基金成立后，管理拟投项目，基金成立按钮不可见
    	            if($v['fund_period'] == 1){
        	            $list[$k]['button_list'] .= '&nbsp;<a href="javascript: fund_investor('.$list[$k]['id'].')">管理基金投资人</a>';
    	                
    	                // 基金是否还有没有处理的意向投资人信息
    	                $tmp = (int)M("fund_expectant_investor")->where("fund_id = {$v['id']} and actual_invest_confirm = 1")->getField("id");
    	                $list[$k]['button_list'] .= '&nbsp;<a href="javascript: manage_proposed_project('.$list[$k]['id'].')">管理拟投项目</a>&nbsp;<a href="javascript:fund_establishment('.$list[$k]['id'].','.$list[$k]['total_amount'].','.$tmp.')">基金成立</a>';
    	            }else{
        	            $list[$k]['button_list'] .= '&nbsp;<a href="javascript: fund_manage_investor('.$list[$k]['id'].')">管理基金投资人</a>';
    	            }
    	            
    	            // 信息披露，基金详情
    	            $list[$k]['button_list'] .= '&nbsp;<a href="javascript:attachment_index('.$list[$k]['id'].')">信息披露</a>&nbsp;<a href="javascript: fund_detail('.$list[$k]['id'].')">基金详情</a>';
    	            
    	            $list[$k]['fund_period']       = $fund_period[$v['fund_period']];
    	            $list[$k]['total_amount']      = number_format($v['total_amount']);
    	            $list[$k]['establish_date']    = date('Y-m-d',$v['establish_date']);
    	            $list[$k]['managers_id']       = M('fund_managers')->where('id='.$v['managers_id'])->getField('short_name');
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
		$this->assign("fund_period_list",C('FUND_PERIOD_LIST')); # 基金阶段列表
		$this->assign("list",$list);
		$this->display ();
	}
	
	/*
	 * 基金回收站列表
	 */
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
		if(trim($_REQUEST['period']) != '' && trim($_REQUEST['period']) != '0'){
		    $map['fund_period'] = $_REQUEST['period'];
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
    	    $map['is_csdk_fund']   = 1;//磁斯达克基金
    	    $map['is_delete']      = 0; // 只查删除的
    	    $name=$this->getActionName();
    	    $model = D ($name);
    	    
    	    if (! empty ( $model )) {
    	        $list=$this->_list ( $model, $map );
    	        $fund_period = C('FUND_PERIOD');
    	        foreach($list as $k=>$v){
    	            if($v['status']==1){
    	                $list[$k]['status_value'] = '未发布';
    	            }elseif($v['status']==2){
    	                $list[$k]['status_value'] = '已发布';
    	            }elseif($v['status']==3){
    	                $list[$k]['status_value'] = '已撤回';
    	            }
    	            $list[$k]['period_value']      = $fund_period[$v['fund_period']];
    	            $list[$k]['button_list']       = '<a href="javascript:fund_restore('.$list[$k]['id'].')">恢复</a>';
    	            $list[$k]['total_amount']      = number_format($v['total_amount']);
    	            $list[$k]['establish_date']    = date('Y-m-d',$v['establish_date']);
    	            $list[$k]['managers_id_cn']    = $v['managers_id'] ? M('fund_managers')->where('id='.$v['managers_id'])->getField('short_name') : '无';
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
		$this->assign("fund_period_list",C('FUND_PERIOD_LIST')); # 基金阶段列表
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
	    
	    $this->assign("fund_period_list",C('FUND_PERIOD_LIST')); # 基金阶段列表
		$this->display();
	}
	/*
	 * 基金增加页面->基金管理团队，查找基金管理公司中的团队成员方法
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
	 * 基金增加页面->基金管理团队，选择团队成员
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
	/*
	 * 添加基金->插入数据
	 */
	public function insert() {
	    
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']) || utf8_strlen($data['name'])>30)
		{
			$this->error("请输入基金名称并确保在30个字以内"); 
		}
		if(!check_empty($data['short_name']) || utf8_strlen($data['short_name'])>6)
		{
			$this->error("请输入基金简称并确保在6个字以内"); 
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
 		if(!check_empty($data['invest_min_amount']))
		{
			$this->error("请输入起投金额"); 
		}
		if(!check_empty($data['summary']))
		{
			$this->error("请输入基金简介"); 
		}
		
		$fund_period = (int)$data['fund_period'];
		if($fund_period == 0){
		    $this->error("请选择基金阶段");
		}
		
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		
		// 募资阶段数据处理
		if($fund_period == 1){
		    
		    $datas_raising = M("fund_raising")->create ();
		    
		    if(!check_empty($datas_raising['raising_start_date'])){
		        $this->error("请输入募集开始日期");
		    }
		    if(!check_empty($datas_raising['raising_end_date'])){
		        $this->error("请输入募集结束日期");
		    }
		    if(!check_empty($datas_raising['intend_profession']) || utf8_strlen($datas_raising['intend_profession'])>200){
		        $this->error("请输入拟投资行业并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['pace_of_invest']) || utf8_strlen($datas_raising['pace_of_invest'])>200){
		        $this->error("请输入出资节奏并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['manage_fee']) || utf8_strlen($datas_raising['manage_fee'])>200){
		        $this->error("请输入管理费并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['invest_type']) || utf8_strlen($datas_raising['invest_type'])>200){
		        $this->error("请输入投资方式并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['vote_type']) || utf8_strlen($datas_raising['vote_type'])>100){
		        $this->error("请输入投票方式并确保在100个字以内");
		    }
		    if(!check_empty($datas_raising['value_orientation']) || utf8_strlen($datas_raising['value_orientation'])>200){
		        $this->error("请输入价值与定位并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['profession_info']) && !check_empty($datas_raising['profession_app_img']) && !check_empty($datas_raising['profession_pc_img'])){
		        $this->error("请完善行业背景信息");
		    }
		    if(!check_empty($datas_raising['invest_philosophy']) && !check_empty($datas_raising['invest_philosophy_app_img']) && !check_empty($datas_raising['invest_philosophy_pc_img'])){
		        $this->error("请完善投资理念信息");
		    }
		    
		    $datas_raising['raising_start_date'] = strtotime($datas_raising['raising_start_date']);
		    $datas_raising['raising_end_date'] = strtotime($datas_raising['raising_end_date']);
		    $datas_raising['create_time'] = time();
		    $datas_raising['update_time'] = time();
		    $datas_raising['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
		    
		    $datas['fund_period'] = 1;
		}
		
		// 管理阶段数据处理
		if($fund_period == 2){
		    
		    if(!check_empty($data['fund_income_rate_director']) && !check_empty($data['fund_income_rate_partner']) && !check_empty($data['fund_income_rate_cisdaq']) && !check_empty($data['fund_income_rate_invester'])){
		        $this->error("请输入基金收益分配");
		    }
		    
		    $datas['fund_income_rate_director'] = $data['fund_income_rate_director'];
		    $datas['fund_income_rate_partner'] = $data['fund_income_rate_partner'];
		    $datas['fund_income_rate_cisdaq'] = $data['fund_income_rate_cisdaq'];
		    $datas['fund_income_rate_invester'] = $data['fund_income_rate_invester'];
		    $datas['fund_period'] = 2;
		}
		
		// 基础信息数据处理
		$datas['name'] = $data['name'];
		$datas['short_name'] = $data['short_name'];
		$datas['managers_id'] = $data['managers_id'];
		$datas['total_amount'] = $data['total_amount'];
		$datas['establish_date'] = strtotime($data['establish_date']);
		$datas['deadline'] = $data['deadline'];
		$datas['invest_min_amount'] = $data['invest_min_amount'];
		$datas['summary'] = $data['summary'];
		
		$datas['operator'] = $adm_session['adm_id'];
		$datas['update_time'] = time();
		$datas['is_delete'] = 1;
		$datas['status'] = 1;
		
		M()->startTrans();
		
 		// 添加基金基础信息 + 管理信息（若是管理阶段）
		$re1 = M("fund")->add($datas);
        if(!$re1){
		    M()->rollback();
		    // 错误提示
		    save_log("添加基金基础信息".L("INSERT_FAILED"),1);
		    $this->error("添加基金基础信息".L("INSERT_FAILED"));
		    exit();
		}
		
		// 添加team信息
		foreach($_REQUEST['managers_team_id'] as $k =>$v){
		    if(trim($v) != "" && trim($_REQUEST['position'][$k]) != '' && trim($_REQUEST['brief'][$k]) != ''){
		        $manage_team = array();
		        $manage_team['user_id'] = trim($_REQUEST['user_id'][$k]);
		        $manage_team['user_type'] = 4; #投资总监
		        $manage_team['fund_id'] = $re1;
		        $manage_team['managers_team_id'] = trim($v);
		        $manage_team['position'] = trim($_REQUEST['position'][$k]);
		        $manage_team['brief'] = trim($_REQUEST['brief'][$k]);
		        $manage_team['is_director'] = trim($_REQUEST['is_director'][$k]);
		        $manage_team['update_time'] = time();
		        $manage_team['operator'] = $adm_session['adm_id'];
		        $re2 = M("user_fund_relation")->add($manage_team);
		        if(!$re2){
		            M()->rollback();
		            // 错误提示
		            save_log("添加team信息".L("INSERT_FAILED"),1);
		            $this->error("添加team信息".L("INSERT_FAILED"));
		            exit();
		        }
		    }
		}
		
		// 添加基金募资信息（若是募资阶段）
		if($fund_period == 1){
		    $datas_raising['fund_id'] = $re1;
    		$re3 = M("fund_raising")->add($datas_raising);
    		if(!$re3){
    		    M()->rollback();
    		    // 错误提示
    		    save_log("添加基金募资信息".L("INSERT_FAILED"),1);
    		    $this->error("添加基金募资信息".L("INSERT_FAILED"));
    		    exit();
    		}
		}
		
		M()->commit();
		
		//成功提示
		save_log($log_info.L("INSERT_SUCCESS"),1);
		$this->success(L("INSERT_SUCCESS"));
	}
	/*
	 * 编辑页面
	 */
	public function edit() {	
		$id = intval($_REQUEST ['id']);
		
		// 基本信息
		$info = M("fund")->where("id = {$id}")->find();
		
		$fund_period_list = C('FUND_PERIOD');
		$info['establish_date'] = date("Y-m-d",$info['establish_date']);
		$info['max_payback'] = floatval($info['max_payback']);
		$info['average_payback'] = floatval($info['average_payback']);
		$info['total_payback'] = floatval($info['total_payback']);
		$info['fund_period_name'] = $fund_period_list[$info['fund_period']];
		
		// 基金管理团队
		$team_list = M("user_fund_relation")->where("fund_id=".$id." and user_type=4")->findAll();
		foreach($team_list as $k=>$v){
		    // 获取头像和名称
		    $mt = M('fund_managers_team')->where('id='.$v['managers_team_id'])->find();
		    $team_list[$k]['user_logo'] = getQiniuPath($mt['user_logo'],"img");
		    $team_list[$k]['name'] = M('user')->where('id='.$mt['user_id'])->getField('user_name');
		}
		$this->assign ( 'team_list', $team_list );
		
		// 募集信息
		if($info['fund_period'] == 1){
		    $info_raising = M("fund_raising")->where("fund_id = {$id}")->find();
		    $info_raising['raising_start_date'] = date("Y-m-d",$info_raising['raising_start_date']);
		    $info_raising['raising_end_date'] = date("Y-m-d",$info_raising['raising_end_date']);
		    $info = array_merge($info_raising, $info);
		}
		
		// 基金管理人
		$manages = M('fund_managers')->where('is_del = 1')->select();
		$this->assign('manages',$manages);
		
		$this->assign ( 'vo', $info );
		$this->display ();
	}

	/*
	 * 修改基金信息
	 */
    public function update() {
		
        B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();
		
		//开始验证有效性
		$fund_id = (int)$_REQUEST['fund_id_flag'];
		if(!$fund_id){
		    $this->error("无法获取基金ID");
		}
		
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$fund_id)));
		
		if(!check_empty($data['name']) || utf8_strlen($data['name'])>30)
		{
			$this->error("请输入基金名称并确保在30个字以内"); 
		}
		if(!check_empty($data['short_name']) || utf8_strlen($data['short_name'])>6)
		{
			$this->error("请输入基金简称并确保在6个字以内"); 
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
 		if(!check_empty($data['invest_min_amount']))
		{
			$this->error("请输入起投金额"); 
		}
		if(!check_empty($data['summary']))
		{
			$this->error("请输入基金简介"); 
		}

		$fund_period = (int)$data['fund_period'];
		if($fund_period == 0){
		    $this->error("请选择基金阶段");
		}
		
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		
		// 募资阶段数据处理
		if($fund_period == 1){
		
		    $datas_raising = M("fund_raising")->create ();
		
		    if(!check_empty($datas_raising['raising_start_date'])){
		        $this->error("请输入募集开始日期");
		    }
		    if(!check_empty($datas_raising['raising_end_date'])){
		        $this->error("请输入募集结束日期");
		    }
		    if(!check_empty($datas_raising['intend_profession']) || utf8_strlen($datas_raising['intend_profession'])>200){
		        $this->error("请输入拟投资行业并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['pace_of_invest']) || utf8_strlen($datas_raising['pace_of_invest'])>200){
		        $this->error("请输入出资节奏并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['manage_fee']) || utf8_strlen($datas_raising['manage_fee'])>200){
		        $this->error("请输入管理费并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['invest_type']) || utf8_strlen($datas_raising['invest_type'])>200){
		        $this->error("请输入投资方式并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['vote_type']) || utf8_strlen($datas_raising['vote_type'])>100){
		        $this->error("请输入投票方式并确保在100个字以内");
		    }
		    if(!check_empty($datas_raising['value_orientation']) || utf8_strlen($datas_raising['value_orientation'])>200){
		        $this->error("请输入价值与定位并确保在200个字以内");
		    }
		    if(!check_empty($datas_raising['profession_info']) && !check_empty($datas_raising['profession_app_img']) && !check_empty($datas_raising['profession_pc_img'])){
		        $this->error("请完善行业背景信息");
		    }
		    if(!check_empty($datas_raising['invest_philosophy']) && !check_empty($datas_raising['invest_philosophy_app_img']) && !check_empty($datas_raising['invest_philosophy_pc_img'])){
		        $this->error("请完善投资理念信息");
		    }
		
		    $datas_raising['raising_start_date'] = strtotime($datas_raising['raising_start_date']);
		    $datas_raising['raising_end_date'] = strtotime($datas_raising['raising_end_date']);
		    $datas_raising['update_time'] = time();
		    $datas_raising['operater'] = $adm_session['adm_id'].'-'.$adm_session['adm_name'];;
		
// 		    $datas['fund_period'] = 1;
		}
	
		// 管理阶段数据处理
		if($fund_period == 2){
		
		    if(!check_empty($data['fund_income_rate_director']) && !check_empty($data['fund_income_rate_partner']) && !check_empty($data['fund_income_rate_cisdaq']) && !check_empty($data['fund_income_rate_invester'])){
		        $this->error("请输入基金收益分配");
		    }
		
		    $datas['fund_income_rate_director'] = $data['fund_income_rate_director'];
		    $datas['fund_income_rate_partner'] = $data['fund_income_rate_partner'];
		    $datas['fund_income_rate_cisdaq'] = $data['fund_income_rate_cisdaq'];
		    $datas['fund_income_rate_invester'] = $data['fund_income_rate_invester'];
// 		    $datas['fund_period'] = 2;
		}
		
		// 基础信息数据处理
		$datas['name'] = $data['name'];
		$datas['short_name'] = $data['short_name'];
		$datas['managers_id'] = $data['managers_id'];
		$datas['total_amount'] = $data['total_amount'];
		$datas['establish_date'] = strtotime($data['establish_date']);
		$datas['deadline'] = $data['deadline'];
		$datas['invest_min_amount'] = $data['invest_min_amount'];
		$datas['summary'] = $data['summary'];
		
		$datas['operator'] = $adm_session['adm_id'];
		$datas['update_time'] = time();
		
		M()->startTrans();
		
		// 更新基金基础信息 + 管理信息（若是管理阶段）
		$re1 = M("fund")->where('id = '.$fund_id)->save($datas);
		if(false === $re1){
		    M()->rollback();
		    // 错误提示
		    save_log("更新基金基础信息ID:{$fund_id}".L("UPDATE_FAILED"),1);
		    $this->error("更新基金基础信息ID:{$fund_id}".L("UPDATE_FAILED"));
		    exit();
		}
		
		// 更新team信息
		M("user_fund_relation")->where('fund_id='.$fund_id." and user_type = 4")->delete();
		foreach($_REQUEST['managers_team_id'] as $k =>$v){
		    if(trim($v) != "" && trim($_REQUEST['position'][$k]) != '' && trim($_REQUEST['brief'][$k]) != ''){
		        $manage_team = array();
		        $manage_team['user_id'] = trim($_REQUEST['user_id'][$k]);
		        $manage_team['user_type'] = 4; #投资总监
		        $manage_team['fund_id'] = $fund_id;
		        $manage_team['managers_team_id'] = trim($v);
		        $manage_team['position'] = trim($_REQUEST['position'][$k]);
		        $manage_team['brief'] = trim($_REQUEST['brief'][$k]);
		        $manage_team['is_director'] = trim($_REQUEST['is_director'][$k]);
		        $manage_team['update_time'] = time();
		        $manage_team['operator'] = $adm_session['adm_id'];
		        $re2 = M("user_fund_relation")->add($manage_team);
		        if(!$re2){
		            M()->rollback();
		            // 错误提示
		            save_log("更新基金ID:{$fund_id}，team信息".L("UPDATE_FAILED"),1);
		            $this->error("更新基金ID:{$fund_id}，team信息".L("UPDATE_FAILED"));
		            exit();
		        }
		    }
		}
		
		// 更新基金募资信息（若是募资阶段）
		if($fund_period == 1){
		    $re3 = M("fund_raising")->where('fund_id = '.$fund_id)->save($datas_raising);
		    if(false === $re3){
		        M()->rollback();
		        // 错误提示
		        save_log("更新基金ID:{$fund_id}募资信息".L("UPDATE_FAILED"),1);
		        $this->error("更新基金ID:{$fund_id}募资信息".L("UPDATE_FAILED"));
		        exit();
		    }
		    // 版本加1
		    $re4 = M("fund_raising")->where('fund_id = '.$fund_id)->setInc("version");
		}
		
		M()->commit();
		
		//成功提示
		save_log("更新基金ID:{$fund_id}".L("UPDATE_SUCCESS"),1);
		$this->success(L("UPDATE_SUCCESS"));
    }

    /*
     * 基金详情
     */
    public function fund_detail() {
		$id = intval($_REQUEST ['id']);
		
		// 基本信息
		$info = M("fund")->where("id = {$id}")->find();
		
		$info['establish_date'] = date("Y-m-d",$info['establish_date']);
		$info['max_payback'] = floatval($info['max_payback']);
		$info['average_payback'] = floatval($info['average_payback']);
		$info['total_payback'] = floatval($info['total_payback']);
		
		// 基金管理团队
		$team_list = M("user_fund_relation")->where("fund_id=".$id." and user_type=4")->findAll();
		foreach($team_list as $k=>$v){
		    // 获取头像和名称
		    $mt = M('fund_managers_team')->where('id='.$v['managers_team_id'])->find();
		    $team_list[$k]['user_logo'] = getQiniuPath($mt['user_logo'],"img");
		    $team_list[$k]['name'] = M('user')->where('id='.$mt['user_id'])->getField('user_name');
		}
		$this->assign ( 'team_list', $team_list );
		
		// 募集信息
	    $info_raising = M("fund_raising")->where("fund_id = {$id}")->find();
		if($info_raising){
		    $info_raising['raising_start_date'] = date("Y-m-d",$info_raising['raising_start_date']);
		    $info_raising['raising_end_date'] = date("Y-m-d",$info_raising['raising_end_date']);
		    $info = array_merge($info_raising, $info);
		}
		
		// 基金管理人
		$manages = M('fund_managers')->where('is_del = 1')->select();
		$this->assign('manages',$manages);
		
		$this->assign ( 'vo', $info );
		$this->assign("fund_period_list",C('FUND_PERIOD_LIST')); # 基金阶段列表
		$this->display ();
    }
    
    /*
     * 基金发布，撤回
     */
    public function update_fund_status() {
		//更新状态
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		$status =  $_REQUEST ['status'];

		if (isset ( $id )) {
		    
		    if($status == 2){
        		// 维护了 基金投资人（管理阶段） or 基金拟投资项目关联信息（募集阶段的基金） 后 【发布】按钮可用
        		$fund_period = M('fund')->where('id = '.$id)->getField('fund_period');
        	    if($fund_period == 1){
        	        $tmp = M('fund_expectant_deal')->where('fund_id='.$id)->find();
        	        if(empty($tmp)){
        	            $this->error ("该基金尚没有拟投资项目关联信息，不可发布！",$ajax);
        	        }
        	    }
        	    if($fund_period == 2){
        	        $tmp = M('user_fund_relation')->where('fund_id='.$id.' and user_type = 1')->find();
        	        if(empty($tmp)){
        	            $this->error ("该基金尚没有投资人数据，不可发布！",$ajax);
        	        }
        	    }
		    }
    	    
			$list = M(MODULE_NAME)->where( "id = {$id}" )->setField("status",$status);		
			if ($list!==false) {
				save_log("更新基金{$id}发布状态成功",1);
				$this->success ("更新基金发布状态成功",$ajax);
			} else {
				save_log("更新基金{$id}发布状态出错",0);					
				$this->error ("更新基金发布状态出错",$ajax);
			}
		} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
    /*
     * 基金成立
     */
	public function fund_establishment_check(){
	    //更新状态
	    $ajax = intval($_REQUEST['ajax']);
	    $id = $_REQUEST ['id'];
	    
	    if (isset ( $id )) {
	        // 基金成立的前提条件是拟投资项目及投资人都有数据
	        $tmp = M('fund_expectant_deal')->where('fund_id='.$id)->find();
	        if(empty($tmp)){
	            $this->error ("该基金尚无拟投资项目，请先维护拟投资项目！",$ajax);
	        }
	         
	        $tmp = M('user_fund_relation')->where('fund_id='.$id.' and user_type = 1')->find();
	        if(empty($tmp)){
	            $this->error ("该基金尚无投资人，请先维护投资人！",$ajax);
	        }
	        
	        $this->success ("基金成立操作检测通关",$ajax);
	        
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
	}
    public function fund_establishment() {
		//更新状态
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];

		if (isset ( $id )) {
			$list = M(MODULE_NAME)->where( "id = {$id}" )->setField("fund_period",2);		
			if ($list !== false) {
				save_log("更新基金{$id}成立状态成功",1);
				$this->success ("更新基金{$id}成立状态成功",$ajax);
			} else {
				save_log("更新基金{$id}成立状态出错",0);					
				$this->error ("更新基金{$id}成立状态出错",$ajax);
			}
		} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}

//------------------------------------- 基金拟投项目相关 START------------------------------------------------
	
	/*
	 * 基金拟投项目列表 cixi_fund_expectant_deal
	 */
	public function manage_proposed_project(){
	    
	    $fund_id = intval($_REQUEST['id']);
	    
	    $field = "d.id as deal_id,d.name,d.s_name,d.is_effect,d.is_publish,e.pe_amount_plan,e.pe_sell_scale,e.period,p.id";
	    $map   = "p.fund_id = {$fund_id}";
	    
	    if(trim($_REQUEST['s_name']) != ''){
	        $map .= " and d.s_name like '%".$_REQUEST['s_name']."%' ";
	    }
	    if(trim($_REQUEST['name']) != ''){
	        $map .= " and d.name like '%".$_REQUEST['name']."%' ";
	    }
	    
	    $map .= " and p.deal_id = d.id and d.id = e.deal_id and e.investor_record_type = 1";
	    
	    $list_sql = "select {$field} "
	               ." from ".DB_PREFIX."fund_expectant_deal as p,".DB_PREFIX."deal as d,".DB_PREFIX."deal_trade_event as e "
	               ."where {$map} ORDER BY p.id desc ";
	        	
	    $count_sql = "select count(1) "
	               ." from ".DB_PREFIX."fund_expectant_deal as p,".DB_PREFIX."deal as d,".DB_PREFIX."deal_trade_event as e "
	               ."where {$map}";
	                	
        // 获取数据结果
        $result = $this->_list_multi_table($count_sql, $list_sql, $map);
        $fund   = M("fund")->field('short_name,fund_period,status')->where("id = ".$fund_id)->find();
        if($result){
            $period = M('deal_period')->getField('id,name');
            $is_publish = array(1 => "未发布",2 => "已发布");
            foreach($result as &$v){
                $v['period_cn']     = $period[$v['period']];
                $v['is_effect_cn']  = "拟投资";
                $v['is_publish_cn'] = $is_publish[$v['is_publish']];
                if($fund['fund_period'] == 2){
                    $v['button_list'] .= '<a href="javascript: mpp_del('.$v['id'].',0,\''.$fund['short_name'].'\')">删除</a>';
                }else{
                    $v['button_list'] .= '<a href="javascript: mpp_del('.$v['id'].',1)">删除</a>';
                }
            }
            unset($v);
        }
        
        $this->assign("list", $result);
        $this->assign("fund_id", $fund_id);
        $this->assign("fund", $fund);
	    $this->display("managePp_index");
	}
	
	/*
	 * 添加页面
	 */
	public function pp_add_search_deal(){
	    
	    $fund_id = intval($_REQUEST['fid']);
	    
	    $field = "id,name,s_name";
	    $map   = array();
	    if(trim($_REQUEST['name']) != ''){
	        $map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
	    }
	    
	    // 已选择的项目
	    $ids = M('fund_expectant_deal')->where('fund_id = '.$fund_id)->getField('id,deal_id');
	    if($ids){
    	    $map['id'] = array('not in',$ids);
	    }
	    
	    if (method_exists ( $this, '_filter' )) {
	        $this->_filter ( $map );
	    }
	     
	    $map['is_effect']  = 2;    #拟投
	    $map['is_publish'] = 2;    #已发布
	    
	    $model = D ("deal");
        $list=$this->_list ( $model, $map );
        if($list){
            foreach($list as &$v){
                $v['is_effect_cn']  = "拟投资";
                $v['is_publish_cn'] = "已发布";
                $v['mpp_deal_select'] = '<a href="javascript:void(0)" onclick="javascript: mpp_deal_select('.$v['id'].',this)">选择</a>';
            }
            unset($v);
        }
//         dump($model->getLastSql());
        $this->assign("list",$list);
	    $this->display ("managePp_add_search");
	}
	
	/*
	 * 添加
	 */
	public function do_pp_add(){
	    
	    $fund_id = intval($_REQUEST['fid']);
	    $deal_id = intval($_REQUEST['deal_id']);
	    
	    if(empty($fund_id) || empty($deal_id)){
	        $this->error(l("INVALID_OPERATION"),1);
	    }
	    
	    
	    $adm_session           = es_session::get(md5(conf("AUTH_KEY")));
	    $data['operater']      = $adm_session['adm_id'].'-'.$adm_session['adm_name'];
	    $data['create_time']   = time();
	    $data['fund_id']       = $fund_id;
	    $data['deal_id']       = $deal_id;
	    
	    $re = M('fund_expectant_deal')->add($data);
	    if($re){
	        $this->success("成功",1);
	    }else{
	        $this->error("失败",1);
	    }
	}
	
	/*
	 * 删除
	 */
	public function pp_delete(){
	    $id = intval($_REQUEST['id']);
	     
	    if(empty($id)){
	        $this->error(l("INVALID_OPERATION"),1);
	    }
	    
	    $fund_id   = M("fund_expectant_deal")->where("id = {$id}")->getField("fund_id");
	    $fund      = M("fund")->field('short_name,fund_period,status')->where("id = ".$fund_id)->find();
	    // 基金成立，不可以删除
	    if($fund['fund_period'] == 2){
	        $this->error("基金【{$fund['short_name']}】已经成立，不可以删除该项目！",1);
	    }
	    // 基金发布状态，不可以删除
	    if($fund['status'] == 2){
	        $this->error("基金【{$fund['short_name']}】已经发布，不可以进行删除操作！",1);
	    }
	    // 如果拟投项目只剩下一个了，也不能继续删除
	    if(M("fund_expectant_deal")->where("fund_id = {$fund_id}")->count() == 1){
	        $this->error("基金【{$fund['short_name']}】必须有一个拟投资项目存在，不可以进行删除操作！",1);
	    }
	    
	    $re = M('fund_expectant_deal')->where('id = '.$id)->delete();
	    if($re){
	        $this->success("成功",1);
	    }else{
	        $this->error("失败",1);
	    }
	    
	}
//------------------------------------- 基金拟投项目相关 END------------------------------------------------
	
	
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
	
	/*
	 * 意向投资人列表
	 */
	public function investorintention_list(){
		$id = intval($_REQUEST['fund_id']);
		
		$stateMap = array(
            1 => '待处理',
            3 => '已通过',
            2 => '已驳回'
        );
        $confirmMap = array(
            1 => '未确认',
            2 => '已确认'
        );
        //审核状态枚举值
         $status_type = array(
            array(
                'id' => 1,
                'name' => '待处理'
            ),
            array(
                'id' => 2,
                'name' => '已驳回'
            ),
            array(
                'id' => 3,
                'name' => '已通过'
            )
        );
        $field = "b.id as id,mobile ,expect_invest_amount, b.status as status ,expect_invest_remark,actual_invest_amount,actual_invest_remark,actual_invest_confirm,b.status as status,user_name,c.id as fund_id";
		$map = "a.id=b.user_id and b.fund_id={$id} and c.id=b.fund_id  ";
		if(trim($_REQUEST['name']) != ''){
		    $map .= " and user_name like '%".$_REQUEST['name']."%' ";			
		}
		if(trim($_REQUEST['mobile']) != ''){
		  $map .= " and mobile like '%".$_REQUEST['mobile']."%' ";		
		}
		if(trim($_REQUEST['status']) != ''){
		  $map .= " and b.status = ".$_REQUEST['status']." ";		
		}
		$list_sql = "select {$field} "
		             ." from ".DB_PREFIX."user as a,".DB_PREFIX."fund_expectant_investor as b,".DB_PREFIX."fund as c "
	                 ."where {$map} ORDER BY b.id asc ";
		$count_sql = "select count(1) "
					 ." from ".DB_PREFIX."user as a,".DB_PREFIX."fund_expectant_investor as b,".DB_PREFIX."fund as c "
					." where {$map} ORDER BY b.id asc ";
		
		// 获取数据结果
		$result = $this->_list_multi_table($count_sql, $list_sql, $map);
		if($result){
		    
		    // 获取基金阶段 + 查询基金起投金额
		    $fund_info            = M("fund")->where("id=" . $id)->find();
		    $invest_min_amount    = $fund_info['invest_min_amount'];
		    $fund_period          = $fund_info['fund_period'];
		    
		    foreach ($result as $k => $v) {
		    
		        // 管理阶段，不可修改数据，按钮不可用
		        if($fund_period == 2){
		            if ($v['status'] == 1) {
		                $result[$k]['fund_button'] = '<span style="color:#ccc">审核通过&nbsp;审核驳回&nbsp;股份确认</span>';
		            } elseif ($v['status'] == 3) {
		                $result[$k]['fund_button'] = '<span style="color:#ccc">审核驳回&nbsp;股份确认</span>';
		            } elseif ($v['status'] == 2) {
		                $result[$k]['fund_button'] = '<span style="color:#ccc">审核通过&nbsp;股份确认</span>';
		            }
		        }else{
		    
		            // 募集阶段，股份确认后，不可修改数据，按钮不可用
		            if($v['actual_invest_confirm'] == 2){
    		            if ($v['status'] == 1) {
    		                $result[$k]['fund_button'] = '<span style="color:#ccc">审核通过&nbsp;审核驳回&nbsp;股份确认</span>';
    		            } elseif ($v['status'] == 3) {
    		                $result[$k]['fund_button'] = '<span style="color:#ccc">审核驳回&nbsp;股份确认</span>';
    		            } elseif ($v['status'] == 2) {
    		                $result[$k]['fund_button'] = '<span style="color:#ccc">审核通过&nbsp;股份确认</span>';
    		            }
		            }else{
		                if ($v['status'] == 1) {
		                    $result[$k]['fund_button'] = '<a href="javascript:intention_pass(' . $result[$k]['id'] . ')">审核通过</a>&nbsp;<a href="javascript: intention_rejected(' . $result[$k]['id'] . ')">审核驳回</a>&nbsp;<a href="javascript:intention_confirm(' . $result[$k]['id'] . ')">股份确认</a>';
		                } elseif ($v['status'] == 3) {
		                    $result[$k]['fund_button'] = '<a href="javascript: intention_rejected(' . $result[$k]['id'] . ')">审核驳回</a>&nbsp;<a href="javascript:intention_confirm(' . $result[$k]['id'] . ')">股份确认</a>';
		                } elseif ($v['status'] == 2) {
		                    $result[$k]['fund_button'] = '<a href="javascript:intention_pass(' . $result[$k]['id'] . ')">审核通过</a>&nbsp;<a href="javascript:intention_confirm(' . $result[$k]['id'] . ')">股份确认</a>';
		                }
		              
		                // 处理可编辑区域数据格式
		                $v['actual_invest_remark'] = $v['actual_invest_remark'] == "" ? "暂无备注信息" : $v['actual_invest_remark'];
		                $result[$k]['actual_invest_amount'] = "<span class='actual_invest_amount_span' onclick='set_actual_invest_amount(".$v['id'].",".$v['actual_invest_amount'].",".$invest_min_amount.",this);'>".$v['actual_invest_amount']."</span>";
		                $result[$k]['actual_invest_remark'] = "<span class='actual_invest_remark_span' onclick='set_actual_invest_remark(".$v['id'].",\"".$v['actual_invest_remark']."\",this);'>".$v['actual_invest_remark']."</span>";
		            }
		        }
		    
		        // 状态值转换成中文
		        $result[$k]['status'] = $stateMap[$v['status']];
		        $result[$k]['actual_invest_confirm'] = $confirmMap[$v['actual_invest_confirm']];
		    }
		    
		}
        
		// 数据统计
        $listcount = $GLOBALS['db']->getRow("select  b.id as id,mobile ,sum(expect_invest_amount) as sumexpect,sum(actual_invest_amount) as sumactual,expect_invest_amount,expect_invest_remark,actual_invest_amount,actual_invest_remark,actual_invest_confirm,b.status as status,user_name,c.id as fund_id from cixi_user as a,cixi_fund_expectant_investor as b,cixi_fund as c where a.id=b.user_id and c.id=b.fund_id  and b.fund_id={$id} ORDER BY id asc");
        $this->assign("status_type",$status_type);
        $this->assign("list",$result);
        $this->assign("listcount",$listcount );
        $this->assign("fund_id",$id);
        $this->display('investorintention_list');
    }
    
    /*
     * 意向投资人列表->修改实际认购金融
     */
    public function set_actual_invest_amount() {
        $id = intval($_REQUEST['id']);
        $actual_invest_amount = intval($_REQUEST['actual_invest_amount']);
        
        //起投金额
        $invest_min_amount = M("fund")->where("id=" . $id)->getField("invest_min_amount"); 
        if($actual_invest_amount < $invest_min_amount || $actual_invest_amount > 9990000){
			$this->error("参数错误,{$invest_min_amount}万 <= 实际认购金额 <= 999亿", 1);
        }
        
        $re = M("fund_expectant_investor")->where("id=" . $id)->setField("actual_invest_amount",$actual_invest_amount);
        if(false === $re){
            $this->error(l("修改失败"), 1);
        }else{
            save_log("意向投资人列表->修改实际认购金额" . l("修改成功"), 1);
            $this->success(l("修改成功"), 1);
        }
    }
    /*
     * 意向投资人列表->修改备注 
     */
    public function set_actual_invest_remark(){
        $id = intval($_REQUEST['id']);
        $actual_invest_remark = trim($_REQUEST['actual_invest_remark']);
        if(!check_empty($actual_invest_remark) || utf8_strlen($actual_invest_remark) > 100){
	        $this->error("请输入备注信息并确保在100个字以内");
	    }
	    
        $re = M("fund_expectant_investor")->where("id=" . $id)->setField("actual_invest_remark", $actual_invest_remark);
        if(false === $re){
            $this->error(l("修改失败"), 1);
        }else{
            save_log("意向投资人列表->修改备注" . l("修改成功"), 1);
            $this->success(l("修改成功"), 1);
        }
    }
    /*
     * 意向投资人列表->审核通过
     */
    public function intention_pass(){
        $id = intval($_REQUEST['id']);
        $list = M("fund_expectant_investor")->where("id = {$id}")->setField("status",3);
        if (false !== $list) {
            // 查找用户信息
            $intention  = M("fund_expectant_investor")->where("id = {$id}")->find();
            $user_info  = M("user")->where("id = {$intention['user_id']}")->find();
            
            // 当该意向投资人不是正式会员时，在处理状态变为【已通过】时应当将该会员的状态值为“已认证会员”；
            if(in_array($user_info['is_review'],array(0,2)) && $user_info['is_effect'] == 1){
                M("user")->where("id = {$intention['user_id']}")->setField("is_review",1);
                
                $msg = getSendSmsTemplate("admin_user_audit_ok");
                
                // 发送站内信
                $user_notify['user_id']     = $user_info['id'];
                $user_notify['log_info']    = $msg;
                $user_notify['url']         = "";
                $user_notify['log_time']    = time();
                $user_notify['is_read']     = 0;
                M("user_notify")->add($user_notify);
                
                // 发送短信
                $params = array(
                    "mobile"    => $user_info['mobile'],
                    "content"   => $msg,
                    "type"      => getSendSmsType("admin_intention_list_cert"),
                );
                request_service_api("Common.Sms.sendSms",$params);
                
            }
            
            // 发送短信
            $fname  = M("fund")->where("id = {$intention['fund_id']}")->getField("short_name");
    		$msg    = getSendSmsTemplate("admin_intention_list_pass",array($fname));
    		$params = array(
    		    "mobile"    => $user_info['mobile'],
    		    "content"   => $msg,
    		    "type"      => getSendSmsType("admin_intention_list_pass"),
    		);
    		$result = request_service_api("Common.Sms.sendSms",$params);
    		
    		// 发送站内信
    		$user_notify['user_id']     = $user_info['id'];
    		$user_notify['log_info']    = $msg;
    		$user_notify['url']         = "";
    		$user_notify['log_time']    = time();
    		$user_notify['is_read']     = 0;
    		M("user_notify")->add($user_notify);
            
            // 成功提示
            save_log($log_info . L("SUCCESS"), 1);
            $this->success(L("审核已经通过"));
        } else {
            // 错误提示
            save_log($log_info . L("FAILED"), 0);
            $this->error(L("处理失败"), 0, $log_info . L("FAILED"));
        }
    }
    
    /*
     * 股份确认检验
     */
     public function intention_confirm_check(){
         
        $id = intval($_REQUEST['id']);
        
        $fund_expectant_investor = M("fund_expectant_investor")->where("id = {$id}")->find();
        if($fund_expectant_investor['actual_invest_confirm'] == 2){
            $this->error("基金份额已经确认无需再次确认！", 1);
        }
        if($fund_expectant_investor['status'] != 3){
            $this->error("审核未通过，无法进行确认！", 1);
        }
        
        // 实际金额是否满足
        $invest_min_amount = M("fund")->where('id='.$fund_expectant_investor['fund_id'])->getField("invest_min_amount");
        if ($fund_expectant_investor['actual_invest_amount'] < $invest_min_amount) {
            $this->error("实际认购金额应>=起投金额[".$invest_min_amount."万]，请修改实际认购金额！", 1);
        }
        
        // 查询该意向投资人是否已存在（投资人）
        $tmp    = M("user_fund_relation")->where("user_id = {$fund_expectant_investor['user_id']} and user_type = 1 and fund_id = {$fund_expectant_investor['fund_id']}")->getField("id");
        $name   = M("user")->where("id = {$fund_expectant_investor['user_id']}")->getField("user_name");
        $this->success(array('info'=>l("success"),'investor'=>(int)$tmp,'actual_invest_amount'=>$fund_expectant_investor['actual_invest_amount'],'name'=>$name),1);
        
     }
     
     /*
      * 意向投资人列表->股份确认
      */
    public function intention_confirm(){
        
        $id     = intval($_REQUEST['id']);
        $update = intval($_REQUEST['update']);
        $list   = M("fund_expectant_investor")->where("id = {$id}")->setField("actual_invest_confirm",2);
        
        if(false !== $list){
            //新增投资人和基金关系记录
            $info   = M("fund_expectant_investor")->where("id = {$id}")->find();
            $tmp    = M("user_fund_relation")->where("user_id = {$info['user_id']} and user_type = 1 and fund_id = {$info['fund_id']}")->find();
            if(empty($tmp)){
                
                $adm_session = es_session::get(md5(conf("AUTH_KEY")));
                
                $data['user_id']            = $info['user_id'];
                $data['fund_id']            = $info['fund_id'];
                $data['investor_amount']    = $info['actual_invest_amount'];
                $data['create_time']        = time();
                $data['user_type']          = 1;
                $data['operator']           = $adm_session['adm_id'];
                $re = M('user_fund_relation')->add($data);
                
                // 发送短信
                $user_info  = M("user")->where("id = {$info['user_id']}")->find();
                $fname      = M('fund')->where("id = {$info['fund_id']}")->getField('short_name');
                $msg        = getSendSmsTemplate("admin_fund_bind_investor",array($fname,$info['actual_invest_amount']));
                $params = array(
                    "mobile"    => $user_info['mobile'],
                    "content"   => $msg,
                    "type"      => getSendSmsType("admin_intention_confirm"),
                );
                $result = request_service_api("Common.Sms.sendSms",$params);
                
                // 发送站内信
                $user_notify['user_id']     = $user_info['id'];
                $user_notify['log_info']    = $msg;
                $user_notify['url']         = "";
                $user_notify['log_time']    = time();
                $user_notify['is_read']     = 0;
                M("user_notify")->add($user_notify);
            }else{
                if($update){
                    // 更新认购金额
                    M("user_fund_relation")->where("id = {$tmp['id']}")->setField("investor_amount",$info['actual_invest_amount']);
                }
            }
            
            save_log("意向投资人列表->股份确认" . L("SUCCESS"), 1);
            $this->success(L("股份确认成功"));
        } else {
            // 错误提示
            save_log("意向投资人列表->股份确认" . L("FAILED"), 0);
            $this->error(L("处理失败"), 0, $log_info . L("FAILED"));
        }
    }
	  //检查意向投资人股份却没确认已经确认，如果有不可驳回
     public function intention_rejected_check(){
     	   $id=intval($_REQUEST['id']);
     	   $result = M(fund_expectant_investor)->where("id= {$id}  and actual_invest_confirm=2 ")->findAll();	
     	  	if ($result) {
					$this->error("该意向投资人投资份额已经确认，不可驳回！", 0);
					exit();
				}else{ 
					 
					   	 $this->success(array('info'=>l("success"),'id'=>$id),1);
				}
     }
     
    /*
     * 意向投资人列表->审核驳回
     */
	public function intention_rejected()
    {
        $id     = intval($_REQUEST['id']);
        $list   = M("fund_expectant_investor")->where("id = {$id}")->setField("status",2);
        
        if (false !== $list) {
            // 发送站内信
            $info   = M("fund_expectant_investor")->where("id = {$id}")->find();
            $fname  = M('fund')->where("id = {$info['fund_id']}")->getField('short_name');
            $user_notify['user_id']     = $info['user_id'];
            $user_notify['log_info']    = "尊敬的投资人，您对{$fname}基金发起的“意向申购”申请已被驳回！";
            $user_notify['url']         = "";
            $user_notify['log_time']    = time();
            $user_notify['is_read']     = 0;
            M("user_notify")->add($user_notify);
            
            // 成功提示
            save_log($log_info . L("SUCCESS"), 1);
            $this->success(L("已驳回"));
        } else {
            // 错误提示
            save_log($log_info . L("FAILED"), 0);
            $this->error(L("处理失败"), 0, $log_info . L("FAILED"));
        }	
       
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
		    $map .= " and b.mobile like '%".$_REQUEST['mobile']."%' ";			
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
		
		// 购买份额合计：该基金所有投资者购买份额总额
		$sum_sql = "select sum(`investor_amount`) "
		          ." from ".DB_PREFIX."user_fund_relation as a,".DB_PREFIX."user as b "
		          ." where {$map}";
		$investor_amount_total = $GLOBALS['db']->getOne($sum_sql);
		
		// 批量计算功能
		$batch_fund_period = $batch_expectant_investor = 0;
	    // 基金是否已经正式成立
	    if((int)M("fund")->where("id = {$id}")->getField("fund_period") != 2){
	        $batch_fund_period = 1;
	    }
	    	
	    // 有拟投资人并且含有未确认的拟投资人认购记录
	    if(M("fund_expectant_investor")->where("fund_id = {$id} and actual_invest_confirm = 1")->find()){
	        $batch_expectant_investor = 1;
	    }
	    
		$this->assign("fund_id",$id );
		$this->assign("list",$result);
		$this->assign("partners",$partners);
		$this->assign("investor_amount_total",$investor_amount_total);
		$this->assign("batch_fund_period",$batch_fund_period);
		$this->assign("batch_expectant_investor",$batch_expectant_investor);
		$this->display('investor_list');
	} 
 	
 	public function investor_add()
	{ 	
		$fund_id = intval($_REQUEST['fund_id']);
		$this->assign("fund_id",$fund_id);
		
		// 渠道合伙人
		$partners = M('user')->where('role = 1 and is_effect = 1 and is_review = 1')->getField('id,user_name');
		$this->assign("partners",$partners);
		
		// 基金规模和起投金额
		$fund = M('fund')->field("total_amount,invest_min_amount")->where('id='.$fund_id)->find();
		$this->assign("total_amount",$fund['total_amount']);
		$this->assign("invest_min_amount",$fund['invest_min_amount']);
		
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
		$msg      = getSendSmsTemplate("admin_fund_bind_investor",array($fname,$data['investor_amount']));
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
		
		// 基金规模和起投金额
		$fund = M('fund')->field("total_amount,invest_min_amount")->where('id='.$vo['fund_id'])->find();
		$this->assign("total_amount",$fund['total_amount']);
		$this->assign("invest_min_amount",$fund['invest_min_amount']);
		
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
	
	/*
	 * 批量占比计算功能
	 */
	function investor_batch_accounting(){
	    
	    $id = (int)$_REQUEST ['id'];
	    
	    if(empty($id)){
	        $this->error (l("INVALID_OPERATION"),1);
	    }
	    
	    // 条件再次验证->基金是否已经正式成立
	    if((int)M("fund")->where("id = {$id}")->getField("fund_period") != 2){
	        $this->error ("该基金未正式成立不可以批量计算占比，请先进行基金成立操作！",1);
	    }
	    
        // 基金规模
        $total_amount = M("fund")->where("id = {$id}")->getField("total_amount");
	    M()->execute("UPDATE cixi_user_fund_relation SET investor_rate = ROUND((investor_amount / {$total_amount} * 100),2) WHERE fund_id = {$id} and user_type = 1");
	    $this->success("投资份额计算完成！",1);
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