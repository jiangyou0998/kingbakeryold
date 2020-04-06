<?php
  require("connect.inc");
  session_start();     
  if (!($_SESSION[authenticated])) {
	$_SESSION['status'] = 'neverLogin';
	$_SESSION[UrlRedirect] = 'order.php';
	header('Location: login.php');
  }
  
  //檢查是否已登入
  if ($_SESSION[authenticated]) {
    $isLogin = true;
  }
  else {
    $isLogin = false;
  }
?>
<html>
<head>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<title>內聯網</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<META http-equiv="refresh" content="300">
<link href="class.css" rel="stylesheet" type="text/css">
<link href="css/bootstrap.min.css" rel="stylesheet"/>
<style type="text/css">
<!--
.style2 {color: #0000CC}
-->
</style>
<script>
function openwindow(){
	var advDays = 14;
	var winHeight = 300 + ((advDays - 5) * 27);

	orderWindow = window.open("select_day_dept.php?advDays="+advDays,"select_day","height=550,width=300,resizable=no,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no, status=no");

	orderWindow.moveTo(600,300);
}

</script>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >

<table id="Table_01" width="995" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td  colspan="13">
        <?php include "head.php"; ?></td>
	    <td>&nbsp;</td>
	</tr>
</table>
<br/>
<br/>
<div align="center" style="width:995px;" class="row">
	<div class="col-sm-4">
		<a href="lawEnforcementAgencyRecord_main.php?head=12"><img src="images/report_law.jpg" alt="執法部門巡查執告" width="150" height="150" border="0"></a>
	</div>
	<div class="col-sm-4">
		<a href="itsupport.php?head=12"><img src="images/report_it.jpg" alt="IT求助報告" width="150" height="150" border="0"></a>
	</div>
	<div class="col-sm-4">
		<a href="repair_project.php?head=12"><img src="images/report_PED.jpg" alt="維修項目報告" width="150" height="150" border="0"></a>		
	</div>
</div>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
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