<?php
header("content-Type: text/html; charset=utf-8");

//檢查是否登錄,是否管理員
require("check_login.php");

require($DOCUMENT_ROOT . "connect.inc");

if (!empty($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    if ($action == 'shop') {
        $codes = $_REQUEST['code'];
        //$sql = " SELECT TBL_ORDER_SHOP.*,TBL_USER.txt_name as shop_name FROM TBL_ORDER_SHOP LEFT JOIN TBL_USER ON TBL_USER.chr_ename = TBL_ORDER_SHOP.chr_code ORDER BY TBL_ORDER_SHOP.int_sort ASC  ";
        $sql = " SELECT T0.int_user_id, T0.int_area_id, T1.txt_name, T1.chr_ename
			FROM tbl_order_z_shop T0
				LEFT JOIN tbl_user T1 ON T0.int_user_id = T1.int_id
				LEFT JOIN tbl_order_z_area T2 ON T0.int_area_id = T2.int_id
			WHERE T0.int_area_id = {$codes}
			ORDER BY T1.chr_ename, T0.int_sort ;";

        $result = mysqli_query($con, $sql) or die($sql);
        while ($record = mysqli_fetch_array($result)) {
            //$name = iconv('big5','utf-8',$record['shop_name']);
            $name = $record['txt_name'];
            $code = $record['chr_ename'];
            $int_id = $record['int_user_id'];
            $area_code = $record['int_area_id'];
            $area_array[] = array(
                'name' => $name,
                'code' => $code,
                'area_code' => $area_code,
                'id' => $int_id
            );
        }

        if (!empty($area_array)) {
            echo json_encode(array('retStatus' => '1', 'retData' => $area_array));
        } else {
            echo json_encode(array('retStatus' => '0', 'retData' => ''));
        }
    } else if ($action == 'area') {
        $sql = "SELECT int_id, chr_name FROM tbl_order_z_area ORDER BY int_sort";
        $result = mysqli_query($con, $sql) or die($sql);
        while ($record = mysqli_fetch_array($result)) {
            //$name = iconv('big5','utf-8',$record['chr_name']);
            $name = $record['chr_name'];
            $code = $record['int_id'];

            $area_array[] = array(
                'name' => $name,
                'code' => $code
            );
        }

        if (!empty($area_array)) {
            echo json_encode(array('retStatus' => '1', 'retData' => $area_array));
        } else {
            echo json_encode(array('retStatus' => '0', 'retData' => ''));
        }
    }

}

?>