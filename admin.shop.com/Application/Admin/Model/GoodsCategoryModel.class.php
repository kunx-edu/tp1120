<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Model;

/**
 * Description of GoodsCategoryModel
 *
 * @author qingf
 */
class GoodsCategoryModel extends \Think\Model {

    /**
     * 获取所有的可用分类列表.
     * @param string $field 字段列表.
     * @return array
     */
    public function getList($field = '*') {
        return $this->field($field)->order('lft asc')->where(array('status' => 1))->select();
    }
    
    /**
     * 添加分类.
     * @return integer|boolean
     */
    public function addCategory() {
        //使用我们的nestedsets计算左右节点和层级
        //实例化nestedsets需要的数据库操作对象
        $mysql_db = new \Admin\Logic\DbMySqlLogic();
        //实例化nestedsets并告知它表名和相关字段名分别是什么
        $nestedsets = new \Admin\Service\NestedSets($mysql_db, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        //nestedsets的insert完成数据的插入操作,不再需要执行模型的add方法了.
        //自动计算左右节点和层级,并保存
        return $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom');
    }
    
    /**
     * 用于修改分类
     * 包括如果修改了父级分类,重新计算左右节点和层级
     */
    public function updateCategory(){
        //1如果没有修改父级分类,就不需要计算节点和层级
        //1.1 获取原来的父级节点
        $parent_id = $this->getFieldById($this->data['id'],'parent_id');
        if($parent_id != $this->data['parent_id']){
            //2.重新计算左右节点和层级
            //2.1实例化nestedsets所需要的数据库连接
             $mysql_db = new \Admin\Logic\DbMySqlLogic();
            //2.2实例化nestedsets并告知它表名和相关字段名分别是什么
            $nestedsets = new \Admin\Service\NestedSets($mysql_db, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
            //2.3执行移动的方法
            if($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom')===false){
                $this->error = '不能将当前分类移动到其后代分类';
                return false;
            }
        }
        return $this->save();
    }

}
