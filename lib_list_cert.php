<?php
session_start();
if (!($_SESSION[authenticated])) {
	$_SESSION['status'] = 'neverLogin';
	$_SESSION[UrlRedirect] = 'lib_list_cert.php';
	header('Location: login.php');
	die();
}
$_REQUEST[action] = "VIEW_ONLY";
$_REQUEST['type_id'] = 6;
?>
<html>
<head>
<title>內聯網</title>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=big5">
</head>
<?php include("head.php"); ?>
<?php include ("CMS_lib_list.php");?>
<table width="994" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><img src="images/TaiHing_23.jpg" width="994" height="49"></td>
</tr>
</table>
</html>