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
class CheckPermissionBehavior extends \Think\Behavior{
    public function run(&$params) {
        //先判断是否忽略
        $url = implode('/', [MODULE_NAME,CONTROLLER_NAME,ACTION_NAME]);
        $ignore = [
            'Admin/Admin/login',
            'Admin/Captcha/captcha',
            'Admin/Index/index',
            'Admin/Index/top',
            'Admin/Index/menu',
            'Admin/Index/main',
            
        ];
        if(in_array($url, $ignore)){
            return true;
        }
        //SELECT DISTINCT path FROM admin_role ar LEFT JOIN role_permission rp ON ar.`role_id`=rp.`role_id` LEFT JOIN permission p ON rp.`permission_id`=p.`id` WHERE admin_id=1 AND path<>''
        
        //SELECT DISTINCT path FROM admin_permission ap LEFT JOIN permission p ON ap.`permission_id` = p.`id` WHERE admin_id=1 AND path<>''
        $userinfo = session('USERINFO');
        if($userinfo){
            $paths = session('PATHS');
//            $sql = "SELECT DISTINCT path FROM admin_role ar LEFT JOIN role_permission rp ON ar.`role_id`=rp.`role_id` LEFT JOIN permission p ON rp.`permission_id`=p.`id` WHERE admin_id={$userinfo['id']} AND path<>''";
//            $permissions = M()->query($sql);
//            $paths = [];
//            foreach($permissions as $permission){
//                $paths[] = $permission['path'];
//            }
//            
            //获取当前请求的路径
            dump($paths);
            if(in_array($url, $paths)){
                echo 'you got it';
            }else{
                echo 'error';
                exit;
                $url = U('Admin/Admin/login');
                redirect($url);
            }
        }
    }

//put your code here
}
