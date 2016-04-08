<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Controller;

/**
 * Description of SupplierController
 *
 * @author qingf
 */
class SupplierController extends \Think\Controller{
    
//    public function __construct() {
//        parent::__construct();
//    }
    
    protected function _initialize(){
        $meta_titles = array(
            'index'=>'供货商管理',
            'add'=>'添加供货商',
            'edit'=>'修改供货商',
            'delete'=>'删除供货商',
        );
        $meta_title = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
    }
    
    /**
     * 供货商列表:
     * 1.分页
     * 2.搜索
     */
    public function index() {
        $model = D('Supplier');
        $rows = $model->select();
        $this->assign('rows',$rows);
//        $this->assign('meta_title', '供货商管理');
        $this->display();
    }
    
    public function add() {
//        $this->assign('meta_title', '供货商管理');
        $this->display();
    }
    
    public function edit($id) {
        $this->display('add');
    }
    
    public function delete($id) {
        $this->display();
    }
}
