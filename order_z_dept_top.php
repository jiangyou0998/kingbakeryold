<?php
     session_start();     
     if (!($_SESSION[authenticated])) {
       $_SESSION['status'] = 'neverLogin';
       $_SESSION[UrlRedirect] = 'order_z_dept.php';
       header('Location: login.php');
     }
	 $timestamp = gettimeofday("sec");
	 require($DOCUMENT_ROOT . "connect.inc");
	 
	/*
	$searchShopCode = '';
	$_SESSION['ShopCode'] = '';
	if(!empty($_REQUEST['data'])){
		$strData = str_replace("\\","",$_REQUEST['data']);
		//echo $strData;
		$data  = json_decode(iconv('big5','utf-8',$strData),true);
		foreach($data as $item){
			foreach($item['item'] as $shop){
				$searchShopCode.= $shop['code'].',';
			}
		}
		if(!empty($searchShopCode)){
			$searchShopCode=rtrim($searchShopCode,',');
			$_SESSION['ShopCode'] = $searchShopCode;
			//echo $searchShopCode;
		}
	}
	
	// if($_SESSION[user_id] == 147) {
		// $_SESSION['ShopCode'] = $_SESSION['user_id'];
	// } elseif($_SESSION[user_id] == 148) {
		// $_SESSION['ShopCode'] = $_SESSION['user_id'];
	// } elseif($_SESSION[user_id] == 175) {
		// $_SESSION['ShopCode'] = $_SESSION['user_id'];
	// }elseif ($_SESSION['type'] == 2){
		// $_SESSION['ShopCode'] = $_SESSION['user_id'];
	// }
	
	if($_SESSION[user_id] == 147
	||$_SESSION[user_id] == 148
	||$_SESSION[user_id] == 175
	||$_SESSION['type'] == 2){
		
		$sql = "select * from tbl_order_shop where int_uid = ".$_SESSION['user_id'];
		$result = mysql_query($sql) or die($sql);
		while($record = mysql_fetch_array($result)){
			$_SESSION['ShopCode'] = $record['int_id'];
		}
		//$_SESSION['ShopCode'] = $_SESSION['user_id'];
	}
//	echo $_SESSION['ShopCode'];
*/
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
body {
	margin-bottom: 0px;
}
-->
ul.checked li {
	color:#000;
	text-align:left;
}

a {
    text-decoration:none
}

</style>
<script>
function openwindow(aa){
  window.open("show_hide.php?showhide="+aa,"showhide","height=1,width=1,resizable=no,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no, status=no");
}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>
</head>

<body bgcolor="#697caf" text="#FFFFFF" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
<table width="100%" height="20px" border="0" cellpadding="0" cellspacing="0">
<!--
	<tr>
		<td colspan="11">
			<form name="select" id="" method="post">
	
			<?php 
				if($_SESSION['type']<>'2'
				&&$_SESSION['user_id'] <> 147
				&&$_SESSION['user_id'] <> 148
				&&$_SESSION['user_id'] <> 175)
				{
					echo ' <div id="select"></div>';
				}
			?>
		 
			<input type="hidden" id="data" name="data" value='<?php echo $strData;?>'/>
		  
		  </form>
		</td>
	</tr>
-->  
</table>
<table border="0" cellpadding="0" cellspacing="2">
  <tr>
<?php
  require($DOCUMENT_ROOT . "connect.inc");
/*
  $sql_check_holiday = "SELECT * FROM tbl_isholiday ";
  $result_check_holiday = mysql_query($sql_check_holiday) or die($sql_check_holiday);
  $record_check_holiday = mysql_fetch_array($result_check_holiday);
  $_SESSION['isHoliday'] = $record_check_holiday[1];
*/
  $sql  = "
    SELECT int_id, chr_name, int_sort, status
	FROM tbl_order_z_cat
	WHERE status <> 4
		AND status <> 5
	ORDER BY int_sort;";
  $result = mysqli_query($con, $sql) or die($sql);
  $count = 1;
  WHILE($record = mysqli_fetch_array($result)) {
	$bgimage = "images/9.jpg";
  
    IF ($count <= 6) {
?>
    <td width="106" height="38" align="center" background="<?=$bgimage;?>"><a href="order_z_dept_middle.php?catid=<?=rawurlencode($record['int_id']);?>&bl_select=<?=$bl_select?>" target="middleFrame" onClick="parent.mainFrame.location.href='order_z_bottom.php'"><?=$record['chr_name'];?></a></td>
<?php
    } ELSE {
?>
  </tr>
  <tr>
    <td width="106" height="38" align="center" background="<?=$bgimage;?>"><a href="order_z_dept_middle.php?catid=<?=rawurlencode($record['int_id']);?>&bl_select=<?=$bl_select?>" target="middleFrame" onClick="parent.mainFrame.location.href='order_z_bottom.php'"><?=$record['chr_name'];?></a></td>
<?php
      $count = 1;
    }
    $count += 1;
  }
?>
  </tr>
</table>

<br/>
<iframe src="order_z_dept_left_bottom.php" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" style="visibility:inherit; width:100%;z-index:1;height:88%;">
</iframe>

 

<!--
<frameset rows="30%,*" cols="*" frameborder="no" border="1" >
    
    <frame src="order_z_dept_bottom.php" name="mainFrame" id="mainFrame" title="mainFrame" />
</frameset>
<noframes></noframes>
-->
</body>
</html>
