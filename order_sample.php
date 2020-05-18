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

        .style4 {
            color: #FF0000;
            font-size:50px;
        }

        .style5 {
            font-size: medium;
            font-weight: bold;
        }

        .sizefont {
             font-size: 130%;
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
<div align="left"><a target="_top" href="order.php" style="font-size: xx-large;">返回</a></div>
<center class="style5">
    <span class="style4">創建範本</span>
    <!-- <input type="radio" name="dept" id="radio" value="R" checked>烘焙
    <input type="radio" name="dept" id="radio" value="B">水吧
    <input type="radio" name="dept" id="radio" value="K">廚房
    <input type="radio" name="dept" id="radio" value="F">樓面 -->
</center>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0">
        <div style="margin-bottom: 10px;"><button class="sizefont"><a href="order_sample_createandedit.php?action=insert">新建範本</a></button></div>
        <?php 

            $sql = "SELECT * FROM tbl_order_sample WHERE user_id = ".$order_user." AND disabled = 0;";
            $result = mysqli_query($con, $sql);
            while ($record = mysqli_fetch_array($result)) { 
                $sampledate = $record['sampledate'];
                foreach ($weekArr as $key => $value) {
                    //將數字轉換為星期
                    $sampledate = str_replace($key, $value, $sampledate);
                }

        ?>
                <tr style="margin-top: 60px" class="sizefont">
                    <td align="right" width="4%"><strong>#</strong></td>
                    <td align="left"><a
                        href="order_sample_createandedit.php?action=edit&id=<?=$record['id'] ?>"><strong><?= $sampledate ?></strong></a>
                    </td>
                    <td align="middle" width="10%"><strong><button onclick="delsample(<?=$record['id'] ?>);">刪除範本</button></strong></td>
                </tr>
        <?php    
            }
        ?>
        
     
  
</table>
<br>

<script>
    function delsample(id){
        // alert(id);
        $.ajax({
            type: "POST",
            url: "order_sample_delete.php",
             data: {
                'id'  : id
            },
            success: function (msg) {
                // console.log(msg);
                alert('範本刪除成功!');
                window.location.reload('order_sample.php');
                // top.location.href = 'order_sample.php';
            }
        });
    }
</script>

</body>
</html>
