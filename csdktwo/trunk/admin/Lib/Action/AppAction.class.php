<?php
// +----------------------------------------------------------------------
// | 磁斯达克
// +----------------------------------------------------------------------
// | Copyright (c) 2015 All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

class AppAction extends CommonAction{
	public function index()
	{		
		
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, '' );
		}
		$this->display ();
	}
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();

		$this->assign("vo",$vo);
		
		$this->display ();
	}	
	
	public function update() {
		//var_dump($_REQUEST);die();
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
			
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['app_version']))
		{
			$this->error("请输入版本号");
		}	

		$list=M(MODULE_NAME)->save ($data);
	
		if (false !== $list) {
			
			//syn_deal($data['id']);
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	

	
}
?>