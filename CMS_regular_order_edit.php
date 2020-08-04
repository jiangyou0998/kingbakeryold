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
$rOrderId = $_POST['orderid'];
$insertDatas = json_decode($json, true);

$sql = "DELETE FROM regular_order_items WHERE r_order_id = $rOrderId";

// var_dump($sql);die;
 mysqli_query($con, $sql) or die($sql);

//有插入數據時才執行 
if ($insertDatas) {
    //獲取新插入id

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