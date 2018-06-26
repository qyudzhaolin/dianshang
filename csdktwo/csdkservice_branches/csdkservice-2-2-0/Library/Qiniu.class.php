<?php
/*
 * 七牛图片相关操作类
 */
namespace Library;

// 引入外部文件
require_once LIB_PATH.'Qiniu/autoload.php';

class Qiniu{
	
	protected $Accesskey   = '5CQmagmT27DNXYhsmVewntOpd9VLGD8sC5c02ptg';
	protected $Secretkey   = 'lQm-akaIb3JOS-WAlBycTXF_95hx7JEd9s88ARk5';
	protected $Bucket      = 'csdkimg';
	protected $ImgDomain   = 'http://img.cisdaq.com/';
	protected $BPDomain    = 'http://bp.cisdaq.com/';
	protected $Auth        = null;

	/**
	 * 初始化函数
	 * @access public
	 * @param string $Bucket 要上传的空间
	 * @return void
	 */
	public function __construct($bucket) {
	    
		// 初始化类属性
		$this->Bucket = $bucket ? $bucket : $this->Bucket;
		
		// 构建鉴权对象
		$this->Auth = new \Qiniu\Auth($this->Accesskey, $this->Secretkey);
	}

	/**
	 * 获取文件上传Token
	 * @access public
	 * @return 返回token
	 */	
	public function getFileUploadToken(){
	    
	    // 生成上传 Token
	    $token = $this->Auth->uploadToken($this->Bucket);
	    
	    return $token;
	}
	
	/**
	 * 上传文件
	 * @access public
	 * @param string $filePath 要上传文件的本地路径
	 * @param string $key 上传到七牛后保存的文件名
	 * @return 返回上传结果
	 */
	public function putFile($filePath,$key=null) {
	    
	    // 获取上传 Token
	    $token = $this->getFileUploadToken();
	    
	    // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new \Qiniu\Storage\UploadManager();
    
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return $err->message();
        } else {
            return $ret;
        }
	}
	
	/**
	 * 获取文件信息
	 * @access public
	 * @param string $key 文件名
	 * @return 返回查询结果
	 */
	public function getFileInfo($key) {
	    
	    // 初始化BucketManager
	    $bucketMgr = new \Qiniu\Storage\BucketManager($this->Auth);
	    
	    // 获取文件的状态信息
	    list($ret, $err) = $bucketMgr->stat($this->Bucket, $key);
	    if ($err !== null) {
	        return $err->message();
	    } else {
	        return $ret;
	    }
	}	
	
	/**
	 * 复制单个文件
	 * @access public
	 * @param string $key 要复制的文件名
	 * @param string $key2 复制后的文件名
	 * @param string $bucket 要复制到的空间
	 * @return 返回查询结果
	 */
	public function moveFile($key,$key2,$bucket) {
	     
	    // 初始化BucketManager
	    $bucketMgr = new \Qiniu\Storage\BucketManager($this->Auth);
	    
	    //将文件从文件$key 复制到文件$key2。 可以在不同bucket复制
	    $key2 = isset($key2) ? $key2 : $key;
	    $err = $bucketMgr->copy($this->Bucket, $key, $bucket, $key2);
	    if ($err !== null) {
	        return $err->message();
	    } else {
	        return true;
	    }
	}	
	
	/**
	 * 删除单个文件
	 * @access public
	 * @param string $key 要删除的文件名
	 * @return 返回查询结果
	 */
	public function deleteFile($key) {
	     
	    // 初始化BucketManager
	    $bucketMgr = new \Qiniu\Storage\BucketManager($this->Auth);
	    
        // 删除$bucket 中的文件 $key
        $err = $bucketMgr->delete($this->Bucket, $key);
	    if ($err !== null) {
	        return $err->message();
	    } else {
	        return true;
	    }
	}	

	public function getQiniuPath($key,$type='') {
	    if($type=='bp'){
	        $url = $this->BPDomain.$key;
	        $duetime = NOW_TIME + 86400;//下载凭证有效时间
	        $DownloadUrl = $url . '?e=' . $duetime;
	        $Sign = hash_hmac ( 'sha1', $DownloadUrl, SECRETKEY, true );
	        $EncodedSign = base64_urlSafeEncode( $Sign );
	        $Token = ACCESSKEY. ':' . $EncodedSign;
	        $url = $DownloadUrl . '&token=' . $Token;
	    }else{
	        $url = $this->ImgDomain.$key;
	    }
	    
	    return $url;
	}
	
	function getQiniuPathAndroid($url) {
	
	    $duetime = time() + 86400;//下载凭证有效时间
	
	    $DownloadUrl = $url . '&e=' . $duetime;
	
	    $Sign = hash_hmac ( 'sha1', $DownloadUrl, SECRETKEY, true );
	    $EncodedSign = base64_urlSafeEncode ( $Sign );
	    $Token = ACCESSKEY. ':' . $EncodedSign;
	    $url = $DownloadUrl . '&token=' . $Token;
	
	    return $url;
	}
	
	function getQiniuPathJs($key,$type='') {
	
	    if($type == 'bp'){
	        $baseUrl = $this->BPDomain.$key;
	        // $baseUrl = 'http://7xju4h.com1.z0.glb.clouddn.com/'.$key.'?imageView2/1/w/35/h/35';
	        $baseUrl = $this->Auth->privateDownloadUrl($baseUrl);
	    }else{
	        $baseUrl = $this->ImgDomain.$key;
	    }
	
	    return $baseUrl;
	}
	
	/**
	 * getFileSuffix 
	 * 截取文件后缀名称
	 * @access private
	 * @return void
	 */
	private function getFileSuffix($fileName) {
		$suffix = basename($fileName);
		return $suffix;
	}

}
?>

