<?php
return array(
		// umeng push 
		'UMENG_PRODUCTION_MODE'=>'true',
		'UMENG_ANDROID_APP_KEY'=>'55947b4b67e58e255c002744',
		'UMENG_ANDROID_APP_SECRET'=>'76kipuvxwcrvjehl9a8d67skvl7eyopx',
		'UMENG_IOS_APP_KEY'=>'559600d067e58e55f90024d3',
		'UMENG_IOS_APP_SECRET'=>'dp624tiownq1360r6ajjbaroqm7zcj0h',
		
		// 数据库配置信息
		'DBHELPER' => array(
				'DB_TABLE_PREFIX' => 'cixi_', //数据库表前缀
				'MASTER_BEAR_READ' => true, //主服务器负担读操作
				'MASTER' => array(//主服务器
						'HOST' => '101.201.53.22', // 服务器地址
						'PORT' => 3306, // 端口
						'USER' => 'csdk', // 用户名
						'PWD' => 'sfh#$%233&', // 密码
						'NAME' => 'csdk', // 数据库名
						'CHARSET' => 'utf8'    // 数据库编码默认采用utf8
				),
				'SLAVE' => array(//从服务器
						// 从服务器1,2,3.....n
						// array('HOST' => '127.0.0.1', 'PORT' => 3306, 'USER' => 'root', 'PWD' => '', 'NAME' => 'vacn_test', 'CHARSET' => 'utf8'),
						// array('HOST' => '127.0.0.1', 'PORT' => 3306, 'USER' => 'root', 'PWD' => '', 'NAME' => 'vacn_test', 'CHARSET' => 'utf8')
				),
				'OTHER' => array(//其他第三方应用数据库配置
						// 'TEST' => array('HOST' => '127.0.0.1', 'PORT' => 3306, 'USER' => 'root', 'PWD' => '', 'NAME' => 'test', 'CHARSET' => 'utf8'),
						// 'MySQL' => array('HOST' => '127.0.0.1', 'PORT' => 3306, 'USER' => 'root', 'PWD' => '', 'NAME' => 'mysql', 'CHARSET' => 'utf8')
						//'SQLITE' => array('PATH' => '/your/path/to/db')
				)
		),
);

?>