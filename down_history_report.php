<?php

require($DOCUMENT_ROOT . "connect.inc");


$timestamp = gettimeofday('sec');
$date = date("Y-m-d", $timestamp);
$date2 = date("Ymd", $timestamp);
//echo $weekday;
$deliTime = $_REQUEST['dTime'];
//    $dTime = str_replace(":",'',$deliTime);
//    echo $deliTime;die;
$sql = "SELECT int_id, chr_report_name, int_num_of_day
			FROM tbl_order_check
			WHERE disabled = 0 ;";
//    die($sql);
$result = mysqli_query($con, $sql) or die($sql);


//文件追加内容到zip
$zip = new ZipArchive;
$filepath = 'order/' . $deliTime . '_history.zip';
$res = $zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

while ($record = mysqli_fetch_assoc($result)) {
    $checkdate = date('Y-m-d', strtotime($deliTime) - $record['int_num_of_day'] * 24 * 60 * 60);
//        echo $checkdate;die;
//	    echo "http://192.168.0.5/CMS_order_c_check_m_print.php?id={$record[int_report_id]}&checkDate={$date}";die;
    $exec = <<<EOT
			wkhtmltopdf\\bin\\wkhtmltopdf.exe --zoom 1.5 -O landscape "http://192.168.0.5/CMS_order_c_check_m_print.php?id={$record['int_id']}&checkDate={$checkdate}" "order\\{$record[chr_report_name]}_{$date2}.pdf"
EOT;
    //echo $exec;
    system($exec, $retval);


    if ($res === TRUE) {
        $zip->addFile('order/' . $record[chr_report_name] . '_' . $date2 . '.pdf', $record[chr_report_name] . '_' . $deliTime . '.pdf');

    }

}

$zip->close();

echo '<a href="' . $filepath . '" style="font-size: 250%" download>点击下载</a>';



