<?php
//  session_start();
//  if (!($_SESSION[authenticated])) {
//    $_SESSION['status'] = 'neverLogin';
//    $_SESSION[UrlRedirect] = 'CMS_order.php';
//    header('Location: login.php');
//  }

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");
$timestamp = gettimeofday("sec") + 28800;
?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta charset="utf-8"/>
    <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="js/json2.js" type="text/javascript"></script>
    <script src="js/select.js" type="text/javascript"></script>
    <script src="js/My97DatePicker/WdatePicker.js"></script>
    <link href="css/select.css" rel="stylesheet" type="text/css"/>
    <title>內聯網</title>
    <script type="text/javascript">
        $(function () {
            $('#select').Select();
        });
    </script>
    <style type="text/css">
        <!--
        .style1 {
            color: #FFFFFF
        }

        -->
    </style>

    <script type="text/javascript">


        function isNumber(event) {
            if (event) {
                var charCode = (event.which) ? event.which : event.keyCode;
                if (charCode > 31 &&
                    (charCode < 48 || charCode > 57) &&
                    (charCode < 96 || charCode > 105) &&
                    (charCode < 37 || charCode > 40) &&
                    charCode != 8 && charCode != 46 || event.shiftKey || charCode == 190)
                    return false;
            }
            return true;
        }

        function isPrice(target, event) {
            // console.log(event);
            if (event) {
                var charCode = (event.which) ? event.which : event.keyCode;
                //alert(charCode);
                if ((charCode == 190 || charCode == 110) && target.value.indexOf('.') == -1)
                    return true;

                if (charCode > 31 &&
                    (charCode < 48 || charCode > 57) &&
                    (charCode < 96 || charCode > 105) &&
                    (charCode < 37 || charCode > 40) &&
                    charCode != 8 && charCode != 46 || event.shiftKey || charCode == 190)
                    return false;
            }
            return true;
        }

        function timePick() {
            WdatePicker({dateFmt: 'HH:mm'});
        }

        //鉤選或取消時,修改canordertimestr(隱藏)的值
        $(document).on('change', 'input[type=checkbox]', function () {
            var canordertimestr = $('input[type=checkbox]:checked').map(function () {
                return this.value
            }).get().join(',');
            $('#canordertimestr').val(canordertimestr);
            // alert(canordertimestr);
        });

        function checksubmit() {


            var base = $('#base').val();
            var min = $('#min').val();
            var cuttime = $('#cuttime').val();
            var canordertimestr = $('input[type=checkbox]:checked').map(function () {
                return this.value
            }).get().join(',');

            if (min < base) {
                alert("最低消費不能少於單位數量");
                return false;
            }
            if (cuttime > 2359) {
                alert("截單時間不符合標準，請重新輸入。");
                return false;
            }
            if (canordertimestr == "") {
                alert("請選擇出貨期！");
                return false;
            }

            return true;
        }
    </script>

</head>
<body>
<?php

$weekArr = [
    '0' => '星期日',
    '1' => '星期一',
    '2' => '星期二',
    '3' => '星期三',
    '4' => '星期四',
    '5' => '星期五',
    '6' => '星期六',
];


switch ($_REQUEST[action]) {
    case "new":
        ?>
        <form name="search" action="" method="post" onsubmit="return checksubmit()">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="canordertimestr" id="canordertimestr" value=""/>
            <table width="90%" border="0" cellspacing="1" cellpadding="6">
                <tr>
                    <td bgcolor="#EB8201"><span class="style1">設定 &gt; 貨品 &gt; 修改</span></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">貨品名稱　
                        <input name="name" type="text" id="name" value="<?php echo $record[chr_name]; ?>" size="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">編　號　　
                        <input name="no" type="text" id="no" value="<?php echo $record[chr_no]; ?>" size="5"
                               maxlength="7" onKeyDown="return isNumber(event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">排　序　　
                        <input name="sorting" type="text" id="sorting" value="999" size="5" maxlength="3"
                               onKeyDown="return isNumber(event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">細　類　　
                        <select name="group" id="group">
                            <?php
                            $sql_select = "
  SELECT T0.int_id, T0.chr_name, T0.int_sort, T0.status, T1.chr_name as cat_name
  FROM tbl_order_z_group T0 
	LEFT JOIN tbl_order_z_cat T1 ON T0.int_cat = T1.int_id
  WHERE T0.status = 1 
  ORDER BY T1.int_sort, T0.int_sort ";
                            $result_select = mysqli_query($con, $sql_select) or die($sql_select);
                            WHILE ($record_select = mysqli_fetch_array($result_select)) {
                                ?>
                                <option value="<?php echo $record_select[int_id]; ?>" <?php IF ($record[int_group] == $record_select[int_id]) echo "selected"; ?>>
                                    <?php echo $record_select[cat_name]; ?>-<?php echo $record_select[chr_name]; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">單　位　　
                        <select name="unit" id="unit">
                            <?php
                            $sql = "SELECT * FROM tbl_order_z_unit order by int_id;";
                            $unit_result = mysqli_query($con, $sql) or die($sql);
                            WHILE ($unit_record = mysqli_fetch_array($unit_result)) {
                                ?>
                                <option value="<?= $unit_record[int_id] ?>" <?php IF ($record[int_unit] == $unit_record[int_id]) echo "selected"; ?>>
                                    <?= $unit_record[chr_name] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">價　錢　＄
                        <input name="price" type="text" id="price" value="0.00" size="5" maxlength="8"
                               onKeyDown="return isPrice(this, event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">單位數量　
                        <input name="base" type="text" id="base" value="1" size="5" maxlength="3"
                               onKeyDown="return isNumber(event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">最低消費　
                        <input name="min" type="text" id="min" value="1" size="5" maxlength="3"
                               onKeyDown="return isNumber(event);">　
                        ※ 最低消費不能少於單位數量
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">截單日期　
                        <input name="phase" type="text" id="phase" value="1" size="5" maxlength="4"
                               onKeyDown="return isNumber(event);">　
                        ※ 最少一天
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">截單時間　
                        <input name="cuttime" type="text" id="cuttime" value="0000" size="5" maxlength="4"
                               onKeyDown="return isNumber(event);">　
                        ※ 0000 - 2359 （24小時制）
                    </td>
                </tr>

                <tr>
                    <td bgcolor="#EEEEEE">出貨期　

                        <?php

                        //星期日到星期六多選框
                        foreach ($weekArr as $key => $value) {
                            $check = '<label style="padding-right:15px;">';
                            $check .= '<input type="checkbox" name="canordertime" value="' . $key . '" />' . $value;
                            $check .= '</label>';

                            echo $check;
                        }
                        // var_dump(explode(',', $record[chr_canordertime]));
                        ?>

                    </td>
                </tr>

                <tr>
                    <td align="left" bgcolor="#EEEEEE">可　視</td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">
                        <input type="hidden" id="data" name="data" value='<?php echo $strJson; ?>'/>
                        <div id="select"></div>
                    </td>
                </tr>

                <tr>
                    <td bgcolor="#EEEEEE">狀　態　　
                        <input name="status" type="radio" id="status" value="1" checked><span style="color:#00356B"> 現　貨　　　</span>
                        <input name="status" type="radio" id="status" value="3"><span
                                style="color:#D7710D"> 新　貨　　　</span>
                        <input name="status" type="radio" id="status" value="5"><span
                                style="color:#008081"> 季節貨　　　</span>
                        <input name="status" type="radio" id="status" value="2"><span
                                style="color:#FF0000"> 暫　停　　　</span>
                    </td>
                </tr>


                <tr>
                    <td bgcolor="#EEEEEE">　　　　　
                        <input type="submit" name="Submit2" value="確認">
                        <input name="back" type="button" id="back" value="返回" onClick="history.go(-1);"></td>
                </tr>
            </table>
        </form>
        <?php
        break;
    case "edit":
        $sql = "SELECT 
			T0.int_id,
			T0.chr_name,
			T0.int_sort,
			T0.status,
			T0.last_modify,
			T0.int_group,
			T0.chr_no,
			T0.int_unit,
			T0.int_base,
			T0.int_min,
			T0.chr_cuttime,
			T0.int_default_price,
			T1.int_cat,
			T0.int_phase,
			T0.chr_canordertime
		FROM tbl_order_z_menu T0
			LEFT JOIN tbl_order_z_group T1 ON T0.int_group = T1.int_id
			LEFT JOIN tbl_order_z_cat T2 ON T1.int_cat = T2.int_id
		WHERE
			T0.int_id = $_REQUEST[id]";
        $result = mysqli_query($con, $sql) or die($sql);
        $record = mysqli_fetch_array($result);
        //print_r($sql);

        $sql = "SELECT T1.int_id, T4.int_id as area_id, T4.chr_name as brand, T1.chr_ename, T1.txt_name
	FROM tbl_order_z_menu_v_shop T0
		LEFT JOIN tbl_user T1 ON T0.int_user_id = T1.int_id
		LEFT JOIN tbl_order_z_menu T2 ON T0.int_menu_id = T2.int_id
		LEFT JOIN tbl_order_z_shop T3 ON T0.int_user_id = T3.int_user_id
		LEFT JOIN tbl_order_z_area T4 ON T3.int_area_id = T4.int_id
	WHERE T2.int_id = $_REQUEST[id]
	ORDER BY T4.int_sort;";

        $result = mysqli_query($con, $sql) or die($sql);
        while ($select_record = mysqli_fetch_array($result)) {
            $jsonArray[] = $select_record;
        }
        if (!empty($jsonArray)) {
            foreach ($jsonArray as $item) {

                if ($areaID == $item['area_id']) {
                    continue;
                } else {
                    $areaID = $item['area_id'];
                    $itemArray = null;
                    foreach ($jsonArray as $val) {
                        if ($areaID == $val['area_id']) {
                            $itemArray[] = array(
                                'code' => $val['int_id'],
                                'name' => '#' . $val['chr_ename'] . '-' . $val['txt_name']
                            );
                        }
                    }
                }
                $areaArray[] = array(
                    'code' => $item['area_id'],
                    'name' => $item['brand'],
                    'item' => $itemArray
                );
            }
        }
        if (!empty($areaArray)) {
            $strJson = json_encode($areaArray);
        }
        ?>
        <form name="search" action="" method="post" onsubmit="return checksubmit()">
            <input type="hidden" name="action" value="confirm">
            <input type="hidden" name="canordertimestr" id="canordertimestr" value="<?php echo $record[chr_canordertime]; ?>"/>
            <input type="hidden" name="id" value="<?php echo $_REQUEST[id]; ?>">
            <input name='chkcuttime' value='0' type='hidden'/>
            <table width="90%" border="0" cellspacing="1" cellpadding="6">
                <tr>
                    <td bgcolor="#EB8201"><span class="style1">設定 &gt; 貨品 &gt; 修改</span></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">貨品名稱　
                        <input name="name" type="text" id="name" value="<?php echo $record[chr_name]; ?>" size="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">編　號　　
                        <input name="no" type="text" id="no" value="<?php echo $record[chr_no]; ?>" size="5"
                               maxlength="7" onKeyDown="return isNumber(event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">排　序　　
                        <input name="sorting" type="text" id="sorting" value="<?php echo $record[int_sort]; ?>" size="5"
                               maxlength="3" onKeyDown="return isNumber(event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">細　類　　
                        <select name="group" id="group">
                            <?php
                            $sql_select = "
  SELECT T0.int_id, T0.chr_name, T0.int_sort, T0.status, T1.chr_name as cat_name
  FROM tbl_order_z_group T0 
	LEFT JOIN tbl_order_z_cat T1 ON T0.int_cat = T1.int_id
  WHERE T0.status = 1 
  ORDER BY T1.int_sort, T0.int_sort ";
                            $result_select = mysqli_query($con, $sql_select) or die($sql_select);
                            WHILE ($record_select = mysqli_fetch_array($result_select)) {
                                ?>
                                <option value="<?php echo $record_select[int_id]; ?>" <?php IF ($record[int_group] == $record_select[int_id]) echo "selected"; ?>>
                                    <?php echo $record_select[cat_name]; ?>-<?php echo $record_select[chr_name]; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">單　位　　
                        <select name="unit" id="unit">
                            <?php
                            $sql = "SELECT * FROM tbl_order_z_unit order by int_id;";
                            $unit_result = mysqli_query($con, $sql) or die($sql);
                            WHILE ($unit_record = mysqli_fetch_array($unit_result)) {
                                ?>
                                <option value="<?= $unit_record[int_id] ?>" <?php IF ($record[int_unit] == $unit_record[int_id]) echo "selected"; ?>>
                                    <?= $unit_record[chr_name] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">價　錢　＄
                        <input name="price" type="text" id="price" value="<?php echo $record[int_default_price]; ?>"
                               size="5" maxlength="8" onKeyDown="return isPrice(this, event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">單位數量　
                        <input name="base" type="text" id="base" value="<?php echo $record[int_base]; ?>" size="5"
                               maxlength="3" onKeyDown="return isNumber(event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">最低消費　
                        <input name="min" type="text" id="min" value="<?php echo $record[int_min]; ?>" size="5"
                               maxlength="3" onKeyDown="return isNumber(event);">　
                        ※ 最低消費不能少於單位數量
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">截單日期　
                        <input name="phase" type="text" id="phase" value="<?php echo $record[int_phase]; ?>" size="5"
                               maxlength="4" onKeyDown="return isNumber(event);">　
                        ※ 最少一天
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">截單時間　
                        <input name="cuttime" type="text" id="cuttime" value="<?php echo $record[chr_cuttime]; ?>"
                               size="5" maxlength="4" onKeyDown="return isNumber(event);">　
                        ※ 0000 - 2359 （24小時制）
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">出貨期　

                        <?php

                        $canordertimeArr = explode(',', $record[chr_canordertime]);

                        //星期日到星期六多選框
                        foreach ($weekArr as $key => $value) {
                            $check = '<label style="padding-right:15px;">';
                            if (in_array($key, $canordertimeArr)) {
                                $check .= '<input type="checkbox" name="canordertime" value="' . $key . '" checked/>' . $value;
                            } else {
                                $check .= '<input type="checkbox" name="canordertime" value="' . $key . '" />' . $value;
                            }

                            $check .= '</label>';

                            echo $check;
                        }
                        // var_dump(explode(',', $record[chr_canordertime]));
                        ?>

                    </td>
                </tr>

                <tr>
                    <td align="left" bgcolor="#EEEEEE">可　視</td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">
                        <input type="hidden" id="data" name="data" value='<?php echo $strJson; ?>'/>
                        <div id="select"></div>
                    </td>
                </tr>

                <tr>
                    <td bgcolor="#EEEEEE">狀　態　　
                        <input name="status" type="radio" id="status"
                               value="1" <?php if ($record[status] == 1) echo "checked"; ?>><span style="color:#00356B"> 現　貨　　　</span>
                        <input name="status" type="radio" id="status"
                               value="3" <?php if ($record[status] == 3) echo "checked"; ?>><span style="color:#D7710D"> 新　貨　　　</span>
                        <input name="status" type="radio" id="status"
                               value="5" <?php if ($record[status] == 5) echo "checked"; ?>><span style="color:#008081"> 季節貨　　　</span>
                        <input name="status" type="radio" id="status"
                               value="2" <?php if ($record[status] == 2) echo "checked"; ?>><span style="color:#FF0000"> 暫　停　　　</span>
                    </td>
                </tr>

                <tr>
                    <td bgcolor="#EEEEEE">最後修改　<?= $record['last_modify'] ?></td>
                </tr>


                <tr>
                    <td bgcolor="#EEEEEE">　　　　　
                        <input type="submit" name="Submit2" value="確認">
                        <input name="back" type="button" id="back" value="返回" onClick="history.go(-1);"></td>
                </tr>
            </table>
        </form>
        <?php
        break;
    case "delete":
        $sql = "UPDATE tbl_order_z_menu SET status = '4' ";
        $sql .= "WHERE int_id = '$_REQUEST[id]' ";
        mysqli_query($con, $sql) or die($sql);

        $sql = "SELECT T1.int_id, T2.int_id
	  FROM tbl_order_z_menu T0
		  LEFT JOIN tbl_order_z_group T1 ON T0.int_group = T1.int_id
		  LEFT JOIN tbl_order_z_cat T2 ON T1.int_cat = T2.int_id
	  WHERE
		  T0.int_id = $_REQUEST[id]";
        $result = mysqli_query($con, $sql);
        $record = mysqli_fetch_row($result);
        $cat = $record[1];
        $group = $record[0];
        break;
    case "add":

        $last_update = "($_SESSION[user_id]) $_SESSION[user_login] ";
        $sql = "INSERT INTO tbl_order_z_menu(chr_name, chr_no, int_group, int_unit, int_base, int_min, int_default_price, int_sort, chr_cuttime, status, last_modify, chr_canordertime) VALUE ";
        $sql .= "('$_REQUEST[name]', '$_REQUEST[no]', '$_REQUEST[group]', '$_REQUEST[unit]', '$_REQUEST[base]', '$_REQUEST[min]', '$_REQUEST[price]', '$_REQUEST[sorting]', ";
        $sql .= "'$_REQUEST[cuttime]', '$_REQUEST[status]', CONCAT('$last_update', NOW()), '$_REQUEST[canordertimestr]')";
        // die($sql);
        mysqli_query($con, $sql) or die($sql);

        $data = json_decode($_REQUEST['data'], true);
        $menuID = mysqli_insert_id($con);

        if (!empty($data)) {
            foreach ($data as $item) {
                foreach ($item['item'] as $val) {
                    $code = $val['code'];
                    $sql = " insert INTO tbl_order_z_menu_v_shop(int_user_id,int_menu_id) VALUES ('" . $code . "'," . $menuID . ")";
                    mysqli_query($con, $sql) or die($sql);
                }
            }
        }

        $sql = "SELECT int_id, int_cat FROM tbl_order_z_group WHERE int_id = $_REQUEST[group];";
        $result = mysqli_query($con, $sql);
        $record = mysqli_fetch_row($result);
        $cat = $record[1];
        $group = $record[0];

        break;
    //點擊"確認"
    case "confirm":
        $last_update = "($_SESSION[user_id]) $_SESSION[user_login] ";

        $sql = "UPDATE tbl_order_z_menu SET ";
        $sql .= "chr_name = '$_REQUEST[name]', int_sort = '$_REQUEST[sorting]' ";
        $sql .= ", int_group = '$_REQUEST[group]' ";
        $sql .= ", chr_no = '$_REQUEST[no]' ";
        $sql .= ", int_unit = '$_REQUEST[unit]' ";
        $sql .= ", int_default_price = '$_REQUEST[price]' ";
        $sql .= ", int_base = '$_REQUEST[base]' ";
        $sql .= ", int_min = '$_REQUEST[min]' ";
        $sql .= ", chr_cuttime = '$_REQUEST[cuttime]' ";
        $sql .= ", int_phase = '$_REQUEST[phase]' ";
        $sql .= ", status = '$_REQUEST[status]' ";
        $sql .= ", last_modify = CONCAT('$last_update', NOW()) ";
        $sql .= ", chr_canordertime = '$_REQUEST[canordertimestr]' ";
        $sql .= "WHERE int_id = $_REQUEST[id] ";
        // var_dump($_REQUEST['canordertimestr']);die;
        mysqli_query($con, $sql) or die($sql);

        $data = json_decode($_REQUEST['data'], true);
        $fid = $_REQUEST['id'];
        $sql = " DELETE FROM tbl_order_z_menu_v_shop WHERE int_menu_id = " . $fid;
        mysqli_query($con, $sql) or die($sql);
        if (!empty($data)) {
            foreach ($data as $item) {
                foreach ($item['item'] as $val) {
                    $code = $val['code'];
                    $sql = " insert INTO tbl_order_z_menu_v_shop(int_user_id,int_menu_id) VALUES ('" . $code . "'," . $fid . ")";
                    // die($sql);
                    mysqli_query($con, $sql) or die($sql);
                }
            }
        }

        $sql = "SELECT int_id, int_cat FROM tbl_order_z_group WHERE int_id = $_REQUEST[group];";

        $result = mysqli_query($con, $sql);
        $record = mysqli_fetch_row($result);
        $cat = $record[1];
        $group = $record[0];
        break;


}
IF (($_REQUEST[action] <> "new") AND ($_REQUEST[action] <> "edit")) {
    ?>
    <table width="90%" border="0" cellspacing="1" cellpadding="6">
        <tr>
            <td bgcolor="#EB8201"><span class="style1">設定 &gt; 貨品 &gt; 完成</span></td>
        </tr>
        <tr>
            <?php
            if (isset($cat) && isset($group))
                echo '<td height="100" align="center" bgcolor="#EEEEEE"><a href="CMS_order_menu.php?cat=' . $cat . '&group=' . $group . '">更新完成</a></td>';
            else
                echo '<td height="100" align="center" bgcolor="#EEEEEE"><a href="CMS_order_menu.php">更新完成</a></td>';
            ?>
        </tr>
    </table>
    <?php
}

?>
</body>
</html>