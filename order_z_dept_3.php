<?php
  session_start();
  if (!($_SESSION[authenticated])) {
    $_SESSION['status'] = 'neverLogin';
    $_SESSION[UrlRedirect] = 'order_z_dept.php';
    header('Location: login.php');
  }
  echo "WAIT...";
  require($DOCUMENT_ROOT . "connect.inc");

  $week = Array('日', '一', '二', '三', '四', '五', '六');
  $timestamp = gettimeofday("sec");
  

  $sql = "SELECT chr_email, txt_name, chr_ename FROM tbl_user ";
  $sql .= "WHERE tbl_user.int_id = $_SESSION[user_id]";

  $result = mysqli_query($con,$sql) or die($sql);
  $record = mysqli_fetch_array($result);
  $email_address = $record[0];

  $branchName = $record[2]." ".$record[1];
  unset($record);

  $sql = "
	SELECT 
		tbl_order_z_dept.int_id AS orderID,
		tbl_order_z_menu.chr_name AS itemName,
		tbl_order_z_menu.chr_no,
		tbl_order_z_unit.chr_name AS UoM,
		tbl_order_z_dept.int_qty,
		tbl_order_z_dept.status,
		LEFT(tbl_order_z_cat.chr_name, 2) AS suppName,
		tbl_order_z_menu.chr_cuttime,
		tbl_order_z_menu.int_phase
	FROM
		tbl_order_z_dept
			INNER JOIN tbl_order_z_menu ON tbl_order_z_dept.int_product = tbl_order_z_menu.int_id
			INNER JOIN tbl_order_z_unit ON tbl_order_z_menu.int_unit = tbl_order_z_unit.int_id
			INNER JOIN tbl_order_z_group ON tbl_order_z_menu.int_group = tbl_order_z_group.int_id
			INNER JOIN tbl_order_z_cat ON tbl_order_z_group.int_cat = tbl_order_z_cat.int_id
	WHERE
		tbl_order_z_dept.int_user = '$_SESSION[user_id]'
			AND tbl_order_z_dept.status IN (0 , 1)
			AND tbl_order_z_dept.int_qty > 0
			AND tbl_order_z_dept.chr_phase = '$_SESSION[advance]'
			AND tbl_order_z_dept.chr_dept = '$_SESSION[OrderDept]'
			AND tbl_order_z_menu.int_id
	ORDER BY tbl_order_z_dept.order_date DESC , 
			 tbl_order_z_group.int_cat , 
			 tbl_order_z_group.int_sort;";


  $result = mysqli_query($con,$sql) or die($sql);
  print($_SESSION[advance]);
  print("<BR>");
  print($_GET[advance]);
  
  $sql = "UPDATE tbl_order_z_dept SET status = 1, order_date = '".date('Y/n/j G:i:s',$timestamp)."' WHERE int_user = $_SESSION[user_id] AND status = 0 AND chr_dept = '$_SESSION[OrderDept]' AND chr_phase=$_GET[advance] ";
  mysqli_query($con, $sql) or die($sql);
  if (mysqli_num_rows($result) != 1000) {
	
	  
    $page = NONE;
    $count = 1;
    $fill = false;
    $w=array(10,30,35,105,15);
    //Column titles
    $header=array('','類別','供應商','貨品','數量');

	/*
    $pdf=new PDF_chinese();
    $pdf->AddBig5Font();
    $pdf->SetAutoPageBreak(1,1);
	*/
	
    WHILE($record = mysqli_fetch_array($result)) {
	  /*
      IF ($record[6] <> $page) {
		$index = date('w', $timestamp + (($record['chr_phase']+1) * 86400) );
		$weekday = $week[$index];
		$deliDate = date("n月j日",$timestamp + (($record[8]+1) * 86400));
		$deliDate .= "( ".$weekday." )";
		 
        $count = 1;
        $pdf->AddPage();
        $pdf->SetFont('Big5','',26);
        $pdf->MultiCell(0,10,$branchName." - ".date('Y/n/j G:i:s',$timestamp),0,'C');
        $pdf->SetFont('Big5','',18);
        $pdf->Cell(97,8,"預落".$record[8]."天　送貨日期 - " . $deliDate,'0',0,'L',$fill);
        $pdf->Cell(97,8,$_REQUEST['dept'],'0',0,'R',$fill);
		$pdf->Ln();

        //Colors, line width and bold font
        $pdf->SetFillColor(123,123,123);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Big5','B',14);

        //Header
        for($i=0;$i<count($header);$i++)
          $pdf->Cell($w[$i],8,$header[$i],1,0,'C',true);
        $pdf->Ln();

      }
      //Color and font restoration
      $pdf->SetFillColor(235,235,235);
      $pdf->SetTextColor(0);
      $pdf->SetFont('','B');

      //Data loading
      $pdf->Cell($w[0],8,$count,'1',0,'C',$fill);
	  $pdf->Cell($w[1],8,$record[5],'1',0,'C',$fill);
      $pdf->Cell($w[2],8,$record[7],'1',0,'C',$fill);
      $pdf->Cell($w[3],8,$record[1]." - ".$record[3],'1',0,'L',$fill);
      $pdf->Cell($w[4],8,$record[4],'1',0,'C',$fill);
      
      $pdf->Ln();
//      $pdf->Cell(array_sum($w),2,'','');
      $pdf->SetFillColor(153,153,153);
      $pdf->Cell(array_sum($w),3,'','1',0,'C',true);
      $pdf->Ln();

      $count += 1;
//      $fill = !$fill;
      $page = $record[6];
	  */
    }
    //$filename = "order_z_dept/".date('Ynj',$timestamp)."_".$branchName."_".$_SESSION[advance]."_".$_SESSION[OrderDept].".pdf";
    //$pdf->Output($filename,'F');
	
	/*
    $mail = new PHPMailer();
    $mail->IsSMTP();                                      // set mailer to use SMTP
    
	include("mail_host.php");
	$mail->Host = $new_mail_host;
	
    $mail->Port = 25;
    $mail->From = "$email_address";
    $mail->FromName = "$branchName";
    $mail->AddReplyTo("$email_address", "$branchName");
    $mail->AddAddress("$email_address", "$branchName");
	$mail->AddBCC('yuecheung.lau@taihingroast.com');
//    $mail->AddAddress("bon.kwok@taihingroast.com","郭百禮");
//    $mail->AddAddress("ryan.chow@taihingroast.com","周浩賢");
//    $mail->AddAddress("taihingstock@taihingroast.com","中央貨倉");
    $mail->CharSet="big5";
    $mail->Encoding = "base64";
    $mail->WordWrap = 50;                                 // set word wrap to 50 characters
    $mail->IsHTML(true);                                  // set email format to HTML
    $mail->AddAttachment($filename);
    $mail->Subject = "分店落貨(供應商) - ".$branchName." - ".$_REQUEST['dept']." - 預落".$_SESSION['advance']."天";
    $mail->Body    = "分店落貨(供應商) - ".$branchName." - ".$_REQUEST['dept']." - 預落".$_SESSION['advance']."天";
    $mail->AltBody = "分店落貨(供應商) - ".$branchName." - ".$_REQUEST['dept']." - 預落".$_SESSION['advance']."天";

    if(!$mail->Send())
    {
      echo "落貨失敗，就重試！<p>";
      echo "Mailer Error: " . $mail->ErrorInfo;
      exit;
    }
*/	
	echo "<br><br><font size='+6'>已成功落貨</font>";
	echo "<script>alert('已落貨!\\n\\n您將會收到電郵確認');</script>";
    echo "<script>document.location.href='order_z_dept.php?advance=$_SESSION[advance]&dept=$_SESSION[OrderDept]';</script>";
//  } ELSE {
//    echo "柯打沒有內容 !!";
  }
  exit;
?>