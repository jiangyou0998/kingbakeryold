<?php 
	require($DOCUMENT_ROOT . "connect.inc");
	$timestamp = gettimeofday("sec")+28800;
	
	$shopID = $_GET[shop];
	$order_date = $_GET[date];
	if($_GET[action] == 'confirm'){
		$sql = "SELECT MAX(int_no) as no FROM tbl_order_z_receipt;";
		$result = mysqli_query($con, $sql) or die($sql);
		$record = mysqli_fetch_assoc($result);
		if($record[no]){
			$newNo = $record[no];
			$newNo++;
		}else{
			$newNo = 1;
		}
		$sql = "INSERT INTO tbl_order_z_receipt (int_no, order_date, int_user) VALUES ($newNo, '$order_date', $shopID)";
		mysqli_query($con, $sql) or die($sql);
		echo "<script>location='order_z_receipt.php?receiptNo=$newNo&shop=$shopID&date=$order_date'</script>";
		exit();
	}
	
	$receipt_no = $_GET[receiptNo];
	
	$sql = "SELECT * FROM tbl_user WHERE int_id = $shopID";
	$result = mysqli_query($con, $sql) or die($sql);
	$shop = mysqli_fetch_array($result);
	
	$sql = "SELECT T1.chr_no, T1.chr_name as itemName, 
		SUM(T0.int_qty_received) as qty, 
		T1.int_default_price,
		T4.int_id as dept,
		T4.chr_name as deptName,
		T4.int_sort,
		T2.chr_name as unitName
	FROM tbl_order_z_dept T0
		LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
		LEFT JOIN tbl_order_z_unit T2 ON T1.int_unit = T2.int_id
		LEFT JOIN tbl_order_z_group T3 ON T1.int_group = T3.int_id
		LEFT JOIN tbl_order_z_cat T4 ON T3.int_cat = T4.int_id
	WHERE DATE(DATE_ADD(T0.order_date, INTERVAL 1+T0.chr_phase DAY)) = '$order_date' and int_user = $shopID AND T0.status <> 4
	GROUP BY T1.chr_no, T1.chr_name, T4.int_id, T4.int_sort, T1.int_default_price, T2.chr_name, T4.chr_name
	ORDER BY T4.int_sort, T1.chr_no;";
//	var_dump($sql);
	$result = mysqli_query($con, $sql) or die($sql);

?>
<html>
<head>
	<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
	<title>內聯網</title>
	<link href="js/My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
	<script src="js/jquery-1.9.1.min.js"></script>
	<script src="js/My97DatePicker/WdatePicker.js"></script>
	<script src="js/parser.js"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet"/>
	<style>	
		<!--
		.style1 {font-size: 34px}
		.style3 {font-size: 16px;}
		.style6 {font-size: 22px; font-weight: bold; }
		body {
			width: 21cm;
			height: 29.7cm; 
			margin-left: auto;
			margin-right: auto;
			padding:0px;
		}
		#content td{
			padding:4px;
		}
		-->
	</style>
</head>
<body>
	<div style="width:100%; height:100px; border:1px solid black;">Top Header</div>
	<br>
	<table style="width:100%">
		<tr>
			<td colspan="3" align="right">
				<?php if($receipt_no){?>
					編號:　<span style="display:inline-block; width:125px"><?=STR_PAD($receipt_no, 8, '0', STR_PAD_LEFT)?></span>
				<?php }else{ ?>
					<a href="?date=<?=$_GET[date]?>&shop=<?=$_GET[shop]?>&action=confirm"><button>確認發票</button></a>
				<?php }?>
				</td>
			</td>
		</tr>
		<tr>
			<td style="width:33%">&nbsp;</td>
			<td style="width:33%" align="center">分店:　<?=$shop[txt_name]?></td>
			<td style="width:33%" align="right">日期:　<span style="display:inline-block; width:125px"><?php echo $order_date; ?></span></td>
		</tr>
	</table>
	<br/>
	<table id="content" style="width:100%" cellspacing="0" cellpadding="0" border="2">
		<tr>
			<td style="font-weight:bold;" width="12%">編號</td>
			<td style="font-weight:bold;" width="30%">貨名</td>
			<td style="font-weight:bold;" width="12%" align="center">數量</td>
			<td style="font-weight:bold;" width="8%" align="center">單位</td>
			<td style="font-weight:bold;" width="12%" align="center">單價</td>
			<td style="font-weight:bold;" width="12%" align="center">折扣</td>
			<td style="font-weight:bold;" width="14%" align="center">實額</td>
		</tr>
		<?php 
		$count = 0;
        $qtyTotal = 0;
        $priceTotal = 0;
		while($record = mysqli_fetch_array($result)) {
//		    var_dump($record);
            if ($count == 0) {
                $dept = $record[dept];
                $deptName = $record[deptName];
                $count++;
            }
            ?>
            <?php
            if ($record[dept] != $dept) {
                $qtyTotal = number_format($qtyTotal, 2);
                $priceTotal = number_format($priceTotal, 2);
                echo "<tr style='border-bottom:4px solid black'>
					<td></td>
					<td align='right'>總件數=</td>
					<td align='right' style='border-right:0px'>$qtyTotal</td>
					<td style='border-left:0px'></td>
					<td align='right' colspan='2'>$deptName 金額=</td>
					<td align='right'>$$priceTotal</td>
				</tr>";
                echo '<tr>
					<td style="font-weight:bold;" width="12%">編號</td>
					<td style="font-weight:bold;" width="30%">貨名</td>
					<td style="font-weight:bold;" width="12%" align="center">數量</td>
					<td style="font-weight:bold;" width="8%" align="center">單位</td>
					<td style="font-weight:bold;" width="12%" align="center">單價</td>
					<td style="font-weight:bold;" width="12%" align="center">折扣</td>
					<td style="font-weight:bold;" width="14%" align="center">實額</td>
				</tr>';
                $dept = $record[dept];
                $deptName = $record[deptName];
                //部門不同,重新計算數量,總價
                $qtyTotal = 0;
                $priceTotal = 0;
                $qtyTotal += $record[qty];
                $priceTotal += $record[qty] * $record[int_default_price];
            } else {
                $qtyTotal += $record[qty];
                $priceTotal += $record[qty] * $record[int_default_price];
            }

            if ($record[qty]) {
                ?>
                <tr>
                    <td><?= $record[chr_no] ?></td>
                    <td><?= $record[itemName] ?></td>
                    <td align="right"><?= $record[qty] ?></td>
                    <td align="center"><?= $record[unitName] ?></td>
                    <td align="right">$<?= $record[int_default_price] ?></td>
                    <td align="right">$0.00</td>
                    <td align="right">$<?= number_format($record[qty] * $record[int_default_price], 2) ?></td>
                </tr>
            <?php }
        }
		$qtyTotal = number_format($qtyTotal,2);
		$priceTotal = number_format($priceTotal,2);
		?>
		<tr style='border-bottom:4px solid black'>
			<td></td>
			<td align='right'>總件數=</td>
			<td align='right' style='border-right:0px'><?=$qtyTotal?></td>
			<td style='border-left:0px'></td>
			<td align='right' colspan='2'><?=$deptName?> 金額=</td>
			<td align='right'>$<?=$priceTotal?></td>
		</tr>
	</table>
	
</body>


</html>