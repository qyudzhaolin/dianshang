<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author:zhaoLin(97139915@qq.com)
// +----------------------------------------------------------------------
// 
// 
class dbencrypt{
  /*
   * 加密
   * @param int $id 用户ID
   * @author zhaoLin
   * 
  */
  static function encrypt($data, $key)  
    {  
        $key    =   md5($key);  
        $x      =   0;  
        $len    =   strlen($data);  
        $l      =   strlen($key);  
        for ($i = 0; $i < $len; $i++)  
        {  
            if ($x == $l)   
            {  
                $x = 0;  
            }  
            $char .= $key{$x};  
            $x++;  
        }  
        for ($i = 0; $i < $len; $i++)  
        {  
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);  
        }  
        return base64_encode($str);  
    }  
}
$data = 'dev123';        // 被加密信息  
$key = '123';  // 秘钥
echo dbencrypt::encrypt($data, $key);  
?>