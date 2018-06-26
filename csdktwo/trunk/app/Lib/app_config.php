<?php
// 常量配置
define('USER_SESSION_KEY','csdq_user_signin_key');
// 菜单标示常量
define('PAGE_MENU_INDEX','index');
define('PAGE_MENU_INVESTOR','investor');
define('PAGE_MENU_DEAL','deal');
define('PAGE_MENU_INTRODUCTION','introduction');
define('PAGE_MENU_RECOM_DEAL','recom_deal');
define('PAGE_MENU_APP','app');
define('PAGE_MENU_LOGIN','signin');
define('PAGE_MENU_REGISTER','signup');
define('PAGE_MENU_USER_CENTER','user_center');
define('PAGE_MENU_RAISING','raising');
define('PAGE_MENU_PROFIT','profit');
define('SIDE_MENU_HOME','home');
define('SIDE_MENU_ACCOUNT','account');
define('SIDE_MENU_RAISING','raising');
define('SIDE_MENU_PROFIT','profit');
define('SIDE_MENU_INVESTING','investing');
define('SIDE_MENU_MESSAGE','message');
define('SIDE_MENU_FUND','fund');
define('SIDE_MENU_PROFIT','profit');

// 登录验证权限列表 ps: 请用 “类名” 作为键，“方法名” 作为值
define( 'APP_LOGIN_STATE_ACCESS', json_encode(array(
		'home' => array('index'),	#个人中心-我的信息
		'account' => array('index'),	#个人中心-账户安全
		'invested' => array('index','invested_fund','deal_details'),	#个人中心-我的投资
		'investing' => array('index'),	#个人中心-“我要投资”项目
		'fund' => array('index'),	#个人中心-我的消息
		'message' => array('index'),	#个人中心-我的消息
		'profit' => array('index'),	#个人中心-我的收益
		'fund' => array('raising_list','raising_details'),	#个人中心-定向基金，基金详情页
// 		'investorindex' => array('index','attention'), # 不需要登陆状态，一些操作单独判断
// 		'dealdetails' => array('index','add_invest'), # 不需要登陆状态，一些操作单独判断
)));

// 枚举型变量
$app_enum_config = array (
		// banner发布渠道
		'BANNER_CHANNEL' => array (
				array('id'=>1, 'title'=>'APP'),
				array('id'=>2, 'title'=>'官网'),
				array('id'=>3, 'title'=>'全部'),
		),
		// banner分组信息
		'BANNER_GROUP' => array (
				array('id'=>1, 'title'=>'首页轮播'),
				array('id'=>2, 'title'=>'投资人轮播'),
				array('id'=>3, 'title'=>'我们轮播'),
				array('id'=>4, 'title'=>'合伙人轮播'),
		),
		// 资讯类别
		'NEWS_CLASS' => array (
				array('id'=>1, 'title'=>'外部资讯'),
				array('id'=>2, 'title'=>'内部资讯'),
		),
		// 资讯角标
		'NEWS_CORNER' => array (
				array('id'=>1, 'title'=>'热点'),
				array('id'=>2, 'title'=>'最新'),
				array('id'=>3, 'title'=>'普通'),
		),
		// 资讯发布渠道
		'NEWS_CHANNEL' => array (
				array('id'=>1, 'title'=>'app'),
				array('id'=>2, 'title'=>'官网'),
				array('id'=>3, 'title'=>'全部'),
		) 
);

function app_enum_conf($name){
	return $GLOBALS['app_enum_config'][$name];
}

?>