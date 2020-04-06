<?php

session_start();
if (!($_SESSION[authenticated])) {
	$_SESSION['status'] = 'neverLogin';
	$_SESSION[UrlRedirect] = 'salesdata.php';
	header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");

$sql = "UPDATE tbl_order_z_dept SET status = 99 WHERE chr_po_no = $_POST[po] AND status = 98 AND int_user = $_SESSION[user_id];";
mysqli_query($con, $sql) or die($sql);
//echo "<PRE>";
//print_r($_REQUEST);
//echo "</PRE>";

?>
<html>
<head>
<title>內聯網</title>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/checkbox-style.css"/>
<link rel="stylesheet" type="text/css" href="/js/layui/css/layui.css">

<script>
	$(function(){
		setTimeout(function(){
			location='grpo.php';
		}, 3000);
	});
</script>

</head>

<body>
<table id="Table_01" width="995" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td  colspan="13">
        <?php include "head.php"; ?></td>
	    <td>&nbsp;</td>
	</tr>
</table>
<div align="center" style="width:995; padding:0px 0px;">
	<br/>
	<br/>
	<br/>
	<h1>收貨完成。</h1>
	
</div>
<br/>
<br/>
<br/>
<br/>
<br/>
<table>
<tr>
<td colspan="13">
	<img src="images/TaiHing_23.jpg" width="994" height="49" alt=""></td>
<td>
	<img src="images/spacer.gif" width="1" height="49" alt=""></td>
</tr>
</table>
</body>

</html>