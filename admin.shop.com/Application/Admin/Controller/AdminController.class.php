<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Controller;

/**
 * Description of AdminController
 *
 * @author qingf
 */
class AdminController extends \Think\Controller{
    /**
     * 存储模型对象.
     * @var \Admin\Model\PermissionModel 
     */
    private $_model = null;

    /**
     * 设置标题和初始化模型.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '管理员管理',
            'add'    => '添加管理员',
            'edit'   => '修改管理员',
            'delete' => '删除管理员',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Admin');
    }
    
    public function index(){
        $this->display();
    }
    
    public function add(){
        $this->_before_view();
        $this->display();
    }
    
    
    private function _before_view() {
        //准备所有的权限,用于ztree展示
        $permissions = D('Permission')->getList('id,name,parent_id');
        $this->assign('permissions', json_encode($permissions));
        //准备所有的角色
        $roles = D('Role')->getList('id,name');
        $this->assign('roles', $roles);
    }
}
