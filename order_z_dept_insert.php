<?php

session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order_z_dept.php';
    header('Location: login.php');
}
require("connect.inc");
$timestamp = gettimeofday("sec");

$maxQTY = 300;
$action = $_REQUEST[action];
$order_user = $_SESSION[order_user] ? $_SESSION[order_user] : $_SESSION[user_id];

$json = $_POST['insertData'];
$insertDatas = json_decode($json, true);

//有插入數據時才執行
if ($insertDatas) {
    $usersql = "SELECT chr_pocode FROM tbl_user WHERE int_id = '$order_user';";
//  die($sql);
    $u_result = mysqli_query($con, $usersql) or die($usersql);
    $user = mysqli_fetch_assoc($u_result);
//    var_dump($user['chr_pocode']);die;

    $sql = "INSERT INTO tbl_order_z_dept (order_date, int_user, int_product, int_qty, chr_ip, status, chr_phase, chr_dept, chr_po_no, int_qty_init, insert_date) ";
    $sql .= "VALUES ";

    $insertDatasLength = count($insertDatas);
    foreach ($insertDatas as $key => $insertData) {
//    var_dump($insertData);
        if ($key != 0) {
            $sql .= ",";
        }
        $sql .= "('";
        //order_date
        $sql .= date('Y/n/j G:i:s', $timestamp);
        $sql .= "','";
        //int_user
        $sql .= $order_user;
        $sql .= "','";
        //int_product
        $sql .= $insertData['itemid'];
        $sql .= "','";
        //int_qty
        $sql .= $insertData['qty'];
        $sql .= "','";
        //chr_ip
        $sql .= $_SERVER['REMOTE_ADDR'];
        $sql .= "','";
        //status
        $sql .= 1;
        $sql .= "','";
        //chr_phase
        $sql .= $_SESSION['advance'];
        $sql .= "','";
        //chr_dept
        $sql .= $_SESSION['OrderDept'];
        $sql .= "','";
        //chr_po_no
        $sql .= date("ymd", strtotime("+" . ($_SESSION['advance'] + 1) . " day")) . $user['chr_pocode'];
        //int_qty_init, insert_date
        $sql .= "', 1, NOW()) ";

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
