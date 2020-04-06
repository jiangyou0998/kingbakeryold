<?php 
session_start();     
if (!($_SESSION[authenticated])) {
	$_SESSION['status'] = 'neverLogin';
	header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");
$timestamp = gettimeofday("sec")+28800;
if(isset($_GET['submit'])) {
	$submit = $_GET['submit'];
	if($submit == "del"){
		$id=$_GET['id'];
		$type=$_GET['type'];
		$sql_del="";
		
		switch($type){
			case 1:
				$sql_del="delete from tbl_itsupport_detail where int_id=".$id;
			break;
			case 2:
				$sql_del="delete from tbl_itsupport_item where int_id=".$id;
			break;
		}
		$del_Result = mysqli_query($con, $sql_del) or die($sql_del);
		if($del_Result > 0) {
			
			?><script language="javascript">alert('刪除紀錄成功!'); document.location.href = "CMS_itsupport_manage.php"</script><?php 
		}
	}
	else if($submit == "add"){
		$type_project = $_POST['type_project'];
		switch ($type_project){
			case 1:
				$sql_insert  = " INSERT INTO tbl_itsupport_detail (chr_name,int_sort,int_item_id)VALUES('".$_POST["txt_name"]."',".$_POST["txt_sort"].",".$_POST["sel_project_type"].")";
			break;
			case 0:
				$sql_insert  = " INSERT INTO tbl_itsupport_item (chr_name,int_sort)VALUES('".$_POST["txt_name"]."',".$_POST["txt_sort"].")";
			break;
			
		}
		$insert_Result = mysqli_query($con, $sql_insert) or die($sql_insert);
		
		if($insert_Result > 0) {?><script language="javascript">alert('新增紀錄成功!'); document.location.href = "CMS_itsupport_manage.php"</script><?php  }
	} 
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />

<title>內聯網</title>
	<style type="text/css">
	body,td,th {
	font-size:small;
}
    </style>
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/wbox-min.js"></script>
<link href="wbox/wbox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
 function SelProject_Type(){
	$.ajax({
		type: "post",
		url: "repairproject_help.php",
		data: "submit=item&pid="+$("#sel_project_type").val(),
		success: function (data) {
			$("#sel_project").empty();
			$(data).appendTo("#sel_project");
		},
		error: function () {
			alert("出現錯誤");
		}
	});
}
function validate(obj){
	var reg = new RegExp("^[0-9]*$");
	
	if(!reg.test(obj.value)){
		return false;
	}else{
		return true;
	}
}

var wBox=null;
function show(type,id) {
	switch(type){
		case 1:
			wBox=$("#look_").wBox({requestType: "iframe",target:"Itsupport_help.php?id="+id+"&type="+type,title:"求助事宜編輯", width:200,height:400});
		break;
		case 2:
			wBox=$("#look_").wBox({requestType: "iframe",target:"Itsupport_help.php?id="+id+"&type="+type,title:"器材編輯", width:200,height:400});
		break;
		
	}
	wBox.showBox();
}
		
function deviceShow(l){
	switch(l){
		case 0:
			$("#title_d").hide();
			$("#title_w").hide();
			$("#project_d").hide();
			$("#project_w").hide();
			$("#project_t").show();
			$("#title_t").hide();
			
			
		break;
		case 1:
			$("#title_d").show();
			$("#title_w").hide();
			$("#project_d").show();
			$("#project_w").hide();
			$("#project_t").hide();
			$("#title_t").hide();
		break;
		case 2:
			$("#title_d").show();
			$("#title_w").show();
			$("#project_d").show();
			$("#project_w").show();
			$("#project_t").hide();
			$("#title_t").hide();
		break;
		
	}
}
function sendAddSubmit(){
	if(document.getElementById('txt_sort').value=="") {
		alert('請提供「排序」');
		return false;
	}else if(!validate(document.getElementById('txt_sort'))) {
		alert('「排序」請輸入數字');
		return false;
	}
	if(document.getElementById('txt_name').value=="") {
		alert('請提供「名稱」');
		return false;
	}
	if (document.getElementById('type_h').checked&&document.getElementById('sel_project_type').selectedIndex == 0) {
		alert('請提供「位置」選項');
		return false;
	}
	if (document.getElementById('type_h').checked&&document.getElementById('sel_project').selectedIndex == 0) {
		alert('請提供「維修項目」選項');
		return false;
	}
	if (document.getElementById('type_w').checked&&document.getElementById('sel_project_type').selectedIndex == 0) {
		alert('請提供「位置」選項');
		return false;
	}			
}
		
</script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
}
.detail {
	display:none;
}
-->
</style></head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<!--
<div align="center"  style="width:994px">
  <h1><u>IT壞機報告管理</u></h1>
</div>
-->
<div align="center" style="width:994px">
  <h2>新增</h2>
</div>
<div  style=" width:994px; text-align: right;">
<form name="form_SendUpdate" id="form" action="CMS_itsupport_manage.php?submit=add" method="post" onSubmit="return sendAddSubmit();">
<table width="994px" border="1" style="border-collapse:collapse;" borderColor="black" cellspacing="0" cellpadding="0">
	
	<tr bgcolor="CCFFFF">
		<td align="center" height="30px"  width="30%"><b>類型</b></td>
		<td align="center"  width="15%"  style="display:none" id="title_t"><b>所屬</b></td>
		<td align="center"  width="15%"  style="display:none" id="title_d"><b>器材</b></td>
		<td align="center"  width="15%"  style="display:none" id="title_w"><b>求助事宜</b></td>
        <td align="center"  width="15%" ><b>名稱</b></td>
        <td align="center" width="15%"><b>排序</b></td>
        <td align="center" width="10%"><b>編輯</b></td>
    </tr>
			<tr>
			  <td align="center"    height="30px" >
			  <label for="type_d"><input  name="type_project" type="radio"  id="type_d" value="0"  checked="checked"  onClick="deviceShow(0)">器材</label>
			  <label for="type_w"><input  name="type_project" type="radio"  id="type_w" value="1"   onClick="deviceShow(1)">求助事宜</label>
			          <td align="center"   id="project_d"  style="display:none" >
					  <select name="sel_project_type" id="sel_project_type" style="width: 95%" onChange="SelProject_Type();">
        		<?php 
        		    echo "<option value=\"0\">請選擇</option>";
					$sql_project_type  = "SELECT chr_name, int_sort,int_id FROM tbl_itsupport_item  ORDER BY int_sort asc";
					$result_project_type = mysqli_query($con, $sql_project_type) or die($sql_project_type);
					while($record_project_type = mysqli_fetch_array($result_project_type)) {
						echo "<option value='".$record_project_type[int_id]."'>$record_project_type[chr_name]</option>";
					}
				?>
        	</select>
			</td>
			<td align="center"   id="project_w"  style="display:none" >
			<select name="sel_project" id="sel_project" style="width: 95%">
				<option value="0">請選擇</option>
        	</select>
					  </td>
        <td align="center"  ><input type="text"  name="txt_name" id="txt_name"/></td>
        <td align="center" ><input type="text"  name="txt_sort" id="txt_sort" value="0"/></td>
        <td align="center"><input name="tijiao" type="submit" value="輸入"></td>
    </tr>
	</table>
<input type="hidden" id="updateIds" name="updateIds" value="0" />
</form>
</div>
<?php 
$deviceId=$_POST["sel_divice"];
$deviceId=$deviceId==""?"0":$deviceId;

?>
<br/>
<br/>
<?php 
	$sql_Notcomplete  = "select * from tbl_itsupport_item ";
	/*
	if($deviceId!="0"){
		$sql_Notcomplete  .= " where int_id=".$deviceId;
	}
	*/
	$sql_Notcomplete  .= " order by int_sort asc";
	$result_Notcomplete = mysqli_query($con, $sql_Notcomplete) or die($sql_Notcomplete);
	$recordCount = 1;
if (mysqli_num_rows($result_Notcomplete) == 0) {
?>
	<table width="994px" cellspacing="0" cellpadding="0" border="0">
	<tr><td align="center"><font size=5 color=red>沒有紀錄 !!</font></td></tr>
<?php 
} else {
?>

<table width="994px" border="1" style="border-collapse:collapse;" borderColor="black" cellspacing="0" cellpadding="0">
	
		<tr bgcolor="CCFFFF">
        <td align="center"  height="30px" width="60%"><b>名稱</b></td>
        <td align="center" width="20%"><b>排序</b></td>
        <td align="center" width="20%"><b>編輯</b></td>
    </tr>

<?php 
	}
	while($record_Notcomplete = mysqli_fetch_array($result_Notcomplete)) {
		//$bgcolor = ($recordCount%2==0) ? "#DDDDDD" : "#fff";
?>

	<?php  	
	$sql_project  = "select * from tbl_itsupport_detail where int_item_id=".$record_Notcomplete[int_id]." order by int_sort asc";
	$result_project = mysqli_query($con, $sql_project) or die($sql_project);
	?>
	<tr class="item" data-id="<?=$record_Notcomplete[int_id]?>">
        <td align="left" height="30px">&nbsp;<?=$record_Notcomplete[chr_name]?></td>
        <td align="center" height="25" ><?=$record_Notcomplete[int_sort];?></td>
        <td align="center" ><a href="javascript:show(2,<?=$record_Notcomplete[int_id]?>)" >編輯</a> <?php  if(mysqli_num_rows($result_project)==0){?> | <a href="?submit=del&id=<?=$record_Notcomplete[int_id] ?>&type=2" onClick="return confirm('確定刪除？')">刪除</a><?php  }?> </td>
	</tr>
	<?php 
	while($record_project = mysqli_fetch_array($result_project)) { ?>
		<tr bgcolor="#BEBEBE" class="detail item-<?=$record_Notcomplete[int_id]?>" data-id="<?=$record_project[int_id]?>">
			<td align="left">&nbsp;&nbsp;&nbsp;<?=$record_project[chr_name]?></td>
			<td align="center" height="25" ><?=$record_project[int_sort];?></td>
			<td align="center" ><a href="javascript:show(1,<?=$record_project[int_id]?>)">編輯</a> | <a href="?submit=del&id=<?=$record_project[int_id] ?>&type=1" onClick="return confirm('確定刪除？')">刪除</a></td>
		</tr>
    <?php }?>
  
  
<?php 	$recordCount ++;}?>
</table>
<script>
	$(function(){
		
		$(".item").on("click", function(event){
			if($(event.target).context.tagName=='A')
				return;
			let _id = $(this).data('id');
			$(".item-" + _id).toggle();
		});
	});
</script>
</body>
</html>