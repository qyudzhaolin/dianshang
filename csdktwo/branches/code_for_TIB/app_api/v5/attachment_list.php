<?php
require_once('base.php');
define("PAGE_SIZE", 10);
$obj = new stdClass();
$obj->status = 500;
$uid = isset ( $_POST ['uid'] ) ? trim ( $_POST ['uid'] ) : NULL;
$sign_sn = isset ( $_POST ['sign_sn'] ) ? trim ( $_POST ['sign_sn'] ) : NULL;
$fund_id = isset ( $_POST ['fund_id'] ) ? trim ( $_POST ['fund_id'] ) : NULL;
if(is_null($fund_id))
{
	$obj-> r = "基金ID 为空";
	CommonUtil::return_info ( $obj );
	return;
	
}
$page_num 	= isset($_POST['page_num'])?trim($_POST['page_num']):2;
$page_offset= PAGE_SIZE*$page_num - PAGE_SIZE;
$page_rows 	= PAGE_SIZE;

/**** Get attachment list ****************/
$sql_attachment = "
					SELECT id, title, attachment, type
					FROM cixi_fund_attachment
					WHERE fund_id = ? and is_del=1 order by id desc
					limit 		$page_offset, $page_rows
					";

$para_fund_id = array($fund_id);
$result_attachment = PdbcTemplate::query($sql_attachment,$para_fund_id,PDO::FETCH_OBJ, 0);

$obj->fund_attachment = array();
if(!empty($result_attachment)){
	foreach($result_attachment as $k => $v){
		$attachment = new stdClass();
		$attachment -> att_id = is_null($v->id) ? "" : $v->id;
		$attachment -> att_title = is_null($v->title) ? "" : $v->title;
		$attachment -> att_type = is_null($v->type) ? "" : $v->type;
		if($v->type==1){
			$attachment -> att_url = is_null($v->attachment) ? "" : IMG_DOMAIN.$v->attachment;
		}
		else
		{
			$attachment -> att_url = is_null($v->attachment) ? "" :BP_URL. $v->attachment;
		}
		array_push($obj->fund_attachment, $attachment);
	}
	$obj->status = 200;	
}
else {
	$obj-> r = "暂无数据";
	$obj-> status = 286;
}
CommonUtil::return_info ( $obj );
?>