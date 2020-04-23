<?php

    //檢查是否登錄,是否管理員
    require ("check_login.php");

    require($DOCUMENT_ROOT . "connect.inc");
	$timestamp = gettimeofday("sec");
	if($_REQUEST[car]){
		$filterCar = $_REQUEST[car];
	}
	//echo "<PRE>";
	//die();
		
	$showDate = $_REQUEST[checkDate];
//	var_dump($showDate);
	$today = date('Y-m-d',$timestamp);
	IF ($showDate == "") $showDate = date('Y-m-d',$timestamp);

	$checkDate = explode("-",$showDate);
	$checkDate[1] = substr("00".$checkDate[1],-2);
	$checkDate[2] = substr("00".$checkDate[2],-2);
	$sqlDate = $checkDate[0]."-".$checkDate[1]."-".$checkDate[2];

	$report = null;
	$sql = "SELECT * FROM tbl_order_check WHERE int_id = $_GET[id] AND disabled = 0;";
	$result = mysqli_query($con, $sql) or die ($sql);
	$report = mysqli_fetch_assoc($result);

	//送貨時間
    $deli_date = date("Y-m-d",mktime(0,0,0,$checkDate[1],$checkDate[2]+$report[int_num_of_day],$checkDate[0]));
//	var_dump($deli_date);

	$type = $report['int_type'] - 1;
	$cat = 'tbl_order_z_cat';
	$grp = 'tbl_order_z_group';
	$menu = 'tbl_order_z_menu';
	$order = 'tbl_order_z_dept';
	$tbl = Array("cat"=>$cat, "grp"=>$grp, "menu"=>$menu, "order"=>$order);
	
	
	
	//prepare Menu
	$arySort = Array();
	$aryTempMenu = explode(", ", $report[chr_item_list]);
	
	foreach ($aryTempMenu as $value){
		$sort = explode(":", $value);
		$arySort[] = ("( $sort[0], $sort[1] )");
	}
	$search_id = join(", ", $arySort);
	
	
	$aryMenu = Array();
	$sql_d = "drop temporary table if exists tmp;";
	$sql_1 = "create temporary table tmp( sort int, id int);";
	$sql_2 = "insert into tmp VALUES $search_id;";
	$sql_3 = "SELECT sort, menu.chr_name, menu.chr_no FROM tmp LEFT JOIN tbl_order_z_menu menu ON menu.int_id = tmp.id WHERE menu.status <> 4 ORDER BY sort;";
	
	mysqli_query($con, $sql_d) or die ($sql_d);
	mysqli_query($con, $sql_1) or die ($sql_1);
	mysqli_query($con, $sql_2) or die ($sql_2);
	$result = mysqli_query($con, $sql_3) or die ($sql_3);
	mysqli_query($con, $sql_d) or die ($sql_d);
	
	while($record = mysqli_fetch_assoc($result)){
		$aryMenu[$record[chr_no]] = $record;
	}
	$code = "";
	$code .= "'";
	foreach($aryMenu as $menu){
		$code .= "$menu[chr_no]','";
	}
	$code = rtrim($code, ",'");
	$code .= "'";
	//print_r($aryMenu);
	//prepare Menu
	
	//prepare shop
	$aryAllShop = Array();
	$sql = "SELECT int_id, chr_ename, txt_name FROM tbl_user WHERE int_dept = 2 ORDER BY int_sort ";
	$result = mysqli_query($con, $sql) or die ($sql);
	while($record = mysqli_fetch_assoc($result)){
		if($record[chr_ename] < 100)
			$record[type] = "BKG";
		else if ($record[chr_ename] > 100)
			$record[type] = "RBS";
		else
			$record[type] = "OTHER";
		
		$aryAllShop[] = $record;
	}
	//print_r($aryAllShop);
	$aryDisplayShop = Array();
	if($report[int_all_shop] != 1){
		$aryExtra = explode(",", $report[chr_shop_list]);
		
		foreach($aryAllShop as $key => $shop){
			if(in_array($shop[int_id], $aryExtra)){
				$aryDisplayShop[] = $shop[int_id];
				continue;
			}
			$aryDisplayShop[] = $shop[int_id];
		}
	}else{
		foreach($aryAllShop as $key => $shop){
			$aryDisplayShop[] = $shop[int_id];
		}
	}
	//prepare shop
	
	//prepare report
	$addOrder = $_GET[addOrder];
	$aryInfo = Array();
	$sql  = "SELECT tbl_user.int_id as shop_id, a.int_user, a.int_qty, tbl_user.txt_name, tbl_user.chr_report_name, tbl_user.chr_ename, a.chr_no FROM ";
	$sql .= "(SELECT o.int_user, o.int_qty, i.chr_no FROM tbl_order_z_dept o LEFT JOIN tbl_order_z_menu i ";
	$sql .= 		"ON o.int_product = i.int_id WHERE i.chr_no in ($code) AND DATE(DATE_ADD(o.order_date, INTERVAL 1+o.chr_phase DAY)) = '$deli_date' ";
	$sql .= "AND (o.status IN (0,1,5,98,99,3))";
		
	
	
	$sql .= "GROUP BY o.int_user, i.chr_no) a ";
	$sql .= "LEFT JOIN tbl_user ON a.int_user = tbl_user.int_id ";
	$sql .= "ORDER BY tbl_user.chr_ename ";

	
//	var_dump($sql);
	$result = mysqli_query($con, $sql) or die ($sql);
	while($record = mysqli_fetch_assoc($result)){
		$aryInfo[] = $record;
	}
	$product = Array();
	$total = Array();
	foreach($aryInfo as $value){
		if($value[int_qty] != 0){
			$product[$value[txt_name]]["shop_id"] = $value[shop_id];
			$product[$value[txt_name]]["ename"] = $value[chr_ename];
			$product[$value[txt_name]][$value[chr_no]] = $value[int_qty] + 0;
			
			
			if($value[chr_report_name]){
				$product[$value[shop_id]]["txt_name"] = $value[chr_report_name];
			}else{
				$product[$value[shop_id]]["txt_name"] = $value[chr_ename] . "<br>" . $value[txt_name];
			}
			$product[$value[shop_id]][$value[chr_no]] = $value[int_qty] + 0;
			
			if(in_array($value[shop_id], $aryDisplayShop ))
				$total[$value[chr_no]] += $value[int_qty] + 0;
			
		}
	}
	//print_r($product);
	$Page = Array();
	$pageID = 1;
	foreach ($aryDisplayShop as $key => $value) {
		if ($product[$value][txt_name] != "") {
		//for($i=0;$i<=3;$i++){
			$Page[$pageID][$key+$i*10] = $value;
			if(count($Page[$pageID]) == 10){
				$pageID++;
			}
		//}
		}
	}
	//echo $report[int_hide];
	//die();
	//echo "<PRE>";
	//print_r($Page);
	//print(count($product));
	//echo "</PRE>";
	//die();
	
?>

<html>
<head>
	<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
	<title>內聯網</title>
	<link href="js/My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
	<script src="js/jquery-1.9.1.min.js"></script>
	<script src="js/My97DatePicker/WdatePicker.js"></script>
	<script src="js/parser.js"></script>
	<style>	
		<!--
		.style1 {font-size: 34px}
		.style3 {font-size: 16px;}
		.style6 {font-size: 22px; font-weight: bold; }
		
		-->
	</style>
</head>
<body>
	<form action="#" method="post" name="frmcheck" >
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td valign="middle">
		  落單日期 
		  <input type="text" name="checkDate" class="form-control" value="<?=$showDate?>" id="datepicker" onclick="WdatePicker({maxDate:'<?=$today?>',isShowClear:false})" style="width:125px" readonly>

		  <input type="submit" name="Submit" value="翻查" />
		</td>
	  </tr>
	  <tr>
		<td align="center"></td>
	  </tr>
	</table>
	</form>
	<br/>
	<br/>
	
	<?php if(count($product) >= 1) { ?>
	<span class="style1"><?=$report[chr_report_name]?> <?=$add?></span>
	<span class="style1" style="margin-left:400px;">出貨日期：<?=date("j/n/Y (D)",mktime(0,0,0,$checkDate[1],$checkDate[2]+$report[int_num_of_day],$checkDate[0]));?></span>
	<hr/>
	<!--
	<table width="90%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
		<td align="right">列印日期:<br><?=date('j/n/Y g:i a',$timestamp);?></td>
	  </tr>
	</table>
	
	<table width="88%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
		<?php if($report[int_separate] == 1){ ?>
		<?php $add = ($addOrder==0 ) ? "(4點前)" : "(加單)"?>
		<?php } ?>
		<td colspan="3" align="center"><span class="style1"><u><?=$report[chr_report_name]?> <?=$add?></u></span></td>
	  </tr>
	  <tr>
		<td width="200" align="left" valign="bottom">落單日期：<?=date("j/n/Y D",mktime(0,0,0,$checkDate[1],$checkDate[2],$checkDate[0]));?></td>
		<td align="center" valign="bottom" class="style1">出貨日期：<?=date("j/n/Y D",mktime(0,0,0,$checkDate[1],$checkDate[2]+$report[int_num_of_day],$checkDate[0]));?></td>
		<td width="200" align="right" valign="bottom">&nbsp;</td>
	  </tr>
	</table>
	-->
	<?php 
	if($report[int_main_item]) { 
		include("CMS_order_c_check_m_item.php");
	}else{
		include("CMS_order_c_check_m_shop.php");
	}
	?>
	
	<?php } else { ?>
		<center><font size="5" color="red"><br>柯打沒有內容 !!<br>請選擇日期</font></center>
	<?php } ?>
</body>
</html>