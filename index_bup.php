<?php
require("connect.inc");
//$sql = "SELECT * FROM tbl_order_z_dept;";
//mysqli_query($con, $sql) or die($sql);
session_start();

//檢查是否已登入
if ($_SESSION[authenticated]) {
    $isLogin = true;
} else {
    $isLogin = false;
}
?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>內聯網</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">
    <META http-equiv="refresh" content="300">
    <link href="class.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        <!--
        .style2 {
            color: #0000CC
        }

        -->
    </style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table id="Table_01" width="995" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="13">
            <?php include "head.php"; ?></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td rowspan="4">&nbsp;</td>
        <td height="890" colspan="3" rowspan="5" align="center" valign="top" background="images/TaiHing_13.jpg">
            <table width="90%" border="0" cellspacing="3" cellpadding="3">
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">最新通告</td>
                </tr>
                <?php
                $sql = "SELECT tbl_notice.*, IF(char_length(txt_name)>10, concat(substring(txt_name, 1, 10), '...'), txt_name) as name FROM tbl_notice ";
                $sql .= "WHERE date_delete = '2000-01-01' ";
                $sql .= "ORDER BY date_modify DESC, int_id DESC ";
                $sql .= "LIMIT 12";
                $result = mysqli_query($con, $sql) or die("invalid query");
                while ($record = mysqli_fetch_array($result)) {
                    $sql_dept = "SELECT * FROM tbl_dept WHERE int_id = $record[2]";
                    $result_dept = mysqli_query($con, $sql_dept) or die("invalid query");
                    $record_dept = mysqli_fetch_array($result_dept);
                    ?>
                    <tr>
                        <td><a href="notice.php"><?= $record['name'] ?></a></td>
                        <td>-<?= substr($record_dept[1], 4); ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="2" align="right">
                        <a href="#">更多...</a>
                    </td>
                </tr>
            </table>
        </td>
        <td height="287" colspan="4" rowspan="2" align="center" background=""></td>

        <td height="210" colspan="4" align="right" background="images/TaiHing_15.jpg">
            <?php if (!$isLogin) { ?>
                <form name="form1" id="form1" method="post" action="login.php">
                    <table width="85%" border="0" cellspacing="3" cellpadding="3">
                        <tr>
                            <td>職員登入</td>
                        </tr>
                        <tr>
                            <td class="Login">登入名稱</td>
                        </tr>
                        <tr>
                            <td><input name="loginAC" type="text" class="Login" id="account" size="20"/></td>
                        </tr>
                        <tr>
                            <td class="Login">密碼</td>
                        </tr>
                        <tr>
                            <td><input name="loginPWD" type="password" id="password" class="Login" size="15"/>
                                <input type="submit" name="Submit" value="登入"/></td>
                        </tr>
                    </table>
                </form>
            <?php } else { ?>
                <table width="92%" border="0" cellspacing="3" cellpadding="3">
                    <tr>
                        <td align="center" class="AfterLogin">歡迎您</td>
                    </tr>
                    <tr>
                        <td align="center">登入身份：<?= $_SESSION[user] ?></td>
                    </tr>
                    <tr>
                        <td align="center"><input type="button" name="Submit" value="登出"
                                                  onClick="location.href='logout.php'"/></td>
                    </tr>
                    <tr>
                        <td align="center"><a href="#">變更密碼</a></td>
                    </tr>
                    <?php if ($_SESSION['type'] === '3') { ?>
                        <tr>
                            <td align="center"><a href="CRM.php">後台系統</a>&nbsp;</td>
                        </tr>
                    <?php } ?>

                </table>
                <?php
            }
            ?>        </td>
        <td rowspan="6">&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td height="680" colspan="4" rowspan="4" align="right" valign="top" background="images/TaiHing_17.jpg">
            <table width="92%" border="0" cellspacing="3" cellpadding="3">
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="RightText">Description 1</td>
                </tr>
                <tr>
                    <td class="RightText">Description 2</td>
                </tr>
                <tr>
                    <td class="RightText">Description 3</td>
                </tr>
                <tr>
                    <td class="RightText">Description 4</td>
                </tr>
            </table>
        </td>
        <td>
            <img src="images/spacer.gif" width="1" height="77" alt=""></td>
    </tr>
    <tr>
        <td colspan="4">
            <img src="images/TaiHing_18.jpg" width="372" height="13" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="1" height="13" alt=""></td>
    </tr>
    <tr>
        <!--
                <td height="288" colspan="4" align="center" background="images/TaiHing_19.jpg"><img src="images/intranet1.jpg" width="372" height="288" border="0"></td>
                <td><img src="images/spacer.gif" width="1" height="288" alt=""></td>
        -->
    </tr>
    <tr>
        <td colspan="4">&nbsp;</td>
        <td>
            <img src="images/spacer.gif" width="1" height="14" alt=""></td>
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