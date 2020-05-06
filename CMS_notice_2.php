<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//  session_start();
//  if (!($_SESSION[authenticated])) {
//    $_SESSION['status'] = 'neverLogin';
//    header('Location: index.php');
//  }

//檢查是否登錄,是否管理員
require("check_login.php");

$email_list = Array(
    '1' => 'kbs@kingbakery.com.hk;',
    '2' => 'intranet@kingbakery.com.hk;',
    '3' => 'kbo@kingbakery.com.hk;',
    '4' => '',
    '5' => '',
    '6' => '',
);


//echo sys_get_temp_dir();
$timestamp = gettimeofday('sec');
require($DOCUMENT_ROOT . "connect.inc");

if (isset($_POST[Submit])) {
    $is_error = false;
    $error_mgs = '';

    $file_ext = strtolower(end(explode('.', $_FILES['notice']['name'])));
    $file_name = "./notice/" . date('YmdHis', $timestamp) . "." . $file_ext;
    if (!move_uploaded_file($_FILES['notice']['tmp_name'], $file_name)) {
        $error_mgs = "上傳失敗。";
    }
    $file_ext = strtolower(end(explode('.', $_FILES['notice_first']['name'])));
    $file_first = "./notice2/" . date('YmdHis', $timestamp) . "." . $file_ext;
    if (!move_uploaded_file($_FILES['notice']['tmp_name'], $file_name)) {
        $error_mgs = "上傳失敗。";
    }

    if (!$is_error) {
        $sql = "UPDATE tbl_notice SET ";
        $sql .= "txt_name = '" . $_POST[txt_name] . "' ";
        $sql .= ",txt_path = '" . $file_name . "' ";
        $sql .= ",first_path = '" . $file_first . "' ";
        if ($_POST[txt_last_time]) {
            $sql .= ",date_last = '" . $_POST[txt_last_time] . "' ";
        }
        $sql .= ",int_user = '" . $_SESSION['user_id'] . "' ";
        $sql .= ",date_modify = '" . date("Y-m-d") . "' ";
        $sql .= ",date_delete = '2000-01-01' ";
        $sql .= ",int_dept = '" . $_POST[dept] . "' ";
        $sql .= "WHERE int_id = " . $_REQUEST['intid'];

        $result = mysqli_query($con, $sql) or die($sql);

        $mail = new PHPMailer();
        $mail->Host = "corpmail1.netvigator.com";
        $mail->Port = 25;
        $mail->IsSMTP();


        $mail->FromName = "kbh-intranet";
        $mail->From = "intranet@kingbakery.com.hk";
        $mail->SMTPDebug = 1;

        $mail->AddBCC("intranet@kingbakery.com.hk");

        $mail->CharSet = "utf-8";
        $mail->Encoding = "base64";
        $mail->WordWrap = 50;                                 // set word wrap to 50 characters
        $mail->IsHTML(true);

        $mail->AddAttachment("D:\\intranet\\wwwroot2\\$file_name");
        $mail->Subject = "$_POST[txt_name]";
        $mail->Body = "$_POST[txt_name]";
        $mail->AltBody = "$_POST[txt_name]";


        $brand = explode(',', $_REQUEST[brand]);
        foreach ($brand as $b) {
            if ($email_list[$b])
                $mail->AddAddress($email_list[$b]);
            $sql = "INSERT INTO tbl_notice_v_brand VALUES(NULL, $b, $_REQUEST[intid])";
            mysqli_query($con, $sql) or die($sql);
        }
        if (!$mail->Send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            exit;
        }


        echo "<script>alert(\" 原始文檔 （" . $file_name . "） \\n\\n 顯示文檔（" . $file_first . "）\\n\\n 已成功上載\")</script>";
        echo "<script>document.location.href='CMS_notice.php';</script>";
    } else {
        echo iconv('big5', 'utf-8', $error_mgs);
        //if ($_FILES['uploadedfile']['error'] == 1) echo "<br>檔案大小限 2MB 以下";
    }
}
function eregi($arg)
{
    //echo $arg;
}

?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>內聯網 - 後勤系統</title>
    <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="js/json2.js" type="text/javascript"></script>
    <script src="js/select.js" type="text/javascript"></script>
    <script type="text/javascript" src="js/MultipleSelect/multiple-select.js"></script>
    <script src="js/My97DatePicker/WdatePicker.js"></script>

    <link href="css/select.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="js/MultipleSelect/multiple-select.css"/>
    <link rel="stylesheet" type="text/css" href="css/checkbox-style.css"/>
    <link rel="stylesheet" type="text/css" href="css/MultipleSelectList-checkbox.css"/>

    <link href="My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
    <style>
        td {
            padding: 4px;
        }
    </style>
    <script language="javascript" type="text/javascript">
        $(function () {
            $("#brand").multipleSelect({
                allSelected: "已選擇所有",
                selectAllText: "全部",
                ellipsis: true,
                selectAll: true,
                countSelected: '已選擇 # 項',
                minimumCountSelected: 10,
                multiple: true,
                multipleWidth: 130,
                onClose: function () {
                    $("#brandVal").val($("#brand").multipleSelect('getSelects').join(','));
                }
            });
            $("#brandVal").val($("#brand").multipleSelect('getSelects').join(','));
        });

        function check_sub() {
            var name = document.getElementById("txt_name").value;
            var lasttime = document.getElementById("txt_last_time").value;
            var fileurl = document.getElementById("uploadedfile").value;
            var times = lasttime.split("-");
            var year = times[0];
            var month = times[1];
            var day = times[2];
            var date = new Date();
            var lastyear = date.getYear() + 3;
            if (new Date(year, month, day) < Date() || new Date(year, month, day) > new Date(lastyear, '12', '31')) {
                alert("日期輸入有誤");
                return false;
            }
            if (name.length == 0 || lasttime.length == 0 || fileurl.length == 0) {
                alert("資料尚未錄入完整");
                return false;
            } else if (changeType(fileurl)) {
                alert("顯示文檔只能為PDF文檔！");
                return false;
            } else {
                return true;
            }
        }

        function loado() {
            var date = new Date();
            var lastyear = date.getYear() + 1;
            document.getElementById("loado").innerHTML = date.getYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
        }

        function changeType(objFile) {

            var objtype = objFile.substring(objFile.lastIndexOf(".")).toLowerCase();

            var fileType = new Array(".pdf");

            for (var i = 0; i < fileType.length; i++) {

                if (objtype == fileType[i]) {

                    return false;

                    break;

                }

            }

            return true;

        }
    </script>
</head>

<body onLoad="loado()">
<form enctype="multipart/form-data" action="" onSubmit="return check_sub()" method="POST" name="upload" id="upload">
    <table border="1">
        <tr>
            <td>通告編號：</td>
            <td><?php echo $_REQUEST[intno]; ?></td>
        </tr>
        <tr>
            <td>主旨：</td>
            <td><input name="txt_name" type="text" id="txt_name" size="31"></td>
        </tr>
        <tr>
            <td>部門</td>
            <td>
                <select name="dept">
                    <?
                    $sql = "SELECT * FROM tbl_dept WHERE int_id IN($_SESSION[dept]);";
                    $result = mysqli_query($con, $sql) or die($sql);
                    while ($record = mysqli_fetch_assoc($result)) { ?>
                        <option value="<?= $record[int_id] ?>"><?= $record[txt_dept] ?></option>
                    <? } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>到期時間：</td>
            <td>
                <input name="txt_last_time" type="text" id="txt_last_time" size="26"
                       onClick="WdatePicker({dateFmt:'yyyy/MM/dd'})" readonly>
                <font style="color:#F00; font-size:12px">*格式：2018/01/01</font>
            </td>
        </tr>
        <tr>
            <td>可視品牌：</td>
            <td>
                <select id="brand" style="width:300px;" multiple>
                    <?php
                    $sql = "SELECT * FROM tbl_order_z_area ORDER BY int_sort; ";
                    $brand = mysqli_query($con, $sql);
                    while ($d = mysqli_fetch_assoc($brand)) {
                        ?>
                        <option value="<?= $d['int_id'] ?>" selected><?= $d['chr_name'] ?></option>
                    <?php } ?>
                </select>
                <input name="brand" id="brandVal" value="" type="hidden"/>
            </td>
        </tr>
        <tr>
            <td colspan="2">選擇上傳之檔案(顯示文檔PDF)</td>
        </tr>

        <tr>
            <td colspan="2"><input name="notice" type="file" id="notice" size="28" accept="application/pdf" single/>
            </td>
        </tr>

        <tr>
            <td colspan="2">選擇上傳之檔案(原始文檔)　(容許格式：doc, docx, xls, xlsx)</td>
        </tr>
        <tr>
            <td colspan="2"><input name="notice_first" type="file" id="notice_first" size="28"/></td>
        </tr>
    </table>
    <p>上傳檔案請按<font style="color:#F00; font-size:16px"><B>一次</B></font>&quot;提交&quot;按鈕便可。 <font
                style="color:#F00; font-size:16px"><B>請勿按多過一次&quot;提交&quot;</B></font>, 因為上傳檔案大小會影響上傳時間。 請&quot;提交&quot;後耐心等候上傳完成。上傳時間大慨2-3分鐘。<br>
        <input type="submit" name="Submit" value="提交"/>
        <input name="Back" type="button" id="Back" onClick="history.go(-1);" value="返回">
        <input type="hidden" name="MAX_FILE_SIZE" value="20000000"/>
</form>

</body>
</html>