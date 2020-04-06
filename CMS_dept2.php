<?php
  session_start();
  if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'dept.php';
    header('Location: login.php');
  }
  require($DOCUMENT_ROOT . "connect.inc");
  $timestamp = gettimeofday("sec");
?>
<html>
<head>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta charset="utf-8"/>
<script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="js/json2.js" type="text/javascript"></script>
<script src="js/select.js" type="text/javascript"></script>
<script src="js/My97DatePicker/WdatePicker.js"></script>
<link href="css/select.css" rel="stylesheet" type="text/css" />
<title>內聯網</title>
<script type="text/javascript">
$(function(){
	$('#select').Select();
});
</script>
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
-->
</style>

<script type="text/javascript">
function isNumber(event) {
  if (event) {
    var charCode = (event.which) ? event.which : event.keyCode;
    if (charCode > 31 && 
       (charCode < 48 || charCode > 57) && 
       (charCode < 96 || charCode > 105) && 
       (charCode < 37 || charCode > 40) && 
        charCode != 8 && charCode != 46 || event.shiftKey || charCode == 190)
       return false;
  }
  return true;
}
function timePick(){
	WdatePicker({ dateFmt:'HH:mm' });
}
</script>

</head>
<body>
<?php
  switch ($_REQUEST[action]) {
    case "new":
?>
<form name="search" action="" method="post">
<input type="hidden" name="action" value="add">
<table width="90%" border="0" cellspacing="1" cellpadding="6">
  <tr>
    <td bgcolor="#EB8201"><span class="style1">設定 &gt; 細類 &gt; 新增</span></td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">部門名稱　
        <input name="name" type="text" id="name" size="50"></td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">　排　序　
        <input name="sorting" type="text" id="sorting" value="999" size="5" maxlength="3" onKeyDown="return isNumber(event);"></td>
  </tr>
	
  <tr>
    <td bgcolor="#EEEEEE">　　　　　
        <input type="submit" name="Submit2" value="確認">
      <input name="back" type="button" id="back" value="返回" onClick="history.go(-1);"></td>
  </tr>
</table>
</form>
<?php
    break;
    case "edit":
		$sql = "SELECT int_id,txt_dept,int_sort
		FROM tbl_dept
		WHERE int_id = $_REQUEST[id]";
      $result = mysqli_query($con, $sql) or die($sql);
      $record = mysqli_fetch_array($result);
	  //print_r($sql);
?>
<form name="search" action="" method="post">
<input type="hidden" name="action" value="confirm">
<input type="hidden" name="id" value="<?php echo $_REQUEST[id];?>">
<input name='chkcuttime' value='0' type='hidden' />
<table width="90%" border="0" cellspacing="1" cellpadding="6">
  <tr>
    <td bgcolor="#EB8201"><span class="style1">設定 &gt; 部門 &gt; 修改</span></td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">部門名稱　
        <input name="name" type="text" id="name" value="<?php echo $record[txt_dept];?>" size="50"></td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">　排　序　
        <input name="sorting" type="text" id="sorting" value="<?php echo $record[int_sort];?>" size="5" maxlength="3" onKeyDown="return isNumber(event);"></td>
  </tr>
  
  <tr>
    <td bgcolor="#EEEEEE">　　　　　
        <input type="submit" name="Submit2" value="確認">
      <input name="back" type="button" id="back" value="返回" onClick="history.go(-1);"></td>
  </tr>
</table>
</form>
<?php
    break;
    case "delete":
      $sql = "UPDATE tbl_dept SET bl_isvalid = '0' ";
      $sql .= "WHERE int_id = '$_REQUEST[id]' ";
      mysqli_query($con, $sql) or die($sql);
    break;
    case "add":
	  $sql = "INSERT INTO tbl_dept(txt_dept, int_sort) VALUE('$_REQUEST[name]','$_REQUEST[sorting]');";
      mysqli_query($con, $sql) or die($sql);
		break;
    case "confirm":
	  
	  $sql = "UPDATE tbl_dept SET ";
      $sql .= "txt_dept = '$_REQUEST[name]', int_sort = '$_REQUEST[sorting]' ";
      $sql .= "WHERE int_id = $_REQUEST[id] ";
      mysqli_query($con, $sql) or die($sql);
	  
      break;
}
  IF (($_REQUEST[action] <> "new") AND ($_REQUEST[action] <> "edit")) {
?>
  <table width="90%" border="0" cellspacing="1" cellpadding="6">
    <tr>
      <td bgcolor="#EB8201"><span class="style1">設定 &gt; 部門 &gt; 完成</span></td>
    </tr>
    <tr>
      <td height="100" align="center" bgcolor="#EEEEEE"><a href="CMS_dept.php">更新完成</a></td>
    </tr>
  </table>
<?php
  }
      
?>
</body>
</html>