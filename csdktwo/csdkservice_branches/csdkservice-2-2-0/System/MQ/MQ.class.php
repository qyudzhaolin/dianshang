<?php 
/**
 * | MQ接口
 */


namespace System\MQ;

interface MQ {
    public function setConfig( $config = null );
    public function setTopic( $topic );
    public function send( $msg, $header = [] );
    public function recv();
}
