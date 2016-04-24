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


    /**
     * 购物车列表展示.
     */
    public function flow1(){
        //取出购物车数据
        $rows = $this->_model->getShoppingCar();
        $this->assign($rows);
        $this->display();
    }
    
    /**
     * 用户提交订单信息.
     * 必须登录,如果没有登录就跳转到登录页,登陆后再回来.
     */
    public function flow2(){
        //如果是登陆成功才能看到
        cookie('forward',null);
        if(!session('MEMBER_INFO')){
            cookie('forward',__SELF__);
            $this->error('请先登录',U('Member/login'));
        }
        $this->display('flow2');
    }
}
