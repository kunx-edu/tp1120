<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {

    /**
     * 存储模型对象.
     * @var \Admin\Model\MemberModel 
     */
    private $_model = null;

    /**
     * 设置标题和初始化模型.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'    => '啊咿呀哟母婴商城',
            'register' => '用户注册',
            'edit'     => '修改商品分类',
            'delete'   => '删除商品分类',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '啊咿呀哟母婴商城';
        $this->assign('meta_title', $meta_title);
//        $this->_model = D('Member');
        
        //获取所有的商品分类
        $goods_category_model = D('GoodsCategory');
        $categories = $goods_category_model->getList();
        $this->assign('categories', $categories);
        
        if(ACTION_NAME == 'index'){
            $this->assign('show_category', true);
        }else{
            $this->assign('show_category', false);
        }
    }

    /**
     * 首页
     */
    public function index() {
        
        $this->display();
    }
    
    /**
     * 商品详情页.
     * @param integer $id 商品id.
     */
    public function goods($id){
        $this->display();
    }

}
