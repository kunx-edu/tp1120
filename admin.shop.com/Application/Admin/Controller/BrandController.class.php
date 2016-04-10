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
class BrandController extends \Think\Controller {
    
    /**
     * 存储模型对象.
     * @var \Admin\Model\SupplierModel 
     */
    private $_model = null;

    protected function _initialize() {
        $meta_titles = array(
            'index'  => '品牌管理',
            'add'    => '添加品牌',
            'edit'   => '修改品牌',
            'delete' => '删除品牌',
        );
        $meta_title  = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Brand');
    }

    /**
     * 品牌列表:
     * 1.分页
     * 2.搜索
     */
    public function index() {
        //获取搜索关键字的功能
        $cond = array();
        //模糊查询品牌的名字
        $keyword = I('get.keyword');
        if($keyword){
            $cond['name'] = array('like','%'.$keyword.'%');
        }
        $this->assign($this->_model->getPageResult($cond));
        $this->display();
    }

    /**
     * 添加品牌.
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
     * 修改品牌.
     * @param integer $id 品牌唯一标识.
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
     * 使用逻辑删除一个品牌.
     * @param integer $id 品牌id.
     */
    public function delete($id) {
        
        $data = array(
            'status'=>-1,
            'name'=>array('exp',"CONCAT(name,'_del')"),//等同于name=CONCAT(name,'_del')
        );
        //删除的品牌名字后添加_del后缀标识
        if($this->_model->where(array('id'=>$id))->setField($data)===false){
            $this->error(get_error($this->_model->getError()));
        }else{
            $this->success('删除成功');
        }
    }

}
