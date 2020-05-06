<?php

session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'itsupport.php';
    header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");

$timestamp = gettimeofday("sec") + 28800;
$sel = 'all';
if (!empty($_GET['sel'])) {
    $sel = $_GET['sel'];
}

$show_completed_days = 14;
if ($_POST['action'] == 'delete') {
    $sql = "UPDATE tbl_itsupport SET int_status = 4, last_update_date = NOW(), last_update_user = $_SESSION[user_id] WHERE int_id = $_POST[id]";
    mysqli_query($con, $sql) or die($sql);
    exit();
}


if (isset($_POST['submit'])) {
    $submit = $_POST['submit'];
    if ($submit == "輸入") {

        foreach ($_FILES["uploadfile"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                print_r($_FILES);

                $filename = $_FILES["uploadfile"]['tmp_name'][$key];
                $extension = end(explode(".", $_FILES["uploadfile"]['name'][$key]));
                $newfilename = $_SESSION['user_id'] . '-' . date('ymdHis', $timestamp) . '-' . ($key + 1) . '.' . $extension;
                if (move_uploaded_file($filename, "./itsupport/" . $newfilename)) {
                    $uploadFile = $newfilename;
                }
            }
        }


        $sql = "SELECT CONCAT(chr_prefix, int_no) as int_no FROM tbl_itsupport_no";
        $result = mysqli_query($con, $sql) or die($sql);
        $record = mysqli_fetch_assoc($result);

        $pj_no = $record['int_no'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_id = $_SESSION[user_id];
        $pj_item = $_POST['sel_project_type'];
        $pj_detail = $_POST['sel_project'];
        $pj_impo = $_POST['sel_Impo'];
        $machine_code = $_POST['txt_number'];
        $remark = $_POST['textarea'];
        $status = 1;

        $insertSQL = "INSERT INTO tbl_itsupport(chr_no,chr_ip,int_user,int_itsupport_item,int_itsupport_detail,int_important,int_status,report_date,chr_machine_code,chr_other,chr_pic) VALUES
		('$pj_no', '$ip_address', '$user_id', '$pj_item', '$pj_detail', '$pj_impo', $status, NOW(), '$machine_code', '$remark', '$uploadFile') ";

        $insert_Result = mysqli_query($con, $insertSQL) or die($insertSQL);
        $sql_update = "UPDATE tbl_itsupport_no SET int_no = int_no + 1";
        $update_Result = mysqli_query($con, $sql_update) or die($sql_update);

        if ($insert_Result) {
            echo '<script language="javascript">alert("成功發出IT求助#' . $row['chr_case_num'] . '"); window.location.href ="itsupport.php"; </script>';
        }
    } else if ($submit == "type") {

        $pid = $_POST['pid'];

        $select_sql = "select * from  tbl_itsupport_item where int_loc_id = $pid ORDER BY int_sort";
        $select_result = mysqli_query($con, $select_sql) or die($select_sql);

        $seloption = "<option value='0'>請選擇</option>";
        $i = 0;
        while ($select_record = mysqli_fetch_array($select_result)) {
            $seloption .= "<option value='$select_record[int_id]'>$select_record[chr_name]</option>";
            $i++;
        }
        if ($i == 0) {
            $seloption .= "<option value='10000'>另提供</option>";
        }
        echo $seloption;
        exit;

    } else if ($submit == "project") {
        $pid = $_POST['pid'];

        $select_sql = "select * from  tbl_itsupport_detail where int_item_id = $pid ORDER BY int_sort";
        $select_result = mysqli_query($con, $select_sql) or die($select_sql);

        $seloption = "<option value='0'>請選擇</option>";
        $i = 0;
        while ($select_record = mysqli_fetch_array($select_result)) {
            $seloption .= "<option value='$select_record[int_id]'>$select_record[chr_name]</option>";
            $i++;
        }
        if ($i == 0) {
            $seloption .= "<option value='10000'>另提供</option>";
        }
        echo $seloption;
        exit;
    }


}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網</title>
    <style type="text/css">
        body, td, th {
            font-size: small;
        }

        input[disabled] {
            background-color: #7F7F7F !important;
        }
    </style>
    <script type="text/javascript" src="js/jquery-1.4.1.js"></script>
    <script type="text/javascript" src="js/wbox-min.js"></script>
    <script type="text/javascript" src="LawEnforcementRecords/js/fbw-ajax.js"></script>
    <script type="text/javascript" src="js/overlib.js"></script>
    <link href="wbox/wbox.css" rel="stylesheet" type="text/css"/>
    <style>
        <!--
        #wBoxContent > iframe {
            width: 500px !important;
            height: 600px !important;
        }

        -->
    </style>
    <script type="text/javascript">

        function confInsertSubmit() {
            document.getElementById("insertSubmit").disabled = true;


            var checkType = true;
            if ($('#inputDate').val() == '') {
                alert('請選擇日期！');
                $('#inputDate').focus();
                document.getElementById("insertSubmit").disabled = false;
                return false;
            }
            if (document.getElementById('sel_Impo').selectedIndex == 0) {
                alert('請選擇「緊急性」選項');
                document.getElementById("insertSubmit").disabled = false;
                return false;
            }


            if (document.getElementById('sel_project_type').selectedIndex == 0) {

                alert('請選擇「位置」選項');
                document.getElementById("insertSubmit").disabled = false;
                return false;
            }

            if (document.getElementById('sel_project').selectedIndex == 0) {

                alert('請選擇「維修項目」選項');
                document.getElementById("insertSubmit").disabled = false;
                return false;
            }

            if (document.getElementById('sel_help').selectedIndex == 0) {
                checkType = false;
            }

            if (!checkType) {

                checkType = true;
                if ($("#textarea").val() == "") {
                    checkType = false;
                }

            }

            if (!checkType) {
                alert('請選擇「求助事宜」或「其他資料提供」選項！');
                document.getElementById("insertSubmit").disabled = false;
                return false
            }


            var answer = confirm("確認提交資料?(如有文件檔，按\"是\"後請耐心等待)");
            if (!answer) {
                document.getElementById("insertSubmit").disabled = false;
                return false;
            }
        }

        function upFile(btnId) {
            if (document.getElementById('uploadfile[]').value != '')
                document.getElementById('btnFile' + btnId).style.backgroundColor = "#00FF00";
            return false;
        }

        function clearFileInputField(tagId) {
            document.getElementById('div_file' + tagId).innerHTML = document.getElementById('div_file' + tagId).innerHTML;
            document.getElementById('btnFile' + tagId).style.backgroundColor = "";
        }


        /*新加入 2015 01 05*/
        function SelProject_Type() {
            $.ajax({
                type: "post",
                url: "itsupport.php",
                data: "submit=project&pid=" + $("#sel_project_type").val(),
                success: function (data) {
                    $("#sel_project").empty();
                    $(data).appendTo("#sel_project");
                },
                error: function (resp) {
                    console.log(resp);
                }
            });
        }

        function SelCheng(obj) {
            if ($(obj).val() == "2") {
                $("#sellogistics").show();
                $("#branch").hide();
            } else {
                $("#sellogistics").hide();
                $("#branch").show();
            }

            $.ajax({
                type: "post",
                url: "itsupport.php",
                data: "submit=project_type&pid=" + $(obj).val(),
                success: function (data) {
                    $("#sel_project_type").empty();
                    $(data).appendTo("#sel_project_type");
                },
                error: function (resp) {
                    console.log(resp);
                }
            });
        }

        function _func_delete(id, code) {
            var answer = confirm("確定取消編號為【" + code + "】的維修項目嗎？");
            if (!answer) {
                return false;
            } else {
                var data = {action: 'delete', id: id};
                $.post('itsupport.php', data, function (resp) {
                    alert("求助已刪除！");
                    location.reload();
                });
            }
        }

        function _func_show(id, type) {
            var title = type == 1 ? "未完成處理" : "最近<?=$show_completed_days?>天內完成處理之申請";
            var wBox = $("#look")
                .wBox({
                    requestType: "iframe",
                    title: title,
                    width: 200,
                    height: 50,
                    target: "itsupport_handle.php?id=" + id + "&type=" + type
                });
            wBox.showBox();
        }

    </script>

</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php include("head.php"); ?>

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

<div align="center" style="width:994px">
    <h1><u>維修項目</u></h1>
</div>
<div align="center" style="width:994px">
    <h2>新輸入</h2>
</div>

<form enctype="multipart/form-data" name="form_CaseInsert" id="form" action="itsupport.php" method="post"
      onSubmit="return confInsertSubmit();">

    <table border="1" style="border-collapse:collapse;" borderColor="#ccc" cellspacing="0" cellpadding="0"
           width="994px">


        <tr>
            <td align="center" bgcolor="#CCFFFF" width="10%">日期</td>
            <td align="center" bgcolor="#CCFFFF" width="10%">分店/部門</td>
            <td align="center" bgcolor="#CCFFFF" width="8%">緊急性</td>
            <td align="center" bgcolor="#CCFFFF" width="9%">器材</td>
            <td align="center" bgcolor="#CCFFFF" width="9%">求助事宜</td>
            <td align="center" bgcolor="#CCFFFF" width="10%">機器號碼#</td>
            <td align="center" bgcolor="#CCFFFF" width="13%">其他資料提供</td>
            <td align="center" bgcolor="#CCFFFF" width="12%">上傳文檔(如有)</td>
            <td align="center" bgcolor="#CCFFFF" width="10%">&nbsp;</td>
        </tr>
        <tr>
            <td height="30" align="center"><?php echo date('Y-m-d', $timestamp); ?></td>
            <td height="30" align="center"><?= $_SESSION['user'] ?></td>
            <td height="30" align="center" valign="middle">
                <select name="sel_Impo" id="sel_Impo" style="width: 95%">
                    <option value="0">請選擇</option>
                    <option value="3">高</option>
                    <option value="4">中</option>
                    <option value="5">低</option>
                </select>
            </td>
            <td height="30" align="center" valign="middle">
                <select name="sel_project_type" id="sel_project_type" style="width: 95%" onChange="SelProject_Type();">
                    <option value="0">請選擇</option>
                    <?php
                    $sql_project_type = "SELECT chr_name,int_id FROM tbl_itsupport_item ORDER BY int_sort";
                    $result_project_type = mysqli_query($con, $sql_project_type) or die($sql_project_type);
                    while ($record_project_type = mysqli_fetch_array($result_project_type)) {
                        echo "<option value='" . $record_project_type[int_id] . "'>$record_project_type[chr_name]</option>";
                    }
                    ?>
                </select>
            </td>
            <td height="30" align="center" valign="middle">
                <select name="sel_project" id="sel_project" style="width: 95%" onChange="SelProject();">
                    <option value="0">請選擇</option>
                </select>
            </td>


            <td height="30" align="center"><input type="text" name="txt_number" id="txt_number" style="width:90%"/></td>


            <td height="30" align="center">
                <table width="100%" height="100%">
                    <tr>
                        <td width="50%"><textarea name="textarea" id="textarea"
                                                  style="height: 98%; width: 90%"></textarea></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td align="right" bgcolor="#EEEEEE">
                            <div id="div_file1" style="position: relative">
                                <input id="uploadfile[]" onChange="upFile('1')" name="uploadfile[]" type="file"
                                       style="position:absolute;filter:alpha(opacity=0);  -moz-opacity:0;         /*火狐*/opacity:0;  width:50px;"/>
                                <input type="button" id="btnFile1" name="btnFile1" style="width:50px" value="相片1"/>
                            </div>
                        </td>
                        <td align="left" bgcolor="#EEEEEE">
                            <input type="button" id="clear1" name="clear1" onClick="clearFileInputField('1');"
                                   value="清除"/>
                        </td>
                    </tr>
                </table>
            </td>
            <td height="30" align="center">
                <input name="submit" value="輸入" type="hidden"/>
                <input id="insertSubmit" type="submit" value="輸入" style="background-color:green;color:white;"/>
            </td>
        </tr>
    </table>
</form>

<br>
<br>

<div align="center" style="width:994px">
    <h2>未完成處理</h2>
</div>
<form name="form_CaseUpdate" id="form2" action="itsupport.php" method="post">

    <?php
    if ($_SESSION[type] == 1 || $_SESSION[type] == 3) {
        $addSQL = 'OR int_user >= 1';
    }

    $sql = "SELECT T0.int_id as pj_id, T0.chr_no, T0.int_important, T0.int_status, T0.chr_machine_code,
	DATE(T0.report_date) as date, DATEDIFF(CURDATE(), DATE(report_date)) as datediff, 
	chr_other, chr_pic, T1.chr_name as itm, T2.chr_name as dtl, T3.txt_name as usr
FROM tbl_itsupport T0
	LEFT JOIN tbl_itsupport_item T1 ON T0.int_itsupport_item = T1.int_id
	LEFT JOIN tbl_itsupport_detail T2 ON T0.int_itsupport_detail = T2.int_id
    LEFT JOIN tbl_user T3 ON T0.int_user = T3.int_id
WHERE int_status = 1 AND (int_user = $_SESSION[user_id] $addSQL)
ORDER BY T0.report_date DESC; ";

    $result = mysqli_query($con, $sql);
    $recordCount = 1;
    $no = 0;
    if (mysqli_num_rows($result) == 0) { ?>
        <table width="994px" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td align="center"><font size="5" color="red">沒有紀錄 !!</font></td>
            </tr>
        </table>
    <?php } else { ?>

    <table width="994px" border="1" style="border-collapse:collapse;" borderColor="#ccc" cellspacing="0"
           cellpadding="0">
        <input type="hidden" id="updateID" name="updateID" value=""/>
        <tr bgcolor="#CCFFFF">

            <td align="center" width="3%"><b>#</b></td>
            <td align="center" width="6%"><b>編號</b></td>

            <td align="center" width="10%"><b>完成日期</b></td>

            <td align="center" width="8%"><b>分店/部門</b></td>
            <td align="center" width="5%"><b>緊急性</b></td>
            <td align="center" width="12%"><b>器材</b></td>
            <td align="center" width="14%"><b>求助事宜</b></td>
            <td align="center" width="7%"><b>#機器號碼</b></td>
            <td align="center" width="10%"><b>其他資料提供</b></td>
            <td align="center" width="6%"><b>上傳文檔</b></td>


            <td align="center" width="18%"><b></b></td>


        </tr>

        <?php }
        while ($project = mysqli_fetch_array($result)) {
            $no++;
            $bgcolor = ($recordCount % 2 == 0) ? "#DDDDDD" : "#ffffff";
            ?>

            <tr bgcolor="<?= $bgcolor; ?>">
                <td rowspan="1" align="center"><b><?= $recordCount ?></b></td>
                <td align="center"><?= $project[chr_no] ?></td>
                <td align="center" height="25">
                    <?= $project[date] ?>
                    (<font color="red"><?= $project[datediff] ?></font>)
                </td>
                <td align="left" height="25"><?= $project[usr] ?></td>
                <td align="center"><?php
                    switch ($project[int_important]) {
                        case "3":
                            echo "高";
                            break;
                        case "4":
                            echo "中";
                            break;
                        default:
                            echo "低";
                    }

                    ?>
                </td>
                <?php
                $itm = $project['itm'] == '' ? '另提供' : $project['itm'];
                $dtl = $project['dtl'] == '' ? '另提供' : $project['dtl'];
                ?>
                <td align="center"><?= $itm ?></td>
                <td align="center"><?= $dtl ?></td>

                <td align="center"><?= $project[chr_machine_code] ?></td>
                <td chr_remarks='<?= $project[chr_other] ?>'
                    onMouseOver="var _a = popupPanel(this); overlib(_a,FULLHTML,HAUTO,OFFSETX, 0,OFFSETY, -10,FOLLOWMOUSE,'off');"
                    target="leftFrame">
                    <?php echo mb_substr(htmlspecialchars_decode($project[chr_other]), 0, 7, 'big5') . (mb_strlen(htmlspecialchars_decode($project[chr_other]), 'big5') > 7 ? "..." : ""); ?>
                </td>
                <td bgcolor="<?= $bgcolor; ?>">
                    <table>
                        <tr><?php if (TRIM($project[chr_pic]) != '') { ?>
                                <td align="right">1:</td>
                                <td align="left"><a href="itsupport/<?= $project[chr_pic]; ?>"
                                                    target="_blank"><?= "附檔</a></td>";
                            } ?></tr>


                    </table>
                </td>

                <td align="center" style="padding:5px;">
                    <?php if ($_SESSION[type] == 2 || $_SESSION[type] == 3) { ?>
                        <button type="button" onclick="_func_show('<?= $project[pj_id] ?>', '1')"
                                style="background-color:#ADFFAD;">補充資料
                        </button>
                    <?php } ?>


                    <button type="button" onclick="_func_delete('<?= $project[pj_id] ?>', '<?= $project[chr_no] ?>');"
                            style="background-color:#FFADAD;">刪除
                    </button>
                </td>

            </tr>
            <?php $recordCount++;
        } ?>
    </table>
</form>

<br>
<br>

<div align="center" style="width:994px">
    <h2>最近<?= $show_completed_days ?>天內完成處理之申請</h2>
</div>


<?php
if ($_SESSION[type] == 1 || $_SESSION[type] == 3) {
    $addSQL = 'OR int_user >= 1';
}

$sql = "SELECT T0.int_id as pj_id, T0.chr_no, T0.int_important, T0.int_status, T0.chr_machine_code,
	DATE(T0.report_date) as date, DATEDIFF(CURDATE(), DATE(report_date)) as datediff, 
	chr_other, chr_pic, T1.chr_name as itm, T2.chr_name as dtl, T3.txt_name as usr
FROM tbl_itsupport T0
	LEFT JOIN tbl_itsupport_item T1 ON T0.int_itsupport_item = T1.int_id
	LEFT JOIN tbl_itsupport_detail T2 ON T0.int_itsupport_detail = T2.int_id
    LEFT JOIN tbl_user T3 ON T0.int_user = T3.int_id
WHERE int_status = 99 AND (int_user = $_SESSION[user_id] $addSQL) AND datediff(CURDATE(), DATE(last_update_date)) <= $show_completed_days 
ORDER BY T0.last_update_date DESC; ";
$result = mysqli_query($con, $sql);
$recordCount = 1;

if (mysqli_num_rows($result) == 0) {
?>
<table width="994px" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td align="center"><font size=5 color=red>沒有紀錄 !!</font></td>
    </tr>
    <?php
    } else {
    ?>

    <table width="994px" border="1" style="border-collapse:collapse;" borderColor="#ccc" cellspacing="0"
           cellpadding="0">

        <tr bgcolor="#CCFFFF">

            <td align="center" width="3%"><b>#</b></td>
            <td align="center" width="6%"><b>編號</b></td>

            <td align="center" width="10%"><b>完成日期</b></td>

            <td align="center" width="8%"><b>分店/部門</b></td>
            <td align="center" width="5%"><b>緊急性</b></td>
            <td align="center" width="8%"><b>維修項目</b></td>
            <td align="center" width="10%"><b>求助事宜</b></td>
            <td align="center" width="7%"><b>#機器號碼</b></td>
            <td align="center" width="10%"><b>其他資料提供</b></td>
            <td align="center" width="6%"><b>上傳文檔</b></td>


            <td align="center" width="18%"><b></b></td>


        </tr>
        <?php
        }
        while ($project = mysqli_fetch_array($result)) {
            $bgcolor = ($recordCount % 2 == 0) ? "#DDDDDD" : "#ffFFFF";
            ?>

            <tr bgcolor="<?= $bgcolor; ?>">
                <td rowspan="1" align="center" style="padding:5px;"><b><?= $recordCount ?></b></td>
                <td align="center"><?= $project[chr_no] ?></td>
                <td align="center" height="25">
                    <?= $project[date] ?>
                    (<font color="red"><?= $project[datediff] ?></font>)
                </td>
                <td align="left" height="25"><?= $project[usr] ?></td>
                <td align="center"><?php
                    switch ($project[int_important]) {
                        case "3":
                            echo "高";
                            break;
                        case "4":
                            echo "中";
                            break;
                        default:
                            echo "低";
                    }

                    ?>
                </td>
                <?php
                $itm = $project['itm'] == '' ? '另提供' : $project['itm'];
                $dtl = $project['dtl'] == '' ? '另提供' : $project['dtl'];
                ?>
                <td align="center"><?= $itm ?></td>
                <td align="center"><?= $dtl ?></td>

                <td align="center"><?= $project[chr_machine_code] ?></td>
                <td chr_remarks='<?= $project[chr_other] ?>'
                    onMouseOver="var _a = popupPanel(this); overlib(_a,FULLHTML,HAUTO,OFFSETX, 0,OFFSETY, -10,FOLLOWMOUSE,'off');"
                    target="leftFrame">
                    <?php echo mb_substr(htmlspecialchars_decode($project[chr_other]), 0, 7, 'big5') . (mb_strlen(htmlspecialchars_decode($project[chr_other]), 'big5') > 7 ? "..." : ""); ?>
                </td>
                <td bgcolor="<?= $bgcolor; ?>">
                    <table>
                        <tr><?php if (TRIM($project[chr_pic]) != '') { ?>
                            <td align="right">1:</td>
                            <td align="left"><a href="itsupport/<?= $project[chr_pic]; ?>"
                                                target="_blank"><?= "附檔</a></td>";
                                    } ?>
                        </tr>
                    </table>
                </td>
                <td align="center" style="padding:5px;">
                    <button type="button" onclick="_func_show('<?= $project[pj_id] ?>', '2')"
                            style="background-color:#FFFFAD;">跟進資料
                    </button>
                </td>
            </tr>
            <?php $recordCount++;
        } ?>
    </table>


    <br>
    <br>

    <div align="center" style="width:994px">
        <h2>最近<?= $show_completed_days ?>天內<font color="red">取消</font>之申請</h2>
    </div>

    <?php
    if ($_SESSION[type] == 1 || $_SESSION[type] == 3) {
        $addSQL = 'OR int_user >= 1';
    }
    $sql = "SELECT T0.int_id as pj_id, T0.chr_no, T0.int_important, T0.int_status, T0.chr_machine_code,
	DATE(T0.report_date) as date, DATEDIFF(CURDATE(), DATE(report_date)) as datediff, 
	chr_other, chr_pic, T1.chr_name as itm, T2.chr_name as dtl, T3.txt_name as usr
FROM tbl_itsupport T0
	LEFT JOIN tbl_itsupport_item T1 ON T0.int_itsupport_item = T1.int_id
	LEFT JOIN tbl_itsupport_detail T2 ON T0.int_itsupport_detail = T2.int_id
    LEFT JOIN tbl_user T3 ON T0.int_user = T3.int_id
	WHERE int_status = 4 AND datediff(CURDATE(), DATE(last_update_date)) <= $show_completed_days 
		AND (int_user = $_SESSION[user_id] $addSQL)
	ORDER BY T0.last_update_date DESC; ";
    $result = mysqli_query($con, $sql) or die($sql);
    $recordCount = 1;


    if (mysqli_num_rows($result) == 0) {
    ?>
    <table width="994px" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center"><font size=5 color=red>沒有紀錄 !!</font></td>
        </tr>
        <?php
        } else {
        ?>

        <table width="994px" border="1" style="border-collapse:collapse;" borderColor="#ccc" cellspacing="0"
               cellpadding="0">
            <tr bgcolor="#CCFFFF">
                <td align="center" width="3%"><b>#</b></td>
                <td align="center" width="6%"><b>編號</b></td>

                <td align="center" width="10%"><b>取消日期</b></td>

                <td align="center" width="8%"><b>分店/部門</b></td>
                <td align="center" width="5%"><b>緊急性</b></td>
                <td align="center" width="8%"><b>維修項目</b></td>
                <td align="center" width="10%"><b>求助事宜</b></td>
                <td align="center" width="7%"><b>#機器號碼</b></td>
                <td align="center" width="10%"><b>其他資料提供</b></td>
                <td align="center" width="6%"><b>上傳文檔</b></td>

            </tr>


            <?php
            }
            while ($project = mysqli_fetch_array($result)) {
                $bgcolor = ($recordCount % 2 == 0) ? "#DDDDDD" : "#ffFFFF";
                ?>

                <tr bgcolor="<?= $bgcolor; ?>">
                    <td rowspan="1" align="center" style="padding:5px;"><b><?= $recordCount ?></b></td>
                    <td align="center"><?= $project[chr_no] ?></td>
                    <td align="center" height="25">
                        <?= $project[date] ?>
                        (<font color="red"><?= $project[datediff] ?></font>)
                    </td>
                    <td align="left" height="25"><?= $project[usr] ?></td>
                    <td align="center"><?php
                        switch ($project[int_important]) {
                            case "3":
                                echo "高";
                                break;
                            case "4":
                                echo "中";
                                break;
                            default:
                                echo "低";
                        }

                        ?>
                    </td>
                    <?php
                    $itm = $project['itm'] == '' ? '另提供' : $project['itm'];
                    $dtl = $project['dtl'] == '' ? '另提供' : $project['dtl'];
                    ?>
                    <td align="center"><?= $itm ?></td>
                    <td align="center"><?= $dtl ?></td>

                    <td align="center"><?= $project[chr_machine_code] ?></td>
                    <td chr_remarks='<?= $project[chr_other] ?>'
                        onMouseOver="var _a = popupPanel(this); overlib(_a,FULLHTML,HAUTO,OFFSETX, 0,OFFSETY, -10,FOLLOWMOUSE,'off');"
                        target="leftFrame">
                        <?php echo mb_substr(htmlspecialchars_decode($project[chr_other]), 0, 7, 'big5') . (mb_strlen(htmlspecialchars_decode($project[chr_other]), 'big5') > 7 ? "..." : ""); ?>
                    </td>
                    <td bgcolor="<?= $bgcolor; ?>">
                        <table>
                            <tr><?php if (TRIM($project[chr_pic]) != '') { ?>
                                <td align="right">1:</td>
                                <td align="left"><a href="itsupport/<?= $project[chr_pic]; ?>"
                                                    target="_blank"><?= "附檔</a></td>";
                                        } ?>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php $recordCount++;
            } ?>
        </table>


        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

        <table width="994" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td><img src="images/TaiHing_23.jpg" width="994" height="49"></td>
            </tr>
        </table>
        <script src="My97DatePicker/WdatePicker.js"></script>
        <script>
            $(function () {

                $('#sel').change(function () {

                    window.location = 'itsupport.php?sel=' + $(this).val();
                });
            });
        </script>
</body>
</html>