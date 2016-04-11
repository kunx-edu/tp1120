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
        return $this->field($field)->where(array('status' => 1))->select();
    }
    
    /**
     * 添加分类.
     * @return integer|boolean
     */
    public function addCategory() {
        //计算左右节点和层级,并保存
        return $this->add();
    }

}
