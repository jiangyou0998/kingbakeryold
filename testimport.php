<?php

//excelToArray();
//setDefultVisual();

function excelToArray()
{

    require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel/IOFactory.php';


    //加载excel文件
    $filename = dirname(__FILE__) . '/xls/import.xls';


    $objPHPExcelReader = PHPExcel_IOFactory::load($filename);

    $sheet = $objPHPExcelReader->getSheet(0);        // 读取第一个工作表(编号从 0 开始)
    $highestRow = $sheet->getHighestRow();           // 取得总行数
    $highestColumn = $sheet->getHighestColumn();     // 取得总列数

    $arr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    // 一次读取一列
    $res_arr = array();

    $cate_arr = array(
        '3' => '包部',
        '2' => '餅部',
        '1' => '廚部',
        '5' => '貨倉'
    );

    $group_arr = array();

//    var_dump($cateArray);die;

    $cateName = '';
    $groupName = '';
    $cateID = '';
    for ($row = 2; $row <= $highestRow; $row++) {
        $row_arr = array();
        for ($column = 0; $arr[$column] != 'N'; $column++) {
            $val = $sheet->getCellByColumnAndRow($column, $row)->getValue();

            //G列,J列是空的
            if ($arr[$column] == 'G' || $arr[$column] == 'J') {
                continue;
            }

            if ($arr[$column] == 'A' && $val == '部門') {
                break;
            }

            if ($arr[$column] == 'A' && $val != '部門' && $val != '') {
                $groupName = $val;
                $cateID = array_search($groupName, $cate_arr);
            }

            if ($arr[$column] == 'A') {
                $row_arr[] = $cateID;
                continue;
            }

            if ($arr[$column] == 'B' && $val != '分類' && $val != '') {
                $groupName = $val;
                $group_arr[$cateID][] = $groupName;
            }

            if ($arr[$column] == 'B') {
                $row_arr[] = $groupName;
                continue;
            }

            if ($arr[$column] == 'H') {
//                $val = substr($val,0,2);
                if ($val == "後勤落單") {
                    $val = -1;
                } else {
                    $val = preg_replace('/[^0-9]/', '', $val);
                }

            }

            if ($arr[$column] == 'K' || $arr[$column] == 'L') {
                if ($val == "") {
                    $val = 1;
                }
            }

            //時間導入為小數 例如12:00 導入值為0.5 ,10:00導入值為0.41666666666667
//            时 = 小数 *24 取整（不要四舍五入）
//            分 = （小数 * 24 * 60 ）% 60
            if ($arr[$column] == 'I') {
//                $val = substr($val,0,2);
                if ($val) {
                    $hour = floor($val * 24);
                    $min = ($val * 24 * 60) % 60;
                    $val = str_pad($hour, 2, "0", STR_PAD_LEFT) . str_pad($min, 2, "0", STR_PAD_LEFT);
                } else {
                    $val = "0000";
                }
            }

            $row_arr[] = $val;
        }

        //表頭跟空行不需要
        if ($row_arr[1] != '產品編號' && $row_arr[1] != '') {
            $res_arr[] = $row_arr;
        }


    }

//    return $res_arr;
//    return $group_arr;

    //插入group數據
    $sql = "INSERT INTO tbl_order_z_group (chr_name,int_sort,status,int_cat,chr_name_long) VALUES ";

    $count = 1;
    foreach ($group_arr as $cID => $groups) {
        foreach ($groups as $key => $group) {
            $sql .= "(\"$group\",$count,1,$cID,\"$group\"),";
            $count++;
        }
    }

    $sql = substr($sql, 0, -1);
    $sql .= ";";

    $con = mysqli_connect("localhost", "root", "Behv6953gl", "db_intranet");
    $result = mysqli_query($con, $sql) or die($sql);

    //查詢group數據
    $sql = "SELECT int_id,chr_name FROM tbl_order_z_group";

    $result = mysqli_query($con, $sql) or die($sql);

    $groups = array();
    WHILE ($record = mysqli_fetch_array($result)) {
        $groups[$record['int_id']] = $record['chr_name'];
    }

    //查詢unit數據
    $sql = "SELECT int_id,chr_name FROM tbl_order_z_unit";

    $result = mysqli_query($con, $sql) or die($sql);

    $units = array();
    WHILE ($record = mysqli_fetch_array($result)) {
        $units[$record['int_id']] = $record['chr_name'];
    }

//    var_dump($units);
//    var_dump($res_arr);

    //插入menu數據
    $sql = "INSERT INTO tbl_order_z_menu (int_group,chr_no,chr_name,int_unit,int_default_price,int_phase,chr_cuttime,int_min,int_base,chr_canordertime,status,int_sort) VALUES ";

    $count = 1;
    foreach ($res_arr as $key => $value) {
        //A 0 部門
        //B 1 分類 int_group
        //C 2 產品編號 chr_no
        //D 3 產品名稱 chr_name
        //E 4 單位 int_unit
        //F 5 售價1 int_default_price
        //H 6 截單期 int_phase
        //I 7 截單時間 chr_cuttime
        //K 8 MOQ int_min
        //L 9 出貨單位 int_base
        //M 10 出貨期 chr_canordertime
        $g = array_search($value[1], $groups);
        $UoM = array_search($value[4], $units);

        if ($value[3] != "") {
            $sql .= "(\"$g\",\"$value[2]\",\"$value[3]\",\"$UoM\",\"$value[5]\",\"$value[6]\",\"$value[7]\",\"$value[8]\",\"$value[9]\",\"$value[10]\",1,$count),";
            $count++;
        }

    }

    $sql = substr($sql, 0, -1);
    $sql .= ";";

    mysqli_query($con, $sql) or die($sql);


}

//將所有item設置成全部可視
function setDefultVisual()
{
    $con = mysqli_connect("localhost", "root", "Behv6953gl", "db_intranet");

    //獲取所有分店的id
    $sql = "SELECT int_id FROM tbl_user where int_dept = 2;";
    $result = mysqli_query($con, $sql) or die($sql);

    $branchIDs = array();
    while ($record = mysqli_fetch_array($result)) {
        $branchIDs[] = $record['int_id'];
    }

//    var_dump($branchIDs);

    //獲取所有group的id
    $sql = "SELECT int_id FROM tbl_order_z_group;";
    $result = mysqli_query($con, $sql) or die($sql);

    $groupIDs = array();
    while ($record = mysqli_fetch_array($result)) {
        $groupIDs[] = $record['int_id'];
    }

    //獲取所有menu的id
    $sql = "SELECT int_id FROM tbl_order_z_menu ;";
    $result = mysqli_query($con, $sql) or die($sql);

    $menuIDs = array();
    while ($record = mysqli_fetch_array($result)) {
        $menuIDs[] = $record['int_id'];
    }

//    var_dump($menuIDs);

    $sql = "INSERT INTO tbl_order_z_menu_v_shop (int_user_id,int_menu_id) VALUES ";
    foreach ($branchIDs as $branchID) {
        foreach ($menuIDs as $menuID) {
            $sql .= "(\"$branchID\",\"$menuID\"),";
        }
    }

    $sql = substr($sql, 0, -1);
    $sql .= ";";

//    var_dump($sql);
    mysqli_query($con, $sql) or die($sql);

    $sql = "INSERT INTO tbl_order_z_group_v_shop (int_user_id,int_group_id) VALUES ";
    foreach ($branchIDs as $branchID) {
        foreach ($groupIDs as $groupID) {
            $sql .= "(\"$branchID\",\"$groupID\"),";
        }
    }

    $sql = substr($sql, 0, -1);
    $sql .= ";";

//    var_dump($sql);
    mysqli_query($con, $sql) or die($sql);

}
