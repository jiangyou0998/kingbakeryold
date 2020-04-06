<?
  session_start();
  if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'lawEnforcementAgencyRecord_rpt.php';
    header('Location: login.php');
  }
  require($DOCUMENT_ROOT . "./pconnect.inc");

  $timestamp = gettimeofday("sec")+28800;
  $currYear = date("Y", $timestamp);
  $currMth = date("m", $timestamp);
  
  $show_Entry_Months = 3;
  
  $riskLvl = $_POST['riskLvl'];
  $riskLvl = $riskLvl == '' ? "4,3,2,1" : $riskLvl;
  
  $rptYear = $_POST['rptYear'];
  $rptYear = $rptYear == '' ? $currYear : $rptYear;
  //print_r($_POST);
  //die();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<!-- <title>執法部門巡查紀錄</title> -->
<title>內聯網</title>
	<style type="text/css">
		body,td,th {
			font-size: small;
		}	
    </style>
	
	<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	
	<script type='text/javascript' src='LawEnforcementRecords/js/easyui/jquery.easyui.min.js'></script>
	<script type='text/javascript' src="js/MultipleSelect/multiple-select.js"></script>
	
	
	<link rel="stylesheet" type="text/css" href="LawEnforcementRecords/js/easyui/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="LawEnforcementRecords/js/easyui/themes/icon.css">
	<link rel="stylesheet" type="text/css" href="js/MultipleSelect/multiple-select.css">

	
	
	<script type="text/javascript">
	
		$(function(){
			$("#riskLvl").multipleSelect({
				selectAllText: '所有',
				allSelected: '所有',
				onClose: function(){
					$("#riskLvl_post").val($("#riskLvl").multipleSelect('getSelects').join(','));
				}
			});
			$("#riskLvl").multipleSelect('setSelects', [<?=$riskLvl?>] );			
			
			$("#rptYear").multipleSelect({
				selectAllText: '所有',
				allSelected: '所有',
				onClose: function(){
					$("#rptYear_post").val($("#rptYear").multipleSelect('getSelects').join(','));
				}
			});
			$("#rptYear").multipleSelect('setSelects', [<?=$rptYear?>] );
		});
		
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
<? include("head.php"); ?>

<form enctype="multipart/form-data" name="form_EnforcementRpt" id="form" action="lawEnforcementAgencyRecord_rpt.php" method="post">

<div align="center" style="width:994;padding:10px;">
  <h1><u>執法部門巡查報告</u></h1>
</div>

<?

	
	$rptMonth = $_POST['rptMonth'];
	$rptMonth = $rptMonth == '' ? $currMth : $rptMonth;
	
	$enfDept = $_POST['enforceDept'];
	$enfDept = $enfDept == '' ? "A" : $enfDept;
	
	$branch = $_POST['branch'];
	$branch = $branch == '' ? "A" : $branch;
?>

<div align="center" style="width:994;">
  			<!--
			巡查年份
  			<select id="rptYear" name="rptYear">
  				<option value="A" <?=$_POST['rptYear']=="A"?" selected":"";?>>所有</option>
  				<?
  					for($count=0; $count<5; $count++) {
  						echo "<option value=\"".($currYear - $count)."\"".(($currYear - $count) == $rptYear && $rptYear <> "A" ? " selected" : "").">".($currYear - $count)."</option>";
  					}
  				?>
  			</select>
			-->
			巡查年份
  			<select id="rptYear" name="rptYear" style="width:85px;">
  				<?
  					for($count=0; $count<5; $count++) {
  						echo "<option value=\"".($currYear - $count)."\"".(($currYear - $count) == $rptYear && $rptYear <> "A" ? " selected" : "").">".($currYear - $count)."</option>";
  					}
  				?>
				<input type="hidden" id="rptYear_post" name="rptYear" value="<?=$rptYear?>" />
  			</select>
  			&nbsp;&nbsp;&nbsp;
  			巡查月份
  			<select id="rptMonth" name="rptMonth">
  				<option value="A" <?=$_POST['rptMonth']=="A"?" selected":"";?>>所有</option>
  				<?
  					for($count=1; $count<=12; $count++) {
  						echo "<option value=\"".(strlen((string)$count)==1?"0".(string)$count:$count)."\""
    							.(((strlen((string)$count)==1?"0".(string)$count:$count)) == $rptMonth ? " selected" : "")
  								.">".(strlen((string)$count)==1?"0".(string)$count:$count)."</option>";
  					}
  					
  				?>
  			</select>
  			&nbsp;&nbsp;&nbsp;
  			分店
  			<select id="branch" name="branch">
  				<option value="A" <?=($branch=="A"?"selected":"");?>>所有</option>
  				<?
  					$sqlShop = "SELECT int_id, chr_ename, CASE int_dept WHEN 99 THEN CONCAT(txt_name,'(Closed)') ELSE txt_name END AS txt_name FROM tbl_user WHERE int_dept IN (2,99) AND LEFT(chr_sap,2) = 'TH' AND chr_ename <> '000' ORDER BY chr_sap";
  					$result_Shop = mysql_query($sqlShop) or die($sqlShop);
  					while($record_Shop = mysql_fetch_array($result_Shop)) {
						echo "<option value=\"".$record_Shop['int_id']."\"".($record_Shop[int_id]==$branch ? " selected" : "").">#".$record_Shop['chr_ename']." - ".$record_Shop['txt_name']."</option>";
					}
  				?>
  			</select>
  			&nbsp;&nbsp;&nbsp;
  			執法部門
  			<select id="enforceDept" name="enforceDept">
  				<option value="A" <?=($enfDept=="A"?"selected":"");?>>所有</option>
		        <option value="1" <?echo $enfDept=="1"?"selected":"";?>>消防處</option>
		        <option value="2" <?echo $enfDept=="2"?"selected":"";?>>食環署</option>
		        <option value="3" <?echo $enfDept=="3"?"selected":"";?>>勞工處</option>
		        <option value="4" <?echo $enfDept=="4"?"selected":"";?>>屋宇署</option>
		        <option value="5" <?echo $enfDept=="5"?"selected":"";?>>環保署</option>
  			</select>
  			&nbsp;&nbsp;&nbsp;
			<!--
  			檢控可能
  			<select id="riskLvl" name="riskLvl">
  				<option value="A" <?=($riskLvl=="A"?"selected":"");?>>所有</option>
  				<option value="4" <?=($riskLvl=="4"?"selected":"");?>>4. 極高</option>
  				<option value="3" <?=($riskLvl=="3"?"selected":"");?>>3. 高</option>
  				<option value="2" <?=($riskLvl=="2"?"selected":"");?>>2. 中</option>
  				<option value="1" <?=($riskLvl=="1"?"selected":"");?>>1. 低</option>
  			</select>
			-->
			
			檢控可能
  			<select id="riskLvl" name="riskLvl" style="width:85px;">
  				<option value="4">4. 極高</option>
  				<option value="3">3. 高</option>
  				<option value="2">2. 中</option>
  				<option value="1">1. 低</option>
  			</select>
			<input type="hidden" id="riskLvl_post" name="riskLvl" value="<?=$riskLvl?>" />
  			&nbsp;&nbsp;&nbsp;
  			<input type="submit" value="查看" />
</div>

<br>

<div id="p" class="easyui-panel" style="width:994;padding:10px;" title="執法部門巡查紀錄"  iconCls="" collapsible="false">
<?
	
	$sql_Notcomplete  = "SELECT DATE(e.datetime_input_date) AS 'datetime_input_date', e.chr_enforce_case_num, e.chr_enforce_risk_photo, e.chr_enforce_risk_measure";
	$sql_Notcomplete .= ", u.txt_name AS branchName, u.chr_ename AS branchCode, e.chr_enforce_dept, e.chr_enforce_time, e.chr_enforce_risk_level, e.chr_enforce_risk_readLicense";
	$sql_Notcomplete .= ", chr_enforce_detail, e.chr_remarks, e.chr_file_name_1, e.chr_file_name_2, e.chr_file_name_3";
	$sql_Notcomplete .= ", e.chr_file_name_4, e.chr_old_file_name_1, e.chr_old_file_name_2, e.chr_old_file_name_3, e.chr_old_file_name_4";
	$sql_Notcomplete .= ", f.chr_ename AS 'follower', e.date_penalty_ticket, e.chr_penalty_no, e.decimal_penalty_amount,f2.chr_ename AS 'follow',d.txt_dept,e.bool_counter,e.bool_end,e.chr_followcont";
	$sql_Notcomplete .= " FROM tbl_law_enforcement_dept_detail e LEFT JOIN tbl_user u ON e.int_branch = u.int_id";
	$sql_Notcomplete .= " LEFT JOIN tbl_user f ON e.int_followed_by = f.int_id ";
	
	$sql_Notcomplete .= " LEFT JOIN tbl_user f2 ON e.int_follow_user = f2.int_id ";
	$sql_Notcomplete .= " LEFT JOIN tbl_dept d ON e.int_followdept = d.int_id ";
	$sql_Notcomplete .= " WHERE (year(e.datetime_input_date) IN (".$rptYear.")) ";
	
	if($rptMonth <> "A"){
		$sql_Notcomplete .= " AND date_format(e.datetime_input_date,'%m') = '".$rptMonth."'";
	}
	
	if($enfDept <> "A") {
		$sql_Notcomplete .= " AND e.chr_enforce_dept = '";
		
		switch ($enfDept)
		{
			case 1:
				$sql_Notcomplete .= "消防處'";
				break;
			case 2:
				$sql_Notcomplete .= "食環署'";
				break;
			case 3:
				$sql_Notcomplete .= "勞工處'";
				break;
			case 4:
				$sql_Notcomplete .= "屋宇署'";
				break;
			case 5:
				$sql_Notcomplete .= "環保署'";
				break;
		}
	}	
	
	if($riskLvl <> "A") {
		$sql_Notcomplete .= " AND LEFT(e.chr_enforce_risk_level, INSTR(e.chr_enforce_risk_level,'.')-1) IN (".$riskLvl.")";
	}

	if($branch <> "A") {
		$sql_Notcomplete .= " AND u.int_id = '".$branch."'";
	}
	
	$sql_Notcomplete .= " ORDER BY e.chr_enforce_case_num;";
	$result_Notcomplete = mysql_query($sql_Notcomplete) or die($sql_Notcomplete);	

	if (mysql_num_rows($result_Notcomplete) == 0) {echo "<center><font size=5 color=red>沒有紀錄 !!</font></center>";} else {
?>
*點選欄目名稱以改變排序方式
	<table width="100%" class="easyui-datagrid" data-options="fitColumns:false,rownumbers:true,singleSelect:true,autoRowHeight:true,remoteSort:false,multiSort:true"> <!-- style="width:400px;height:250px" -->
	    <thead>
	        <tr>
	            <th data-options="field:'enf_code',halign:'center',align:'center',sortable:true">巡查編號</th>
	            <th data-options="field:'enf_date',halign:'center',align:'center',sortable:true">巡查日期</th>
	            <th data-options="field:'branch',halign:'center',sortable:true">分店</th>
	            <th data-options="field:'enf_dept',halign:'center',align:'center',sortable:true">執法部門</th>
	            <th data-options="field:'enf_time',halign:'center',align:'center'">巡查時間</th>
	            <th data-options="field:'enf_detail',halign:'center'">違規事項</th>
	            <th data-options="field:'risk_level',halign:'center',align:'center',sortable:true">檢控可能</th>
	            <th data-options="field:'risk_photo',halign:'center',align:'center',sortable:true">有否拍照</th>
	            <th data-options="field:'risk_measure',halign:'center',align:'center',sortable:true">有否度位</th>
	            <th data-options="field:'risk_readLicense',halign:'center',align:'center',sortable:true">查閱BR</th>
	            <th data-options="field:'enf_remark',halign:'center'">官員提及事項</th>
	            <th data-options="field:'attach',halign:'center'">上傳文件</th>
	            <th data-options="field:'follower',halign:'center',align:'center'">後勤負責同事</th>
	            <th data-options="field:'pt_date',halign:'center',align:'center'">傳票/報告日期</th>
	            <th data-options="field:'pt_number',halign:'center'">傳票/報告號碼</th>
	            <th data-options="field:'pAmt',halign:'center',align:'right',sortable:true">罰款$</th>
				
				 <th data-options="field:'chr_followcont',halign:'center',align:'right',sortable:true">跟進內容</th>
				 <th data-options="field:'follow',halign:'center',align:'right',sortable:true">後勤跟進部門</th>
				  <th data-options="field:'bool_counter',halign:'center',align:'right',sortable:true">區域經理已閱</th>
				  <th data-options="field:'bool_end',halign:'center',align:'right',sortable:true">案件完結</th>
			</tr>
	    </thead>
	    <tbody>
<?
			while($record_Notcomplete = mysql_fetch_array($result_Notcomplete)) {
?>
			<tr>
				<td><?=$record_Notcomplete[chr_enforce_case_num]?></td>
				<td><?=$record_Notcomplete[datetime_input_date]?></td>
				<td><?="#".$record_Notcomplete[branchCode]." - ".$record_Notcomplete[branchName];?></td>
				<td><?=$record_Notcomplete[chr_enforce_dept]?></td>
				<td><?=$record_Notcomplete[chr_enforce_time]?></td>
				<td><?=$record_Notcomplete[chr_enforce_detail]?></td>
				<td><?=$record_Notcomplete[chr_enforce_risk_level]?></td>
				<td><?=str_replace("無","---",$record_Notcomplete[chr_enforce_risk_photo])?></td>
				<td><?=str_replace("無","---",$record_Notcomplete[chr_enforce_risk_measure])?></td>
				<td><?=str_replace("無","---",$record_Notcomplete[chr_enforce_risk_readLicense])?></td>
				<td><?=nl2br($record_Notcomplete[chr_remarks])?></td>
				<td>
					<?if(TRIM($record_Notcomplete[chr_file_name_1]) != '') {?>
        				1:<a href="LawEnforcementRecords/<?=$record_Notcomplete[chr_file_name_1];?>" target="_blank"><?=$record_Notcomplete[chr_old_file_name_1]."</a>";}
        			  if(TRIM($record_Notcomplete[chr_file_name_2]) != '') {?>
        				<br>2:<a href="LawEnforcementRecords/<?=$record_Notcomplete[chr_file_name_2];?>" target="_blank"><?=$record_Notcomplete[chr_old_file_name_2]."</a>";}
        			  if(TRIM($record_Notcomplete[chr_file_name_3]) != '') {?>
        				<br>3:<a href="LawEnforcementRecords/<?=$record_Notcomplete[chr_file_name_3];?>" target="_blank"><?=$record_Notcomplete[chr_old_file_name_3]."</a>";}
        			  if(TRIM($record_Notcomplete[chr_file_name_4]) != '') {?>
        				<br>4:<a href="LawEnforcementRecords/<?=$record_Notcomplete[chr_file_name_4];?>" target="_blank"><?=$record_Notcomplete[chr_old_file_name_4]."</a>";}
        			?>
        		</td>
				<td><? echo ($record_Notcomplete[follower]==''?"&nbsp;":$record_Notcomplete[follower]);?></td>
				<td><? echo $record_Notcomplete[date_penalty_ticket] == "2999-01-01" ? "&nbsp;" : $record_Notcomplete[date_penalty_ticket];?></td>
				<td><? echo $record_Notcomplete[follower] == '' ? "&nbsp;" : $record_Notcomplete[chr_penalty_no];?></td>
				<td><? echo $record_Notcomplete[follower] == '' ? "&nbsp;" : number_format($record_Notcomplete[decimal_penalty_amount]);?></td>
					<td><? echo $record_Notcomplete[chr_followcont] == '' ? "&nbsp;" :  $record_Notcomplete[chr_followcont];?></td>
				<td><? echo $record_Notcomplete[follow] == '' ? "&nbsp;" :$record_Notcomplete[txt_dept]."-".$record_Notcomplete[follow];?></td>
				<td><? echo  ord($record_Notcomplete[bool_counter]) == '0' ? "否" : "是";?></td>
				<td><? echo ord($record_Notcomplete[bool_end]) == '0' ? "否" : '是';?></td>
			</tr>
<?
			}    	
?>
	    </tbody>
	</table>
	
<?
	}
?>
	
</div>

</form>

<br>

<table width="994" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><img src="images/TaiHing_23.jpg" width="994" height="49"></td>
  </tr>
</table>

</body>
</html>