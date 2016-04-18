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
        //先判断是否忽略
        $url    = implode('/', [MODULE_NAME, CONTROLLER_NAME, ACTION_NAME]);
        $ignore = C('IGNORE_PATHS');
        if (in_array($url, $ignore)) {
            return true;
        }
        $userinfo = session('USERINFO');
        if ($userinfo) {
            //如果发现是超级管理员用户,就可以操作任何请求
            if($userinfo['username'] == 'admin'){
                return true;
            }
            $paths = session('PATHS');
            //获取当前请求的路径
            if (!in_array($url, $paths)) {
                $url = U('Admin/Admin/login');
                redirect($url);
            }
        }
    }

}
