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
        
        $userinfo = session('USERINFO');
        //如果session中没有数据,表示需要登陆,就执行自动登陆的逻辑
        if(empty($userinfo)){
            $admin_model = D('Admin');
            //自动登陆并保存用户的信息和权限信息
            $admin_model->autoLogin();
            //保存之后由于要判断是否是超级管理员,所以要再取一次用户信息
            $userinfo = session('USERINFO');
        }
        
        //判断是否是超级管理员
        if ($userinfo) {
            //如果发现是超级管理员用户,就可以操作任何请求
            if($userinfo['username'] == 'admin'){
                return true;
            }
        }
        
        //先判断是否忽略
        $url    = implode('/', [MODULE_NAME, CONTROLLER_NAME, ACTION_NAME]);
//        if (in_array($url, $ignore)) {
//            return true;
//        }
        //获取用户可以访问的路径
        $paths = session('PATHS');
        if(!is_array($paths)){
            $paths = [];
        }
//        $ignore = C('IGNORE_PATHS');
        $paths = array_merge(C('IGNORE_PATHS'),$paths);
        //获取当前请求的路径
        if (!in_array($url, $paths)) {
            $url = U('Admin/Admin/login');
            redirect($url);
        }
    }

}
