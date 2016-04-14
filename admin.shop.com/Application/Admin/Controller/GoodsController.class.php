<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Controller;

/**
 * Description of GoodsController
 *
 * @author qingf
 */
class GoodsController extends \Think\Controller{
    /**
     * 存储模型对象.
     * @var \Admin\Model\GoodsModel 
     */
    private $_model = null;

    /**
     * 设置标题和初始化模型.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '商品管理',
            'add'    => '添加商品',
            'edit'   => '修改商品',
            'delete' => '删除商品',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Goods');
    }
    
    /**
     * 展示商品列表.
     */
    public function index(){
        //准备数据
        $rows = $this->_model->getPageResult();
        $this->assign($rows);
        $this->display();
    }
    
    /**
     * 添加商品
     */
    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model->getError()));
            }
            if($this->_model->addGoods() === false){
                $this->error(get_error($this->_model->getError()));
            }else{
                $this->success('添加商品成功',U('index'));
            }
        }else{
            //1.获取商品分类json数据
            //2.传递到表单
            $this->_before_view();
            $this->display();
        }
    }
    
    public function edit($id) {
        if(IS_POST){
            //1.收集数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model->getError()));
            }
            //2.执行修改
            if($this->_model->updateGoods()===false){
                $this->error(get_error($this->_model->getError()));
            }else{
                $this->success('修改商品成功',U('index'));
            }
            
        }else{
            //获取商品信息,如果没有找到,就跳转到列表页
            if(!$row = $this->_model->getGoodsInfo($id)){
                $this->error('请检查商品id',U('index'));
            }
            $this->_before_view();
            $this->assign('row', $row);
            $this->display('add');
        }
    }
    
    /**
     * 准备分类列表用于选择父级分类,ztree插件使用的是json对象,所以传递的是json字符串.
     */
    private function _before_view(){
        //商品分类
        $categories = D('GoodsCategory')->getList('id,name,parent_id');
        $this->assign('categories', json_encode($categories));
        //商品品牌
        $brands = D('Brand')->getList('id,name');
        $this->assign('brands', $brands);
        //商品供货商
        $suppliers = D('Supplier')->getList('id,name');
        $this->assign('suppliers', $suppliers);
        
    }
}
