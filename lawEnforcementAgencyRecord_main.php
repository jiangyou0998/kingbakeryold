<?
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'lawEnforcementAgencyRecord_main.php';
    header('Location: login.php');
}
require("connect.inc");
//require("phpMailer/class.phpmailer.php");
$timestamp = gettimeofday("sec") + 28800;
//$_SESSION['user_id']=56;
$show_Entry_Months = 3;

$riskLvl = $_REQUEST['riskLvl'];
$riskLvl = $riskLvl == '' ? "1,2,3,4" : $riskLvl;

if (isset($_POST['submit'])) {
    $submit = $_POST['submit'];
    if ($submit == "輸入") {
        foreach ($_FILES["uploadfile"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $filename = $_FILES["uploadfile"]['tmp_name'][$key];
                $extension = end(explode(".", $_FILES["uploadfile"]['name'][$key]));
                $newfilename = $_SESSION['user_id'] . '-' . date('ymdHis', $timestamp) . '-' . ($key + 1) . '.' . $extension;
                move_uploaded_file($filename, "./LawEnforcementRecords/" . $newfilename);
                $upName[0][$key] = $_FILES["uploadfile"]['name'][$key];
                $upName[1][$key] = $newfilename;
            }
        }

        $sql_insert = "INSERT INTO tbl_law_enforcement_dept_detail (chr_enforce_case_num, int_branch, datetime_input_date, chr_enforce_dept, chr_enforce_time, chr_enforce_risk_level, chr_enforce_risk_photo, chr_enforce_risk_measure, chr_shopStaff ";
        $sql_insert .= ", chr_enforce_detail, chr_remarks, chr_file_name_1, chr_file_name_2, chr_file_name_3, chr_file_name_4,chr_old_file_name_1,chr_old_file_name_2,chr_old_file_name_3,chr_old_file_name_4, chr_enforce_risk_readLicense";
        $sql_insert .= ") VALUES ((SELECT CONCAT(chr_prefix, LPAD(CAST(int_enforce_no AS CHAR),3,'0')) FROM tbl_law_enforcement_dept_no), " . ($_SESSION['dept'] <> 20 ? $_SESSION['user_id'] : $_POST["branch"]) . "," . ($_SESSION['dept'] <> 20 ? "NOW()" : "'" . $_POST["d11"] . "'") . " , '";
        $sql_insert .= $_POST['enf_Dept'] . "','" . $_POST['enf_Time'] . "','" . $_POST['risk_level'] . "','" . $_POST['risk_photo'] . "','" . $_POST['risk_measure'] . "','" . $_POST['shopStaff'] . "','" . $_POST['de'] . "','" . $_POST['textarea'];
        $sql_insert .= "','" . $upName[1][0] . "','" . $upName[1][1] . "','" . $upName[1][2] . "','" . $upName[1][3] . "','" . $upName[0][0] . "','" . $upName[0][1] . "','" . $upName[0][2] . "','" . $upName[0][3] . "','" . $_POST['risk_readLicense'] . "') ";
//echo $sql_insert;
        mysqli_query($con, 'set name big5');
        $insert_Result = mysqli_query($con, $sql_insert) or die($sql_insert);
        if ($insert_Result > 0) {
            $sql_update = "UPDATE tbl_law_enforcement_dept_no SET int_enforce_no = int_enforce_no + 1";
            $update_Result = mysqli_query($con, $sql_update) or die($sql_update);
            ?>
            <script language="javascript">alert("成功新增巡查事項!");
                window.location.href = "lawEnforcementAgencyRecord_main.php"</script><?
            //sendEmail();
            //sendEmail2();
        }

    } else if ($submit == "更新") {
        $updateID = $_POST['updateID'];

        $sql_update = "UPDATE tbl_law_enforcement_dept_detail SET int_followed_by = " . $_SESSION['user_id'] . ", date_updateDate = DATE(NOW()), date_penalty_ticket = '" . $_POST['update_ptDate'];
        $sql_update .= "', chr_penalty_no = '" . $_POST['update_ptNum'] . "', decimal_penalty_amount = '" . $_POST['update_pAmt'] . "' WHERE int_id = " . $updateID;

        $update_Result = mysqli_query($con, $sql_update) or die($sql_update);
//$update_Result = 0;
        if ($update_Result > 0) {
            if ($_POST['updateReply'] == 1) {
            }
            ?>
            <script language="javascript"> window.location.href = "lawEnforcementAgencyRecord_main.php"</script><?
        }
    }
}

/*  
  function sendEmail()
  {
	  
  	$sql_email  = "SELECT le.*, date(le.datetime_input_date) AS 'inputDate', shop.chr_ename AS 'shopCode', shop.txt_name AS 'shopName'";
  	$sql_email .= ", shop.chr_email AS 'shopEmail', zonemgr.txt_name AS 'AMName', zonemgr.chr_email AS 'AMEmail', Mzonemgr.txt_name AS 'MZName'";
  	$sql_email .= ", Mzonemgr.chr_email AS 'MZEmail' FROM tbl_law_enforcement_dept_detail le LEFT JOIN tbl_user shop ON le.int_branch = shop.int_id";
	$sql_email .= " LEFT JOIN tbl_district dst ON dst.int_id = shop.int_district AND shop.int_dept = 2 LEFT JOIN tbl_dis_group zone";
	$sql_email .= " ON zone.int_id = dst.int_dis_group LEFT JOIN tbl_user zonemgr ON zonemgr.int_id = zone.int_manager LEFT JOIN tbl_dis_group_new Mzone";
	$sql_email .= " ON Mzone.int_id = zone.int_group_new LEFT JOIN tbl_user Mzonemgr ON Mzonemgr.int_id = Mzone.int_manager WHERE le.int_branch = ";
	$sql_email .= $_SESSION['user_id']." ORDER BY le.chr_enforce_case_num DESC LIMIT 1";
	
  	$email_Result = mysqli_query($con, $sql_email) or die($sql_email);
  	
  	while($record_email = mysqli_fetch_array($email_Result)) {
  	
  		$email_Name = "執法部門巡查報告 - 巡查編號:".$record_email['chr_enforce_case_num'];
  		
  		$emailCntStr  = "<table border=\"1\" cellspacing=\"1\" cellpadding=\"1\"> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>巡查編號</b></td><td>".$record_email['chr_enforce_case_num']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>巡查日期/時間</b></td><td>".$record_email[inputDate]." (".$record_email[chr_enforce_time].")</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>分店</b></td><td>#".$record_email['shopCode']." - ".$record_email['shopName']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>接待同事</b></td><td>".$record_email['chr_shopStaff']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>執法部門</b></td><td>".$record_email['chr_enforce_dept']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>違規事項</b></td><td>".$record_email['chr_enforce_detail']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>檢控可能</b></td><td>".$record_email['chr_enforce_risk_level']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>有否拍照</b></td><td>".$record_email['chr_enforce_risk_photo']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>有否度位</b></td><td>".$record_email['chr_enforce_risk_measure']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>有否查閱BR/食肆牌照</b></td><td>".$record_email['chr_enforce_risk_readLicense']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>官員提及事項</b></td><td>".nl2br($record_email['chr_remarks'])."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>有否上載附件</b></td><td>";
		$emailCntStr .= TRIM($record_email[chr_file_name_1]).TRIM($record_email[chr_file_name_2]).TRIM($record_email[chr_file_name_3]).TRIM($record_email[chr_file_name_4]) == '' ? "無" : "有";
		$emailCntStr .= "</td></tr> ";
  		$emailCntStr .= "</table> ";
  		
//  		echo $emailCntStr;
		
  		$email_contect  = $emailCntStr;
  			
  		$mail = new PHPMailer();
  		$mail->IsSMTP();                                      // set mailer to use SMTP
		//  		$mail->Host = "corpmail1.netvigator.com";             // specify main and backup server
		
  		include("mail_host.php");
		$mail->Host = $new_mail_host;
		
  		$mail->Port = 25;
  		
  		$mail->AddAddress($record_email['shopEmail'], $record_email['shopName']);	// Send mail back to shop  		
  		$mail->AddReplyTo($record_email['shopEmail'], $record_email['shopName']);
		$mail->AddBCC($record_email['AMEmail'], $record_email['AMName']);

		if($record_email['MZEmail'] != '' && $record_email['MZEmail'] != $record_email['AMEmail']){
			$mail->AddBCC($record_email['MZEmail'], $record_email['MZEmail']);
		}

		if(substr($record_email['chr_enforce_risk_level'],0,strpos($record_email['chr_enforce_risk_level'],'.')) >= 3) {
			$mail->AddBCC($OPD_headmail,$OPD_headname);
  		}  			  		
  		
  		$mail->CharSet="big5";
  		$mail->Encoding = "base64";
  		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
  		$mail->IsHTML(true);                                  // set email format to HTML
  		//			    $mail->AddAttachment($target_path);
  		$mail->Subject = $email_Name;
  		$mail->Body    = $email_contect;
  	
  		if(!$mail->Send())
  		{
  			echo "報告失敗，就重試！<p>";
  			echo "Mailer Error: " . $mail->ErrorInfo;
  	
  			exit;
  		}
  	}
	}
	
	function sendEmail2()
  {
  	$sql_email  = "SELECT le.*, date(le.datetime_input_date) AS 'inputDate', shop.chr_ename AS 'shopCode', shop.txt_name AS 'shopName'";
  	$sql_email .= ", shop.chr_email AS 'shopEmail', zonemgr.txt_name AS 'AMName', zonemgr.chr_email AS 'AMEmail', Mzonemgr.txt_name AS 'MZName'";
  	$sql_email .= ", Mzonemgr.chr_email AS 'MZEmail',mu.chr_email as memail,mu.txt_name as mname FROM tbl_law_enforcement_dept_detail le LEFT JOIN tbl_user shop ON le.int_branch = shop.int_id";
	$sql_email .= " LEFT JOIN tbl_district dst ON dst.int_id = shop.int_district AND shop.int_dept = 2 LEFT JOIN tbl_dis_group zone";
	$sql_email .= " ON zone.int_id = dst.int_dis_group LEFT JOIN tbl_user zonemgr ON zonemgr.int_id = zone.int_manager LEFT JOIN tbl_dis_group_new Mzone";
	$sql_email .= " ON Mzone.int_id = zone.int_group_new LEFT JOIN tbl_user Mzonemgr ON Mzonemgr.int_id = Mzone.int_manager";
	
				$sql_email .= " left join tbl_district as dis on dis.int_dis_no=shop.chr_ename ";
				$sql_email .= " left join tbl_dis_group as g on g.int_id=dis.int_dis_group ";
				$sql_email .= " left join tbl_user as mu on mu.int_id=g.int_manager ";
	
$sql_email .= "	WHERE le.int_branch = ";
	$sql_email .= $_SESSION['user_id']." ORDER BY le.chr_enforce_case_num DESC LIMIT 1";
	
  	$email_Result = mysqli_query($con, $sql_email) or die($sql_email);
  	
  	while($record_email = mysqli_fetch_array($email_Result)) {
  	
  		$email_Name = "執法部門巡查報告 - 巡查編號:".$record_email['chr_enforce_case_num'];
  		
  		$emailCntStr  = "<table border=\"1\" cellspacing=\"1\" cellpadding=\"1\"> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>巡查編號</b></td><td>".$record_email['chr_enforce_case_num']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>巡查日期/時間</b></td><td>".$record_email[inputDate]." (".$record_email[chr_enforce_time].")</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>分店</b></td><td>#".$record_email['shopCode']." - ".$record_email['shopName']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>接待同事</b></td><td>".$record_email['chr_shopStaff']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>執法部門</b></td><td>".$record_email['chr_enforce_dept']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>違規事項</b></td><td>".$record_email['chr_enforce_detail']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>檢控可能</b></td><td>".$record_email['chr_enforce_risk_level']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>有否拍照</b></td><td>".$record_email['chr_enforce_risk_photo']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>有否度位</b></td><td>".$record_email['chr_enforce_risk_measure']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>有否查閱BR/食肆牌照</b></td><td>".$record_email['chr_enforce_risk_readLicense']."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>官員提及事項</b></td><td>".nl2br($record_email['chr_remarks'])."</td></tr> ";
  		$emailCntStr .= "<tr><td align=\"center\"><b>有否上載附件</b></td><td>";
		$emailCntStr .= TRIM($record_email[chr_file_name_1]).TRIM($record_email[chr_file_name_2]).TRIM($record_email[chr_file_name_3]).TRIM($record_email[chr_file_name_4]) == '' ? "無" : "有";
		$emailCntStr .= "</td></tr> ";
  		$emailCntStr .= "</table> ";
  		
  		//echo $emailCntStr;


  		$email_contect  = $emailCntStr;
  			
  		$mail = new PHPMailer();
  		$mail->IsSMTP();                                      // set mailer to use SMTP
//  		$mail->Host = "corpmail1.netvigator.com";             // specify main and backup server
  		
		include("mail_host.php");
		$mail->Host = $new_mail_host;
		
  		$mail->Port = 25;
  		$mail->FromName = "內聯網 - 執法部門巡查報告"; 			  		
  		$mail->AddBCC($record_email[memail], $record_email[mname]);
  		$mail->CharSet="big5";
  		$mail->Encoding = "base64";
  		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
  		$mail->IsHTML(true);                                  // set email format to HTML
  		//			    $mail->AddAttachment($target_path);
  		$mail->Subject = $email_Name;
  		$mail->Body    = $email_contect;
  	
  		if(!$mail->Send())
  		{
  			echo "報告失敗，就重試！<p>";
  			echo "Mailer Error: " . $mail->ErrorInfo;
  	
  			exit;
  		}

  	}
  }
  */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>

    <title>內聯網</title>
    <style type="text/css">
        body, td, th {
            font-size: small;
        }

        td {
        }

        tr td:last-child {
            width: 1%;
            white-space: nowrap;
        }
    </style>

    <script type="text/javascript" src="LawEnforcementRecords/js/fbw-ajax.js"></script>
    <script type="text/javascript" src="js/overlib.js"></script>
    <script src="calendar/calendar2.js"></script>
    <!--
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/themes/hot-sneaks/jquery-ui.css" rel="stylesheet">
    -->
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <link href="css/jquery-ui.css" rel="stylesheet">

    <script type='text/javascript' src="training/js/jquery-ui-timepicker-addon.js"></script>
    <script type='text/javascript' src='LawEnforcementRecords/js/jquery-ui-sliderAccess.js'></script>
    <link rel="stylesheet" type="text/css" href="LawEnforcementRecords/css/jquery-ui-timepicker-addon.css">
    <script type='text/javascript' src='My97DatePicker/WdatePicker.js'></script>
    <script type='text/javascript' src="js/MultipleSelect/multiple-select.js"></script>


    <script src="js/wbox-min.js"></script>
    <link rel="stylesheet" type="text/css" href="js/MultipleSelect/multiple-select.css">
    <link href="css/checkbox-style.css" rel="stylesheet" type="text/css"/>
    <link href="css/MultipleSelectList-checkbox.css" rel="stylesheet" type="text/css"/>
    <link href="wbox/wbox.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">


        $(document).ready(function () {
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
            $.timepicker.setDefaults($.timepicker.regional["zh-TW"]);

            var opt = {
                showSecond: false,
                timeFormat: 'HH:mm'
//							,
//						addSliderAccess:true,
//						sliderAccessArgs:{touchonly:false}
            };
            $('#enf_Time').timepicker(opt);

            $("#riskLvl").multipleSelect({
                selectAllText: '所有',
                allSelected: '所有',
                multiple: true,
                multipleWidth: 70,
                onClose: function () {
                    $("#riskLvlSel").val($("#riskLvl").multipleSelect('getSelects').join(','));
                    rstSel();
                }
            });
            $("#riskLvl").multipleSelect('setSelects', [<?=$riskLvl?>]);
        });

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode

            if ((charCode > 31 && (charCode != 46 && (charCode < 48 || charCode > 57)))
                || (charCode == 46 && (document.getElementById('pAmt').value.indexOf(".") > -1 || document.getElementById('pAmt').value == '')))
                return false;
            return true;
        }

        function populate(o) {

            d = document.getElementById('de');

            if (!d) {
                return;
            }

            var mitems = new Array();
            mitems['---請選擇---'] = ['--請先選部門--'];
            mitems['消防處'] = ['沒有', '阻塞走火通道', '走火通道不暢通', '其他'];
            mitems['食環署'] = ['沒有', '食物不潔/有異物', '食肆內有蛇鼠', '無牌經營', '不按牌照範圍經營', '更改圖則', '走火通道不暢通'];
            mitems['勞工處'] = ['沒有', '走火通道不暢通', '工傷調查', '其他'];
            mitems['屋宇署'] = ['沒有', '走火通道不暢通', '其他'];
            mitems['環保署'] = ['沒有', '其他'];
            mitems['五常部'] = ['沒有', '其他'];
            d.options.length = 0;
            cur = mitems[o.options[o.selectedIndex].value];
            if (!cur) {
                return;
            }
            d.options.length = cur.length;
            for (var i = 0; i < cur.length; i++) {
                d.options[i].text = cur[i];
                d.options[i].value = cur[i];
            }
        }

        function checkRisk() {
            riskPoint = ((document.getElementById('risk_photo').selectedIndex == 1 ? 1 : 0)
                + (document.getElementById('risk_measure').selectedIndex == 1 ? 1 : 0)
                + (document.getElementById('risk_readLicense').selectedIndex == 1 ? 1 : 0)
            );
            riskSelIdx = document.getElementById('risk_level').selectedIndex;
            exceptCase = (document.getElementById('enf_Dept').value == "勞工處" && document.getElementById('de').value == "工傷調查") ? true : false;


            if (riskPoint == 3 && !exceptCase) {
                if (riskSelIdx != 1 && riskSelIdx != 2) {
                    alert('「檢控可能」請選「高」或以上');
                    document.getElementById('risk_level').selectedIndex = 2;
                }

            } else if (riskPoint == 2 && !exceptCase) {
                if (riskSelIdx == 0 || riskSelIdx == 4) {
                    alert('「檢控可能」請選「中」或以上');
                    document.getElementById('risk_level').selectedIndex = 3;
                }
            }
        }

        function alertRisk() {
            if (document.getElementById('risk_level').selectedIndex == 1 || document.getElementById('risk_level').selectedIndex == 2)
                alert('請緊記附上/補上「特別事項報告」及「相片」!!!');
        }

        function confInsertSubmit() {


            if (document.getElementById('enf_Dept').selectedIndex == 0) {
                alert('請提供「執法部門」選項');
                return false;
            } else if (document.getElementById('udept').value == '20' && document.getElementById('branch').selectedIndex == 0) {
                alert('請提供「分店」選項');
                return false;
            } else if (document.getElementById('shopStaff').value == '') {
                alert('請提供「接待同事」');
                return false;
            } else if (document.getElementById('enf_Time').selectedIndex == 0) {
                alert('請提供「巡查時間」選項');
                return false;
            } else if (document.getElementById('risk_level').selectedIndex == 0) {
                alert('請提供「檢控可能」選項');
                return false;
            } else if (document.getElementById('risk_photo').selectedIndex == 0) {
                alert('請提供「有否拍照」選項');
                return false;
            } else if (document.getElementById('risk_measure').selectedIndex == 0) {
                alert('請提供「有否度位」選項');
                return false;
            } else if (document.getElementById('risk_readLicense').selectedIndex == 0) {
                alert('請提供「有否查閱BR/食肆牌照」選項');
                return false;
            } else if (document.getElementById('textarea').value == '') {
                alert('請提供「官員提及事項」');
                return false;
            } else if (document.getElementById('enf_Time').value == '請選時間') {
                alert('請選擇「巡查時間」');
                return false;
            } else if (document.getElementById('udept').value == '20' && document.getElementById('d11').value == "請選擇日期") {
                alert('請選擇「巡查日期」');
                return false;
            }

            checkRisk();

            var answer = confirm("確認提交資料?(如有文件檔，按\"是\"後請耐心等待)");
            if (!answer) {
                return false;
            }
        }

        function updateButtonClick(o) {
            if (document.getElementById('ptNum' + o.id).value == '' || document.getElementById('pAmt' + o.id).value == '') {
                document.getElementById('updateCheck').value = 0;
                alert('請輸入「傳票/報告號碼」及「罰款金額」!');
            } else {
                document.getElementById('updateID').value = o.id;
                document.getElementById('update_ptDate').value = document.getElementById('ptDate' + o.id).value;
                document.getElementById('update_ptNum').value = document.getElementById('ptNum' + o.id).value;
                document.getElementById('update_pAmt').value = document.getElementById('pAmt' + o.id).value;

                document.getElementById('updateCheck').value = 1;
            }
        }

        function confUpdateSubmit() {
            return document.getElementById('updateCheck').value == 1 ? true : false;
        }

        function upFile(btnId) {
            //document.getElementById('file'+btnId).click();

            if (document.getElementById('file' + btnId).value != '')
                document.getElementById('btnFile' + btnId).style.backgroundColor = "#00FF00";

            return false;
        }

        function clearFileInputField(tagId) {
            document.getElementById('div_file' + tagId).innerHTML =
                document.getElementById('div_file' + tagId).innerHTML;
            document.getElementById('btnFile' + tagId).style.backgroundColor = "";
        }

        function show(obj, id) {

            wBox = $("#extraInfo" + obj).wBox({
                requestType: "iframe",
                target: "lawEnforcementAgencyRecord_additional_utf_8.php?id=" + id,
                title: "跟進資料",
                width: 200,
                height: 400
            });
            wBox.showBox();
        }

        function rstSel() {
            enforceDept = document.getElementById('enforceDeptSel');
            branch = document.getElementById('branchSel');
            riskLvl = document.getElementById('riskLvlSel');
            read = document.getElementById('readSel');
            caseClose = document.getElementById('caseCloseSel');

            rdURL = "lawEnforcementAgencyRecord_main.php?branch=" + branch.options[branch.selectedIndex].value
                + "&enforceDept=" + enforceDept.options[enforceDept.selectedIndex].value
                //+"&riskLvl="+riskLvl.options[riskLvl.selectedIndex].value
                + "&riskLvl=" + riskLvl.value
                + "&read=" + read.options[read.selectedIndex].value
                + "&caseClose=" + caseClose.options[caseClose.selectedIndex].value;

            window.location.href = rdURL;
        }

    </script>
    <style type="text/css">
        <!--
        body {
            margin-left: 0px;
            margin-top: 0px;
        }

        -->
    </style>
</head>
<body>
<? include("head.php"); ?>

<div id="popupPanel" style="display: none;">
    <div class="popup" name="__popup" onMouseOut="exitPanel(event)">
        <table cellspacing="0" width="222px" height="120px" cellpadding="0" border="0" background="images/order_bg.jpg"
               style="background-repeat:no-repeat">
            <tr>
                <td class="description" width="230px"
                    style="padding: 5px; padding-left:8px;padding-top:8px;vertical-align: top;">
                    <span style="font-size:15px; color:#000;">p{chr_remarks}</span>
                </td>
                <td width="5px" style="padding: 5px;padding-top:8px;padding-left:0px;  vertical-align: bottom;">
                    <a style="border: none;" href="javascript:void(0)" onClick="nd();">
                        <img style="border: none;" src="images/close.gif"/>
                    </a>
                </td>
            </tr>
        </table>
    </div>
</div>

<div align="center" style="width:994px;">
    <h1><u>執法部門巡查報告</u></h1>
</div>
<div align="center" style="width:994px;line-height:25px;height:50px;overflow:hidden">
    <h2>新巡查輸入</h2>
</div>

<form enctype="multipart/form-data" name="form_CaseInsert" id="form" action="lawEnforcementAgencyRecord_main.php"
      method="post" onSubmit="return confInsertSubmit();">

    <table border="1" cellspacing="0" cellpadding="1" width="994px">
        <tr bgcolor="#CCFFFF">
            <td width="100" align="center" bgcolor="#CCFFFF"><b>巡查</b></td>
            <td width="64" align="center" bgcolor="#CCFFFF"><b>分店</b></td>
            <td width="80" align="center" bgcolor="#CCFFFF"><b>執法部門</b></td>
            <td width="80" align="center" bgcolor="#CCFFFF"><b>接待同事</b></td>
            <td width="100" align="center" bgcolor="#CCFFFF"><b>違規事項</b></td>
            <td width="200" align="center" bgcolor="#CCFFFF"><b>官員提及事項</b></td>
            <td width="110" align="center" bgcolor="#CCFFFF"><b>官員行為</b></td>
            <td width="160" align="center" bgcolor="#CCFFFF"><b>上傳文件 (如有)</b></td>
            <td align="center" bgcolor="#CCFFFF">&nbsp;</td>
        </tr>
        <tr>
            <td align="center"><? if ($_SESSION['dept'] <> 20) {
                    echo date('Y-m-d', $timestamp) . "<br>";
                } else { ?>
                    <input id="d11" type="text" name="d11" size="9" value="請選日期" onClick="WdatePicker()"/>
                <? } ?>
                <input type='text' id='enf_Time' name='enf_Time' maxlength='5' size="8" value='10:10' READONLY/></td>
            <td align="center">
                <input type="hidden" id="udept" value="<?= $_SESSION['dept'] ?>">
                <?= $_SESSION['dept'] <> 20 ? $_SESSION['user'] : "" ?>
                <select id="branch" name="branch" <? if ($_SESSION['dept'] <> 20) { ?>style="display:none"<? } ?>>
                    <option value="0">請選擇</option>
                    <?
                    $sqlShop = "SELECT int_id, chr_ename, txt_name FROM tbl_user WHERE int_dept = 2 AND LEFT(chr_sap,2) = 'TH' ORDER BY chr_sap";
                    $result_Shop = mysqli_query($con, $sqlShop) or die($sqlShop);
                    while ($record_Shop = mysqli_fetch_array($result_Shop)) {
                        echo "<option value=\"" . $record_Shop['int_id'] . "\">#" . $record_Shop['chr_ename'] . " - " . $record_Shop['txt_name'] . "</option>";
                    }
                    ?>
                </select>
            </td>
            <td align="center" valign="middle">
                <select name="enf_Dept" id="enf_Dept" style="width: 64" onChange="populate(this)">
                    <option>-請選擇-</option>
                    <option value="消防處" <? echo $_REQUEST['sel_enf_Dept'] == 1 ? "selected" : ""; ?>>消防處</option>
                    <option value="食環署" <? echo $_REQUEST['sel_enf_Dept'] == 2 ? "selected" : ""; ?>>食環署</option>
                    <option value="勞工處" <? echo $_REQUEST['sel_enf_Dept'] == 3 ? "selected" : ""; ?>>勞工處</option>
                    <option value="屋宇署" <? echo $_REQUEST['sel_enf_Dept'] == 4 ? "selected" : ""; ?>>屋宇署</option>
                    <option value="環保署" <? echo $_REQUEST['sel_enf_Dept'] == 5 ? "selected" : ""; ?>>環保署</option>
                </select>
            </td>
            <td align="center" valign="middle">
                <input type='text' id='shopStaff' name='shopStaff' size="8"/>
            </td>
            <td align="center" valign="middle">
                <select name="de" id="de" style="width: 100">
                    <?
                    if (($_REQUEST['sel_enf_Dept'] == '' || $_REQUEST['sel_enf_Dept'] == '0') && $_SESSION['dept'] <> 20)
                        echo "<option>-請先選部門-</option>";
                    else
                        echo "<option>其他</option>";
                    ?>
                </select>
            </td>
            <td align="center">
                <textarea name="textarea" id="textarea" style="height: 100px; width: 200px"></textarea>
            </td>
            <td>
                <table width="100%" border="0" cellpadding="3" cellspacing="0">
                    <tr>
                        <td align="right">
                            <select name="risk_level" id="risk_level" style="width:105px" onChange="alertRisk()">
                                <option>檢控可能</option>
                                <option value="4. 極高">****極高</option>
                                <option value="3. 高">***高</option>
                                <option value="2. 中">**中</option>
                                <option value="1. 低">*低</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <select name="risk_photo" id="risk_photo" style="width:105px" onChange="checkRisk()">
                                <option>有否拍照</option>
                                <option value="有">**有拍照</option>
                                <option value="無">無拍照</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <select name="risk_measure" id="risk_measure" style="width:105px" onChange="checkRisk()">
                                <option selected="selected">有否度位/取樣版</option>
                                <option value="有">**有度位/取樣版</option>
                                <option value="無">無度位/取樣版</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <select name="risk_readLicense" id="risk_readLicense" style="width:105px"
                                    onChange="checkRisk()">
                                <option>有否查閱牌照</option>
                                <option value="有">**有查閱</option>
                                <option value="無">無查閱</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table width="100%" border="0" cellpadding="1" cellspacing="0">
                    <tr>
                        <td align="right">
                            <div id="div_file1">
                                <input id="file1" name="uploadfile[]" type="file" onChange="upFile('1');"
                                       style="position:absolute;filter:alpha(opacity=0);  -moz-opacity:0;         /*火狐*/opacity:0;  width:105px;"/>
                                <input type="button" id="btnFile1" name="btnFile1" style="width:116px" value="特別事項報告"/>
                            </div>
                        </td>
                        <td><input type="button" id="clear1" name="clear1" onClick="clearFileInputField('1');"
                                   value="清除"/></td>
                    </tr>
                    <tr>
                        <td align="right">
                            <div id="div_file2">
                                <input id="file2" name="uploadfile[]" type="file" onChange="upFile('2');" size="1"
                                       style="position:absolute;filter:alpha(opacity=0);  -moz-opacity:0;         /*火狐*/opacity:0;  width:105px;"/>
                                <input type="button" id="btnFile2" name="btnFile2" style="width:116px" value="相片1"/>
                            </div>
                        </td>
                        <td><input type="button" id="clear2" name="clear2" onClick="clearFileInputField('2');"
                                   value="清除"/></td>
                    </tr>
                    <tr>
                        <td align="right">
                            <div id="div_file3">
                                <input id="file3" name="uploadfile[]" type="file" onChange="upFile('3');" size="1"
                                       style="position:absolute;filter:alpha(opacity=0);  -moz-opacity:0;         /*火狐*/opacity:0;  width:105px;"/>
                                <input type="button" id="btnFile3" name="btnFile3" style="width:116px" value="相片2"/>
                            </div>
                        </td>
                        <td><input type="button" id="clear3" name="clear3" onClick="clearFileInputField('3');"
                                   value="清除"/></td>
                    </tr>
                    <tr>
                        <td align="right">
                            <div id="div_file4">
                                <input id="file4" name="uploadfile[]" type="file" onChange="upFile('4');" size="1"
                                       style="position:absolute;filter:alpha(opacity=0);  -moz-opacity:0;         /*火狐*/opacity:0;  width:105px;"/>
                                <input type="button" id="btnFile4" name="btnFile4" style="width:116px" value="相片3"/>
                            </div>
                        </td>
                        <td><input type="button" id="clear4" name="clear4" onClick="clearFileInputField('4');"
                                   value="清除"/></td>
                    </tr>
                </table>
            </td>
            <td align="center">
                <input type="submit" name="submit" value="輸入"/>
            </td>
        </tr>
    </table>
</form>

<br>
<br>

<?
$enfDept = $_REQUEST['enforceDept'];
$enfDept = $enfDept == '' ? "A" : $enfDept;

$branch = $_REQUEST['branch'];
$branch = $branch == '' ? "A" : $branch;

$read = $_REQUEST['read'];
$read = $read == '' ? "A" : $read;

$caseClose = $_REQUEST['caseClose'];
$caseClose = $caseClose == '' ? "A" : $caseClose;
?>
<table width="994px" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center">
            <h2>最近<?= $show_Entry_Months; ?>個月內收到之巡查意見</h2>
            <label
                <? if ($_SESSION['chr_sap'] <> '' && $_SESSION['dept'] == 2) { ?>style="display:none"<? } ?>>分店</label>
            <select id="branchSel" name="branchSel" onChange="rstSel()"
                    <? if ($_SESSION['chr_sap'] <> '' && $_SESSION['dept'] == 2) { ?>style="display:none"<? } ?>>
                <option value="A" <?= ($branch == "A" ? "selected" : ""); ?>>所有</option>
                <?
                $sqlShop = "SELECT int_id, chr_ename, txt_name FROM tbl_user WHERE int_dept = 2 AND LEFT(chr_sap,2) = 'TH' ORDER BY chr_sap";
                $result_Shop = mysqli_query($con, $sqlShop) or die($sqlShop);
                while ($record_Shop = mysqli_fetch_array($result_Shop)) {
                    echo "<option value=\"" . $record_Shop['int_id'] . "\"" . ($record_Shop[int_id] == $branch ? " selected" : "") . ">#" . $record_Shop['chr_ename'] . " - " . $record_Shop['txt_name'] . "</option>";
                }
                ?>
            </select>
            <label <? if ($_SESSION['chr_sap'] <> '' && $_SESSION['dept'] == 2) { ?>style="display:none"<? } ?>>&nbsp;&nbsp;&nbsp;</label>
            執法部門
            <select id="enforceDeptSel" name="enforceDeptSel" onChange="rstSel()">
                <option value="A" <?= ($enfDept == "A" ? "selected" : ""); ?>>所有</option>
                <option value="1" <? echo $enfDept == "1" ? "selected" : ""; ?>>消防處</option>
                <option value="2" <? echo $enfDept == "2" ? "selected" : ""; ?>>食環署</option>
                <option value="3" <? echo $enfDept == "3" ? "selected" : ""; ?>>勞工處</option>
                <option value="4" <? echo $enfDept == "4" ? "selected" : ""; ?>>屋宇署</option>
                <option value="5" <? echo $enfDept == "5" ? "selected" : ""; ?>>環保署</option>
                <option value="6" <? echo $enfDept == "6" ? "selected" : ""; ?>>五常部</option>
            </select>
            &nbsp;&nbsp;&nbsp;
            檢控可能
            <!--
  			<select id="riskLvlSel" name="riskLvlSel" onChange="rstSel()">
  				<option value="A" <?= ($riskLvl == "A" ? "selected" : ""); ?>>所有</option>
  				<option value="4" <?= ($riskLvl == "4" ? "selected" : ""); ?>>4. 極高</option>
  				<option value="3" <?= ($riskLvl == "3" ? "selected" : ""); ?>>3. 高</option>
  				<option value="2" <?= ($riskLvl == "2" ? "selected" : ""); ?>>2. 中</option>
  				<option value="1" <?= ($riskLvl == "1" ? "selected" : ""); ?>>1. 低</option>
  			</select>
			-->
            <select id="riskLvl" name="riskLvl" style="width:160px;">
                <option value="4">4. 極高</option>
                <option value="3">3. 高</option>
                <option value="2">2. 中</option>
                <option value="1">1. 低</option>
            </select>
            <input type="hidden" id="riskLvlSel" name="riskLvlSel" value="1,2,3,4"/>

            &nbsp;&nbsp;&nbsp;
            已閱
            <select id="readSel" name="readSel" onChange="rstSel()">
                <option value="A" <?= ($read == "A" ? "selected" : ""); ?>>所有</option>
                <option value="1" <?= ($read == "1" ? "selected" : ""); ?>>是</option>
                <option value="0" <?= ($read == "0" ? "selected" : ""); ?>>否</option>
            </select>
            &nbsp;&nbsp;&nbsp;
            已結
            <select id="caseCloseSel" name="caseCloseSel" onChange="rstSel()">
                <option value="A" <?= ($caseClose == "A" ? "selected" : ""); ?>>所有</option>
                <option value="1" <?= ($caseClose == "1" ? "selected" : ""); ?>>是</option>
                <option value="0" <?= ($caseClose == "0" ? "selected" : ""); ?>>否</option>
            </select>
        </td>
    </tr>
</table>

<form name="form_CaseUpdate" id="form" action="lawEnforcementAgencyRecord_main.php" method="post">
    <!--  onSubmit="return confUpdateSubmit();" -->

    <?

    //$sql_m='select count(int_id) from  tbl_dis_group where int_manager ='.$_SESSION['user_id'];
    //$result_m = mysqli_query($con, $sql_m) or die($sql_m);
    //$record_m = mysqli_fetch_array($result_m);

    $sql_Notcomplete = "SELECT e.int_id, DATE(e.datetime_input_date) AS 'datetime_input_date', e.chr_enforce_case_num, e.chr_shopStaff";
    $sql_Notcomplete .= ", u.txt_name AS branchName, u.chr_ename AS branchCode, e.chr_enforce_dept, e.chr_enforce_time, e.chr_enforce_risk_level, e.chr_enforce_risk_photo";
    $sql_Notcomplete .= ", chr_enforce_detail, e.chr_remarks, e.chr_file_name_1, e.chr_file_name_2, e.chr_file_name_3, e.chr_enforce_risk_measure, CASE IFNULL(e.bool_end,0) WHEN 1 THEN '是' END AS 'bool_end'";
    $sql_Notcomplete .= ", e.chr_file_name_4, e.chr_old_file_name_1, e.chr_old_file_name_2, e.chr_old_file_name_3, e.chr_old_file_name_4, CASE IFNULL(e.bool_counter,0) WHEN 1 THEN '是' END AS 'bool_counter'";
    $sql_Notcomplete .= ", f.chr_ename AS 'follower', e.date_penalty_ticket, e.chr_penalty_no, e.decimal_penalty_amount, e.chr_enforce_risk_readLicense";
    $sql_Notcomplete .= " FROM tbl_law_enforcement_dept_detail e LEFT JOIN tbl_user u ON e.int_branch = u.int_id";
    $sql_Notcomplete .= " LEFT JOIN tbl_user f ON e.int_followed_by = f.int_id ";
    //if($_SESSION[dept] == 2){
    //	$sql_Notcomplete .= " LEFT JOIN tbl_district dst ON dst.int_id = u.int_district ";
    //	$sql_Notcomplete .= " LEFT JOIN tbl_dis_group zone ON zone.int_id = dst.int_dis_group ";
    //}
    $sql_Notcomplete .= " WHERE (IFNULL(e.bool_end,0) = 0 OR DATE_ADD(DATE(e.datetime_input_date), INTERVAL " . $show_Entry_Months . " MONTH) >= DATE(NOW()))";
    if ($_SESSION[dept] == 2) {
        $sql_Notcomplete .= " AND e.int_branch = '$_SESSION[user_id]' ";
    }
    /*
    if($_SESSION[dept] == 2) {

        if($record_m[0]>0){
            $sql_Notcomplete .= " AND zone.int_manager = ".$_SESSION['user_id'];
        }else{
            $sql_Notcomplete .= " AND e.int_branch = ".$_SESSION['user_id'];
        }
    }
    */
    if ($branch <> 'A')
        $sql_Notcomplete .= " AND e.int_branch = " . $branch;

    if ($enfDept <> 'A') {
        $sql_Notcomplete .= " AND e.chr_enforce_dept = '";
        switch ($enfDept) {
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
            case 6:
                $sql_Notcomplete .= "五常部'";
                break;
        }
    }

    if ($riskLvl <> 'A') {
        $sql_Notcomplete .= " AND e.chr_enforce_risk_level IN (";
        $aryRiskLvl = Array();

        if (strpos($riskLvl, '1') !== false) {
            $aryRiskLvl[] = "'1. 低'";
        }
        if (strpos($riskLvl, '2') !== false) {
            $aryRiskLvl[] = "'2. 中'";
        }
        if (strpos($riskLvl, '3') !== false) {
            $aryRiskLvl[] = "'3. 高'";
        }
        if (strpos($riskLvl, '4') !== false) {
            $aryRiskLvl[] = "'4. 極高'";
        }

        $sql_Notcomplete .= implode(',', $aryRiskLvl) . ")";
    }

    if ($read <> 'A') {
        $sql_Notcomplete .= " AND IFNULL(e.bool_counter,0) = '" . $read . "'";
    }

    if ($caseClose <> 'A') {
        $sql_Notcomplete .= " AND IFNULL(e.bool_end,0) = '" . $caseClose . "'";
    }

    $sql_Notcomplete .= " ORDER BY bool_end, bool_counter, e.chr_enforce_case_num desc; ";
    //echo  $sql_Notcomplete;
    $result_Notcomplete = mysqli_query($con, $sql_Notcomplete) or die($sql_Notcomplete);
    $recordCount = 1;

    if (mysqli_num_rows($result_Notcomplete) == 0) {
        echo "<table width=\"994px\"><tr><td align=\"center\"><font size=5 color=red>沒有紀錄 !!</font></td></tr>";
    } else {
    ?>

    <table width="994" border="1" style="border-collapse:collapse;" borderColor="black" cellspacing="0" cellpadding="3">
        <input type="hidden" id="updateID" name="updateID" value=""/>
        <input type="hidden" id="update_ptDate" name="update_ptDate" value=""/>
        <input type="hidden" id="update_ptNum" name="update_ptNum" value=""/>
        <input type="hidden" id="update_pAmt" name="update_pAmt" value=""/>
        <input type="hidden" id="updateCheck" name="updateCheck" value="0"/>
        <tr bgcolor="#CCFFFF">
            <td width="22" align="center" class="block"><b>#</b></td>
            <td width="34" align="center" class="block"><b>巡查<br>編號</b></td>
            <td width="67" align="center" class="block"><b>巡查日期</b></td>
            <td width="34" align="center" class="block"><b>分店</b></td>
            <td width="34" align="center" class="block"><b>執法<br>部門</b></td>
            <td width="67" align="center" class="block"><b>接待同事</b></td>
            <td width="67" align="center" class="block"><b>違規事項</b></td>
            <td width="67" align="center" class="block"><b>檢控可能</b></td>
            <td width="34" align="center" class="block"><b>有否<br>拍照</b></td>
            <td width="75" align="center" class="block"><b>有否度位<br>/取樣版</b></td>
            <td width="47" align="center" class="block"><b>查閱BR</b></td>
            <td width="100" align="center" class="block"><b>官員提及事項</b></td>
            <td width="73" align="center" class="block"><b>上傳<br>文件</b></td>
            <td width="34" align="center" class="block"><b>已閱</b></td>
            <td width="34" align="center" class="block"><b>已結</b></td>
            <td width="75" align="center" class="block"><b>跟進資料<br>輸入</b></td>
        </tr>
        <?
        $canEdit = ($_SESSION[dept] == 6 || $_SESSION[dept] == 19 || $_SESSION['user_id'] == 122) ? true : false;
        }
        $bgcolor = "#CCFFFF";
        ?>

        <?
        while ($record_Notcomplete = mysqli_fetch_array($result_Notcomplete)) {
            $risklvl = substr($record_Notcomplete[chr_enforce_risk_level], 0, strpos($record_Notcomplete[chr_enforce_risk_level], '.'));
            $bgcolor = $risklvl > 1 ? "#FFFF99" : (($recordCount % 2 == 0) ? "#DDDDDD" : "");
            ?>
            <tr>
                <td align="center" bgcolor="<?= $bgcolor; ?>">
                    <b><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . $recordCount . ($risklvl >= 3 ? "</font>" : ""); ?></b>
                </td>
                <td align="center"
                    bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . $record_Notcomplete[chr_enforce_case_num] . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td align="center"
                    bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . $record_Notcomplete[datetime_input_date] . '(' . $record_Notcomplete[chr_enforce_time] . ')' . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . "#" . $record_Notcomplete[branchCode] . ' - ' . $record_Notcomplete[branchName] . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td align="center"
                    bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . $record_Notcomplete[chr_enforce_dept] . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td align="center"
                    bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . $record_Notcomplete[chr_shopStaff] . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . $record_Notcomplete[chr_enforce_detail] . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . $record_Notcomplete[chr_enforce_risk_level] . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td align="center"
                    bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . str_replace("無", "---", $record_Notcomplete[chr_enforce_risk_photo]) . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td align="center"
                    bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . str_replace("無", "---", $record_Notcomplete[chr_enforce_risk_measure]) . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td align="center"
                    bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . str_replace("無", "---", $record_Notcomplete[chr_enforce_risk_readLicense]) . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td bgcolor="<?= $bgcolor; ?>" chr_remarks='<?= nl2br($record_Notcomplete[chr_remarks]); ?>'
                    onmouseover="var _a = popupPanel(this); overlib(_a,FULLHTML,HAUTO,OFFSETX, 0,OFFSETY, -10,FOLLOWMOUSE,'off');"
                    target="leftFrame"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . mb_substr($record_Notcomplete[chr_remarks], 0, 5, 'big5') . (mb_strlen($record_Notcomplete[chr_remarks], 'big5') > 5 ? "..." : "") . ($risklvl >= 3 ? "</font>" : ""); ?></td>

                <td bgcolor="<?= $bgcolor; ?>">
                    <table width="100%">
                        <?
                        if (TRIM($record_Notcomplete[chr_file_name_1]) != '') {
                        ?>
                        <tr>
                            <!--           <td align="right">1:</td>  -->
                            <td align="left"><a
                                        href="LawEnforcementRecords/<?= $record_Notcomplete[chr_file_name_1]; ?>"
                                        target="_blank">
                                    <?
                                    //							=$record_Notcomplete[chr_old_file_name_1]."</a></td>";}
                                    echo "附件1</a></td></tr>";
                                    }
                                    ?>
                                    <?
                                    if (TRIM($record_Notcomplete[chr_file_name_2]) != '') {
                                    ?>
                        <tr>
                            <!--          <td align="right">2:</td>  -->
                            <td align="left"><a
                                        href="LawEnforcementRecords/<?= $record_Notcomplete[chr_file_name_2]; ?>"
                                        target="_blank">

                                    <?
                                    //							=$record_Notcomplete[chr_old_file_name_2]."</a></td>";}
                                    echo "附件2</a></td></tr>";
                                    }
                                    ?>


                                    <?
                                    if (TRIM($record_Notcomplete[chr_file_name_3]) != '') {
                                    ?>
                        <tr>
                            <!--          <td align="right">3:</td>  -->
                            <td align="left"><a
                                        href="LawEnforcementRecords/<?= $record_Notcomplete[chr_file_name_3]; ?>"
                                        target="_blank">
                                    <?
                                    //							=$record_Notcomplete[chr_old_file_name_3]."</a></td>";}
                                    echo "附件3</a></td></tr>";
                                    }
                                    ?>


                                    <?
                                    if (TRIM($record_Notcomplete[chr_file_name_4]) != '') {
                                    ?>
                        <tr>
                            <!--          <td align="right">4:</td>  -->
                            <td align="left"><a
                                        href="LawEnforcementRecords/<?= $record_Notcomplete[chr_file_name_4]; ?>"
                                        target="_blank">
                                    <?
                                    //							=$record_Notcomplete[chr_old_file_name_4]."</a></td>";}
                                    echo "附件4</a></td></tr>";
                                    }
                                    ?>

                    </table>
                </td>
                <td align="center"
                    bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . $record_Notcomplete[bool_counter] . ($risklvl >= 3 ? "</font>" : ""); ?></td>
                <td align="center"
                    bgcolor="<?= $bgcolor; ?>"><? echo ($risklvl >= 3 ? "<font color=\"red\">" : "") . $record_Notcomplete[bool_end] . ($risklvl >= 3 ? "</font>" : ""); ?></td>

                <td align="center" bgcolor="<?= $bgcolor; ?>">
                    <input type="button" name="extraInfo<?= $record_Notcomplete[int_id]; ?>"
                           onClick="show('<?= $record_Notcomplete[int_id]; ?>','<?= $record_Notcomplete[int_id]; ?>')"
                           id="extraInfo<?= $record_Notcomplete[int_id]; ?>" value="資料"/>
                </td>

            </tr>
            <? $recordCount++;
        } ?>
    </table>
</form>

<br><br>
<br><br>
<br><br>
<br><br>
<table width="994" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td><img src="images/TaiHing_23.jpg" width="994" height="49"></td>
    </tr>
</table>
</body>
</html>