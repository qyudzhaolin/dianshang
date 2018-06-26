<?php
/**
 * 公共参数----API
 * +----------------------------------------------------------------------
 * | cisdaq 磁斯达克
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.cisdaq.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: songce <soce@cisdaq.com>
 * +----------------------------------------------------------------------
 * | Version: All
 * +----------------------------------------------------------------------
 * |
 */
define ( 'RUN_ENV', isset ( $_SERVER ['RUN_ENV'] ) ? $_SERVER ['RUN_ENV'] : 'develop' );
define ( 'ROLE_INITIATOR', "0" );
define ( 'ROLE_INVESTOR', "1" );
define ( 'IMG_DOMAIN', "http://img.cisdaq.com/" );
define ( 'BP_DOMAIN', "http://bp.cisdaq.com/" );
define ( 'BP_URL', "http://www.cisdaq.com/bp_viewer/get_bp1.php?key=" );
define ( 'ACCESSKEY', "5CQmagmT27DNXYhsmVewntOpd9VLGD8sC5c02ptg" );
define ( 'SECRETKEY', "lQm-akaIb3JOS-WAlBycTXF_95hx7JEd9s88ARk5" );
require_once ('../conf/' . RUN_ENV . '/conf_db.php');
require_once ("CommonUtil.php");
require_once ("PdbcTemplate.php");
require_once ("Waf.php");
require_once ("Api_common.php");


?>