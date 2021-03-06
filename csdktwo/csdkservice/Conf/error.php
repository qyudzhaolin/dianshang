<?php

/*
 * | 错误配置文件
 */

return array(
    /* E_USER_ERROR E_ALL E_USER_NOTICE */
    'ERROR_LIVE' => array(E_USER_ERROR, E_PARSE), # 一般级别错误记录

    /* E_ERROR E_CORE_ERROR E_PARSE */
    'FATAL_ERROR_LIVE' => array(E_ERROR, E_CORE_ERROR, E_PARSE, E_USER_ERROR, E_COMPILE_ERROR), # 致命级别错误记录
    'ERROR' => # 所有错误消息集合
    array(
        1 => '调用的类不存在.',
        2 => '调用的类方法不存在.',
        3 => '未知异常.',
        4 => '调用方式不正确.',
        5 => '参数异常',
        6 => '代码发生严重错误!',
        7 => '执行过程异常抛出!',
        8 => '系统执行异常.',
        9 => '返回消息未定义',
        10 => '请求未授权',
        11 => '代码执行过程发生致命错误',
        12 => '数据库写入失败',
    		
    	14 => '股份确认已完成',
    		
    	//我的收益首页数据
    	20 =>'暂无收益', 
    	21 =>'暂无收益详情',

    	//我的基金列表数据
    	31 =>'暂无基金列表数据',
    	32 =>'参数错误',
    		
    	//意向投资人列表
    	41=>'暂无数据',	
    	42=>'此用户没有查看列表权限',
    		
    		
    		
    	1001 => '必要的参数没有全部正确的提供',
    	1002 => '请求的数据不存在或者已经被删除',
		// 基金投资占比计算
		    
        
        // 文件上传相关操作
        2001 => '只允许上传gif、jpg、png、pdf格式',
        2009 => '文件上传失败',
        
        //短信发送相关操作
        3000 => '没有实例化SMS服务',
        3001 => '无电话号码（白名单过滤后）',
        
    	
    	
        //极品错误码, 用于提示所有无法正确描述的情况--liujing
        8888 => '对不起, 系统繁忙, 请再试一次或者稍后重试!',
        9999 => '对不起, 系统繁忙, 请再试一次或者稍后重试',
        
    ),
);

