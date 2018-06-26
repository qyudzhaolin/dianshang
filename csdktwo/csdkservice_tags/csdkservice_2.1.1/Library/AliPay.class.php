<?php

/*
 * | 支付宝处理
 */




namespace Library;
include LIB_PATH.'Alipay/alipayconfig.class.php';
include LIB_PATH.'Alipay/lib/alipaysubmit';
class AliPay {
    
    private static $instance;
    private function __construct() {
        
    }
    
    /**
     * 单例化
     */
    public static function getInstance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    /**
     * 跳转到支付页面
     * @param string $oid  订单号
     * @param string $name  订单名称
     * @param decimal $price  订单金额
     * @return $html_text string;
     */
    
    public function alipay($oid, $name, $price){
        $name = "幸福9号" . $oid;
        /*         * ************************请求参数************************* */

        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = "http://www.xf9.com/home/paystatus/notify/";
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        $return_url = "http://www.xf9.com/home/paystatus/returns/";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        //卖家支付宝帐户
        $seller_email = 'pay@vacn.com.cn';
        //必填
        //商户订单号
        $out_trade_no = $oid;
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $name;
        //必填
        //付款金额
        $total_fee = $price;
        //必填
        //订单描述

        $body = $name;
        //商品展示地址
        $show_url = '';
        //需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html
        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数
        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "payment_type" => $payment_type,
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "seller_email" => $seller_email,
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "show_url" => $show_url,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset" => trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        //$html_text = $alipaySubmit->buildRequestHttp($parameter);
        return $html_text;
    }

}
?>

