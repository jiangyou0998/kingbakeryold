<?php
session_start();
if (!($_SESSION[authenticated])) {
	$_SESSION['status'] = 'neverLogin';
	$_SESSION[UrlRedirect] = 'salesresult.php';
	header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");

$date1 = date("Y-m-d", gettimeofday('sec')-86400);
$date2 = date("Y-m-d", gettimeofday('sec')-86400);
if($_REQUEST[action] == "search"){
	$date1 = $_POST[date1];
	$date2 = $_POST[date2];
}


$sql = "SELECT int_id, chr_name FROM tbl_salesdata_item WHERE int_id IN(
	SELECT int_item_id FROM db_intranet.tbl_salesdata WHERE date(upload_date) BETWEEN '$date1' AND '$date2'
) ORDER BY int_sort";
//die($sql);
$result = mysqli_query($con, $sql) or die($sql);
$salesItemCount = mysqli_num_rows($result);
while($record = mysqli_fetch_assoc($result)){
	$item[$record[int_id]] = $record[chr_name];
}

$sql = "SELECT int_id, txt_name FROM db_intranet.tbl_user WHERE chr_type = 2 AND int_dept = 2
ORDER BY chr_ename;";
$result = mysqli_query($con, $sql) or die($sql);
while($record = mysqli_fetch_assoc($result)){
	$shop[$record[int_id]] = $record[txt_name];
}

$sql = "SELECT shop, int_item_id, SUM(int_price) as int_price FROM db_intranet.tbl_salesdata WHERE date(upload_date) BETWEEN '$date1' AND '$date2' GROUP BY shop, int_item_id;";
$result = mysqli_query($con, $sql) or die($sql);
while($record = mysqli_fetch_assoc($result)){
	$data[$record[shop]][$record[int_item_id]] = $record[int_price];
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>內聯網</title>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<link href="class.css" rel="stylesheet" type="text/css">
<link href="css/bootstrap.min.css" rel="stylesheet"/>
<script src="js/My97DatePicker/WdatePicker.js"></script>
<style>
<!--
	tr .item-td { 
		width:calc(100% / <?=$salesItemCount+1?>);
		border-right: 1px solid black;
		text-align:center;
	}
	tr .item-td:last-child { 
		border-right: 0px solid black;
	}
	table .item-tr {
		border-bottom: 1px solid black;
	}
	table .item-tr:last-child {
		border-bottom: 0px solid black;
	}
	#dataTable td{
		padding:0px 4px;
	}
-->	
</style>
</head>

<body>
<table id="Table_01" width="995" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td  colspan="13">
        <?php include "head.php"; ?></td>
	    <td>&nbsp;</td>
	</tr>
</table>
<div style="margin:1%;">
	
	<form action="?action=search" method="POST" align="center" style="width:995">
	查閱數據日期: 
	由 <input name="date1" onclick="WdatePicker();" size="10" style="text-align:center;" value="<?=$date1?$date1:date("Y-m-d", gettimeofday('sec'))?>"/> 
	至 <input name="date2" onclick="WdatePicker();" size="10" style="text-align:center;" value="<?=$date2?$date2:date("Y-m-d", gettimeofday('sec'))?>"/> 
	<input type="submit" value="查閱">
	</form>
	
	<br>
	數據日期：由 <?=$date1?$date1:date("Y-m-d", gettimeofday('sec'))?> 至 <?=$date2?$date2:date("Y-m-d", gettimeofday('sec'))?>
	<br/>
	<table id="dataTable" width="100%" cellspacing="0" cellpadding="0" border="1">
	<tr style="background-color:#CCFFCC;">
		<th style="min-width:160px; text-align:center;">分店</th>
		<?php foreach ($item as $i){ ?>
		<th class="item-td"><?=$i?></th>
		<? } ?>
		<th class="item-td">收入</th>
		<th class="item-td">差額</th>
	</tr>
	<? foreach($shop as $sid=>$s){?>
		<tr>
			<td><?=$s?></td>
			<? foreach($item as $itemID=>$itemName){
				$displayPrice = $data[$sid][$itemID]; ?>
			
			<td style="text-align:right"><?=number_format($displayPrice, 1, '.', ',')?></td>
			<? 
				$itemTotal[$itemID] += $displayPrice;
			} ?>
			<td style="text-align:right"><?=number_format("0.0", 1, '.', ',')?></td>
			<td style="text-align:right"><?=number_format("0.0", 1, '.', ',')?></td>
		</tr>
	<? $scount++;} ?>
	<tr style="background-color:#CCFFCC;">
		<th style="width:200px; text-align:center;">合計</th>
		<?php foreach ($item as $i){ ?>
		<th class="item-td"><?=$i?></th>
		<? } ?>
		<th class="item-td">收入</th>
		<th class="item-td">差額</th>
	</tr>
	<tr style="background-color:#CCFFCC;">
		<th style="width:200px; text-align:center;">分店: <?=$scount?></th>
		<?php foreach ($item as $itemID=>$itemName){ ?>
		<th class="item-td"><?=number_format($itemTotal[$itemID], 1, '.', ',')?></th>
		<? } ?>
		<th class="item-td"><?=number_format(0.0, 1, '.', ',')?></th>
		<th class="item-td"><?=number_format(0.0, 1, '.', ',')?></th>
	</tr>
	</table>
</div>	
</body>
</html>

















