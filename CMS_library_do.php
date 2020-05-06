<?php

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");
if ($_REQUEST[action] == "AddLibrary") {
    //print_r($_FILES);
    //die();

    $group_id = $_REQUEST["select_group"]; //分類
    $createtime = date("Y-m-d"); //創建時間
    $date = date('Ymdhis');//
    $select_main = substr($_REQUEST["select_main"], 0, strpos($_REQUEST["select_main"], "."));//摸大類
    $select_state = substr($_REQUEST["select_main"], strpos($_REQUEST["select_main"], ".") + 1);//大類狀態

    $select_shop = 0;
    $cent = 0;
    $logs = 0;
    $dept = 0;
    if ($select_state == 1) {
        $select_shop = $_REQUEST[select_shop];    //指定分店可見
    } else {
        if ($_REQUEST[cent]) {
            $cent = 1;
        }    //所有分店可見
        if ($_REQUEST[logs]) {
            $logs = 1;
        }    //所有後勤可見
        if ($_REQUEST[dept]) {
            $dept = 1;
        }
    }


    foreach ($_FILES['txt_url']['type'] as $key => $value) {
        if ($value !== "application/pdf") {
            echo "<script>alert('顯示文檔必須為PDF');document.location.href='CMS_library.php';</script>";
            die;
        }
    }


    $original = $_FILES['txt_f_url'];//原始文?數組
    $num_1 = 0;
    $num_2 = 0;
    $i = 0;
    foreach ($_POST[txt_p_name] as $key => $value) {
        $txt_p_name = $_POST['txt_p_name'][$key];
        $txt_p_name = stripcslashes($txt_p_name);
        $fileName = $_FILES['txt_url']['name'][$key];
        $name = explode('.', $fileName);
        $newPath = "library/" . $date . $i . '.' . $name[count($name) - 1];    //新地址
        $oldPath = $_FILES['txt_url']['tmp_name'][$key];    //舊地址
        //上傳原始文檔
        $ori_file = $_POST['txt_name'][$key];
        $tmp = $original['tmp_name'][$key];
        $ori_name = $original['name'][$key];
        $o_name = explode('.', $ori_name);
        $path = "library/" . $date . "ori" . '.' . $o_name[count($o_name) - 1];

        if (move_uploaded_file($oldPath, $newPath)) {
            $sql = "insert into tbl_lib_pmd 
							(txt_name,txt_path,txt_ori_doc,txt_ori_file,txt_ori_name,txt_ori_url,date_create,date_modify,state_cent,state_logs,state_dept,int_dept,int_shop,author,group_id) 
							values ('$txt_p_name','$newPath','$fileName','$ori_file','$ori_name','$path','$createtime','$createtime',$cent,$logs,$dept,$_SESSION[dept],$select_shop,'$_SESSION[user]',$group_id)";

            $intNum = mysqli_query($con, $sql) or die($sql . "<BR>" . mysqli_error());
            if ($intNum > 0) {
                $num_1++;
            } else {
                $num_2++;
            }
            $i++;
            move_uploaded_file($tmp, $path);
        }
    }

    if ($num_1 > 0 && $num_2 == 0) {
        echo "<script>alert('新增成功');document.location.href='CMS_library.php';</script>";
    } else if ($num_1 > 0 && $num_2 > 0) {
        echo "<script>alert('其中" . $num_2 . "項增加失敗');document.location.href='CMS_library.php'; </script>";
    } else {
        echo "<script>alert('新增失敗');document.location.href='CMS_library.php'; </script>";
    }

}
if ($_REQUEST[action] == "lib_do") {
    $main_id = $_REQUEST['pram'];
    $sql = "select int_id,txt_name from tbl_lib_group where main_id = $main_id";
    $res = mysqli_query($con, $sql);
    if (mysqli_num_rows($res) > 0) {
        echo "<select name=\"select_group\" id=\"select_group\" style='width:150px;'>";
        while ($rec = mysqli_fetch_array($res)) {
            echo "<option value='" . $rec['int_id'] . "'>";
            echo escape($rec['txt_name']);
            echo "</option>";
        }
        echo "</select>";
    } else {
        echo "<font size=\"-1\" color=\"red\">" . escape("沒有相關分類,請先添加分類") . "</font>";
    }
}
if ($_REQUEST[action] == "list_do") {
    $main_id = $_REQUEST['pram'];
    $id = $_REQUEST['id'];
    $sql = "select int_id,txt_name from tbl_lib_group where main_id = $main_id";
    $res = mysqli_query($con, $sql);
    if (mysqli_num_rows($res) > 0) {
        echo "<select name=\"select_group" . $id . "\" id=\"select_group" . $id . "\" style='width:100px;'>";
        while ($rec = mysqli_fetch_array($res)) {
            echo "<option value='" . $rec['int_id'] . "'>";
            echo escape($rec['txt_name']);
            echo "</option>";
        }
        echo "</select>";
    } else {
        echo "<font size=\"-1\" color=\"red\">" . escape("請先添加分類") . "</font>";
    }
}
function escape($str)
{
    return $str;
}

?>
<html>
<head>
    <title>內聯網</title>
    <META name="ROBOTS" content="NOINDEX,NOFOLLOW">
</head>
<body></body>
<html>