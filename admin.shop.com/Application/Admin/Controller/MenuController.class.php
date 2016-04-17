<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Controller;

/**
 * Description of MenuController
 *
 * @author qingf
 */
class MenuController extends \Think\Controller{
    
    /**
     * 存储模型对象.
     * @var \Admin\Model\MenuModel 
     */
    private $_model = null;

    /**
     * 设置标题和初始化模型.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '菜单管理',
            'add'    => '添加菜单',
            'edit'   => '修改菜单',
            'delete' => '删除菜单',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Menu');
    }
    
    /**
     * 展示菜单列表
     */
    public function index(){
        $this->assign('rows', $this->_model->getList());
        $this->display();
    }
    
    public function add(){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model->getError()));
            }
            if($this->_model->addMenu()===false){
                $this->error(get_error($this->_model->getError()));
            }
            $this->success('添加成功',U('index'));
        }else{
            $this->_before_view();
            $this->display();
        }
    }
    
    public function edit($id){
        if(IS_POST){
            if($this->_model->create()===false){
                $this->error(get_error($this->_model->getError()));
            }
            if($this->_model->updateMenu()===false){
                $this->error(get_error($this->_model->getError()));
            }
            $this->success('修改成功',U('index'));
        }else{
            //准备数据
            $row = $this->_model->getMenuInfo($id);
            $this->assign('row', $row);
            $this->_before_view();
            $this->display('add');
        }
    }
    
    /**
     * 删除菜单.
     * @param integer $id
     */
    public function delete($id){
        //删除菜单及其后代菜单
        if($this->_model->deleteMenu($id)===false){
            $this->error(get_error($this->_model->getError()));
        }
        $this->success('删除成功',U('index'));
    }
    
    private function _before_view(){
        //准备权限列表
        $permission_model = D('Permission');
        $permissions = $permission_model->getList('id,name,parent_id');
        $this->assign('permissions', json_encode($permissions));
        //准备菜单列表
        $menus = $this->_model->getList('id,name,parent_id');
        array_unshift($menus, ['id'=>0,'parent_id'=>0,'name'=>'顶级菜单']);
        $this->assign('menus', json_encode($menus));
    }
}
