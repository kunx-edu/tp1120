<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Model;

/**
 * Description of PermissionModel
 *
 * @author qingf
 */
class PermissionModel extends \Think\Model{
    
    /**
     * 获取所有的可用分类列表.
     * @param string $field 字段列表.
     * @return array
     */
    public function getList($field = '*') {
        return $this->field($field)->order('lft asc')->where(array('status' => 1))->select();
    }
    
    /**
     * 执行权限的添加.
     * @return boolean
     */
    public function addPermission(){
        //创建nestedsets所需的数据库对象
        $db_obj = D('DbMySql','Logic');
        //创建nestedsets对象
        $nestedsets = new \Admin\Service\NestedSets($db_obj, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        //执行插入操作
        if($nestedsets->insert($this->data['parent_id'], $this->data, 'bottom')===false){
            $this->error = '创建失败';
            return false;
        }
        return true;
    }
}
