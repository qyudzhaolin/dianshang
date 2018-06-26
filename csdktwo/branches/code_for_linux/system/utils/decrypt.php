<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class decrypt
{
    public $devstr='';
    public $devchar='';
    //解密方法
 function  decode($data, $key)  
      {  
        $key = md5($key);  
        $x = 0;  
        $data = base64_decode($data);  
        $len = strlen($data);  
        $l = strlen($key);  
        for ($i = 0; $i < $len; $i++)  
        {  
            if ($x == $l)   
            {  
                $x = 0;  
            }  
            $this->devchar.= substr($key, $x, 1);  
            $x++;  
        }  
        for ($i = 0; $i < $len; $i++)  
        {  
            if (ord(substr($data, $i, 1)) < ord(substr($this->devchar, $i, 1)))  
            {  
                $this->devstr .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($this->devchar, $i, 1)));  
            }  
            else  
            {  
                $this->devstr.= chr(ord(substr($data, $i, 1)) - ord(substr($this->devchar, $i, 1)));  
            }  
        }  

        return $this->devstr;  
    }  


}
?>