<?php
/**
 * 七牛上传接口
 */
namespace Api\Common;

use System\Base;
use System\Logs;

class QiniuUpload extends Base{
	
    /*
     * 获取文件Token
     * <p>请求参数说明</p>
     * <p>func: Common.QiniuUpload.getToken</p>
     * @param   string $params['bucket'] bucket空间值
     * @return  mix
     * @version 1.0 
     */
    public function getToken($params){
        
        Logs::info('Common.QiniuUpload.getToken',LOG_FLAG_NORMAL,['getToken-获取文件Token，传入的参数',$params]);
        
        $Upload = new \Library\Qiniu($params['bucket']);
        
        $token  = $Upload->getFileUploadToken();
        
        return $this->endResponse($token,0);
        
    }
    
    /**
     * 上传文件
     * <p>请求参数说明</p>
     * <p>func: Common.QiniuUpload.putFile</p>
     * @param   string  $params['file_name']    文件名
     * @param   string  $params['file_type']    文件类型，图片->img，PDF->pdf，如果有值则会限制类型
     * @param   integer $params['x']            文件宽，传入则限制宽
     * @param   integer $params['y']            文件高，传入则限制高
     * @param   integer $params['max_size']     文件大小的最大值，传入则限制大小
     * @param   integer $params['scale']        文件比例（宽/高 or 高/宽），传入则限制比例
     * @param   string  $params['domain']       script，主要用于跨域，传入则返回值追加该脚本内容
     * @param   resource $_FILES                上传文件resource
     * @return  mix
     * @version 1.0
     */
    public function putFile($params) {
        Logs::info('Common.QiniuUpload.putFile',LOG_FLAG_NORMAL,['putFile-上传文件到七牛，传入的参数',$params]);
        
        // 跨域问题解决
        if($params['domain']){
            $domain = 'document.domain = "'.$params['domain'].'";';
        }else{
            $domain = null;
        }
        
        $file_name = trim($params['file_name']);
        if ($_FILES[$file_name]['error'] != 0) {
            return $this->endResponse(null, 5, null, $this->getFileError($_FILES[$file_name]['error']), $domain);
        }
        
        $size       = $_FILES[$file_name]['size'] / (1024 * 1024);
        $max_size   = isset($params['max_size']) ? (int)$params['max_size'] : 0;
        if ($max_size != 0 && $size > $max_size) {
            return $this->endResponse(null, 5, null, '请确保文件在 ' . $max_size . 'M 内', $domain);
        }
        
        $file_type = trim($params['file_type']);
        
        if ($_FILES[$file_name]['type'] == 'application/pdf' || $_FILES[$file_name]['type'] == 'application/vnd.adobe.pdx' || $_FILES[$file_name]['type'] == 'application/kswps') {
           
            if($file_type && $file_type != 'pdf'){
                return $this->endResponse(null,2009,null,$this->getFileTypeError($file_type), $domain);
            }
            
            $Upload = new \Library\Qiniu("csdkbp");
            $image  = $Upload->putFile($_FILES[$file_name]['tmp_name']);
            if (isset($image['key']) && !empty($image['key'])) {
                $data['key']    = $image['key'];
                $data['url']    = "/bp_viewer/get_bp.php?key=" . $image['key'];
                $data['type']   = 2;    #pdf
                return $this->endResponse($data,0,null,null, $domain);
            } else {
                return $this->endResponse(null,2009,null,$image, $domain);
            }
            
        } else if ($_FILES[$file_name]['type'] == 'image/gif' || $_FILES[$file_name]['type'] == 'image/jpeg' || $_FILES[$file_name]['type'] == 'image/pjpeg' || $_FILES[$file_name]['type'] == 'image/png' || $_FILES[$file_name]['type'] == 'image/x-png') {
           
            if($file_type && $file_type != 'img'){
                return $this->endResponse(null,2009,null,$this->getFileTypeError($file_type), $domain);
            }
            
            $imageinfo  = getimagesize($_FILES[$file_name]['tmp_name']);
            $xy         = explode(" ", $imageinfo[3]);
            
            $width  = explode("=", $xy[0]);
            $len    = strlen($width[1]);
            $width  = intval(substr($width[1], 1, - 1));
            
            $height = explode("=", $xy[1]);
            $len    = strlen($height[1]);
            $height = intval(substr($height[1], 1, - 1));
            
            $scale = isset($params['scale']) ? (int)$params['scale'] : 0;
            if($scale){
                if ($width/$height != $scale && $height/$width != $scale) {
                    return $this->endResponse(null, 5, null, '请确保图片比例为：' . $scale, $domain);
                }
            }
                
            $x = isset($params['x']) ? (int)$params['x'] : 0;
            $y = isset($params['y']) ? (int)$params['y'] : 0;
            
            if ($x != 0 && $x != $width) {
                return $this->endResponse(null, 5, null, '请确保图片宽度为：' . $x, $domain);
            }
            
            if ($y != 0 && $y != $height) {
                return $this->endResponse(null, 5, null, '请确保图片高度为：' . $y, $domain);
            }
                
            
            $Upload = new \Library\Qiniu("csdkimg");
            $image = $Upload->putFile($_FILES[$file_name]['tmp_name']);
            if (isset($image['key']) && !empty($image['key'])) {
                // 缓存到页面
                $data['key']    = $image['key'];
                $data['url']    = $Upload->getQiniuPathJs($image['key'], "img");
                $data['scale']  = round( $height/ $width, 2); // 高宽比例
                $data['type']   = 1;    #图片
                return $this->endResponse($data,0,null,null, $domain);
            } else {
                return $this->endResponse(null,2009,null,$image, $domain);
            }
        } else {
            return $this->endResponse(null,2001,null,null, $domain);
        }

    }

    /**
     * 获取文件信息
     * <p>请求参数说明</p>
     * <p>func: Common.QiniuUpload.getFileInfo</p>
     * @param   string $params['key']
     * @param   string $params['bucket']
     * @return  mix
     * @version 1.0
     */
    public function getFileInfo($params){
        Logs::info('Common.QiniuUpload.getFileInfo',LOG_FLAG_NORMAL,['getFileInfo，传入的参数',$params]);
        
        $key    = trim($params['key']);
        $bucket = trim($params['bucket']) ? trim($params['bucket']) : 'csdkimg';
        if(!isset($key) || empty($key) || empty($bucket)){
            return $this->endResponse(null,5);
        }
        
        $Upload = new \Library\Qiniu($bucket);
        $info = $Upload->getFileInfo($key);
        return $this->endResponse($info,0);
    }
    
    /**
     * 获取文件上传错误信息
     * <p>请求参数说明</p>
     * <p>func: Common.QiniuUpload.getFielError</p>
     * @param   string $code
     * @return  string
     * @version 1.0
     */
    private function getFileError($code){
        switch($code) {
            case 1:
                // The file is too large (server).
                $error = "文件大小超出了服务器的空间大小";
                break;
        
            case 2:
                // The file is too large (form).
                $error = "要上传的文件大小超出浏览器限制";
                break;
                 
            case 3:
                // The file was only partially uploaded.
                $error = "文件仅部分被上传";
                break;
                 
            case 4:
                // No file was uploaded.
                $error = "没有找到要上传的文件";
                break;
                 
            case 5:
                // The servers temporary folder is missing.
                $error = "服务器临时文件夹丢失";
                break;
                 
            case 6:
                // Failed to write to the temporary folder.
                $error = "文件写入到临时文件夹出错";
                break;
            default:
                $error = "文件上传失败";
        }
        
        return $error;
    }
    
    private function getFileTypeError($type){
        switch($type){
            case 'img':
                $error = "只允许上传gif、jpg、png格式";
                break;
            case 'pdf':
                $error = "只允许上传pdf格式";
                break;
            default:
                $error = "文件格式错误";
        }
    
        return $error;
    }
}

?>
