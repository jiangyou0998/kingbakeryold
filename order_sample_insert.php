<?php

session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    header('Location: login.php');
}
require("connect.inc");
$timestamp = gettimeofday("sec");

$maxQTY = 300;
$action = $_REQUEST[action];
$order_user = $_SESSION[order_user] ? $_SESSION[order_user] : $_SESSION[user_id];

$json = $_POST['insertData'];
$insertDatas = json_decode($json, true);

$sql = "INSERT INTO tbl_order_sample (user_id, sampledate, disabled) ";
$sql .= "VALUES (".$order_user.",'".$_POST['sampledate']."',0)";
// var_dump($insertData);die;
 mysqli_query($con, $sql) or die($sql);

//有插入數據時才執行 
if ($insertDatas) {
    //獲取新插入id
    $sql_getid = "SELECT LAST_INSERT_ID();";
    $resultGetid = mysqli_query($con, $sql_getid);
    $resultArr = mysqli_fetch_array($resultGetid);
    $sampleid = $resultArr[0];

    $sql = "INSERT INTO tbl_order_sample_item (sample_id, menu_id, qty, disabled) ";
    $sql .= "VALUES";

    $insertDatasLength = count($insertDatas);
    foreach ($insertDatas as $key => $insertData) {
//    var_dump($insertData);
        if ($key != 0) {
            $sql .= ",";
        }
        $sql .= "('";
        //sample_id
        $sql .= $sampleid;
        $sql .= "','";
        //menu_id
        $sql .= $insertData['itemid'];
        $sql .= "','";
        //qty
        $sql .= $insertData['qty'];
        $sql .= "','";
        //disabled
        $sql .= 0;
        $sql .= "')";

        if ($key == ($insertDatasLength - 1)) {
            $sql .= ";";
        }
    }
// die($sql);
    mysqli_query($con, $sql) or die($sql);
}

//更新數據
$json = $_POST['updateData'];
$updateDatas = json_decode($json, true);
if ($updateDatas) {
    $sql = "";

    $idsArray = array();
    $sql = "UPDATE tbl_order_z_dept SET int_qty = CASE int_id ";
    foreach ($updateDatas as $updateData) {
        array_push($idsArray, $updateData['mysqlid']);
        $sql .= sprintf("WHEN %d THEN %d ", $updateData['mysqlid'], $updateData['qty']);
    }
    $ids = implode(',', array_values($idsArray));
    $sql .= "END WHERE int_id IN ($ids)";

    //var_dump($ids);
    //var_dump($sql);

    mysqli_query($con, $sql) or die("error update");
}

$json = $_POST['delData'];
$delDatas = json_decode($json, true);

//刪除數據(status變為4)
if ($delDatas) {
    $sql = "";

//var_dump($delDatas);
//有刪除數據時才執行
    if ($delDatas) {
        $idsArray = array();

        foreach ($delDatas as $delData) {
            array_push($idsArray, $delData['mysqlid']);
//        var_dump($delData['mysqlid']);
        }
        $ids = implode(',', array_values($idsArray));

        $sql = "UPDATE tbl_order_z_dept SET status = 4, order_date = NOW() WHERE int_id IN ($ids)";

        mysqli_query($con, $sql) or die("error delete");
    }
}

//var_dump($sql);
