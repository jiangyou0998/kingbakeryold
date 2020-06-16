<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'notice.php';
    header('Location: login.php');
}

$showExpiry = true;

//計算文件與今日相差之日數, 如 3 日內, 就打出 NEW 字
/*
function date_diff($d1, $d2){
    $d1 = (is_string($d1) ? strtotime($d1) : $d1);
    $d2 = (is_string($d2) ? strtotime($d2) : $d2);

    $diff_secs = abs($d1 - $d2);
    $base_year = min(date("Y", $d1), date("Y", $d2));

    $diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
    return array(
        "years" => date("Y", $diff) - $base_year,
        "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
        "months" => date("n", $diff) - 1,
        "days_total" => floor($diff_secs / (3600 * 24)),
        "days" => date("j", $diff) - 1,
        "hours_total" => floor($diff_secs / 3600),
        "hours" => date("G", $diff),
        "minutes_total" => floor($diff_secs / 60),
        "minutes" => (int) date("i", $diff),
        "seconds_total" => $diff_secs,
        "seconds" => (int) date("s", $diff)
    );
}
*/

?>
<html>
<head>
    <title>內聯網-通訊錄</title>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5">
    <link href="class.css" rel="stylesheet" type="text/css">
    <style>
        #table01 {
            font-size: 18px;
        }

        #table01 td, th {
            font-size: 16px;
        }
    </style>
    <script>
        function checkno() {
            var ValidChars = "0123456789"; // 只限數字
            var dataArray = new Array();
            var tempCheck;
            var IsNumber = true;
            var Char;
            dataArray[0] = document.frmsearch.n_number.value;
            for (i = 0; i < dataArray.length; i++) {
                tempCheck = dataArray[i];
                for (j = 0; j < tempCheck.length && IsNumber == true; j++) {
                    Char = tempCheck.charAt(j);
                    if (ValidChars.indexOf(Char) == -1) {
                        IsNumber = false;
                        alert("搜尋通告編號只可填上數字");
                        return false;
                    }
                }
            }
            document.frmsearch.submit();
        }
    </script>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table id="Table_01" width="995" height="1148" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="14">
            <?php include("head.php"); ?>
        </td>
    </tr>
    <td rowspan="6">
        <img src="images/TaiHing_12.jpg" width="27" height="890" alt=""></td>
    <td height="890" colspan="3" rowspan="6" align="left" valign="top" background="images/TaiHing_13.jpg">
        <table width="200" border="0" cellspacing="5" cellpadding="3">
            <tr>
                <td width="200" align="center">&nbsp;</td>
            </tr>
            <tr>
                <td align="left"><a href="?area=0&head=<?= $_REQUEST['head']; ?>" class="SelectMenu">全部</a></td>
            </tr>
            <?php
            require($DOCUMENT_ROOT . "connect.inc");
            $sql = "SELECT * FROM tbl_order_z_area WHERE int_id IN(SELECT DISTINCT(int_area) FROM tbl_district) ORDER BY int_sort;";
            //die($sql);
            $result = mysqli_query($con, $sql) or die("invalid query");
            while ($record = mysqli_fetch_array($result)) {
                ?>
                <tr>
                    <td align="left"><a href="?area=<?= $record[0]; ?>&head=<?= $_REQUEST['head']; ?>"
                                        class="SelectMenu"><?= $record[1]; ?></a></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </td>
    <td colspan="8" rowspan="6" align="center" valign="top">
        <br/>
        <br/>
        <table width="100%" border="1" cellspacing="1" cellpadding="3" id="table01">
            <tr style="background-color:cff;">
                <td align="center" width="15%"><strong>分店</strong></td>
                <td align="center" width="75%"><strong>資料</strong></td>
            </tr>
            <?php
            $sql = "SELECT * FROM tbl_district WHERE int_area <> 999 ";
            if ($_REQUEST['area'] != 0)
                $sql .= "AND int_area = '$_REQUEST[area]' ";
            $sql .= "ORDER BY chr_code ";
            //die($sql);
            $result = mysqli_query($con, $sql) or die($sql);
            while ($record = mysqli_fetch_array($result)) {
                ?>
                <tr>
                    <th rowspan="4" align="left"><?= $record['chr_name']; ?></th>
                    <td align="center" style="padding:0px;">
                        <table width="100%" height="100%">
                            <tr>
                                <th width="15%" align="left" valign="top" style="border-right:2px solid black;">中文地址
                                </th>
                                <td><?= $record['chr_address'] ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:0px;">
                        <table width="100%">
                            <tr>
                                <th width="15%" align="left" valign="top" style="border-right:2px solid black;">英文地址
                                </th>
                                <td><i><?= $record['chr_eng_address'] ?></i></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:0px;">
                        <table width="100%">
                            <tr>
                                <th width="15%" align="left" valign="top" style="border-right:2px solid black;">營業時間
                                </th>
                                <td><i><?= $record['chr_oper_time'] ?></i></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tr>
                <tr>
                    <td align="center" style="padding:0px;">
                        <table width="100%">
                            <tr>
                                <th width="15%" align="left" valign="top" style="border-right:2px solid black;">電　話</th>
                                <td width="35%" style="border-right:2px solid black;"><?= $record['chr_tel'] ?></td>
                                <th width="15%" align="left" valign="top" style="border-right:2px solid black;">ＦＡＸ</th>
                                <td width="35%"><?= $record['chr_fax'] ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="padding:0px;background-color:ffc;">&nbsp;</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <br>
        <table width="80%" border="0" cellspacing="3" cellpadding="3">
            <tr>
                <td width="50%" align="right"></td>
            </tr>
        </table>
    </td>
    <td rowspan="6">
        <img src="images/TaiHing_16.jpg" width="20" height="890" alt=""></td>
    <td>
        <img src="images/spacer.gif" width="1" height="210" alt=""></td>
    </tr>
    <tr>
        <td>
            <img src="images/spacer.gif" width="1" height="77" alt=""></td>
    </tr>
    <tr>
        <td>
            <img src="images/spacer.gif" width="1" height="13" alt=""></td>
    </tr>
    <tr>
        <td>
            <img src="images/spacer.gif" width="1" height="288" alt=""></td>
    </tr>
    <tr>
        <td>
            <img src="images/spacer.gif" width="1" height="14" alt=""></td>
    </tr>
    <tr>
        <td>
            <img src="images/spacer.gif" width="1" height="288" alt=""></td>
    </tr>
    <tr>
        <td colspan="13">
            <img src="images/TaiHing_22.jpg" width="994" height="17" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="1" height="17" alt=""></td>
    </tr>
    <tr>
        <td colspan="13">
            <img src="images/TaiHing_23.jpg" width="994" height="49" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="1" height="49" alt=""></td>
    </tr>
    <tr>
        <td>
            <img src="images/spacer.gif" width="27" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="181" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="77" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="30" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="98" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="127" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="76" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="71" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="57" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="103" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="74" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="53" height="1" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="20" height="1" alt=""></td>
        <td></td>
    </tr>
</table>

</body>
</html>