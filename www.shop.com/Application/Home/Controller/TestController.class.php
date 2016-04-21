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
        $flag = sendEmail('kunx@kunx.org', '测试邮件', '今天早上没吃药,感觉自己萌萌哒');
        dump($flag);
    }

}
