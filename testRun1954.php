<?php
require("phpMailer/class.phpmailer.php");
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
$mail->CharSet = "big5";
$mail->Encoding = "base64";
$mail->WordWrap = 50;                                 // set word wrap to 50 characters
$mail->IsHTML(true);

$mail->Subject = "testing";
$mail->Body = "testing";
$mail->AltBody = "testing";

echo $mail->Send();
/*
if(!$mail->Send())
 {
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
 }
 */

function eregi($arg)
{
    //echo $arg;
}

?>