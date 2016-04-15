<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Controller;

/**
 * Description of PermissionController
 *
 * @author qingf
 */
class PermissionController extends \Think\Controller {

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
            'index'  => '权限管理',
            'add'    => '添加权限',
            'edit'   => '修改权限',
            'delete' => '删除权限',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Permission');
    }

    /**
     * 权限列表页面
     */
    public function index() {
        $rows = $this->_model->getList();
        $this->assign('rows', $rows);
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }

            //添加节点
            //提示条状
            if($this->_model->addPermission()===false){
                $this->error(get_error($this->_model->getError()));
            }
            $this->success('添加成功',U('index'));
        } else {
            $this->_before_view();
            $this->display();
        }
    }

    private function _before_view() {
        //准备所有的权限,用于ztree展示
        $categories = $this->_model->getList('id,name,parent_id');
        array_unshift($categories, array('id' => 0, 'name' => '顶级权限', 'parent_id' => 0));
        $this->assign('categories', json_encode($categories));
    }

}
