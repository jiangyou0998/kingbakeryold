<?php

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");

//跳轉後返回的URL
$_SESSION['back_url']=$_SERVER['REQUEST_URI'];

$timestamp = gettimeofday("sec") + 28800;

if (isset($_POST["Sort"])) {
    $sql = "UPDATE tbl_order_check SET int_sort = CASE int_id ";
    foreach ($_POST as $id => $new_sort) {
        if ($id == "Sort") continue;
        if ($id == "reporttime") continue;
        if ($new_sort == "") continue;
        $sql .= "WHEN $id THEN $new_sort ";
    }
    $sql .= "ELSE int_sort END;";
//		var_dump($_POST);die;
    mysqli_query($con, $sql) or die($sql);
}


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
    <script src="calendar/calendar.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/json3/3.3.2/json3.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/parser.js"></script>

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
        <h1>報告列表</h1>
        <form id="sort" method="POST">
            <input type="hidden" name="Sort" value="1">
            <div style="margin-bottom:5px; text-align:right">
                <input type="submit" value="更新排序" style="cursor:pointer; position:relative; right:183px;">
                <img src="images/plus_2.png" onclick="document.location='CMS_order_c_check_add.php';"
                     style="cursor:pointer; position:relative; right:0px;">
            </div>

            <table class="cssTable1" id="table1">
                <tr class="cssImportant">
                    <th style="width:50px;">#</th>
                    <th style="width:300px;">報告名稱</th>
                    <th>相隔日數</th>
                    <th>排序</th>
                    <th>查看</th>
                    <th>修改</th>
                    <!-- <th>刪除</th> -->
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
                    <!--                排序-->
                    <td><input value='<?= $value[int_sort] ?>' style="width:50px; text-align:center"
                               name="<?= $value[int_id] ?>" oninput="inputcheck(this)"
                               onpropertychange="inputcheck(this)"></td>
                    <!--				查看-->
                    <td><img src="images/clipboard.png"
                             onclick="window.open('CMS_order_c_check_m.php?id=<?= $value[int_id] ?>');"
                             style="cursor:pointer;"></td>
                    <!--				編輯-->
                    <td><img src="images/edit.png"
                             onclick="document.location='CMS_order_c_check_add.php?id=<?= $value[int_id] ?>&action=update';"
                             style="cursor:pointer;"></td>
                    <!--                刪除-->
                    <!-- <td><img src="images/delete_2.png" onclick="deletereport(<?= $value[int_id] ?>)"
                             style="cursor:pointer;"></td> -->
                    </tr>
                <?php } ?>
        </form>
        </table>
        <div><span>報表時間:</span>
            <select name="reporttime" id="reporttime">
                <option value="0" <?php if ($_REQUEST[cat] == "0") {
                    echo "selected";
                } ?>>請選擇時間
                </option>
                <?php
                $sql_select = "SELECT distinct TP.chr_time 
                        FROM tbl_order_check TC
                            LEFT JOIN tbl_order_z_print_time TP ON TP.int_report_id = TC.int_id
                        WHERE TP.chr_time IS NOT NULL 
                            AND TP.chr_time <> \"\" 
                            AND TP.chr_weekday LIKE CONCAT('%', WEEKDAY(CURDATE()), '%')
                            AND TC.disabled = 0
                        ORDER BY TP.chr_time  ";
                $result_select = mysqli_query($con, $sql_select) or die($sql_select);
                WHILE ($record_select = mysqli_fetch_array($result_select)) {
                    ?>
                    <option value="<?php echo $record_select[chr_time]; ?>"><?php echo $record_select[chr_time]; ?></option>
                    <?php
                }
                ?>
            </select>
            <a href="#" style=" font-size:150%;" onclick="createReport()">生成報表</a></div>

    </div>
</div>
<div id='loading'>報表正在生成中...</div>
</body>
<script>
    //禁止input框顯示歷史記錄
    $(document).ready(function () {
        $("input").attr('autocomplete', "off");
    });

    function inputcheck(sender) {

        if (sender.value.match(/\D/g)) {
            sender.value = sender.value.replace(/\D/g, "");
        }
    }


    //刪除報告
    // function deletereport(id) {

    //     $.ajax({
    //         type: "post",
    //         url: "CMS_order_c_check_crud.php",
    //         data: {
    //             'id': id,
    //             'method': "delete"
    //         },
    //         success: function (data) {
    //             window.location.reload();
    //             console.log(data);
    //         },
    //         error: function () {
    //             alert("出現錯誤");
    //         }
    //     });
    //     console.log(id);
    // }

    function createReport() {
        // alert(111);
        var isSelectedTime = true;
        var rTime = $("#reporttime option:selected").val();
        if (rTime == 0) {
            alert('請選擇時間!');
            isSelectedTime = false;
        }
        if (isSelectedTime) {
            $('#loading').show();
            window.location.href = "testdown.php?rTime=" + rTime;
        }
    }

    $(window).load(function () {
        $('#loading').hide();
    });

</script>

</html>