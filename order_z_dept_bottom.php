<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order_z_dept.php';
    header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");
$testing_all_item = true;

$maxQTY = 300;
$timestamp = gettimeofday("sec");
$curtime = date('Hi', $timestamp);
$deliW = date('w', $timestamp + 86400 * ($_SESSION['advance'] + 1));
//	var_dump($deliW);
$order_user = $_SESSION[order_user] ? $_SESSION[order_user] : $_SESSION[user_id];
//echo $curtime;
//echo $_SESSION['advance'];

/*
 $stop_date = new DateTime();
 $advance = $_SESSION['advance'];
 echo 'date before day adding: ' . $stop_date->format('Y-m-d') . "<br>";
 $count = 0;
 while($count<=$advance){
    $stop_date->modify('+1 day');
    echo 'date after adding 1 day: ' . $stop_date->format('Y-m-d') . "<br>";
    $count++;
 }
*/

?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網</title>
    <style type="text/css">
        input.item {
            font-size: 12px;
        }

        .div {
            width: 100%;
            height: 100%;
            overflow: auto;
            overflow-x: hidden;
        }

        body {
            margin: 0;
        }

        .ss {
            color: blue;
        }

        .item {
            color: black !important;
        }

        a {
            text-decoration: none
        }

    </style>
    <script src="js/jquery.min.js"></script>
    <script>
        function addItem(id) {
            console.log(window.frames);

        }

        function dropItem(id) {
            alert(id);
        }

        function add(itemid, id, suppName, itemName, uom, base, min, overtime) {

            var topWin = window.top.document.getElementById("leftFrame").contentWindow;
            // var qty = 0;
            // var qty2 = topWin.document.$("#qty100001").val();
            //console.log($("#leftFrame").contents());

            var count = $(topWin.document).find(".cart,.cartold").length;
            // count += $(topWin.document).find(".cartold").length;
            // alert(count);

            var item = topWin.document.getElementById("qty" + id);
            if (item) {
                // console.log($(item).hasClass('cartdel'));
                var parent = $(item).parents(".cartdel");
                // console.log(parent.attr("class"));
                if (parent.attr("class")) {
                    parent.removeClass('cartdel').addClass("cartold");
                    parent.show();
                    item.value = base;
                    return true;
                }
                // qty = topWin.document.getElementById(id).style.display='none';
                var qty = topWin.document.getElementById("qty" + id).value;
                topWin.document.getElementById("qty" + id).value = parseInt(qty) + base;
                var qty = parseInt(qty) + base;
                var maxQty = <?=$maxQTY?>;
                if (qty > maxQty) {
                    alert("每項目數量最多只可為「" + maxQty + "」");
                    topWin.document.getElementById("qty" + id).value = maxQty;
                } else if (qty < min) {
                    alert("該項目最少落單數量為「" + min + "」");
                    topWin.document.getElementById("qty" + id).value = min;
                } else if (qty % base != 0) {
                    alert("該項目數量必須以「" + base + "」為單位");
                    var newQty = qty - qty % base;
                    topWin.document.getElementById("qty" + id).value = newQty;
                }
                ;
            } else {
                var bg = "#F0F0F0";
                if (count & 1) {
                    bg = "#F0F0F0";
                } else {
                    bg = "#FFFFFF";
                }

                var tr = "<tr bgcolor=\"" + bg + "\" class=\"cart\" id=" + id + " data-itemid=" + itemid + ">\n" +
                    "        <td width=\"10\" align=\"right\">" + (count + 1) + ".</td>\n" +
                    "        <td><font color=\"blue\" size=\"-1\">" + suppName + " </font> " + itemName + ",&nbsp;" + id + "</td>\n" +
                    "        <td align=\"center\">\n";

                //超過截單時間
                if (overtime) {
                    tr += "<img src=\"images/alert.gif\" width=\"20\" height=\"20\"> ";
                }
                // "        <img src="images/alert.gif" width="20" height="20">        \n" +
                tr += "\t\t</td>\n" +
                    "        <td width=\"100\" align=\"center\">x \n" +
                    "          <input class=\"qty\" type=\"tel\" id=\"qty" + id + "\" name=\"1136\" type=\"text\" value=" + min + " data-base=" + base + " data-min=" + min + " size=\"3\" maxlength=\"4\" autocomplete=\"off\"></td>\n" +
                    "        <td align=\"center\">" + uom + "</td>\n" +
                    "        <td align=\"center\">\n" +
                    "\t\t\t<a href=\"#\" class=\"delnew\"><font color=\"#FF6600\">X</font></a>\n" +
                    "\t\t</td>\n" +
                    "      </tr>";

                $(topWin.document).find(".blankline").before(tr);
                count += 1;

                // if(count > 0){
                //     $(topWin.document).find(".cart").eq(count-1).after(tr);
                //     count += 1;
                // }else{
                //
                //     // alert(count);
                // }

            }
            // console.log(qty);
        }

        function drop(id, base, min) {

            var topWin = window.top.document.getElementById("leftFrame").contentWindow;
            // var qty = 0;
            // var qty2 = topWin.document.$("#qty100001").val();
            //console.log($("#leftFrame").contents());

            // var count = $(topWin.document).find(".cart").length;

            if (topWin.document.getElementById("qty" + id)) {
                // qty = topWin.document.getElementById(id).style.display='none';
                var qty = topWin.document.getElementById("qty" + id).value;
                topWin.document.getElementById("qty" + id).value = parseInt(qty) - base;
                var qty = parseInt(qty) - base;
                var maxQty = <?=$maxQTY?>;
                if (qty > maxQty) {
                    alert("每項目數量最多只可為「" + maxQty + "」");
                    topWin.document.getElementById("qty" + id).value = maxQty;
                } else if (qty < min) {
                    alert("該項目最少落單數量為「" + min + "」");
                    topWin.document.getElementById("qty" + id).value = min;
                } else if (qty % base != 0) {
                    alert("該項目數量必須以「" + base + "」為單位");
                    var newQty = qty - qty % base;
                    topWin.document.getElementById("qty" + id).value = newQty;
                }
                ;
            }
            // console.log(qty);
        }
    </script>
</head>

<body style="overflow-y:hidden" bgcolor="#697caf" text="#FFFFFF" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF"
      topmargin="0">
<hr>

<div class="div">
    <form name="orderByFi" action="order_z_dept_left.php" target="leftFrame" method="get">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td height="39"></td>
                <td align="left">&nbsp;

                </td>
                <td width="50" align="center" bgcolor="#FFFF00" style="color:black;">現貨</td>
                <!-- <td width="50" align="center" bgcolor="#D7710D">新貨</td>
                <td width="50" align="center" bgcolor="#008081">季節貨</td>
                -->
                <td width="50" align="center" bgcolor="#7D0101">已截單</td>
                <!--<td width="50" align="center" bgcolor="#666666">暫停</td>-->
            </tr>
        </table>
        <form name="orderByDirect" action="order_z_dept_left.php" target="leftFrame" method="get">
            <br/>
            <br/>
            <input type="hidden" name="action" value="DirectAdd">
            <table border="0" cellpadding="0" cellspacing="5">
                <tr>
                    <?php
                    //分店查看,已截單內容不顯示
                    $sql = "
  SELECT 
    T0.int_id AS itemID,
    T0.chr_name AS itemName,
    T0.chr_no,
    T1.chr_name AS UoM,
    T0.status,
    T0.txt_detail_1,
    T0.txt_detail_2,
    T0.txt_detail_3,
    T0.chr_image,
	T0.int_phase,
	T0.chr_cuttime,
	T0.int_base,
	T0.int_min,
	T0.chr_canordertime,
	LEFT(tbl_order_z_cat.chr_name, 2) AS suppName
  FROM
    tbl_order_z_menu T0
        INNER JOIN tbl_order_z_unit T1 ON T1.int_id = T0.int_unit
        LEFT JOIN tbl_order_z_menu_v_shop T2 ON T2.int_menu_id = T0.int_id
        INNER JOIN tbl_order_z_group ON T0.int_group = tbl_order_z_group.int_id
		INNER JOIN tbl_order_z_cat ON tbl_order_z_group.int_cat = tbl_order_z_cat.int_id
             
  WHERE
    T0.int_group = '$_REQUEST[groupid]'
        AND T0.status NOT IN(2, 4)
		AND T2.int_user_id  = '$order_user'
  GROUP BY T0.int_id
  ORDER BY T0.int_sort, T0.int_id";

                    //  AND (
                    //		    (T0.int_phase-1 <= '$_SESSION[advance]' AND DATE_FORMAT(NOW(),\"%H%i\") < T0.chr_cuttime)
                    //		    OR (T0.int_phase <= '$_SESSION[advance]' AND DATE_FORMAT(NOW(),\"%H%i\") > T0.chr_cuttime)
                    //		    )
                    //  die($sql);
                    //用戶等級是管理員
                    if ($_SESSION[type] == 3) {
                        $sql = "
	  SELECT 
		T0.int_id AS itemID,
		T0.chr_name AS itemName,
		T0.chr_no,
		T1.chr_name AS UoM,
		T0.status,
		T0.txt_detail_1,
		T0.txt_detail_2,
		T0.txt_detail_3,
		T0.chr_image,
		T0.chr_cuttime,
		T0.int_phase,
		T0.int_base,
		T0.int_min,
		T0.chr_canordertime,
		LEFT(tbl_order_z_cat.chr_name, 2) AS suppName
		
	  FROM
		tbl_order_z_menu T0
			INNER JOIN tbl_order_z_unit T1 ON T1.int_id = T0.int_unit
			INNER JOIN tbl_order_z_group ON T0.int_group = tbl_order_z_group.int_id
			INNER JOIN tbl_order_z_cat ON tbl_order_z_group.int_cat = tbl_order_z_cat.int_id
	  WHERE
		T0.int_group = '$_REQUEST[groupid]'
			AND T0.status NOT IN(2, 4)
	  GROUP BY T0.int_id
	  ORDER BY T0.int_sort, T0.int_id";
                    }

                    //die($sql);
                    $result = mysqli_query($con, $sql) or die();
                    $count = 1;
                    $countdisplay = 1;

                    WHILE ($record = mysqli_fetch_array($result)) {

                        $styleTD = "background-color:#FFFF00; ";
                        $styleFont = "color: black; ";
                        $disableButton = "";
                        $overTime = false;

                        //把字符串分解成 可以下單的日子的數組
                        $canOrderTime = explode(",", $record['chr_canordertime']);

                        //獲取今天星期(數字),$dayW用於循環
                        $todayW = $dayW = date('w');

                        $phase = $newPhase = $record['int_phase'];

                        //送貨日期不在可下單日期時
                        if (!in_array($deliW, $canOrderTime)) {
                            $overTime = true;
                        }

                        //phase小於等於0,表示沒有截單時間,只能管理員下單
                        if ($phase <= 0) {
                            $overTime = true;
                        }

                        if ($record['chr_cuttime'] < $curtime) {
                            $newPhase += 1;
                        }

//      $dayW = 5;

                        if (($dayW + $newPhase) >= 7 && !in_array('0', $canOrderTime)) {
                            $newPhase += 1;
                        }

//      echo $newPhase;

//
//	if( $record['chr_cuttime'] < $curtime && $_SESSION['advance'] < $newPhase ){
//		$overTime = true;
////		$record['status'] = 999;
////		//#7D0101深紅色 截單顏色
////		$styleTD = "background-color:#7D0101; color:white; ";
////		$styleFont = "color: white;";
////		$disableButton = "disabled";
//	}
//
////      var_dump($newPhase);
////      var_dump($overTime);

//	if ($record['chr_cuttime'] > $curtime && ($_SESSION['advance']+1) < $newPhase ){
                        if (($_SESSION['advance'] + 1) < $newPhase) {
                            $overTime = true;
//        $record['status'] = 999;
//        $styleTD = "background-color:#7D0101; color:white; ";
//        $styleFont = "color: white;";
//        $disableButton = "disabled";
                        }

//	echo $record['status'];

                        //$overTime == true 改變樣式
                        if ($overTime == true) {
                            $styleTD = "background-color:#7D0101; color:white; ";
                            $styleFont = "color: white;";
                        }


//    var_dump($record['itemName']);

                        if ($overTime == true && $_SESSION[type] != 3) continue;
                        ?>

                        <td width="150" height="60" align="center" style="<?= $styleTD ?>">
                            <table width="150px" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td colspan="2" align="center" style="font-size:16px;">
                                        <?php IF ($record['status'] <> 2 && $overTime == true && $_SESSION[type] == 3) { ?>
                                        <a id="itm-<?= $record['itemID'] ?>" href="#" style="<?= $styleFont ?>">
                                            <?php }else { ?>
                                            <a style="<?= $styleFont ?>">
                                                <?php } ?>

                                                <table class="item" width="100%">
                                                    <tr>
                                                        <td colspan="4" align="left"
                                                            style="font-size:12px; <?= $styleFont ?>">
                                                            <?= $record['chr_no']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" align="center" style="font-size:16px;">
                                                            <span style="<?= $styleFont ?>"><?= $record['itemName']; ?>(<?= $record['int_min']; ?>)</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="height:20px; width:50%; font-size:24px; text-align:center"
                                                            colspan="2">
                                                            <!-- <a id="itm-<?= $record['itemID'] ?>" target="leftFrame" href="order_z_dept_left.php?action=add&productid=<?= $record['itemID']; ?>">-->
                                                            <a id="itm-<?= $record['itemID'] ?>" href="#"
                                                               onclick="add(<?= $record['itemID']; ?>,<?= $record['chr_no']; ?>,'<?= $record['suppName']; ?>','<?= $record['itemName']; ?>','<?= $record['UoM']; ?>',<?= $record['int_base']; ?>,<?= $record['int_min']; ?>,<?= $overTime; ?>)">
                                                                <button type="button"
                                                                        style="height:100%; width:100%; font-size:18px;" <?= $disableButton ?>>
                                                                    +
                                                                </button>
                                                            </a>
                                                        </td>
                                                        <td style="height:20px; width:50%; text-align:center"
                                                            colspan="2">
                                                            <a id="itm-<?= $record['itemID'] ?>" href="#"
                                                               onclick="drop(<?= $record['chr_no']; ?>,<?= $record['int_base']; ?>,<?= $record['int_min']; ?>)"
                                                               style="color:black;">
                                                                <button type="button"
                                                                        style="height:100%; width:100%; font-size:18px;" <?= $disableButton ?>>
                                                                    -
                                                                </button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <?php
                        $count++;
                        if ($count >= 4) {
                            echo "</tr><tr>";
                            $count = 1;
                        }
                    }
                    ?>
                </tr>
            </table>
</div>
</form>
</body>
</html>
