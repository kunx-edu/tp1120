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
        //使用数据缓存出处商品分类和文章列表
        if(!$categories = S('goods_categories')){
            //获取所有的商品分类
            $categories =  D('GoodsCategory')->getList();
            S('goods_categories',$categories);
        }
        $this->assign('categories', $categories);
        
        if(!$help_articles=S('help_articles')){
            $help_articles = $this->_model->getHelpArticleList();
            S('help_articles',$help_articles);
        }
        //获取帮助文章列表
        $this->assign('help_articles',$help_articles);
        
        //首页才展示分类列表
        $this->assign('show_category', false);
        
        $this->_model = D('OrderInfo'); 
        //如果是登陆成功才能看到
        check_login();
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
    
    public function index(){
        //获取当前用户的订单列表.
        $rows = $this->_model->getList();
        $this->assign('rows', $rows);
        //获取所有的支付方式
        $pay_types = M('Payment')->where(['status'=>1])->getField('id,name');
        $this->assign('pay_types', $pay_types);
        $this->assign('order_statuses', $this->_model->order_statuses);
        $this->display();
    }
    
    public function finish($id) {
        $data = [
            'status'=>4,
            'id'=>$id,
        ];
        if($this->_model->save($data)===false){
            $this->error($this->_model->getError());
        }else{
            $this->success('订单完成');
        }
    }
}
