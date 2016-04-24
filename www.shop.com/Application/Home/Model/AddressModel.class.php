<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Model;

/**
 * Description of AddressModel
 *
 * @author qingf
 */
class AddressModel extends \Think\Model{
    
    /**
     * 获取指定地址下的地址.
     * @param integer $parent_id 父级地址id
     * @return array
     */
    public function getListByParentId($parent_id=0){
        return M('Locations')->where(['parent_id'=>$parent_id])->select();
    }
}
