<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order_z_dept.php';
    header('Location: login.php');
}
require("connect.inc");
$timestamp = gettimeofday("sec");

$maxQTY = 300;
$action = $_REQUEST[action];
$order_user = $_SESSION[order_user] ? $_SESSION[order_user] : $_SESSION[user_id];
$dateofweek = date('w', $timestamp + 86400 * ($_SESSION['advance'] + 1));

switch ($_SESSION['OrderDept']) {
    case "R":
        $dept = "烘焙";
        break;
    case "B":
        $dept = "水吧";
        break;
    case "K":
        $dept = "廚房";
        break;
    default:
        $dept = "樓面";
        $_SESSION['OrderDept'] = "F";
        break;
}

switch ($dateofweek) {
    case "1":
        $week = "一";
        break;
    case "2":
        $week = "二";
        break;
    case "3":
        $week = "三";
        break;
    case "4":
        $week = "四";
        break;
    case "5":
        $week = "五";
        break;
    case "6":
        $week = "六";
        break;
    case "0":
        $week = "日";
        break;
}

switch ($action) {
    case 'drop':
        $sql_base = "SELECT int_base, int_min FROM tbl_order_z_menu WHERE int_id = '$_REQUEST[productid]'; ";
        $result_base = mysqli_query($con, $sql_base) or die($sql_base);
        $record_base = mysqli_fetch_row($result_base);
        $base = $record_base[0];
        $min = $record_base[1];

        $sql_check = "SELECT int_id AS orderID, int_qty FROM tbl_order_z_dept ";
        $sql_check .= "WHERE int_user = '$order_user' AND int_product = '$_REQUEST[productid]' AND status = '1' AND chr_phase =  '$_SESSION[advance]' AND chr_dept = '$_SESSION[OrderDept]' ";

        $result_check = mysqli_query($con, $sql_check) or die($sql_check);

        IF (($record_check = mysqli_fetch_row($result_check)) > 0) {
            IF (($record_check[1] - $base) <= 0 || ($record_check[1] - $base) < $min) {
                $sql = "DELETE FROM tbl_order_z_dept WHERE int_id = " . $record_check[0];
                mysqli_query($con, $sql) or die($sql);
            } else {
                $newQty = $record_check[1] - $base;
                $sql = "UPDATE tbl_order_z_dept SET int_qty_init = $newQty, int_qty = $newQty ";
                $sql .= "WHERE int_id = " . $record_check[0];
                mysqli_query($con, $sql) or die($sql);
            }
        }

        break;
    case 'add':
        $sql_base = "SELECT int_base, int_min FROM tbl_order_z_menu WHERE int_id = '$_REQUEST[productid]'; ";
        $result_base = mysqli_query($con, $sql_base) or die($sql_base);
        $record_base = mysqli_fetch_row($result_base);
        $base = $record_base[0];
        $min = $record_base[1];


        $sql_check = "SELECT int_id AS orderID, int_qty FROM tbl_order_z_dept ";
        $sql_check .= "WHERE int_user = '$order_user' AND int_product = '$_REQUEST[productid]' AND status IN ('0', '1') AND chr_phase =  '$_SESSION[advance]' AND chr_dept = '$_SESSION[OrderDept]' ";

        $result_check = mysqli_query($con, $sql_check) or die($sql_check);

        IF (($record_check = mysqli_fetch_row($result_check)) > 0) {
            IF (($record_check[1] + $base) > $maxQTY) {
                echo "<script>alert('每項目數量最多只可為「" . $maxQTY . "」');</script>";
            }


            $sql = "UPDATE tbl_order_z_dept SET int_qty_init = " . (($record_check[1] + $base) > $maxQTY ? $maxQTY . " " : "int_qty + $base ") . ",int_qty = " . (($record_check[1] + $base) > $maxQTY ? $maxQTY . " " : "int_qty + $base ");
            $sql .= "WHERE int_id = " . $record_check[0];

            mysqli_query($con, $sql) or die($sql);
        } ELSE {
            $sql = "INSERT INTO tbl_order_z_dept (order_date, int_user, int_product, int_qty, chr_ip, status, chr_phase, chr_dept, int_qty_init, insert_date) ";
            $sql .= "VALUES ('";
            //order_date
            $sql .= date('Y/n/j G:i:s', $timestamp);
            $sql .= "','";
            //int_user
            $sql .= $order_user;
            $sql .= "','";
            //int_product
            $sql .= $_REQUEST[productid];
            $sql .= "','";
            //int_qty
            $sql .= $min;
            $sql .= "','";
            //chr_ip
            $sql .= $_SERVER['REMOTE_ADDR'];
            $sql .= "','";
            //status
            $sql .= 1;
            $sql .= "','";
            //chr_phase
            $sql .= $_SESSION['advance'];
            $sql .= "','";
            //chr_dept
            $sql .= $_SESSION['OrderDept'];
            //int_qty_init, insert_date
            $sql .= "', 1, NOW()) ";

            mysqli_query($con, $sql) or die($sql);
        }
        break;
    case 'DirectAdd':
        $moreThanMaxQTY = false;
        foreach ($_GET as $key => $value) {
            if (($value <> "") && ($key <> "Input_x") && ($key <> "Input_y") && ($key <> "Submit_x") && ($key <> "Submit_y") && ($key <> "action")) {
                if ($_SESSION['chr_sap'] >= 'TH501' && $_SESSION['chr_sap'] <= 'TH599' && $value > $maxQTY) {
                    $sql = "SELECT chr_sap FROM tbl_order_z_menu WHERE int_id = $key;";
                    $result = mysql_query($sql) or die($sql);
                    $record = mysql_fetch_row($result);
                    if ($record[0] == "RVG073-1")
                        $maxQTY = 900;
                }
                $sql_check = "SELECT int_id AS orderID, int_qty FROM tbl_order_z_dept ";
                $sql_check .= "WHERE int_user = '$order_user' AND int_product = '$key' AND status = '0' AND chr_phase =  '$_SESSION[advance]' AND chr_dept = '$_SESSION[OrderDept]' ";

                $result_check = mysql_query($sql_check) or die($sql_check);
                IF (($record_check = mysql_fetch_row($result_check)) > 0) {
                    IF (($record_check[1] + $value) > $maxQTY) {
                        $moreThanMaxQTY = true;
                    }

                    $sql = "UPDATE tbl_order_z_dept SET int_qty_init = " . (($record_check[1] + $value) > $maxQTY ? $maxQTY . " " : "int_qty + $value ")
                        . ", int_qty = " . (($record_check[1] + $value) > $maxQTY ? $maxQTY . " " : "int_qty + $value ");
                    $sql .= "WHERE int_id = " . $record_check[0];
                    /*
                                $sql = "UPDATE tbl_order_z_dept SET int_qty = int_qty + $value ";
                                $sql .= "WHERE int_id = ".$record_check['orderID'];
                    */
                    mysql_query($sql) or die($sql);
                } ELSE {
                    IF ($value > $maxQTY) {
                        $moreThanMaxQTY = true;
                        $value = $maxQTY;
                    }

                    $sql = "INSERT INTO tbl_order_z_dept (order_date, int_user, int_product, int_qty, chr_ip, status, chr_phase, chr_dept, int_qty_init, insert_date) ";
                    $sql .= "VALUES ('";
                    $sql .= date('Y/n/j G:i:s', $timestamp);
                    $sql .= "','";
                    $sql .= $order_user;
                    $sql .= "','";
                    $sql .= $key;
                    $sql .= "','";
                    $sql .= $value;
                    $sql .= "','";
                    $sql .= $_SERVER['REMOTE_ADDR'];
                    $sql .= "','";
                    $sql .= 1;
                    $sql .= "','";
                    $sql .= $_SESSION['advance'];
                    $sql .= "','";
                    $sql .= $_SESSION['OrderDept'];
                    $sql .= "', '$value', NOW()) ";

                    mysql_query($sql) or die($sql);
                }
            }
        }

        if ($moreThanMaxQTY) echo "<script>alert('每項目數量最多只可為「" . $maxQTY . "」');</script>";

        break;
    case 'delete':
        $sql = "UPDATE tbl_order_z_dept SET status = 4, order_date = NOW() WHERE int_id = $_REQUEST[id] ";
        mysqli_query($con, $sql) or die($sql);

        break;
    case 'update':
        $sql_base = "SELECT int_base, int_min, int_qty FROM tbl_order_z_dept T0 
		LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id WHERE T0.int_id = '$_REQUEST[int_id]'; ";
        $result_base = mysqli_query($con, $sql_base) or die($sql_base);
        $record_base = mysqli_fetch_row($result_base);
        $base = $record_base[0];
        $min = $record_base[1];
        $oqty = $record_base[2];

        if ($_SESSION['chr_sap'] >= 'TH501' && $_SESSION['chr_sap'] <= 'TH599' && $_REQUEST[int_qty] > $maxQTY) {
            $sql = "
			SELECT T1.chr_sap 
			FROM tbl_order_z_dept T0 
				LEFT JOIN tbl_order_z_menu T1 ON T0.int_product = T1.int_id
			WHERE T0.int_id = $_REQUEST[int_id];";
            $result = mysqli_query($con, $sql) or die($sql);
            $record = mysql_fetch_row($result);
            if ($record[0] == "RVG073-1")
                $maxQTY = 900;
        }

        IF ($_REQUEST[int_qty] > $maxQTY) {
            $qty = $maxQTY;
            echo "<script>alert('每項目數量最多只可為「" . $maxQTY . "」');</script>";
        } ELSE {
            $qty = $_REQUEST[int_qty];
        }

        IF ($_REQUEST[int_qty] % $base != 0) {
            $qty = $oqty;
            echo "<script>alert('該項目數量必須以「" . $base . "」為單位');</script>";
        } else IF ($_REQUEST[int_qty] < $min) {
            $qty = $oqty;
            echo "<script>alert('該項目最少落單數量為「" . $min . "」');</script>";
        }

        $sql = "UPDATE tbl_order_z_dept SET int_qty_init = $qty, int_qty = $qty ";
        $sql .= "WHERE int_id = $_REQUEST[int_id] ";
        mysqli_query($con, $sql) or die($sql);
        break;
}

?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網</title>
    <script src="js/jquery.min.js"></script>
</head>
<style type="text/css">
    <!--
    input.qty {
        width: 40%
    }

    }
    -->
</style>
<body>
<div align="left"><a target="_top" href="select_day_dept.php?advDays=14">返回</a></div>
<!-- <form action="order_z_dept_2.php?action=confirm&dept=<?= $dept ?>" method="post" id="cart" name="cart" target="_top">-->
<div align="right"><strong><font color="#FF0000" size="+3">分店：
            <?php
            $sql_select = "SELECT txt_name FROM tbl_user WHERE int_id = " . $order_user;
            $result_select = mysqli_query($con, $sql_select);
            $record_select = mysqli_fetch_array($result_select);
            echo $record_select[0];
            ?>
        </font></strong></div>
<div align="right"><strong><font color="#FF0000" size="+3">部門：<?= $dept ?>
            <br>送貨日期：<?= date('n月d日 (', $timestamp + 86400 * ($_SESSION['advance'] + 1)) . $week . ")"; ?>
        </font></strong></div>
<table width="100%" height="89%" border="1" cellpadding="10" cellspacing="2" id="shoppingcart">
    <tr>
        <td valign="top">
            <table width="100%" border="0" cellspacing="2" cellpadding="2">
                <?php
                //查询订单详情
                $sql = "
	SELECT 
		tbl_order_z_dept.int_id AS orderID,
		tbl_order_z_menu.chr_name AS itemName,
		tbl_order_z_menu.chr_no,
		tbl_order_z_unit.chr_name AS UoM,
		tbl_order_z_menu.chr_cuttime,
		tbl_order_z_dept.int_qty,
		tbl_order_z_dept.status,
		tbl_order_z_menu.int_phase,
		DATE(tbl_order_z_dept.order_date) AS order_date,
		LEFT(tbl_order_z_cat.chr_name, 2) AS suppName,
		tbl_order_z_menu.int_id AS itemID,
		tbl_order_z_menu.int_base,
		tbl_order_z_menu.int_min
		
	FROM
		tbl_order_z_dept
			INNER JOIN tbl_order_z_menu ON tbl_order_z_dept.int_product = tbl_order_z_menu.int_id
			INNER JOIN tbl_order_z_unit ON tbl_order_z_menu.int_unit = tbl_order_z_unit.int_id
			INNER JOIN tbl_order_z_group ON tbl_order_z_menu.int_group = tbl_order_z_group.int_id
			INNER JOIN tbl_order_z_cat ON tbl_order_z_group.int_cat = tbl_order_z_cat.int_id
	WHERE
		tbl_order_z_dept.int_user = '$order_user'
			AND tbl_order_z_dept.status IN (0 , 1)
			AND tbl_order_z_dept.int_qty > 0
			AND
			 DATE(DATE_ADD(tbl_order_z_dept.order_date, INTERVAL 1+chr_phase DAY)) = 
			 DATE(DATE_ADD(NOW(), INTERVAL $_SESSION[advance]+1 DAY))
			 
			AND tbl_order_z_dept.chr_dept = '$_SESSION[OrderDept]'
			AND tbl_order_z_menu.int_id
	ORDER BY tbl_order_z_menu.chr_no;";

                $result = mysqli_query($con, $sql) or die($sql);
                $count = 0;

                WHILE ($record = mysqli_fetch_array($result)) {
                    $haveoutdate = 0;
                    if ($count & 1) {
                        $bg = "#F0F0F0";
                    } else {
                        $bg = "#FFFFFF";
                    }
                    $count += 1;
                    ?>
                    <tr bgcolor="<?php echo $bg; ?>" class="cartold" id="<?= "$record[chr_no]"; ?>"
                        data-itemid="<?= $record['itemID']; ?>" data-mysqlid="<?= $record['orderID']; ?>">
                        <td width="10" align="right"><?= $count; ?>.</td>
                        <td><font color="blue"
                                  size=-1><?= $record['suppName']; ?> </font><?= "$record[itemName], $record[chr_no]"; ?>
                        </td>
                        <td align="center">
                            <?php
                            $curtime = date('Hi', $timestamp);
                            //if ($record['status'] == 1) echo "<font color=blue size=-1>(已落)</font>";
                            if ($record[chr_cuttime] < $curtime && $_SESSION[advance] < $record[int_phase]) {
                                //if (date('Hi',$timestamp) > $record['chr_cuttime']) {
                                echo "<img title='已超過截單時間' src='images/alert.gif' width='20' height='20'>";
                                $haveoutdate = 1;
                            }

                            if ($record[chr_cuttime] > $curtime && ($_SESSION[advance] + 1) < $record[int_phase]) {
                                //if (date('Hi',$timestamp) > $record['chr_cuttime']) {
                                echo "<img title='已超過截單時間' src='images/alert.gif' width='20' height='20'>";
                                $haveoutdate = 1;
                            }

                            ?>
                        </td>
                        <td width="100" align="center">x
                            <input class="qty" type="tel"
                                   id="qty<?= "$record[chr_no]"; ?>"
                                   name="<?= $record['orderID']; ?>"
                                   type="text" value="<?= round($record['int_qty'], 2); ?>"
                                   data-base="<?= ($record['int_base']); ?>"
                                   data-min="<?= ($record['int_min']); ?>"
                                   size="3" maxlength="4"
                                   autocomplete="off"
                            <?php if ($haveoutdate == 1 && $_SESSION[type] != 3) echo "disabled"; ?>
                            ">
                        </td>
                        <td align="center"><?= $record[3]; ?></td>
                        <td align="center">
                            <?php if ($haveoutdate == 0 || $_SESSION[type] == 3)
                                echo "<a href=\"#\" class=\"del\"><font color=\"#FF6600\">X</font></a>";
                            ?>

                        </td>
                    </tr>
                    <?php
                }
                // var_dump($count);

        //沒有產品時,加載範本內容
                if ($count == 0){
                    $sql = "
        SELECT 
        
        tbl_order_z_menu.int_id AS itemID,
        tbl_order_z_menu.chr_name AS itemName,
        tbl_order_z_menu.chr_no,
        tbl_order_z_unit.chr_name AS UoM,
        tbl_order_z_menu.chr_cuttime,
        tbl_order_z_menu.int_phase,
        LEFT(tbl_order_z_cat.chr_name, 2) AS suppName,
        tbl_order_sample_item.qty,
        tbl_order_z_menu.int_base,
        tbl_order_z_menu.int_min
        
    FROM
        tbl_order_sample_item
            INNER JOIN tbl_order_sample ON tbl_order_sample_item.sample_id = tbl_order_sample.id
            INNER JOIN tbl_order_z_menu ON tbl_order_sample_item.menu_id = tbl_order_z_menu.int_id
            INNER JOIN tbl_order_z_unit ON tbl_order_z_menu.int_unit = tbl_order_z_unit.int_id
            INNER JOIN tbl_order_z_group ON tbl_order_z_menu.int_group = tbl_order_z_group.int_id
            INNER JOIN tbl_order_z_cat ON tbl_order_z_group.int_cat = tbl_order_z_cat.int_id
    WHERE
        tbl_order_sample.user_id = $order_user
        AND tbl_order_sample.sampledate like '%$dateofweek%'
            
    ORDER BY tbl_order_z_menu.chr_no;";

    // var_dump($sql);

    $result = mysqli_query($con, $sql) or die($sql);
                $count = 0;

                while ($record = mysqli_fetch_assoc($result)) {
                    if ($count & 1) {
                        $bg = "#ffcc33";
                    } else {
                        $bg = "#ff9933";
                    }
                    $count += 1;
                    ?>
                    <tr bgcolor="<?php echo $bg; ?>" class="cart" id="<?= "$record[chr_no]"; ?>"
                        data-itemid="<?= $record['itemID']; ?>">
                        <td width="10" align="right"><?= $count; ?>.</td>
                        <td><font color="blue"
                                  size=-1><?= $record['suppName']; ?> </font><?= "$record[itemName], $record[chr_no]"; ?>
                        </td>
                        <td align="center">
                            <?php
                            $curtime = date('Hi', $timestamp);
                            //if ($record['status'] == 1) echo "<font color=blue size=-1>(已落)</font>";
                            if ($record[chr_cuttime] < $curtime && $_SESSION[advance] < $record[int_phase]) {
                                //if (date('Hi',$timestamp) > $record['chr_cuttime']) {
                                echo "<img title='已超過截單時間' src='images/alert.gif' width='20' height='20'>";
                                $haveoutdate = 1;
                            }

                            if ($record[chr_cuttime] > $curtime && ($_SESSION[advance] + 1) < $record[int_phase]) {
                                //if (date('Hi',$timestamp) > $record['chr_cuttime']) {
                                echo "<img title='已超過截單時間' src='images/alert.gif' width='20' height='20'>";
                                $haveoutdate = 1;
                            }

                            ?>
                        </td>
                        <td width="100" align="center">x
                            <input class="qty" type="tel"
                                   id="qty<?= "$record[chr_no]"; ?>"
                                   name=""
                                   type="text" value="<?php if ($haveoutdate == 1 && $_SESSION[type] != 3){echo "0";}else{echo round($record['qty'], 2);}  ?>"
                                   data-base="<?= ($record['int_base']); ?>"
                                   data-min="<?= ($record['int_min']); ?>"
                                   size="3" maxlength="4"
                                   autocomplete="off"
                            <?php if ($haveoutdate == 1 && $_SESSION[type] != 3) echo "disabled"; ?>
                            ">
                        </td>
                        <td align="center"><?= $record[3]; ?></td>
                        <td align="center">
                            <?php if ($haveoutdate == 0 || $_SESSION[type] == 3)
                                echo "<a href=\"#\" class=\"delnew\"><font color=\"#FF6600\">X</font></a>";
                            ?>

                        </td>
                    </tr>
                <?php  
                    }
                }
                ?>
                <tr class="blankline">
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <? $sql = "SELECT txt_name FROM db_intranet.tbl_user WHERE int_id = $order_user ";
                    $result = mysqli_query($con, $sql) or die($sql);
                    $record = mysqli_fetch_assoc($result);
                    ?>
                    <!-- <td colspan="3" valign="middle">分店：<?= $record[txt_name] ?><br>柯打日期：<?= date('Y/n/j', $timestamp) ?><br>柯打合共：<?= $count; ?></td> -->
                    <td colspan="6" align="center"><input id="btnsubmit" name="Input" type="image"
                                                          src="images/Finish.jpg" border="0" onClick="sss();"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- </form>-->
<script>
    $(document).on('click', '.qty', function () {

        var u = navigator.userAgent;
        if (u.indexOf('iPhone') > -1 || u.indexOf('iPad') > -1) {
            // ios端的方法
            this.selectionStart = 0;
            this.selectionEnd = this.val().length;
        } else {
            // pc和安卓端的方法
            $(this).focus().select();
        }

    });

    $(document).on('change', '.qty', function () {
        var qty = $(this).val();
        var maxQty = <?=$maxQTY?>;
        var base = $(this).data('base');
        var min = $(this).data('min');
        if (qty > maxQty) {
            alert("每項目數量最多只可為「" + maxQty + "」");
            $(this).val(maxQty);
        } else if (qty < min) {
            alert("該項目最少落單數量為「" + min + "」");
            $(this).val(min);
        } else if (qty % base != 0) {
            alert("該項目數量必須以「" + base + "」為單位");
            var newQty = qty - qty % base;
            $(this).val(newQty);
        }
        ;
    });

    //刪除(x按鈕),隱藏相應行,原本已經存在的
    $(document).on('click', '.del', function () {
        var parent = $(this).parents(".cartold");
        var parentClass = parent.attr("class");
        parent.removeClass(parentClass).addClass("cartdel");
        parent.hide();
        // console.log(parent.attr("class"));

    });

    //刪除(x按鈕),隱藏相應行,新增的行
    $(document).on('click', '.delnew', function () {
        var parent = $(this).parents(".cart");
        var parentClass = parent.attr("class");
        parent.remove();
        // console.log(parent.attr("class"));

    });

    //點擊完成按鈕提交修改
    function sss() {
        //禁止按鈕重複點擊
        $("#btnsubmit").attr('disabled', true);
        var insertarray = [];
        //insert
        $(".cart").each(function () {

            var id = $(this).attr('id');

            var itemid = $(this).data('itemid');
            // console.log($id);

            var qty = $("#qty" + id).val();
            // console.log($qty);

            var item = {'itemid': itemid, 'qty': qty};
            insertarray.push(item);

        });

        var updatearray = [];
        //insert
        $(".cartold").each(function () {

            var id = $(this).attr('id');

            var mysqlID = $(this).data('mysqlid');

            var itemid = $(this).data('itemid');
            // console.log($id);

            var qty = $("#qty" + id).val();
            // console.log($qty);

            var item = {'mysqlid': mysqlID, 'qty': qty};
            updatearray.push(item);

        });

        var delarray = [];
        //insert
        $(".cartdel").each(function () {

            var mysqlID = $(this).data('mysqlid');

            var item = {'mysqlid': mysqlID};
            delarray.push(item);

        });
        // console.log(JSON.stringify(insertarray));

        $.ajax({
            type: "POST",
            url: "order_z_dept_insert.php",
            data: {
                'insertData': JSON.stringify(insertarray),
                'updateData': JSON.stringify(updatearray),
                'delData': JSON.stringify(delarray)
            },
            success: function (msg) {
                alert('已落貨!');
                window.location.reload();
                // console.log(msg);
            }
        });

    }
</script>


</body>
</html>