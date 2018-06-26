<?php 
/**
 * | 推送消息 操作类
 */

namespace System\MQ;

class PushMQ {

    protected $_systemList = [
        'tc' => 'service.tc.commonService',
        'tm' => 'service.tm.commonService',
        'webapi' => 'service.webapi.commonService', 
    ];

    protected $_systemSign = [
        'tc' => '_javaApiSign',
        'tm' => '_serverApiSign',
        'webapi' => '_serverApiSign', 
    ];

    protected $_systemTask = [
        'tc' => '_javaApiTask',
        'tm' => '_serverApiTask',
        'webapi' => '_serverApiTask', 
    ];

    protected $_conn = null;                 // 队列链接

    protected $_topic = '';

    protected $_invokeMode = 0;              // 单个执行/批量执行
    protected $_redelivoryDelays = [
        "1m","2m","3m","4m",
    ];                                       // 重试时间
    protected $_taskList = [];               // 任务列表

    public function __construct( $topic, $MQ ) {
        $MQ = "System\\MQ\\".$MQ;
        $this -> setTopic( $topic ) -> _conn = new $MQ( $this -> _topic );
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
     * 设置执行模式
     * @param [type] $topic [description]
     */
    public function setInvokeMode( $mode ) {
        $this -> _invokeMode = $mode;
        return $this;
    }

    /**
     * 设置延迟执行时间
     * @param  [type] $delays 延迟执行时间
     * @return [type]         [description]
     */
    public function redelivoryDelays( $delays ) {
        $this -> _redelivoryDelays = $delays;
        return $this;
    }

    /**
     * 添加任务列表
     */
    public function addTaskList( $system, $api, $from, $message ) {

        if( !isset( $this -> _systemList[$system] ) ) {
            return false;
        }

        if( is_array( $message ) ) {
            $message = json_encode( $message );
        }
        
        $addTaskMethod = $this -> _systemTask[$system];
        $this -> _taskList[] = $this -> $addTaskMethod( $system, $api, $from, $message );

        return $this;
    }

    protected function _serverApiTask( $system, $api, $from, $message ) {
        $task = [
            'serviceName' => $this -> _systemList[$system],
            'data' => [
                'func' => $api,
                'from' => $from,
                'params' => $message
            ]
        ];
        $signMethod = $this -> _systemSign[$system];
        $task['data']['signKey'] = $this -> $signMethod( $task['data'], $system );
        return $task;
    }

    protected function _javaApiTask( $system, $api, $from, $message ) {
        $task = [
            'serviceName' => $this -> _systemList[$system],
            'data' => [
                'api' => $api,
                'from' => $from,
                'message' => $message
            ]
        ];
        $signMethod = $this -> _systemSign[$system];
        $task['data']['sign'] = $this -> $signMethod( $task['data'], $system );
        return $task;
    }

    /**
     * 发送消息
     * @return [type] [description]
     */
    public function send() {
        if( empty( $this -> _taskList ) || empty( $this -> _topic ) ) {
            return false;
        }

        $data = json_encode( $this -> _build() );

        $this -> _clear();

        return $this -> _conn -> setTopic( $this -> _topic ) -> send( $data );
    }

    /**
     * 创建数据包
     * @return [type] [description]
     */
    private function _build() {
        $data = [
            'invokeMode' => $this -> _invokeMode,
            'redelivoryDelays' => $this -> _redelivoryDelays,
            'taskList' => $this -> _taskList,
        ];
        return $data;
    }

    /**
     * 初始化数据
     * @return [type] [description]
     */
    private function _clear() {
        $this -> _invokeMode = 0; 
        $this -> _redelivoryDelays = [
            "1m",
            "2m",
            "3m",
            "4m"
        ];
        $this -> _taskList = [];
        return $this;
    }

    /**
     * phpapi加密
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    private function _serverApiSign( $data, $system ) {
        $sign = md5( $data['params'].C('SERVICE_API_REQUEST_SECKEY').$data['func'] );
        return $sign;
    }

    /**
     * javaapi加密
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    private function _javaApiSign( $data, $system ) {
        $service_config_all = C( 'SERVICE_CONFIG' );
        $service_config = $service_config_all[strtoupper($system)];
        $prekey = md5( $data['message'] . substr($service_config['sign_key'], 0, 64) );
        $sign = strtoupper( md5( $prekey . substr($service_config['sign_key'], 64) ) );
        return $sign;
    }

}