<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'salesdata.php';
    header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");

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


</head>

<body>
<table id="Table_01" width="995" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="13">
            <?php include "head.php"; ?></td>
        <td>&nbsp;</td>
    </tr>
</table>
<br/>
<br/>
<div align="center" style="width:995">

    <?php
    if ($_SESSION[type] == 3) {
        echo "<script>location='salesresult.php'</script>";
    } else if ($_SESSION[type] != '2') {
        echo "抱歉，此功能只限分店使用";
    } else {
        ?>
        <form method="POST">
            <input type="hidden" name="action" value="upload"/>
            <table border="1" width="800px" cellpadding="24">
                <tr>
                    <td align="left" width="50%" style="font-size:24px;padding:4px;">
                        分店: <?= $_SESSION[user] ?>
                    </td>
                    <td align="right" width="50%" style="font-size:24px;padding:4px;">
                        數據日期: <?= date("Y-m-d", gettimeofday("sec")) ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><h2 align="center">分 店 每 日 報 數 表</h2></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:4px;">
                        <table style="width:90%; margin:auto; font-size:18px;" border="1">
                            <?php
                            $sql = "SELECT T0.*, T1.int_price
						FROM tbl_salesdata_item T0
							LEFT JOIN tbl_salesdata T1 ON T1.int_item_id = T0.int_id AND DATE(upload_date) = CURDATE() AND shop = '$_SESSION[user_id]'
						WHERE status = 1 
						ORDER BY int_sort;";
                            //die($sql);
                            $result = mysqli_query($con, $sql) or die($sql);
                            while ($record = mysqli_fetch_array($result)) {
                                ?>
                                <tr>
                                    <td style="padding:8px;width:60%;"><b><?= $record[chr_name] ?></b></td>
                                    <td style="padding:8px"><input name="salesdata[<?= $record[int_id] ?>]"
                                                                   type="number"
                                                                   value="<?= $record[int_price] ? $record[int_price] : "0.00" ?>"
                                                                   step="any" style="text-align:right;"
                                                                   onfocus="this.select();"/></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="padding:8px;"><input type="submit" value="提交"
                                                                               style="font-size:24px;padding:4px 18px;"/>
                    </td>
                </tr>
            </table>
        </form>
    <?php } ?>
</div>
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