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
function sendSMS($telphone,$params,$sign_name='四哥测试',$template_code = 'SMS_5590023') {
    $config = C('ALIDAYU_SETTING');
    vendor('Alidayu.Autoloader');
    $c            = new \TopClient;
    $c->format = 'json';
    $c->appkey    = $config['ak'];
    $c->secretKey = $config['sk'];
    $req          = new \AlibabaAliqinFcSmsNumSendRequest;
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($sign_name);
    
//    $data         = [
//        'code'    => (string) mt_rand(1000, 9999),
//        'product' => '仙人跳文化',
//    ];
    
    $req->setSmsParam(json_encode($params));
    $req->setRecNum($telphone);
    $req->setSmsTemplateCode($template_code);
    $resp         = $c->execute($req);
    if(isset($resp->result->success) && $resp->result->success){
        return true;
    }else{
        return false;
    }
//    if($resp)
}
