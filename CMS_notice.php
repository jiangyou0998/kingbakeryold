<?php require($DOCUMENT_ROOT . "connect.inc");?>
<?php
	session_start();
	
	$showExpiry = "Y";
?>
<html>
<head>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>內聯網 - 後勤系統</title>
<script>
function confirmation(aa) {
    var answer = confirm('通告編號：' + aa + '\n\n是否確認 ?')
    if (answer){
      location.href='CMS_notice_new_no.php?action=new';
    } else {
      return false;
    }
}
function del_notice(aa){
 	var answer = confirm('通告編號：' + aa + '\n\n是否確認刪除 ?')
    if (answer){
      location.href='CMS_notice_new_no.php?action=del&int_no='+aa;
    } else {
      return false;
    }
}
</script>
</head>
<body>
<center>
<?php
  $sql_no = "SELECT MAX(int_no)+1 AS N_NO FROM tbl_notice";
  $result_no = mysqli_query($con, $sql_no) or die("invalid query");
  $record_no = mysqli_fetch_array($result_no);
?>
<input name="getno" type="button" id="getno" value="取得通告編號" onClick="confirmation(<?php echo $record_no[0];?>);">　
<br/>
<br/>
<!-- <a href="/notice/通告格式.doc" target="_blank">下載通告範本</a>　 -->
<!-- <a href="/notice/通告格式_簡介.pdf" target="_blank">通告格式簡介</a>　 -->
</center>
<table width="100%" border="1" cellspacing="1" cellpadding="3">
  <tr height="20px">
    <td width="8%"  align="left"><strong>取號時間</strong></td>
    <td width="8%" align="left"><strong>負責同事</strong></td>
    <td width="7%" align="left"><strong>編號</strong></td>
    <td width="9%" align="left"><strong>到期時間</strong></td>
    <td width="27%" align="left"><strong>主旨</strong></td>
    <td width="31%" align="left"><strong>檔案名稱</strong></td>
	  <td width="31%" align="left"><strong>原始檔案名稱</strong></td>
    <td width="10%" align="left">&nbsp;</td>
  </tr>
  <?php
  
  $sql = "SELECT * FROM tbl_notice ";
  $sql .= "WHERE date_delete IN ('2000-01-01','2000-01-02') ";
  if(!$showExpiry) {
	$sql .= "AND (date_last >= DATE(NOW()) OR date_last IS NULL) ";
  }
  $sql .= "AND int_dept IN (SELECT int_dept_id FROM tbl_dept_v_user WHERE int_user_id = $_SESSION[user_id])";
  $sql .= "ORDER BY date_modify DESC, int_id DESC ";
 if ($_REQUEST[pageno] == "") {
    $page = 1;
  } else {
    $page = $_REQUEST[pageno];
  }
  $sql .= "LIMIT " . ($page-1) * 14 . ", 14";
  $count = 1;
  
  $result = mysqli_query($con, $sql) or die("invalid query");
  
  while($record=mysqli_fetch_array($result)){
	 
    if ($count & 1) {
      $bg = "#FFFFFF";
    } else {
      $bg = "#FFFFCC";
    }
    $count += 1;
?>
  <tr>
    <td width="8%" height="29" align="left" bgcolor="<?php echo $bg;?>"><?php echo $record[6];?></td>
    <td width="8%" align="left" bgcolor="<?php echo $bg;?>">
<?php
  $sql_user = "SELECT txt_name FROM tbl_user WHERE int_id = $record[4]";
  $result_user = mysqli_query($con, $sql_user) or die("invalid query");
  $record_user = mysqli_fetch_array($result_user);
  echo $record_user[0];?></td>
 
    <td width="7%" align="left" bgcolor="<?php echo $bg;?>"><?php echo $record[8];?></td>
    <td width="9%" align="left" bgcolor="<?php echo $bg;?>">&nbsp;<?php echo $record[9];?></td>
    <td width="27%" align="left" bgcolor="<?php echo $bg;?>"><?php if ($record[1] == "") echo "---"; else echo $record[1];?></td>
    <td width="31%" align="left" bgcolor="<?php echo $bg;?>"><?php if ($record[3] == "") echo "---"; else echo $record[3];?></td>
	  <td width="31%" align="left" bgcolor="<?php echo $bg;?>"><?php if ($record['first_path'] == "") echo "---"; else echo ' <a href="notice/'.$record['first_path'].'" target="_blank">'.$record['first_path'].'</a>';?></td>
    <td width="10%" align="left" bgcolor="<?php echo $bg;?>">
    <input name="del" type="button" id="del" value="刪除" onClick="del_notice(<?php echo $record[8] ?>)">
    <?php if ($record[1] == "") {?>
      <input name="upload" type="button" id="upload" value="上載" onClick="location.href='CMS_notice_2.php?action=upload&intid=<?php echo $record[0];?>&intno=<?php echo $record[8];?>'">
    <?php } else echo "&nbsp;";?></td>
  </tr>
  <?php
}
?>
</table>
<table width="100%" border="0" cellspacing="3" cellpadding="3">
          <tr>
            <td width="100%" height="24" align="right">
  <?php
  $sql = "SELECT count(*) FROM tbl_notice ";
  $sql .= "WHERE date_delete IN ('2000-01-01','2000-01-02') ";
if(!$showExpiry) {
	$sql .= "AND (date_last >= DATE(NOW()) OR date_last IS NULL) ";
}
  $sql .= "AND int_dept IN (SELECT int_dept FROM tbl_user WHERE int_id = $_SESSION[user_id])";
  $sql .= "ORDER BY date_modify DESC, int_id DESC ";
  $result = mysqli_query($con, $sql) or die("invalid query");
  $record = mysqli_fetch_array($result);
  $countPage = ceil($record[0]/14);
  echo "第".$page."頁&nbsp;\&nbsp;共".$countPage."頁&nbsp;&nbsp;&nbsp;&nbsp;";
  if ($page != 1){
    echo "<a href=\"?pageno=" . ($page-1) . "\">上一頁</a>";
  }else{
	echo "上一頁";
  }
  echo "&nbsp;&nbsp;";
  if ($page<$countPage) {
    echo "<a href=\"?pageno=" . ($page+1) . "\">下一頁</a>";
  }else{
	echo "下一頁";
  }
?>
</td>
</tr>
</table>
</body>
</html>

