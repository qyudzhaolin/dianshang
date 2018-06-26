<?php
/*
 * | 初始化引导类
 */

namespace System;

class Start {

	/**
	 * initialize 
	 * 初始化引导函数
	 * @static
	 * @access public
	 */
	
	static public function initialize() {
		# 常规化处理
		spl_autoload_register('System\start::autoload');           # 注册自动加载函数 __autoload
		register_shutdown_function('System\Start::fatalError');		# 致命错误处理函数
		set_error_handler('System\Start::appError');				# 错误处理
		set_exception_handler('System\Start::appException');        # 异常处理
		
		# 加载系统基础文件

		if(file_exists($filename = COMMON_PATH."common.php")) {  # 自定义函数
			include $filename;
		}

		if(file_exists($filename = COMMON_PATH."function.php")) { # 系统公共函数
			include $filename;
		}

		if(file_exists($filename = CONF_PATH."config.php")) {      # 配置文件
			C(include $filename);
		}
		
		if(file_exists($filename = CONF_PATH."env_conf/".RUN_ENV.".php")){
			C(include $filename);
		}

		if(file_exists($filename = CONF_PATH."error.php")) {      # 调用出错配置文件
			C(include $filename);
		}
		
		if(file_exists($filename = CONF_PATH."lua.php")) {		   # lua脚本配置文件
			C(include $filename);
		}
		
		if(START_MODE == 1) { # curl mode
			ob_start();	
			Url::dispose_url();

		} elseif(START_MODE == 2) { # socket tcp mode
			return true;

		} else {
			return false;
		}
	}
	
	/**
	 * 自动加载函数
	 * @access public
	 * @param mixed $class 类名
	 */
	public static function autoload($class) {
		$name = strstr($class, '\\');
		if( !$name ) { # 如果没有位于根目录则定位到 Common 目录下
			$filename = COMMON_PATH. $class;

		} else { # 如果存在命名空间则定位到相对应的目录中
			$filename = APP_PATH. '/' . str_replace('\\', '/', $class);

		}
		$filename = str_replace('\\', '/', $filename) . EXT;
		if( file_exists($filename) ) {
			include $filename;

		}
	}

	/**
	 * 自定义异常处理
	 * @access public
	 * @param mixed $e 异常对象
	 */

	static public function appException($e) {
	    $errstr  = $e->getMessage();
		if(APP_DEBUG) {
		    $errfile = $e->getFile();
		    $errline = $e->getLine();
		    $code    = $e->getCode();
		    $code = $code ? $code : 7;
		    $errorStr = "[$code] $errstr ".$errfile." 第 $errline 行.";
			E($errorStr, 7);
		}
		else {
	        L("####\n". $errstr ."\n". getExceptionTraceAsString($e->getTrace()) .'####', 7, 'exception.txt');
	        $error = C('ERROR');
	        E($error[7], 7);
		}
	}

	/**
	 * 自定义错误处理
	 * @access public
	 * @param int $errno 错误类型
	 * @param string $errstr 错误信息
	 * @param string $errfile 错误文件
	 * @param int $errline 错误行数
	 * @return void
	 */

	static public function appError($errno, $errstr, $errfile, $errline) {
		$default_error = C('ERROR_LIVE');
		$is_error = in_array($errno, $default_error);
		if($is_error) {
			$error = C('ERROR');
			$errorStr = "[$errno] $errstr ".$errfile." 第 $errline 行.";
			ob_end_clean();
			if(APP_DEBUG) {
				E($errorStr, 8);
			} else {
				L($errorStr, 8);
				E($error[8], 8);
			}
	}	
	}	

	/**
	 * fatalError 
	 * 致命错误处理函数
	 * @static
	 * @access public
	 * @return void
	 */
	static public function fatalError() {
		# 判断是否为致命级别错误
		$e = error_get_last();
		if($e) {
			$errno = $e['type'];
			$default_error = C('FATAL_ERROR_LIVE');
			$is_error = in_array($errno, $default_error);
			if($is_error) {
				$file = $e['file'];
				$line = $e['line'];
				$message = $e['message'];
				$errorStr = "[$errno] {$message} ".$file." 第 $line 行.";
				ob_end_clean();
				$error = C('ERROR');
				if(APP_DEBUG) {
					E($errorStr, 6);
				}else {
					L($errorStr, 6);
					E($error[6], 6);
				}
			}
		}
	}
}

?>
