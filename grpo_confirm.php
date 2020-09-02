<?php

require("connect.inc");

//更新數據
$sql = "";
$json = $_POST['updateData'];

$updateDatas = json_decode($json, true);
if($updateDatas){
	$idsArray = array();
	$sql = "UPDATE tbl_order_z_dept SET int_qty_received = CASE int_id ";
	foreach ($updateDatas as $updateData) {
	    array_push($idsArray, $updateData['mysqlid']);
	    $sql .= sprintf("WHEN %d THEN %d ", $updateData['mysqlid'], $updateData['receivedqty']);
	}
	$ids = implode(',', array_values($idsArray));
	$sql .= "END,";

	$sqlWhen = '';
	foreach ($updateDatas as $updateData) {
	    if ($updateData['reason'] != "") {
	        $sqlWhen .= sprintf("WHEN %d THEN '%s' ", $updateData['mysqlid'], $updateData['reason']);
	    }
	}
	if ($sqlWhen != '') {
	    $sql .= " reason = CASE int_id ";
	    $sql .= $sqlWhen;
	    $sql .= "END,";
	}
	$sql .= sprintf("received_date = '%s' ", date('Y-m-d H:i:s'));
	// $sql .= sprintf("status = %d ", 99);

	$sql .= " WHERE int_id IN ($ids);";

	//var_dump($ids);
	//    var_dump($sql);die;

	mysqli_query($con, $sql) or die("error update:" . $sql);
}

