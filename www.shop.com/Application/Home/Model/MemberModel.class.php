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
     * 手机验证码 自定义验证
     * 验证码 自定义验证
     * @var array 
     */
    protected $_validate = [
        ['username','require','用户名必填',self::EXISTS_VALIDATE,'',self::MODEL_INSERT],
        ['username','2,16','用户名长度应该在2-16位',self::EXISTS_VALIDATE,'length',self::MODEL_INSERT],
        ['username','','用户名已存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT],
        ['password','require','密码必填',self::EXISTS_VALIDATE,'',self::MODEL_INSERT],
        ['password','6,16','密码长度应该在6-16位',self::EXISTS_VALIDATE,'length',self::MODEL_INSERT],
        ['repassword','password','两次密码不一致',self::EXISTS_VALIDATE,'confirm',self::MODEL_INSERT],
        ['email','require','邮箱必填',self::EXISTS_VALIDATE,'',self::MODEL_INSERT],
        ['email','email','邮箱不合法',self::EXISTS_VALIDATE,'',self::MODEL_INSERT],
        ['tel','require','手机号码必填',self::EXISTS_VALIDATE,'',self::MODEL_INSERT],
        ['tel','','手机号码已存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT],
        ['tel','/^(13|14|15|17|18)\d{9}$/','手机号码不合法',self::EXISTS_VALIDATE,'regex',self::MODEL_INSERT],
        ['captcha','checkPhoneCode','手机验证码不正确',self::EXISTS_VALIDATE,'callback',self::MODEL_INSERT],
        ['checkcode','checkCode','验证码不正确',self::EXISTS_VALIDATE,'callback',self::MODEL_INSERT],
    ];
    
    /**
     * 验证验证码.
     * @param string $code 验证码.
     * @return bool
     */
    protected function checkCode($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }
    
    /**
     * 自动完成,随机盐,注册时间.
     */
    protected $_auto = [
        ['salt','Org\Util\String::randString',self::MODEL_INSERT,'function'],
        ['add_time',NOW_TIME,self::MODEL_INSERT],
    ];

    /**
     * 验证手机验证码.
     * @param string $code 用户输入的手机验证码.
     * @return boolean
     */
    protected function checkPhoneCode($code){
        //获取session中的验证码
        $session_code = session('TEL_CAPTCHA');
        session('TEL_CAPTCHA',null);
        return $code == $session_code;
    }
    
    /**
     * 用户注册,密码使用加盐加密.
     */
    public function addMember() {
        $this->data['password'] =  salt_password($this->data['password'], $this->data['salt']);
        return $this->add();
    }
}
