<?php
//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");
?>

<?php
$dept = $_SESSION['dept'];
$reqDept = isset($_GET['dept']) ? $_GET['dept'] : 10;

/*
if($_POST[action] == "update"){
	foreach($_POST as $key=>$value){
		if($key == "action") continue;
		
		$sql = "UPDATE tbl_forms SET is_multi_print = $value WHERE int_id = $key";
		mysqli_query($con, $sql) or die($sql);
	}
}
*/

if ($_POST[action] == "update2") {
    $id = $_POST[id];
    $check = $_POST[cb];
    $sql = "UPDATE tbl_forms SET is_multi_print = $check WHERE int_id = $id";
    mysqli_query($con, $sql) or die($sql);
    print(1);
    exit();
}
?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5">
    <script src="js/jquery-1.11.0.min.js"></script>
    <title>內聯網 - 後勤系統</title>
    <script>
        $(document).ready(function () {
            $(".cbUpdate").change(function () {
                var cb = $(this).prop("checked") ? 1 : 0;
                var id = $(this).attr("name");

                var data = {
                    action: "update2",
                    id: id,
                    cb: cb
                }
                $.post("CMS_form.php", data, function (resp) {
                    //alert(resp);
                });
            });
        });

        function confirmation(aa, bb, cc) {
            var answer = confirm('溫馨提示：\n\n是否確認刪除' + cc + '?')
            if (answer) {
                location.href = "CMS_form_2.php?action=del&type=" + aa + "&id=" + bb;
            } else {
                return false;
            }
        }
    </script>
</head>
<body>
<center>
    <input name="add" type="button" id="add" value="新增" onClick="location.href='CMS_form_2.php?action=new&type=14'">　
    <!-- <a href="forms/表格格式_參考.xls" target="_blank">表格格式參考</a>　 -->
    <!-- <input name="update" id="update" type="button" value="更新大量列印" onclick="document.getElementById('updateForm').submit()"> -->
    <br>
    <?php if ($dept == 10) { //AMD ?>
        部門:
        <select id="dept" name="dept" onchange="location='?dept=' + document.getElementById('dept').value; ">
            <option value="0">全部</option>
            <?php $sql = "SELECT * FROM tbl_dept WHERE bl_isvalid = 1";
            $result = mysqli_query($con, $sql) or die($sql);
            while ($record = mysqli_fetch_assoc($result)) {
                ?>
                <option value="<?= $record[int_id] ?>" <?= ($reqDept == $record[int_id]) ? "selected" : "" ?>><?= $record[txt_dept] ?></option>
            <?php } ?>
        </select>
    <?php } ?>

</center>
<br>
<table width="100%" border="1" cellspacing="1" cellpadding="3">
    <form id="updateForm" method="POST">
        <input type="hidden" name="action" value="update">
        <tr>
            <td align="left"><strong>新增日期</strong></td>
            <td align="left"><strong>負責同事</strong></td>
            <td align="left"><strong>編號</strong></td>
            <td align="left"><strong>主旨</strong></td>
            <td align="left"><strong>檔案名稱</strong></td>
            <td align="left"><strong>原始檔案名稱</strong></td>
            <td align="left"><strong>大量列印</strong></td>
            <td align="left">&nbsp;</td>
            <td align="left"><strong>樣本</strong></td>
        </tr>
        <?php
        $sql = "SELECT * FROM tbl_forms ";
        $sql .= "WHERE date_delete = '2000-01-01' ";

        if ($dept == 10) {
            $sql .= ($reqDept) ? "AND int_dept IN ($reqDept) " : " ";
        } else {
            //$sql .= "AND int_dept IN ";
            //$sql .= "(SELECT int_dept FROM tbl_user WHERE int_id = $_SESSION[user_id])";
            $sql .= "AND int_dept IN( $dept )";
        }
        $sql .= "ORDER BY date_modify DESC, int_id DESC ";

        $count = 1;
        $result = mysqli_query($con, $sql) or die($sql);
        while ($record = mysqli_fetch_array($result)) {
            if ($count & 1) {
                $bg = "#FFFFFF";
            } else {
                $bg = "#FFCCFF";
            }
            $count += 1;
            ?>
            <tr>
                <input name="<?= $record[0] ?>" type="hidden" value="0">
                <td align="left" bgcolor="<?php echo $bg; ?>"><?php echo $record[5]; ?></td>
                <td align="left" bgcolor="<?php echo $bg; ?>">
                    <?php
                    $sql_user = "SELECT txt_name FROM tbl_user WHERE int_id = $record[4]";
                    $result_user = mysqli_query($con, $sql_user) or die("invalid query");
                    $record_user = mysqli_fetch_array($result_user);
                    echo $record_user[0]; ?></td>
                <td align="left" bgcolor="<?php echo $bg; ?>"><?php echo $record[8]; ?></td>
                <td align="left" bgcolor="<?php echo $bg; ?>"><?php echo $record[1]; ?></td>
                <td align="left" bgcolor="<?php echo $bg; ?>"><?php echo $record[3]; ?></td>
                <td align="left" bgcolor="<?php echo $bg; ?>"><?php if ($record['first_path'] == '') {
                        echo "---";
                    } else {
                        echo '<a href="forms/' . $record['first_path'] . '" target="_blank">' . $record['first_path'] . '</a>';
                    } ?></td>

                <td align="left" bgcolor="<?php echo $bg; ?>"><input name="<?= $record[0] ?>" type="checkbox"
                                                                     value="1"<?= $record[12] ? "checked" : "" ?>
                                                                     class="cbUpdate"></td>
                <td align="left" bgcolor="<?php echo $bg; ?>">
                    <input name="del" type="button" id="del" value="刪除"
                           onClick="return confirmation(14,<?php echo $record[0]; ?>,'<?php echo $record[1]; ?>');">
                </td>
                <td align="left" bgcolor="<?php echo $bg; ?>">
                    <?php IF ($record[9] == 1) { ?>
                        <a href="CMS_form_3.php?action=edit&id=<?= $record[0] ?>"><strong>修改</strong></a>
                    <?php } else { ?>
                        <a href="CMS_form_3.php?action=upload&id=<?= $record[0] ?>">上載</a>
                    <?php } ?></td>
            </tr>
            <?php
        }
        ?>
    </form>
</table>
</body>
</html>
