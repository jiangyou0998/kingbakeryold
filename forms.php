<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'forms.php';
    header('Location: login.php');
}
//計算文件與今日相差之日數, 如 15 日內, 就打出 NEW 字
?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>內聯網</title>
    <meta http-equiv="Content-Type" content="text/html; charset=big5">
    <link href="class.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        <!--
        .style17 {
            font-size: 14px;
            font-weight: bold;
        }

        .style18 {
            font-size: 14px
        }

        -->
    </style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table id="Table_01" width="995" height="1148" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="14">
            <?php include("head.php"); ?>
        </td>
    </tr>
    <tr>
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
                $sql = "SELECT * FROM tbl_dept WHERE bl_isvalid = 1 AND int_id IN (SELECT DISTINCT(int_dept) FROM tbl_forms WHERE date_delete = '2000-01-01') ORDER BY int_sort";
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
                可輸入主旨部份文字作搜尋
                <input name="n_number" type="text" id="n_number" value="<?= $_POST[n_number]; ?>">
                <input name="search" type="submit" id="search" value="搜尋">
                <input type="hidden" name="issearch" value="1">
            </form>
            <table width="100%" border="1" cellpadding="3" cellspacing="1">
                <tr>
                    <td width="14%" align="center"><span class="style17">日期</span></td>
                    <td width="8%" align="center"><span class="style17">編號</span></td>
                    <td width="35%" align="center"><span class="style17">主旨</span></td>
                    <td width="20%" align="center"><span class="style17">部門</span></td>
                    <td width="15%" colspan="2" align="center"><span class="style17">大量申請<br>
            (5 - 30 張) </span></td>
                    <td align="center"><span class="style17">樣本</span></td>
                </tr>
                <?php
                $sql = "SELECT tbl_forms.*, tbl_dept.txt_dept FROM tbl_forms, tbl_dept ";
                $sql .= "WHERE tbl_forms.int_dept = tbl_dept.int_id ";
                $sql .= " AND tbl_forms.date_delete = '2000-01-01' ";
                if ($_SESSION[dept] == 2) {
                    $area = "SELECT int_area_id FROM tbl_order_z_shop WHERE int_user_id = $_SESSION[user_id]";
                    $area = mysqli_query($con, $area) or die($area);
                    $area = mysqli_fetch_assoc($area);
                    //echo $area[int_area_id];
                    $sql .= " AND ((SELECT COUNT(*) as bl_show FROM tbl_forms_v_brand WHERE int_form_id = tbl_forms.int_id AND int_brand_id = $area[int_area_id]) > 0) ";
                }

                if ($_REQUEST[dept] != 0) $sql .= "AND tbl_forms.int_dept = $_REQUEST[dept] ";
                $sql .= "ORDER BY tbl_forms.date_modify DESC, tbl_forms.int_id DESC ";
                if ($_REQUEST[pageno] == "") {
                    $page = 0;
                } else {
                    $page = $_REQUEST[pageno];
                }
                $sql .= "LIMIT " . ($page * 27) . ", 27";

                if ($_POST[issearch] == 1) {
                    $sql = "SELECT tbl_forms.*, tbl_dept.txt_dept FROM tbl_forms, tbl_dept WHERE tbl_forms.int_dept = tbl_dept.int_id AND (tbl_forms.txt_name LIKE '%$_POST[n_number]%' OR tbl_forms.int_no LIKE '%$_POST[n_number]%') AND (tbl_forms.date_delete = '2000-01-01') ORDER BY tbl_forms.date_modify DESC, tbl_forms.int_id DESC";
                }
                $result = mysqli_query($con, $sql) or die("invalid query");
                while ($record = mysqli_fetch_array($result)) {
                    $isMulti = $record[is_multi_print];
                    ?>
                    <form name="frmorder<?= $record['int_id']; ?>" action="order_form_2.php" method="post">
                        <input type="hidden" name="ordertype" value="form">
                        <input type="hidden" name="formid" value="<?= $record['int_id']; ?>">
                        <tr>
                            <td width="80" align="left"><span
                                        class="RightText RightText style18"><?= $record['date_modify']; ?>&nbsp;</span>
                            </td>
                            <td align="left"><span
                                        class="RightText RightText style18"><?= $record['int_no']; ?>&nbsp;</span></td>
                            <td align="left"><span class="RightText RightText style18"><a
                                            href="forms/<?= $record['txt_path']; ?>"
                                            target="_blank"><?= $record['txt_name']; ?>&nbsp;</a>
              <?php
              //$a = date_diff($record['date_modify'], date("Y-m-d"));
              //if ($a[days_total] < 15) echo "<img src=\"images/news_a.gif\" width=\"32\" height=\"24\">";
              ?>
            </span></td>
                            <td align="left"><span
                                        class="RightText RightText style18"><?= $record['txt_dept']; ?></span></td>
                            <td align="left"><input name="orderno" type="<?= $isMulti ? "text" : "hidden" ?>"
                                                    id="orderno" value="" size="4" maxlength="2"></td>
                            <td align="left"><input name="Submit" type="<?= $isMulti ? "submit" : "hidden" ?>"
                                                    value="申請"></td>
                            <td align="left"><span class="RightText RightText style18"><?php
                                    IF ($record['is_sample'] <> "0") {
                                        echo "<a href=\"forms/samples/$record[10]\" target=\"_blank\">檢視</a>";
                                    }
                                    ?></span>&nbsp;
                            </td>
                        </tr>
                    </form>
                    <?php
                }
                ?>
            </table>
            <br>
            <table width="80%" border="0" cellspacing="3" cellpadding="3">
                <tr>
                    <td width="50%" align="left">
                        <?php
                        if ($page != 0) {
                            echo "<a href=\"?dept=$_REQUEST[dept]&pageno=" . ($page - 1) . "\">上一頁</a>";
                        } else echo "&nbsp;";
                        ?></td>
                    <td width="50%" align="right">
                        <?php
                        $sql = "SELECT * FROM tbl_forms ";
                        $sql .= "WHERE date_delete = '2000-01-01' ";
                        if ($_REQUEST[dept] != 0) $sql .= "AND int_dept = $_REQUEST[dept] ";
                        $sql .= "ORDER BY date_modify DESC, int_id DESC ";
                        $sql .= "LIMIT " . (($page + 1) * 27) . ", 1";

                        if ($_POST[issearch] == 1) {
                            $sql = "SELECT * FROM tbl_forms WHERE (txt_name LIKE '%$_POST[n_number]%') AND (date_delete = '2000-01-01') ORDER BY date_modify DESC, int_id DESC LIMIT " . (($page + 1) * 27) . ", 1";
                        }

                        $result = mysqli_query($con, $sql) or die("invalid query");
                        if (mysqli_num_rows($result) != 0) {
                            echo "<a href=\"?dept=$_REQUEST[dept]&pageno=" . ($page + 1) . "\">下一頁</a>";
                        } else echo "&nbsp;";
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