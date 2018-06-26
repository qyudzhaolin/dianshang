<?php
require_once("base.php") ;
class RedisTemplate {
	public static function lrange($key) {
		$val = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$val = $redis->lrange($key, 0, -1) ;
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
		/*finally {
			//echo "finally \n" ;
		}
		*/
        return $val;
    }
    public static function incr($key) {
		$val = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$val = $redis->incr($key) ;
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
		/*finally {
			//echo "finally \n" ;
		}
		*/
        return $val;
    }
    public static function get($key) {
		$val = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$val = $redis->get($key) ;
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
		/*finally {
			//echo "finally \n" ;
		}
		*/
        return $val;
    }
    public static function mget($key) {
		$val = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$val = $redis->mget($key) ;
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
		/*	
		finally {
			//echo "finally \n" ;
		}
		*/
        return $val;
    }
	
	public static function set($key, $val) {
		$res = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$res = $redis->set($key, $val) ;
			
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
        return $res;
    }
	
	public static function hset($key, $field, $val) {
		$res = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$res = $redis->hset($key, $field, $val) ;
			
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
        return $res;
    }
	
	public static function hget($key, $field) {
		$val = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$val = $redis->hget($key, $field) ;
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
        return $val;
    }
	
	public static function hmget($key, $fields) {
		$val = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$val = $redis->hmget($key, $fields) ;
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
        return $val;
    }
	
	public static function hkeys($key) {
		$val = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$val = $redis->hkeys($key) ;
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
        return $val;
    }
	
	public static function hdel($hash, $key) {
		$val = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$val = $redis->hdel($hash, $key) ;
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
        return $val;
    }

	public static function delete($key) {
		$val = "" ;
		if (is_null($key)) {
			return $val ;
		}
        Global $redis_host, $redis_port, $redis_timeout  ;
		try {
			$redis = new Redis() ;
			$redis->connect($redis_host, $redis_port, $redis_timeout) ;
			$val = $redis->delete($key) ;
		} catch (RedisException $e) {
			echo $e->getMessage() ;
		} 
        return $val;
    }	
}
?>
