<?
//     session_start();
//     if (!($_SESSION[authenticated])) {
//       $_SESSION['status'] = 'neverLogin';
//       $_SESSION[UrlRedirect] = 'CMS_manage.php';
//       header('Location: login.php');
//     }

//檢查是否登錄,是否管理員
require("check_login.php");

?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>內聯網</title>
</head>

<frameset cols="120,*" frameborder="no" border="1" framespacing="0">
    <frame src="CMS_manage_left.php" name="subLeftFrame" scrolling="No" noresize="noresize" id="subLeftFrame"
           title="subLeftFrame"/>
    <frame src="" name="subMainFrame" id="subMainFrame" title="subMainFrame"/>
</frameset>
<noframes>
    <body>
    </body>
</noframes>
</html>
