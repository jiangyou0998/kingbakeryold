<?php
session_start();
if (!($_SESSION[authenticated])) {
	$_SESSION['status'] = 'neverLogin';
	$_SESSION[UrlRedirect] = 'repair_project.php';
	header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");

if($_POST[action] == "update"){
	//print_r($_POST);
	//die();
	$sql = "UPDATE tbl_repair_project SET last_update_date = NOW(), last_update_user = $_SESSION[user_id], txt_comment = '$_POST[comment]' WHERE int_id = $_POST[id]";
	mysqli_query($con, $sql) or die($sql);
	if($_POST[complete]){
		$sql = "UPDATE tbl_repair_project SET int_status = '99', complete_date = '$_POST[cDate]', handle_staff = '$_POST[staff]' WHERE int_id = $_POST[id]";
		
		mysqli_query($con, $sql) or die($sql);
	}
	
	echo "<script>top.location.href = 'repair_project.php'; window.close();</script>";
	exit();
}

$sql = "SELECT T0.int_id as pj_id, T0.chr_no, T0.int_important, T0.int_status, T0.chr_machine_code,
	DATE(T0.report_date) as date, DATEDIFF(CURDATE(), DATE(report_date)) as datediff, 
	chr_other, chr_pic, T1.chr_name as loc, T2.chr_name as itm, T3.chr_name as dtl, T4.txt_name as usr,
	T5.txt_name as update_usr, DATE(T0.last_update_date) as update_date, txt_comment, DATE(T0.complete_date) as complete_date, T0.handle_staff
FROM tbl_repair_project T0
	LEFT JOIN tbl_repair_loc T1 ON T0.int_repair_loc = T1.int_id
	LEFT JOIN tbl_repair_item T2 ON T0.int_repair_item = T2.int_id
	LEFT JOIN tbl_repair_detail T3 ON T0.int_repair_detail = T3.int_id
    LEFT JOIN tbl_user T4 ON T0.int_user = T4.int_id
	LEFT JOIN tbl_user T5 ON T0.last_update_user = T5.int_id
WHERE T0.int_id = $_GET[id];";

$result = mysqli_query($con, $sql) or die($sql);
$project = mysqli_fetch_assoc($result);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>內聯網</title>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script src="js/My97DatePicker/WdatePicker.js"></script>
<link href="My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
</head>
<style>
	* { font-size:18px; }
</style>
<body>
	<table style="width:100%" cellpadding="4px">
		<tr>
			<td style="width:30%">編號</td>
			<td><?=$project[chr_no]?></td>
		</tr>		
		<tr>
			<td>報告日期</td>
			<td><?=$project[date]?></td>
		</tr>
		<tr>
			<td>分店/部門</td>
			<td><?=$project[usr]?></td>
		</tr>		
		<tr>
			<td>緊急性</td>
			<td><?php
			switch ($project[int_important]){
				case "3":
				  echo "高";
				  break;  
				case "4":
				  echo "中";
				  break;
				default:
				  echo "低";
			}?>
			</td>
		</tr>		
		<tr>
			<td>求助事宜</td>
			<td><?=$project[loc]?> - <?=$project[itm]?> - <?=$project[dtl]?></td>
		</tr>		
		<tr>
			<td>#機器號碼</td>
			<td><?=$project[chr_machine_code]?></td>
		</tr>
	</table>
	<hr style="width:98%; margin:8px auto;">
	<?php if($_GET[type] == 1) {?>
	<form action="repair_project_handle.php" method="post">
	<input type="hidden" name="id" value="<?=$_GET[id]?>"/>
	<input type="hidden" name="action" value="update"/>
	<table style="width:100%" cellpadding="4px">
		<tr>
			<td style="width:30%; vertical-align:top;">跟進結果</td>
		</tr>
		<tr>
			<td colspan="2"><textarea style="margin: 0px; width: 100%; height: 100px; resize: none;" name="comment"><?=$project[txt_comment]?></textarea></td>
		</tr>			
		<tr>
			<td style="width:30%">已完成</td>
			<td>
				<input type="hidden" name="complete" value="0">
				<input type="checkbox" name="complete" value="1">
			</td>
		</tr>
		<tr>
			<td style="width:30%">完成日期</td>
			<td>
				<input name="cDate" onclick="WdatePicker();" size="10" value="<?=date("Y-m-d", gettimeofday('sec'))?>"/> 
			</td>
		</tr>
		<tr>
			<td style="width:30%">人員</td>
			<td>
				<input name="staff" size="10"/> 
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2"><input type="submit" value="提交" style="font-size:18px;"/></td>
		</tr>		
	</table>
	</form>
	<?php }else{ ?>
	<table style="width:100%" cellpadding="4px">
		<tr>
			<td style="width:30%; vertical-align:top;">跟進結果</td>
			<td style="word-break: break-all;"><?=$project[txt_comment]?></td>
		</tr>
		<tr>
			<td style="width:30%">完成日期</td>
			<td><?=$project['complete_date']?></td>
		</tr>
		<tr>
			<td style="width:30%">人員</td>
			<td><?=$project['handle_staff']?></td>
		</tr>
	</table>
	<?php } ?>
	<hr style="width:98%; margin:8px auto;">
	<table style="width:100%" cellpadding="4px">
		<tr>
			<td style="width:30%">最後更新</td>
			<td><?=$project['update_date']?></td>
		</tr>
		<tr>
			<td style="width:30%">負責人</td>
			<td><?=$project['update_usr']?></td>
		</tr>
	</table>
	
	
</body>
</html>