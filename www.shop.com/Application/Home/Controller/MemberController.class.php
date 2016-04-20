<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of MemberController
 *
 * @author qingf
 */
class MemberController extends \Think\Controller{
    public function register(){
        if(IS_POST){
            //1.收集数据
            //2.执行插入
            //3.提示跳转
        }else{
            $this->display();
        }
    }
}
