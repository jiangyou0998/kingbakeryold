<?php
session_start();

if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order.php';
    header('Location: login.php');
}

require("connect.inc");

$timestamp = gettimeofday("sec");
$order_user = $_SESSION[order_user] ? $_SESSION[order_user] : $_SESSION[user_id];
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=big5">
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>內聯網</title>
    <script src="js/jquery-1.11.0.min.js"></script>
    <script>
        function opensupplier(aa) {
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
            if (bool) {
                location.href = "order_z_dept.php?shop=" + shop + "&dept=" + Obj[i].value + "&advance=" + aa;
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
            color: #FF0000
        }

        .style5 {
            font-size: medium;
            font-weight: bold;
        }

        -->
    </style>
</head>

<body>

<?php


?>
<div align="left"><a target="_top" href="order.php">返回</a></div>
<center class="style5">
    <span class="style4">創建範本</span>
    <input type="radio" name="dept" id="radio" value="R" checked>烘焙
    <input type="radio" name="dept" id="radio" value="B">水吧
    <input type="radio" name="dept" id="radio" value="K">廚房
    <input type="radio" name="dept" id="radio" value="F">樓面
</center>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0">
        <button><a href="order_sample_createandedit.php">新建範本</a></button>
        <?php 

            $sql = "SELECT * FROM tbl_order_sample WHERE user_id = ".$order_user.";";
            $result = mysqli_query($con, $sql);
            while ($record = mysqli_fetch_array($result)) { ?>
                <tr>
                    <td align="right"><strong></strong></td>
                    <td align="left"><a
                        href="javascript:opensupplier(<?= $count; ?>);"><strong><?=$record['sampledate'] ?></strong></a>
                </td>
        </tr>
        <?php    }

        ?>
        
     
  
</table>
<br>
</body>
</html>
