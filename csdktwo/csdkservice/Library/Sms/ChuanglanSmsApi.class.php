<?php

// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: csdk team
// +----------------------------------------------------------------------

namespace Library\Sms;

use Library;
class ChuanglanSmsApi{
    
    private $api_account    = "";
    private $api_password   = "";
    private $api_send_url   = "";
    private $api_balance_query_url = "";
    
    /**
     * 架构函数
     * 
     */
    public function __construct(){
        $this->api_account  = 'csdake';
        $this->api_password = 'Tch123456';
        $this->api_send_url = 'http://222.73.117.156/msg/HttpBatchSendSM?';
        $this->api_balance_query_url = 'http://222.73.117.156/msg/QueryBalance?';
    }
    
    /**
     * 发送短信
     *
     * @param string $mobile 手机号码
     * @param string $msg 短信内容
     * @param string $needstatus 是否需要状态报告
     * @param string $product 产品id，可选
     * @param string $extno   扩展码，可选
     */
    public function sendSMS($mobile, $msg, $needstatus = 'false', $product = '', $extno = '') {
        
        // 创蓝接口参数
        $postArr = array (
            'account'       => $this->api_account,
            'pswd'          => $this->api_password,
            'msg'           => urlencode(mb_convert_encoding($msg,'UTF-8', 'auto')),
            'mobile'        => $mobile,
            'needstatus'    => $needstatus,
            'product'       => $product,
            'extno'         => $extno
        );
        $result = $this->curlPost( $this->api_send_url , $postArr);
        return $result;
    }
    
    /**
     * 查询额度
     *
     */
    public function queryBalance() {
        // 查询参数
        $postArr = array (
            'account'   => $this->api_account,
            'pswd'      => $this->api_password,
        );
        $result = $this->curlPost($this->api_balance_query_url, $postArr);
        return $result;
    }
    
    /**
     * 获取短信提交返回值说明
     *
     * @param int $code 状态码
     */
    public function getSmsSubmit($code){
        $status = array(
            0   => '提交成功',
            101 => '无此用户',
            102 => '密码错',
            103 => '提交过快（提交速度超过流速限制）',
            104 => '系统忙（因平台侧原因，暂时无法处理提交的短信）',
            105 => '敏感短信（短信内容包含敏感词）',
            106 => '消息长度错（>536或<=0）',
            107 => '包含错误的手机号码',
            108 => '手机号码个数错（群发>50000或<=0;单发>200或<=0）',
            109 => '无发送额度（该用户可用短信数已使用完）',
            110 => '不在发送时间内',
            111 => '超出该账户当月发送额度限制',
            112 => '无此产品，用户没有订购该产品',
            113 => 'extno格式错（非数字或者长度不对）',
            115 => '自动审核驳回',
            116 => '签名不合法，未带签名（用户必须带签名的前提下）',
            117 => 'IP地址认证错,请求调用的IP地址不是系统登记的IP地址',
            118 => '用户没有相应的发送权限',
            119 => '用户已过期',
            120 => '短信内容不在白名单中',
        );
        
        return $status[$code];
    }
    
    /**
     * 获取额度查询返回值说明
     *
     * @param int $code 状态码
     */
    public function getBalanceQuery($code){
        $status = array(
            0   => '成功',
            101 => '无此用户',
            102 => '密码错',
            103 => '查询过快（30秒查询一次）',
        );
    
        return $status[$code];
    }
    
    /**
     * 获取状态报告返回值说明
     *
     * @param string $code 状态码
     */
    public function getStatusReport($code){
        $status = array(
            'DELIVRD' => '短消息转发成功',
            'EXPIRED' => '短消息超过有效期',
            'UNDELIV' => '短消息是不可达的',
            'UNKNOWN' => '未知短消息状态',
            'REJECTD' => '短消息被短信中心拒绝',
            'DTBLACK' => '目的号码是黑名单号码',
            'ERR:104' => '系统忙',
            'REJECT'  => '审核驳回',
            '其他'    => '网关内部状态',
        );
        
        return $status[$code];
    }
    
    /**
     * 处理返回值
     *
     */
    public function execResult($result){
        $result=preg_split("/[,\r\n]/",$result);
        return $result;
    }
    
    /**
     * 通过CURL发送HTTP请求
     * @param string $url  请求URL
     * @param array $postFields 请求参数
     * @return mixed
     */
    private function curlPost($url,$postFields){
        
//         $ch     = new \Library\Curl();
//         $result = $ch->post($url,$postFields);
        
        $postFields = http_build_query($postFields);
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
        $result = curl_exec ( $ch );
        curl_close ( $ch );

        return $result;
        
    }
    
}

?>