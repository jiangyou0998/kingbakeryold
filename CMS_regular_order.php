<?php
session_start();

// echo $_REQUEST['menuid'];

if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order.php';
    header('Location: login.php');
}

require("connect.inc");

$timestamp = gettimeofday("sec");
$menuid = $_REQUEST['menuid'];
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=big5">
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>固定柯打-內聯網</title>
    <script src="js/jquery-1.11.0.min.js"></script>
    <script>
        
    </script>
    <style type="text/css">
        <!--
        body {
            margin-top: 30px;
            margin-left: 30px;
            margin-right: 30px;
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

//中間標題相關數據查詢
$titleArr = array();
$sql = "SELECT chr_name,chr_no FROM tbl_order_z_menu WHERE int_id = ". $menuid .";";

        $result = mysqli_query($con, $sql);
        $titleResult = mysqli_fetch_array($result);
        $titleArr = $titleResult;

        // var_dump($titleArr);

?>

<center class="style5">
    <span class="style4"><?= $titleArr['chr_no'] ?>&nbsp&nbsp<?= $titleArr['chr_name'] ?>&nbsp&nbsp固定柯打</span>

</center>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0">
        <div style="margin-bottom: 10px;"><button class="sizefont"><a href="CMS_regular_order_createandedit.php?action=insert&menuid=<?= $_REQUEST['menuid'] ?>">新建範本</a></button></div>
        <?php 

            $sql = "SELECT * FROM regular_orders WHERE menu_id = ".$_REQUEST['menuid']." AND disabled = 0;";
            $result = mysqli_query($con, $sql);
            while ($record = mysqli_fetch_array($result)) { 
                $orderdates = $record['orderdates'];
                foreach ($weekArr as $key => $value) {
                    //將數字轉換為星期
                    $orderdates = str_replace($key, $value, $orderdates);
                }

        ?>
                <tr style="margin-top: 60px" class="sizefont">
                    <td align="right" width="4%"><strong>#</strong></td>
                    <td align="left"><a
                        href="CMS_regular_order_createandedit.php?action=edit&menuid=<?= $_REQUEST['menuid'] ?>&rOrderID=<?= $record['id'] ?>"><strong><?= $orderdates ?></strong></a>
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

        var confirmBox = confirm('你確定要刪除範本嗎?');

        if(confirmBox == true){
            // alert(id);
            $.ajax({
                type: "POST",
                url: "CMS_regular_order_delete.php",
                 data: {
                    'id'  : id,
                    'menuid' : <?= $menuid ?>
                },
                success: function (msg) {
                    // console.log(msg);
                    alert('範本刪除成功!');
                    window.location.reload('CMS_regular_order.php?menuid='+<?= $menuid ?>);
                    // top.location.href = 'order_sample.php';
                }
            });
        }

    }
</script>

</body>
</html>


