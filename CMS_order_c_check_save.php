<?php

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");

$info = $_POST["report_info"];
//	var_dump($info);
//die();
$data = json_decode($info, true);
//	var_dump($data);
$item = join(', ', $data[item]);


if ($_POST[report_id] != "") {
    $report_id = $_POST[report_id];
} else {
    $report_id = 'NULL';
}


if ($_POST["action"] == "update") {
    $sql = "DELETE FROM tbl_order_check WHERE int_id = $_POST[report_id]";
    mysqli_query($con, $sql) or die($sql);
}
$sql = "INSERT INTO tbl_order_check VALUES ";
if ($data[all_shop] == 1) {
    $all_shop = 1;
    $sql .= "($report_id, '1', NULL, '$item', '$data[name]', '$data[num_of_day]', '$data[hide]', '$data[mainItem]', '$data[sort]' , 0)";

} else {
    $all_shop = 0;

    if ($all_th + $all_tw + $all_ctc + $all_other != 4) {
        if (count($data[shop]) != 0) {
            $shop = join(', ', $data[shop]);
            $sql .= "($report_id, '$all_shop ', '$shop', '$item', '$data[name]', '$data[num_of_day]', '$data[hide]', '$data[mainItem]', '$data[sort]', '$_POST[type]')";
        } else {
            $sql .= "($report_id, '$all_shop ',  NULL, '$item', '$data[name]', '$data[num_of_day]', '$data[separate]', '$data[hide]', '$data[mainItem]', '$data[sort]', '$_POST[type]')";
        }
    } else {
        $sql .= "($report_id, '$all_shop ', NULL, '$item', '$data[name]', '$data[num_of_day]', '$data[hide]', '$data[mainItem]', '$data[sort]', '$_POST[type]')";
    }
}
//die($sql);
mysqli_query($con, $sql) or die($sql);

$sql = "SELECT * FROM tbl_order_z_print_time WHERE int_report_id = $report_id";
$result = mysqli_query($con, $sql) or die($sql);
if (mysqli_fetch_assoc($result)) {
    $sql = "UPDATE tbl_order_z_print_time SET ";
    $sql .= "chr_time = '$data[print_time]', ";
    $sql .= "chr_weekday = '$data[print_weekday]' ";
    $sql .= "WHERE int_report_id = '$report_id' ";
} else {
    $sql = "INSERT INTO tbl_order_z_print_time(int_report_id, chr_time, chr_weekday) VALUE(LAST_INSERT_ID(), '$data[print_time]', '$data[print_weekday]');";
}
//die($sql);
mysqli_query($con, $sql) or die($sql);
header('Location: CMS_order_c_check_list.php');


?>