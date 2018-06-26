<?php

/**
 * | memcache
 */
namespace Library;

class Memcache {
    private $memcache;
    private $connect;
    /**
     * 构造函数
     * servers = array('host' => '', 'port' => 11211, 'weight' => 50);
     */
    public function __construct($servers = null) {
        if (empty ( $this->memcache ) && C ( 'MEMCACHE_ON' )) {
            $this->memcache = new \Memcache ();

            if ($servers) {
                $servers = array($servers); // 为了一致 
            } else {
                $servers = C ( 'MEMCACHE_SERVER' );
            }
            if ($servers && is_array ( $servers )) {
                foreach ( $servers as $ele ) {
                    // 使用长连接，将memcache服务器添加到连接池
                    $connect = $this->memcache->addServer ( $ele ['host'], $ele ['port'], true, $ele ['weight'], 1, 3600 );
                    if ($connect) {
                        $this->connect = true;
                    }
                }
            }
        }
    }
    /**
     * 获取单个memcache缓存
     *
     * @param unknown $key            
     * @return string
     */
    public function get($key, &$flags = null) {
        if (C ( 'MEMCACHE_ON' ) && $this->connect) {
            return $this->memcache->get ( $key, $flags );
        } else {
            return "";
        }
    }
    
    /**
     * 新增memcache缓存
     *
     * @param unknown $key            
     * @param unknown $var            
     */
    public function add($key, $var, $flag = null, $expire = null) {
        if (C ( 'MEMCACHE_ON' ) && $this->connect) {
            return $this->memcache->add ( $key, $var, $flag, $expire );
        } else {
            return false;
        }
    }
    /**
     * 设置单个memcache缓存
     *
     * @param unknown $key            
     * @param unknown $var            
     */
    public function set($key, $var, $flag = null, $expire = null) {
        if (C ( 'MEMCACHE_ON' ) && $this->connect) {
            return $this->memcache->set ( $key, $var, $flag, $expire );
        } else {
            return false;
        }
    }
    
    /**
     * 清除memcache缓存,但不释放内存空间
     */
    public function flush() {
        if (C ( 'MEMCACHE_ON' ) && $this->connect) {
            return $this->memcache->flush ();
        } else {
            return false;
        }
    }
    /**
     * 删除memcache缓存
     */
    public function delete($key, $timeout = null) {
        if (C ( 'MEMCACHE_ON' ) && $this->connect) {
            return $this->memcache->delete ( $key, $timeout );
        } else {
            return false;
        }
    }
}
