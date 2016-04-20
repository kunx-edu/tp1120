<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of TestController
 *
 * @author qingf
 */
class TestController extends \Think\Controller {

    public function sendSMS() {
        $flag = sendSMS('17002810533', ['code' => (string) 1033, 'product' => '仙人跳']);
    }

    public function sendEmail() {
        $address = 'kunx_edu@126.com';

//        require 'PHPMailerAutoload.php';
        vendor('PHPMailer.PHPMailerAutoload');

        $mail = new \PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host       = 'smtp.163.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                               // Enable SMTP authentication
        $mail->Username   = '13036395508@163.com';                 // SMTP username
        $mail->Password   = 'yuyuangang28';                           // SMTP password
//        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
//        $mail->Port       = 25;                                    // TCP port to connect to

        $mail->setFrom('13036395508@163.com', 'yuyuangang');
        $mail->addAddress('13036395508@163.com');     // Add a recipient

//        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'welcome join us';
        $mail->Body    = '<span style="color:red">仙人跳</span>北京仙人跳,人民真需要!';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->CharSet = 'utf-8';

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }

}
