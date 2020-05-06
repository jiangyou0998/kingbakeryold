<?php

require($DOCUMENT_ROOT . "connect.inc");


$timestamp = gettimeofday('sec');
$date = date("Y-m-d", $timestamp);
$date2 = date("Ymd", $timestamp);
//echo $weekday;
$reportTime = $_REQUEST['rTime'];
$rTime = str_replace(":", '', $reportTime);
//    echo $rTime;die;
$sql = "SELECT int_report_id, chr_report_name, chr_time 
			FROM tbl_order_z_print_time T0
				LEFT JOIN tbl_order_check T1 ON T0.int_report_id = T1.int_id
			WHERE T1.disabled = 0 AND chr_time = '$reportTime' AND chr_weekday LIKE CONCAT('%', WEEKDAY(CURDATE()), '%');";
//    die($sql);
$result = mysqli_query($con, $sql) or die($sql);


//文件追加内容到zip
$zip = new ZipArchive;
$filepath = 'order/' . $date2 . '_' . $rTime . '.zip';
$res = $zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

while ($record = mysqli_fetch_assoc($result)) {
//	    echo "http://192.168.0.5/CMS_order_c_check_m_print.php?id={$record[int_report_id]}&checkDate={$date}";die;
    $exec = <<<EOT
			wkhtmltopdf\\bin\\wkhtmltopdf.exe --zoom 1.5 -O landscape "http://192.168.0.5/CMS_order_c_check_m_print.php?id={$record[int_report_id]}&checkDate={$date}" "order\\{$record[chr_report_name]}_{$date2}.pdf"
EOT;
    //echo $exec;
    system($exec, $retval);


    if ($res === TRUE) {
        $zip->addFile('order/' . $record[chr_report_name] . '_' . $date2 . '.pdf', $record[chr_report_name] . '_' . $date2 . '.pdf');

    }

}

$zip->close();

echo '<a href="' . $filepath . '" style="font-size: 250%" download>点击下载</a>';



