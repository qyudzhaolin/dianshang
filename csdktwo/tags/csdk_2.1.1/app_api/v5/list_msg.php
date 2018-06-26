<?php
require_once('base.php');
require_once('../function/Session.php');


define("PAGE_SIZE", 5);

$user_status = CommonUtil::verify_user();
CommonUtil::check_status($user_status);
$obj = new stdClass;
$obj->status = 500;

$page_num 	= isset($_POST['page'])?trim($_POST['page']):1;
$page_offset= PAGE_SIZE*$page_num - PAGE_SIZE;
$page_rows 	= PAGE_SIZE;

$uid		= trim($_POST["uid"]) ;
$sign_sn   	= trim($_POST["sign_sn"]) ;


	   $sql="select id,log_info,url,log_time from cixi_user_notify where user_id ={$uid} order by id desc limit {$page_offset}, {$page_rows}";
       $msg_info=  PdbcTemplate::query($sql,null,PDO::FETCH_OBJ);
       if(empty($msg_info))
        {
        		$obj ->status = 500;
			    $obj ->r = "无消息";
			    CommonUtil::return_info($obj);
	   	 	    return;
        }
  	$obj_msg = new stdClass;
     	  $obj_msg->msg_info=array();
     		if ($msg_info) {
				foreach($msg_info as $k=>$v){
					$info=new stdClass;
					//$style_info->id=$v->id;
					$info->title= is_null($v->log_info) ? "" : trim($v->log_info);
					$info->id= is_null($v->id) ? "" : $v->id;
					$info->url= is_null($v->url) ? "" : $v->url;
					$info->log_time= is_null($v->log_time) ? "" : $v->log_time;
					array_push($obj_msg->msg_info, $info) ;
				}
			}
   	
		 $obj->status = 200;
	  	 $obj->data = $obj_msg;
	  	 CommonUtil::return_info($obj);
?>