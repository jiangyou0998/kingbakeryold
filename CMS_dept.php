<?php
//  session_start();
//  if (!($_SESSION[authenticated])) {
//    $_SESSION['status'] = 'neverLogin';
//    $_SESSION[UrlRedirect] = 'CMS_dept.php';
//    header('Location: login.php');
//  }

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");
if ($_REQUEST[action] == 'update') {
    foreach ($_REQUEST as $key => $value) {
        if (is_numeric($key)) {
            $sql = "UPDATE tbl_order_z_group SET int_sort = $value WHERE int_id = $key;";
            mysqli_query($con, $sql);
        }
    }
}

?>
<html>
<head>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
    <meta http-equiv="Content-Type" content="text/html; charset=big5"/>
    <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="js/json2.js" type="text/javascript"></script>
    <title>內聯網</title>
    <style type="text/css">
        <!--
        .style1 {
            color: #FFFFFF
        }

        -->
    </style>
    <script type="text/JavaScript">
        <!--
        function MM_jumpMenu(targ, selObj, restore) { //v3.0
            eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
            if (restore) selObj.selectedIndex = 0;
        }

        //-->
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
    </script>
</head>
<body>
<table width="90%" border="0" cellspacing="1" cellpadding="6">
    <tr>
        <td bgcolor="#EB8201"><span class="style1">設定 &gt; 部門</span></td>
    </tr>


</table>
<p>
    <input name="Add" type="button" id="Add" value="新增部門" onClick="document.location.href='CMS_dept2.php?action=new';">
<form action="" name="select" method="post">
</p>
<form name="update" action="CMS_dept.php?cat=<?= $_REQUEST[cat] ?>&action=update" method="post">
    <table width="90%" border="0" cellspacing="1" cellpadding="6">
        <tr>
            <td width="10" align="center" bgcolor="#EB8201">&nbsp;</td>
            <td width="50" align="center" bgcolor="#EB8201"><span class="style1">排序</span></td>
            <td align="left" bgcolor="#EB8201"><span class="style1">名稱</span></td>
            <td width="40" align="center" bgcolor="#EB8201"><span class="style1">更新</span></td>
            <td width="40" align="center" bgcolor="#EB8201"><span class="style1">刪除</span></td>
        </tr>
        <?php
        $sql = "SELECT int_id, txt_dept, bl_isvalid, int_sort FROM tbl_dept WHERE bl_isvalid = '1' ORDER BY int_sort;";
        $result = mysqli_query($con, $sql) or die($sql);
        $count = 1;
        WHILE ($record = mysqli_fetch_array($result)) {
            IF ($count & 1) {
                $bg = "#FFFFFF";
            } ELSE {
                $bg = "#F2F2F2";
            }
            ?>
            <tr>
                <td width="10" align="center" bgcolor="<?php echo $bg; ?>"><?php echo $count; ?></td>
                <td width="50" align="center" bgcolor="<?php echo $bg; ?>"><input name="<?php echo $record[int_id]; ?>"
                                                                                  type="text"
                                                                                  id="<?php echo $record[int_id]; ?>"
                                                                                  value="<?php echo $record[int_sort]; ?>"
                                                                                  size="2" maxlength="3"
                                                                                  onKeyDown="return isNumber(event);">
                </td>
                <td align="left" bgcolor="<?php echo $bg; ?>"><?php echo $record[txt_dept]; ?></td>
                <td width="40" align="center" bgcolor="<?php echo $bg; ?>"><a
                            href="CMS_dept2.php?action=edit&id=<?php echo $record[int_id]; ?>">更新</a></td>
                <td width="40" align="center" bgcolor="<?php echo $bg; ?>"><a
                            href="CMS_dept2.php?action=delete&id=<?php echo $record[int_id]; ?>">刪除</a></td>
            </tr>
            <?php
            $count += 1;
        }
        ?>
    </table>
    <p>
        <input name="Update" type="submit" id="Update" value="更新排序">
    </p>
</form>
</body>
</html>