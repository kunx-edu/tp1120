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
class SupplierController extends \Think\Controller {
    
    /**
     * 存储模型对象.
     * @var \Admin\Model\SupplierModel 
     */
    private $_model = null;

//    public function __construct() {
//        parent::__construct();
//    }

    protected function _initialize() {
        $meta_titles = array(
            'index'  => '供货商管理',
            'add'    => '添加供货商',
            'edit'   => '修改供货商',
            'delete' => '删除供货商',
        );
        $meta_title  = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Supplier');
    }

    /**
     * 供货商列表:
     * 1.分页
     * 2.搜索
     */
    public function index() {
//        $rows  = $this->_model->getPageResult();
        $this->assign($this->_model->getPageResult());
        $this->display();
    }

    /**
     * 添加供货商.
     * 使用了自动验证.
     */
    public function add() {
        if (IS_POST) {
            // 收集数据.
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            // 保存数据.
            if ($this->_model->add() === false) {
                $this->error(get_error($this->_model->getError()));
            } else {
                $this->success('添加成功', U('index'));
            }
        } else {
            $this->display();
        }
    }

    /**
     * 修改供货商.
     * @param integer $id 供货商唯一标识.
     */
    public function edit($id) {
        if (IS_POST) {
            // 收集数据.
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            // 保存数据.
            if ($this->_model->save() === false) {
                $this->error(get_error($this->_model->getError()));
            } else {
                $this->success('修改成功', U('index'));
            }
        } else {
            //取出数据表中的内容回显
            $row = $this->_model->find($id);
            $this->assign('row', $row);
            $this->display('add');
        }
    }

    /**
     * 使用逻辑删除一个供货商.
     * @param integer $id 供货商id.
     */
    public function delete($id) {
        
        $data = array(
            'status'=>-1,
            'name'=>array('exp',"CONCAT(name,'_del')"),//等同于name=CONCAT(name,'_del')
        );
        
        if($this->_model->where(array('id'=>$id))->setField($data)===false){
            $this->error(get_error($this->_model->getError()));
        }else{
            $this->success('删除成功');
        }
    }

}
