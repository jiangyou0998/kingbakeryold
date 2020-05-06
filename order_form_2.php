<?php
session_start();
if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'forms.php';
    header('Location: login.php');
}
require($DOCUMENT_ROOT . "connect.inc");

$sql = "INSERT INTO tbl_order_form (order_date, int_branch, int_form, int_qty, chr_ip) ";
$sql .= "VALUES ('";
$sql .= date('Y-m-d');
$sql .= "','";
$sql .= $_SESSION[user_id];
$sql .= "','";
$sql .= $_POST[formid];
$sql .= "','";
$sql .= $_POST[orderno];
$sql .= "','";
$sql .= $_SERVER['REMOTE_ADDR'];
$sql .= "') ";

$result = mysqli_query($con, $sql) or die($sql);

echo "<script>alert('申請已遞交!');</script>";
echo "<script>document.location.href='forms.php';</script>";
exit;
?>