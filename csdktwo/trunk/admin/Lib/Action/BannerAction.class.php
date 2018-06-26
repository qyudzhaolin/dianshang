<?php
class BannerAction extends CommonAction {
	public function cafi_insert() {
		B ( 'FilterString' );
		$ajax = intval ( $_REQUEST ['ajax'] );
		$adm_session = es_session::get ( md5 ( conf ( "AUTH_KEY" ) ) );
		$id = intval ( $adm_session ['adm_id'] );
		$name = $GLOBALS ['db']->getOne ( "select adm_name from " . DB_PREFIX . "admin where id='$id'" );
		$data = M ( MODULE_NAME )->create ();
		if (! check_empty ( $_REQUEST ['b_channel'] )) {
			$this->error ( "请选择发布渠道" );
		}
		if ($_REQUEST ['b_channel'] == 3) {
		    $this->error ( "发布渠道：全部已移除" );
		}
		if (! check_empty ( $_REQUEST ['b_title'] )) {
			$this->error ( "请输入轮播标题" );
		}
		/*if (! check_empty ( $_REQUEST ['b_pc_img'] )) {
			$this->error ( "请上传图片" );
		}
		if (! check_empty ( $_REQUEST ['b_app_img'] )) {
			$this->error ( "请上传图片" );
		}*/
		$data ['b_channel'] = $_REQUEST ['b_channel'];
		$data ['b_title'] = $_REQUEST ['b_title'];
		$data ['b_bygroup'] = $_REQUEST ['b_bygroup'];
		$data ['b_url'] = $_REQUEST ['b_url'];
		$data ['b_pc_img'] = $_REQUEST ['b_pc_img'];
		$data ['b_app_img'] = $_REQUEST ['b_app_img'];
		$data ['b_sort'] = $_REQUEST ['b_sort'];
		
		if($_REQUEST ['b_publish_state']==1){

                 $data ['b_publish_state'] = 1;
		}elseif ($_REQUEST ['b_publish_state']==2) {
			    $data ['b_publish_state'] = 2;
				$data ['b_person'] = $name ;
		        $data ['create_time'] = time ();
		}
		$list = M ( MODULE_NAME )->add ( $data );
		if (false !== $list) {
			// 成功提示
			DealCateAction::update_cates_version ();
			save_log ( $log_info . L ( "INSERT_SUCCESS" ), 1 );
			$this->success ( L ( "INSERT_SUCCESS" ) );
		} else {
			// 错误提示
			save_log ( $log_info . L ( "INSERT_FAILED" ), 0 );
			$this->error ( L ( "INSERT_FAILED" ) );
		}
	}
	// 删除
	public function cafi_delete() {
		$ajax = intval ( $_REQUEST ['ajax'] );
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array (
					'id' => array (
							'in',
							explode ( ',', $id ) 
					) 
			);
			$rel_data = M ( MODULE_NAME )->where ( $condition )->findAll ();
			foreach ( $rel_data as $data ) {
				$info [] = $data ['name'];
			}
			if ($info)
				$info = implode ( ",", $info );
			$list = M ( MODULE_NAME )->where ( $condition )->delete ();
			if ($list !== false) {
				save_log ( $info . l ( "FOREVER_DELETE_SUCCESS" ), 1 );
				$this->success ( l ( "FOREVER_DELETE_SUCCESS" ), $ajax );
			} else {
				save_log ( $info . l ( "FOREVER_DELETE_FAILED" ), 0 );
				$this->error ( l ( "FOREVER_DELETE_FAILED" ), $ajax );
			}
		} else {
			$this->error ( l ( "INVALID_OPERATION" ), $ajax );
		}
	}
	// 撤回
	public function cafi_cancel() {
		$id = intval ( $_REQUEST ['id'] );
		$condition ['id'] = $id;
      $sate= $GLOBALS ['db']->getOne ( "select b_publish_state from " . DB_PREFIX . "banner where id='$id'" );
     if($sate==1){
   $this->error ( "请先发布" );
   }
		$jihe = $GLOBALS ['db']->query ( "update " . DB_PREFIX . "banner set b_publish_state=3 where id='$id'" );
		if (false !== $list) {
			// 成功提示
			save_log ( $log_info . L ( "UPDATE_SUCCESS" ), 1 );
			$this->success ( L ( "撤回成功" ) );
		} else {
			// 错误提示
			save_log ( $log_info . L ( "UPDATE_FAILED" ), 0 );
			$this->error ( L ( "撤回失败" ), 0, $log_info . L ( "UPDATE_FAILED" ) );
		}
	}
	// 发布
	public function cafi_publish() {
		$adm_session = es_session::get ( md5 ( conf ( "AUTH_KEY" ) ) );
		$ids = intval ( $adm_session ['adm_id'] );
		$name = $GLOBALS ['db']->getOne ( "select adm_name from " . DB_PREFIX . "admin where id='$ids'" );
		$id = intval ( $_REQUEST ['id'] );
		$condition ['id'] = $id;
		$time = time ();
		$jihe = $GLOBALS ['db']->query ( "update " . DB_PREFIX . "banner set b_publish_state=2,b_person='$name',create_time=$time where id='$id'" );
		if (false !== $list) {
			// 成功提示
			save_log ( $log_info . L ( "UPDATE_SUCCESS" ), 1 );
			$this->success ( L ( "发布成功" ) );
		} else {
			// 错误提示
			save_log ( $log_info . L ( "UPDATE_FAILED" ), 0 );
			$this->error ( L ( "撤回失败" ), 0, $log_info . L ( "UPDATE_FAILED" ) );
		}
	}
	public function cafi_update() {
		$data = M ( MODULE_NAME )->create ();
		$adm_session = es_session::get ( md5 ( conf ( "AUTH_KEY" ) ) );
		$id = intval ( $adm_session ['adm_id'] );
		$name= $GLOBALS ['db']->getOne ( "select adm_name from " . DB_PREFIX . "admin where id='$id'" );
		B ( 'FilterString' );
		$ajax = intval ( $_REQUEST ['ajax'] );
		$data = M ( MODULE_NAME )->create ();
		if (! check_empty ( $_REQUEST ['b_channel'] )) {
			$this->error ( "请选择发布渠道" );
		}
		if ($_REQUEST ['b_channel'] == 3) {
		    $this->error ( "发布渠道：全部已移除" );
		}
		if (! check_empty ( $_REQUEST ['b_title'] )) {
			$this->error ( "请输入轮播标题" );
		}
		/*if (! check_empty ( $_REQUEST ['b_pc_img'] )) {
			$this->error ( "请上传图片" );
		}
		if (! check_empty ( $_REQUEST ['b_app_img'] )) {
			$this->error ( "请上传图片" );
		}*/
		$data ['id'] = intval ( $_REQUEST ['id'] );
		$data ['b_channel'] = $_REQUEST ['b_channel'];
		$data ['b_title'] = $_REQUEST ['b_title'];
		$data ['b_bygroup'] = $_REQUEST ['b_bygroup'];
		$data ['b_url'] = $_REQUEST ['b_url'];
		$data ['b_pc_img'] = $_REQUEST ['b_pc_img'];
		$data ['b_app_img'] = $_REQUEST ['b_app_img'];
		$data ['b_sort'] = $_REQUEST ['b_sort'];
		if($_REQUEST ['b_publish_state']==1){
                 $data ['b_publish_state'] = 1;
		}elseif ($_REQUEST ['b_publish_state']==2) {
			    $data ['b_publish_state'] = 2;
				$data ['b_person'] = $name ;
		        $data ['create_time'] = time ();
		}elseif ($_REQUEST ['b_publish_state']==3) {
			    $data ['b_publish_state'] =3;
				$data ['b_person'] = $name ;
		        $data ['create_time'] = time ();
		}
		$list = M ( MODULE_NAME )->save ( $data );
		if (false !== $list) {
			// 成功提示
			save_log ( $log_info . L ( "UPDATE_SUCCESS" ), 1 );
			$this->success ( L ( "UPDATE_SUCCESS" ) );
		} else {
			// 错误提示
			save_log ( $log_info . L ( "UPDATE_FAILED" ), 0 );
			$this->error ( L ( "UPDATE_FAILED" ), 0, $log_info . L ( "UPDATE_FAILED" ) );
		}
	}
}
?>