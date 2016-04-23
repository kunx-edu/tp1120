<?php

/*
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

/**
 * 将错误信息转换成一个有序列表字符串.
 * @param array|string $errors 错误信息.
 * @return string
 */
function get_error($errors) {
    if (!is_array($errors)) {
        $errors = array($errors);
    }
    $html = '<ol>';
    foreach ($errors as $error) {
        $html .= '<li>' . $error . '</li>';
    }
    $html .= '<ol>';
    return $html;
}

/**
 * 
 * @param type $telphone
 */
function sendSMS($telphone, $params, $sign_name = '四哥测试', $template_code = 'SMS_5590023') {
    $config       = C('ALIDAYU_SETTING');
    vendor('Alidayu.Autoloader');
    $c            = new \TopClient;
    $c->format    = 'json';
    $c->appkey    = $config['ak'];
    $c->secretKey = $config['sk'];
    $req          = new \AlibabaAliqinFcSmsNumSendRequest;
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($sign_name);

    $req->setSmsParam(json_encode($params));
    $req->setRecNum($telphone);
    $req->setSmsTemplateCode($template_code);
    $resp = $c->execute($req);
    if (isset($resp->result->success) && $resp->result->success) {
        return true;
    } else {
        return false;
    }
//    if($resp)
}

/**
 * 加盐加密.
 * @param string $password 原密码.
 * @param string $salt     盐.
 * @return string
 */
function salt_password($password, $salt) {
    return md5(md5($password) . $salt);
}

/**
 * 发送邮件。
 * @param string $address 收件人地址.
 * @param string $subject 邮件主题.
 * @param string $content 邮件正文.
 * @param array $attachment 邮件附件.如果有就添加,为空就不添加.
 * @return bool
 */
function sendEmail($address, $subject, $content, array $attachment = []) {
    $config           = C('EMAIL_SETTING');
    vendor('PHPMailer.PHPMailerAutoload');
    $mail             = new \PHPMailer;
    $mail->isSMTP(); //使用smtp发送邮件
    $mail->Host       = $config['host']; //配置发送服务器,如果是多个,使用英文逗号分隔
    $mail->SMTPAuth   = true; //需要认证信息
    $mail->Username   = $config['username']; //用户名
    $mail->Password   = $config['password']; //密码
    $mail->SMTPSecure = $config['smtpsecure']; //传输协议
    $mail->Port       = $config['port']; //端口
    $mail->setFrom($config['username']); // 发件人
    $mail->addAddress($address);     // 收件人

    if ($attachment) {
        foreach ($attachment as $item) {
            $mail->addAttachment($item);         // 添加附件
        }
    }
    $mail->isHTML(true); //HTML格式邮件
    $mail->Subject = $subject; //标题
    $mail->Body    = $content; //正文
    $mail->CharSet = 'utf-8'; //编码
    return $mail->send();
}

/**
 * 获取redis对象.
 * @staticvar type $instance
 * @return \Redis
 */
function get_redis(){
    static $instance = null;
    if(empty($instance)){
        $instance = new Redis();
        $instance->connect(C('REDIS_HOST'),C('REDIS_PORT'));
    }
    return $instance;
}

/**
 * 金额格式化
 * @param number $number        原始数字.
 * @param integer $decimals     小数点后的位数.
 * @param string $dec_point     小数点使用的字符.
 * @param string $thousands_sep 千位分隔符.
 * @return string
 */
function money_format($number,$decimals=2,$dec_point ='.',$thousands_sep=''){
    return number_format($number,$decimals,$dec_point,$thousands_sep);
}