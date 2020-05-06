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
    <title>內聯網</title>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5">
    <link href="class.css" rel="stylesheet" type="text/css">
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
        <table width="275" border="0" cellspacing="5" cellpadding="3">
            <tr>
                <td width="263" align="center">&nbsp;</td>
            </tr>
            <tr>
                <td align="left"><a href="?dept=0&head=<?= $_REQUEST['head']; ?>" class="SelectMenu">全部</a></td>
            </tr>
            <?php
            require($DOCUMENT_ROOT . "connect.inc");
            $sql = "SELECT * FROM tbl_dept WHERE bl_isvalid = 1 AND int_id IN (SELECT DISTINCT(int_dept) ";
            $sql .= "FROM tbl_notice WHERE date_delete = '2000-01-01' ";
            if (!$showExpiry) {
                $sql .= "AND (date_last >= DATE(NOW()) OR date_last IS NULL) ";
            }
            $sql .= ") ORDER BY int_sort";
            //die($sql);
            $result = mysqli_query($con, $sql) or die("invalid query");
            while ($record = mysqli_fetch_array($result)) {
                ?>
                <tr>
                    <td align="left"><a href="?dept=<?= $record[0]; ?>&head=<?= $_REQUEST['head']; ?>"
                                        class="SelectMenu"><?= $record[1]; ?></a></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </td>
    <td colspan="8" rowspan="6" align="center" valign="top">
        <form name="frmsearch" action="" method="post">
            可輸入通告編號 / 主旨部份文字作搜尋
            <input name="n_number" type="text" id="n_number" value="<?= $_POST[n_number]; ?>">
            <input name="search" type="submit" id="search" value="搜尋">
            <input type="hidden" name="issearch" value="1">
        </form>
        <table width="100%" border="1" cellspacing="1" cellpadding="3">
            <tr>
                <td align="center" width="15%"><strong>日期</strong></td>
                <td align="center" width="7%"><strong>編號</strong></td>
                <td align="center" width="48%"><strong>主旨</strong></td>
                <td align="center" width="30%"><strong>部門</strong></td>
            </tr>
            <?php
            $sql = "SELECT * FROM tbl_notice,tbl_dept ";
            $sql .= "WHERE tbl_notice.date_delete = '2000-01-01' ";
            if ($_SESSION[dept] == 2) {
                $area = "SELECT int_area_id FROM tbl_order_z_shop WHERE int_user_id = $_SESSION[user_id]";
                $area = mysqli_query($con, $area) or die($area);
                $area = mysqli_fetch_assoc($area);
                //echo $area[int_area_id];
                $sql .= " AND ((SELECT COUNT(*) as bl_show FROM tbl_notice_v_brand WHERE int_notice_id = tbl_notice.int_id AND int_brand_id = $area[int_area_id]) > 0) ";
            }

            if (!$showExpiry) {
                $sql .= "AND (date_last >= DATE(NOW()) OR date_last IS NULL) ";
            }
            $sql .= "AND tbl_notice.int_dept = tbl_dept.int_id ";
            if ($_REQUEST[dept] != 0) $sql .= "AND tbl_notice.int_dept = $_REQUEST[dept] ";
            $sql .= "ORDER BY tbl_notice.date_modify DESC, tbl_notice.int_id DESC ";

            if ($_REQUEST[pageno] == "" || $_REQUEST[pageno] < 1) {
                $page = 1;
            } else {
                $page = $_REQUEST[pageno];
            }
            $sql .= "LIMIT " . (($page - 1) * 27) . ", 27";

            if ($_POST[issearch] == 1) {
                $sql = "SELECT tbl_notice.*, tbl_dept.txt_dept FROM tbl_notice, tbl_dept WHERE tbl_notice.int_dept = tbl_dept.int_id AND ((tbl_notice.int_no LIKE ";
                $sql .= "'$_POST[n_number]') OR (tbl_notice.txt_name LIKE '%$_POST[n_number]%')) AND ";
                $sql .= "(tbl_notice.date_delete = '2000-01-01' ";
                if (!$showExpiry) {
                    $sql .= "AND (date_last >= DATE(NOW()) OR date_last IS NULL) ";
                }
                $sql .= ") ORDER BY tbl_notice.date_modify DESC, tbl_notice.int_id DESC";
            }

            $result = mysqli_query($con, $sql) or die($sql);
            while ($record = mysqli_fetch_array($result)) {
                ?>
                <tr>
                    <td align="center"><?= $record['date_modify']; ?></td>
                    <td align="center"><?= $record['int_no']; ?></td>
                    <td align="left"><a href="<?= $record['txt_path']; ?>"
                                        target="_blank"><?= $record['txt_name']; ?></a>
                        <?php
                        //$a = date_diff($record['date_modify'], date("Y-m-d"));
                        //if ($a[days_total] < 3) echo "<img src=\"images/news_a.gif\" width=\"32\" height=\"24\">";
                        ?></td>
                    <td align="left"><?= $record['txt_dept']; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <br>
        <table width="80%" border="0" cellspacing="3" cellpadding="3">
            <tr>
                <td width="50%" align="right">
                    <?php
                    $sql = "SELECT count(*) FROM tbl_notice ";
                    $sql .= "WHERE tbl_notice.date_delete = '2000-01-01' ";
                    if (!$showExpiry) {
                        $sql .= "AND (date_last >= DATE(NOW()) OR date_last IS NULL) ";
                    }
                    if ($_REQUEST[dept] != 0) $sql .= "AND tbl_notice.int_dept = $_REQUEST[dept] ";
                    $sql .= "ORDER BY tbl_notice.date_modify DESC, tbl_notice.int_id DESC ";

                    $result = mysqli_query($con, $sql) or die("invalid query");
                    $record = mysqli_fetch_array($result);
                    $countPage = ceil($record[0] / 27);
                    echo "第 " . $page . " 頁 / 共 " . $countPage . " 頁&nbsp;&nbsp;&nbsp;&nbsp;";
                    if ($page != 1) {
                        echo "<a href=\"?dept=$_REQUEST[dept]&pageno=" . ($page - 1) . "\">上一頁</a>";
                    } else {
                        echo "第一頁";
                    }
                    echo "&nbsp;&nbsp;";
                    if ($page < $countPage) {
                        echo "<a href=\"?dept=$_REQUEST[dept]&pageno=" . ($page + 1) . "\">下一頁</a>";
                    } else {
                        echo "最終頁";
                    }
                    ?>
                </td>
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