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

        function timePick() {
            WdatePicker({dateFmt: 'HH:mm'});
        }
    </script>

</head>
<body>
<?php
switch ($_REQUEST[action]) {
    case "new":
        ?>
        <form name="search" action="" method="post">
            <input type="hidden" name="action" value="add">
            <table width="90%" border="0" cellspacing="1" cellpadding="6">
                <tr>
                    <td bgcolor="#EB8201"><span class="style1">設定 &gt; 細類 &gt; 新增</span></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">細類名稱　
                        <input name="name" type="text" id="name" size="50"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">　排　序　
                        <input name="sorting" type="text" id="sorting" value="999" size="5" maxlength="3"
                               onKeyDown="return isNumber(event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">　大　類　
                        <select name="cat" id="cat">
                            <?php
                            $sql_select = "SELECT int_id, chr_name, int_sort, status, int_page FROM tbl_order_z_cat WHERE status = 1 ORDER BY int_sort ";
                            $result_select = mysqli_query($con, $sql_select) or die($sql_select);
                            WHILE ($record_select = mysqli_fetch_array($result_select)) {
                                ?>
                                <option value="<?php echo $record_select[int_id]; ?>" <?php IF ($record[int_cat] == $record_select[int_id]) echo "selected"; ?>><?php echo $record_select[chr_name]; ?></option>
                                <?php
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                <tr>
                    <td align="left" bgcolor="#EEEEEE">可　視</td>
                </tr>
                <td bgcolor="#EEEEEE">
                    <input type="hidden" id="data" name="data" value=''/>
                    <div id="select">
                    </div>
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
			tbl_order_z_group.int_id,
			tbl_order_z_group.chr_name,
			tbl_order_z_group.int_sort,
			tbl_order_z_group.status,
			tbl_order_z_group.int_cat,
			tbl_order_z_group.last_modify
		FROM
			tbl_order_z_group
		WHERE
			tbl_order_z_group.int_id = $_REQUEST[id]";
        $result = mysqli_query($con, $sql) or die($sql);
        $record = mysqli_fetch_array($result);
        //print_r($sql);

        $sql = "SELECT T1.int_id, T4.int_id as area_id, T4.chr_name as brand, T1.chr_ename, T1.txt_name
	FROM tbl_order_z_group_v_shop T0
		LEFT JOIN tbl_user T1 ON T0.int_user_id = T1.int_id
		LEFT JOIN tbl_order_z_group T2 ON T0.int_group_id = T2.int_id
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
        <form name="search" action="" method="post">
            <input type="hidden" name="action" value="confirm">
            <input type="hidden" name="id" value="<?php echo $_REQUEST[id]; ?>">
            <input name='chkcuttime' value='0' type='hidden'/>
            <table width="90%" border="0" cellspacing="1" cellpadding="6">
                <tr>
                    <td bgcolor="#EB8201"><span class="style1">設定 &gt; 細類 &gt; 修改</span></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">細類名稱　
                        <input name="name" type="text" id="name" value="<?php echo $record[chr_name]; ?>" size="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">　排　序　
                        <input name="sorting" type="text" id="sorting" value="<?php echo $record[int_sort]; ?>" size="5"
                               maxlength="3" onKeyDown="return isNumber(event);"></td>
                </tr>
                <tr>
                    <td bgcolor="#EEEEEE">　大　類　
                        <select name="cat" id="cat">
                            <?php
                            $sql_select = "SELECT int_id, chr_name, int_sort, status, int_page FROM tbl_order_z_cat WHERE status = 1 ORDER BY int_sort ";
                            $result_select = mysqli_query($con, $sql_select) or die($sql_select);
                            WHILE ($record_select = mysqli_fetch_array($result_select)) {
                                ?>
                                <option value="<?php echo $record_select[int_id]; ?>" <?php IF ($record[int_cat] == $record_select[int_id]) echo "selected"; ?>><?php echo $record_select[chr_name]; ?></option>
                                <?php
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                <tr>
                    <td align="left" bgcolor="#EEEEEE">可　視</td>
                </tr>
                <td bgcolor="#EEEEEE">
                    <input type="hidden" id="data" name="data" value='<?php echo $strJson; ?>'/>
                    <div id="select">
                    </div>
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
        $sql = "UPDATE tbl_order_z_group SET status = '4' ";
        $sql .= "WHERE int_id = '$_REQUEST[id]' ";
        mysqli_query($con, $sql) or die($sql);
        break;
    case "add":
        $last_update = "($_SESSION[user_id]) $_SESSION[user_login] ";
        $sql = "INSERT INTO tbl_order_z_group(chr_name, int_sort, int_cat, chr_name_long, last_modify) VALUE('$_REQUEST[name]','$_REQUEST[sorting]','$_REQUEST[cat]','$_REQUEST[name]', CONCAT('$last_update', NOW()));";
        mysqli_query($con, $sql) or die($sql);

        $data = json_decode($_REQUEST['data'], true);
        $groupID = mysqli_insert_id($con);

        if (!empty($data)) {
            foreach ($data as $item) {
                foreach ($item['item'] as $val) {
                    $code = $val['code'];
                    $sql = " insert INTO tbl_order_z_group_v_shop(int_user_id,int_group_id) VALUES ('" . $code . "'," . $groupID . ")";
                    mysqli_query($con, $sql) or die($sql);
                }
            }
        }
        break;
    case "confirm":
        $last_update = "($_SESSION[user_id]) $_SESSION[user_login] ";

        $sql = "UPDATE tbl_order_z_group SET ";
        $sql .= "chr_name = '$_REQUEST[name]', int_sort = '$_REQUEST[sorting]' ";
        $sql .= ", int_cat = '$_REQUEST[cat]' ";
        $sql .= ", last_modify = CONCAT('$last_update', NOW()) ";
        $sql .= "WHERE int_id = $_REQUEST[id] ";
        mysqli_query($con, $sql) or die($sql);

        $data = json_decode($_REQUEST['data'], true);
        $fid = $_REQUEST['id'];
        $sql = " DELETE FROM tbl_order_z_group_v_shop WHERE int_group_id = " . $fid;
        mysqli_query($con, $sql) or die($sql);
        if (!empty($data)) {
            foreach ($data as $item) {
                foreach ($item['item'] as $val) {
                    $code = $val['code'];
                    $sql = " insert INTO tbl_order_z_group_v_shop(int_user_id,int_group_id) VALUES ('" . $code . "'," . $fid . ")";
                    mysqli_query($con, $sql) or die($sql);
                }
            }
        }
        break;
}
IF (($_REQUEST[action] <> "new") AND ($_REQUEST[action] <> "edit")) {
    ?>
    <table width="90%" border="0" cellspacing="1" cellpadding="6">
        <tr>
            <td bgcolor="#EB8201"><span class="style1">設定 &gt; 細類 &gt; 完成</span></td>
        </tr>
        <tr>
            <td height="100" align="center" bgcolor="#EEEEEE"><a href="CMS_order_group.php">更新完成</a></td>
        </tr>
    </table>
    <?php
}

?>
</body>
</html>