<?php
/*
 * | 初始化引导类
 */

namespace System;

class Url {

	/**
	 * dispose_url
	 * url参数解析函数
	 * @static
	 * @access public
	 */
	static public function dispose_url() {
		$err = C('ERROR');
		if(IS_GET) {
			# 获取其他参数
			$data = $_GET;

			# 处理请求数据
			self::dispose_request($data);

			# 获取API类名
			$api = $data['class'];
                        # 获取调用分组
                        $call_name = $data['name'];
			# 获取API方法 send
			$func = $data['function'];

		}elseif(IS_POST) {
			# 获取其他参数
			$data = $_POST;
			
			# 处理请求数据
			self::dispose_request($data);
			
			# 获取调用分组
			$call_name = $data['name'];

			# 获取API类名
			$api = $data['class'];

			# 获取API方法 send
			$func = $data['function'];
		
		}else{
		    exit;
			//E($err[4], 4);
		}
		
		# 引入相关API文件
		if(file_exists($filename =  CALL_PATH. $call_name. '/'. $api. EXT)) {
			require_once $filename;
		}
			
		# 实例化对象
		$new = '\\Api\\'. $call_name. '\\' .$api;
                $obj = new $new();

		# 判断方法是否存在
        if(!method_exists($obj, $func) && !is_callable(array($obj, $func))) {
			E($err[2], 2);
		}

        # 验证传进来的参数是否存在，矫正参数的顺序
	//	$data = param_verify($obj, $func, $data);
	
		# 调用API
		call_user_func(array($obj, $func), $data['params']);

	}

	/**
	 * dispose_request 
	 * 处理请求数据
	 * @param mixed $data 要处理的数据 引用传值
	 * @access private
	 * @return void
	 *
	 * Key Map:
     *   //请求的Api路径, 目前为三段, 分别为 Api模块名称.ApiClass名称.function名称
     *   func                    Wap.Collect.getList
     *   //Api的执行参数, 使用json_encode进行参数编码
     *   params                  {"uid":11}
     *   //请求的签名码, 参加下方生成说明
	 *   signKey                 029a8978e6cacdd1f272f4f77f3508ee
	 *
	 *	$signKey = md5($params.API_REQUEST_SECKEY.$func);
	 *
	 */

	static public function dispose_request(&$data) {
		# 定义基本数据
		$key = C('SINGN_KEY');
		define('API_REQUEST_SECKEY', $key);
		$func = $data['func'];
		$params = $data['params'];
		
		# 计算验证数据
		$sign = md5($params. API_REQUEST_SECKEY. $func);
		if($sign == $data['signKey']) {

			# 验证通过处理数据
			$data['params'] = json_decode($params,true);
			$method = isset($data['method']) ? $data['method'] : '';
			$func = explode('.', $func);
			$call_name = $func[0];
			$data['type'] = $call_name;
			defined('IS_WAP') || define('IS_WAP', ($method == 'Wap')? true: false);
			defined('IS_WEB') || define('IS_WEB', ($method == 'Web')? true: false);
			defined('IS_APP') || define('IS_APP', ($method == 'App')? true: false);
			$data['name'] = $call_name;
			$data['class'] = $func[1];
			$data['function'] = $func[2];
		}else {
			E($err[10], 10);
		}
		
	}

}

?>
