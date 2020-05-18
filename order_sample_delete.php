<?php

session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    header('Location: login.php');
}
require("connect.inc");
$timestamp = gettimeofday("sec");

$order_user = $_SESSION[order_user] ? $_SESSION[order_user] : $_SESSION[user_id];

$id = $_POST['id'];

//刪除數據()
if ($id) {

    $sql = "UPDATE tbl_order_sample SET disabled = 1 WHERE id = $id AND user_id = $order_user ;";

    // die($sql);


    mysqli_query($con, $sql) or die("error delete");
}

