<?php
    //檢查是否登錄,是否管理員
    require ("check_login.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<title>內聯網 - 後勤系統</title>
</head>

<frameset rows="*" cols="180,*" frameborder="yes" border="1" framespacing="0">
  <frame src="CRM_left.php" name="leftFrame" scrolling="No" noresize="noresize" id="leftFrame" title="leftFrame">
  <frameset rows="80,*" frameborder="yes" border="1" framespacing="0">
    <frame src="CRM_head.php" name="topFrame" scrolling="No" noresize="noresize" id="topFrame" title="topFrame">
    <frame src="CRM_body.php" name="mainFrame" id="mainFrame" title="mainFrame">
  </frameset>
</frameset>
<noframes><body>
</body>
</noframes></html>
