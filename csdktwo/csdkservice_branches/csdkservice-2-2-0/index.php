<?php
/**
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: wangdongxing <wangdongxing@cisdaq.com>
 * +----------------------------------------------------------------------
 * |
 */

# 开启调试模式
define("APP_DEBUG", 1);

# 设置启动模式 1.curl应答模式 2.socket tcp 应答模式
defined('START_MODE')  or  define('START_MODE',  1);

# MYSQL监控模式
define('MONITOR', FALSE);                 // 是否开启全局监控，暂未启用
define('MONITOR_LOG_PIPELINE', 'REDIS');  // 设定监控日志数据存储介质  REDIS 或 SQLITE
define('MONITOR_MYSQL', FALSE);           // 是否开启MYSQL监控
define('MONITOR_MYSQL_RATE', 0);          // 设定MYSQL监控比率 0-100
define('MONITOR_MYSQL_THE',  MONITOR_MYSQL || mt_rand(0, 99) < MONITOR_MYSQL_RATE); // 是否开启MYSQL子监控

define('MONITOR_SYSLOG', true);                     // 是否记录mysql日志
define('MONITOR_SYSLOG_LONGTIME', 1000);            // 执行时间大于设定值则记录  // 1000 = 1s

# 设置系统路径
define('APP_PATH',dirname(__FILE__));

# 引入核心文件
require APP_PATH."/System/Core.class.php";

?>
