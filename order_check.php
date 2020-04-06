<?php
  require("connect.inc");
  session_start();     
  if (!($_SESSION[authenticated])) {
	$_SESSION['status'] = 'neverLogin';
	$_SESSION[UrlRedirect] = 'order.php';
	header('Location: login.php');
  }
  
  
  $timestamp = gettimeofday("sec");
  $today = Date("Y-m-d", $timestamp);  
  
  if(isset($_GET[cat]) && $_GET[cat] != -1 ){
	  if(isset($_GET[supplier]) && $_GET[supplier] != -1){
		$sql = "SELECT int_id FROM tbl_order_z_group WHERE int_cat = $_GET[cat] AND int_id = $_GET[supplier]";
		if (mysqli_num_rows(mysqli_query($con, $sql)) <= 0) $_GET[supplier] = -1;
	  }
  }
  $search_date = isset($_GET[date])?$_GET[date]:$today;
  
  
  $sql = "SELECT * FROM tbl_order_z_group ";
  $result = mysqli_query($con, $sql) or die($sql);
  $arySup = Array();
  while($record = mysqli_fetch_assoc($result)){ 
	$arySup[] = $record;
  }
  
  
  if ($_GET[action] == "search"){
	  $sql = 
<<<EOT
	SELECT 
		DATE(T0.order_date) AS order_date,
		TIME(T0.order_date) AS order_time,
		DATE_ADD(DATE(T0.order_date), INTERVAL (T0.chr_phase + 1) DAY) AS dev_date,
		REPLACE(int_qty, '.00', '') AS int_qty,
		T2.chr_name AS unit,
		chr_po_no,
		T1.chr_name AS item_name,
		T5.txt_name,
		T3.chr_name_long,
		T6.chr_code,
		T1.chr_no,
		T3.int_id as group_id,
		T4.chr_name as cat_name,
		T1.int_id as itemID
	FROM
		tbl_order_z_dept T0
			LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
			LEFT JOIN tbl_order_z_unit T2 ON T1.int_unit = T2.int_id
			LEFT JOIN tbl_order_z_group T3 ON T1.int_group = T3.int_id
			LEFT JOIN tbl_order_z_cat T4 ON T3.int_cat = T4.int_id
			LEFT JOIN tbl_user T5 ON T0.int_user = T5.int_id
			LEFT JOIN tbl_district T6 ON T5.int_district = T6.int_id
	WHERE T0.status IN (1,98,99)
EOT;
	  $sql .= "AND order_date >= '$search_date 00:00' ";
	  $sql .= "AND order_date <= '$search_date 23:59' ";
	  //$sql .= "AND T5.int_dept = 2 ";
	  
	  if(isset($_GET[shop]) && $_GET[shop] != -1){
		  $sql .= "AND int_user = $_GET[shop] ";
	  }
	  
	  
	  if(isset($_GET[cat]) && $_GET[cat] != -1 ){
		  if(isset($_GET[supplier]) && $_GET[supplier] != -1)
			  $sql .= "AND int_group = $_GET[supplier] ";
		  else
			  $sql .= "AND T3.int_cat = $_GET[cat] ";
	  }else{
		  if(isset($_GET[supplier]) && $_GET[supplier] != -1)
			  $sql .= "AND int_group = $_GET[supplier] ";
	  }
	  $sql .= "ORDER BY RIGHT(T6.chr_code, 3)";
	  
	  //die($sql);
	  $result = mysqli_query($con, $sql);
	  $aryList = Array();
	  while($record = mysqli_fetch_assoc($result)){
		  $item = Array();
		  
		  $item[name] = $record[item_name];
		  $item[qty] = $record[int_qty];
		  $item[unit] = $record[unit];
		  $item[price] = $record[chr_price];
		  $item[chr_no] = $record[chr_no];
		  $item[detail] = $record[txt_datil_3];
		  
		  $shop_key = "#" . $record[chr_code] . " " . $record[txt_name];
		  $supp_key = $record[group_id];
		  $po_key = $record[chr_po_no];
		  
		  $aryList[$shop_key][$supp_key]["long_name"] = $record[cat_name] . " - " . $record[chr_name_long];
		  $aryList[$shop_key][$supp_key]["contact"] = $record[chr_attn];
		  $aryList[$shop_key][$supp_key]["tel"] = $record[chr_tel];
		  $aryList[$shop_key][$supp_key]["fax"] = $record[chr_fax];
		  
		  $aryList[$shop_key][$supp_key]["po_list"][$po_key]["order_time"] = $record[order_time];
		  $aryList[$shop_key][$supp_key]["po_list"][$po_key]["dev_date"] = $record[dev_date];
		  if(isset($aryList[$shop_key][$supp_key]["po_list"][$po_key]["item"][$record[itemID]])){
			  $aryList[$shop_key][$supp_key]["po_list"][$po_key]["item"][$record[itemID]][qty] += $item[qty];
		  }else{
			  $aryList[$shop_key][$supp_key]["po_list"][$po_key]["item"][$record[itemID]] = $item;
		  }
	  }
  }
  //echo "<PRE>";
  //print_r($aryList);
  //echo "</PRE>";
  
  

  $sql = "SELECT * FROM tbl_order_z_group ";
  if(isset($_GET[cat]) &&  $_GET[cat] != "-1"){
	  $sql = "SELECT * FROM tbl_order_z_group WHERE int_cat = $_GET[cat]";
  }
  $supplierHTML = '
  <select style="width:125px" id="supplier">
	<option value="-1" >全部</option>';
	$result = mysqli_query($con, $sql) or die($sql);
	while($record = mysqli_fetch_assoc($result)){
		$selected = ($record[int_id] == $_GET[supplier]) ? "selected" : "";
		$supplierHTML .= '<option value="' . $record[int_id].'" '. $selected .'>';
		$supplierHTML .= $record[chr_name];
		$supplierHTML .= '</option>';
	} 
  $supplierHTML .= '</select>';
  
?>
<html>
<head>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>內聯網</title>
<style type="text/css">
<!--
	.cssMenu { list-style-type: none; padding: 0; overflow: hidden; background-color: #ECECEC; float:right;}
	.cssMenuItem { float: right;  width:140px; border-right: 2px solid white; }
	.cssMenuItem a { display: block; color: black; text-align: center; padding: 4px; text-decoration: none; }
	.cssMenuItem a:hover { background-color: #BBBBBB; color:white; }
	
	.cssImportant{ background-color: #CCFFFF }
	
	div { margin-top:15px; }
	.cssTable1 { border-collapse: collapse; margin-bottom:15px;}
	.cssTable1 { border: 2px solid black;}
	.cssTable1 th{  padding:0px; text-align:center; border: 2px solid black; padding:5px 5px;}
	.cssTable1 td{  padding:0px; text-align:center; border: 2px solid black; padding:5px 5px; }
	
	.cssInfo { word-break: break-all; }
	
	.style1 { font-size:16px; font-weight:bold;}
	.style2 { width:50px; }
	.style3 { width:150px; }
	.style4 { width:200px; }
	.style5 { width:250px; }
	.style6 { background-color: cff;}
	.style7 { background-color: ffc;}
	.style8 { background-color: #dbdbdb;}
-->
</style>
<link href="js/My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/My97DatePicker/WdatePicker.js"></script>
<script>
var setInputListener = function(id, callback) {
	var obj = document.getElementById(id);
	var initValue = obj.value;
	
	function checkValue() {
		if(obj.value != initValue){
			initValue = obj.value;
			callback(obj);
		}
	}
	checkValue();
	var timeinterval = setInterval(checkValue, 200);
}
var arySup = [];

function search(){
	$("#submitBtn")[0].disabled = true;
	$("#submitBtn")[0].innerHTML = "請稍侯";
	
	var cat = $("#cat").val();
	var sup = $("#supplier").val();
	<?php if($_SESSION[dept] != 2) { ?>
	var shop = $("#shop").val();
	<?php }else{ ?>
	var shop = <?=$_SESSION[user_id]?>;
	<?php } ?>
	var date = $("#datepicker").val();
	location.href = 
		"?cat=" + cat + 
		"&supplier=" + sup + 
		"&shop=" + shop + 
		"&date=" + date + 
		"&action=search";
		
	
}
function filterSupplier(){
	var cat = $("#cat").val();
	var sup = $("#supplier").val();
	<?php if($_SESSION[dept] != 2) { ?>
	var shop = $("#shop").val();
	<?php }else{ ?>
	var shop = <?=$_SESSION[user_id]?>;
	<?php } ?>
	var date = $("#datepicker").val();
	location.href = 
		"?cat=" + cat + 
		"&supplier=" + sup + 
		"&shop=" + shop + 
		"&date=" + date + 
		"&action=filter";
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
<div align="center" style="width:995px;">
<h1>翻查過去柯打</h1>
<div align="center" style="width:100%;margin:auto;">
	<b>落單日期： </b>
	<input type="text" class="form-control" value="<?=$search_date?>" id="datepicker" onclick="WdatePicker({maxDate:'<?=$today?>',isShowClear:false})" style="width:125px" readonly>
	
	<?php if($_SESSION[dept] != 2) { ?>
	<span style="margin-left:15px"></span>
	<b>分店：</b>
	<select style="width:125px" id="shop">
		<option value="-1" <?php if($_GET[shop] == -1) echo "selected" ?>>全部</option>
		<?php
		$sql = "SELECT T0.int_id, T1.chr_code, T0.txt_name
		FROM tbl_user T0
		LEFT JOIN tbl_district T1 ON T0.int_district = T1.int_id
		ORDER BY RIGHT(T1.chr_code, 3);";
		$result = mysqli_query($con, $sql) or die($sql);
		while($record = mysqli_fetch_row($result)){ ?>
		<option value="<?=$record[0]?>" <?php if($_GET[shop] == $record[0]) echo "selected" ?>><?=$record[1]?> - <?=$record[2]?></option>
		<?php } ?>
	</select>
	<?php } ?>
	<span style="margin-left:15px"></span>
	<b>供應商類別：</b>
	<select style="width:125px" id="cat" onchange="filterSupplier()">
		<option value="-1" <?php if($_GET[cat] == -1) echo "selected" ?> >全部</option>
	<?php
	$sql = "SELECT * FROM tbl_order_z_cat;";
	$result = mysqli_query($con, $sql) or die($sql);
	while($record = mysqli_fetch_assoc($result)){ ?>
		<option value="<?=$record[int_id]?>" <?php if($_GET[cat] == $record[int_id]) echo "selected" ?>>
			<?=$record[chr_name]?>
		</option>
	<?php } ?>
	</select>
	<span style="margin-left:15px"></span>
	
	<b>供應商：</b>
	<?=$supplierHTML?>
	<span style="margin-left:15px"></span>
	
	<button onclick="search()" id="submitBtn" style="width:75px;">提交</button>
	<?php if (isset($_GET[date])){ ?>
		<h2 >落單日期: <?=$_GET[date]?></h2>
	<?php } ?>
	<?php if (isset($aryList)){ ?>
	
	<?php if(count($aryList) > 0) { ?>
	<?php foreach($aryList as $shop => $supp) {?>
	<div align="left" style="width:900px; margin-top:0px;"> 
		<h2><?=$shop?></h2>
		<?php foreach($supp as $sap => $supp_detail) {  ?>
		<span class="style1"><?=$supp_detail[long_name]?></span>
		<?php foreach ($supp_detail[po_list] as $po => $po_detail) { ?>
			<table class="cssTable1">
				<tr class="style7">
				<th colspan="7">
					<span style="float:left; font-weight:bold; color:red; font-size:18px;">送貨日期: <?=$po_detail[dev_date]?></span>
					PO#<?=Date("y", $timestamp)?>A<?=str_pad($po, 6, '0', STR_PAD_LEFT)?> - <?=$po_detail[order_time]?>
				</th>
				</tr>
				<tr class="style6"><th>項目</th><th>貨品</th><th>數量</th><th>編號</th><th>主要規格</th></tr>
				<?php foreach ($po_detail[item] as $count => $item_detail) { ?>
				<?php $bg = ($count % 2 ==0)?"#FFFFFF":"#DBDBDB";?>
					<tr bgcolor="<?=$bg?>">
						<td class="style2"><?=$count+1?></td>
						<td class="style4"><?=$item_detail[name]?></td>
						<td class="style3"><?=$item_detail[qty]?> <?=$item_detail[unit]?></td>
						<td class="style3"><?=$item_detail[chr_no]?></td>
						<td class="style5"><?=$item_detail[detail]?></td>
					</tr>
				<?php } ?>
			</table>
		<?php } ?>
		<br>
		<?php } ?>
	</div>
	<hr style="width:900px">
	<?php } ?>
	<?php }else{ ?>
	<div align="left" style="width:900px; margin-top:0px;"> 
		<h2 style="color:red">沒有任何記錄</h2>
	</div>
	<?php } ?>
	<?php }else{ ?>
	<div align="left" style="width:900px; margin-top:0px;"> 
		<h2>請先輸入資料</h2>
	</div>
	<?php } ?>
</div>

</div>
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