<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'salesdata.php';
    header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");
$year = date('Y', gettimeofday('sec'));
$month = date('m', gettimeofday('sec'));

if ($_POST[action] == 'upload') {
    $sql = "DELETE FROM tbl_salesdata WHERE DATE(upload_date) = CURDATE() AND shop = '$_SESSION[user_id]' ";
    mysqli_query($con, $sql);

    foreach ($_POST[salesdata] as $id => $price) {
        $price = $price ? $price : '0';
        $sql = "INSERT INTO tbl_salesdata(upload_date, shop, int_item_id, int_price) VALUES(NOW(), $_SESSION[user_id], '$id', '$price');";
        mysqli_query($con, $sql) or die($sql);
    }
    print("<script>alert('已成功提交');</script>");
    //print_r($_POST);
    //die();
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
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <style type="text/css">
        <!--
        body {
            margin-left: 0px;
            margin-top: 0px;
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
<table id="Table_01" width="995" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="13">
            <?php include "head.php"; ?></td>
        <td>&nbsp;</td>
    </tr>
</table>
<div align="center" style="width:995; padding:0px 8px;">
    <h3>未收PO</h3>
    <center><strong><? echo $month; ?>月份貨品收貨成本</strong></center>
    <center class="style5">只供參考，最終以財務部作準！</center>
    <br/>
    <table width="100%" border="1" cellspacing="0" cellpadding="1">
        <tbody>
        <tr>
            <td align="center" bgcolor="#CCCCCC" width="12%"><strong>貨單類別</strong></td>
            <td align="center" bgcolor="#CCCCCC" width="22%"><strong>烘焙($)</strong></td>
            <td align="center" bgcolor="#CCCCCC" width="22%"><strong>水吧($)</strong></td>
            <td align="center" bgcolor="#CCCCCC" width="22%"><strong>廚房($)</strong></td>
            <td align="center" bgcolor="#CCCCCC" width="22%"><strong>樓面($)</strong></td>
        </tr>
        <tr align="right">
            <td align="center">中央工場</td>
            <?php
            $sql = "SELECT chr_dept, SUM(int_qty * int_default_price) as dept_price
					FROM db_intranet.tbl_order_z_dept T0
						LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
					WHERE int_user = $_SESSION[user_id] AND T0.status <> 4 AND MONTH(order_date) = $month AND YEAR(order_date) = $year
					GROUP BY chr_dept;";
            $result = mysqli_query($con, $sql) or die($sql);
            while ($record = mysqli_fetch_assoc($result)) {
                $p[$record[chr_dept]] = $record[dept_price];
            }
            ?>
            <td><?= number_format($p['R'], 0, '.', ',') ?></td>
            <td><?= number_format($p['B'], 0, '.', ',') ?></td>
            <td><?= number_format($p['K'], 0, '.', ',') ?></td>
            <td><?= number_format($p['F'], 0, '.', ',') ?></td>
            <?
            $total['R'] += $p['R'];
            $total['B'] += $p['B'];
            $total['K'] += $p['K'];
            $total['F'] += $p['F'];
            $total_price += ($p['R'] + $p['B'] + $p['K'] + $p['F']);
            ?>
        </tr>
        <tr align="right">
            <td align="center">供應商</td>
            <?php
            $sql = "SELECT chr_dept, SUM(int_qty * int_default_price) as dept_price
					FROM db_intranet.tbl_order_z_dept T0
						LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
					WHERE int_user = $_SESSION[user_id] AND T0.status <> 4 AND MONTH(order_date) = $month AND YEAR(order_date) = $year
					GROUP BY chr_dept;";
            $result = mysqli_query($con, $sql) or die($sql);
            while ($record = mysqli_fetch_assoc($result)) {
                $p[$record[chr_dept]] = 0;
            }
            ?>
            <td><?= number_format($p['R'], 0, '.', ',') ?></td>
            <td><?= number_format($p['B'], 0, '.', ',') ?></td>
            <td><?= number_format($p['K'], 0, '.', ',') ?></td>
            <td><?= number_format($p['F'], 0, '.', ',') ?></td>
            <?
            $total['R'] += $p['R'];
            $total['B'] += $p['B'];
            $total['K'] += $p['K'];
            $total['F'] += $p['F'];
            $total_price += ($p['R'] + $p['B'] + $p['K'] + $p['F']);
            ?>
        </tr>
        <tr align="right" bgcolor="#CCFFFF">
            <td align="center"><strong>小計</strong></td>
            <td><strong><?= number_format($total['R'], 0, '.', ',') ?></strong></td>
            <td><strong><?= number_format($total['B'], 0, '.', ',') ?></strong></td>
            <td><strong><?= number_format($total['K'], 0, '.', ',') ?></strong></td>
            <td><strong><?= number_format($total['F'], 0, '.', ',') ?></strong></td>
        </tr>
        <tr align="right" bgcolor="#CCFFFF">
            <td colspan="4"><strong>加總：</strong></td>
            <td><strong><?= number_format($total_price, 0, '.', ',') ?></strong></td>
        </tr>
        </tbody>
    </table>
    <hr/>
    <table width="100%" border="1" cellspacing="0" cellpadding="8" style="padding:8px;">
        <tbody>
        <tr>
            <td align="center" bgcolor="#CCCCCC"><strong>PO#</strong></td>
            <td align="center" bgcolor="#CCCCCC"><strong>供應商</strong></td>
            <td align="center" bgcolor="#CCCCCC"><strong>收貨日期</strong></td>
            <td align="center" bgcolor="#CCCCCC"><strong>PO日期</strong></td>
            <td align="center" bgcolor="#CCCCCC"><strong>狀態</strong></td>
            <td align="center" bgcolor="#CCCCCC"><strong>總數($)</strong></td>
            <td align="center" bgcolor="#CCCCCC"><strong>收貨</strong></td>
        </tr>
        <?php
        $sql = "SELECT chr_po_no, DATE(order_date) as order_date, DATE(DATE_ADD(order_date, INTERVAL 1+chr_phase DAY)) as deli_date, SUM(int_default_price * int_qty) as po_total, 'warehouse' as order_type
				FROM tbl_order_z_dept T0 
					LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
				WHERE int_user = $_SESSION[user_id] AND T0.status = 1
				GROUP BY chr_po_no
				ORDER BY chr_po_no;";
        //		die($sql);
        $result = mysqli_query($con, $sql) or die($sql);
        while ($record = mysqli_fetch_assoc($result)) {
            //收貨時間沒到不顯示
            if (strtotime($record[deli_date]) > strtotime(date("Y-m-d"))) {
//            var_dump(strtotime($record[deli_date]).'++++');
//                var_dump(strtotime(date("Y-m-d")));
                continue;
            }

            ?>
            <tr>
                <td align="center" bgcolor="#FFFFFF">
                    <a href="grpo2.php?po=<?= $record[chr_po_no] ?>"><?= $record[chr_po_no] ?></a>
                </td>
                <td align="left" bgcolor="#FFFFFF">
				<span class="style3">&nbsp;&nbsp;&nbsp;&nbsp;
				<? if ($record[order_type] == 'warehouse') echo '中央工場'; ?>
				</span>
                </td>
                <td align="center" bgcolor="#FFFFFF"><?= $record[deli_date] ?></td>
                <td align="center" bgcolor="#FFFFFF"><span class="style4"><?= $record[order_date] ?></span></td>
                <td align="center" bgcolor="#FFFFFF">未收</td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format($record[po_total], 2, '.', ',') ?></td>
                <td align="center" bgcolor="#FFFFFF"><input type="checkbox" id="chkedTransed[]" name="chkedTransed[]"
                                                            value="<?= $record[chr_po_no] ?>"
                                                            onclick="checkSelect(this)"></td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>
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