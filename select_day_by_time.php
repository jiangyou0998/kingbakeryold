<?php
session_start();

if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order.php';
    header('Location: login.php');
}

require("connect.inc");

$timestamp = gettimeofday("sec");
$advDays = $_REQUEST[advDays];
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=big5">
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>內聯網</title>
    <script src="js/jquery-1.9.1.min.js"></script>
    <script src="js/My97DatePicker/WdatePicker.js"></script>
    <script src="js/parser.js"></script>
    <script>
        function opensupplier() {
            var Obj = document.getElementsByName("dept");
            var bool = false;
            for (var i = 0; i < Obj.length; i++) {
                if (Obj[i].checked == true) {
                    bool = true;
                    break;
                }
            }
            var shop = 0;
            <?php if($_SESSION[type] == 3){ ?>
            if ((shop = $("#shop").val()) == '0') {
                alert("請先選擇分店");
                return;
            }
            <?}?>

            var date = new Date($("#datepicker").val());
            var today = new Date();
            var datediff=date.getTime()-today.getTime();
            var advance=Math.floor(datediff/(24*3600*1000));

            if (bool) {
                location.href = "order_z_dept.php?shop=" + shop + "&dept=" + Obj[i].value + "&advance=" + advance;
                //this.close();
            } else {
                alert("請先選擇部門");
            }

        }
    </script>
    <style type="text/css">
        <!--
        body {
            background-color: #FFFFCC;
        }

        .style3 {
            font-size: 250%;
        }

        .style4 {
            color: #FF0000;
        }

        .style5 {
            font-size: 150%;
            font-weight: bold;
        }

        .daylist {
            font-size: 160%;
        }

        -->
    </style>
</head>

<body>


<div align="left"><a target="_top" href="order.php" style="font-size: xx-large;">返回</a></div>
<center class="style5">
    <span class="style4">按日期修改訂單</span>
    <? if ($_SESSION[type] == 3) { ?>
        <br>
        <br>
        落貨分店


        <select style="width:200px;" id="shop">
            <option value="0">請選擇分店</option>
            <?php
            $sql = "SELECT int_id, txt_name FROM db_intranet.tbl_user WHERE chr_type = 2 AND int_dept = 2 ORDER BY txt_login;";
            $result = mysqli_query($con, $sql) or die($sql);
            while ($record = mysqli_fetch_assoc($result)) { ?>
                <option value="<?= $record[int_id] ?>"><?= $record[txt_name] ?></option>
            <? } ?>
        </select>
        <br>
    <? } ?>
    <br>
    <input type="radio" name="dept" id="radio" value="R" checked>烘焙
    <input type="radio" name="dept" id="radio" value="B">水吧
    <input type="radio" name="dept" id="radio" value="K">廚房
    <input type="radio" name="dept" id="radio" value="F">樓面
</center>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0">

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td valign="middle">
                日期:
                <input type="text" name="checkDate" class="form-control" value="<?= $showDate ?>" id="datepicker"
                       onclick="WdatePicker({maxDate:'',isShowClear:false})" style="width:125px" readonly>

                <button onclick="opensupplier()">查詢</button>
            </td>
        </tr>
        <tr>
            <td align="center"></td>
        </tr>
    </table>
</table>
<br>

</body>
</html>
