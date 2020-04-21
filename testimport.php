<?php

var_dump(excelToArray());

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

    $groupName = '';
    for ($row = 2; $row <= $highestRow; $row++) {
        $row_arr = array();
        for ($column = 1; $arr[$column] != 'N'; $column++) {
            $val = $sheet->getCellByColumnAndRow($column, $row)->getValue();

            //G列,J列是空的
            if ($arr[$column] == 'G' || $arr[$column] == 'J') {
                continue;
            }

            if ($arr[$column] == 'B' && $val != '分類' && $val != '') {
                $groupName = $val;
            }

            if ($arr[$column] == 'B') {
                $row_arr[] = $groupName;
                continue;
            }

            $row_arr[] = $val;
        }

        //表頭跟空行不需要
        if ($row_arr[1] != '產品編號' && $row_arr[1] != '') {
            $res_arr[] = $row_arr;
        }


    }

    return $res_arr;
}
