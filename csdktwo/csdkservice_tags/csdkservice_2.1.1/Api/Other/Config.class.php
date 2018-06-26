<?php

/**
 * | 网站配置功能模块
 */

namespace Api\Other;
use System\Base;
class Config extends Base{
	private $tablePrefix = null;
	private $table;
	private $redis;
	private $redis_cache_time;
	public function __construct() {
		parent::__construct();
		$this->tablePrefix = $this->getMainDbTablePrefix();
		$this->table = $this->tablePrefix.'config';
		$this->redis_cache_time = 3600;
		$this->redis = R();
	}
	
	/**
	 * 获取后台配置
	 * <p>请求参数说明</p>
	 * <p>func: Other.Config.getConfig</p>
	 * @return  bool
	 * @version 1.0
	 * @author  汪刚 <wangg@vacn.com.ccn>
	 */
	public function getConfig(){
		$result = $this->redis->get('site_config');
		$result = json_decode($result);
		//如果redis中存在则返回
		if($result){
			return $this->endResponse($result);
		}
		
		$sql = "select name,title,extra,remark,value from ".$this->table;
		$result = $this->executeQuery($sql);
		if ($result){
			$this->redis->setex('site_config', $this->redis_cache_time, json_encode($result));
			return $this->endResponse($result);
		}
		return  $this->endResponse(false);
	}
}
?>