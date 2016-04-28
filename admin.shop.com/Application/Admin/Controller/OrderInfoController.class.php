<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Controller;

class OrderInfoController extends \Think\Controller{
    /**
     * 存储模型对象.
     * @var \Admin\Model\OrderInfoModel 
     */
    private $_model = null;

    /**
     * 设置标题和初始化模型.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '订单管理',
            'add'    => '添加订单',
            'edit'   => '修改订单',
            'delete' => '删除订单',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('OrderInfo');
    }
    
    public function index(){
        /**
         * 订单搜索条件
         * 订单状态
         * 订单金额区间
         * 收货人电话号码
         * 收货人姓名
         * 订单的创建时间
         */
        //1.读取分页订单列表
        $cond = [];
        $rows = $this->_model->getPageResult($cond);
        $this->assign($rows);
        //获取所有的支付方式
        $pay_types = M('Payment')->where(['status'=>1])->getField('id,name');
        $this->assign('pay_types', $pay_types);
        $this->assign('order_statuses', $this->_model->order_statuses);
        $this->display();
    }
    
    /**
     * 发货
     * 1.渲染一个表单,用于收集物流信息
     * 2.接收提交的数据,修改自己的订单状态,然后调用支付宝,告知订单已发货
     * @param type $id
     */
    public function send($id){
        $data = [
            'status'=>3,
            'id'=>$id,
        ];
        if($this->_model->save($data)===false){
            $this->error($this->_model->getError());
        }else{
            $this->success('发货成功');
        }
    }
}
