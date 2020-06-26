<?php

session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    header('Location: login.php');
}
require("connect.inc");
$timestamp = gettimeofday("sec");

$id = $_POST['id'];
$menuid = $_POST['menuid'];

//刪除數據()
if ($id) {

    $sql = "UPDATE regular_orders SET disabled = 1 WHERE id = $id AND menu_id = $menuid ;";

    // die($sql);


    mysqli_query($con, $sql) or die("error delete");
}