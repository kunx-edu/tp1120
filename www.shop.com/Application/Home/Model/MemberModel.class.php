<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Model;

/**
 * Description of MemberModel
 *
 * @author qingf
 */
class MemberModel extends \Think\Model{
    /**
     * TODO:验证
     * 用户名 必填 2-16位 唯一
     * 密码 必填 6-16位
     * email 必填 email 唯一
     * 手机号码 必填 正则手机号码 唯一
     * 
     * @var type 
     */
    protected $_validate = [
//        ['username',''],
        ['captcha','checkPhoneCode','手机验证码不正确',self::EXISTS_VALIDATE,'callback',self::MODEL_INSERT],
    ];
    
    /**
     * TODO:自动完成
     * 盐
     * 注册时间
     */
    
    /**
     * 验证手机验证码
     * @param string $code 用户输入的手机验证码
     * @return boolean
     */
    protected function checkPhoneCode($code){
        //获取session中的验证码
        $session_code = session('TEL_CAPTCHA');
        session('TEL_CAPTCHA',null);
        if($code == $session_code){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * TODO:加盐加密
     */
    public function addMember() {
        
    }
}
