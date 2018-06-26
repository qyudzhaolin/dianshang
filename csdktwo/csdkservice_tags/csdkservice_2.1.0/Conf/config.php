<?php
/**
 * | 系统全局配置文件
 */
return array(
		'SINGN_KEY' => 'HTTP://WWW.CISDAQ.COM/_2016@CSDK_#@DU^^&JGK_((*&gjGH',  //api调用验证key
        
        'UC_AUTH_KEY' => 'y?Ewc7lkd5}"Ubh~t3iOWFe6=>&|SG9z*^]C;81L', //MD5加密KEY
        // 数据库配置信息
//         'DBHELPER' => array(
//                 'DB_TABLE_PREFIX' => 'cixi_', //数据库表前缀
//                 'MASTER_BEAR_READ' => true, //主服务器负担读操作
//                 'MASTER' => array(//主服务器
//                     'HOST' => '192.168.1.28', // 服务器地址
//                     'PORT' => 3306, // 端口
//                     'USER' => 'dev', // 用户名
//                     'PWD' => 'dev123', // 密码
//                     'NAME' => 'csdk', // 数据库名
//                     'CHARSET' => 'utf8'    // 数据库编码默认采用utf8
//                 ),
//                'SLAVE' => array(//从服务器
//                     // 从服务器1,2,3.....n
//          		    // array('HOST' => '127.0.0.1', 'PORT' => 3306, 'USER' => 'root', 'PWD' => '', 'NAME' => 'vacn_test', 'CHARSET' => 'utf8'),
//                     // array('HOST' => '127.0.0.1', 'PORT' => 3306, 'USER' => 'root', 'PWD' => '', 'NAME' => 'vacn_test', 'CHARSET' => 'utf8')
//                 ),
//                 'OTHER' => array(//其他第三方应用数据库配置
//                     // 'TEST' => array('HOST' => '127.0.0.1', 'PORT' => 3306, 'USER' => 'root', 'PWD' => '', 'NAME' => 'test', 'CHARSET' => 'utf8'),
//                     // 'MySQL' => array('HOST' => '127.0.0.1', 'PORT' => 3306, 'USER' => 'root', 'PWD' => '', 'NAME' => 'mysql', 'CHARSET' => 'utf8')
//           			//'SQLITE' => array('PATH' => '/your/path/to/db')
//                 )
//         ),
		
		/* aliyun OSS 文件云上传 调用配置 */
// 		'OSS_KEYID' =>'6UrEQQ5HBU5S568Q',						//key
// 		'OSS_KEYSECRET' =>'mWZLf80JE4L1i2U8iijHtK3lEXtjd7',    //密匙
// 		'OSS_BUCKET' => 'csdk',                              //存储对象名称
// 		'OSS_ALIYUN_DOMAIN' => 'img.cisdaq.com',   //阿里云图片服务器绑定域名
// 		'OSS_ALIYUN_IMG_DISPOSE_DOMAIN' => 'img.cisdaq.com',   //阿里云图片处理服务器绑定域名
		
		/* redis数据库设置 */
		'REDIS_HOST' => '192.168.3.78',
		'REDIS_PORT' => '6379',
		'REDIS_CACHE_TIME' => 86400,

        'REDIS_CONNECT_CONFIG' => array( //redis 链接池配置
                'master' => array('host'=>'192.168.3.78', 'port'=>6379),  //主服务器配置
                'slave' => array( //从服务器配置
                        array('host'=>'192.168.3.78', 'port'=>6379),  //从服务器配置
                 )
        ),
        
		
		// 供服务调用方配置使用
        'SERVICE_API_HOST'=>'http://service_api.test.com/',               //服务Api主机地址
        'SERVICE_API_HOST_ENTRY'=>'index.php',          //服务Api主机入口文件地址
        'SERVICE_API_DEFAULT_MODULE'=>'Wap',            //服务Api默认Api模块名称,用于没有指定Api模块的时候进行默认值指定
        'SERVICE_API_REQUEST_SECKEY'=>'HTTP://WWW.CISDAQ.COM/_2016@CSDK_#@DU^^&JGK_((*&gjGH',      //服务Api请求安全密钥

		
		//==============================如下配置暂用不到=======================================
		/* 服务化系统接口配置 */
		'SERVICE_CONFIG' => array(
				'TC' => array(
						'sign_key' => 'F5689GHJKL54UI562CD54POIF6589GHBF5689GHJKL54UI562CD54POIF6589GHBF5689GHJKL54UI562CD54POIF6589GHBF5689GHJKL54UI562CD54POIF6589GHB',
						'host' => '192.168.3.102',
						'port' => '8080',
						'host_entry' => '/tc/service.html',
				),
		),
		
		'ACTIVEMQ_CONFIG' => array(
				'host' => '192.168.3.102',
				'port' => '61613',
		),
		/* 搜素服务配置项 */
		'SPHINX_CONF' => array(
				'host' => "192.168.3.56",
				'port' => 9312,
				'limits' => 1000,
				'query_time' => 3,
				'default_field' => "*,id",
				'source' => "main",
				#'search_model' => SPH_MATCH_EXTENDED2,
				#'order_by' => SPH_SORT_EXTENDED
		),
		'QUEUE_CONNECT_CONFIG' => array(  // 异步调用队列服务器配置
				'height' => array(  //高优先级服务器
						array('host' => '192.168.3.58', 'port'=>22133),
				),
				'low' => array(  //低优先级服务器
						array('host' => '192.168.3.53', 'port'=>22133),
				),
		),
		
		'QUEUE_WORKER_HOST' => array( //队列异步调用地址 SERVER_API 地址 开发环境可以填写本机内网地址
				'host' =>'http://api.cisdaq.com/',
		),
		
		'CORE_API_HOST' => 'http://core.xf9h.com/', //核心层地址
		'CORE_API_HOST_ENTRY' => 'index.php', //核心Api主机入口文件地址
		'CORE_API_DEFAULT_MODULE' => 'Core', //核心Api默认Api模块名称,用于没有指定Api模块的时候进行默认值指定
		'CORE_API_REQUEST_SECKEY' => 'HTTP://WWW.c9sdaq.COM/_2014@VACN_#@DU^^&JGK_((*&gjGH', //核心Api请求安全密钥
		
		'WAP_IMG_SIZE' => array(   //wap api 调用返回尺寸设定
				'banner' => array('width'=>300,'height'=>175),
				'goods_detail' => array('width'=>320,'height'=>320),
				'goods_detail_attr' => array('width'=>320,'height'=>320),
				'goods_list' => array('width'=>152,'height'=>152),
				'order' => array('width'=>106,'height'=>106),
				'other_thumb' => array('width'=>106,'height'=>106),
		),
);

