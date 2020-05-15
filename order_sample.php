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
            if (bool) {
                location.href = "order_sample_createandedit.php?action=edit&id="+aa;
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
            font-size:50px;
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

$weekArr = [
    '0' => '星期日',
    '1' => '星期一',
    '2' => '星期二',
    '3' => '星期三',
    '4' => '星期四',
    '5' => '星期五',
    '6' => '星期六',
];

?>
<div align="left"><a target="_top" href="order.php" style="font-size: larger;">返回</a></div>
<center class="style5">
    <span class="style4">創建範本</span>
    <!-- <input type="radio" name="dept" id="radio" value="R" checked>烘焙
    <input type="radio" name="dept" id="radio" value="B">水吧
    <input type="radio" name="dept" id="radio" value="K">廚房
    <input type="radio" name="dept" id="radio" value="F">樓面 -->
</center>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0">
        <button><a href="order_sample_createandedit.php?action=insert">新建範本</a></button>
        <?php 

            $sql = "SELECT * FROM tbl_order_sample WHERE user_id = ".$order_user.";";
            $result = mysqli_query($con, $sql);
            while ($record = mysqli_fetch_array($result)) { 
                $sampledate = $record['sampledate'];
                foreach ($weekArr as $key => $value) {
                    //將數字轉換為星期
                    $sampledate = str_replace($key, $value, $sampledate);
                }

        ?>
                <tr>
                    <td align="right"><strong>#</strong></td>
                    <td align="left"><a
                        href="order_sample_createandedit.php?action=edit&id=<?=$record['id'] ?>"><strong><?= $sampledate ?></strong></a>
                    </td>
                </tr>
        <?php    
            }
        ?>
        
     
  
</table>
<br>
</body>
</html>
