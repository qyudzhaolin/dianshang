<?php
/**
 * 七牛上传接口
 */
namespace Api\Common;

use System\Base;
use System\Logs;

class QiniuUpload extends Base{
	
    /*
     * 获取文件上传Token
     * <p>请求参数说明</p>
     * <p>func: Common.QiniuUpload.getFielUploadToken</p>
     * @param   string $bucket
     * @return  mix
     * @version 1.0 
     */
    public function getFileUploadToken($params){
        
        Logs::info('Common.QiniuUpload.getFielUploadToken',LOG_FLAG_NORMAL,['putFile-获取文件上传Token，传入的参数',$params]);
        
        $Upload = new \Library\Qiniu($params['bucket']);
        
        $token  = $Upload->getFileUploadToken();
        
        return $this->endResponse($token,0);
        
    }
    
    /**
     * 上传文件
     * <p>请求参数说明</p>
     * <p>func: Common.QiniuUpload.putFile</p>
     * @param   string $params['file_name']
     * @param   string $params['file_type']
     * @param   integer $params['x']
     * @param   integer $params['y']
     * @param   integer $params['max_size']
     * @param   mix $_FILES
     * @return  mix
     * @version 1.0
     */
    public function putFile($params) {
        
        Logs::info('Common.QiniuUpload.putFile',LOG_FLAG_NORMAL,['putFile-上传文件到七牛，传入的参数',$params]);
        
        $file_name = trim($params['file_name']);
        if ($_FILES[$file_name]['error'] != 0) {
            return $this->endResponse(null, 5, null, $this->getFileError($_FILES[$file_name]['error']));
        }
        
        $size       = $_FILES[$file_name]['size'] / (1024 * 1024);
        $max_size   = isset($params['max_size']) ? (int)$params['max_size'] : 0;
        if ($max_size != 0 && $size > $max_size) {
            return $this->endResponse(null, 5, null, '请确保文件在' . $max_size . 'M内');
        }
        
        if ($_FILES[$file_name]['type'] == 'application/pdf' || $_FILES[$file_name]['type'] == 'application/vnd.adobe.pdx' || $_FILES[$file_name]['type'] == 'application/kswps') {
           
            $Upload = new \Library\Qiniu("csdkbp");
            $image  = $Upload->putFile($_FILES[$file_name]['tmp_name']);
            if (isset($image['key']) && !empty($image['key'])) {
                $data['key']    = $image['key'];
                $data['url']    = "/bp_viewer/get_bp.php?key=" . $image['key'];
                $data['type']   = 2;    #pdf
                return $this->endResponse($data,0);
            } else {
                return $this->endResponse(null,2009,null,$image);
            }
            
        } else if ($_FILES[$file_name]['type'] == 'image/gif' || $_FILES[$file_name]['type'] == 'image/jpeg' || $_FILES[$file_name]['type'] == 'image/pjpeg' || $_FILES[$file_name]['type'] == 'image/png' || $_FILES[$file_name]['type'] == 'image/x-png') {
           
            $x = isset($params['x']) ? (int)$params['x'] : 0;
            $y = isset($params['y']) ? (int)$params['y'] : 0;
            
            $imageinfo  = getimagesize($_FILES[$file_name]['tmp_name']);
            $xy         = explode(" ", $imageinfo[3]);
            
            $width  = explode("=", $xy[0]);
            $len    = strlen($width[1]);
            $width  = intval(substr($width[1], 1, - 1));
            
            $height = explode("=", $xy[1]);
            $len    = strlen($height[1]);
            $height = intval(substr($height[1], 1, - 1));
            
            if ($x != 0 && $x != $width) {
                return $this->endResponse(null, 5, null, '请确保图片宽度为' . $x);
            }
            
            if ($y != 0 && $y != $height) {
                return $this->endResponse(null, 5, null, '请确保图片高度为' . $y);
            }
            
            $Upload = new \Library\Qiniu("csdkimg");
            $image = $Upload->putFile($_FILES[$file_name]['tmp_name']);
            if (isset($image['key']) && !empty($image['key'])) {
                // 缓存到页面
                $data['key']    = $image['key'];
                $data['url']    = $Upload->getQiniuPathJs($image['key'], "img");
                $data['scale']  = round( $height/ $width, 2); // 高宽比例
                $data['type']   = 1;    #图片
                return $this->endResponse($data,0);
            } else {
                return $this->endResponse(null,2009,null,$image);
            }
        } else {
            return $this->endResponse(null,2001);
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
                // 文件大小超出了服务器的空间大小
                $error = "The file is too large (server).";
                break;
        
            case 2:
                // 要上传的文件大小超出浏览器限制
                $error = "The file is too large (form).";
                break;
                 
            case 3:
                // 文件仅部分被上传
                $error = "The file was only partially uploaded.";
                break;
                 
            case 4:
                // 没有找到要上传的文件
                $error = "No file was uploaded.";
                break;
                 
            case 5:
                // 服务器临时文件夹丢失
                $error = "The servers temporary folder is missing.";
                break;
                 
            case 6:
                // 文件写入到临时文件夹出错
                $error = "Failed to write to the temporary folder.";
                break;
        }
        
        return $error;
    }
    
}

?>
