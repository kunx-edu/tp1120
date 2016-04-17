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
     * @var \Admin\Model\AdminModel 
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
        $this->assign('rows',$this->_model->getList());
        $this->display();
    }
    
    /**
     * 添加管理员.
     */
    public function add(){
        if(IS_POST){
            if($this->_model->create() === false){
                $this->error(get_error($this->_model->getError()));
            }
            if($this->_model->addAdmin() === false){
                $this->error(get_error($this->_model->getError()));
            }
            $this->success('添加成功',U('index'));
        }else{
            $this->_before_view();
            $this->display();
        }
    }
    
    /**
     * 修改管理员
     * @param integer $id
     */
    public function edit($id){
        if(IS_POST){
            if($this->_model->create() === false){
                $this->error(get_error($this->_model->getError()));
            }
            if($this->_model->updateAdmin() === false){
                $this->error(get_error($this->_model->getError()));
            }
            $this->success('修改成功',U('index'));
        }else{
            //获取管理员数据,包括基本信息 角色 权限
            $this->assign('row', $this->_model->getAdminInfo($id));
            $this->_before_view();
            $this->display('add');
        }
    }
    
    /**
     * 删除用户
     * @param integer $id
     */
    public function delete($id) {
        if($this->_model->deleteAdmin($id) === false){
            $this->error(get_error($this->_model->getError()));
        }
        $this->success('删除成功',U('index'));
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
