<?php 
  session_start();   
  require($DOCUMENT_ROOT . "connect.inc");      
  if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'CMS_library.php';
    header('Location: login.php');
  }
?>
<html>
<head>
<title>內聯網</title>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<link href="class.css" rel="stylesheet" type="text/css">
<script src="js/jquery-1.4.1.js"></script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
}
-->
</style>
<script language="javascript">
var i = 0,j = 1;     //行號與列號
var oNewRow ;    //定義插入行對象
var oNewCell1,oNewCell2,oNewCell3;     //定義插入列對象
//添加條件行
function AddRow(){
	i = document.all.MyTable.rows.length;
	oNewRow = document.all.MyTable.insertRow(i);
	oNewRow.id = j;
	
	//添加第1列---名稱
	oNewCell1 = document.all.MyTable.rows[i].insertCell(0)
	oNewCell1.innerHTML = "<input type='text' style='border:0;border-bottom:1px #000 solid;' name='txt_p_name[]' size='12' id='txt_p_name"+j+"'>";
	//添加第2列---地址
	oNewCell2 = document.all.MyTable.rows[i].insertCell(1)
	oNewCell2.innerHTML = "<input type='file' style='border:0;border-bottom:1px #000 solid;' name='txt_url[]' size='16' id='txt_url"+j+"'>";
	//添加第3列---刪否
	oNewCell3 = document.all.MyTable.rows[i].insertCell(2)
	oNewCell3.innerHTML = "<input type=button name=Del" + j + " value='刪除'"+"onClick='DelCurrentRow(" + j + ");'>";
	j++;
}

//刪除行
function DelCurrentRow(j){
	with(document.all.MyTable){
		for (var i=0;i<rows.length;i++){
			if (rows[i].id == j)
			{
				deleteRow(i);
			}
		}
	}
}
function setUrl(op,pp){
	document.getElementById("txt_temp_url"+pp).value = op.value
}

function subcheck(){
	var bool = true;
	var temp_txt ="";
	var m = document.all.MyTable.rows.length;
	if(m<2){
		temp_txt +="<br># 請添加至少一條檔案";
		bool = false;
	}
	
	for(var i = 1; i <= j;i++){
		if(document.getElementById("txt_p_name"+i)==null){continue;}
		var txt_p_name = document.getElementById("txt_p_name"+i).value;
		var txt_url = document.getElementById("txt_url"+i).value;

		if(txt_p_name=="" || txt_p_name.length==0){		
			temp_txt +="<br># 請填寫檔案名稱";	
			bool = false;
		}
		if(txt_url=="" || txt_url.length==0){	
			temp_txt +="<br># 請填寫檔案地址"; 	
			bool = false;
		}
		if(bool == false){break;}
	}
	
	if(document.getElementById("select_group")==null){
		document.getElementById("t_fenlei").innerHTML = "<font color='red' size='-1'>沒有相關分類，請先添加分類</font>";
		temp_txt +="<br># 沒有相關分類，請先添加分類";
		bool = false;
	}
	
	var select_main = document.getElementById("select_main").value;
	select_main = select_main.substring(select_main.indexOf('.')+1,select_main.length);
	if(select_main!=1 && document.getElementById("cent").checked==false && document.getElementById("logs").checked==false && document.getElementById("dept").checked==false){
		temp_txt +="<br># 可視至少選擇一項！";
		bool = false;
	}
	if(bool==false){document.getElementById("errorDiv").innerHTML = "請完整錄入圖書信息,所有項必填！"+temp_txt;}
	return bool;
}

function Change_main(op)
{
//	alert(op.indexOf('.'));
//	alert(document.getElementById("select_main").value);
	
	var temp = document.getElementById("select_main").value;
	temp = temp.substring(0,temp.indexOf('.'));
	//console.log(temp);
//	var temp = op.substring(op.indexOf('.')+1,op.length);
//	if(temp==1){
	if(temp==6){
	    document.getElementById("other").style.display = "none";
		document.getElementById("other6").style.display = "block";
	 }else{	 
	 	document.getElementById("other").style.display = "block";
		document.getElementById("other6").style.display = "none";
	 }
     $.post("CMS_library_do.php", { 'action':"lib_do",'pram':op.substring(0,op.indexOf('.'))}, function (o) {
         $('#t_fenlei').html(unescape(o));
     })
}
function addTitle(txt){
	document.getElementById("tbl_boss").innerHTML=txt;
}
</script>

<script language="javascript">
var v = 0,k = 1;     //行號與列號
var NewRow ;    //定義插入行對象
var NewCell1,NewCell2,NewCell3;     //定義插入列對象
//添加條件行
function MyRow(){
	v = document.all.MyTable.rows.length;
	NewRow = document.all.MyFile.insertRow(i);
	NewRow.id = k;
	
	//添加第1列---名稱
	NewCell1 = document.all.MyFile.rows[i].insertCell(0)
	NewCell1.innerHTML = "<input type='text' style='border:0;border-bottom:1px #000 solid;' name='txt_name[]' size='12' id='txt_p_name"+k+"'>";
	//添加第2列---地址
	NewCell2 = document.all.MyFile.rows[i].insertCell(1)
	NewCell2.innerHTML = "<input type='file' style='border:0;border-bottom:1px #000 solid;' name='txt_f_url[]' size='16' id='txt_url"+k+"'>";
	//添加第3列---刪否
	NewCell3 = document.all.MyFile.rows[i].insertCell(2)
	NewCell3.innerHTML = "<input type=button name=Del" + k + " value='刪除'"+"onClick='DelRow(" + k + ");'>";
	k++;
}

//刪除行
function DelRow(k){
	with(document.all.MyFile){
		for (var i=0;i<rows.length;i++){
			if (rows[i].id == k)
			{
				deleteRow(i);
			}
		}
	}
}

function selectRow(){
	var cent	= document.getElementById("cent");
	var logs	= document.getElementById("logs");
	var dept	= document.getElementById("dept");
	
	if(dept.checked){
		cent.disabled=true;  
		logs.disabled=true;
		cent.checked=false;
		logs.checked=false;
	}else{
		cent.disabled=false;
		logs.disabled=false;
	}
}
</script>
<style> 
#nav li { float:right;} 
#nav li a { color:#000000; text-decoration:none; padding-top:4px; display:block; width:150px; height:22px; text-align:center; background-color:#ececec; margin-left:2px;} 
#nav li a:hover { background-color:#bbbbbb; color:#ffffff;} 
</style> 
</head>
<body>
<div align="right">
     <ul id="nav"> 
        <li><a href="CMS_library.php">圖書上傳</a></li> 
        <li><a href="CMS_lib_class.php">圖書分類</a></li> 
        <li><a href="CMS_lib_list.php">圖書列表</a></li> 
  	 </ul>
</div>
<br><br>
<div align="center">
<form action="CMS_library_do.php?action=AddLibrary" enctype= "multipart/form-data" onSubmit="return subcheck()" method="post" name="form1">
<table width="530" height="287" border="1">
  <tr>
    <td height="50" colspan="3" align="center">上傳圖書<!--<font color="#FF0000" size="+3">(維護中，暫停使用)</font>--></td>
    </tr>
  <tr>
    <td height="44" align="center">顯示檔案</td>
    <td valign="top">
    <table id="MyTable" width="100%" border="0" cellpadding="0" cellspacing="0" style="text-align:center">
          <tr bgcolor="#EAEAEA">
            <td align="center" width="29%">名稱</td>
            <td align="center" width="58%">地址</td>
            <td align="center" width="13%"><input type=button value="添加" name"addfieldbt" onclick="AddRow();"/></td>
 		</tr>
 </table>
 <script language="javascript">AddRow()</script>
    </td>
  </tr>
  <tr>
    <td height="44" align="center">原始檔案</td>
    <td valign="top">
    <table id="MyFile" width="100%" border="0" cellpadding="0" cellspacing="0" style="text-align:center">
          <tr bgcolor="#EAEAEA">
            <td align="center" width="29%">名稱</td>
            <td align="center" width="58%">地址</td>
            <td align="center" width="13%"><input type=button value="添加" name"addfile" onclick="MyRow();"/></td>
 		</tr>
 </table>
 <script language="javascript">MyRow()</script>
    </td>
  </tr>
  <tr>
    <td height="38" align="center">大類</td>
    <td>
    <select name="select_main" id="select_main" onChange="Change_main(this.value)" style='width:150px;'>
        <?php 
		$temp_main=0;
		 $sql = "select int_id,txt_name,status from tbl_lib_main order by int_sort";
	 	 $res = mysqli_query($con, $sql) or die($sql);
		 while($rec=mysqli_fetch_array($res))
		 {
			 if($temp_main==0){$temp_main=$rec["int_id"];}
		?>
            <option value="<?php  echo $rec["int_id"].".".$rec["status"]?>"> <?php  echo $rec["txt_name"]?> </option>
         <?php 
		 }
         ?>
    </select>
    </td>
  </tr>
   <tr>
    <td width="107" height="35" align="center">分類</td>
    <td width="350" id="t_fenlei">
    <?php 
	 $sql = "select int_id,txt_name from tbl_lib_group where main_id = $temp_main order by int_sort";
	 $res = mysqli_query($con, $sql) or die($sql);
    if(mysqli_num_rows($res)>0)
	{
	?>
		<select name="select_group" id="select_group" style='width:150px;'>
        <?php 
		 while($rec=mysqli_fetch_array($res))
		 {
		?>
            <option value="<?php  echo $rec["int_id"]?>"> <?php  echo $rec["txt_name"]?> </option>
         <?php 
		 }
         ?>
    	</select>
	<?php 	
	}
	?>
    &nbsp;
    </td>
  </tr>
  <tr>
    <td height="32" align="center">可視</td>
    <td>
    <div id="other">
    <input id="cent" type="checkbox" name="cent" checked>分店
    <input id="logs" type="checkbox" name="logs" >後勤
	<input id="dept" type="checkbox" name="dept" value="<?=$_SESSION['dept'];?>" onClick="selectRow()" >部門專屬
    </div>
    <div id="other6" style="display:none">
    <select name="select_shop" id="select_shop" onChange="Change_shop(this)" style='width:150px;'>
        <?php 
		 $sql = "select  int_id,CONCAT('#',chr_ename,' - ',txt_name) AS txt_name from tbl_user where int_dept = 2 ";
	 	 $res = mysqli_query($con, $sql) or die($sql);
		 while($rec=mysqli_fetch_array($res))
		 {
		?>
            <option value="<?php  echo $rec['int_id'].".".$rec['status'] ?>"> <?php  echo $rec['txt_name']?> </option>
         <?php 
		 }
         ?>
    </select>
    </div>
    </td>
  </tr>
  <tr>
    <td height="34">&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="    提    交     "></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td  align="left" id="errorDiv"  style="color:#F00; font-size:13px">&nbsp;</td>
  </tr>
</table>
</form>
</div>
</body>
</html>