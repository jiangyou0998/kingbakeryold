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
			  $sql = "UPDATE tbl_order_z_group SET int_sort = $value WHERE int_id = $key;";
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
-->
</style>
<script type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
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
    <td bgcolor="#EB8201"><span class="style1">設定 &gt; 使用者</span></td>
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
  <select name="menu1" onChange="MM_jumpMenu('self',this,1)">
    <option value="?dept=0&type=0"<?php if($_REQUEST[type]=="0") {echo "selected";} ?>>請選擇帳戶類型</option>
    <option value="?dept=0&type=2" <?php if ($_REQUEST[type] == 2) echo "selected"?>>分店</option>
    <option value="?dept=0&type=1" <?php if ($_REQUEST[type] == 1) echo "selected"?>>一般使用者</option>
    <option value="?dept=0&type=3" <?php if ($_REQUEST[type] == 3) echo "selected"?>>系統管理員</option>
  </select>
  <?php  if($_REQUEST['type'] <> 2 && $_REQUEST['type'] <> 0) { ?>
  <select name="menu2" onChange="MM_jumpMenu('self',this,1)">
	<option value="?type=<?=$_REQUEST[type]?>&dept=0"<?php if($_REQUEST[dept]=="0") {echo "selected";} ?>>選擇部門</option>
	<?php
	$sql = "SELECT * FROM tbl_dept WHERE bl_isvalid = 1";
	$result = mysqli_query($con, $sql) or die($sql);
	WHILE($record = mysqli_fetch_array($result)) { ?>
	<option value="?type=<?=$_REQUEST[type]?>&dept=<?=$record[int_id]?>"<?php if($_REQUEST[dept]==$record[int_id]) {echo "selected";} ?>><?=$record[txt_dept]?></option>
	<?php } ?>
  </select>
  <?php } ?>
  <input name="Add" type="button" id="Add" value="新增使用者" onClick="document.location.href='CMS_user2.php?action=new';">
  </td>
  
 </tr>
  		
 </table>
</tr>
</table>
</form>
</p>
<form name="update" action="CMS_user.php?dept=<?=$_REQUEST[dept]?>&type=<?=$_REQUEST[type]?>&action=update" method="post">
<table width="90%" border="0" cellspacing="1" cellpadding="6">
  <tr>
    <td width="10" align="center" bgcolor="#EB8201">&nbsp;</td>
    <td width="180" align="center" bgcolor="#EB8201"><span class="style1">登入名稱</span></td>
    <td align="left" bgcolor="#EB8201"><span class="style1">帳戶名稱</span></td>
    <?php 
	if($_REQUEST['type'] <> 2){
		echo '<td width="180" align="center" bgcolor="#EB8201"><span class="style1">部門</span></td>';
	}else{
		echo '<td width="180" align="center" bgcolor="#EB8201"><span class="style1">分店區域</span></td>';
	}
	?>
	<td width="180" align="center" bgcolor="#EB8201"><span class="style1">帳戶類型</span></td>
    <td width="40" align="center" bgcolor="#EB8201"><span class="style1">更新</span></td>
    <td width="40" align="center" bgcolor="#EB8201"><span class="style1">刪除</span></td>
  </tr>
<?php
  $type = $_REQUEST[type];
  $dept = $_REQUEST[dept];
  IF($dept && $type <> 2) $addSQL = "AND $dept IN (SELECT int_dept_id FROM tbl_dept_v_user WHERE int_user_id = T0.int_id)";
  IF (empty($type)) $type = 999;
  $sql = "SELECT T0.*, T1.txt_dept, T3.chr_name as arae FROM tbl_user T0
	LEFT JOIN tbl_dept T1 ON T0.int_dept = T1.int_id
	LEFT JOIN tbl_district T2 ON T0.int_district = T2.int_id
	LEFT JOIN tbl_order_z_area T3 ON T2.int_area = T3.int_id
  WHERE chr_type = $type 
	$addSQL
  ORDER BY T1.int_sort, T0.txt_login
  ";
  //die($sql);
  $result = mysqli_query($con, $sql) or die($sql);
  $count = 1;
  WHILE($record = mysqli_fetch_array($result)) {
    IF ($count & 1) {
      $bg = "#FFFFFF";
    } ELSE {
      $bg = "#F2F2F2";
    }
?>
  <tr>
    <td width="10" align="center" bgcolor="<?php echo $bg;?>"><?php echo $count;?></td>
    <td width="40" align="left" bgcolor="<?php echo $bg;?>"><?=$record[txt_login]?></td>
    <td align="left" bgcolor="<?php echo $bg;?>"><?php echo $record[txt_name];?></td>
    <?php 
	if($_REQUEST['type'] <> 2){
		$sql = "SELECT txt_dept FROM tbl_dept WHERE int_id IN($record[int_dept])";
		//die($sql);
		$user_depts = mysqli_query($con, $sql) or die($sql);
		while($user_dept = mysqli_fetch_assoc($user_depts)){
			$u_dept .= $user_dept[txt_dept] . "<br/>";
		}
		echo '<td width="180" align="center" bgcolor="'.$bg.'">'.$u_dept.'</td>';
		$u_dept = '';
	}else{
		echo '<td width="180" align="center" bgcolor="'.$bg.'">'.$record['arae'].'</td>';
	}
	?>
	<td width="180" align="center" bgcolor="<?php echo $bg;?>">
		<?php switch($record[chr_type]){
			case '1' : echo "一般使用者"; break;
			case '2' : echo "分店"; break;
			case '3' : echo "系統管理員"; break;
			
		}?>
	</td>
    <td width="40" align="center" bgcolor="<?php echo $bg;?>"><a href="CMS_user2.php?action=edit&id=<?php echo $record[int_id];?>">更新</a></td>
    <td width="40" align="center" bgcolor="<?php echo $bg;?>"><a href="CMS_user2.php?action=delete&id=<?php echo $record[int_id];?>">刪除</a></td>
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