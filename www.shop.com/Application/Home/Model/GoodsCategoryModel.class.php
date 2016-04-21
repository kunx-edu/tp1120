<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Model;

/**
 * Description of GoodsCategoryModel
 *
 * @author qingf
 */
class GoodsCategoryModel extends \Think\Model{
    
    /**
     * 获取分类列表.
     * @return array
     */
    public function getList(){
        $cond = [
            'status'=>1,
        ];
        return $this->where($cond)->select();
    }
}
