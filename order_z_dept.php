<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order_z_dept.php';
    header('Location: login.php');
}
if ($_SESSION[type] == 3 && $_GET['shop'] != 0) {
    $_SESSION[order_user] = $_GET['shop'];
}
$_SESSION['OrderDept'] = $_REQUEST['dept'];
$_SESSION['advance'] = $_REQUEST['advance'] + 0;
?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網-中央工場</title>
</head>
<frameset id="parentFrame" rows="*" cols="48%,*" frameborder="no" border="1" framespacing="0">
    <frame src="order_z_dept_left.php" name="leftFrame" scrolling="Yes" noresize="noresize" id="leftFrame"
           title="leftFrame"/>
    <frame src="order_z_dept_top.php" name="topFrame" scrolling="Yes" noresize="noresize" id="topFrame"
           title="topFrame"/>
</frameset>
</html>
