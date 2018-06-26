<?php
if (!defined('THINK_PATH')) exit();
if(file_exists(APP_ROOT_PATH.'system/config.php'))
$sys_config	=	require APP_ROOT_PATH.'system/config.php';
$array = array(
	'TOKEN_ON'	=>	0,
    'DEFAULT_THEME'    => 'default', //后台模板
	'DEFAULT_LANG'	   =>	'zh-cn', //后台语言
	'URL_MODEL'		   =>	'0',     //后台URL模式为原始模式



	'TMPL_ACTION_ERROR'     => 'Public:error', // 默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS'   => 'Public:success', // 默认成功跳转对应的模板文件
	//'TMPL_TRACE_FILE'       =>  BASE_PATH.'/global/PageTrace.tpl.php',     // 页面Trace的模板
	
	'PAGE_ROLLPAGE'         => 5,      // 分页显示页数
	'PAGE_LISTROWS'         => 15,     // 分页每页显示记录数
	//后台自动载入的类库
	'APP_AUTOLOAD_PATH'     => 'Think.Util.,@.COM.',// __autoLoad 机制额外检测路径设置,注意搜索顺序	
	
	// 用户状态
	'AUTH_TYPE' => array(
		0=>'未认证',
		1=>'已认证',
		2=>'认证中'
	),
	'AUTH_TYPE_LIST' => array(
		array('id'=>'0','name'=>'未认证'),
		array('id'=>'1','name'=>'已认证'),
		array('id'=>'2','name'=>'认证中')
	),
	// 用户类别
	'USER_TYPE' => array(
		1=>'投资者',
		2=>'合伙人'
	),
	'USER_TYPE_LIST' => array(
		array('id'=>'1','name'=>'投资者') 
	),
	
	'USER_IS_EFFECT' => array(
		0=>'禁用',
		1=>'未禁用'
	),
	
	'EDUCATION_LIST' => array(
		array('id'=>'初中及以下','name'=>'初中及以下'),
		array('id'=>'高中','name'=>'高中'),
		array('id'=>'中专技校','name'=>'中专技校'),
		array('id'=>'大专','name'=>'大专'),
		array('id'=>'本科','name'=>'本科'),
		array('id'=>'硕士及以上','name'=>'硕士及以上')
	),
	
	// 项目投资意向状态列表
	'DEAL_INTEND_LIST' => array(
		array('id'=>0,'name'=>'申请中'),
		array('id'=>1,'name'=>'已沟通'),
		array('id'=>2,'name'=>'已投资'),
		array('id'=>3,'name'=>'份额已满'),
		array('id'=>4,'name'=>'主动放弃')
	),
	
    // 定义业务对象名称
	'BUSINESS_COMPANY'=>'company', // 基金管理公司
	'BUSINESS_COMPANY_TEAM'=>'company_team', // 基金管理公司团队成员
	'BUSINESS_COMPANY_SHARE'=>'company_share', // 基金管理公司股份构成
	
	// 业务编码定义，key为对象名称，value数组的第一个值为编码前缀，第二个值为编码序号长度
	'BUSINESS_CODE_RULE'=>array(
		'company' => array('C','5'),
		'company_team' => array('XM','5'),
		'company_share' => array('GF','3'),
		'fund_attachment' => array('XX','4'),
	),
	
);


if(file_exists(APP_ROOT_PATH.'system/config.php'))
$config = array_merge($sys_config,$array);
else
$config = $array;
return $config;
?>