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
