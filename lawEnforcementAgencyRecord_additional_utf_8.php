<?
  session_start();
  if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'lawEnforcementAgencyRecord_main.php';
    header('Location: login.php');
  }
  require("connect.inc");
  //require("phpMailer/class.phpmailer.php");
  $timestamp = gettimeofday("sec")+28800;
/*  $_SESSION['user_id']=2;
   $_SESSION[dept]=19;*/
  $show_Entry_Months = 3;

  if(isset($_POST['submit'])) {
	$submit = $_POST['submit'];
		//echo $submit;
	if($submit == "更新"){
  		foreach($_FILES["uploadfile"]["error"] as $key => $error){
  			if($error == UPLOAD_ERR_OK) {
	  			$filename=$_FILES["uploadfile"]['tmp_name'][$key];
	  			$extension=end(explode(".", $_FILES["uploadfile"]['name'][$key]));
	  			$newfilename=$_SESSION['user_id'].'-'.date('ymdHis',$timestamp).'-'.($key+1).'.'.$extension;
	  		  	move_uploaded_file($filename, "./LawEnforcementRecords/" .$newfilename);
	  		  	$upName[0][$key] = $_FILES["uploadfile"]['name'][$key];
	  		  	$upName[1][$key] = $newfilename;
				//echo $newfilename.'文件';
  			}
  		}		
		$updateID = $_POST['updateID'];
		
  		$sql_update  = "UPDATE tbl_law_enforcement_dept_detail SET  date_updateDate = DATE(NOW()) ";
		if($_POST['ptDate']<>""){
			$sql_update.=" ,date_penalty_ticket = '".$_POST['ptDate']."'";
		}
		if($_POST['ptNum']<>""){
			$sql_update.=", chr_penalty_no = '".$_POST['ptNum']."'";
		}
		if($_POST['pAmt']<>""){
			$sql_update.=", decimal_penalty_amount = '".$_POST['pAmt']."'";
		}
		if(trim($_POST['update_counter'])<>""){
			$sql_update.=", bool_counter=".$_POST["update_counter"];
		}
		if(trim($_POST['update_end'])<>""){
			$sql_update.=", bool_end=".$_POST["update_end"]."";
		}else{
			$sql_update.=",int_follow_user = ".$_SESSION['user_id'].", int_followdept=".$_SESSION[dept]."";
		}
		
		
		if($_POST['textarea']<>""){
			$sql_update.=", chr_followcont='".$_POST["textarea"]."'";
		}
		if($upName[1][0]<>""){
			$sql_update.=", chr_file_name_1='".$upName[1][0]."'";
		}
		if($upName[1][1]<>""){
			$sql_update.=", chr_file_name_2='".$upName[1][1]."'";
		}
		if($upName[1][2]<>""){
			$sql_update.=", chr_file_name_3='".$upName[1][2]."'";
		}
		if($upName[1][3]<>""){
			$sql_update.=", chr_file_name_4='".$upName[1][3]."'";
		}
		if($upName[0][0]<>""){
			$sql_update.=", chr_old_file_name_1='".$upName[0][0]."'";
		}
		if($upName[0][1]<>""){
			$sql_update.=", chr_old_file_name_2='".$upName[0][1]."'";
		}
		if($upName[0][2]<>""){
			$sql_update.=", chr_old_file_name_3='".$upName[0][2]."'";
		}
		if($upName[0][3]<>""){
			$sql_update.=", chr_old_file_name_4='".$upName[0][3]."'";
		}
		
  		$sql_update  .= "  WHERE int_id = ".$updateID;
		/*', chr_penalty_no = '".$_POST['ptNum']."', decimal_penalty_amount = '".$_POST['pAmt']."' , bool_counter=".$_POST["update_counter"].", bool_end=".$_POST["update_end"].", int_followdept=".$_POST["sle_followdept"].", chr_followcont='".$_POST["textarea"]."'*/

  		$update_Result = mysqli_query($con, $sql_update) or die($sql_update);
  		if($update_Result > 0) {
			if($_POST['updateReply'] == 1)
			{ 
			}
			?><script language="javascript">top.location.href = "lawEnforcementAgencyRecord_main.php"</script><?
		}
  	}
  }
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<!-- <title>執法部門巡查報告</title> -->
<title>內聯網</title>
	<style type="text/css">
	body,td,th {
	font-size: 15px;
	}
	td{

	}


    </style>

<script type="text/javascript" src="LawEnforcementRecords/js/fbw-ajax.js"></script>
<script type="text/javascript" src="js/overlib.js"></script>
<script src="calendar/calendar2.js"></script>
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/themes/hot-sneaks/jquery-ui.css" rel="stylesheet">
<script type='text/javascript' src="training/js/jquery-ui-timepicker-addon.js"></script>
<script type='text/javascript' src='LawEnforcementRecords/js/jquery-ui-sliderAccess.js'></script>
<link rel="stylesheet" type="text/css" href="LawEnforcementRecords/css/jquery-ui-timepicker-addon.css">
<script type="text/javascript">

		$(document).ready(function() {
			//timepicker
			$.timepicker.regional['zh-TW'] = {
					timeOnlyTitle: '選擇時間',
					timeText: '時間',
					hourText: '小時',
					minuteText: '分鐘',
					secondText: '秒鐘',
					millisecText: '微秒',
					timezoneText: '時區',
					currentText: '現在時間',
					closeText: '確定',
					closeText: '關閉',
					timeFormat: 'HH:mm',
					amNames: ['AM', 'A'],
					pmNames: ['PM', 'P'],
					ampm: false
				};
			$.timepicker.setDefaults( $.timepicker.regional[ "zh-TW" ] );

			var opt={
						showSecond: false,
						timeFormat: 'HH:mm'
//							,
//						addSliderAccess:true,
//						sliderAccessArgs:{touchonly:false}
					};
			$('#enf_Time').timepicker(opt);
			
		});

		function isNumberKey(evt){
		    var charCode = (evt.which) ? evt.which : event.keyCode
			
		    if ((charCode > 31 && (charCode != 46 &&(charCode < 48 || charCode > 57)))
		    	|| (charCode == 46 && (document.getElementById('pAmt').value.indexOf(".") > -1 || document.getElementById('pAmt').value == '')))
		        return false;
		    return true;
		}

		function upFile(btnId)
		{
			//document.getElementById('file'+btnId).click();
			
			if(document.getElementById('file'+btnId).value != '')
				document.getElementById('btnFile'+btnId).style.backgroundColor="#00FF00";

			return false;
		}
		
		function clearFileInputField(tagId) {
		    document.getElementById('div_file'+tagId).innerHTML = 
		                    document.getElementById('div_file'+tagId).innerHTML;
		    document.getElementById('btnFile'+tagId).style.backgroundColor="";
		}

		
		

 		function updateButtonClick(o)
  		{
  	  		if(document.getElementById('ptNum'+o.id).value == '' || document.getElementById('pAmt'+o.id).value == '') {
  	  			document.getElementById('updateCheck').value = 0;
  	  			alert('請輸入「傳票/報告號碼」及「罰款金額」!');
  	  		} else {
	  			document.getElementById('updateID').value = o.id;
	  			document.getElementById('update_ptDate').value = document.getElementById('ptDate'+o.id).value;
	  			document.getElementById('update_ptNum').value  = document.getElementById('ptNum'+o.id).value;
	  			document.getElementById('update_pAmt').value   = document.getElementById('pAmt'+o.id).value;

	  			document.getElementById('updateCheck').value = 1;
  	  		}  	  		
  		}
  		
		function confUpdateSubmit() {
	/*	if(document.getElementById('ptNum').value == '' || document.getElementById('pAmt').value == '') {
  	  			document.getElementById('updateCheck').value = 0;
  	  			alert('請輸入「傳票/報告號碼」及「罰款金額」!');
  	  		}*/
			document.getElementById('update_end').value = document.getElementById('cb_end').checked?1:0;
		    document.getElementById('update_counter').value = document.getElementById('cb_counter').checked?1:0;
			document.getElementById('updateCheck').value = 1;
			return document.getElementById('updateCheck').value == 1?true:false;
		}
    </script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
}
-->
</style></head>
<body>
<form name="form_CaseUpdate" id="form" enctype="multipart/form-data" action="lawEnforcementAgencyRecord_additional_utf_8.php?id=<?=$_GET["id"]?>" method="post" onSubmit="return confUpdateSubmit();">

<?
	$sql_Notcomplete  = "SELECT e.int_id, DATE(e.datetime_input_date) AS 'datetime_input_date', e.chr_enforce_case_num, e.chr_shopStaff";
	$sql_Notcomplete .= ", u.txt_name AS branchName, u.chr_ename AS branchCode, e.chr_enforce_dept, e.chr_enforce_time, e.chr_enforce_risk_level, e.chr_enforce_risk_photo";
	$sql_Notcomplete .= ", chr_enforce_detail, e.chr_remarks, e.chr_file_name_1, e.chr_file_name_2, e.chr_file_name_3, e.chr_enforce_risk_measure";
	$sql_Notcomplete .= ", e.chr_file_name_4, e.chr_old_file_name_1, e.chr_old_file_name_2, e.chr_old_file_name_3, e.chr_old_file_name_4";
	$sql_Notcomplete .= ", f.chr_ename AS 'follower', e.date_penalty_ticket, e.chr_penalty_no, e.decimal_penalty_amount, e.chr_enforce_risk_readLicense,e.bool_counter,e.chr_followcont,e.int_followdept,e.bool_end,d.txt_dept ,f2.chr_ename as follow_user";
	$sql_Notcomplete .= " FROM tbl_law_enforcement_dept_detail e LEFT JOIN tbl_user u ON e.int_branch = u.int_id";
	$sql_Notcomplete .= " LEFT JOIN tbl_user f ON e.int_followed_by = f.int_id ";
	$sql_Notcomplete .= " LEFT JOIN tbl_dept d ON d.int_id =e.int_followdept ";
	$sql_Notcomplete .= " LEFT JOIN tbl_user f2 ON e.int_follow_user = f2.int_id ";
	$sql_Notcomplete .= " WHERE  e.int_id=".$_GET["id"]."";
	//(DATE_ADD(DATE(e.datetime_input_date), INTERVAL ".$show_Entry_Months." MONTH) >= DATE(NOW())) and
/*	if($_SESSION[dept] == 2) {
		$sql_Notcomplete .= " AND e.int_branch = ".$_SESSION['user_id'];
	}*/
	$sql_Notcomplete .= " ORDER BY e.chr_enforce_case_num;";
	$result_Notcomplete = mysqli_query($con, $sql_Notcomplete) or die($sql_Notcomplete);
	
	
    //$sql_UserTitle .= "  select count(int_id) from  tbl_dis_group where int_manager =".$_SESSION['user_id'];
	//$result_UserTitle = mysqli_query($con, $sql_UserTitle)  or die("invalid query");
	//$record_UserTitle=mysqli_fetch_array($result_UserTitle);
	$record_UserTitle=Array(1);

	
	$recordCount = 1;

	if (mysqli_num_rows($result_Notcomplete) == 0) {echo "<center><font size=5 color=red>沒有紀錄 !!</font></center>";} else {
?>

<table width="100%" border="0" cellspacing="0" cellpadding="3">

<?

		$canEdit = ($_SESSION[dept] == 6 || $_SESSION[dept] == 19 || $_SESSION['user_id'] == 122) ? true : false;
		//$canEdit=true;
	}
	$bgcolor = "#CCFFFF";
?>
<?
	while($record_Notcomplete = mysqli_fetch_array($result_Notcomplete)) {
//		if($recordCount > 1) { echo "<tr><td width=\"100%\" colspan=\"10\" bgcolor=\"#FFFF00\">&nbsp;</td></tr>";}
		$bgcolor = ($recordCount%2==0) ? "#DDDDDD" : "";// "#CCFFFF";
?>
	<input  type="hidden" id="updateID" name="updateID" value="<?=$record_Notcomplete[int_id]?>" />
	<input type="hidden" id="update_ptDate" name="update_ptDate" value="<?=$record_Notcomplete[date_penalty_ticket]?>" />
	<input type="hidden" id="update_ptNum" name="update_ptNum" value="<?=$record_Notcomplete[chr_penalty_no]?>" />
	<input type="hidden" id="update_pAmt" name="update_pAmt" value="<?=$record_Notcomplete[decimal_penalty_amount]?>" />
	<input type="hidden" id="updateCheck" name="updateCheck" value="1" />
	
	<input type="hidden" id="update_counter" name="update_counter" value="<?=ord($record_Notcomplete[bool_counter])?>" />
	<input type="hidden" id="update_end" name="update_end" value="<?=ord($record_Notcomplete[bool_end])?>" />
	
	
<tr>
<th width="40%" align="right"><b>巡查編號</b></th>
<td ><?=$record_Notcomplete[chr_enforce_case_num]?></td>
</tr>
<tr>
  <th align="right"><b>巡查日期</b></th>
  <td><?=$record_Notcomplete[datetime_input_date].' ('.$record_Notcomplete[chr_enforce_time].')';?></td>
</tr>
<tr>
  <th align="right"><b>分店</b></th>
  <td><?=$record_Notcomplete[branchName]?></td>
</tr>
<tr>
  <th align="right"><b>執法部門</b></th>
  <td><?=$record_Notcomplete[chr_enforce_dept]?></td>
</tr>
<tr>
  <th align="right"  style="border-top:1px solid #ccc;"><b>傳票輸入同事</b></th>
  <td  style="border-top:1px solid #ccc;"><?echo ($record_Notcomplete[follower]==''?"&nbsp;":$record_Notcomplete[follower]);?></td>
</tr>
<tr>
  <th align="right"><b>傳票/報告日期</b></th>
  <td><?
				if($record_Notcomplete[follower]==''&& $canEdit) {
					echo "<input type=\"text\" id=\"ptDate\" name = \"ptDate\" value=\"".date('Y-m-d',$timestamp);
					echo "\" onFocus=\"this.select();lcs(this)\" onClick=\"event.cancelBubble=true;this.select();lcs(this)\" size=\"8\" READONLY />";
	    		} else {
					echo $record_Notcomplete[date_penalty_ticket] == "2999-01-01" ? "&nbsp;" : $record_Notcomplete[date_penalty_ticket];
	        			}
	        ?></td>
</tr>
<tr>
  <th align="right"><b>傳票/報告號碼</b></th>
  <td><?
				if($record_Notcomplete[follower]=='' && $canEdit) {
					echo "<input type=\"text\" id=\"ptNum\" name = \"ptNum\" size=\"12\" />";
				} else {
					echo $record_Notcomplete[chr_penalty_no] == '' ? "&nbsp;" : $record_Notcomplete[chr_penalty_no];
				}
			?></td>
</tr>
<tr>
  <th align="right"><b>罰款$</b></th>
  <td><?
				if($record_Notcomplete[follower]=='' && $canEdit) {
					echo "<input type=\"number\" id=\"pAmt\" name = \"pAmt\" size=\"6\" maxlength=\"6\" onkeypress=\"return isNumberKey(event)\" />";
				} else {
					echo $record_Notcomplete[decimal_penalty_amount] == '' ? "&nbsp;" : $record_Notcomplete[decimal_penalty_amount];
				}
			?></td>
</tr>

<tr>
  <th align="right"  style="border-top:1px solid #ccc;">後勤跟進部門</th>
  <td  style="border-top:1px solid #ccc;">
  <?=$record_Notcomplete[txt_dept]."-".$record_Notcomplete[follow_user]?>  </td>
</tr>
<tr>
  <th align="right">跟進內容</th>
  <td><label>
    
	<?
				if($record_Notcomplete[chr_followcont]=='' && $canEdit && $record_Notcomplete[bool_end]=='') {
					echo "<textarea name=\"textarea\"></textarea>";
				} else {
					echo $record_Notcomplete[chr_followcont] == '' ? "&nbsp;" : $record_Notcomplete[chr_followcont];
				}
			?>
  </label></td>
</tr>
<tr>
  <th align="right"  style="border-top:1px solid #ccc;">上傳:</th>
  <td><table width="200xp"  style="border-top:1px solid #ccc;">
        <tr>
        <?
          if(TRIM($record_Notcomplete[chr_file_name_1]) != '') {
		?>
          <td align="right">1:</td>
          <td align="left"><a href="LawEnforcementRecords/<?=$record_Notcomplete[chr_file_name_1];?>" target="_blank">
        <?
//							=$record_Notcomplete[chr_old_file_name_1]."</a></td>";}
          echo "附件1</a></td>";}
		?>
		    <td align="right"  <?=TRIM($record_Notcomplete[chr_file_name_1]) != ''?'style="display: none"':' '?>  >1:</td>
          <td align="left" <?=TRIM($record_Notcomplete[chr_file_name_1]) != ''?'style="display: none"':''?> >            <div id="div_file1" style="float:left">
              <input id="file1" name="uploadfile[]" type="file"  onChange="upFile('1');" style="position:absolute;filter:alpha(opacity=0);  -moz-opacity:0;         /*火狐*/opacity:0;  width:105px;"  />
              <input type="button" id="btnFile1" name="btnFile1"   style="width:105" value="特別事項報告" />
            </div><div style="float:left"><input type="button" id="clear1" name="clear1" onClick="clearFileInputField('1');" value="清除" /></div></td>
	
        </tr>
        <tr>
        <?
          if(TRIM($record_Notcomplete[chr_file_name_2]) != '') {
		?>
          <td align="right">2:</td>
          <td align="left"><a href="LawEnforcementRecords/<?=$record_Notcomplete[chr_file_name_2];?>" target="_blank">
        <?
//							=$record_Notcomplete[chr_old_file_name_2]."</a></td>";}
          echo "附件2</a></td>";}
		?>
				    <td align="right" <?=TRIM($record_Notcomplete[chr_file_name_2]) != ''?'style="display: none"':''?> >2:</td>
          <td align="left" <?=TRIM($record_Notcomplete[chr_file_name_2]) != ''?'style="display: none"':''?> >            <div id="div_file2"  style="float:left">
              <input id="file2" name="uploadfile[]" type="file"  onChange="upFile('2');" style="position:absolute;filter:alpha(opacity=0);  -moz-opacity:0;         /*火狐*/opacity:0;  width:105px;"  />
              <input type="button" id="btnFile2" name="btnFile2"   style="width:105" value="相片1" />
            </div><div  style="float:left"><input type="button" id="clear2" name="clear2" onClick="clearFileInputField('2');" value="清除" /></div></td>
	
        </tr>
        <tr>
        <?
          if(TRIM($record_Notcomplete[chr_file_name_3]) != '') {
		?>
          <td align="right">3:</td>
          <td align="left"><a href="LawEnforcementRecords/<?=$record_Notcomplete[chr_file_name_3];?>" target="_blank">
        <?
//							=$record_Notcomplete[chr_old_file_name_3]."</a></td>";}
          echo "附件3</a></td>";}
		?>
				    <td align="right" <?=TRIM($record_Notcomplete[chr_file_name_3]) != ''?'style="display: none"':''?> >3:</td>
          <td align="left" <?=TRIM($record_Notcomplete[chr_file_name_3]) != ''?'style="display: none"':''?> >            <div id="div_file3" style="float:left">
              <input id="file3" name="uploadfile[]" type="file"  onChange="upFile('3');" style="position:absolute;filter:alpha(opacity=0);  -moz-opacity:0;         /*火狐*/opacity:0;  width:105px;"  />
              <input type="button" id="btnFile3" name="btnFile3"   style="width:105" value="相片2" />
            </div><div style="float:left"><input type="button" id="clear3" name="clear3" onClick="clearFileInputField('3');" value="清除" /></div></td>
        </tr>
        <tr>
        <?
          if(TRIM($record_Notcomplete[chr_file_name_4]) != '') {
		?>
          <td align="right">4:</td>
          <td align="left"><a href="LawEnforcementRecords/<?=$record_Notcomplete[chr_file_name_4];?>" target="_blank">
        <?
//							=$record_Notcomplete[chr_old_file_name_4]."</a></td>";}
          echo "附件4</a></td>";}
		?>
				    <td align="right" <?=TRIM($record_Notcomplete[chr_file_name_4]) != ''?'style="display: none"':''?> >4:</td>
          <td align="left" <?=TRIM($record_Notcomplete[chr_file_name_4]) != ''?'style="display: none"':''?> >            <div id="div_file4" style="float:left">
              <input id="file4" name="uploadfile[]" type="file"  onChange="upFile('4');" style="position:absolute;filter:alpha(opacity=0);  -moz-opacity:0;         /*火狐*/opacity:0;  width:105px;"  />
              <input type="button" id="btnFile4" name="btnFile4"   style="width:105" value="相片3" />
            </div><div style="float:left"><input type="button" id="clear4" name="clear4" onClick="clearFileInputField('4');" value="清除" /></div></td>
        </tr>
      </table></td>
</tr>
<tr >
  <th align="right" style="border-top:1px solid #ccc;">區域經理已閱</th>
  <td  style="border-top:1px solid #ccc;">
 
<?php
					if($record_UserTitle[0]>0) {
						echo ord($record_Notcomplete[bool_counter]) == '1' ? "是" :" <input name=\"cb_counter\" id='cb_counter' type=\"checkbox\" value=\"\">";
					} else {
					    echo ord($record_Notcomplete[bool_counter]) == '1' ? "是" :" 否";
					}
?> </td>
</tr>
  <?php if($record_UserTitle[0]>0) {?>
<tr>
  <th align="right">案件完結</th>
  <td>
  <?
  if(ord($record_Notcomplete["bool_end"])==1){
  echo "是";
  }else{
?>
<input name="cb_end" id="cb_end" type="checkbox" <?=ord($record_Notcomplete["bool_end"])==1 ?"checked":""?> value="">
<?
}
?>
  </td>
</tr>
<? }else{ ?>
<tr>
  <th align="right">案件完結</th>
  <td><?=ord($record_Notcomplete[bool_end])==1?"是":"否"?></td>
</tr>
<? }?>
<? if(($canEdit&&ord($record_Notcomplete[bool_end])!=1)||($record_UserTitle[0]>0 &&ord($record_Notcomplete[bool_end])!=1)){?>
<tr>
  <th align="right">&nbsp;</th>
  <td><input type="submit" name="submit"   value="更新"></td>
</tr>
<? }?>
<? $recordCount ++;}?>
</table>
</form>
</body>
</html>