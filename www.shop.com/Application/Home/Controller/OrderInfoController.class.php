<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of OrderInfoController
 *
 * @author qingf
 */
class OrderInfoController extends \Think\Controller{
    /**
     * @var \Home\Model\OrderInfoModel 
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('OrderInfo');
    }
    
    
    public function add(){
        if($this->_model->create() === false){
            $this->error(get_error($this->_model->getError()));
        }
        if($this->_model->addOrder() === false){
            $this->error(get_error($this->_model->getError()));
        }
        $this->redirect('ShoppingCar/flow3');
    }
}
