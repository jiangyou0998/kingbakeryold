<?php
//  session_start();
//  if (!($_SESSION[authenticated])) {
//    $_SESSION['status'] = 'neverLogin';
//    $_SESSION[UrlRedirect] = 'CMS_order.php';
//    header('Location: login.php');
//  }

//檢查是否登錄,是否管理員
require ("check_login.php");

  require($DOCUMENT_ROOT . "connect.inc");
  if($_REQUEST[action] == 'update'){
	  foreach($_REQUEST as $key=>$value){
		  if(is_numeric($key)){
			  $sql = "UPDATE tbl_order_z_menu SET int_sort = $value WHERE int_id = $key;";
			  mysqli_query($con, $sql);
		  }
	  }
  }
  
?>
<html>
<head>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="js/json2.js" type="text/javascript"></script>
<title>內聯網</title>
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
.style2 {color: #00356B}
.style3 {color: #FF0000}
.style4 {color: #D7710D}
.style5 {color: #008081}
-->
</style>
<script type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  var cat = $('#cat').val();
  var group = $('#group').val();
  var url;
  url  = "?cat=" + cat;
  url += "&group=" +group;
  location = url;
}

//-->
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
</script>
</head>
<body>
<form name="search" action="?action=search" method="post">
<table width="90%" border="0" cellspacing="1" cellpadding="6">
  <tr>
    <td bgcolor="#EB8201"><span class="style1">設定 &gt; 貨品</span></td>
  </tr>
</table>
</form>

<p>
<form action="" name="select" method="post">
<table width="95%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td rowspan="3">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td>
<select name="cat" id="cat" onChange="MM_jumpMenu()">
    <option value="0" <?php if($_REQUEST[cat]=="0") {echo "selected";} ?>>請選擇大類</option>
<?php
  $sql_select  = "SELECT int_id, chr_name, int_sort, status, int_page ";
  $sql_select .= "FROM tbl_order_z_cat WHERE status = 1 ORDER BY int_sort ";
  $result_select = mysqli_query($con, $sql_select) or die($sql_select);
  WHILE($record_select = mysqli_fetch_array($result_select)) {
?>
    <option value="<?=$record_select[int_id]?>" <?php if ($record_select[int_id] == $_REQUEST[cat]) echo "selected"?>>
		<?php echo $record_select[chr_name];?>
	</option>
<?php
  }
?>
  </select>
  <select name="group" id="group" onChange="MM_jumpMenu()">
    <option value="0" <?php if($_REQUEST[group]=="0") {echo "selected";} ?>>請選擇細類</option>
<?php
  $sql_select  = "SELECT T0.int_id, T0.chr_name, T0.int_sort, T0.status ";
  $sql_select .= "FROM tbl_order_z_group T0 ";
  $sql_select .= "LEFT JOIN tbl_order_z_cat T1 ON T0.int_cat = T1.int_id WHERE T0.status = 1 ";
  if($_GET['cat'])
	$sql_select .= "AND T0.int_cat = '$_GET[cat]' ";
  $sql_select .= "ORDER BY T1.int_sort, T0.int_sort ";
  $result_select = mysqli_query($con, $sql_select) or die($sql_select);
  WHILE($record_select = mysqli_fetch_array($result_select)) {
?>
    <option value="<?=$record_select[int_id]?>" <?php if ($record_select[int_id] == $_REQUEST[group]) echo "selected"?>>
		<?php echo $record_select[chr_name];?>
	</option>
<?php
  }
?>
  </select>
  
  <input name="Add" type="button" id="Add" value="新增貨品" onClick="document.location.href='CMS_order_menu2.php?action=new';">
  </td>
  
 </tr>
  		
 </table>
</tr>
</table>
</form>
</p>
<form name="update" action="CMS_order_menu.php?cat=<?=$_REQUEST[cat]?>&group=<?=$_GET[group]?>&action=update" method="post">
<table width="90%" border="0" cellspacing="1" cellpadding="6">
  <tr>
    <td width="10" align="center" bgcolor="#EB8201">&nbsp;</td>
    <td width="40" align="center" bgcolor="#EB8201"><span class="style1">排序</span></td>
    <td width="60" align="center" bgcolor="#EB8201"><span class="style1">編號</span></td>
    <td align="left" bgcolor="#EB8201"><span class="style1">名稱</span></td>
    <td width="180" align="center" bgcolor="#EB8201"><span class="style1">類別</span></td>
	<td width="60" align="center" bgcolor="#EB8201"><span class="style1">狀態</span></td>
    <td width="40" align="center" bgcolor="#EB8201"><span class="style1">更新</span></td>
    <td width="40" align="center" bgcolor="#EB8201"><span class="style1">刪除</span></td>
  </tr>
<?php
  $group = $_REQUEST[group];
  $cat = $_REQUEST[cat];
  $sql = "SELECT T0.int_id, T0.int_sort, T0.status, T0.chr_name, T0.chr_no, T0.chr_cuttime, T1.chr_name as group_name,
	T2.chr_name as cat_name
	FROM tbl_order_z_menu T0
		LEFT JOIN tbl_order_z_group T1 ON T0.int_group = T1.int_id
		LEFT JOIN tbl_order_z_cat T2 ON T1.int_cat = T2.int_id ";
  $sql .= "WHERE T0.status <> 4 ";
  if($cat)
	$sql .= "AND int_cat = $cat ";
  if($group)
	$sql .= "AND int_group = $group ";
  
  $sql .= "ORDER BY T0.int_sort, T0.int_id";
  $result = mysqli_query($con, $sql) or die($sql);
  $count = 1;
  WHILE($record = mysqli_fetch_array($result)) {
	if($group == 0){
		break;
	}
    IF ($count & 1) {
      $bg = "#FFFFFF";
    } ELSE {
      $bg = "#F2F2F2";
    }
?>
  <tr>
    <td width="10" align="center" bgcolor="<?php echo $bg;?>"><?php echo $count;?></td>
    <td width="40" align="center" bgcolor="<?php echo $bg;?>"><input name="<?php echo $record[int_id];?>" type="text" id="<?php echo $record[int_id];?>" value="<?php echo $record[int_sort];?>" size="2" maxlength="3" onKeyDown="return isNumber(event);"></td>
    <td width="60" align="center" bgcolor="<?php echo $bg;?>"><?=$record[chr_no]?></td>
    <td align="left" bgcolor="<?php echo $bg;?>"><?php echo $record[chr_name];?></td>
    <td width="180" align="center" bgcolor="<?php echo $bg;?>"><?php echo $record[cat_name];?>-<?php echo $record[group_name];?></td>
	<?php
	switch ($record[status]) {
		case 1 : $status = '<span class="style2">現貨</span>'; break;
		case 2 : $status = '<span class="style3">暫停</span>'; break;
		case 3 : $status = '<span class="style4">新貨</span>'; break;
		case 5 : $status = '<span class="style5">季節貨</span>'; break;
	}
	?>
	<td width="60" align="center" bgcolor="<?php echo $bg;?>"><?=$status?></td>
    <td width="40" align="center" bgcolor="<?php echo $bg;?>"><a href="CMS_order_menu2.php?action=edit&id=<?php echo $record[int_id];?>">更新</a></td>
    <td width="40" align="center" bgcolor="<?php echo $bg;?>"><a href="CMS_order_menu2.php?action=delete&id=<?php echo $record[int_id];?>">刪除</a></td>
  </tr>
<?php
    $count += 1;
  }
?>
</table>
<p>
  <input name="Update" type="submit" id="Update" value="更新排序">
</p>
</form>
</body>
</html>