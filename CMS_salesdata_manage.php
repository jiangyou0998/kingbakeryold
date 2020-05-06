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


if ($_POST[action] == "update") {
    foreach ($_POST[data] as $id => $value) {
        $sql = "UPDATE tbl_salesdata_item SET int_cal = '$value[cal]', chr_name = '$value[name]', int_sort = '$value[sort]', status = '$value[status]' WHERE int_id = '$id' ";
        mysqli_query($con, $sql) or die($sql);
    }
    echo "<script>alert('更新成功');</script>";
} else if ($_POST[action] == "add") {
    $sql = "INSERT INTO tbl_salesdata_item(chr_name, int_sort, status, int_cal) VALUES ('$_POST[name]', '$_POST[sort]', '$_POST[status]', '$_POST[cal]')";
    mysqli_query($con, $sql) or die($sql);
    echo "<script>alert('新增成功');</script>";
} else if ($_POST[action] == "delete") {
    $sql = "UPDATE tbl_salesdata_item SET status = 4 WHERE int_id = '$_POST[id]'";
    mysqli_query($con, $sql) or die($sql);
    echo "<script>alert('項目已被刪除');</script>";
}


?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <title>內聯網</title>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
</head>

<body>
<div align="center" style="width:994px">
    <h2>新增</h2>

    <form method="post">
        <input type="hidden" value="add" name="action">
        <table width="600px" border="1" style="border-collapse:collapse;" borderColor="black" cellspacing="0"
               cellpadding="4px" align="center">
            <tr bgcolor="CCFFFF">
                <td align="center" width="30%"><b>名稱</b></td>
                <td align="center" width="10%"><b>計算</b></td>
                <td align="center" width="30%"><b>狀態</b></td>
                <td align="center" width="20%"><b>排序</b></td>
                <td align="center" width="20%"><b></b></td>
            </tr>
            <tr>
                <td align="center"><input type="text" name="name" id="txt_name"/></td>
                <td align="center">
                    <select name="cal">
                        <option value="1"> N/A</option>
                        <option value="2"> ＋</option>
                        <option value="3"> －</option>
                    </select>
                </td>
                <td align="center">
                    使用中<input type="radio" name="status" value="1" checked/>　
                    停用<input type="radio" name="status" value="2"/>
                </td>
                <td align="center"><input type="text" name="sort" id="txt_sort" value="0" size="5"/></td>
                <td align="center"><input type="submit" value="新增"></td>
            </tr>
        </table>
    </form>

    <form id="deleteform" method="post">
        <input type="hidden" value="delete" name="action">
        <input type="hidden" value="" name="id">
    </form>

    <br/>
    <br/>
    <form method="POST">
        <input type="hidden" value="update" name="action">
        <table width="80%" border="1" style="border-collapse:collapse;" borderColor="black" cellspacing="0"
               cellpadding="4px">
            <tr bgcolor="CCFFFF">
                <td align="center" width="30%"><b>名稱</b></td>
                <td align="center" width="10%"><b>計算</b></td>
                <td align="center" width="30%"><b>狀態</b></td>
                <td align="center" width="20%"><b>排序</b></td>
                <td align="center" width="10%"><b></b></td>
            </tr>
            <?php
            $sql = "SELECT * FROM tbl_salesdata_item WHERE status <> 4 ORDER BY int_sort; ";
            $result = mysqli_query($con, $sql) or die($sql);
            while ($record = mysqli_fetch_assoc($result)) { ?>
                <tr style="background-color:<?= $record[status] == 1 ? '#ADFFAD' : '#FFFFAD' ?>">
                    <td><input type="text" name="data[<?= $record[int_id] ?>][name]" id="txt_name"
                               value="<?= $record[chr_name] ?>"/></td>
                    <td align="center">
                        <select name="data[<?= $record[int_id] ?>][cal]">
                            <option value="1" <?= $record[int_cal] == 1 ? 'selected' : '' ?>> N/A</option>
                            <option value="2" <?= $record[int_cal] == 2 ? 'selected' : '' ?>> ＋</option>
                            <option value="3" <?= $record[int_cal] == 3 ? 'selected' : '' ?>> －</option>
                        </select>
                    </td>
                    <td align="center">
                        使用中 <input type="radio" name="data[<?= $record[int_id] ?>][status]"
                                   value="1" <?= $record[status] == 1 ? 'checked' : '' ?>/>　
                        停用 <input type="radio" name="data[<?= $record[int_id] ?>][status]"
                                  value="2" <?= $record[status] == 2 ? 'checked' : '' ?>/>
                    </td>
                    <td align="center"><input type="text" name="data[<?= $record[int_id] ?>][sort]" id="txt_sort"
                                              value="<?= $record[int_sort] ?>" size="5"/></td>
                    <td align="center">
                        <button type="button"
                                onclick="deleteform.id.value='<?= $record[int_id] ?>'; deleteform.submit();"
                                style="background-color:#FFADAD;">刪除
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <br/>
        <table width="80%" border="0" style="border-collapse:collapse;" cellspacing="0" cellpadding="4px">
            <tr>
                <td align="right"><input type="submit" value="更新" style="font-size:18px;padding:4px 20px;"/></td>
            </tr>
        </table>
    </form>
</div>
</body>


</html>