<?php

session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order_z_dept.php';
    header('Location: login.php');
}
require("connect.inc");
$timestamp = gettimeofday("sec");

$method=$_POST['method'];
$id=$_POST['id'];

switch ($method){
    case "delete":
        $sql = "UPDATE tbl_order_check SET disabled = 1 WHERE int_id = ".$id;
        mysqli_query($con, $sql) or die("error delete");
        break;
}