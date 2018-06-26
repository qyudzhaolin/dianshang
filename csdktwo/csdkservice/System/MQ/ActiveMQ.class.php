<?php 
/**
 * | ActiveMQ 操作类
 */

namespace System\MQ;

class ActiveMQ implements MQ {

    protected $_conn = null;
    protected $_config = [];
    protected $_topic = '';

    public function __construct( $topic, $config = null ) {
        if( empty( $this -> _conn ) ) {
            $this -> setConfig( $config )
                  -> setTopic( $topic )
                  -> _init();
        }
    }

    public function __destruct() {
        if( !empty( $this -> _conn ) ) {
            unset( $this -> _conn );
        }
    }

    /**
     * 初始化方法
     * @return [type] [description]
     */
    protected function _init() {
        $broker = $this -> _buildBroker( $this -> _config['host'], $this -> _config['port'] );
        $this -> _conn = new \Stomp( $broker );
        return $this;
    }

    /**
     * 设置配置文件
     * @param [type] $config [description]
     */
    public function setConfig( $config = null ) {
        if( empty( $config ) && !is_array( $config ) ) {
            $config = C( 'ACTIVEMQ_CONFIG' );
        }

        $this -> _config = $config;
        return $this;
    }

    /**
     * 设置主题
     * @param [type] $topic [description]
     */
    public function setTopic( $topic ) {
        $this -> _topic = $topic;
        return $this;
    }

    /**
     * 发送消息
     * @return [type] [description]
     */
    public function send( $msg, $header = [] ) {
        return $this -> _conn -> send( $this -> _topic, $msg, $header );
    }

    /**
     * 接收消息
     * @return [type] [description]
     */
    public function recv() {
        $this -> _conn -> subscribe( $this -> _topic );
        $frame = $this -> _conn -> readFrame();
        $data = ["headers" => $frame -> headers, "body" => $frame -> body];
        $this -> _conn -> ack( $frame );
        $this -> _conn -> unsubscribe( $this -> _topic );
        return $data;
    }

    /**
     * 创建broker
     * @param  [type] $host [description]
     * @param  [type] $port [description]
     * @return [type]       [description]
     */
    protected function _buildBroker( $host, $port ) {
        return 'tcp://' . $host . ':' . $port;
    }
}