<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

//前后台加载的函数库
require_once 'system_init.php';

// 防sql、xss 注入
require_once 'waf.php';

/**
 * 请求一个服务层Api
 * @param string $func    	api 路径, 	ex. Wap.Address.getAllAddressByUser
 * @param array $params	api调用参数
 * @return NULL
 */
function request_service_api($func, $params=array() ){
	$curl = new system\util\Curl();
	$curl->setOpt(CURLOPT_TIMEOUT, 15);
	$curl->setOpt(CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded; charset=UTF-8'));
	$curl->setOpt(CURLOPT_FRESH_CONNECT, true);
	$curl->setOpt(CURLOPT_ENCODING , 'gzip');
	$curl->setOpt(CURLOPT_USERAGENT, 'CSDK_WAP_UI_LAYER');
	
	$func = substr_count($func, '.')===1 ?  app_conf('SERVICE_API_DEFAULT_MODULE').'.'.$func : $func;
	$params = array(
			'method' => 'Wap',
			'func' =>  $func,
			'params' =>  json_encode($params),
	);
	$params['signKey'] = md5($params['params'].app_conf('SERVICE_API_REQUEST_SECKEY').$params['func']);
	
	$requestEntry = app_conf('SERVICE_API_HOST').app_conf('SERVICE_API_HOST_ENTRY');
	
	$curl->post($requestEntry,$params);

	if ($curl->error){
		return null;
	}

	return  json_decode($curl->response, true);
}

//获取真实路径
function get_real_path()
{
	return APP_ROOT_PATH;
}

//获取北京时间
function get_gmtime()
{
	return time();
}

function to_date($utc_time, $format = 'Y-m-d H:i:s') {
	if (empty ( $utc_time )) {
		return '';
	}
	$timezone = intval(app_conf('TIME_ZONE'));
	$time = $utc_time + $timezone * 3600; 
	return date ($format, $time );
}

function to_timespan($str, $format = 'Y-m-d H:i:s')
{
	$timezone = intval(app_conf('TIME_ZONE'));
	//$timezone = 8; 
	$time = intval(strtotime($str));
	if($time!=0)
	$time = $time - $timezone * 3600;
    return $time;
}


//获取客户端IP
function get_client_ip() {
	if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
		$ip = getenv ( "HTTP_CLIENT_IP" );
	else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
		$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
	else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
		$ip = getenv ( "REMOTE_ADDR" );
	else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
		$ip = $_SERVER ['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return ($ip);
}

//过滤注入
function filter_injection(&$request)
{
	$pattern = "/(select[\s])|(insert[\s])|(update[\s])|(delete[\s])|(from[\s])|(where[\s])/i";
	foreach($request as $k=>$v)
	{
				if(preg_match($pattern,$k,$match))
				{
						die("SQL Injection denied!");
				}
		
				if(is_array($v))
				{					
					filter_injection($v);
				}
				else
				{					
					
					if(preg_match($pattern,$v,$match))
					{
						die("SQL Injection denied!");
					}					
				}
	}
	
}

//过滤请求
function filter_request(&$request)
{
		if(MAGIC_QUOTES_GPC)
		{
			foreach($request as $k=>$v)
			{
				if(is_array($v))
				{
					filter_request($v);
				}
				else
				{
					$request[$k] = stripslashes(trim($v));
				}
			}
		}
		
}

function adddeepslashes(&$request)
{

			foreach($request as $k=>$v)
			{
				if(is_array($v))
				{
					adddeepslashes($v);
				}
				else
				{
					$request[$k] = addslashes(trim($v));
				}
			}		
}


function quotes($content)
{
	//if $content is an array
	if (is_array($content))
	{
		foreach ($content as $key=>$value)
		{
			$content[$key] = mysql_real_escape_string($value);
		}
	} else
	{
		//if $content is not an array
		mysql_real_escape_string($content);
	}
	return $content;
}


//request转码
function convert_req(&$req)
{
	foreach($req as $k=>$v)
	{
		if(is_array($v))
		{
			convert_req($req[$k]);
		}
		else
		{
			if(!is_u8($v))
			{
				$req[$k] = iconv("gbk","utf-8",$v);
			}
		}
	}
}

function is_u8($string)
{
	return preg_match('%^(?:
		 [\x09\x0A\x0D\x20-\x7E]            # ASCII
	   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $string);
}


//清除缓存
function clear_cache()
{
		//系统后台缓存
		clear_dir_file(get_real_path()."public/runtime/admin/Cache/");	
		clear_dir_file(get_real_path()."public/runtime/admin/Data/_fields/");		
		clear_dir_file(get_real_path()."public/runtime/admin/Temp/");	
		clear_dir_file(get_real_path()."public/runtime/admin/Logs/");	
		@unlink(get_real_path()."public/runtime/admin/~app.php");
		@unlink(get_real_path()."public/runtime/admin/~runtime.php");
		@unlink(get_real_path()."public/runtime/admin/lang.js");
		@unlink(get_real_path()."public/runtime/app/config_cache.php");	
		
		
		//数据缓存
		clear_dir_file(get_real_path()."public/runtime/app/data_caches/");				
		clear_dir_file(get_real_path()."public/runtime/app/db_caches/");
		$GLOBALS['cache']->clear();
		clear_dir_file(get_real_path()."public/runtime/data/");

		//模板页面缓存
		clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");		
		clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");
		@unlink(get_real_path()."public/runtime/app/lang.js");	
		
		//脚本缓存
		clear_dir_file(get_real_path()."public/runtime/statics/");		
			
				
		
}
function clear_dir_file($path)
{
   if ( $dir = opendir( $path ) )
   {
            while ( $file = readdir( $dir ) )
            {
                $check = is_dir( $path. $file );
                if ( !$check )
                {
                    @unlink( $path . $file );                       
                }
                else 
                {
                 	if($file!='.'&&$file!='..')
                 	{
                 		clear_dir_file($path.$file."/");              			       		
                 	} 
                 }           
            }
            closedir( $dir );
            rmdir($path);
            return true;
   }
}


function check_install()
{
	if(!file_exists(get_real_path()."public/install.lock"))
	{
	    clear_cache();
		header('Location:'.APP_ROOT.'/install');
		exit;
	}
}


/**发邮件与短信的示例*/

function send_demo_mail()
{
	if(app_conf("MAIL_ON")==1)
	{		
			//$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg = "测试邮件";
			$msg_data['dest'] = "demo@demo.com";
			$msg_data['send_type'] = 1;
			$msg_data['title'] = "测试邮件";
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = get_gmtime();
			$msg_data['user_id'] = 0;
			$msg_data['is_html'] = 1;
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入

	}
}


function send_demo_sms()
{
	if(app_conf("SMS_ON")==1)
	{
		
			//$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg = "测试短信";
			$msg_data['dest'] = "13333333333";
			$msg_data['send_type'] = 0;
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = get_gmtime();
			$msg_data['user_id'] = 0;
			$msg_data['is_html'] = 0;
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入

	}
}


//utf8 字符串截取
function msubstr($str, $start=0, $length=15, $charset="utf-8", $suffix=true)
{
	if(function_exists("mb_substr"))
    {
        $slice =  mb_substr($str, $start, $length, $charset);
        if($suffix&$slice!=$str) return $slice."…";
    	return $slice;
    }
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix&&$slice!=$str) return $slice."…";
    return $slice;
}


//字符编码转换
if(!function_exists("iconv"))
{	
	function iconv($in_charset,$out_charset,$str)
	{
		require 'libs/iconv.php';
		$chinese = new Chinese();
		return $chinese->Convert($in_charset,$out_charset,$str);
	}
}

//JSON兼容
if(!function_exists("json_encode"))
{	
	function json_encode($data)
	{
		require_once 'libs/json.php';
		$JSON = new JSON();
		return $JSON->encode($data);
	}
}
if(!function_exists("json_decode"))
{	
	function json_decode($data)
	{
		require_once 'libs/json.php';
		$JSON = new JSON();
		return $JSON->decode($data,1);
	}
}

//邮件格式验证的函数
 
function check_email($email){
	$data=array();
	$field_name="电子邮箱";
	if(!preg_match("/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/",$email)){
		$data['status']=0;
		$data['info']=$field_name."格式不正确";
	}
	else{
		$data['status']=1;
	}
	return $data;
}
//验证手机号码
function check_mobile($mobile)
{
	if(!empty($mobile) && !preg_match("/^1[34578]\d{9}$/",$mobile))
	{
		return false;
	}
	else
	return true;
}

//验证手机号码
function check_mobile_web($mobile,$is_require)
{
	$data=array();
	$data['data']="";
	$field_name="手机号码";
	if($is_require){
		if(empty($mobile))
		{
			$data['status']=0;
			$data['info']=$field_name."不能为空";
		}elseif(!preg_match("/^1[34578]\d{9}$/",$mobile)){
			$data['status']=0;
			$data['info']=$field_name."格式不正确";
		}
		else{
			$data['status']=1;
		}
	}else{
		if(!empty($mobile)&&!preg_match("/^1[34578]\d{9}$/",$mobile))
		{
			$data['status']=0;
			$data['info']=$field_name."格式不正确";
		}
		else{
			$data['status']=1;
		}
	}	
	return $data;
}
//验证机构电话
function check_tel($tel){
	$data=array();
	$data['data']="";
	$field_name="联系电话";
	if(!preg_match("/^1[34578]\d{9}$/",$tel)&&!preg_match("/^([0-9]{3,4}-)?[0-9]{7,8}$/",$tel)){
		$data['status']=0;
		$data['info']=$field_name."格式不正确";
	}
	else{
		$data['status']=1;
	}
	return $data;
}
//验证长度
function check_len($str,$max_len,$is_require,$field_name)
{
	$data=array();
	$data['data']="";
	$len=utf8_strlen($str);
	if($is_require==1){
		if($len==0){
			$data['status']=0;
			$data['info']=$field_name."不能为空";
		}elseif($len>$max_len){
			$data['status']=0;
			$data['info']="请保证".$field_name."内容在".$max_len."个字以内";
		}else{
			$data['status']=1;
		}
	}else{
		if($len>$max_len){
			$data['status']=0;
			$data['info']="请保证".$field_name."内容在".$max_len."个字以内";
		}else{
			$data['status']=1;
		}
	}
	return $data;
		
}
// 计算中文字符串长度 utf-8
function utf8_strlen($string = null) {
	// 将字符串分解为单元
	$string=str_replace("\r\n", "\n", $string);
	preg_match_all("/./us", $string, $match);
	// 返回单元个数
	return count($match[0]);
}
//验证密码
function check_pwd($pwd){
	$data=array();
	$data['data']="";
	$field_name="密码";
	if(empty($pwd))
	{
		$data['status']=0;
		$data['info']=$field_name."不能为空";
// 	}elseif(!preg_match("/^([A-Z]+[a-z]+[0-9]+){6,12}$/",$pwd)){
// 	}elseif(!preg_match("/^(?=.*[0-9].*)(?=.*[A-Z].*)(?=.*[a-z].*).{6,12}$/",$pwd)){
	}elseif(!preg_match("/^(?=.*[0-9].*)(?=.*[A-Za-z].*)[0-9a-zA-Z]{6,12}$/",$pwd)){
		$data['status']=0;
		$data['info']=$field_name."密码应由6-12位数字和字母组成";
	}
	else{
		$data['status']=1;
	}
	return $data;
	
}
//验证网址
function check_url($str){
	$data=array();
	$field_name="在线网址";
	if(!preg_match("/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\%&=]*)?$/i",$str)){
		$data['status']=0;
		$data['info']=$field_name."格式不正确";
	}
	else{
		$data['status']=1;
	}
	return $data;
}
//验证地区
function check_region($region,$field_name){
	$data=array();
	$data['data']="";
	 
	if(empty($region)){
		$data['status']=0;
		$data['info']=$field_name."不能为空";
	}else{
		$data['status']=1;
	}
	return $data;
}
//验证验证码
function check_verify_code($code){
	$data=array();
	$data['data']="";
	$field_name="验证码";
	if(empty($code))
	{
		$data['status']=0;
		$data['info']=$field_name."不能为空";
	}elseif(!preg_match("/^\d{6}$/",$code)){
		$data['status']=0;
		$data['info']=$field_name."格式不正确";
	}
	else{
		$data['status']=1;
	}
	return $data;
}
//跳转
function app_redirect($url,$time=0,$msg='')
{
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);    
    if(empty($msg))
        $msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if(0===$time) {
        	if(substr($url,0,1)=="/")
        	{        		
        		header("Location:".get_domain().$url);
        	}
        	else
        	{
        		header("Location:".$url);
        	}
            
        }else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0)
            $str   .=   $msg;
        exit($str);
    }
}



/**
 * 验证访问IP的有效性
 * @param ip地址 $ip_str
 * @param 访问页面 $module
 * @param 时间间隔 $time_span
 * @param 数据ID $id
 */
function check_ipop_limit($ip_str,$module,$time_span=0,$id=0)
{
		$op = es_session::get($module."_".$id."_ip");
    	if(empty($op))
    	{
    		$check['ip']	=	 get_client_ip();
    		$check['time']	=	get_gmtime();
    		es_session::set($module."_".$id."_ip",$check);    		
    		return true;  //不存在session时验证通过
    	}
    	else 
    	{   
    		$check['ip']	=	 get_client_ip();
    		$check['time']	=	get_gmtime();    
    		$origin	=	es_session::get($module."_".$id."_ip");
    		
    		if($check['ip']==$origin['ip'])
    		{
    			if($check['time'] - $origin['time'] < $time_span)
    			{
    				return false;
    			}
    			else 
    			{
    				es_session::set($module."_".$id."_ip",$check);
    				return true;  //不存在session时验证通过    				
    			}
    		}
    		else 
    		{
    			es_session::set($module."_".$id."_ip",$check);
    			return true;  //不存在session时验证通过
    		}
    	}
    }

function gzip_out($content)
{
	header("Content-type: text/html; charset=utf-8");
    header("Cache-control: private");  //支持页面回跳
	$gzip = app_conf("GZIP_ON");
	if( intval($gzip)==1 )
	{
		if(!headers_sent()&&extension_loaded("zlib")&&preg_match("/gzip/i",$_SERVER["HTTP_ACCEPT_ENCODING"]))
		{
			$content = gzencode($content,9);	
			header("Content-Encoding: gzip");
			header("Content-Length: ".strlen($content));
			echo $content;
		}
		else
		echo $content;
	}else{
		echo $content;
	}
	
}


/**
	 * 保存图片
	 * @param array $upd_file  即上传的$_FILES数组
	 * @param array $key $_FILES 中的键名 为空则保存 $_FILES 中的所有图片
	 * @param string $dir 保存到的目录
	 * @param array $whs
	 	可生成多个缩略图
		数组 参数1 为宽度，
			 参数2为高度，
			 参数3为处理方式:0(缩放,默认)，1(剪裁)，
			 参数4为是否水印 默认为 0(不生成水印)
	 	array(
			'thumb1'=>array(300,300,0,0),
			'thumb2'=>array(100,100,0,0),
			'origin'=>array(0,0,0,0),  宽与高为0为直接上传
			...
		)，
	 * @param array $is_water 原图是否水印
	 * @return array
	 	array(
			'key'=>array(
				'name'=>图片名称，
				'url'=>原图web路径，
				'path'=>原图物理路径，
				有略图时
				'thumb'=>array(
					'thumb1'=>array('url'=>web路径,'path'=>物理路径),
					'thumb2'=>array('url'=>web路径,'path'=>物理路径),
					...
				)
			)
			....
		)
	 */
//$img = save_image_upload($_FILES,'avatar','temp',array('avatar'=>array(300,300,1,1)),1);
function save_image_upload($upd_file, $key='',$dir='temp', $whs=array(),$is_water=false,$need_return = false)
{
		require_once APP_ROOT_PATH."system/utils/es_imagecls.php";
		$image = new es_imagecls();
		$image->max_size = intval(app_conf("MAX_IMAGE_SIZE"));
		
		$list = array();

		if(empty($key))
		{
			foreach($upd_file as $fkey=>$file)
			{
				$list[$fkey] = false;
				$image->init($file,$dir);
				if($image->save())
				{
					$list[$fkey] = array();
					$list[$fkey]['url'] = $image->file['target'];
					$list[$fkey]['path'] = $image->file['local_target'];
					$list[$fkey]['name'] = $image->file['prefix'];
				}
				else
				{
					if($image->error_code==-105)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'上传的图片太大');
						}
						else
						echo "上传的图片太大";
					}
					elseif($image->error_code==-104||$image->error_code==-103||$image->error_code==-102||$image->error_code==-101)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'非法图像');
						}
						else
						echo "非法图像";
					}
					exit;
				}
			}
		}
		else
		{
			$list[$key] = false;
			$image->init($upd_file[$key],$dir);
			if($image->save())
			{
				$list[$key] = array();
				$list[$key]['url'] = $image->file['target'];
				$list[$key]['path'] = $image->file['local_target'];
				$list[$key]['name'] = $image->file['prefix'];
			}
			else
				{
					if($image->error_code==-105)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'上传的图片太大');
						}
						else
						echo "上传的图片太大";
					}
					elseif($image->error_code==-104||$image->error_code==-103||$image->error_code==-102||$image->error_code==-101)
					{
						if($need_return)
						{
							return array('error'=>1,'message'=>'非法图像');
						}
						else
						echo "非法图像";
					}
					exit;
				}
		}

		$water_image = APP_ROOT_PATH.app_conf("WATER_MARK");
		$alpha = app_conf("WATER_ALPHA");
		$place = app_conf("WATER_POSITION");
		
		foreach($list as $lkey=>$item)
		{
				//循环生成规格图
				foreach($whs as $tkey=>$wh)
				{
					$list[$lkey]['thumb'][$tkey]['url'] = false;
					$list[$lkey]['thumb'][$tkey]['path'] = false;
					if($wh[0] > 0 || $wh[1] > 0)  //有宽高度
					{
						$thumb_type = isset($wh[2]) ? intval($wh[2]) : 0;  //剪裁还是缩放， 0缩放 1剪裁
						if($thumb = $image->thumb($item['path'],$wh[0],$wh[1],$thumb_type))
						{
							$list[$lkey]['thumb'][$tkey]['url'] = $thumb['url'];
							$list[$lkey]['thumb'][$tkey]['path'] = $thumb['path'];
							if(isset($wh[3]) && intval($wh[3]) > 0)//需要水印
							{
								$paths = pathinfo($list[$lkey]['thumb'][$tkey]['path']);
								$path = $paths['dirname'];
				        		$path = $path."/origin/";
				        		if (!is_dir($path)) { 
						             @mkdir($path);
						             @chmod($path, 0777);
					   			}   	    
				        		$filename = $paths['basename'];
								@file_put_contents($path.$filename,@file_get_contents($list[$lkey]['thumb'][$tkey]['path']));      
								$image->water($list[$lkey]['thumb'][$tkey]['path'],$water_image,$alpha, $place);
							}
						}
					}
				}
			if($is_water)
			{
				$paths = pathinfo($item['path']);
				$path = $paths['dirname'];
        		$path = $path."/origin/";
        		if (!is_dir($path)) { 
		             @mkdir($path);
		             @chmod($path, 0777);
	   			}   	    
        		$filename = $paths['basename'];
				@file_put_contents($path.$filename,@file_get_contents($item['path']));        		
				$image->water($item['path'],$water_image,$alpha, $place);
			}
		}			
		return $list;
}

function empty_tag($string)
{	
	$string = preg_replace(array("/\[img\]\d+\[\/img\]/","/\[[^\]]+\]/"),array("",""),$string);
	if(trim($string)=='')
	return $GLOBALS['lang']['ONLY_IMG'];
	else 
	return $string;
	//$string = str_replace(array("[img]","[/img]"),array("",""),$string);
}



/**
 * utf8字符转Unicode字符
 * @param string $char 要转换的单字符
 * @return void
 */
function utf8_to_unicode($char)
{
	switch(strlen($char))
	{
		case 1:
			return ord($char);
		case 2:
			$n = (ord($char[0]) & 0x3f) << 6;
			$n += ord($char[1]) & 0x3f;
			return $n;
		case 3:
			$n = (ord($char[0]) & 0x1f) << 12;
			$n += (ord($char[1]) & 0x3f) << 6;
			$n += ord($char[2]) & 0x3f;
			return $n;
		case 4:
			$n = (ord($char[0]) & 0x0f) << 18;
			$n += (ord($char[1]) & 0x3f) << 12;
			$n += (ord($char[2]) & 0x3f) << 6;
			$n += ord($char[3]) & 0x3f;
			return $n;
	}
}

/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @param string $depart 分隔,默认为空格为单字
 * @return string
 */
function str_to_unicode_word($str,$depart=' ')
{
	$arr = array();
	$str_len = mb_strlen($str,'utf-8');
	for($i = 0;$i < $str_len;$i++)
	{
		$s = mb_substr($str,$i,1,'utf-8');
		if($s != ' ' && $s != '　')
		{
			$arr[] = 'ux'.utf8_to_unicode($s);
		}
	}
	return implode($depart,$arr);
}


/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @return string
 */
function str_to_unicode_string($str)
{
	$string = str_to_unicode_word($str,'');
	return $string;
}

//分词
function div_str($str)
{
	require_once APP_ROOT_PATH."system/libs/words.php";
	$words = words::segment($str);
	$words[] = $str;	
	return $words;
}



/**
 * 
 * @param $tag  //要插入的关键词
 * @param $table  //表名
 * @param $id  //数据ID
 * @param $field		// tag_match/name_match/cate_match/locate_match
 */
function insert_match_item($tag,$table,$id,$field)
{
	if($tag=='')
	return;
	
	$unicode_tag = str_to_unicode_string($tag);
	$sql = "select count(*) from ".DB_PREFIX.$table." where match(".$field.") against ('".$unicode_tag."' IN BOOLEAN MODE) and id = ".$id;	
	$rs = $GLOBALS['db']->getOne($sql);
	if(intval($rs) == 0)
	{
		$match_row = $GLOBALS['db']->getRow("select * from ".DB_PREFIX.$table." where id = ".$id);
		if($match_row[$field]=="")
		{
				$match_row[$field] = $unicode_tag;
				$match_row[$field."_row"] = $tag;
		}
		else
		{
				$match_row[$field] = $match_row[$field].",".$unicode_tag;
				$match_row[$field."_row"] = $match_row[$field."_row"].",".$tag;
		}
		$GLOBALS['db']->autoExecute(DB_PREFIX.$table, $match_row, $mode = 'UPDATE', "id=".$id, $querymode = 'SILENT');	
		
	}	
}
/**同步索引的示例
function syn_supplier_match($supplier_id)
{
	$supplier = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id = ".$supplier_id);
	if($supplier)
	{
		$supplier['name_match'] = "";
		$supplier['name_match_row'] = "";
		$GLOBALS['db']->autoExecute(DB_PREFIX."supplier", $supplier, $mode = 'UPDATE', "id=".$supplier_id, $querymode = 'SILENT');	
		
		
		//同步名称
		$name_arr = div_str(trim($supplier['name'])); 
		foreach($name_arr as $name_item)
		{
			insert_match_item($name_item,"supplier",$supplier_id,"name_match");
		}
		
	}
}
*/

//封装url

function url($route="index",$param=array())
{
	$key = md5("URL_KEY_".$route.serialize($param));
	
	if(isset($GLOBALS[$key]))
	{
		$url = $GLOBALS[$key];
		return $url;
	}
	
	$url = load_dynamic_cache($key);
	if($url!==false)
	{
		$GLOBALS[$key] = $url;
		return $url;
	}
	
	$route_array = explode("#",$route);
	
    // 解析参数
    if(is_string($param)) { // aaa=1&bbb=2 转换成数组
        parse_str($param,$param);
    }elseif(!is_array($vars)){
        $vars = array();
    }
    
	$module = strtolower(trim($route_array[0]));
	$action = strtolower(trim($route_array[1]));

	if(!$module||$module=='index')$module="";
	if(!$action||$action=='index')$action="";
	
	if(app_conf("URL_MODEL")==0)
	{
	//原始模式
		$url = APP_ROOT."/index.php";
		if($module!=''||$action!=''||count($param)>0) //有后缀参数
		{
			$url.="?";
		}
	
		if($module&&$module!='')
		$url .= "ctl=".$module."&";
		if($action&&$action!='')
		$url .= "act=".$action."&";
		if(count($param)>0)
		{
			foreach($param as $k=>$v)
			{
				if($k&&$v)
				$url =$url.$k."=".urlencode($v)."&";
			}
		}
		if(substr($url,-1,1)=='&'||substr($url,-1,1)=='?') $url = substr($url,0,-1);
		$GLOBALS[$key] = $url;
		set_dynamic_cache($key,$url);
		return $url;
	}
	else
	{
		//重写的默认
		$url = APP_ROOT."/";

		if($module&&$module!='')
		$url .= $module."/";
		if($action&&$action!='')
		$url .= $action."/";
		
		if(count($param)>0)
		{
			foreach($param as $k=>$v)
			{
// 				$url = $url.$k."-".urlencode($v)."-";
				$url .= urlencode($v)."/";
			}
		}
		
		$route = $module."#".$action;
		switch ($route)
		{
				case "xxx":
					break;
				default:
					break;
		}
		
// 		if(substr($url,-1,1)=='/'||substr($url,-1,1)=='-') $url = substr($url,0,-1);
        if($action && count($param) == 0){
    		$url = rtrim($url,'/');
        }
// 		if(!strstr($url,'about')) $url=preg_replace('/(\d+)/','${1}/',$url);
//  		$replace = array('id-','home','home//','help','help//','introduce','introduce//','deals','deals//','investors','investors//','download','message','ideal','ideal//');
// 		$subject =array('','home/','home/','help/','help/','introduce/','introduce/','deals/','deals/','investors/','investors/','download/','message/','ideal/','ideal/');
// 		$url  = str_replace($replace, $subject, $url);
		
// 		if($url=='')$url="/";
		$GLOBALS[$key] = $url;
		set_dynamic_cache($key,$url);
		return $url;
	}
	
	
}


function unicode_encode($name) {//to Unicode
    $name = iconv('UTF-8', 'UCS-2', $name);
    $len = strlen($name);
    $str = '';
    for($i = 0; $i < $len - 1; $i = $i + 2) {
        $c = $name[$i];
        $c2 = $name[$i + 1];
        if (ord($c) > 0) {// 两个字节的字
            $cn_word = '\\'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
            $str .= strtoupper($cn_word);
        } else {
            $str .= $c2;
        }
    }
    return $str;
}

function unicode_decode($name) {//Unicode to
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches)) {
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++) {
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0) {
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code).chr($code2);
                $c = iconv('UCS-2', 'UTF-8', $c);
                $name .= $c;
            } else {
                $name .= $str;
            }
        }
    }
    return $name;
}


//载入动态缓存数据
function load_dynamic_cache($name)
{
	if(isset($GLOBALS['dynamic_cache'][$name]))
	{
		return $GLOBALS['dynamic_cache'][$name];
	}
	else
	{
		return false;
	}
}

function set_dynamic_cache($name,$value)
{
	if(!isset($GLOBALS['dynamic_cache'][$name]))
	{
		if(count($GLOBALS['dynamic_cache'])>MAX_DYNAMIC_CACHE_SIZE)
		{
			array_shift($GLOBALS['dynamic_cache']);
		}
		$GLOBALS['dynamic_cache'][$name] = $value;		
	}
}

function load_auto_cache($key,$param=array())
{
	require_once APP_ROOT_PATH."system/libs/auto_cache.php";
	$file =  APP_ROOT_PATH."system/auto_cache/".$key.".auto_cache.php";
	if(file_exists($file))
	{
		require_once $file;
		$class = $key."_auto_cache";
		$obj = new $class;
		$result = $obj->load($param);
	}
	else
	$result = false;
	return $result;
}

function rm_auto_cache($key,$param=array())
{
	require_once APP_ROOT_PATH."system/libs/auto_cache.php";
	$file =  APP_ROOT_PATH."system/auto_cache/".$key.".auto_cache.php";
	if(file_exists($file))
	{
		require_once $file;
		$class = $key."_auto_cache";
		$obj = new $class;
		$obj->rm($param);
	}
}


function clear_auto_cache($key)
{
	require_once APP_ROOT_PATH."system/libs/auto_cache.php";
	$file =  APP_ROOT_PATH."system/auto_cache/".$key.".auto_cache.php";
	if(file_exists($file))
	{
		require_once $file;
		$class = $key."_auto_cache";
		$obj = new $class;
		$obj->clear_all();
	}
}


/*ajax返回*/
function ajax_return($data)
{
		header("Content-Type:text/html; charset=utf-8");
        echo(json_encode($data));
        exit;	
}



function is_animated_gif($filename){
 $fp=fopen($filename, 'rb');
 $filecontent=fread($fp, filesize($filename));
 fclose($fp);
 return strpos($filecontent,chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0')===FALSE?0:1;
}



function update_sys_config()
{
	$filename = APP_ROOT_PATH."public/sys_config.php";
	if(!file_exists($filename))
	{
		//定义DB
		require APP_ROOT_PATH.'system/db/db.php';
		$dbcfg = require APP_ROOT_PATH."public/db_config.php";
		define('DB_PREFIX', $dbcfg['DB_PREFIX']); 
		if(!file_exists(APP_ROOT_PATH.'public/runtime/app/db_caches/'))
			mkdir(APP_ROOT_PATH.'public/runtime/app/db_caches/',0777);
		$pconnect = false;
		$db = new mysql_db($dbcfg['DB_HOST'].":".$dbcfg['DB_PORT'], $dbcfg['DB_USER'],$dbcfg['DB_PWD'],$dbcfg['DB_NAME'],'utf8',$pconnect);
		//end 定义DB

		$sys_configs = $db->getAll("select * from ".DB_PREFIX."conf");
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach($sys_configs as $k=>$v)
		{
			$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
		}
		$config_str.=");\n ?>";	
		file_put_contents($filename,$config_str);
		$url = APP_ROOT."/";
		app_redirect($url);
	}
}


function gen_qrcode($str,$size = 5)
{

	require_once APP_ROOT_PATH."system/phpqrcode/qrlib.php";

	$root_dir = APP_ROOT_PATH."public/images/qrcode/";
 	if (!is_dir($root_dir)) {
            @mkdir($root_dir);               
            @chmod($root_dir, 0777);
     }
     
     $filename = md5($str."|".$size);
     $hash_dir = $root_dir. '/c' . substr(md5($filename), 0, 1)."/";
     if (!is_dir($hash_dir))
     {
        @mkdir($hash_dir);
        @chmod($hash_dir, 0777);
     }   
	
	$filesave = $hash_dir.$filename.'.png';

	if(!file_exists($filesave))
	{
		QRcode::png($str, $filesave, 'Q', $size, 2); 
	}	
	return APP_ROOT."/public/images/qrcode/c". substr(md5($filename), 0, 1)."/".$filename.".png";       
}

function format_price($v)
{
	return "¥".number_format($v,2);
}

function syn_deal($deal_id)
{
	$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
	if($deal_info)
	{
		$deal_info['comment_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_comment where deal_id = ".$deal_info['id']." and log_id = 0"));
		$deal_info['intend_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_support_log where deal_id = ".$deal_info['id']));
		$deal_info['focus_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_focus_log where deal_id = ".$deal_info['id']));
		$deal_info['view_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_visit_log where deal_id = ".$deal_info['id']));
		$deal_info['support_amount'] = doubleval($GLOBALS['db']->getOne("select sum(price) from ".DB_PREFIX."deal_support_log where deal_id = ".$deal_info['id']));
	 
		$deal_info['pay_amount'] = ($deal_info['support_amount']*(1-app_conf("PAY_RADIO")))+$deal_info['delivery_fee_amount'];
		

		if($deal_info['support_amount']>=$deal_info['pe_amount_plan'])
		{
			$deal_info['is_success'] = 1;
		}
		else
		{
			$deal_info['is_success'] = 0;
		}
		
		$deal_info['tags_match'] = "";
		$deal_info['tags_match_row'] = "";
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal", $deal_info, $mode = 'UPDATE', "id=".$deal_info['id'], $querymode = 'SILENT');	
		
		$tags_arr = preg_split("/[, ]/",$deal_info["tags"]);

		foreach($tags_arr as $tgs){
			if(trim($tgs)!="")
			insert_match_item(trim($tgs),"deal",$deal_info['id'],"tags_match");
		}
		
		$name_arr = div_str($deal_info['name']);
		foreach($name_arr as $name_item){
			if(trim($name_item)!="")
			insert_match_item(trim($name_item),"deal",$deal_info['id'],"name_match");
		}

	}


}



//发密码验证邮件
function send_user_password_mail($user_id)
{

		$verify_code = rand(111111,999999);
		$GLOBALS['db']->query("update ".DB_PREFIX."user set password_verify = '".$verify_code."' where id = ".$user_id);
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);			
		if($user_info)
		{
			$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_USER_PASSWORD'");
			$tmpl_content=  $tmpl['content'];
			$user_info['password_url'] = get_domain().url("settings#password", array("code"=>$user_info['password_verify'],"id"=>$user_info['id']));			
			$GLOBALS['tmpl']->assign("user",$user_info);
			$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
			$msg_data['dest'] = $user_info['email'];
			$msg_data['send_type'] = 1;
			$msg_data['title'] = "重置密码";
			$msg_data['content'] = addslashes($msg);
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = get_gmtime();
			$msg_data['user_id'] = $user_info['id'];
			$msg_data['is_html'] = $tmpl['is_html'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
		}

}

function strim($str)
{
	return quotes(htmlspecialchars(trim($str)));
}
function btrim($str)
{
	return quotes(trim($str));
}
function valid_tag($str)
{
	
	return preg_replace("/<(?!div|ol|ul|li|sup|sub|span|br|img|p|h1|h2|h3|h4|h5|h6|\/div|\/ol|\/ul|\/li|\/sup|\/sub|\/span|\/br|\/img|\/p|\/h1|\/h2|\/h3|\/h4|\/h5|\/h6|blockquote|\/blockquote|strike|\/strike|b|\/b|i|\/i|u|\/u)[^>]*>/i","",$str);
}


//返回array: status:0:未支付 1:已支付(过期) 2:已支付(无库存) 3:成功  money:剩余需支付金额
function pay_order($order_id)
{
	require_once APP_ROOT_PATH."system/libs/user.php";
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
	if($order_info['credit_pay']+$order_info['online_pay']>=$order_info['total_price']) //订单已成功支付
	{
		
		if($order_info['credit_pay']+$order_info['online_pay']>$order_info['total_price'])
		{
			$more_money = $order_info['credit_pay']+$order_info['online_pay'] - $order_info['total_price'];
			modify_account(array("money"=>$more_money),$order_info['user_id'],$order_info['deal_name']."超额支付，转存入会员帐户");
		}
		
		$order_info['pay_time'] = get_gmtime();
		$GLOBALS['db']->query("update ".DB_PREFIX."deal set intend_count = intend_count + 1,support_amount = support_amount + ".$order_info['deal_price'].",pay_amount = pay_amount + ".$order_info['total_price'].",delivery_fee_amount = delivery_fee_amount + ".$order_info['delivery_fee']." where id = ".$order_info['deal_id']." and is_effect = 1 and is_delete = 0 and begin_time < ".get_gmtime()." and (end_time > ".get_gmtime()." or end_time = 0)");
		if($GLOBALS['db']->affected_rows()>0)
		{
			//记录支持日志
			$support_log['deal_id'] = $order_info['deal_id'];
			$support_log['user_id'] = $order_info['user_id'];
			$support_log['create_time'] = get_gmtime();
			$support_log['price'] = $order_info['deal_price'];
			$support_log['deal_item_id'] = $order_info['deal_item_id'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_support_log",$support_log);
			$support_log_id = intval($GLOBALS['db']->insert_id());
			
			
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_item set intend_count = intend_count + 1,support_amount = support_amount +".$order_info['deal_price']." where (intend_count + 1 <= limit_user or limit_user = 0) and id = ".$order_info['deal_item_id']);
			if($GLOBALS['db']->affected_rows()>0)
			{
				$result['status'] = 3;
				$order_info['order_status'] = 3;	

				
				$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$order_info['deal_id']." and is_effect = 1 and is_delete = 0");
				//下单项目成功，准备加入准备队列
				if($deal_info['is_success'] == 0)
				{
					//未成功的项止准备生成队列
					$notify['user_id'] = $GLOBALS['user_info']['id'];
					$notify['deal_id'] = $deal_info['id'];
					$notify['create_time'] = get_gmtime();
					$GLOBALS['db']->autoExecute(DB_PREFIX."user_deal_notify",$notify,"INSERT","","SILENT");

				}
				
				//更新用户的支持数
				$GLOBALS['db']->query("update ".DB_PREFIX."user set intend_count = intend_count + 1 where id = ".$order_info['user_id']);
				//同步项目状态
				syn_deal_status($order_info['deal_id']);				
				syn_deal($order_info['deal_id']);
				
				
				
			}
			else
			{
				$result['status'] = 2;
				$order_info['order_status'] = 2;
				$order_info['is_refund'] =1;
				$GLOBALS['db']->query("update ".DB_PREFIX."deal set intend_count = intend_count - 1,support_amount = support_amount - ".$order_info['deal_price'].",pay_amount = pay_amount - ".$order_info['total_price'].",delivery_fee_amount = delivery_fee_amount - ".$order_info['delivery_fee']." where id = ".$order_info['deal_id']);
				$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_support_log where id = ".$support_log_id);
				modify_account(array("money"=>$order_info['total_price']),$order_info['user_id'],$order_info['deal_name']."限额已满，转存入会员帐户");
			}
		}
		else
		{
			$result['status'] =1;
			$order_info['order_status'] =1;
			$order_info['is_refund'] =1;
			modify_account(array("money"=>$order_info['total_price']),$order_info['user_id'],$order_info['deal_name']."已过期，转存入会员帐户");
		}
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set order_status = ".intval($order_info['order_status']).",pay_time = ".$order_info['pay_time'].",is_refund = ".$order_info['is_refund']." where id = ".$order_info['id']);
		
	}
	else
	{
		$result['status'] = 0;
		$result['money'] = $order_info['total_price'] - $order_info['online_pay'] - $order_info['credit_pay'];
	}
	return $result;
}

function syn_deal_status($deal_id)
{
	$GLOBALS['db']->query("update ".DB_PREFIX."deal set is_success = 1,success_time = ".get_gmtime()." where id = ".$deal_id." and is_effect=  1 and is_delete = 0 and support_amount >= pe_amount_plan and begin_time <".get_gmtime()." and (end_time > ".get_gmtime()." or end_time = 0)");
	if($GLOBALS['db']->affected_rows()>0)
	{		
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_success = 1 where deal_id = ".$deal_id);
		//项目成功，加入项目成功的待发队列
		$deal_notify['deal_id'] = $deal_id;
		$deal_notify['create_time'] = get_gmtime();
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_notify",$deal_notify,"INSERT","","SILENT");	
	}
}

//同步到微博
function syn_weibo($data)
{
	$api_list = $GLOBALS['db']->getAllCached("select * from ".DB_PREFIX."api_login where is_weibo = 1");
	foreach($api_list as $k=>$v)
	{
		if($GLOBALS['user_info'][strtolower($v['class_name'])."_id"]==""||$GLOBALS['user_info'][strtolower($v['class_name'])."_token"]=="")
		{
			unset($api_list[$k]);
		}
		else
		{
			$class_name = $v['class_name']."_api";
			require_once APP_ROOT_PATH."system/api_login/".$class_name.".php";
			$o = new $class_name($v);
			$o->send_message($data);
		}
	}
}


//发送给用户通知
function send_notify($user_id,$content,$url_route,$url_param)
{
	$notify = array();
	$notify_user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($user_id));
	if($notify_user)
	{
		$notify['user_id'] = $user_id;
		$notify['log_info'] = $content;
		$notify['log_time'] = get_gmtime();
		$notify['url_route'] = $url_route;
		$notify['url_param'] = $url_param;
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."user_notify",$notify,"INSERT","","SILENT");
	}
	
}

// 定义七牛图片引用地址
define('IMG_DOMAIN', "http://img.cisdaq.com/") ; 
define('BP_DOMAIN', "http://bp.cisdaq.com/") ; 
define('ACCESSKEY', "5CQmagmT27DNXYhsmVewntOpd9VLGD8sC5c02ptg") ; 
define('SECRETKEY', "lQm-akaIb3JOS-WAlBycTXF_95hx7JEd9s88ARk5") ; 

// define('BP_URL', "http://www.cisdaq.com/bp_viewer/get_bp1.php?key=") ;
/**
	 * 上传文件到七牛服务器
	 *
	 * @return array
	 */
//   function uploadQiniu($file,$bucket)
// 	{ 
// 		require_once APP_ROOT_PATH."api/qiniu/vendor/autoload.php";
// 		include  APP_ROOT_PATH."api/qiniu/vendor/qiniu/php-sdk/src/Qiniu/Auth.php"; 

// 		$auth = new Auth(ACCESSKEY, SECRETKEY); 
// 		//$bucket = 'zhcxtest';
// 		$token = $auth->uploadToken($bucket);
// 		include  APP_ROOT_PATH."api/qiniu/vendor/qiniu/php-sdk/src/Qiniu/Storage/UploadManager.php";
// 		$uploadMgr = new UploadManager();
// 		//$file="D:\wamp\wamp\www\svnrepos\admin_web\admin\Lib\Action\DealAction.class.php";
// 		list($ret, $err) = $uploadMgr->putFile($token,null, $file);//var_dump($err);指定上传文件名
// 		if ($err === null) {
// 			return $ret;
// 		} 
// 	}

// function getQiniuPathJs($key,$type)
// {

//     if($type=='bp'){
//         $baseUrl = BP_DOMAIN.$key;
//         require_once APP_ROOT_PATH."api/qiniu/vendor/autoload.php";
//         include_once  APP_ROOT_PATH."api/qiniu/vendor/qiniu/php-sdk/src/Qiniu/Auth.php";
//         $auth = new Auth(ACCESSKEY, SECRETKEY);
//         //$baseUrl = 'http://7xju4h.com1.z0.glb.clouddn.com/'.$key.'?imageView2/1/w/35/h/35';
//         $baseUrl = $auth->privateDownloadUrl($baseUrl);

//     }else{
//         $baseUrl = IMG_DOMAIN.$key;
//     }

//     return $baseUrl;
// }

function getQiniuPath_key($key){
	return BP_DOMAIN.$key;
}

// URLSafeBase64Encode
function Qiniu_Encode($str){
    $find       = array('+', '/');
    $replace    = array('-', '_');
    return str_replace($find, $replace, base64_encode($str));
}

function getQiniuPath_android($url) {

    $duetime        = time() + 86400;//下载凭证有效时间
    $downloadUrl    = $url . '&e=' . $duetime;
    $encodedSign    = Qiniu_Encode ( hash_hmac ( 'sha1', $downloadUrl, SECRETKEY, true ) );
    $token          = ACCESSKEY. ':' . $encodedSign;
    $url            = $downloadUrl . '&token=' . $token;
    
    return $url;
}
 
// 获取七牛文件地址
function getQiniuPath($key,$type) {//$info里面的url

    if($type == 'bp'){
        $url            = BP_DOMAIN.$key;
        $duetime        = NOW_TIME + 86400;//下载凭证有效时间
        $downloadUrl    = $url . '?e=' . $duetime;
        $encodedSign    = Qiniu_Encode ( hash_hmac ( 'sha1', $downloadUrl, SECRETKEY, true ) );
        $token          = ACCESSKEY. ':' . $encodedSign;
        $url            = $downloadUrl . '&token=' . $token;
    }else{
    	$url            = IMG_DOMAIN.$key;
    }
    return $url;
}

/*
 * 获取发送短信模板
 * @param string $source 来源
 * @param array $data 数组形式传入，模板变量依次替换
 * @author sunerju
 * @date 2016-08-12
 */
function getSendSmsTemplate($source,$data){
    switch($source){
        case 'admin_reset_password':
            $tpl = "尊敬的投资人，您的登录密码已重置成功，新的密码为{$data[0]}，请登录后及时修改密码，谢谢！";
            break;
        case 'admin_user_audit_ok':
            $tpl = "恭喜您已经完成认证！可完整体验磁斯达克为您带来的专业服务。";
            break;
        case 'admin_user_audit_fail':
            $tpl = "很遗憾您的会员认证被驳回，原因如下[{$data[0]}]如有疑问，联系磁斯达克客服400-862-8262。";
            break;
        case 'admin_user_add':
            $tpl = "尊敬的投资人，您的磁斯达克账号已经开通，用户名：{$data[0]}，密码：{$data[1]}，请登录后及时修改密码，感谢您的支持！谢谢！";
            break;
        case 'admin_user_add_cert':
            $tpl = "尊敬的投资人，您的会员认证申请已经通过！";
            break;
        case 'admin_fund_bind_investor':
            $tpl = "尊敬的投资人，恭喜您已成功认购{$data[0]}基金，您的购买金额为{$data[1]}万，占比{$data[2]}%，您可通过登录磁斯达克APP查看投资详情。感谢您对磁斯达克平台的信任与支持，谢谢！";
            break;
        case 'admin_finance_finish':
            $tpl = "尊敬的投资人，您购买的{$data[0]}基金已成功投资{$data[1]}项目{$data[2]}，您可以随时登录磁斯达克查看项目的最新资讯及收益情况，谢谢！";
            break;
        case 'code':
            $tpl = "尊敬的用户，您的验证码为：{$data[0]}，请勿告知他人！";
            break;
        default:
            $tpl = "";
    }
    
    return $tpl;
}

/*
 * 获取发送短信类型（入表记录日志的时候使用）
 * @param string $source 来源
 * @author sunerju
 * @date 2016-08-12
 */
function getSendSmsType($source){
    $typeArr = array(
        'code'                  => 0,       #验证码
        'admin_sms'             => 1,       #后台短信群发
        'admin_reset_password'  => 2,       #重置密码
        'admin_user_add_cert'   => 3,       #新增会员认证通过
        'admin_user_audit'      => 4,       #会员审核
        'admin_fund_bind_investor' => 5,    #基金绑定投资人
        'admin_finance_finish'  => 6,       #融资完成
    );
    return $typeArr[$source];
}


define("NOW_TIME",get_gmtime());
?>