<?php
/*
 * | 调用控制器 系统级别公共方法
 */

namespace System;

use System\MQ\PushMQ;

class Controller extends DBHelperApi{
	
	
	/**
	 * 委托调用层级
	 * @var integer
	 */
	private static  $_invokeLevel = 0;

	/**
	*  队列调用对象
	*/
	protected static $_queueObj = null;

	/**
	*  队列服务器cluster
	*/
	protected static $_queueServer = array();

	protected static $_pushMQ = null;

	# 队列扩展属性
	private $extends_attr = array();
	private $clear_flag;

	/**
	 * 请求处理完毕, 结束当前程序并发送响应结果给客户端
	 * @param mixed $response  	响应结果
	 * @param integer $status 		响应状态
	 * @param array $messageArgs	消息替换参数列表,用于替换配置或者指定的消息内的占位符
	 * @param string $message 		响应消息文本, 用于指定展示给调用者的可视化文本
	 * @param string $script 		是否返回script脚本，值为返回的脚本内容（主要用于解决跨域问题）
	 * @return void or array 如果当前处于api调用期间, 则不会终止程序, 会把响应数据返回给调用者
	 */
	final public function endResponse($response = null, $status = 0, $messageArgs=null, $message = null, $script = null){
		$data['response'] = $response===null ? array() : $response;
		$data['status'] = intval($status);
		$data['message'] = $message!=null ? $message : ($status==0 ? 'ok' :  C('ERROR')[$status] ) ;
		if($data['message']==null)
			$data['message']=C('ERROR')[9];
		//如果当前指定了消息或者状态码率, 且指定了有效的消息替换参数, 则进行消息替换处理
		if($messageArgs!==null && $data['message']!='ok' && is_array($messageArgs) && sizeof($messageArgs)>0){
			$search = array();
			foreach (array_keys($messageArgs) as $v)
				$search[] ="{#{$v}}";
			$data['message'] = str_replace($search, array_values($messageArgs), $data['message']);
		}
		
		if(self::$_invokeLevel > 0)
			return $data;

		if(START_MODE == 2) {
		    // 直接返回数据用不到js脚本
			return (json_encode($data));
		} else {
			die($script ? '<script>'.$script.'</script>'.json_encode($data) : json_encode($data));
		}
	}
	
	
	/**
	 * invoke
	 * 本地调用一个API,  返回调用结果
	 * @param string $apiPath  要调用的Api字符串路径 demo: Wap.Goods.lists 
	 * @param array $apiArgs  调用指定Api时候的参数
	 * @return mixed
	 */
	final protected function invoke($apiPath, $apiArgs) {
		$cut = explode('.', $apiPath);
		$function	=	$cut[2];
		$object	=	N($apiPath);  	//实例化需要invoke的类
		self::$_invokeLevel++;
		$reponse = $object->$function($apiArgs);
		self::$_invokeLevel --;
		return  $reponse;
	}

	/**
	 * redis_query 
	 * 执行redis查询 
	 * @param mixed $query array or string
	 *  demo: 
	 *
	 *	array(
	 *		'hset user uid:10 99',
	 *		'hset user uid:10 999',
	 *  );
	 *
	 * string = "
	 *  local a = redis.call('get' a);
	 *  local b = redis.call('get' a);
	 *  return a+b; "
	 *
	 * @return void
	 * 返回最后一条语句的执行结果
	 *
	 */

	final protected function redis_query($query, $redis_handle = null) {
		if(is_array($query)) {
			$number = count($query);
			$sql = '';
			for($i = 0; $i < $number; $i++) {
				$v = $query[$i];
				$cut = explode(' ', $v);
				$cut_number = count($cut);

				for($c = 0; $c < $cut_number; $c++) {
					$cut[$c] = "'". $cut[$c]. "'";
				}

				if($i == ($number - 1)) {
					$sql .= 'return redis.call(';
					$sql .= implode(",", $cut). ');';

				} else {
					$sql .= 'redis.call(';
					$sql .= implode(",", $cut). ');';
				}

			}

		} elseif (is_string($query)) {
			$sql = $query;

		} else {
			throw new \Exception('redis 查询的 sql语句不符合查询类型');

		}
		$redis_handle = $redis_handle ? $redis_handle : R();
		return $redis_handle->eval($sql);
	}

	/**
	 * 推送入activemq
	 * @param  [type] $api     目标方法名称
	 * @param  [type] $message 参数
	 * @param  [type] $system  目标系统   tc/tm/webapi
	 * @param  [type] $from    来源       tm/webapi
	 * @return [type]          [description]
	 */
	protected function activemq( $api, $message, $system, $from ) {
		self::$_pushMQ = new PushMQ( 'serviceQueue', 'ActiveMQ' );
		return self::$_pushMQ -> addTaskList( $system, $api, $from, $message ) -> send();
	}
	
	/**
	 * push_queue( $queue_name, $function, [$post , $sync,  $sync_time, $unque_id, $lev] ) 
	 * 推送到队列
	 * @param string $queue_name 队列名称   order
	 * @param string $function 回调接口   Queue.Demo.test
	 * @param mixed $post 回调传递的数据 数据格式自己定义
	 * @param bool $sync  异步调用时是否线程等待 处理成功回应(处理安全性比较高的数据)
	 * @param bool $sync_time  异步调用时线程等待时间
	 * @param mixed $unque_id 数据唯一ID标识符
	 * @param int $lev 队列回调处理的优先级 1高 2 低 (队列服务器异步调用时的调用级别)
	 *
	 * @return boolean 返回true 或 balse
	 */
	protected function push_queue($queue_name, $function, $post = null, $sync = 1, $sync_time = 3 ,$unque_id = null, $lev = 1) {
		# getinfo
		if( !isset( self::$_queueObj ) ) {
			self::$_queueObj = new \Memcache;
			$queue_server = C('QUEUE_CONNECT_CONFIG');
			self::$_queueServer['height'] = $queue_server['height'];
			self::$_queueServer['low'] = $queue_server['low'];
		}
		
		# set oo
		$queue_server = self::$_queueServer;

		if( $lev === 1 ) {
			$queue_server = $queue_server['height'][0];
		} else {
			$queue_server = $queue_server['low'][0];
		}
		
		# connect
		$res = self::$_queueObj->connect($queue_server['host'], $queue_server['port']);
		if(!$res) {
			return false;
		}

		# assembly data
		$data = array(
			'sync' => $sync,                   # 回调是否等待结束
			'sync_time' => $sync_time,         # 回调线程等待时间
			'function' => $function,		   # 回调API地址
			'queueId'=> $queue_name,           # 队列名称
			'pushTime' => time(),              # 推入队列时间
			'message' => $post,                # 消息体
			'unque_id' => $unque_id,           # 唯一ID
			'worker_host' => C('QUEUE_WORKER_HOST')['host'], # 回调地址
			'error_push_number' => 0,          # 入栈出错计数器
			'error_pop_number' => 0,           # 出栈出错计数器
			'lev' => $lev,					   # 执行优先级
		);
		# check data and extends attr
		if(count($this->extends_attr) > 0 && is_array($this->extends_attr)) {
			$data = array_merge($data, $this->extends_attr);
			if($this->clear_flag) {  # 是否清除扩展属性
				$this->extends_attr = array();
			}

			if(count($data) <= 0 ) {
				return false;
			}
		};

		# do
		$function_queue_name = $queue_name;
		$queue_name = $sync ? 'sync': 'async';
		//$res = self::$_queueObj->getStats('sync');var_dump($res);exit;
		$res = self::$_queueObj->set($queue_name, json_encode($data));
        if ($res) {
            if ($lev != 1) {
                $param_log = array (
                        'type' => 1, // EC->WMS
                        'time' => time (),
                        'function' => $function_queue_name,
                        'message' => $post,
                        'status' => 1,
                        'call_message' => '报文接收成功' 
                );
                $this->invoke ( 'Wms.Log.add', $param_log );
            }
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 设置队列扩展属性
	 * setQueueExtendsAttr 
	 * @param array $array   array("fail_function"=>"wms.order.demo"); 数组型扩展属性
	 * @param blooean $clear 扩展属性被使用后是否清除 
	 * @access public
	 * @return boolean 返回 成功, 失败
	 */
	public function setQueueExtendsAttr($arr, $clear = true) {
		if(is_array($arr)) {
			$this->extends_attr = $arr;
			$this->clear_flag = $clear;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * response_queue 
	 *
	 * 响应队列 同步调用时返回消费结果
	 * @param mixed $flag  true 消费失败  false 消费成功
	 * 
	 * 如果消费失败($flag = false) 会被重新扔回队列下一次继续请求消费
	 * 如果消费失败大于5次会被扔进消费失败的队列($queue_name:push:fail) 不做任何处理(后续处理逻辑等待开发完善)
	 *
	 * @access protected
	 * @return void
	 */

	protected function response_queue( $flag = true ) {
		if( $flag ) {
			echo 1;
		} else {
			echo 0;
		}
	}

	/**
	 * 获取全局自增id
	 * getSequence 
	 * 
	 * @final
	 * @access protected
	 * @return void
	 *
	 */
	final private function getGlobalSequence() {
		$res = $this->getOne("select get_sequence() as id", null, self::DEFAULT_DBCONFIG_FLAG_WRITE);
		return $res['id'];
	}

	/**
	 *
	 * 创建订单号
	 * mkOrder  
	 * @param mixed $operaFlag 业务标识, 具体参照附件
	 * @final 不允许其他类继承
	 * @access protected
	 * @return void 返回生成号的订单号码
	 * 调用此方法前必须初始化数据库操作类 parent::__construct()
	 *
	 */
	final protected function mkOrder($operaFlag) {
		if(intval($operaFlag) != 0) {
			$sequence = $this->getGlobalSequence();
			if($sequence == 0) {
				return false;
			}else {
				$sequence = str_pad($sequence, 8, '0', STR_PAD_LEFT);
				$operaFlag = str_pad($operaFlag, 2, '0', STR_PAD_LEFT);
				$orderSN = date("ymd"). $operaFlag. $sequence;
				return $orderSN;
			}
		}else {
			return false;
		}
	}


}
?>
