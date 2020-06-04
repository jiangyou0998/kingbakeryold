<?php
session_start();

if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order.php';
    header('Location: login.php');
}

require("connect.inc");

$timestamp = gettimeofday("sec");
$advDays = $_REQUEST[advDays];
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
            <?php if($_SESSION[type] == 3){ ?>
            if ((shop = $("#shop").val()) == '0') {
                alert("請先選擇分店");
                return;
            }
            <?}?>
            if (bool) {
                location.href = "order_z_dept.php?shop=" + shop + "&dept=" + Obj[i].value + "&advance=" + aa;
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
        }

        .style5 {
            font-size: 150%;
            font-weight: bold;
        }

        .daylist {
            font-size: 160%;
        }

        -->
    </style>
</head>

<body>

<?php
//週日有些不能下單
function isSunday($date)
{
    $isSun = false;

    if (date(D, $date) == "Sun")
        $isSun = true;

    return $isSun;
}

function showDayStr($dayCount, $selDate)
{
    $chiYearDay1 = "2015-02-19";

    if (date('Y-m-d', $selDate) == $chiYearDay1) {
        $dayStr = "<font color=\"" . (isSunday($selDate) ? "Yellow" : "Red") . "\">年初一</font>";
    } else {
        switch ($dayCount) {
            case 0:
                $dayStr = "一日後";
                break;
            case 1:
                $dayStr = "兩日後";
                break;
            case 2:
                $dayStr = "三日後";
                break;
            case 3:
                $dayStr = "四日後";
                break;
            case 4:
                $dayStr = "五日後";
                break;
            default:
                $dayStr = "特別安排";
                break;
        }
    }

    return $dayStr;
}

function showWkday($date)
{
    $rtnString = date('n月d日 (', $date);

    switch (date(D, $date)) {
        case "Sun":
            $rtnString .= "日)";
            break;
        case "Mon":
            $rtnString .= "一)";
            break;
        case "Tue":
            $rtnString .= "二)";
            break;
        case "Wed":
            $rtnString .= "三)";
            break;
        case "Thu":
            $rtnString .= "四)";
            break;
        case "Fri":
            $rtnString .= "五)";
            break;
        default:
            $rtnString .= "六)";
            break;
    }

    return $rtnString;
}

?>
<div align="left"><a target="_top" href="order.php" style="font-size: xx-large;">返回</a></div>
<center class="style5">
    請選<span class="style4">送貨日</span>及<span class="style4">部門</span>
    <? if ($_SESSION[type] == 3) { ?>
        <br>
        <br>
        落貨分店


        <select style="width:200px;" id="shop">
            <option value="0">請選擇分店</option>
            <?php
            $sql = "SELECT int_id, txt_name FROM db_intranet.tbl_user WHERE chr_type = 2 AND int_dept = 2 ORDER BY txt_login;";
            $result = mysqli_query($con, $sql) or die($sql);
            while ($record = mysqli_fetch_assoc($result)) { ?>
                <option value="<?= $record[int_id] ?>"><?= $record[txt_name] ?></option>
            <? } ?>
        </select>
        <br>
    <? } ?>
    <br>
    <input type="radio" name="dept" id="radio" value="R" checked>烘焙
    <input type="radio" name="dept" id="radio" value="B">水吧
    <input type="radio" name="dept" id="radio" value="K">廚房
    <input type="radio" name="dept" id="radio" value="F">樓面
</center>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="0">
    <?php
    //計算advance
    for ($count = 0; $count < $advDays; $count++) {
        $selDate = $timestamp + 86400 * ($count + 1);
        ?>
        <tr class="daylist" <?php echo(isSunday($selDate) ? "style=\"background-color:Red\"" : ""); ?>>
            <td align="right" width="48%"><strong><?= showDayStr($count, $selDate); ?></strong></td>
            <td align="left" width="52%"><a
                        href="javascript:opensupplier(<?= $count; ?>);"><strong><?= showWkday($selDate); ?></strong></a>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
<br>
<center class="style3">不同送貨日<span class="style4">必須</span>分單</center>
</body>
</html>
