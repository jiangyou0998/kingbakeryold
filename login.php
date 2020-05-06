<?php
require($DOCUMENT_ROOT . "connect.inc");
session_start();
$demoAC = "demo";
$demoPWD = "demo";

if (isset($_POST[Submit])) {
    // 如果帳號和密碼正確的話，寫入Session變數，並視情況重導到相關的頁面

    $sql = "SELECT * FROM tbl_user where txt_login = '$_POST[loginAC]' and txt_password = '$_POST[loginPWD]' AND chr_type not in (4,99)";
    $result = mysqli_query($con, $sql) or die("invalid query");
    if (mysqli_num_rows($result) <> 0) {
        $record = mysqli_fetch_array($result);
        // 寫入 Session 變數值
        $_SESSION['authenticated'] = true;


        if ($_POST[loginAC] == $demoAC && $_POST[loginPWD] == $demoPWD) {
            $_SESSION['user'] = "Demo User";
            $_SESSION['user_id'] = 0;
            $_SESSION['status'] = 'goodPWD';
            $_SESSION['chr_sap'] = "Demo";
            $_SESSION['type'] = "1";
            $_SESSION['user_no'] = 0;
            $_SESSION['dept'] = 0;
            $_SESSION['chr_visible'] = "";
            $_SESSION['e_name'] = "Demo User";
            $_SESSION['district'] = 0;
            $_SESSION['user_login'] = $demoAC;
        } else {
            $_SESSION['user'] = $record['txt_name'];
            $_SESSION['user_id'] = $record['int_id'];
            $_SESSION['status'] = 'goodPWD';
            $_SESSION['chr_sap'] = $record['chr_sap'];
            $_SESSION['type'] = $record['chr_type'];
            $_SESSION['user_no'] = $record['int_no'];
            $_SESSION['dept'] = $record['int_dept'];
            //$_SESSION['chr_visible'] = $record[19];
            $_SESSION['e_name'] = $record['chr_ename'];
            //$_SESSION['district'] = $record[5];
            $_SESSION['user_login'] = $record['txt_login'];
        }


        // 檢查在 $_SESSION 全域變數中，是否有之前設定好的網址重導 Session 變數
        if (isset($_SESSION[UrlRedirect])) {
            $redir = $_SESSION[UrlRedirect];
        } else {
            $redir = 'index.php';
        }

        // 重導到相關頁面
        header("Location: $redir");
        exit;
    } else {
        $_SESSION['status'] = 'wrongPWD';
        echo $_SESSION['status'];
        header('Location: login.php');
        exit;
    }
}


?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>內聯網</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">
    <link href="class.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        <!--
        .style1 {
            color: #FF0000
        }

        -->
    </style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
      onLoad="document.form1.loginAC.focus();">

<table id="Table_01" width="995" height="1148" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="14">
            <?php include("head.php"); ?>
        </td>
    </tr>
    <tr>
        <td rowspan="6">
            <img src="images/TaiHing_12.jpg" width="27" height="890" alt=""></td>
        <td height="890" colspan="11" rowspan="6" align="center" valign="top">
            <table width="50%" border="0" cellspacing="2" cellpadding="2">
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><span class="style1">
              <?php
              if ($_SESSION['status'] == 'wrongPWD') {
                  echo '密碼錯誤，請重試！';
              } else if ($_SESSION['status'] == 'neverLogin') {
                  echo '請先登入！';
              } else if ($_SESSION['status'] == 'goodPWD') {
                  echo '';
              }
              ?>
            </span></td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr align="center">
                    <td colspan="2">
                        <form name="form1" id="form1" method="post" action="">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFCC66">
                                <tr>
                                    <td colspan="2" align="center">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td width="40%" align="right">使用者名稱：</td>
                                    <td width="60%" align="left"><input name="loginAC" type="text" id="account"/></td>
                                </tr>
                                <tr>
                                    <td width="40%" align="right">密碼：</td>
                                    <td width="60%" align="left"><input name="loginPWD" type="password" id="password"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center"><input type="submit" name="Submit" value="登入"/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center">&nbsp;</td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
            <br></td>
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