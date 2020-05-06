<?php

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");
$timestamp = gettimeofday("sec") + 28800;

$aryOR = Array();
$sql = "SELECT * FROM tbl_order_check WHERE disabled = 0 order by int_sort, int_id";
$result = mysqli_query($con, $sql) or die($sql);
while ($record = mysqli_fetch_assoc($result)) {
    $aryOR[] = $record;
}
?>

<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網</title>
    <link href="js/My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
    <!--	<script src="calendar/calendar.js"></script>-->
    <!--	<script src="//cdnjs.cloudflare.com/ajax/libs/json3/3.3.2/json3.min.js"></script>-->
    <script src="js/My97DatePicker/WdatePicker.js"></script>
    <script src="js/jquery.min.js"></script>
    <!--	<script src="js/parser.js"></script>-->

    <style>
        <!--
        .cssMenu {
            list-style-type: none;
            padding: 0;
            overflow: hidden;
            background-color: #ECECEC;
            float: right;
        }

        .cssMenuItem {
            float: right;
            width: 140px;
            border-right: 2px solid white;
        }

        .cssMenuItem a {
            display: block;
            color: black;
            text-align: center;
            padding: 4px;
            text-decoration: none;
        }

        .cssMenuItem a:hover {
            background-color: #BBBBBB;
            color: white;
        }

        .cssImportant {
            background-color: #CCFFFF
        }

        div {
            margin-top: 15px;
        }

        .cssTable1 {
            border-collapse: collapse;
        }

        .cssTable1 {
            border: 2px solid black;
        }

        .cssTable1 th {
            padding: 0px;
            text-align: center;
            border: 2px solid black;
            width: 100px;
        }

        .cssTable1 td {
            padding: 0px;
            text-align: center;
            border: 2px solid black;
        }

        -->


        #loading {
            position: fixed;
            z-index: 400;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0%;
            text-align: center;
            color: #595758;
            background-color: #ffffff;
            font-size: 250%;
        }
    </style>
</head>
<body>
<div align="center" width="100%">
    <div align="center" style="width:850px;">
        <h1>查看生產表</h1>
        <form id="sort" method="POST">
            <input type="hidden" name="Sort" value="1">

            <table class="cssTable1" id="table1">
                <tr class="cssImportant">
                    <th style="width:50px;">#</th>
                    <th style="width:300px;">報告名稱</th>
                    <th>相隔日數</th>
                    <th>查看</th>
                </tr>

                <?php foreach ($aryOR as $key => $value) { ?>
                    <?php $s = Array('否', '是'); ?>
                    <?php if ($key % 2 == 0) { ?>
                        <tr>
                    <?php } else { ?>
                        <tr bgcolor="#DDDDDD">
                    <?php } ?>
                    <td><?= $key + 1 ?></td>
                    <!--                報告名稱-->
                    <td><?= $value[chr_report_name] ?></td>
                    <!--                相隔日數-->
                    <td><?= $value[int_num_of_day] ?>日</td>
                    <!--				查看-->
                    <td><img src="images/clipboard.png"
                             onclick="viewReport(<?= $value[int_id] ?>,<?= $value[int_num_of_day] ?>)"
                             style="cursor:pointer;"></td>
                    </tr>
                <?php } ?>
        </form>
        </table>
        <div><span>收貨時間:</span>
            <input type="text" name="checkDate"
                   class="form-control"
                   value="<?= $showDate ?>"
                   id="datepicker"
                   onclick="WdatePicker({maxDate:'<?= $today ?>',isShowClear:false})" style="width:125px" readonly>

            <a href="#" style=" font-size:150%;" onclick="createReport()">生成報表</a></div>

    </div>
</div>
<div id='loading'>報表正在生成中...</div>
</body>
<script>
    function viewReport(id, numofday) {
        var delidate = $('#datepicker').val();

        // alert(delidate);
        if (delidate == '') {
            alert('請選擇收貨日期!');
            return false;
        }

        var dateTime = new Date(delidate);
        dateTime = dateTime.setDate(dateTime.getDate() - numofday);
        var url = '';
        url = 'CMS_order_c_check_m.php?id=' + id + '&checkDate=' + formatDate(dateTime);
        window.open(url);
    }

    // 第三种方式：函数处理
    function formatDate(now) {
        var date = new Date(now);
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var date = date.getDate();
        if (month < 10) {
            month = '0' + month;
        }
        if (date < 10) {
            date = '0' + date;
        }
        return year + "-" + month + "-" + date;
    }

    function createReport() {
        // alert(111);
        var isSelectedTime = true;
        var delidate = $('#datepicker').val();
        if (delidate == '') {
            alert('請選擇收貨日期!');
            return false;
        }
        $('#loading').show();
        window.location.href = "down_history_report.php?dTime=" + delidate;

    }

    $(window).load(function () {
        $('#loading').hide();
    });

</script>

</html>