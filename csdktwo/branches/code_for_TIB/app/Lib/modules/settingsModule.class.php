<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class settingsModule extends BaseModule
{

	public function index()
	{		
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		$region_pid = 0;
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['name'] == $GLOBALS['user_info']['province'])
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
				if($v['name'] == $GLOBALS['user_info']['city'])
				{
					$region_lv3[$k]['selected'] = 1;
					break;
				}
			}
			$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		}
		
		$weibo_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_weibo where user_id = ".intval($GLOBALS['user_info']['id']));
		$GLOBALS['tmpl']->assign("weibo_list",$weibo_list);
		
		$GLOBALS['tmpl']->display("settings_index.html");
	}
	
	public function save_index()
	{		
		$ajax = intval($_REQUEST['ajax']);		
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		if(!check_ipop_limit(get_client_ip(),"setting_save_index",5))
		showErr("提交太频繁",$ajax,"");	
		
		require_once APP_ROOT_PATH."system/libs/user.php";


		$user_data = array();
		$user_data['province'] = strim($_REQUEST['province']);
		$user_data['city'] = strim($_REQUEST['city']);
		$user_data['sex'] = intval($_REQUEST['sex']);
		$user_data['intro'] = strim($_REQUEST['intro']);
		$user_data['intro'] = strim($_REQUEST['intro']);
		$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_data,"UPDATE","id=".intval($GLOBALS['user_info']['id']));
		
	
		showSuccess("资料保存成功",$ajax,"");
		//$res = save_user($user_data);
	}
	
	public function password()
	{		

		if(intval($_REQUEST['code'])!=0)
		{
			$uid = intval($_REQUEST['id']);
			$code = intval($_REQUEST['code']); 
			$GLOBALS['user_info'] = $user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$uid." and password_verify = '".$code."' and is_effect = 1");
			if($user_info)
			{
				es_session::set("user_info",$user_info);
				$GLOBALS['tmpl']->assign("user_info",$user_info);
				$GLOBALS['db']->query("update ".DB_PREFIX."user set password_verify = '' where id = ".$uid);
			}
			else
			{
				app_redirect(url("index"));
			}
		}
		else if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));		
		$GLOBALS['tmpl']->display("settings_password.html");
	
	}
	
	
	public function save_password()
	{		
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		if(!check_ipop_limit(get_client_ip(),"setting_save_password",5))
		showErr("提交太频繁",$ajax,"");	
		
		$user_pwd = strim($_REQUEST['user_pwd']);
		$confirm_user_pwd = strim($_REQUEST['confirm_user_pwd']);
		if(strlen($user_pwd)<4)
		{
			showErr("密码不能低于四位",$ajax,"");
		}
		if($user_pwd!=$confirm_user_pwd)
		{
			showErr("密码确认失败",$ajax,"");
		}
		
		require_once APP_ROOT_PATH."system/libs/user.php";
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
		$user_info['user_pwd'] = $user_pwd;
		save_user($user_info,"UPDATE");
		
		showSuccess("保存成功",$ajax,"");
		//$res = save_user($user_data);
	}

	public function bind()
	{
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		
		
		$api_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."api_login");
		foreach($api_list as $k=>$v)
		{
			if($GLOBALS['user_info'][strtolower($v['class_name'])."_id"]!='')
			{
				$api_list[$k]['is_bind'] = true;
				$api_list[$k]['weibo_url'] = $GLOBALS['user_info'][strtolower($v['class_name'])."_url"];
			}
			else
			{
				$api_list[$k]['is_bind'] = false;
				require_once APP_ROOT_PATH."system/api_login/".$v['class_name']."_api.php";
				$class_name = $v['class_name']."_api";
				$o = new $class_name($v);
				$api_list[$k]['url'] = $o->get_bind_api_url();
			}
			
		}
		
		
		$GLOBALS['tmpl']->assign("api_list",$api_list);
		$GLOBALS['tmpl']->display("settings_bind.html");
	}
	
	public function unbind()
	{
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		
		$class_name = strtolower(strim($_REQUEST['c']));
		
		$GLOBALS['db']->query("update ".DB_PREFIX."user set ".$class_name."_id = '',".$class_name."_url = '' where id = ".intval($GLOBALS['user_info']['id']),"SILENT");
		
		app_redirect(url("settings#bind"));
	}
	
	
	public function bank()
	{		
		if(!$GLOBALS['user_info'])
		app_redirect(url("user#login"));
		
		if($GLOBALS['user_info']['ex_real_name']!=""||$GLOBALS['user_info']['ex_account_info']!=""||$GLOBALS['user_info']['mobile']!="")
		{
			app_redirect_preview();
		}
		
		$GLOBALS['tmpl']->display("settings_bank.html");
	}
	
	
	public function save_bank()
	{		
		$ajax = intval($_REQUEST['ajax']);		
		if(!$GLOBALS['user_info'])
		{
			showErr("",$ajax,url("user#login"));
		}
		
		if($GLOBALS['user_info']['ex_real_name']!=""||$GLOBALS['user_info']['ex_account_info']!=""||$GLOBALS['user_info']['mobile']!="")
		{
			showErr("银行帐户信息已经设置过",$ajax,"");	
		}
		
		if(!check_ipop_limit(get_client_ip(),"setting_save_bank",5))
		showErr("提交太频繁",$ajax,"");	
		
		$ex_real_name = strim($_REQUEST['ex_real_name']);
		$ex_account_info = strim($_REQUEST['ex_account_info']);
		$mobile = strim($_REQUEST['mobile']);
		
		if($ex_real_name==""||$ex_account_info==""||$mobile=="")
		{
			showErr("请填写完整的信息",$ajax,"");	
		}
		
		$GLOBALS['db']->query("update ".DB_PREFIX."user set ex_real_name = '".$ex_real_name."',ex_account_info = '".$ex_account_info."',mobile = '".$mobile."' where id = ".intval($GLOBALS['user_info']['id']));
		
		showSuccess("资料保存成功",$ajax,url("settings"));
		//$res = save_user($user_data);
	}
}
?>