<?php
require("connect.inc");
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order.php';
    header('Location: login.php');
}

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
    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <style type="text/css">
        <!--
        .style2 {
            color: #0000CC
        }

        .styleA {
            font-size: large;;
        }

        span.btn {
            /*display: inline-block;
            font-size: 40px;
            text-align:center;
            color: white;
            background: -webkit-linear-gradient(left top, #925101 , #E37D01);*/
            width: 150px;
            height: 150px;
            /*line-height:150px; */

            padding: 8px;
            background: -webkit-linear-gradient(left top, #925101, #E37D01);
            border-color: #357ebd;
            color: #fff;
            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            border-radius: 10px; /* future proofing */
            -khtml-border-radius: 10px; /* for old Konqueror browsers */
            text-align: center;
            vertical-align: middle;
            border: 1px solid transparent;
            font-weight: 900;
            border-width: 10px;


        }

        -->
    </style>
    <script>
        function openwindow() {
            var advDays = 14;
            var winHeight = 500 + ((advDays - 5) * 27);

            orderWindow = window.open("select_day_dept.php?advDays=" + advDays, "select_day", "height=600,width=550,resizable=no,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no, status=no");

            orderWindow.moveTo(600, 300);
        }

    </script>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table id="Table_01" width="995" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="13">
            <?php include "head.php"; ?></td>
        <td>&nbsp;</td>
    </tr>
</table>
<br/>
<br/>
<div align="center" style="width:995px;" class="row">
    <div class="col-sm-6">
        <!-- <a href="select_day_dept.php?advDays=14"><img src="images/Order_Button_Stock.jpg" width="150" height="150" border="0"></a> -->
        <a href="select_day_dept.php?advDays=14"><span class="btn" style="font-size: 40px;line-height: 50px;">中央<br/>工場</span></a>
        <br/>
        <a href="order_check.php?head=5" class="styleA">翻查柯打</a>
        <br/>
        <?php 
            if ($_SESSION[type] == 2){
                echo "<a href='order_sample.php' class='styleA'>創建範本</a>";
            }
        ?>
        
    </div>
    <div class="col-sm-6">
        <!-- <a href="#"><img src="images/Order_Button_Supplier.jpg" width="150" height="150" border="0"></a> -->
        <a href="#"><span class="btn" style="font-size: 30px;line-height: 100px;">供應商</span></a>
    </div>
</div>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<table>
    <tr>
        <td colspan="13">
            <img src="images/TaiHing_23.jpg" width="994" height="49" alt=""></td>
        <td>
            <img src="images/spacer.gif" width="1" height="49" alt=""></td>
    </tr>
</table>
</body>
</html>