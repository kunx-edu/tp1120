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
class ArticleController extends \Think\Controller {

    /**
     * 存储模型对象.
     * @var \Admin\Model\SupplierModel 
     */
    private $_model = null;

    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '文章管理',
            'add'    => '添加文章',
            'edit'   => '修改文章',
            'delete' => '删除文章',
        );
        $meta_title   = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Article');
    }

    /**
     * 文章列表:
     * 1.分页
     * 2.搜索
     */
    public function index() {
        //获取搜索关键字的功能
        $cond    = array();
        //模糊查询文章的名字
        $keyword = I('get.keyword');
        if ($keyword) {
            $cond['name'] = array('like', '%' . $keyword . '%');
        }
        $categories = D('ArticleCategory')->getList();
        $this->assign('categories', $categories);
        $this->assign($this->_model->getPageResult($cond));
        $this->display();
    }

    /**
     * 添加文章.
     * 使用了自动验证.
     */
    public function add() {
        if (IS_POST) {
            // 收集数据.
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            // 保存数据.
            if ($this->_model->addArticle() === false) {
                $this->error(get_error($this->_model->getError()));
            } else {
                $this->success('添加成功', U('index'));
            }
        } else {
            $categories = D('ArticleCategory')->getList();
            $this->assign('categories', $categories);
            $this->display();
        }
    }

    /**
     * 修改文章.
     * @param integer $id 文章唯一标识.
     */
    public function edit($id) {
        if (IS_POST) {
            // 收集数据.
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            // 保存数据.
            if ($this->_model->updateArticle() === false) {
                $this->error(get_error($this->_model->getError()));
            } else {
                $this->success('修改成功', U('index'));
            }
        } else {
            //取出数据表中的内容回显
            $row        = $this->_model->getArticleInfo($id);
            $categories = D('ArticleCategory')->getList();
            $this->assign('row', $row);
            $this->assign('categories', $categories);
            $this->display('add');
        }
    }

    /**
     * 使用逻辑删除一个文章.
     * @param integer $id 文章id.
     */
    public function delete($id) {
        $data = array(
            'status' => 0,
        );
        //删除的文章名字后添加_del后缀标识
        if ($this->_model->where(array('id' => $id))->setField($data) === false) {
            $this->error(get_error($this->_model->getError()));
        } else {
            $this->success('删除成功');
        }
    }

}
