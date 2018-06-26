<?php
require_once('../system/common.php');
$key	= isset($_REQUEST['key'])? trim($_REQUEST['key']):'Fl8jvaD40eiPWHmPt_UjwbTSysCk';
if (!is_null($key)) {
	$bp_url=getQiniuPath($key,"bp");
	$bp_url=$bp_url."&attname=cisdaq.pdf";
}

?>

<html style="height: 100%; overflow: hidden;"><head>
<meta http-equiv="content-type" content="text/html; charset=utf8">
    <title>磁斯达克</title>
    <script type="text/javascript" src="js/pdfobject.js"></script>
    <script type="text/javascript">

      window.onload = function (){
        var myPDF = new PDFObject({ url: <?php echo $bp_url ?> }).embed();
      };

    </script>
  </head>
 
  <body style="margin: 0px; padding: 0px; height: 100%; overflow: hidden;"><object data="<?php echo $bp_url ?>" type="application/pdf" height="100%" width="100%"></object></body></html>