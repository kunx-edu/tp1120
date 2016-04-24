<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of AddressController
 *
 * @author qingf
 */
class AddressController extends \Think\Controller{
    /**
     * 存储模型对象.
     * @var \Home\Model\AddressModel 
     */
    private $_model = null;

    /**
     * 设置标题和初始化模型.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'    => '收货地址管理',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '收货地址管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Address');
        
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
        
        $this->assign('show_category', false);
        
        //如果是登陆成功才能看到
        check_login();
        
    }
    
    /**
     * 收货地址列表
     */
    public function index() {
        //读取地址列表.
        $rows = $this->_model->getList();
        $this->assign('rows',$rows);
        //读取省级列表
        $provinces = $this->_model->getListByParentId();
        $this->assign('provinces', $provinces);
        $this->display();
    }
    
    public function edit($id){
        //获取当前地址
        $row = $this->_model->find($id);
        $this->assign('row', $row);
        //读取地址列表.
        $rows = $this->_model->getList();
        $this->assign('rows',$rows);
        //读取省级列表
        $provinces = $this->_model->getListByParentId();
        $this->assign('provinces', $provinces);
        $this->display('index');
    }
    
    /**
     * 通过父级地址获取地址列表
     * @param integer $parent_id 父级id.
     */
    public function getListByParentId($parent_id){
        echo json_encode($this->_model->getListByParentId($parent_id));
        exit;
    }
    
    /**
     * 添加收货地址.
     */
    public function add(){
        if($this->_model->create()===false || $this->_model->addAddress() === false){
            $this->error(get_error($this->_model->getError()));
        } else{
            $this->success('添加成功',U('index'));
        }
    }
}
