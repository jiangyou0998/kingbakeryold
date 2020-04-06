<?php 
  session_start();   
  require($DOCUMENT_ROOT . "connect.inc");   
  if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'CMS_library.php';
    header('Location: login.php');
  }
  
  if($_REQUEST[action]=='DALETE_M'){
	  $sql = "select count(*) from tbl_lib_group where main_id = $_REQUEST[int_id]";
  	  $res = mysqli_query($con, $sql) or die("invalid query_m: ".$sql);
      $rec = mysqli_fetch_array($res);
	  if($rec[0]>0)
	  {
		  echo "<script>alert('此類包含分類數據,暫不能刪除!');document.location.href='CRM_lib_class.php';</script>";
	  }else{
		  $sql ="DELETE from tbl_lib_main where int_id = $_REQUEST[int_id]";
		  mysqli_query($con, $sql) or die("刪除出錯...");
	  }
  }
  else if($_REQUEST[action]=='DALETE_G')
  {
	  $sql = "select count(*) from tbl_lib_pmd where group_id = $_REQUEST[int_id] and date_delete = '2000-01-01'";
  	  $res = mysqli_query($con, $sql) or die("invalid query_g");
      $rec = mysqli_fetch_array($res);
	  if($rec[0]>0)
	  {
		  echo "<script>alert('此分類包含檔案,暫不能刪除!');document.location.href='CRM_lib_class.php';</script>";
	  }else{
		  $sql ="DELETE from tbl_lib_group where int_id = $_REQUEST[int_id]";
		  mysqli_query($con, $sql) or die("刪除出錯...");
	  } 
	  $main = $_REQUEST[main];
	  echo "<script>location='?linkState=true&int_main=$main';</script>";
  }
  
  if($_REQUEST[action]=='UPDATE_M'){
	$int_id = $_REQUEST[int_id];
	$txt_name = $_REQUEST["txt_name".$int_id];
	if($_REQUEST["int_sort".$int_id])
	{
		$int_sort = $_REQUEST["int_sort".$int_id];
	}else{
		$int_sort =0;
	}
	$sql = "update tbl_lib_main set txt_name='$txt_name',int_sort=$int_sort where int_id=$int_id";
	$result = mysqli_query($con, $sql) or die("修改出錯...");
  }
  else if($_REQUEST[action]=='UPDATE_G')
  {
	$int_id = $_REQUEST[int_id];
	$txt_name = $_REQUEST["txt_name".$int_id];
	$main = $_REQUEST[main];
	if($_REQUEST["int_sort".$int_id])
	{
		$int_sort = $_REQUEST["int_sort".$int_id];
	}else{
		$int_sort =0;
	}
	$sql = "update tbl_lib_group set txt_name='$txt_name',int_sort=$int_sort where int_id=$int_id";
	$result = mysqli_query($con, $sql) or die("修改出錯...");
	echo "<script>location='?linkState=true&int_main=$main';</script>";
  }
  if($_REQUEST[action]=='main'){
	   $sql = "select max(int_sort) as int_sort from tbl_lib_main";
  	  $res = mysqli_query($con, $sql) or die("invalid query");
      $rec = mysqli_fetch_array($res);
	  $int_sort = $rec[int_sort]+1;
	  
	  $txt_name = $_REQUEST["txt_name"];
	  $status = $_REQUEST["type"];
	  $sql = "INSERT INTO tbl_lib_main (txt_name,int_sort,status) VALUES('$txt_name',$int_sort,$status) ";
	  $result = mysqli_query($con, $sql) or die("修改出錯...".$sql);
  }
  if($_REQUEST[action]=='group'){
	   $sql = "select max(int_sort) as int_sort from tbl_lib_group";
  	  $res = mysqli_query($con, $sql) or die("invalid query");
      $rec = mysqli_fetch_array($res);
	  $int_sort = $rec[int_sort]+1;
	  
	  $main = $_REQUEST[main];
	  $txt_name = $_REQUEST["txt_name"];
	  $sql = "INSERT INTO tbl_lib_group (txt_name,main_id,int_sort) VALUES('$txt_name', $main,$int_sort) ";
	  $result = mysqli_query($con, $sql) or die("修改出錯...".$sql);
	  echo "<script>location='?linkState=true&int_main=$main';</script>";
  }
?>
<html>
<head>
<title>內聯網</title>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<link href="class.css" rel="stylesheet" type="text/css">
<script src="js/library.js"></script>
<style type="text/css">
<!--
body {margin-left: 0px;margin-top: 0px;}
-->
</style>
<script language="javascript">
var bool=true;
function addlib(aa) {
	if(bool){
		 document.getElementById("tblMess").style.display="block";bool=false;
	 }else{
		 document.getElementById("tblMess").style.display="none"; bool=true;
	 }
}
function add_lib(){
	var name = document.getElementById("txt_name").value;
	if(name==""||name.length==0){
		return false;
	}else{
		return true;	
	}
}
//修改
function update_lib(op){
	document.getElementById("name1"+op).style.display="none";
	document.getElementById("but_update"+op).style.display="none";
	document.getElementById("but_del"+op).style.display="none";
	
	document.getElementById("name2"+op).style.display="block";
	document.getElementById("but_save"+op).style.display="block";
	document.getElementById("but_res"+op).style.display="block";
	if(document.getElementById("sort1"+op)!=null)
	{
		
		document.getElementById("sort1"+op).style.display="none";
		document.getElementById("sort2"+op).style.display="block";
	}
}
//取消
function res_lib(op){
	document.getElementById("name1"+op).style.display="block";
	document.getElementById("but_update"+op).style.display="block";
	document.getElementById("but_del"+op).style.display="block";
	
	document.getElementById("name2"+op).style.display="none";
	document.getElementById("but_save"+op).style.display="none";
	document.getElementById("but_res"+op).style.display="none";
	if(document.getElementById("sort1"+op)!=null)
	{
		document.getElementById("sort1"+op).style.display="block";
		document.getElementById("sort2"+op).style.display="none";
	}
}

//刪除
function delete_lib(op,s,main){
	var bool = confirm("是否確定刪除該檔？");
	if(!bool){return;}
	if(s==1)
	{
		document.location = "?action=DALETE_M&int_id="+op+"&main="+main;
	}else if(s==2){
		document.location = "?action=DALETE_G&int_id="+op+"&main="+main;
	}
}
//保存
function save_lib(op,s,main){
	var txt_name = document.getElementById("txt_name"+op).value;
	if(txt_name=="" || txt_name.length ==0){
		alert("類名不能為空");
		return false;
	}
	if(s==1)
	{
		document.form2.action = "?action=UPDATE_M&int_id="+op+"&main="+main;
	}else if(s==2){
		document.form2.action = "?action=UPDATE_G&int_id="+op+"&main="+main;
	}
	document.form2.submit();
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
<?php 
if(!$_REQUEST[linkState])
{
?>
<div align="right">
<form enctype= "multipart/form-data" onSubmit="return add_lib()" action="?action=main" method="POST" name="form1" id="form1">
<table height="37">
<tr>
<td height="25" width="500" align="right">
 <table height="20" id="tblMess" align="center" style="display:none;">
  <tr>
      <td align="right">
      大類名稱：<input name="txt_name" type="text" size="15" />
      可視：
        <select name="type" id="type">
        <option value="0">通用</option>
        <option value="1">專屬</option>
        </select>
      </td>
      <td ><input type="Submit" name="Submit" value="提交" /></td>
    </tr>
 </table> </td>
 <td width="125">
 <a href="javascript:addlib()">新增大類</a>
 </td>
 </tr>
 </table>
</form>
</div>
<form  enctype= "multipart/form-data" name="form2" method="post"  action="">
<table width="624" height="65" border="1" cellpadding="0" cellspacing="0" style="text-align:center"s>
  <tr>
    <td width="431" height="23" bgcolor="#CCCCCC"><strong>大類名稱</strong></td>
    <td width="80" bgcolor="#CCCCCC"><strong>排序</strong></td>
    <td width="42" bgcolor="#CCCCCC"><strong>修改</strong></td>
    <td width="43" bgcolor="#CCCCCC"><strong>刪除</strong></td>
  </tr>
  <?php 
  $sql = "select int_id,txt_name,int_sort from tbl_lib_main order by int_sort";
  $res = mysqli_query($con, $sql) or die("invalid query");
  while($rec = mysqli_fetch_array($res)){
  ?>
  <tr>
    <td width="431" height="38" >
    <div id="name1<?=$rec[int_id]?>" style="display:block">
    <a href="?linkState=true&int_main=<?=$rec[int_id]?>&name=<?=$rec[txt_name]?>">
	<?=$rec[txt_name]?></a></div>
    <div id="name2<?=$rec[int_id]?>" style="display:none" >
    <input type="text" id="txt_name<?=$rec[int_id]?>" name="txt_name<?=$rec[int_id]?>" size="16" value="<?=$rec[txt_name]?>">
    </div>
    </td>
    <td width="80" height="38" >
    <div id="sort1<?=$rec[int_id]?>" style="display:block"> <?=$rec[int_sort]?> </div>
    <div id="sort2<?=$rec[int_id]?>" style="display:none" >
        <input type="text" id="int_sort<?=$rec[int_id]?>" name="int_sort<?=$rec[int_id]?>" size="6" value="<?=$rec[int_sort]?>">
     </div>
    </td>

    <td width="42">
      <input type="button" id ="but_update<?=$rec[int_id]?>" value="修改" onClick="update_lib(<?=$rec[int_id]?>)">
      <input type="button" id ="but_save<?=$rec[int_id]?>" style=" display:none" value="保存" onClick="save_lib(<?=$rec[int_id]?>,1,0)">
    </td>
    <td width="43">
    <input type="button" id ="but_del<?=$rec[int_id]?>" value="刪除" onClick="delete_lib(<?=$rec[int_id]?>,1,0)">
    <input type="button" id ="but_res<?=$rec[int_id]?>" style=" display:none" value="取消" onClick="res_lib(<?=$rec[int_id]?>)">
    </td>
  </tr>
  <?php 
  }
  ?>
</table>
</form>
<?php 
}else if($_REQUEST[linkState]){
?>
<div align="right">
<form enctype= "multipart/form-data" onSubmit="return add_lib()" action="?action=group&main=<?=$_REQUEST[int_main]?>" method="POST" name="form1" id="form1">
<table height="37">
<tr>
<td height="25" width="500" align="right">
 <table height="20" id="tblMess" align="center" style="display:none;">
  <tr>
      <td align="right">
      分類名稱：<input name="txt_name" type="text" size="15" />
      </td>
      <td ><input type="Submit" name="Submit" value="提交" /></td>
    </tr>
 </table> </td>
 <td width="125">
 <a href="javascript:addlib()">新增分類</a>
 </td>
 </tr>
 </table>
</form>
</div>

<form  enctype= "multipart/form-data" name="form2" method="post"  action="">
<table width="624" height="63" border="1" cellpadding="0" cellspacing="0" style="text-align:center"s>
  <tr>
    <td width="432" height="20" valign="middle" bgcolor="#CCCCCC"><strong>所屬大類:  <font color="#666666" size="+2">
      <?=$_REQUEST[name]?>
    </font></strong></td>
    <td width="67" bgcolor="#CCCCCC"><strong>排序</strong></td>
    <td width="49" bgcolor="#CCCCCC"><strong>修改</strong></td>
    <td width="48" bgcolor="#CCCCCC"><strong>刪除</strong></td>
  </tr>
  <?php 
  $sql = "select int_id,txt_name,int_sort from tbl_lib_group where  main_id = $_REQUEST[int_main] order by int_sort";

  $res = mysqli_query($con, $sql) or die("invalid query: ".$sql);
  while($rec = mysqli_fetch_array($res)){
  ?>
  <tr>
    <td width="432" height="38"  >
    <div id="name1<?=$rec[int_id]?>" style="display:block"><?=$rec[txt_name]?></div>
     <div id="name2<?=$rec[int_id]?>" style="display:none" >
        <input type="text" id="txt_name<?=$rec[int_id]?>" name="txt_name<?=$rec[int_id]?>" size="16" value="<?=$rec[txt_name]?>">
      </div>
    </td>
    <td width="67" height="38"  >
    <div id="sort1<?=$rec[int_id]?>" style="display:block"> <?=$rec[int_sort]?> </div>
    <div id="sort2<?=$rec[int_id]?>" style="display:none" >
        <input type="text" id="int_sort<?=$rec[int_id]?>" name="int_sort<?=$rec[int_id]?>" size="6" value="<?=$rec[int_sort]?>">
     </div>
     </td>
   <td width="49"><input type="button" id ="but_update<?=$rec[int_id]?>" value="修改" onClick="update_lib(<?=$rec[int_id]?>)">
      <input type="button" id ="but_save<?=$rec[int_id]?>" style=" display:none" value="保存" onClick="save_lib(<?=$rec[int_id]?>,2,<?=$_REQUEST[int_main]?>)"></td>
   <td width="48">
   	   <input type="button" id ="but_del<?=$rec[int_id]?>" value="刪除" onClick="delete_lib(<?=$rec[int_id]?>,2,<?=$_REQUEST[int_main]?>)">
      <input type="button" id ="but_res<?=$rec[int_id]?>" style=" display:none" value="取消" onClick="res_lib(<?=$rec[int_id]?>)"></td>
  </tr>
  <?php 
  }
  ?>
</table>
</form>
<?php 
}
?>
<p>&nbsp;</p>
</div>
</body>
</html>