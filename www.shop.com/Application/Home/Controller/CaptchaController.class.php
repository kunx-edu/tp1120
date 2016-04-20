<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of CaptchaController
 *
 * @author qingf
 */
class CaptchaController extends \Think\Controller{
    public function captcha(){
        $options = C('CAPTCHA_SETTING');
        $verify = new \Think\Verify($options);
        $verify->entry();
    }
}
