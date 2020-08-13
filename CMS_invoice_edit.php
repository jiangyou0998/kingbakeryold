<?php
session_start();

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");
$year = date('Y', gettimeofday('sec'));
$month = date('m', gettimeofday('sec'));

$showDate = $_REQUEST['delidate'];

$today = date('Y-m-d');
IF ($showDate == "") $showDate = $today;


?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>內聯網</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">
    <META http-equiv="refresh" content="300">
    <link href="class.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <link href="js/My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
    <script src="js/jquery-1.9.1.min.js"></script>
    <script src="js/My97DatePicker/WdatePicker.js"></script>
    <style type="text/css">
        <!--
        body {
            margin-left: 40px;
            margin-top: 40px;
        }

        .style2 {
            font-size: 24px;
            font-weight: bold;
        }

        a:link {
            color: #0000FF;
        }

        a:visited {
            color: #0000FF;
        }

        a:hover {
            color: #FF00FF;
        }

        a:active {
            color: #0000FF;
        }

        .style3 {
            font-size: 14px
        }

        .style4 {
            font-size: 10px
        }

        .style5 {
            color: #FF0000
        }


        -->
    </style>


</head>

<body>


<div class="form-inline" style="margin-bottom: 10px;">
    <label>日期:</label>
    <input type="text" name="delidate" class="form-control" value="<?= $showDate ?>" id="datepicker"
        onclick="WdatePicker({maxDate:'',isShowClear:false})" style="width:125px" readonly>

    <button class="btn btn-default" onclick="btnSubmit();">查詢</button>

</div>
    


<div align="center" style="width:995; padding:0px 8px;">

    <table class="table table-striped" width="100%" border="1" cellspacing="0" cellpadding="8" style="padding:8px;">
        <tbody>
        <tr>
            <td align="center" bgcolor="#CCCCCC"><strong>PO#</strong></td>
            <td align="center" bgcolor="#CCCCCC"><strong>分店</strong></td>
            <td align="center" bgcolor="#CCCCCC"><strong>收貨日期</strong></td>
<!--             <td align="center" bgcolor="#CCCCCC"><strong>PO日期</strong></td> -->
            <td align="center" bgcolor="#CCCCCC"><strong>狀態</strong></td>
            <td align="center" bgcolor="#CCCCCC"><strong>總數($)</strong></td>

        </tr>
        <?php
        $sql = "SELECT chr_po_no, DATE(order_date) as order_date, DATE(DATE_ADD(insert_date, INTERVAL 1+chr_phase DAY)) as deli_date, SUM(int_default_price * ifnull(T0.int_qty_received,T0.int_qty)) as po_total, 'warehouse' as order_type , int_user , T2.chr_report_name 
                FROM tbl_order_z_dept T0 
                    LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
                    LEFT JOIN tbl_user T2 ON T0.int_user = T2.int_id
                WHERE DATE(DATE_ADD(insert_date, INTERVAL 1+chr_phase DAY)) = '$showDate'
                AND T0.status in (1,99)
                GROUP BY chr_po_no , int_user
                ORDER BY deli_date desc , T2.txt_login, chr_po_no;";
        //		die($sql);
        $result = mysqli_query($con, $sql) or die($sql);
        while ($record = mysqli_fetch_assoc($result)) {
            //收貨時間沒到不顯示
//             if (strtotime($record['deli_date']) > strtotime(date("Y-m-d"))) {
// //            var_dump(strtotime($record[deli_date]).'++++');
// //                var_dump(strtotime(date("Y-m-d")));
//                 continue;
//             }

            ?>
            <tr>
                <td align="center">
                    <a href="CMS_invoice_detail.php?po=<?= $record['chr_po_no'] ?>&id=<?= $record['int_user'] ?> " target="_blank"><?= $record['chr_po_no'] ?></a>
                </td>
                <td align="center">
				<span>
				<?= $record['chr_report_name'] ?>
				</span>
                </td>
                <td align="center"><?= $record['deli_date'] ?></td>
<!--                 <td align="center" bgcolor="#FFFFFF"><span class="style4"><?= $record['order_date'] ?></span></td> -->
                <td align="center">未收</td>
                <td align="right"><?= number_format($record['po_total'], 2, '.', ',') ?></td>

            </tr>
        <? } ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function btnSubmit(){
        window.location.href = "/CMS_invoice_edit.php?delidate="+$('#datepicker').val();
    }
</script>

</body>

</html>