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
$sampleid = $_POST['sampleid'];
$sampledate = $_POST['sampledate'];

$json = $_POST['insertData'];
$insertDatas = json_decode($json, true);

// var_dump($_POST['insertData']);die();
if($sampleid && $sampledate){

    $sql = "UPDATE tbl_order_sample SET sampledate = '$sampledate' WHERE id = $sampleid";

    // die($sql);

    mysqli_query($con, $sql) or die("error update sampledate");
}

//有插入數據時才執行 
if ($insertDatas) {

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
    mysqli_query($con, $sql) or die("error insert");
}

//更新數據
$json = $_POST['updateData'];
$updateDatas = json_decode($json, true);
if ($updateDatas) {
    $sql = "";

    $idsArray = array();
    $sql = "UPDATE tbl_order_sample_item SET qty = CASE id ";
    foreach ($updateDatas as $updateData) {
        array_push($idsArray, $updateData['mysqlid']);
        $sql .= sprintf("WHEN %d THEN %d ", $updateData['mysqlid'], $updateData['qty']);
    }
    $ids = implode(',', array_values($idsArray));
    $sql .= "END WHERE id IN ($ids)";

    //var_dump($ids);
    // die($sql);

    mysqli_query($con, $sql) or die("error update");
}

$json = $_POST['delData'];
$delDatas = json_decode($json, true);

//刪除數據()
if ($delDatas) {
    $idsArray = array();

    foreach ($delDatas as $delData) {
        array_push($idsArray, $delData['mysqlid']);
//      var_dump($delData['mysqlid']);
    }
    $ids = implode(',', array_values($idsArray));

    $sql = "UPDATE tbl_order_sample_item SET disabled = 1 WHERE id IN ($ids)";

    // die($sql);

    mysqli_query($con, $sql) or die("error delete");
}

//var_dump($sql);
