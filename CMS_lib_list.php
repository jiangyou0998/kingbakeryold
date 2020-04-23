<?php

//檢查是否登錄,是否管理員
require ("check_login.php");

  require($DOCUMENT_ROOT . "connect.inc");

  if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'CMS_library.php';
    header('Location: login.php');
  }
  if($_REQUEST[action]=='DALETE'){
	  $time = date("Y-m-d");     //時間	
	  $sql ="UPDATE tbl_lib_pmd set date_delete = '$time' where int_id = $_REQUEST[int_id]";
	  mysqli_query($con, $sql) or die("刪除出錯...");
  }
  if($_REQUEST[action]=='UPDATE'){
	    $pmd_id = $_REQUEST[pmd_id];
		$date=date('Y-m-d');
		$select_main = $_REQUEST['select_main'.$pmd_id];//大類
		$select_group = $_REQUEST['select_group'.$pmd_id];//分類

		$time=date('Ymdhis');	//得到當前時間;20070705163148
		$fileName=$_FILES['txt_path'.$pmd_id]['name'];	//得到上傳文件的名字
		$oldPath =$_FILES['txt_path'.$pmd_id]['tmp_name'];	//臨時文件夾，即以前的路徑
		
		$name = explode('.',$fileName);	//將文件以'.'分割的到後綴名,得到一個數組。
		$newPath = "library/".$time.'.'.$name[count($name)-1];	//得到一個新的數組為'20070705163148.pfd',即新的路徑
		$newname = $_POST['txt_name'.$pmd_id];
		
		//上傳原始文檔
		$oriname = $_FILES['txt_url'.$pmd_id]['name'];//文檔名稱
		$oritmp  = $_FILES['txt_url'.$pmd_id]['tmp_name'];//文檔臨時地址
		$oname   = explode('.',$oriname);
		$newUrl  = "library/".$time.'ori'.'.'.$oname[count($oname)-1];//文檔新地址
		$newfile = $_POST['txt_oriname'.$pmd_id];//文檔新名稱
		
		$select_shop = $_REQUEST['select_shop'.$pmd_id];	 //分店D
		$cent =0;$logs =0;$dept=0;	
		if($_REQUEST['cent'.$pmd_id]){	$cent=1;	}//分店
		if($_REQUEST['logs'.$pmd_id]){	$logs=1;	}//後勤
		if($_REQUEST['dept'.$pmd_id]){	$dept=1;	}//後勤
		if($fileName==""&&$oriname=="")
		{
			$sql = "update tbl_lib_pmd set author='$_SESSION[user]',group_id=$select_group,date_modify='$time',state_cent=$cent,
			state_logs=$logs,state_dept=$dept,int_shop=$select_shop,txt_name='$newname',txt_ori_file='$newfile' where int_id=$pmd_id";
			$result = mysqli_query($con, $sql) or die("修改出錯:".$sql);
			echo "<script>document.location.href='CMS_lib_list.php';<--/script>";
		}
		else if(isset($oldPath) || isset($oritmp)) 
		{
			if($oldPath){
				if(move_uploaded_file($oldPath, $newPath)){
					$sqls ="select txt_path from tbl_lib_pmd where int_id=".$pmd_id;
					$results = mysqli_query($con, $sqls) or die($sqls);
					$record = mysqli_fetch_array($results); 
			
					$sql = "update tbl_lib_pmd set author='$_SESSION[user]',group_id=$select_group,
					date_modify='$time',state_cent=$cent,state_logs=$logs,state_dept=$dept,
					int_shop=$select_shop,txt_name='$newname',txt_path='$newPath' where int_id=$pmd_id";

					$result = mysqli_query($con, $sql) or die("修改出錯:".$sql);
					if(file_exists($record["txt_path"]))
					{
						unlink($record["txt_path"]);
					}
					echo "<script>document.location.href='CMS_lib_list.php';</script>";
				}
			}else if($oritmp){
				if(move_uploaded_file($oritmp,$newUrl)){
					$sqls ="select txt_ori_url from tbl_lib_pmd where int_id=".$pmd_id;
					$results = mysqli_query($con, $sqls) or die($sqls);
					$record = mysqli_fetch_array($results);
					
					$sql = "update tbl_lib_pmd set  txt_ori_file='$newfile',txt_ori_name='$oriname',txt_ori_url='$newUrl' where int_id=$pmd_id";
					
					$result = mysqli_query($con, $sql) or die("修改出錯:".$sql);
					if(file_exists($record["txt_ori_url"]))
					{
						unlink($record["txt_ori_url"]);
					}
					echo "<script>document.location.href='CMS_lib_list.php';</script>";
				}
			}
			/*
			move_uploaded_file($oldPath, $newPath) && move_uploaded_file($oritmp,$newUrl)
			$sqls ="select txt_path from tbl_lib_pmd where int_id=".$pmd_id;
			$results = mysqli_query($con, $sqls) or die($sqls);
			$record = mysqli_fetch_array($results); 
			
			$sql = "update tbl_lib_pmd set author='$_SESSION[user]',txt_ori_doc='$fileName',txt_ori_file='$newfile',txt_ori_name='$oriname',txt_ori_url='$newUrl',group_id=$select_group,
			date_modify='$time',state_cent=$cent,state_logs=$logs,state_dept=$dept,
			int_shop=$select_shop,txt_name='$newname',txt_path='$newPath' where int_id=$pmd_id";

			$result = mysqli_query($con, $sql) or die("修改出錯:".$sql);
			if(file_exists($record["txt_path"]))
			{
				unlink($record["txt_path"]);
			}
			echo "<script>document.location.href='CMS_lib_list.php';</script>";
			*/
		}else{
			 echo "<font color='#FF0000'> 檔案上傳發生錯誤</font>";
			 if ($_FILES['addfile']['error'] == 1) echo "<br>檔案大小限2MB以下";
		}
  }

	$aryS = Array();
	$shop_sql = "SELECT int_id, CONCAT('#',chr_ename,' - ',txt_name) AS 'shopName' FROM tbl_user WHERE int_dept = 2 ORDER BY chr_ename ";
	$result = mysqli_query($con, $shop_sql) or die($shop_sql);
	while($record  = mysqli_fetch_assoc($result)){
		$aryS[] = $record;
	};
	
	$aryC = Array();
	$cat_sql  = "SELECT * FROM tbl_lib_group";
	if($_REQUEST[type_id]) $cat_sql .= " WHERE main_id = $_REQUEST[type_id];";
	$result = mysqli_query($con, $cat_sql) or die($cat_sql);
	while($record  = mysqli_fetch_assoc($result)){
		$aryC[] = $record;
	};
	
	$pre_name = (isset($_REQUEST['name'])) ? $_REQUEST['name'] : "";
	$pre_shop = (isset($_REQUEST['shop'])) ? $_REQUEST['shop'] : 0;
	$pre_cat  = (isset($_REQUEST['cat'])) ? $_REQUEST['cat'] : 0;
 ?>
<html>
<head>
<title>內聯網</title>
<META name="ROBOTS" content="NOINDEX,NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<link href="class.css" rel="stylesheet" type="text/css">
<script src="js/jquery-1.4.1.js"></script>
<style type="text/css">
<!--
body {
margin-left: 0px;
margin-top: 0px;
}
.style2 {font-size: 24px}

.cssSearch { width:1164px; text-align:left; margin-bottom:10px;}
.cssInput {width: 150px;}
.cssBreak {margin: 8px; 0px; 0px; 8px;}
-->
</style>
<script language="javascript">
//修改
function update_lib(op,status){
	document.getElementById("name1"+op).style.display="none";
	document.getElementById("path1"+op).style.display="none";
	document.getElementById("oriname1"+op).style.display="none";	
//	document.getElementById("url1"+op).style.display="none";
	document.getElementById("but_update"+op).style.display="none";
	document.getElementById("but_del"+op).style.display="none";
	document.getElementById("m_name1"+op).style.display="none";
	document.getElementById("g_name1"+op).style.display="none";
	
	document.getElementById("name2"+op).style.display="block";
	document.getElementById("path2"+op).style.display="block";
	document.getElementById("oriname2"+op).style.display="block";
//	document.getElementById("url2"+op).style.display="block";
	document.getElementById("but_save"+op).style.display="block";
	document.getElementById("but_res"+op).style.display="block";
	document.getElementById("m_name2"+op).style.display="block";
	document.getElementById("g_name2"+op).style.display="block";
	if(status==1)
	{
		document.getElementById("shop1"+op).style.display="none";	
		document.getElementById("shop2"+op).style.display="block";
	}else{
		document.getElementById("cent1"+op).style.display="none";
		document.getElementById("logs1"+op).style.display="none";
		document.getElementById("dept1"+op).style.display="none";
		document.getElementById("cent2"+op).style.display="block";
		document.getElementById("logs2"+op).style.display="block";
		document.getElementById("dept2"+op).style.display="block";
	}
}
//取消
function res_lib(op){
	document.getElementById("name1"+op).style.display="block";
	document.getElementById("oriname1"+op).style.display="block";
	document.getElementById("path1"+op).style.display="block";
//	document.getElementById("url1"+op).style.display="block";
	document.getElementById("but_update"+op).style.display="block";
	document.getElementById("but_del"+op).style.display="block";
	document.getElementById("cent1"+op).style.display="block";
	document.getElementById("logs1"+op).style.display="block";
	document.getElementById("dept1"+op).style.display="block";
	document.getElementById("shop1"+op).style.display="block";
	document.getElementById("m_name1"+op).style.display="block";
	document.getElementById("g_name1"+op).style.display="block";
	
	document.getElementById("name2"+op).style.display="none";
	document.getElementById("oriname2"+op).style.display="none";
	document.getElementById("path2"+op).style.display="none";
//	document.getElementById("url2"+op).style.display="none";
	document.getElementById("but_save"+op).style.display="none";
	document.getElementById("but_res"+op).style.display="none";
	document.getElementById("cent2"+op).style.display="none";
	document.getElementById("logs2"+op).style.display="none";
	document.getElementById("dept2"+op).style.display="none";
	document.getElementById("shop2"+op).style.display="none";
	document.getElementById("m_name2"+op).style.display="none";
	document.getElementById("g_name2"+op).style.display="none";
}
//刪除
function delete_lib(op){
	var bool = confirm("是否確定刪除該檔？");
	if(!bool){return;}
	document.location = "?action=DALETE&int_id="+op;
}
//保存
function save_lib(op,status){
	console.log(op + ", " + status);
	var group = document.getElementById("select_group"+op);
	var txt_name = document.getElementById("txt_name"+op).value;
	//var txt_url = document.getElementById("txt_oriurl"+op).value;
	var shop = document.getElementById("select_shop"+op).value;
	var cent = document.getElementById("cent"+op).checked;
	var logs = document.getElementById("logs"+op).checked;
	var dept = document.getElementById("dept"+op).checked;
	var bool = false;
	if(status==1)
	{
		if(shop==0)bool=true;
	}else{
		if(cent==false&&logs==false&&dept==false)bool=true;
	}
	//txt_url=="" || txt_url.length == 0 ||
	if(group==null||txt_name=="" || txt_name.length ==0 ||  bool){
		//alert(group+author+txt_name);
		alert("請填入完整再保存");
		return false;
	}
	document.form1.action = "?action=UPDATE&pmd_id="+op;
	document.form1.submit();
}

function changeClik(op){
	if (op == null) {
		op = document.getElementById("type");
		
		var name = document.getElementById("fieldName").value;
		var shop = document.getElementById("fieldShop").value;
		var cat  = document.getElementById("fieldCat").value;
		
		var value = (op==null)? 0 : op.value;
		
		if(value == 0){
			submitForm(0,  name, shop, cat);
		}else{	
			submitForm(value,  name, shop, cat);
		}
	}
	else{
		if(op.value == 0){
			document.location="?";
		}else{	
			document.location="?type_id="+ op.value;
		}
	}
}
function Change_main(op,id) {
     $.post("CMS_library_do.php", { 'action':"list_do",'pram':op.substring(0,op.indexOf('.')),'id':id}, function (o) {
         $("#g_name2"+id).html(unescape(o));
     })
}
function select_check(op){
	var cent	= document.getElementById("cent"+op);
	var logs	= document.getElementById("logs"+op);
	var dept	= document.getElementById("dept"+op);

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

function load(){
	var name = '<?=$pre_name?>';
	var shop = <?=$pre_shop?>;
	var cat = <?=$pre_cat?>;
	document.getElementById('fieldName').value = name;
	document.getElementById('fieldShop').value = shop;
	document.getElementById('fieldCat').value = cat;
}
function submitForm(type, name, shop, cat){
	var f = document.getElementById('form_search');
	f.name.value = name;
	f.type_id.value = type;
	f.shop.value = shop;
	f.cat.value = cat;
	f.submit();
}
</script>
<style> 
#nav li { float:right;} 
#nav li a { color:#000000; text-decoration:none; padding-top:4px; display:block; width:150px; height:22px; text-align:center; background-color:#ececec; margin-left:2px;} 
#nav li a:hover { background-color:#bbbbbb; color:#ffffff;} 
</style> 
</head>
<body onload="load()">
<form id="form_search">
	<input type="hidden" name="name">
	<input type="hidden" name="type_id">
	<input type="hidden" name="shop">
	<input type="hidden" name="cat">
</form>
<?php  if($_REQUEST[action]!='VIEW_ONLY'){ ?>
<div align="right">
     <ul id="nav"> 
         <li><a href="CMS_library.php">圖書上傳</a></li> 
         <li><a href="CMS_lib_class.php">圖書分類</a></li> 
         <li><a href="CMS_lib_list.php">圖書列表</a></li> 
  	 </ul>
</div>
<?php  } ?>
<br><br>
<div align="center">
<?php  if($_REQUEST[action]!='VIEW_ONLY'){ ?>
<div align="left"style="width:1164px" >
<span class="cssField">類型選擇:</span>
<select name="type" id="type" onChange="changeClik(this)" class="cssInput">
<option value="0">顯示所有</option>
<?php 
$sql = "select int_id,txt_name from tbl_lib_main order by int_sort";
$res = mysqli_query($con, $sql) or die($sql);
  while($rec = mysqli_fetch_array($res)){
?>
	<option value="<?=$rec[int_id]?>" <?php  if($_REQUEST[type_id]==$rec[int_id]){?>selected="selected"<?php  }?>><?=$rec[txt_name]?></option>
<?php 
  }
?>
</select>
<span class="cssField">按顯示檔案名稱搜尋:</span> 
<input name="name" class="cssInput" id="fieldName"/>

<span class="cssField">指定分類:</span>
<select class="cssInput" id="fieldCat" name="shop">
	<option value="0">不指定</option>
<?php  
	foreach($aryC as $value){
		echo "<option value='$value[int_id]'>$value[txt_name]</option>";
	}
?>
</select>
		
<span class="cssField">指定分店:</span>
<select class="cssInput" id="fieldShop" name="shop">
	<option value="0">不指定</option>
<?php  
	foreach($aryS as $value){
		echo "<option value='$value[int_id]'>$value[shopName]</option>";
	}
?>
</select>

<input type="button" value="搜尋" style="width:100px;" onclick="changeClik(null)">
</div>

<?php  } ?>
</div>

<p>
<?php  
$sql = "SELECT count(*) FROM tbl_lib_pmd ";
  $sql .= "WHERE date_delete IN ('2000-01-01','2000-01-02') ";
  if($_REQUEST[type_id]) $sql.=" and group_id in (select int_id from tbl_lib_group where main_id =$_REQUEST[type_id])";
  if($_REQUEST[name]) $sql .= " and txt_name LIKE '%$_REQUEST[name]%'"; //mark
  if($_REQUEST[shop]) $sql .= " and int_shop = $_REQUEST[shop]"; //mark
  if($_REQUEST[cat]) $sql .= " and group_id = $_REQUEST[cat]"; //mark
  
  $sql .= " ORDER BY int_id DESC ";
  $result = mysqli_query($con, $sql) or die("invalid query");
  $record = mysqli_fetch_array($result);
  $countPage = ceil($record[0]/10);
if($countPage==0)
{
?>
<?php  if($_REQUEST[action]!='VIEW_ONLY'){ ?>
	<div align="center">
	  <p></p><p></p><p></p>
	  <h2><strong>沒有數據...</strong></h2>
	  <h2><a href="?type_id=<?=$_REQUEST[type_id]?>">重新搜尋</a></h2>
	</div>
<?php  } else{?>
	<div align="center" style="width:980px">
	  <p></p><p></p><p></p>
	  <h2><strong>沒有數據...</strong></h2>
	  <h2><a href="lib_list_cert.php">重新搜尋</a></h2>
	</div>
<?php  } ?>
<?php 
}else{
?>
<?php  if($_REQUEST[action]!='VIEW_ONLY'){ ?>
	<div align="center">
<?php  }else{ ?>
	<div style="padding:10px;margin-top:-50px;">
	<h2>牌照圖則、保養及證書</h2>
	<div class="cssSearch">
	
		<span class="cssField">按顯示檔案名稱搜尋:</span> 
		<input name="name" class="cssInput" id="fieldName"/>
		
		<span class="cssField">指定分類:</span>
		<select class="cssInput" id="fieldCat" name="shop">
			<option value="0">不指定</option>
		<?php  
			foreach($aryC as $value){
				echo "<option value='$value[int_id]'>$value[txt_name]</option>";
			}
		?>
		</select>
		
		<span class="cssField">指定分店:</span>
		<select class="cssInput" id="fieldShop" name="shop">
			<option value="0">不指定</option>
		<?php  
			foreach($aryS as $value){
				echo "<option value='$value[int_id]'>$value[shopName]</option>";
			}
		?>
		</select>
		
		<input type="button" value="搜尋" style="width:100px;" onclick="changeClik(null)">
	</div>
<?php  } ?>

<?php  if($_REQUEST[action]!='VIEW_ONLY'){ ?>
	<form  enctype= "multipart/form-data" name="form1" method="post"  action="">
	<table width="1164" height="65" border="1" cellpadding="0" cellspacing="0" style="text-align:center">
	  <tr>
		<td width="100"><strong>大類</strong></td>
<?php  }else{ ?>
	<form  enctype= "multipart/form-data" name="form1" method="post"  action="">
	<table width="980" height="65" border="1" cellpadding="0" cellspacing="0" style="text-align:center">
	<tr>
<?php  } ?>	
	
		
    <td width="180"><strong>所屬分類</strong></td>
    <td width="85"><strong>建立者</strong></td>
    <td width="214"><strong>顯示檔案名稱</strong></td>
    <td width="231"><strong>顯示檔案地址</strong></td>
	<td width="445"><strong>原始檔案名稱</strong></td>
<!--	<td width="231"><strong>原始檔案地址</strong></td>-->
    <td width="100"><strong>分店可視<br>(指定)</strong></td>
	<?php  if($_REQUEST[action]!='VIEW_ONLY'){ ?>
    <td width="78"><strong>分店可視<br> (所有)</strong></td>
    <td width="83"><strong>後勤可視<br> (所有)</strong></td>
	<td width="100"><strong>部門專屬<br>(指定)</strong></td>
    <td width="42"><strong>修改</strong></td>
    <td width="48"><strong>刪除</strong></td>
	<?php  } ?>
  </tr>
  <?php 
  $sql = "select pmd.int_id as pmd_id,pmd.txt_name as p_name,pmd.txt_path as p_path,pmd.txt_ori_doc as p_orifile,pmd.txt_ori_url as p_oriurl,gp.txt_name as g_name,gp.int_id as gp_id,mn.int_id as mn_id,
  		mn.txt_name as m_name ,pmd.state_cent as cent,pmd.state_logs as logs,pmd.state_dept as depts,pmd.int_shop as shop,pmd.author as author,group_id,status
		from tbl_lib_pmd as pmd 
		left join tbl_lib_group as gp on gp.int_id = pmd.group_id 
		left join tbl_lib_main as mn on mn.int_id = gp.main_id 
		where date_delete = '2000-01-01'";
		
  if($_REQUEST[type_id]) $sql.=" and mn.int_id = $_REQUEST[type_id]";
  if($_REQUEST[name]) $sql.=" and pmd.txt_name LIKE '%$_REQUEST[name]%'"; //mark
  if($_REQUEST[shop]) $sql.=" and pmd.int_shop = $_REQUEST[shop]";
  if($_REQUEST[cat]) $sql .= " and group_id = $_REQUEST[cat]";
  
  $sql.=" order by mn.int_sort,gp.int_sort ";
  if ($_REQUEST[pageno] == "") {
    $page = 1;
  } else {
    $page = $_REQUEST[pageno];
  }
  $sql .= "LIMIT " . ($page-1) * 10 . ", 10";
  $res = mysqli_query($con, $sql) or die($sql);
  while($rec = mysqli_fetch_array($res)){
  ?>
  <tr onMouseOver="this.style.background='#AFD1DD'" onMouseOut="this.style.background=''">
  <?php  if($_REQUEST[action]!='VIEW_ONLY'){ ?>
    <td width="100" height="38"  >
     <div id="m_name1<?=$rec[pmd_id]?>" style="display:block"><?=$rec[m_name]?>&nbsp;</div>
    <div id="m_name2<?=$rec[pmd_id]?>" style="display:none" >
    <select name="select_main<?=$rec[pmd_id]?>" id="select_main<?=$rec[pmd_id]?>" onChange="Change_main(this.value,<?=$rec[pmd_id]?>)" style='width:90px;'>
        <?php 
		 $sql2 = "select  int_id,txt_name,status from tbl_lib_main";
	 	 $res2 = mysqli_query($con, $sql2) or die($sql2);
		 while($rec2=mysqli_fetch_array($res2))
		 {
		?>
            <option value="<?php  echo $rec2["int_id"].".".$rec2["status"]?>"<?php  if($rec[mn_id]==$rec2[int_id]) echo "selected"; ?>> 
			<?php  echo $rec2["txt_name"]?> </option>
         <?php 
		 }
         ?>
    </select>
    </div>
  </td> <?php  } ?>
    <td width="180" height="38"  >
    <div id="g_name1<?=$rec[pmd_id]?>" style="display:block"><?=$rec[g_name]?>&nbsp;</div>
    <div id="g_name2<?=$rec[pmd_id]?>" style="display:none" >
    <?php 
    	if($rec[mn_id]!="")
		{
	?>
    <select name="select_group<?=$rec[pmd_id]?>" id="select_group<?=$rec[pmd_id]?>" style='width:100px;'>
    <?php 
		 $sql2 = "select  int_id,txt_name from tbl_lib_group where main_id= ".$rec[mn_id];
	 	 $res2 = mysqli_query($con, $sql2) or die($sql2);
		 while($rec2=mysqli_fetch_array($res2))
		 {
		?>
            <option value="<?php  echo $rec2["int_id"]?>" <?php  if($rec["group_id"]==$rec2["int_id"]) echo "selected"; ?>> <?php  echo $rec2["txt_name"]?> </option>
         <?php 
		 }
         ?>
    </select>
    <?php 
		}else{
			echo "<font size=\"-1\" color=\"red\">請添加分類</font>";
		}
	?>
    </div>
</td>
    <td width="85">
    <div id="author<?=$rec[pmd_id]?>" style="display:block"><?=$rec[author]?>&nbsp;</div>
    <!--
    <div id="author2" style="display:none" >
    <input type="text" id="author" name="author" size="10" value=""></div>
    -->
</td>
    <td width="214">    
    <div id="name1<?=$rec[pmd_id]?>" style="display:block"><?=$rec[p_name]?>&nbsp;</div>
    <div id="name2<?=$rec[pmd_id]?>" style="display:none" >
    <input type="text" id="txt_name<?=$rec[pmd_id]?>" name="txt_name<?=$rec[pmd_id]?>" size="20" value="<?=$rec[p_name]?>"></div>
</td>
    <td width="231">
   	<div id="path1<?=$rec[pmd_id]?>" style="display:block"><a target="_new" href="<?=$rec[p_path]?>"><?=$rec[p_path]?></a>&nbsp;</div>
    <div id="path2<?=$rec[pmd_id]?>" style="display:none" >
    <input type="file" size="18" id="txt_path<?=$rec[pmd_id]?>" name="txt_path<?=$rec[pmd_id]?>" value="<?=$rec[p_path]?>"></div>
</td>
	<td width="445">    
    <div id="oriname1<?=$rec[pmd_id]?>" style="display:block"><?=$rec[p_orifile]?>&nbsp;</div>
    <div id="oriname2<?=$rec[pmd_id]?>" style="display:none" >
    <input type="text" id="txt_oriname<?=$rec[pmd_id]?>" name="txt_oriname<?=$rec[pmd_id]?>" size="20" value="<?=$rec[p_orifile]?>"></div>
</td>
<!--
	<td width="231">
   	<div id="url1<?=$rec[pmd_id]?>" style="display:block"><?=$rec[p_oriurl]?>&nbsp;</div>
    <div id="url2<?=$rec[pmd_id]?>" style="display:none" >
    <input type="file" size="18" id="txt_oriurl<?=$rec[pmd_id]?>" name="txt_url<?=$rec[pmd_id]?>" value="<?=$rec[p_oriurl]?>"></div>
</td>
-->
	<td width="100">
   	<div id="shop1<?=$rec[pmd_id]?>" style="display:block">
    <?php 
		 $sql_shop = "select  int_id,txt_name from tbl_user where chr_type=2";
	 	 $res_shop = mysqli_query($con, $sql_shop) or die($sql_shop);
		 while($rec_shop=mysqli_fetch_array($res_shop))
		 {
			 if($rec_shop[int_id]==$rec[shop]) echo $rec_shop["txt_name"];
		 }
	?>
    &nbsp;
    </div>
    <div id="shop2<?=$rec[pmd_id]?>" style="display:none">
    	<select name="select_shop<?=$rec[pmd_id]?>" id="select_shop<?=$rec[pmd_id]?>" style='width:100px;'>
        <option value="0" <?php  if($rec_shop[int_id]==$rec[shop]){$str .= "selected='selected'>";} ?>>請選擇(可選)</option>
        <?php 
		 $sql_shop = "select  int_id,txt_name from tbl_user where chr_type=2";
	 	 $res_shop = mysqli_query($con, $sql_shop) or die($sql_shop);
		 while($rec_shop=mysqli_fetch_array($res_shop))
		 {
		?>
            <option value="<?php  echo $rec_shop["int_id"] ?>" <?php  if($rec_shop[int_id]==$rec[shop]){ echo "selected='selected'";} ?>> <?php  echo $rec_shop["txt_name"]?> </option>
         <?php 
		 }
         ?>
    	</select>
    </div>
</td>
<?php  if($_REQUEST[action]!='VIEW_ONLY'){ ?>
    <td width="78" align="center" valign="middle">
    <div id="cent1<?=$rec[pmd_id]?>" style="display:block"><?php  if($rec[cent]=="1") echo "√";?>&nbsp;</div>
    <div id="cent2<?=$rec[pmd_id]?>" style="display:none" >
    <input name="cent<?=$rec[pmd_id]?>" type="checkbox" id="cent<?=$rec[pmd_id]?>" <?php  if($rec[cent]==1) echo "checked"; ?>>
    </div>
</td>
    
    <td width="83" align="center" valign="middle">
    <div id="logs1<?=$rec[pmd_id]?>" style="display:block"><?php  if($rec[logs]=="1") echo "√";?>&nbsp;</div>
    <div id="logs2<?=$rec[pmd_id]?>" style="display:none" >
    <input name="logs<?=$rec[pmd_id]?>" type="checkbox" id="logs<?=$rec[pmd_id]?>"  <?php  if($rec[logs]==1) echo "checked"; ?>>
    </div>
</td>
<td width="83" align="center" valign="middle">
    <div id="dept1<?=$rec[pmd_id]?>" style="display:block"><?php  if($rec[depts]=="1") echo "√";?>&nbsp;</div>
    <div id="dept2<?=$rec[pmd_id]?>" style="display:none" >
    <input name="dept<?=$rec[pmd_id]?>" type="checkbox" id="dept<?=$rec[pmd_id]?>" onClick="select_check(<?=$rec[pmd_id]?>)" <?php  if($rec[depts]==1) echo "checked"; ?>>
    </div>
</td>
    <td width="42">
    <input type="button" id ="but_save<?=$rec[pmd_id]?>" style=" display:none" value="保存" onClick="save_lib(<?=$rec[pmd_id]?>,<?=$rec[status]?>)">
    <input type="button" id ="but_update<?=$rec[pmd_id]?>" value="修改" onClick="update_lib(<?=$rec[pmd_id]?>,<?=$rec[status]?>)">
</td>
    <td width="48">
    <input type="button" id ="but_res<?=$rec[pmd_id]?>" style=" display:none" value="取消" onClick="res_lib(<?=$rec[pmd_id]?>)">
    <input type="button" id ="but_del<?=$rec[pmd_id]?>" value="刪除" onClick="delete_lib(<?=$rec[pmd_id]?>)">
</td>
<?php  } ?>
  </tr>
  <?php 
  }
  ?>
</table>
</form>
<table width="900" border="0" cellspacing="3" cellpadding="3">
          <tr>
            <td width="100%" height="24" align="right">
  <?php 
  echo "第".$page."頁&nbsp;\&nbsp;共".$countPage."頁&nbsp;&nbsp;&nbsp;&nbsp;";
  if ($page != 1){
    echo "<a href=\"?pageno=" . ($page-1) . "&type_id={$_REQUEST['type_id']}" . "&name={$pre_name}". "&shop={$pre_shop}" . "&cat={$pre_cat}" ."\">上一頁</a>"; //mark
  }else{
	echo "上一頁";
  }
  echo "&nbsp;&nbsp;";
  if ($page<$countPage) {
    echo "<a href=\"?pageno=" . ($page+1) . "&type_id={$_REQUEST['type_id']}" . "&name={$pre_name}". "&shop={$pre_shop}" . "&cat={$pre_cat}" ."\">下一頁</a>"; //mark
  }else{
	echo "下一頁";
  }
?>
</td>
          </tr>
        </table>
</div>
<?php  } ?>
<?php  if($_REQUEST[action]=='VIEW_ONLY'){ ?>
<div style="text-align:center; width:980px;">
	<a href="library.php" class="style2">返回</a>
</div>
<?php  } ?>
</body>
</html>