<?php  if (!defined('THINK_PATH')) exit(); filter_request($_REQUEST); filter_request($_GET); filter_request($_POST); define("AUTH_NOT_LOGIN", 1); define("AUTH_NOT_AUTH", 2); function conf($name,$value = false) { if($value === false) { return C($name); } else { if(M("Conf")->where("is_effect=1 and name='".$name."'")->count()>0) { if(in_array($name,array('EXPIRED_TIME','SUBMIT_DELAY','SEND_SPAN','WATER_ALPHA','MAX_IMAGE_SIZE','INDEX_LEFT_STORE','INDEX_LEFT_TUAN','INDEX_LEFT_YOUHUI','INDEX_LEFT_DAIJIN','INDEX_LEFT_EVENT','INDEX_RIGHT_STORE','INDEX_RIGHT_TUAN','INDEX_RIGHT_YOUHUI','INDEX_RIGHT_DAIJIN','INDEX_RIGHT_EVENT','SIDE_DEAL_COUNT','DEAL_PAGE_SIZE','PAGE_SIZE','BATCH_PAGE_SIZE','HELP_CATE_LIMIT','HELP_ITEM_LIMIT','REC_HOT_LIMIT','REC_NEW_LIMIT','REC_BEST_LIMIT','REC_CATE_GOODS_LIMIT','SALE_LIST','INDEX_NOTICE_COUNT','RELATE_GOODS_LIMIT'))) { $value = intval($value); } M("Conf")->where("is_effect=1 and name='".$name."'")->setField("value",$value); } C($name,$value); } } function write_timezone($zone='') { if($zone=='') $zone = conf('TIME_ZONE'); $var = array( '0' => 'UTC', '8' => 'PRC', ); $timezone_config_str = "<?php\r\n"; $timezone_config_str .= "return array(\r\n"; $timezone_config_str.="'DEFAULT_TIMEZONE'=>'".$var[$zone]."',\r\n"; $timezone_config_str.=");\r\n"; $timezone_config_str.="?>"; @file_put_contents(get_real_path()."public/timezone_config.php",$timezone_config_str); } function save_log($msg,$status) { if(conf("ADMIN_LOG")==1) { $adm_session = es_session::get(md5(conf("AUTH_KEY"))); $log_data['log_info'] = $msg; $log_data['log_time'] = get_gmtime(); $log_data['log_admin'] = intval($adm_session['adm_id']); $log_data['log_ip'] = get_client_ip(); $log_data['log_status'] = $status; $log_data['module'] = MODULE_NAME; $log_data['action'] = ACTION_NAME; M("Log")->add($log_data); } } function get_toogle_status($tag,$id,$field) { if($tag) { return "<span class='is_effect' onclick=\"toogle_status(".$id.",this,'".$field."');\">".l("YES")."</span>"; } else { return "<span class='is_effect' onclick=\"toogle_status(".$id.",this,'".$field."');\">".l("NO")."</span>"; } } function get_is_effect($tag,$id) { if($tag) { return "<span class='is_effect' onclick='set_effect(".$id.",this);'>".l("IS_EFFECT_1")."</span>"; } else { return "<span class='is_effect' onclick='set_effect(".$id.",this);'>".l("IS_EFFECT_0")."</span>"; } } function get_sort($sort,$id) { if($tag) { return "<span class='sort_span' onclick='set_sort(".$id.",".$sort.",this);'>".$sort."</span>"; } else { return "<span class='sort_span' onclick='set_sort(".$id.",".$sort.",this);'>".$sort."</span>"; } } function get_nav($nav_id) { return M("RoleNav")->where("id=".$nav_id)->getField("name"); } function get_module($module_id) { return M("RoleModule")->where("id=".$module_id)->getField("module"); } function get_group($group_id) { if($group_data = M("RoleGroup")->where("id=".$group_id)->find()) $group_name = $group_data['name']; else $group_name = L("SYSTEM_NODE"); return $group_name; } function get_role_name($role_id) { return M("Role")->where("id=".$role_id)->getField("name"); } function get_admin_name($admin_id) { $adm_name = M("Admin")->where("id=".$admin_id)->getField("adm_name"); if($adm_name) return $adm_name; else return l("NONE_ADMIN_NAME"); } function get_log_status($status) { return l("LOG_STATUS_".$status); } function check_sort($sort) { if(!is_numeric($sort)) { return false; } return true; } function check_empty($data) { if(trim($data)=='') { return false; } return true; } function set_default($null,$adm_id) { $admin_name = M("Admin")->where("id=".$adm_id)->getField("adm_name"); if($admin_name == conf("DEFAULT_ADMIN")) { return "<span style='color:#f30;'>".l("DEFAULT_ADMIN")."</span>"; } else { return "<a href='".u("Admin/set_default",array("id"=>$adm_id))."'>".l("SET_DEFAULT_ADMIN")."</a>"; } } function get_all_files( $path ) { $list = array(); $dir = @opendir($path); while (false !== ($file = @readdir($dir))) { if($file!='.'&&$file!='..') if( is_dir( $path.$file."/" ) ){ $list = array_merge( $list , get_all_files( $path.$file."/" ) ); } else { $list[] = $path.$file; } } @closedir($dir); return $list; } function get_send_type_msg($status) { if($status==0) { return l("SMS_SEND"); } else { return l("MAIL_SEND"); } } function get_is_send($is_send) { if($is_send==0) return L("YES"); else return L("NO"); } function get_send_result($result) { if($result==0) { return L("SUCCESS"); } else { return L("FAILED"); } } function get_status($status) { if($status) { return l("YES"); } else return l("NO"); } function show_content($content,$id) { return "<a title='".l("VIEW")."' href='javascript:void(0);' onclick='show_content(".$id.")'>".l("VIEW")."</a>"; } function get_deal_user($uid) { $uinfo = M("User")->getById($uid); if($uinfo) { return $uinfo['user_name']; } else { if($uid==0) return "管理员发起"; else return "发起人被删除"; } } function get_to_date($time) { if($time==0)return "长期"; if($time<get_gmtime()) { return "<span style='color:#f30;'>过期</span>"; } else { return "<span>".to_date($time,"Y/m/d H:i")."</span>"; } } function get_title($title) { return "<span title='".$title."'>".msubstr($title)."</span>"; } function get_deal_name($id) { $name = M("Deal")->where("id=".$id)->getField("name"); return get_title($name); } function P($data){ echo '<pre>'; print_r($data); echo '</pre>'; echo '<hr>'; } function convert_array($dyadic_array){ $arr = array(); if(count($dyadic_array) >=1){ foreach ($dyadic_array as $v){ $arr[$v[id]] = $v[name]; } } return $arr; } function generalBusinessCode($objName,$id){ if(empty($id)){ return 0; } $business_code_rule = C('BUSINESS_CODE_RULE')[$objName]; $num = str_pad($id, $business_code_rule[1],0,STR_PAD_LEFT); return $business_code_rule[0].$num; } return array ( 'app_debug' => false, 'app_domain_deploy' => false, 'app_plugin_on' => false, 'app_file_case' => false, 'app_group_depr' => '.', 'app_group_list' => '', 'app_autoload_reg' => false, 'app_autoload_path' => 'Think.Util.,@.COM.', 'app_config_list' => array ( 0 => 'taglibs', 1 => 'routes', 2 => 'tags', 3 => 'htmls', 4 => 'modules', 5 => 'actions', ), 'cookie_expire' => 3600, 'cookie_domain' => '', 'cookie_path' => '/', 'cookie_prefix' => '', 'default_app' => '@', 'default_group' => 'Home', 'default_module' => 'Index', 'default_action' => 'index', 'default_charset' => 'utf-8', 'default_timezone' => 'PRC', 'default_ajax_return' => 'JSON', 'default_theme' => 'default', 'default_lang' => 'zh-cn', 'db_type' => 'mysql', 'db_host' => '192.168.1.28', 'db_name' => 'csdk', 'db_user' => 'dev', 'db_pwd' => 'dev123', 'db_port' => '3306', 'db_prefix' => 'cixi_', 'db_suffix' => '', 'db_fieldtype_check' => false, 'db_fields_cache' => true, 'db_charset' => 'utf8', 'db_deploy_type' => 0, 'db_rw_separate' => false, 'data_cache_time' => -1, 'data_cache_compress' => false, 'data_cache_check' => false, 'data_cache_type' => 'File', 'data_cache_path' => './admin/../public/runtime/admin/Temp/', 'data_cache_subdir' => false, 'data_path_level' => 1, 'error_message' => '您浏览的页面暂时发生了错误！请稍后再试～', 'error_page' => '', 'html_cache_on' => false, 'html_cache_time' => 60, 'html_read_type' => 0, 'html_file_suffix' => '.shtml', 'lang_switch_on' => false, 'lang_auto_detect' => true, 'log_record' => false, 'log_file_size' => 2097152, 'log_record_level' => array ( 0 => 'EMERG', 1 => 'ALERT', 2 => 'CRIT', 3 => 'ERR', ), 'page_rollpage' => 5, 'page_listrows' => 15, 'session_auto_start' => true, 'show_run_time' => false, 'show_adv_time' => false, 'show_db_times' => false, 'show_cache_times' => false, 'show_use_mem' => false, 'show_page_trace' => false, 'show_error_msg' => true, 'tmpl_engine_type' => 'Think', 'tmpl_detect_theme' => false, 'tmpl_template_suffix' => '.html', 'tmpl_cachfile_suffix' => '.php', 'tmpl_deny_func_list' => 'echo,exit', 'tmpl_parse_string' => '', 'tmpl_l_delim' => '{', 'tmpl_r_delim' => '}', 'tmpl_var_identify' => 'array', 'tmpl_strip_space' => false, 'tmpl_cache_on' => '0', 'tmpl_cache_time' => -1, 'tmpl_action_error' => 'Public:error', 'tmpl_action_success' => 'Public:success', 'tmpl_trace_file' => './admin/ThinkPHP/Tpl/PageTrace.tpl.php', 'tmpl_exception_file' => './admin/ThinkPHP/Tpl/ThinkException.tpl.php', 'tmpl_file_depr' => '/', 'taglib_begin' => '<', 'taglib_end' => '>', 'taglib_load' => true, 'taglib_build_in' => 'cx', 'taglib_pre_load' => '', 'tag_nested_level' => 3, 'tag_extend_parse' => '', 'token_on' => 0, 'token_name' => '__hash__', 'token_type' => 'md5', 'url_case_insensitive' => false, 'url_router_on' => false, 'url_dispatch_on' => true, 'url_model' => '0', 'url_pathinfo_model' => 2, 'url_pathinfo_depr' => '/', 'url_html_suffix' => '', 'var_group' => 'g', 'var_module' => 'm', 'var_action' => 'a', 'var_router' => 'r', 'var_page' => 'p', 'var_template' => 't', 'var_language' => 'l', 'var_ajax_submit' => 'ajax', 'var_pathinfo' => 's', 'default_admin' => 'admin', 'auth_key' => 'csdk', 'time_zone' => '0', 'admin_log' => '1', 'db_version' => '2.1', 'db_vol_maxsize' => '8000000', 'water_mark' => './public/attachment/201507/18/09/55a9aaa661b7b.png', 'big_width' => '500', 'big_height' => '500', 'small_width' => '200', 'small_height' => '200', 'water_alpha' => '75', 'water_position' => '5', 'max_image_size' => '3000000', 'allow_image_ext' => 'jpg,gif,png', 'bg_color' => '#ffffff', 'is_water_mark' => '1', 'template' => 'csdk', 'site_logo' => './public/attachment/201507/18/09/55a9aaa661b7b.png', 'seo_title' => '好项目的第一站|创业、融资、投资就在磁斯达克|提供一站式资本解决方案的专业股权交易平台', 'reply_address' => 'admin@cisdaq.com', 'mail_on' => '1', 'sms_on' => '0', 'public_domain_root' => '', 'app_msg_sender_open' => '0', 'admin_msg_sender_open' => '0', 'gzip_on' => '0', 'site_name' => '磁斯达克', 'cache_on' => '0', 'expired_time' => '0', 'tmpl_domain_root' => '', 'cache_type' => 'File', 'memcache_host' => '127.0.0.1:11211', 'image_username' => 'liusiyu@cisdaq.com', 'image_password' => '1983711', 'deal_msg_lock' => '0', 'send_span' => '2', 'domain_root' => '', 'integrate_cfg' => '', 'integrate_code' => '', 'pay_radio' => '0.1', 'site_license' => '磁斯达克 @磁信科技 京ICP备15055470号-1', 'seo_keyword' => '磁斯达克、理财、股票、支付、众筹、投资、融资、新三版、创业、股权、金融、募资、估值、互联网金融', 'seo_description' => '磁斯达克-好项目的第一站|创业、融资、投资就在磁斯达克|提供一站式资本解决方案的专业股权交易平台', 'log_path' => './public/logger/', 'service_api_host' => 'http://service.test.com/', 'service_api_host_entry' => 'index.php', 'service_api_default_module' => 'Wap', 'service_api_request_seckey' => 'HTTP://WWW.CISDAQ.COM/_2016@CSDK_#@DU^^&JGK_((*&gjGH', 'app_name' => '磁斯达克 ', 'app_sub_ver' => 0, 'rewriter_depart' => '-', 'debug' => false, 'site_domain' => '', 'accesskey' => '5CQmagmT27DNXYhsmVewntOpd9VLGD8sC5c02ptg', 'secretkey' => 'lQm-akaIb3JOS-WAlBycTXF_95hx7JEd9s88ARk5', 'maxsize' => 5242880, 'auth_type' => array ( 0 => '未认证', 1 => '已认证', 2 => '认证中', ), 'auth_type_list' => array ( 0 => array ( 'id' => '0', 'name' => '未认证', ), 1 => array ( 'id' => '1', 'name' => '已认证', ), 2 => array ( 'id' => '2', 'name' => '认证中', ), ), 'user_type' => array ( 1 => '投资者', 2 => '合伙人', ), 'user_type_list' => array ( 0 => array ( 'id' => '1', 'name' => '投资者', ), ), 'user_is_effect' => array ( 0 => '禁用', 1 => '未禁用', ), 'education_list' => array ( 0 => array ( 'id' => '初中及以下', 'name' => '初中及以下', ), 1 => array ( 'id' => '高中', 'name' => '高中', ), 2 => array ( 'id' => '中专技校', 'name' => '中专技校', ), 3 => array ( 'id' => '大专', 'name' => '大专', ), 4 => array ( 'id' => '本科', 'name' => '本科', ), 5 => array ( 'id' => '硕士及以上', 'name' => '硕士及以上', ), ), 'deal_intend_list' => array ( 0 => array ( 'id' => 0, 'name' => '申请中', ), 1 => array ( 'id' => 1, 'name' => '已沟通', ), 2 => array ( 'id' => 2, 'name' => '已投资', ), 3 => array ( 'id' => 3, 'name' => '份额已满', ), 4 => array ( 'id' => 4, 'name' => '主动放弃', ), ), 'business_company' => 'company', 'business_company_team' => 'company_team', 'business_company_share' => 'company_share', 'business_code_rule' => array ( 'company' => array ( 0 => 'C', 1 => '5', ), 'company_team' => array ( 0 => 'XM', 1 => '5', ), 'company_share' => array ( 0 => 'GF', 1 => '3', ), 'fund_attachment' => array ( 0 => 'XX', 1 => '4', ), ), '_taglibs_' => array ( 'html' => '@.TagLib.TagLibHtml', ), ); ?>