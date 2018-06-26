<?php
//签名验证
$signature      = isset ( $_POST ['signature'] ) ? trim ( $_POST ['signature'] ) : NULL;
$mobile		    = isset($_POST['mobile'])? trim($_POST['mobile']):NULL;
$uid		    = isset($_POST['uid'])? trim($_POST['uid']):NULL;
$sign_sn	    = isset($_POST['sign_sn'])? trim($_POST['sign_sn']):NULL;
?>