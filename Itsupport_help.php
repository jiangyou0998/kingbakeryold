<?php 
session_start();     
if (!($_SESSION[authenticated])) {
	$_SESSION['status'] = 'neverLogin';
	header('Location: index.php');
}

require($DOCUMENT_ROOT . "connect.inc");
if(isset($_POST['submit'])) {
	$submit = $_POST['submit'];
	if($submit == "更新"){
		$id=$_POST['int_id'];
		switch($_GET[type]){
			case 1:
				$sql_update="update tbl_itsupport_detail  set chr_name='".$_POST["txt_name"]."',int_sort=".$_POST["txt_sort"].",int_item_id=".$_POST["sel_project_type"]." where int_id=".$id;
			break;
			case 2:
				$sql_update="update tbl_itsupport_item  set chr_name='".$_POST["txt_name"]."',int_sort=".$_POST["txt_sort"]." where int_id=".$id;
			break;
		}
		$update_Result = mysqli_query($con, $sql_update) or die($sql_update);
		if($update_Result > 0) {
			?><script language="javascript">alert('紀錄更新成功!'); top.location.href = "CMS_itsupport_manage.php"</script><?php 
		}
	}
	else if( $submit == "item"){
	    $pid = $_POST['pid'];
		
  		$select_sql  = "select * from tbl_itsupport_item where int_loc_id=".$pid." order by int_sort asc";
  		$select_result = mysqli_query($con, $select_sql) or die($select_sql);
		
		$seloption="<option value=\"0\">請選擇</option>";
		$i=0;
		while($select_record = mysqli_fetch_array($select_result)){
			$seloption.="<option value='".$select_record['int_id']."'>".$select_record['chr_name']."</option>";
			$i++;
		}
		if($i==0){
			$seloption.="<option value='10000'>另提供</option>";
		}
		echo $seloption;
		exit;
	}
	else if( $submit == "detail"){
	    $pid = $_POST['pid'];
		
  		$select_sql  = "select * from tbl_itsupport_detail where int_item_id=".$pid." order by int_sort asc";
  		$select_result = mysqli_query($con, $select_sql) or die($select_sql);
		
		$seloption="<option value=\"0\">請選擇</option>";
		$i=0;
		while($select_record = mysqli_fetch_array($select_result)){
			$seloption.="<option value='".$select_record['int_id']."'>".$select_record['chr_name']."</option>";
			$i++;
		}
		if($i==0){
			$seloption.="<option value='10000'>另提供</option>";
		}
		echo $seloption;
		exit;
	}
	
}
switch($_GET[type]){
	
	case 1:
		$sql_completed  = "SELECT detail.*, item.int_id AS item_id
		FROM tbl_itsupport_detail AS detail
			LEFT JOIN tbl_itsupport_item item ON item.int_id = detail.int_item_id
		WHERE
			detail.int_id = $_GET[id]
		ORDER BY detail.int_sort ASC";
	break;
	case 2:
		$sql_completed  = "select * from  tbl_itsupport_item  where int_id=".$_GET["id"]." order by int_sort asc";
	break;
}

$result_completed = mysqli_query($con, $sql_completed) or die($sql_completed);
$recordCount = 1;
$record_completed = mysqli_fetch_array($result_completed);


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<!-- <title>貨品投訴系統</title> -->
<title>內聯網</title>
<style type="text/css">
*{padding:0;margin:0;}
body,td,th {font-size: small;line-height:30px;}
</style>
<script src="js/jquery-1.4.1.js"></script>
<script>
$(function(){
	switch(<?=$_GET[type]?>){
		case 1:
			$("#project_type_tr").show();
			$("#project_tr").hide();
			$("#genus_tr").hide();
		break;
		case 2:
			$("#project_type_tr").hide();
			$("#project_tr").hide();
			$("#genus_tr").show();
		break;
		
	}
	SelProject_Type();
	$("#sel_project_type").val('<?=$record_completed[int_item_id]?>');
});
function SelProject_Type(){
	
	$.ajax({
		async:false,
		type: "post",
		url: "Itsupport_help.php",
		data: "submit=item&pid="+$("#sel_project_type").val(),
		success: function (data) {
			console.log(data);
			$("#sel_project").empty();
			$(data).appendTo("#sel_project");
		},
		error: function () {
			alert("出現錯誤");
		}
	});
	
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
	if ($("#project_type_tr").arrt("style")!="display: none;"&&document.getElementById('sel_project_type').selectedIndex == 0) {
		alert('請提供「位置」選項');
		return false;
	}
	if ($("#project_tr").arrt("style")!="display: none;"&&document.getElementById('sel_project').selectedIndex == 0) {
		alert('請提供「維修項目」選項');
		return false;
	}	

}
function validate(obj){
	var reg = new RegExp("^[0-9]*$");
	if(!reg.test(obj.value)){
		return false;
	}else{
		return true;
	}
}
</script>
</head>
<body bgcolor="CCFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form_CaseUpdate" id="form" action="Itsupport_help.php?type=<?=$_GET[type]?>&id=<?=$_GET["id"]?>" method="post" onSubmit="return sendAddSubmit();">
<table width="100%" border="1" style="border-collapse:collapse;" borderColor="black"cellspacing="0" cellpadding="0">
	<input name="int_id" type="hidden" value="<?=$_GET["id"]?>">
	<tr bgcolor="CCFFFF" id="project_type_tr">
        <td align="right" ><b>器　　材</b>&nbsp</td>
        <td align="left" ><select name="sel_project_type" id="sel_project_type" style="width: 95%" onChange="SelProject_Type();">
		<?php 
		echo "<option value=\"0\">請選擇</option>";
		$sql_project_type  = "SELECT chr_name, int_sort,int_id FROM tbl_itsupport_item ORDER BY int_sort asc";
		$result_project_type = mysqli_query($con, $sql_project_type) or die($sql_project_type);
		while($record_project_type = mysqli_fetch_array($result_project_type)) {
			echo "<option value='".$record_project_type[int_id]."'>$record_project_type[chr_name]</option>";
		}
		?>
		</select>
		</td>
	</tr>
	

	<tr bgcolor="CCFFFF">
        <td align="right" style="width:20%" ><b>名　　稱</b>&nbsp</td>
        <td align="left" ><input type="text" style="width:95%"   name="txt_name" id="txt_name" value="<?=$record_completed[chr_name]?>"/></td>
	</tr>
	<tr bgcolor="CCFFFF">
        <td align="right" ><b>排　　序</b>&nbsp</td>
        <td align="left" ><input type="text" style="width:95%"  name="txt_sort" id="txt_sort" value="<?=$record_completed[int_sort]?>"/></td>
	</tr>
			
	</tr>
	<tr bgcolor="CCFFFF">
        <td align="center" ></td>
        <td align="center" ><input type="submit" id="submit" name="submit" value="更新"/></td>
	</tr>
</table>
</form>
<br>
</body>
</html>