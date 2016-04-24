<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of ShoppingCarController
 *
 * @author qingf
 */
class ShoppingCarController extends \Think\Controller{
    /**
     * @var \Home\Model\ShoppingCarModel 
     */
    private $_model = null;
    protected function _initialize(){
        $this->_model = D('ShoppingCar');
    }


    public function flow1(){
        //取出购物车数据
        $rows = $this->_model->getShoppingCar();
        $this->assign($rows);
        $this->display();
    }
    public function flow2(){
        //如果是登陆成功才能看到
        if(!session('MEMBER_INFO')){
            $this->error('请先登录',U('Member/login'));
        }
        $this->display('flow1');
    }
}
