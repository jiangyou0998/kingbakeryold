<?php
//session_start();
//if (!($_SESSION[authenticated])) {
//	$_SESSION['status'] = 'neverLogin';
//	$_SESSION[UrlRedirect] = 'CMS_salesdata_manage.php';
//	header('Location: login.php');
//}

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");
if ($_POST[action] == "search") {
    $date = $_POST[date];
    $shop = $_POST[shop];

}

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網</title>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <link href="class.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <script src="js/My97DatePicker/WdatePicker.js"></script>
</head>

<body>
<div align="center" style="width:994px">
    <br/>
    <br/>
    <br/>
    <table border="1" width="800px" cellpadding="24">
        <form name="search" id="search" method="post">
            <input type="hidden" name="action" value="search"/>
            <tr>
                <td align="left" width="50%" style="font-size:24px;padding:4px;">
                    分店:
                    <select name="shop" onchange="search.submit();">
                        <option value="0">--請選擇--</option>
                        <?php
                        $sql = "SELECT * FROM tbl_user WHERE chr_type = 2";
                        $result = mysqli_query($con, $sql) or die($sql);
                        while ($record = mysqli_fetch_assoc($result)) { ?>
                            <option value="<?= $record[int_id] ?>" <?php if ($shop == $record[int_id]) echo "selected"; ?>><?= $record[txt_name] ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td align="right" width="50%" style="font-size:24px;padding:4px;">
                    數據日期:
                    <input name="date" onclick="WdatePicker({onpicked:function(){search.submit();}});" size="10"
                           style="text-align:right;" value="<?= $date ? $date : date("Y-m-d", gettimeofday('sec')) ?>"/>
                </td>
            </tr>
        </form>
        <?php if ($shop && $date) { ?>
            <tr>
                <td colspan="2"><h2 align="center">分 店 每 日 報 數 表</h2></td>
            </tr>
            <tr>
                <td colspan="2" style="padding:4px;">
                    <table style="width:90%; margin:auto; font-size:18px;" border="1">
                        <?php
                        $sql = "SELECT T0.*, T1.int_price
						FROM tbl_salesdata_item T0
							LEFT JOIN tbl_salesdata T1 ON T1.int_item_id = T0.int_id AND DATE(upload_date) = '$date' AND shop = '$shop'
						WHERE status = 1 OR T1.int_price <> 0
						ORDER BY int_sort;";
                        //die($sql);
                        $result = mysqli_query($con, $sql) or die($sql);
                        while ($record = mysqli_fetch_array($result)) {
                            ?>
                            <tr>
                                <td style="padding:8px;width:60%;"><b><?= $record[chr_name] ?></b></td>
                                <td style="padding:8px"><?= $record[int_price] ? $record[int_price] : "0.00" ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>


</html>