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
     * TODO:
     * 收货人姓名\电话\各级地址必填
     * 手机号码使用正则约束13|14|15|17|18
     * @var type 
     */
    protected $_auto = [
        ['member_id','get_user_id',self::MODEL_INSERT,'function'],
    ];


    /**
     * 获取指定地址下的地址.
     * @param integer $parent_id 父级地址id
     * @return array
     */
    public function getListByParentId($parent_id=0){
        return M('Locations')->where(['parent_id'=>$parent_id])->select();
    }
}
