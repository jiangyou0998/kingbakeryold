<?php
	require($DOCUMENT_ROOT . "connect.inc");
	require("phpMailer/class.phpmailer.php");
	
	$timestamp = gettimeofday('sec');
	$date = date("Y-m-d", $timestamp);
	$date2 = date("Ymd", $timestamp);
	//echo $weekday;
	$sql = "SELECT int_report_id, chr_report_name, chr_time 
			FROM db_intranet.tbl_order_z_print_time T0
				LEFT JOIN tbl_order_check T1 ON T0.int_report_id = T1.int_id
			WHERE T1.disabled = 0 AND chr_time = '11:00' AND chr_weekday LIKE CONCAT('%', WEEKDAY(CURDATE()), '%');";
	$result = mysqli_query($con, $sql) or die($sql);
	//die();
	while($record = mysqli_fetch_assoc($result)){
		$exec = <<<EOT
			D:\\intranet\\wwwroot2\\wkhtmltopdf\\bin\\wkhtmltopdf.exe --zoom 1.5 -O landscape "http://192.168.0.5/CMS_order_c_check_m_print.php?id={$record[int_report_id]}&checkDate={$date}" "D:\\intranet\\wwwroot2\\order\\{$record[chr_report_name]}_{$date2}.pdf"
EOT;
		//echo $exec;
		system($exec, $retval);
		
		$mail = new PHPMailer();
		$mail->Host = "corpmail1.netvigator.com";
		$mail->Port = 25;
		$mail->IsSMTP();
		//$mail->SMTPAuth = true;
		//$mail->Username = "kbh-podocument";
		//$mail->Password = "#HqURsEC";

		$mail->FromName = "kbh-podocument";
		$mail->From = "podocument@kingbakery.com.hk";
		$mail->SMTPDebug = 1;
        //		$mail->AddAddress("podocument@kingbakery.com.hk");
        $mail->AddAddress("jianli@kingbakery.com.hk");
		//$mail->AddBCC("yuecheung.lau@gmail.com");
		$mail->AddAttachment("D:\\intranet\\wwwroot2\\order\\{$record[chr_report_name]}_{$date2}.pdf");
		$mail->CharSet="utf-8";
		$mail->Encoding = "base64";
		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		$mail->IsHTML(true);

		$mail->Subject = "$record[chr_report_name]";
		$mail->Body    = "$record[chr_report_name]";
		$mail->AltBody = "$record[chr_report_name]";

		if(!$mail->Send())
		{
		  echo "Mailer Error: " . $mail->ErrorInfo;
		  exit;
		}
		//die();
	}
	/*
	$mail = new PHPMailer();
	$mail->Host = "corpmail1.netvigator.com";
	$mail->Port = 25;
	$mail->IsSMTP();
	//$mail->SMTPAuth = true;
	//$mail->Username = "kbh-podocument";
	//$mail->Password = "#HqURsEC";

	$mail->FromName = "kbh-podocument";
	$mail->From = "podocument@kingbakery.com.hk";
	$mail->SMTPDebug = 1;
	$mail->AddAddress("podocument@kingbakery.com.hk");
	$mail->AddBCC("bb602345@gmail.com");
	$mail->CharSet="big5";
	$mail->Encoding = "base64";
	$mail->WordWrap = 50;                                 // set word wrap to 50 characters
	$mail->IsHTML(true);

	$mail->Subject = "testing";
	$mail->Body    = "testing";
	$mail->AltBody = "testing";

	echo $mail->Send();
	/*
	if(!$mail->Send())
	{
	  echo "Mailer Error: " . $mail->ErrorInfo;
	  exit;
	}
	*/
	
	function eregi($arg){
		//echo $arg;
	}
	
?>