<?php
//  session_start();
//  if (!($_SESSION[authenticated])) {
//    $_SESSION['status'] = 'neverLogin';
//    $_SESSION[UrlRedirect] = 'dept.php';
//    header('Location: login.php');
//  }

//檢查是否登錄,是否管理員
require ("check_login.php");

  require($DOCUMENT_ROOT . "connect.inc");
  $timestamp = gettimeofday("sec");
?>
<html>
<head>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta charset="utf-8"/>
<script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="js/json2.js" type="text/javascript"></script>
<script src="js/select.js" type="text/javascript"></script>
<script type="text/javascript" src="js/MultipleSelect/multiple-select.js"></script>
<script src="js/My97DatePicker/WdatePicker.js"></script>

<link href="css/select.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="js/MultipleSelect/multiple-select.css"/>
<link rel="stylesheet" type="text/css" href="css/checkbox-style.css"/>
<link rel="stylesheet" type="text/css" href="css/MultipleSelectList-checkbox.css"/>

<title>內聯網</title>
<script type="text/javascript">
$(function(){
	$('#select').Select();
});
</script>
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
.msg { color:blue; font-weight:bold; display:none; }
.tr-shop, .tr-user { display:none; }
-->
</style>

<script type="text/javascript">
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
function timePick(){
	WdatePicker({ dateFmt:'HH:mm' });
}
function changeType(event){
	if(event.target.value == "2"){
		//$(".shopField").show();
	}else{
		//$(".shopField").hide();
	}
}
$(function(){
	$("#dept").multipleSelect({
		allSelected: false,
		ellipsis: true,
		selectAll: false,
		countSelected: '已選擇 # 項',
		minimumCountSelected: 10,
		multiple: true,
		multipleWidth: 220,
		onClose: function(){
			$("#deptVal").val($("#dept").multipleSelect('getSelects').join(','));
		}
	});
});
</script>

</head>
<body>
<?php
  switch ($_REQUEST[action]) {
    case "new":
?>
<form name="search" action="" method="post">
<input type="hidden" name="action" value="add">
<table width="90%" border="0" cellspacing="1" cellpadding="6">
  <tr>
    <td bgcolor="#895F9F"><span class="style1">設定 &gt; 使用者 &gt; 新增</span></td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">帳戶類型　
        <select name="type" class="type" onchange="changeType(event);">
			<option value="0" <?php if ($record[chr_type] == 0) echo "selected"; ?>>--請選擇帳戶類型--</option>
			<option value="2" <?php if ($record[chr_type] == 2) echo "selected"; ?>>分店</option>
			<option value="1" <?php if ($record[chr_type] == 1) echo "selected"; ?>>一般使用者</option>
			<option value="3" <?php if ($record[chr_type] == 3) echo "selected"; ?>>系統管理員</option>
		</select>
		<span class="msg" id="msg1" <?php if ($record[chr_type] == 1) echo "style='display:inline;'"; ?>>　※ 只能瀏覽資料，不能落單</span>
		<span class="msg" id="msg2" <?php if ($record[chr_type] == 2) echo "style='display:inline;'"; ?>>　※ 能夠瀏覽資料以及提交柯打</span>
		<span class="msg" id="msg3" <?php if ($record[chr_type] == 3) echo "style='display:inline;'"; ?>>　※ 能夠修改內聯網資料，瀏覽資料</span>
	</td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">帳戶名稱　
        <input name="name" type="text" id="name" value="<?php echo $record[txt_name];?>" size="50"></td>
  </tr>  
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
    <td bgcolor="#EEEEEE">報表名稱　
        <input name="reportName" type="text" id="reportName" value="<?php echo $record[chr_report_name];?>" size="50"></td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">登入名稱　
        <input name="login" type="text" id="login" value="<?php echo $record[txt_login];?>" size="50"></td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">電子郵件　
        <input name="email" type="text" id="name" value="<?php echo $record[chr_email];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">分店編號　
        <input name="code" type="text" id="name" value="<?php echo $record[chr_ename];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">分店區域　
		<select name="area">
			<option value="0">--請選擇分店區域--</option>
			<?php 
			$sql = "SELECT * FROM tbl_order_z_area;";
			$aryArea = mysqli_query($con, $sql);
			while($a = mysqli_fetch_assoc($aryArea)){ ?>
			<option value="<?=$a[int_id]?>"><?=$a[chr_name]?></option>
			<?php  } ?>
		</select>
	</td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">電話　　　
        <input name="tel" type="text" id="name" value="<?php echo $record[chr_ename];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">FAX　　　
        <input name="fax" type="text" id="name" value="<?php echo $record[chr_ename];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">中文地址　
        <input name="addr1" type="text" id="name" value="<?php echo $record[chr_ename];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">英文地址　
        <input name="addr2" type="text" id="name" value="<?php echo $record[chr_ename];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">營業時間　
        <input name="oper_time" type="text" id="name" value="<?php echo $record[chr_oper_time];?>" size="50"></td>
  </tr>
  <tr class="tr-user" <?php if ($record[chr_type] <> 2) echo "style='display:table-row;'"; ?>>
    <td bgcolor="#EEEEEE">部　　門　
        <select id="dept" style="width:500px;" multiple>
			<!--<option value="0">--請選擇部門--</option>-->
			<?php
			$sql = "SELECT * FROM tbl_dept WHERE bl_isvalid='1'; ";
			$dept = mysqli_query($con, $sql);
			while($d = mysqli_fetch_assoc($dept)){?>
			<option value="<?=$d['int_id']?>" <?php if ($d['int_id'] == $record['int_dept']) echo 'selected'; ?>><?=$d['txt_dept']?></option>	
			<?php } ?>
		</select>
		<input name="dept" id="deptVal" value="<?=$record['int_dept']?>" type="hidden" />
	</td>
  </tr>
	
  <tr>
    <td bgcolor="#EEEEEE">　　　　　
        <input type="submit" name="Submit2" value="確認">
      <input name="back" type="button" id="back" value="返回" onClick="history.go(-1);"></td>
  </tr>
</table>
</form>
<?php
    break;
    case "edit":
		$sql = "SELECT * FROM tbl_user WHERE int_id = $_REQUEST[id]";
      $result = mysqli_query($con, $sql) or die($sql);
      $record = mysqli_fetch_array($result);
	  //print_r($sql);
?>
<form name="search" action="" method="post">
<input type="hidden" name="action" value="confirm">
<input type="hidden" name="id" value="<?php echo $_REQUEST[id];?>">
<table width="90%" border="0" cellspacing="1" cellpadding="6">
  <tr>
    <td bgcolor="#895F9F"><span class="style1">設定 &gt; 使用者 &gt; 修改</span></td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">帳戶類型　
        <select name="type" class="type" onchange="changeType(event);">
			<option value="0" <?php if ($record[chr_type] == 0) echo "selected"; ?>>--請選擇帳戶類型--</option>
			<option value="2" <?php if ($record[chr_type] == 2) echo "selected"; ?>>分店</option>
			<option value="1" <?php if ($record[chr_type] == 1) echo "selected"; ?>>一般使用者</option>
			<option value="3" <?php if ($record[chr_type] == 3) echo "selected"; ?>>系統管理員</option>
		</select>
		<span class="msg" id="msg1" <?php if ($record[chr_type] == 1) echo "style='display:inline;'"; ?>>　※ 只能瀏覽資料，不能落單</span>
		<span class="msg" id="msg2" <?php if ($record[chr_type] == 2) echo "style='display:inline;'"; ?>>　※ 能夠瀏覽資料以及提交柯打</span>
		<span class="msg" id="msg3" <?php if ($record[chr_type] == 3) echo "style='display:inline;'"; ?>>　※ 能夠修改內聯網資料，瀏覽資料</span>
	</td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">帳戶名稱　
        <input name="name" type="text" id="name" value="<?php echo $record[txt_name];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
    <td bgcolor="#EEEEEE">報表名稱　
        <input name="reportName" type="text" id="reportName" value="<?php echo $record[chr_report_name];?>" size="50"></td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">登入名稱　
        <input name="login" type="text" id="login" value="<?php echo $record[txt_login];?>" size="50"></td>
  </tr>  
  <tr>
    <td bgcolor="#EEEEEE">登入密碼　
		<button type="button" onclick="location='CMS_user2.php?id=<?=$record[int_id]?>&action=pwd';">重設密碼</button>
	</td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">電子郵件　
        <input name="email" type="text" id="name" value="<?php echo $record[chr_email];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">分店編號　
        <input name="code" type="text" id="name" value="<?php echo $record[chr_ename];?>" size="50"></td>
  </tr>
  
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
  <?php
  $sql = "SELECT T1.int_id 
  FROM tbl_district T0
	LEFT JOIN tbl_order_z_area T1 ON T0.int_area = T1.int_id
  WHERE T0.int_id = $record[int_district]";
  $result = mysqli_query($con, $sql);
  $area = mysqli_fetch_assoc($result);
  ?>
  
	<td bgcolor="#EEEEEE">分店區域　
		<select name="area">
			<?php 
			$sql = "SELECT * FROM tbl_order_z_area;";
			$aryArea = mysqli_query($con, $sql);
			while($a = mysqli_fetch_assoc($aryArea)){ ?>
			<option value="<?=$a[int_id]?>" <?php if ($a[int_id] == $area[int_id]) echo "selected"; ?>><?=$a[chr_name]?></option>
			<?php  } ?>
		</select>
	</td>
        <!-- <input name="area" type="text" id="area" value="<?php echo $record[chr_ename];?>" size="50"></td> -->
  </tr>
  <?
  $sql = "SELECT * FROM db_intranet.tbl_district WHERE int_id = (SELECT int_district FROM db_intranet.tbl_user WHERE int_id = '$_GET[id]');";
  //die($sql);
  $d_result = mysqli_query($con, $sql) or die($sql);
  $dis = mysqli_fetch_assoc($d_result);

  $sql = "SELECT chr_pocode FROM db_intranet.tbl_user WHERE int_id = '$_GET[id]';";
//  die($sql);
  $u_result = mysqli_query($con, $sql) or die($sql);
  $user = mysqli_fetch_assoc($u_result);

  ?>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">電話　　　
        <input name="tel" type="text" id="name" value="<?php echo $dis[chr_tel];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">FAX　　　
        <input name="fax" type="text" id="name" value="<?php echo $dis[chr_fax];?>" size="50"></td>
  </tr>
    <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
        <td bgcolor="#EEEEEE">PO編號　　
            <input name="pocode" type="text" id="name" value="<?php echo $user[chr_pocode];?>" size="50"></td>
    </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">中文地址　
        <input name="addr1" type="text" id="name" value="<?php echo $dis[chr_address];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">英文地址　
        <input name="addr2" type="text" id="name" value="<?php echo $dis[chr_eng_address];?>" size="50"></td>
  </tr>
  <tr class="tr-shop" <?php if ($record[chr_type] == 2) echo "style='display:table-row;'"; ?>>
	<td bgcolor="#EEEEEE">營業時間　
        <input name="oper_time" type="text" id="name" value="<?php echo $dis[chr_oper_time];?>" size="50"></td>
  </tr>
  <tr class="tr-user" <?php if ($record[chr_type] <> 2) echo "style='display:table-row;'"; ?>>
    <td bgcolor="#EEEEEE">部　　門　
        <select name="dept" id="dept" style="width:500px;" multiple>
			<!--<option value="0">--請選擇部門--</option>-->
			<?php
			$sql = "SELECT * FROM tbl_dept WHERE bl_isvalid='1'; ";
			$dept = mysqli_query($con, $sql);
			while($d = mysqli_fetch_assoc($dept)){?>
			<option value="<?=$d['int_id']?>" <?php if (in_array($d['int_id'], explode(',',$record['int_dept']))) echo 'selected'; ?>><?=$d['txt_dept']?></option>	
			<?php } ?>
		</select>
		<input name="dept" id="deptVal" value="<?=$record['int_dept']?>" type="hidden" />
	</td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">　　　　　
        <input type="submit" name="Submit2" value="確認">
      <input name="back" type="button" id="back" value="返回" onClick="history.go(-1);"></td>
  </tr>
</table>
</form>
<?php
    break;
    case "delete":
        //chr_type = 0,賬號禁用
      $sql = "UPDATE tbl_user SET chr_type = '0' ";
      $sql .= "WHERE int_id = '$_REQUEST[id]' ";
      mysqli_query($con, $sql) or die($sql);
    break;
    case "add":
	  if($_REQUEST['type'] == 2){
		$sql = "INSERT INTO tbl_district (chr_name, int_area, chr_code, chr_tel, chr_fax, chr_address, chr_eng_address, chr_oper_time , chr_pocode) VALUE ('$_REQUEST[name]', '$_REQUEST[area]', '$_REQUEST[code]', '$_REQUEST[tel]', '$_REQUEST[fax]', '$_REQUEST[addr1]', '$_REQUEST[addr2]', '$_REQUEST[oper_time]', '$_REQUEST[pocode]')";
		mysqli_query($con, $sql) or die($sql);
	  }
	  if($_REQUEST['type'] == 2){
		  $dept = 2;
		  $code = $_REQUEST['code'];
		  $district = mysqli_insert_id($con);
	  }else{
		  $dept = $_REQUEST['dept'];
		  $code = "NULL";
		  $district = "NULL";
	  }
	  $sql  = "INSERT INTO tbl_user(chr_type, txt_name, txt_login, chr_email, int_dept, chr_ename, txt_password, int_district) ";
	  $sql .= "VALUE('$_REQUEST[type]','$_REQUEST[name]', '$_REQUEST[login]', '$_REQUEST[email]', '$dept', '$code', '123456', $district);";
      mysqli_query($con, $sql) or die($sql);
	  
	  if($_REQUEST['type'] == 2){
		  $shopID = mysqli_insert_id($con);
		  $sql = "INSERT INTO tbl_order_z_shop (int_sort, int_area_id, int_user_id) VALUE (999, $_REQUEST[area], $shopID);";
		  mysqli_query($con, $sql) or die($sql);
		  
		  $sql = "INSERT INTO tbl_order_z_group_v_shop(int_user_id, int_group_id) SELECT $shopID, int_id FROM tbl_order_z_group;";
		  mysqli_query($con, $sql) or die($sql);
		  
		  $sql = "INSERT INTO tbl_order_z_menu_v_shop(int_user_id, int_menu_id) SELECT $shopID, int_id FROM tbl_order_z_menu;";
		  mysqli_query($con, $sql) or die($sql);
	  }
	  
      break;
    case "confirm":
	  $sql = "UPDATE tbl_district T0
	  LEFT JOIN tbl_user T1 ON T0.int_id = T1.int_district
	  SET T0.int_area = '$_REQUEST[area]', T0.chr_code = '$_REQUEST[code]', T0.chr_tel = '$_REQUEST[tel]', T0.chr_fax = '$_REQUEST[fax]', T0.chr_address = '$_REQUEST[addr1]', T0.chr_eng_address = '$_REQUEST[addr2]', T0.chr_oper_time = '$_REQUEST[oper_time]', T0.chr_name = '$_REQUEST[name]' 
	  WHERE T1.int_id = $_REQUEST[id];";
	  mysqli_query($con, $sql) or die($sql);
		//die($sql);
	  $sql  = "UPDATE tbl_user SET chr_type = '$_REQUEST[type]'";
	  $sql .= ",txt_name = '$_REQUEST[name]' ";
	  $sql .= ",txt_login = '$_REQUEST[login]' ";
	  $sql .= ",chr_email = '$_REQUEST[email]' ";
	  if($_REQUEST[type] == 2){
		  $sql .= ", int_dept = 2 ";
		  $sql .= ", chr_ename = '$_REQUEST[code]' ";
		  $sql .= ", chr_report_name = '$_REQUEST[reportName]' ";
		  $sql .= ", chr_pocode = '$_REQUEST[pocode]' ";
	  }else{
		  $sql .= ", int_dept = '$_REQUEST[dept]' ";
	  }
	  $sql .= "WHERE int_id = $_REQUEST[id] ";
	  //die($sql);
      mysqli_query($con, $sql) or die($sql);
	  
	  $sql = "DELETE FROM tbl_dept_v_user WHERE int_user_id = $_REQUEST[id] ";
	  mysqli_query($con, $sql) or die($sql);
	  
	  $dept = explode(',', $_REQUEST[dept]);
	  foreach($dept as $deptID){
		$sql = "INSERT INTO tbl_dept_v_user(int_user_id, int_dept_id) value($_REQUEST[id], $deptID);";
		mysqli_query($con, $sql) or die($sql);
	  }
	  
	  if($_REQUEST[type] == 2){
		$sql = "DELETE FROM tbl_order_z_shop WHERE int_user_id = $_REQUEST[id]; ";
		mysqli_query($con, $sql) or die($sql);
		
		$sql = "INSERT INTO tbl_order_z_shop (int_sort, int_area_id, int_user_id) VALUE (999, $_REQUEST[area], $_REQUEST[id]);";
		mysqli_query($con, $sql) or die($sql);
	  }
      break;
	case "pwd":
	  $sql = "UPDATE tbl_user SET txt_password = '123456' WHERE int_id = '$_REQUEST[id]';";
	  mysqli_query($con, $sql) or die($sql);
	  
	  echo "<script>alert('密碼已重設為123456，請提醒使用者自行修改密碼。');</script>";
	  break;
}
  IF (($_REQUEST[action] <> "new") AND ($_REQUEST[action] <> "edit")) {
?>
  <table width="90%" border="0" cellspacing="1" cellpadding="6">
    <tr>
      <td bgcolor="#895F9F"><span class="style1">設定 &gt; 部門 &gt; 完成</span></td>
    </tr>
    <tr>
      <td height="100" align="center" bgcolor="#EEEEEE"><a href="CMS_user.php?type=<?=$_REQUEST[type]?>">更新完成</a></td>
    </tr>
  </table>
<?php
  }
      
?>
</body>
<script>
$(".type").change(function(){
	$(".msg").hide();
	$("#msg" + $(this).val()).show();
	if($(this).val() == '2'){
		$(".tr-shop").show();
		$(".tr-user").hide();
	}else{
		$(".tr-shop").hide();
		$(".tr-user").show();
	}
});
</script>
</html>