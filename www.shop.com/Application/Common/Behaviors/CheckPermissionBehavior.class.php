<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Common\Behaviors;

/**
 * Description of CheckPermissionBehavior
 *
 * @author qingf
 */
class CheckPermissionBehavior extends \Think\Behavior {

    public function run(&$params) {
        $userinfo = session('MEMBER_INFO');
        //如果session中没有数据,表示需要登陆,就执行自动登陆的逻辑
        if(empty($userinfo)){
            $admin_model = D('Member');
            //自动登陆并保存用户的信息和权限信息
            $admin_model->autoLogin();
        }
    }

}
