<?php
class NewsAction extends CommonAction {
	public function index() {
		//筛选
		
		if (trim ( $_REQUEST ['n_title'] ) != '') {
			$map ['n_title'] = array (
					'like',
					'%' . trim ( $_REQUEST ['n_title'] ) . '%' 
			);
		}
		if (trim ( $_REQUEST ['n_class'] ) != '') {
			$map ['n_class'] = array (
					'like',
					'%' . trim ( $_REQUEST ['n_class'] ) . '%' 
			);
		}
		if (trim ( $_REQUEST ['n_cate'] ) != '') {
			$map ['n_cate'] =  trim($_REQUEST['n_cate']);
		
		}
		if (trim ( $_REQUEST ['n_deal'] ) != '') {
			$map ['n_deal'] =  trim($_REQUEST['n_deal']);
		}
		if (trim ( $_REQUEST ['n_corner'] ) != '') {
			$map ['n_corner'] = array (
					'like',
					'%' . trim ( $_REQUEST ['n_corner'] ) . '%' 
			);
		}
		if (trim ( $_REQUEST ['n_chanel'] ) != '') {
			$map ['n_channel'] = array (
					'like',
					'%' . trim ( $_REQUEST ['n_chanel'] ) . '%' 
			);
		}
		if (trim ( $_REQUEST ['n_publish_state'] ) != '') {
			$map ['n_publish_state'] = array (
					'like',
					'%' . trim ( $_REQUEST ['n_publish_state'] ) . '%' 
			);
		}
		// 下拉列表
		$region_lv1 =  M(deal_cate)->findAll();
		foreach($region_lv1 as $k=>$v){
			$regionMap[$v['id']] = $v['name'];
		}
		$region_lv2 = M(deal)->findAll();
		foreach($region_lv2 as $k=>$v){
			$dealMap[$v['id']] = $v['name'];
		}
		  
		$news_type = array(
			array('id'=>1,'name'=>'外部资讯'),
			array('id'=>2,'name'=>'内部资讯')
		);
		$newsTypeMap = array(
			1=>'外部资讯',
			2=>'内部资讯'
		);
		$corner_type=array(
            array('id'=>1,'name'=>'热点'),
			array('id'=>2,'name'=>'推荐'),
            array('id'=>3,'name'=>'普通')	
		);
		$cornerTypeMap = array(
			1=>'热点',
			2=>'推荐',
			3=>'普通'
		);
        $state_type=array(
            array('id'=>1,'name'=>'未发布'),
			array('id'=>2,'name'=>'已发布'),
            array('id'=>3,'name'=>'撤回')	
		);
        $stateTypeMap = array(
			1=>'未发布',
			2=>'已发布',
			3=>'撤回'
		);
        $chanel_type=array(
            array('id'=>1,'name'=>'app'),
			array('id'=>2,'name'=>'官网'),
			array('id'=>3,'name'=>'全部')
			);	
        $chanelTypeMap = array(
			1=>'app',
			2=>'官网',
			3=>'全部'
		);


	
		$name = $this->getActionName ();
		$model = D ( $name );
		$list = $this->_list ( $model, $map );
		foreach ( $list as $k => $v ) {
			if($v['n_publish_state']=='1'){
					$list[$k]['deal_button']='<a href="javascript:news_edit('.$list[$k]['id'].')">编辑</a>&nbsp;<a href="javascript: news_delete('.$list[$k]['id'].')">删除</a>&nbsp;<a href="javascript:news_publish('.$list[$k]['id'].')">发布</a>';
				}elseif($v['n_publish_state']=='2'){
					
					$list[$k]['deal_button']='<a href="javascript:news_cancel('.$list[$k]['id'].')">撤回</a>';
				}elseif($v['n_publish_state']=='3'){
					
					$list[$k]['deal_button']='<a href="javascript: news_delete('.$list[$k]['id'].')">删除</a>&nbsp;<a href="javascript:news_publish('.$list[$k]['id'].')">发布</a>&nbsp;<a href="javascript:news_edit('.$list[$k]['id'].')">编辑</a>';
				}
			$list[$k]['n_class'] = $newsTypeMap[$v['n_class']];
            $list[$k]['n_corner'] = $cornerTypeMap[$v['n_corner']];
			$list[$k]['n_publish_state'] = $stateTypeMap[$v['n_publish_state']];
			// 初始化列表行业属性值
			$list [$k] ['n_cate'] = $regionMap[$v['n_cate']];
			// 初始化列表项目属性值
			$list [$k] ['n_deal'] = $dealMap[$v['n_deal']];
         

		}
		$this->assign ( "region_lv2", $region_lv2 );
		$this->assign ( "region_lv1", $region_lv1 );
        $this->assign ( "news_type",  $news_type );
        $this->assign ( "corner_type",$corner_type );
        $this->assign ( "state_type", $state_type );
        $this->assign ( "chanel_type", $chanel_type );
		$this->assign ( "list", $list );
		$this->display ();
	}
	
	// 增加
	public function news_add() {
		// 1关联行业
		$news_type = array(
			array('id'=>1,'name'=>'外部资讯'),
			array('id'=>2,'name'=>'内部资讯')
		);
		$corner_type=array(
            array('id'=>1,'name'=>'热点'),
			array('id'=>2,'name'=>'推荐'),
            array('id'=>3,'name'=>'普通')	
		);

        $state_type=array(
            array('id'=>1,'name'=>'未发布'),
			array('id'=>2,'name'=>'已发布'),
            array('id'=>3,'name'=>'撤回')	
		);
        $chanel_type=array(
            array('id'=>1,'name'=>'app'),
			array('id'=>2,'name'=>'官网'),
			array('id'=>3,'name'=>'全部')
			);	
		$region_lv1 = $GLOBALS ['db']->getAll ( "select name,id  from " . DB_PREFIX . "deal_cate ORDER BY name desc" );
		$this->assign ( "region_lv1", $region_lv1 );
		// 2关联项目
		$region_lv2 = $GLOBALS ['db']->getAll ( "select name,id from " . DB_PREFIX . "deal ORDER BY name desc;" );
		$this->assign ( "region_lv2", $region_lv2 );

		 $this->assign ( "news_type",  $news_type );
        $this->assign ( "corner_type",$corner_type );
        $this->assign ( "state_type", $state_type );
        $this->assign ( "chanel_type", $chanel_type );
		$this->display ();
	}
	// 编辑
	public function news_edit() {
		$id = intval ( $_REQUEST ['id'] );
		$condition ['id'] = $id;
		$vo = M ( MODULE_NAME )->where ( $condition )->find ();
		$region_lv1 =  M(deal_cate)->findAll();
		foreach($region_lv1 as $k=>$v){
			$regionMap[$v['id']] = $v['name'];
		}
		$region_lv2 = M(deal)->findAll();
		foreach($region_lv2 as $k=>$v){
			$dealMap[$v['id']] = $v['name'];
		}

		if (trim ( $vo ['n_list_img'] )) {
			// 获取缩略图完整url地址
			include_once APP_ROOT_PATH . "system/common.php";
			$vo ['news_url'] = trim ( $vo ['n_list_img'] );
			$vo ['news_url'] = getQiniuPath ($vo ['news_url'], "img" );
		}
		if (trim ( $vo ['n_app_img'] )) {
			// 获取缩略图完整url地址
			include_once APP_ROOT_PATH . "system/common.php";
			$vo ['news_url2'] = trim ( $vo ['n_app_img'] );
			$vo ['news_url2'] = getQiniuPath ( $vo ['news_url2'], "img" );
		}
		$this->assign ( "region_lv2", $region_lv2 );
		$this->assign ( "region_lv1", $region_lv1 );
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	// 删除
	public function news_delete() {
		$ajax = intval ( $_REQUEST ['ajax'] );
		$id = $_REQUEST ['id'];

		if (isset ( $id )) {
			$condition = array (
					'id' => array (
							'in',
							explode ( ',', $id ) 
					) 
			);
			if (M ( "News" )->where ( array (
					'cate_id' => array (
							'in',
							explode ( ',', $id ) 
					) 
			) )->count () > 0) {
			$this->error ( "无法删除", $ajax );
			}
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
	public function news_update() {
		$ajax = intval ( $_REQUEST ['ajax'] );
		$data = M ( MODULE_NAME )->create ();
		$log_info = M ( MODULE_NAME )->where ( "id=" . intval ( $data ['id'] ) )->getField ( "name" );
		// 开始验证有效性
		$this->assign ( "jumpUrl", u ( MODULE_NAME . "/news_edit", array (
				"id" => $data ['id'] 
		) ) );
		if (! check_empty ( $_REQUEST ['n_class'] )) {
			$this->error ( "请输入类别" );
		}
		if (! check_empty ( $_REQUEST ['n_channel'] )) {
			$this->error ( "请输入渠道" );
		}
		if (! check_empty ( $_REQUEST ['n_class'] )) {
			$this->error ( "请输入类别" );
		}
		if (! check_empty ( $_REQUEST ['n_title'] )) {
			$this->error ( "请输入资讯标题" );
		}
		if (! check_empty ( $_REQUEST ['n_app_img'] )) {
			$this->error ( "请输入标题图片app" );
		}
		
		if (! check_empty ( $_REQUEST ['n_source'] )) {
			$this->error ( "请输入资讯来源" );
		}
        $adm_session = es_session::get ( md5 ( conf ( "AUTH_KEY" ) ) );
		$ids= intval ( $adm_session ['adm_id'] );
		$name = $GLOBALS ['db']->getOne ( "select adm_name from " . DB_PREFIX . "admin where id='$ids'" );
		$data ['id'] = intval ( $_REQUEST ['id'] );
		$data ['n_class'] = $_REQUEST ['n_class'];
		$data ['n_cate'] = $_REQUEST ['n_cate'];
		$data ['n_deal'] = $_REQUEST ['n_deal'];
		$data ['n_corner'] = $_REQUEST ['n_corner'];
		$data ['n_channel'] = $_REQUEST ['n_channel'];
		$data ['n_title'] = $_REQUEST ['n_title'];
		$data ['n_brief'] = $_REQUEST ['n_brief'];
		$data ['n_list_img'] = $_REQUEST ['n_list_img'];
		$data ['n_app_img'] = $_REQUEST ['n_app_img'];
		$data ['n_desc'] = $_REQUEST ['n_desc'];
		$data ['n_source'] = $_REQUEST ['n_source'];
	
		if($_REQUEST ['n_publish_state']==1){

                 $data ['n_publish_state'] = 1;
		}elseif ($_REQUEST ['n_publish_state']==2) {
			    $data ['n_publish_state'] = 2;
				$data ['n_person'] = $name ;
		        $data ['create_time'] = time ();
		}elseif ($_REQUEST ['n_publish_state']==3) {
			    $data ['n_publish_state'] = 3;
				$data ['n_person'] = $name ;
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
	public function news_insert() {
		B ( 'FilterString' );
		$ajax = intval ( $_REQUEST ['ajax'] );
		$data = M ( MODULE_NAME )->create ();
		if (! check_empty ( $_REQUEST ['n_class'] )) {
			$this->error ( "请输入类别" );
		}
		if (! check_empty ( $_REQUEST ['n_channel'] )) {
			$this->error ( "请输入渠道" );
		}
		if (! check_empty ( $_REQUEST ['n_class'] )) {
			$this->error ( "请输入类别" );
		}
		if (! check_empty ( $_REQUEST ['n_title'] )) {
			$this->error ( "请输入资讯标题" );
		}
		if (! check_empty ( $_REQUEST ['n_app_img'] )) {
			$this->error ( "请输入标题图片app" );
		}
		
		if (! check_empty ( $_REQUEST ['n_source'] )) {
			$this->error ( "请输入资讯来源" );
		}
      if ( $_REQUEST ['n_publish_state'] ==1) {
		$data ['n_class'] = $_REQUEST ['n_class'];
		$data ['n_cate'] = $_REQUEST ['n_cate'];
		$data ['n_deal'] = $_REQUEST ['n_deal'];
		$data ['n_corner'] = $_REQUEST ['n_corner'];
		$data ['n_channel'] = $_REQUEST ['n_channel'];
		$data ['n_title'] = $_REQUEST ['n_title'];
		$data ['n_brief'] = $_REQUEST ['n_brief'];
		$data ['n_app_img'] = $_REQUEST ['n_app_img'];
		$data ['n_list_img'] = $_REQUEST ['n_list_img'];
		$data ['n_desc'] = $_REQUEST ['n_desc'];
		$data ['n_source'] = $_REQUEST ['n_source'];
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
	  }else if($_REQUEST ['n_publish_state'] ==2){
     
	       	$adm_session = es_session::get ( md5 ( conf ( "AUTH_KEY" ) ) );
			$id = intval ( $adm_session ['adm_id'] );
			$name = $GLOBALS ['db']->getOne ( "select adm_name from " . DB_PREFIX . "admin where id='$id'" );
	        $time = time ();
		    $data ['n_class'] = $_REQUEST ['n_class'];
			$data ['n_cate'] = $_REQUEST ['n_cate'];
			$data ['n_deal'] = $_REQUEST ['n_deal'];
			$data ['n_corner'] = $_REQUEST ['n_corner'];
			$data ['n_channel'] = $_REQUEST ['n_channel'];
			$data ['n_title'] = $_REQUEST ['n_title'];
			$data ['n_brief'] = $_REQUEST ['n_brief'];
			$data ['n_app_img'] = $_REQUEST ['n_app_img'];
			$data ['n_list_img'] = $_REQUEST ['n_list_img'];
			$data ['n_desc'] = $_REQUEST ['n_desc'];
			$data ['n_source'] = $_REQUEST ['n_source'];
			$data ['n_publish_state'] = $_REQUEST ['n_publish_state'];
			$data ['create_time'] =$time ;
			$data ['n_person'] =$name;
	
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
	}
	public function cafi_index() {
		if (trim ( $_REQUEST ['b_title'] ) != '') {
			$map ['b_title'] = array (
					'like',
					'%' . trim ( $_REQUEST ['b_title'] ) . '%' 
			);
		}
		if (trim ( $_REQUEST ['b_channel'] ) != '') {
			$map ['b_channel'] = array (
					'like',
					'%' . trim ( $_REQUEST ['b_channel'] ) . '%' 
			);
		}
		if (trim ( $_REQUEST ['b_bygroup'] ) != '') {
			$map ['b_bygroup'] = array (
					'like',
					'%' . trim ( $_REQUEST ['b_bygroup'] ) . '%' 
			);
		}
		if (trim ( $_REQUEST ['b_publish_state'] ) != '') {
			$map ['b_publish_state'] = array (
					'like',
					'%' . trim ( $_REQUEST ['b_publish_state'] ) . '%' 
			);
		}
        	$channel_type=array(
            array('id'=>1,'name'=>'app'),
			array('id'=>2,'name'=>'官网'),
            array('id'=>3,'name'=>'全部')	
		);
        $channelTypeMap = array(
			1=>'app',
			2=>'官网',
			3=>'全部'
		);
        $state_type=array(
            array('id'=>1,'name'=>'未发布'),
			array('id'=>2,'name'=>'已发布'),
            array('id'=>3,'name'=>'撤回')	
		);
        $stateTypeMap = array(
			1=>'未发布',
			2=>'已发布',
			3=>'撤回'
      );


         $bygroup_type= array(
			array('id'=>1,'name'=>'首页轮播'),
			array('id'=>2,'name'=>'投资人轮播'),
			array('id'=>3,'name'=>'关于我们轮播'),
			array('id'=>4,'name'=>'合伙人轮播')
        );
        $bygroupTypeMap = array(
			1=>'首页轮播',
			2=>'投资人轮播',
			3=>'关于我们轮播',
			4=>'合伙人轮播'
        );
		$model = D ( banner );
		$list = $this->_list ( $model, $map );
		foreach ( $list as $k => $v ) {
			if ($v ['b_publish_state'] == '1') {
				$list [$k] ['cafi_button'] = '<a href="javascript:cafi_edit(' . $list [$k] ['id'] . ')">编辑</a>&nbsp;<a href="javascript: cafi_delete(' . $list [$k] ['id'] . ')">删除</a>&nbsp;<a href="javascript:cafi_publish(' . $list [$k] ['id'] . ')">发布</a>';
			} elseif ($v ['b_publish_state'] == '2') {
				$list [$k] ['cafi_button'] = '<a href="javascript:cafi_cancel(' . $list [$k] ['id'] . ')">撤回</a>';
			} elseif ($v ['b_publish_state'] == '3') {
				$list [$k] ['cafi_button'] = '<a href="javascript: cafi_delete(' . $list [$k] ['id'] . ')">删除</a>&nbsp;<a href="javascript:cafi_publish(' . $list [$k] ['id'] . ')">发布</a>&nbsp;<a href="javascript:cafi_edit(' . $list [$k] ['id'] . ')">编辑</a>';
			}
			if ($v ['b_bygroup'] == '1') {
				$list [$k] ['b_bygroup'] = '首页轮播';
			} elseif ($v ['b_bygroup'] == '2') {
				$list [$k] ['b_bygroup'] = '投资人轮播';
			} elseif ($v ['b_bygroup'] == '3') {
				$list [$k] ['b_bygroup'] = '关于我们轮播';
			} elseif ($v ['b_bygroup'] == '4') {
				$list [$k] ['b_bygroup'] = '合伙人轮播';
			}
			else{$list [$k] ['b_bygroup'] = '未选择';}

			$list [$k] ['b_channel'] = $channelTypeMap [$v ['b_channel']];
			$list [$k] ['b_publish_state'] = $stateTypeMap [$v ['b_publish_state']];
		}
		$this->assign ( "bygroup_type",$bygroup_type );
        $this->assign ( "state_type", $state_type );
        $this->assign ( "channel_type", $channel_type );
		$this->assign ( "list", $list );
		$this->display ();
	}
	// 撤回
	public function news_cancel() {
		$id = intval ( $_REQUEST ['id'] );
		$condition ['id'] = $id;
		$sate = $GLOBALS ['db']->getOne ( "select n_publish_state from " . DB_PREFIX . "news where id='$id'" );
		if ($sate == 1) {
			$this->error ( "请先发布" );
		}
		$jihe = $GLOBALS ['db']->query ( "update " . DB_PREFIX . "news set n_publish_state=3  where id='$id'" );
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
	public function news_publish() {
		$ids = intval ( $_REQUEST ['id'] );
		$condition ['id'] = $ids;
		$time = time ();
		$adm_session = es_session::get ( md5 ( conf ( "AUTH_KEY" ) ) );
		$id = intval ( $adm_session ['adm_id'] );
		$jihe = $GLOBALS ['db']->getOne ( "select adm_name from " . DB_PREFIX . "admin where id='$id'" );
		$sate = $GLOBALS ['db']->query ( "update " . DB_PREFIX . "news set n_publish_state=2 , create_time=$time ,n_person='$jihe' where id='$ids'" );
		if (false !== $list) {
			// 成功提示
			save_log ( $log_info . L ( "UPDATE_SUCCESS" ), 1 );
			$this->success ( L ( "发布成功" ) );
		} else {
			// 错误提示
			save_log ( $log_info . L ( "UPDATE_FAILED" ), 0 );
			$this->error ( L ( "发布失败" ), 0, $log_info . L ( "UPDATE_FAILED" ) );
		}
	}
	
	// 轮播图增加
	public function cafi_add() {

      	$channel_type=array(
            array('id'=>1,'name'=>'app'),
			array('id'=>2,'name'=>'官网'),
            array('id'=>3,'name'=>'全部')	
		);
       
        $bygroup_type= array(
			array('id'=>1,'name'=>'首页轮播'),
			array('id'=>2,'name'=>'投资人轮播'),
			array('id'=>3,'name'=>'关于我们轮播'),
			array('id'=>4,'name'=>'合伙人轮播')
        );
        $this->assign ( "channel_type",$channel_type );
        $this->assign ( "bygroup_type", $bygroup_type );  
		$this->display ();
	}
	// 编辑
	public function cafi_edit() {
		$id = intval ( $_REQUEST ['id'] );
		$condition ['id'] = $id;
		$vo = M ( banner )->where ( $condition )->find ();
		if (trim ( $vo ['b_pc_img'] )) {
			// 获取缩略图完整url地址
			include_once APP_ROOT_PATH . "system/common.php";
			$vo ['news_url'] = trim ( $vo ['b_pc_img'] );
			$vo ['news_url'] = getQiniuPath ( $vo ['news_url'], "img" );
		}
		if (trim ( $vo ['b_app_img'] )) {
			// 获取缩略图完整url地址
			include_once APP_ROOT_PATH . "system/common.php";
			$vo ['news_url2'] = trim ( $vo ['b_app_img'] );
			$vo ['news_url2'] = getQiniuPath ( $vo ['news_url2'], "img" );
		}

   $channel_type = array(
			1=>'app',
			2=>'官网',
			3=>'全部'
		);
   $channelTypeMap = array(
			1=>'app',
			2=>'官网',
			3=>'全部'
		);
        $state_type=array(
            array('id'=>1,'name'=>'未发布'),
			array('id'=>2,'name'=>'已发布'),
            array('id'=>3,'name'=>'撤回')	
		);
        $stateTypeMap = array(
			1=>'未发布',
			2=>'已发布',
			3=>'撤回'
      );
        $bygroup_type= array(
			array('id'=>1,'name'=>'首页轮播'),
			array('id'=>2,'name'=>'投资人轮播'),
			array('id'=>3,'name'=>'关于我们轮播'),
			array('id'=>4,'name'=>'合伙人轮播')
        );
		foreach ( $vo as $k => $v ) {
			  $list[$k]['b_channel'] = $channelTypeMap[$v['b_channel']];
			$list[$k]['b_publish_state'] = $stateTypeMap[$v['b_publish_state']];
		}
		$this->assign ( "channel_type",$channel_type );
        $this->assign ( "bygroup_type", $bygroup_type );  

		$this->assign ( "region", $vo );
		$this->display ();
	}
	
	// 轮播图数据的加入
}

?>