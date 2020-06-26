<?php

session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    header('Location: login.php');
}
require("connect.inc");
$timestamp = gettimeofday("sec");

$maxQTY = 600;
$action = $_REQUEST[action];

$json = $_POST['insertData'];
$insertDatas = json_decode($json, true);

$sql = "INSERT INTO regular_orders (menu_id, orderdates, disabled) ";
$sql .= "VALUES (".$_POST['menuid'].",'".$_POST['orderdates']."',0)";
// var_dump($sql);die;
 mysqli_query($con, $sql) or die($sql);

//有插入數據時才執行 
if ($insertDatas) {
    //獲取新插入id
    $sql_getid = "SELECT LAST_INSERT_ID();";
    $resultGetid = mysqli_query($con, $sql_getid);
    $resultArr = mysqli_fetch_array($resultGetid);
    $rOrderId = $resultArr[0];

    $sql = "INSERT INTO regular_order_items (r_order_id , user_id, qty, disabled) ";
    $sql .= "VALUES";

    $insertDatasLength = count($insertDatas);
    foreach ($insertDatas as $key => $insertData) {
//    var_dump($insertData);
        if ($key != 0) {
            $sql .= ",";
        }
        $sql .= "('";
        //r_order_id
        $sql .= $rOrderId;
        $sql .= "','";

        //user_id
        $sql .= $insertData['userid'];
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