<?php
require($DOCUMENT_ROOT . "connect.inc");
$timestamp = gettimeofday("sec");
if ($_REQUEST[car]) {
    $filterCar = $_REQUEST[car];
}
//echo "<PRE>";
//die();

$showDate = $_REQUEST[checkDate];
$today = date('Y-m-d', $timestamp);
IF ($showDate == "") $showDate = date('Y-m-d', $timestamp);

$checkDate = explode("-", $showDate);
$checkDate[1] = substr("00" . $checkDate[1], -2);
$checkDate[2] = substr("00" . $checkDate[2], -2);
$sqlDate = $checkDate[0] . "-" . $checkDate[1] . "-" . $checkDate[2];

$report = null;
$sql = "SELECT * FROM tbl_order_check WHERE disabled = 0 AND int_id = $_GET[id];";
$result = mysqli_query($con, $sql) or die ($sql);
$report = mysqli_fetch_assoc($result);

//送貨時間
$deli_date = date("Y-m-d", mktime(0, 0, 0, $checkDate[1], $checkDate[2] + $report[int_num_of_day], $checkDate[0]));


$type = $report['int_type'] - 1;
$cat = 'tbl_order_z_cat';
$grp = 'tbl_order_z_group';
$menu = 'tbl_order_z_menu';
$order = 'tbl_order_z_dept';
$tbl = Array("cat" => $cat, "grp" => $grp, "menu" => $menu, "order" => $order);


//prepare Menu
$arySort = Array();
$aryTempMenu = explode(", ", $report[chr_item_list]);

foreach ($aryTempMenu as $value) {
    $sort = explode(":", $value);
    $arySort[] = ("( $sort[0], $sort[1] )");
}
$search_id = join(", ", $arySort);


$aryMenu = Array();
$sql_d = "drop temporary table if exists tmp;";
$sql_1 = "create temporary table tmp( sort int, id int);";
$sql_2 = "insert into tmp VALUES $search_id;";
$sql_3 = "SELECT sort, menu.chr_name, menu.chr_no FROM tmp LEFT JOIN tbl_order_z_menu menu ON menu.int_id = tmp.id WHERE menu.status <> 4 ORDER BY sort;";

// var_dump($sql_d);

mysqli_query($con, $sql_d) or die ($sql_d);
mysqli_query($con, $sql_1) or die ($sql_1);
mysqli_query($con, $sql_2) or die ($sql_2);
$result = mysqli_query($con, $sql_3) or die ($sql_3);
mysqli_query($con, $sql_d) or die ($sql_d);

while ($record = mysqli_fetch_assoc($result)) {
    $aryMenu[$record[chr_no]] = $record;
}
$code = "";
$code .= "'";
foreach ($aryMenu as $menu) {
    $code .= "$menu[chr_no]','";
}
$code = rtrim($code, ",'");
$code .= "'";
//print_r($aryMenu);
//prepare Menu

//prepare shop
$aryAllShop = Array();
//查詢所有分店
$sql = "SELECT int_id, chr_ename, txt_name FROM tbl_user WHERE int_dept = 2 ORDER BY int_sort ";
$result = mysqli_query($con, $sql) or die ($sql);
//意義不明?chr_ename並不是數字,跟100比較不知道是什麼意思
while ($record = mysqli_fetch_assoc($result)) {
    if ($record[chr_ename] < 100)
        $record[type] = "BKG";
    else if ($record[chr_ename] > 100)
        $record[type] = "RBS";
    else
        $record[type] = "OTHER";

    $aryAllShop[] = $record;
}
//	print_r($aryAllShop);
$aryDisplayShop = Array();
//int_all_shop暫時不知道是什麼,全是等於1的
if ($report[int_all_shop] != 1) {
    $aryExtra = explode(",", $report[chr_shop_list]);

    foreach ($aryAllShop as $key => $shop) {
        if (in_array($shop[int_id], $aryExtra)) {
            $aryDisplayShop[] = $shop[int_id];
            continue;
        }
        $aryDisplayShop[] = $shop[int_id];
    }
} else {
    foreach ($aryAllShop as $key => $shop) {
        $aryDisplayShop[] = $shop[int_id];
    }
}
//prepare shop

//prepare report
$addOrder = $_GET[addOrder];
$aryInfo = Array();
$sql = "SELECT tbl_user.int_id as shop_id, a.int_user, a.int_qty, tbl_user.txt_name, tbl_user.chr_report_name, tbl_user.chr_ename, a.chr_no FROM ";
$sql .= "(SELECT o.int_user, o.int_qty, i.chr_no FROM tbl_order_z_dept o LEFT JOIN tbl_order_z_menu i ";
$sql .= "ON o.int_product = i.int_id WHERE i.chr_no in ($code) AND DATE(DATE_ADD(o.order_date, INTERVAL 1+o.chr_phase DAY)) = '$deli_date' ";
$sql .= "AND (o.status IN (0,1,5,98,99,3))";


$sql .= "GROUP BY o.int_user, i.chr_no) a ";
$sql .= "LEFT JOIN tbl_user ON a.int_user = tbl_user.int_id ";
$sql .= "ORDER BY tbl_user.chr_ename ";


//die($sql);
$result = mysqli_query($con, $sql) or die ($sql);
while ($record = mysqli_fetch_assoc($result)) {
    $aryInfo[] = $record;
}
$product = Array();
$total = Array();
foreach ($aryInfo as $value) {
    if ($value[int_qty] != 0) {
        $product[$value[txt_name]]["shop_id"] = $value[shop_id];
        $product[$value[txt_name]]["ename"] = $value[chr_ename];
        $product[$value[txt_name]][$value[chr_no]] = $value[int_qty] + 0;


        if ($value[chr_report_name]) {
            $product[$value[shop_id]]["txt_name"] = $value[chr_report_name];
        } else {
            $product[$value[shop_id]]["txt_name"] = $value[chr_ename] . "<br>" . $value[txt_name];
        }
        $product[$value[shop_id]][$value[chr_no]] = $value[int_qty] + 0;

        if (in_array($value[shop_id], $aryDisplayShop))
            $total[$value[chr_no]] += $value[int_qty] + 0;

    }
}

//	var_dump($product);
//	var_dump($aryDisplayShop);

$Page = Array();
$pageID = 1;
foreach ($aryDisplayShop as $key => $value) {
    if ($product[$value][txt_name] != "") {
        //for($i=0;$i<=3;$i++){
        $Page[$pageID][$key + $i * 10] = $value;
        if (count($Page[$pageID]) == 10) {
            $pageID++;
        }
        //}
    }
}

//        var_dump($Page);
//echo $report[int_hide];
//die();
//echo "<PRE>";
//print_r($Page);
//print(count($product));
//echo "</PRE>";
//die();

?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <title>內聯網</title>
    <link href="js/My97DatePicker/skin/WdatePicker.css" rel="stylesheet" type="text/css">
    <script src="js/jquery-1.9.1.min.js"></script>
    <script src="js/My97DatePicker/WdatePicker.js"></script>
    <script src="js/parser.js"></script>
    <style>
        <!--
        .style1 {
            font-size: 34px
        }

        .style3 {
            font-size: 16px;
        }

        .style6 {
            font-size: 22px;
            font-weight: bold;
        }

        .data {
            max-height: 32px;
            height: 32px;
            min-height: 32px;
            vertical-align: middle;
            padding: 4px;
        }

        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 297mm;
            min-height: 203mm;
            padding: 4mm;
            background: white;
        }

        -->
    </style>
</head>
<body>
<?php
$pageCount = 1;
if (count($Page) == 0)
    echo '<font size="5" color="red"><br>柯打沒有內容 !!</font>';
while ($pageCount <= count($Page)) { ?>

    <div class="page">
        <div width="100%">
            <div width="50%" align="left">列印時間: <?= date('Y-m-d H:i', $timestamp) ?></div>
            <div width="50%" align="right"><?= $pageCount ?>/<?= count($Page) ?></div>
        </div>

        <br/>
        <span class="style1"><?= $report[chr_report_name] ?> <?= $add ?></span>
        <span class="style1"
              style="margin-left:400px;">出貨日期：<?= date("j/n/Y (D)", mktime(0, 0, 0, $checkDate[1], $checkDate[2] + $report[int_num_of_day], $checkDate[0])); ?></span>
        <hr/>
        <table border="1" cellpadding="0" cellspacing="0">
            <tr bgcolor="#CCFFFF">
                <td align="center" style="width:30px; height:40px;"><strong>#</strong></td>
                <td align="center" style="width:90px"><strong>產品編號</strong></td>
                <td align="center" style="max-width:130px; min-width:130px; width:130px;"><strong>產品名稱</strong></td>
                <td bgcolor="#FFFFCC" align="center" style="width:85px"><strong>Total</strong></td>
                <?php
                foreach ($Page[$pageCount] as $key => $value) { ?>
                    <td align="center" style="width:85px"><strong><?= $product[$value][txt_name] ?></strong></td>
                <?php } ?>
            </tr>
            <?php
            $count = 1;
            // var_dump($aryMenu);
            foreach ($aryMenu as $key => $value) {
                if (strpos($key, "-") === false) {
                    $bg = ($count % 2 == 0) ? "#EEEEEE" : "#FFFFFF" ?>
                    <tr bgcolor="<?= $bg ?>">
                        <td class="data style3" align="center"><?= $count ?></td>
                        <td class="data style3" align="center"><?= $aryMenu[$key][chr_no] ?></td>
                        <td class="data style3" align="center"
                            style="max-width:130px; min-width:130px; width:130px;"><?= $aryMenu[$key][chr_name] ?></td>
                        <td class="data style6" align="center" bgcolor="#FFFFCC">
                            <?= $total[$aryMenu[$key][chr_no]] + 0 ?>
                        </td>
                        <?php foreach ($Page[$pageCount] as $shop) { ?>
                            <td align="center" class="data style6"
                                width="<?= $width ?>"><?= $product[$shop][$aryMenu[$key][chr_no]] ?></td>
                        <?php } ?>
                    </tr>
                    <?php $count++;
                }
            } ?>
        </table>
    </div>
    <?php $pageCount++;
} ?>
</body>