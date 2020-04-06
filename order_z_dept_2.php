<?php
  session_start();
  if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order_z_dept.php';
    header('Location: login.php');
  }
  require($DOCUMENT_ROOT . "connect.inc");
  $timestamp = gettimeofday("sec");

  $action = $_GET['action'];
  switch ($action) {
    case 'confirm':
      foreach ($_POST as $key=>$value) {
        $sql = "UPDATE tbl_order_z_dept SET int_qty = '$value' ";
        $sql .= "WHERE int_id = '$key' ";

        mysqli_query($con, $sql) or die($sql);
      }
    break;
  }
  
  $advance = $_SESSION['advance'];
?>
<html>
<head>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>內聯網</title>
</head>
<style type="text/css">
<!--
.style2{
	-webkit-filter: grayscale(1); /* Webkit */
	filter: gray; /* IE6-9 */
	filter: grayscale(1); /* W3C */
}
.style3{
	cursor: pointer;
}
-->
</style>

<body>
<table width="640" height="480" border="1" cellpadding="10" cellspacing="2">
  <tr>
    <td align="center" valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="2">
<?php
  $sql = "
	SELECT 
		tbl_order_z_dept.int_id AS orderID,
		tbl_order_z_menu.chr_name AS itemName,
		tbl_order_z_menu.chr_no,
		tbl_order_z_unit.chr_name AS UoM,
		tbl_order_z_dept.int_qty,
		tbl_order_z_dept.status,
		LEFT(tbl_order_z_cat.chr_name, 2) AS suppName,
		tbl_order_z_menu.chr_cuttime,
		tbl_order_z_menu.int_phase
	FROM
		tbl_order_z_dept
			INNER JOIN tbl_order_z_menu ON tbl_order_z_dept.int_product = tbl_order_z_menu.int_id
			INNER JOIN tbl_order_z_unit ON tbl_order_z_menu.int_unit = tbl_order_z_unit.int_id
			INNER JOIN tbl_order_z_group ON tbl_order_z_menu.int_group = tbl_order_z_group.int_id
			INNER JOIN tbl_order_z_cat ON tbl_order_z_group.int_cat = tbl_order_z_cat.int_id
	WHERE
		tbl_order_z_dept.int_user = '$_SESSION[user_id]'
			AND tbl_order_z_dept.status IN (0 , 1)
			AND tbl_order_z_dept.int_qty > 0
			AND tbl_order_z_dept.chr_phase = '$_SESSION[advance]'
			AND tbl_order_z_dept.chr_dept = '$_SESSION[OrderDept]'
			AND tbl_order_z_menu.int_id
	ORDER BY tbl_order_z_dept.order_date DESC , 
			 tbl_order_z_group.int_cat , 
			 tbl_order_z_group.int_sort;";

  $result = mysqli_query($con,$sql) or die($sql);
  $count = 0;
  WHILE($record = mysqli_fetch_array($result)) {
    if ($count & 1) {
      $bg = "#F0F0F0";
    } else {
      $bg = "#FFFFFF";
    }
    $count += 1;
?>
      <tr bgcolor="<?php echo $bg;?>">
        <td width="10" align="right"><?=$count;?>.</td>
        <td><font color=blue size=-1><?=$record[6];?> </font><?="$record[1], $record[2]";?></td>
        <td align="center">
		<?php
		  if ($record[5] == 0) echo "<font color=red size=-1>(新)</font>";
		  if($record['int_phase']-1 == $_SESSION['advance'] && $record[chr_cuttime] <= date('Hi',$timestamp)){
		    echo "<img src='images/alert.gif' width='20' height='20'>";
		    $haveoutdate = 1;
		  }
		?>
        </td>
        <td width="107" align="center">x <?=$record[4];?><a href=""></a></td>
        <td width="107" align="center"><?=$record[3];?></td>
      </tr>
<?php
  }
?>
      <tr>
        <td colspan="5">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" valign="middle">分店：<?=$_SESSION[user]?><br>柯打日期：<?=date('Y/n/j',$timestamp)?><br>柯打合共：<?=$count;?><br><strong><font size="+3" color="#FF0000">預落<?=$_SESSION['advance'];?>天</font></strong></td>
        <td colspan="2" align="right">
			<a href="order_z_dept.php?advance=<?=$_SESSION[advance]?>&dept=<?=$_SESSION[OrderDept]?>"><img src="images/Amend.jpg" width="106" height="60" border="0"></a>
			<a href="order_z_dept_3.php?dept=<?=$_REQUEST['dept']?>&advance=<?=$advance?>"><img src="images/Confirm.jpg" width="106" height="60" border="0" class="style3"></a>
		</td>
      
	  </tr>
    </table>
    <p><?php if($haveoutdate == 1) echo "<strong><u><font color='#FF0000' size='5'><img src='images/alert.jpg' width='20' height='20'>產品已截單，<br><img src='images/alert.jpg' width='20' height='20'>產品需自行致電貨倉落貨。</font></u></strong>";?>&nbsp;</p>
    <p><font color="#FF0000" size="4">落貨前請確認清楚柯打 !!</font></p></td>
  </tr>
</table>
</body>
</html>