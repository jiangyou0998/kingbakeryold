<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //服务器配置
    $mail->CharSet = "UTF-8";                     //设定邮件编码
    $mail->SMTPDebug = 0;                        // 调试模式输出
    $mail->isSMTP();                             // 使用SMTP

    $mail->Host = 'smtp.office365.com';                // SMTP服务器
    $mail->SMTPAuth = true;                      // 允许 SMTP 认证
    $mail->Username = 'fs378354476@outlook.com';                // SMTP 用户名  即邮箱的用户名
    $mail->Password = 'jian3344';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
    $mail->SMTPSecure = 'STARTTLS';                    // 允许 TLS 或者ssl协议
    $mail->Port = 587;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持
//
//    $mail->setFrom('jianli@kingbakery.com.hk', 'Mailer');  //发件人
//    $mail->addAddress('jianli@kingbakery.com.hk', 'Joe');  // 收件人
//    //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
//    $mail->addReplyTo('jianli@kingbakery.com.hk', 'info'); //回复的时候回复给哪个邮箱 建议和发件人一致
//    //$mail->addCC('cc@example.com');                    //抄送
//    //$mail->addBCC('bcc@example.com');                    //密送

//    $mail->Host = 'corpmail1.netvigator.com';                // SMTP服务器
//    $mail->SMTPAuth = true;                      // 允许 SMTP 认证
//    $mail->Username = 'intranet@kingbakery.com.hk';                // SMTP 用户名  即邮箱的用户名
//    $mail->Password = 'kb27900990';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
//    $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
//    $mail->Port = 25;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持
//
//    $mail->setFrom('intranet@kingbakery.com.hk', 'Mailer');  //发件人
//    $mail->addAddress('jianli@kingbakery.com.hk', 'Joe');  // 收件人
//    //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
//    $mail->addReplyTo('intranet@kingbakery.com.hk', 'info'); //回复的时候回复给哪个邮箱 建议和发件人一致
//    //$mail->addCC('cc@example.com');                    //抄送
//    //$mail->addBCC('bcc@example.com');                    //密送

    //发送附件
    // $mail->addAttachment('../xy.zip');         // 添加附件
    // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名

//    $mail->Host = "corpmail1.netvigator.com";
//    $mail->Port = 25;
//    $mail->IsSMTP();
    //$mail->SMTPAuth = true;
    //$mail->Username = "kbh-podocument";
    //$mail->Password = "#HqURsEC";

    $mail->FromName = "kbh-podocument";
    $mail->From = "fs378354476@outlook.com";
    $mail->SMTPDebug = 1;
    $mail->AddAddress("jianli@kingbakery.com.hk");
    $mail->AddAddress("fs378354476@outlook.com");
    //$mail->AddBCC("yuecheung.lau@gmail.com");
    $mail->CharSet = "utf-8";
    $mail->Encoding = "base64";
    $mail->WordWrap = 50;                                 // set word wrap to 50 characters
    $mail->IsHTML(true);


    //Content
    $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
    $mail->Subject = '这里是邮件标题' . time();
    $mail->Body = '<h1>这里是邮件内容</h1>' . date('Y-m-d H:i:s');
    $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';

    $mail->send();
    echo '邮件发送成功';
} catch (Exception $e) {
    echo '邮件发送失败: ', $mail->ErrorInfo;
}

//require("phpMailer/class.phpmailer.php");
//
//
//
//try {
//    $mail = new PHPMailer(true);
//    $mail->Host = "corpmail1.netvigator.com";
//    $mail->Port = 25;
//    $mail->IsSMTP();
//
//    $mail->FromName = "kbh-intranet";
//    $mail->From = "intranet@kingbakery.com.hk";
//    $mail->SMTPDebug = 1;
////$mail->SMTPAuth = true;
////$mail->Username = "intranet@kingbakery.com.hk";
////$mail->Password = "kb27900990";
//
//    $mail->AddAddress("jianli@kingbakery.com.hk");
//
//    $mail->CharSet="utf-8";
//    $mail->Encoding = "base64";
//    $mail->WordWrap = 50;                                 // set word wrap to 50 characters
//    $mail->IsHTML(true);
//
//    $mail->Subject = "test";
//    $mail->Body    = "test";
//    $mail->AltBody = "test";
//
//    $mail->send();
//    echo '邮件发送成功';
//}catch (Exception $e) {
//    echo '邮件发送失败: ', $mail->ErrorInfo;
//}
//
//function eregi($arg){
//	//echo $arg;
//}
?>