<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Model;

/**
 * Description of RoleModel
 *
 * @author qingf
 */
class RoleModel extends \Think\Model{
    /**
     * 自动验证
     * @var array 
     */
    protected  $_validate = array(
        array('name','require','角色名不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
    );
    
    /**
     * 添加角色,并保存角色和权限关联.
     * @return boolean
     */
    public function addRole() {
        //添加角色
        //1.保存基本信息
        if(($role_id = $this->add()) === false){
            return false;
        }
        //2.保存角色-权限关联信息
        if($this->_save_permission($role_id)===false){
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }
    
    /**
     * 保存角色和权限的关联关系
     * @param integer $role_id 角色id.
     * @param bool    $is_new  新增还是修改,如果修改先删除原来的数据.
     * @return boolean
     */
    private function _save_permission($role_id,$is_new=true){
        $perms = I('post.perm');
        if(empty($perms)){
            return true;
        }
        
        $data = array();
        foreach ($perms as $perm){
            $data[] = array(
                'role_id'=>$role_id,
                'permission_id'=>$perm,
            );
        }
        $model = M('RolePermission');
        if(!$is_new){
            $model->where(array('role_id'=>$role_id))->delete();
        }
        return M('RolePermission')->addAll($data);
    }
    
    /**
     * 获取列表.
     * @param string $field
     * @return type
     */
    public function getList($field='*'){
        return $this->field($field)->where(array('status' => 1))->select();
    }
    
    /**
     * 获取角色详情,包括权限.
     * @param integer $id 角色id.
     * @return boolean
     */
    public function getRoleInfo($id){
        $row = $this->where(array('status' => 1))->find($id);
        if(empty($row)){
            $this->error = '角色不存在';
            return false;
        }
        $permission_model =M('RolePermission');
        $permission_ids = $permission_model->where(array('role_id'=>$id))->getField('permission_id',true);
        $row['permission_ids'] = json_encode($permission_ids);
        return $row;
    }
    
    /**
     * 修改角色并保存关联的权限.
     * @return boolean
     */
    public function updateRole(){
        $request_data = $this->data;
        //1.保存基本信息
        if($this->save() === false){
            return false;
        }
        //2.保存关联关系
        if($this->_save_permission($request_data['id'], false) === false){
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }
    
    /**
     * 删除角色,逻辑删除,不删除关联的权限.
     * @param integer $id 删除角色.
     * @return integer|bool
     */
    public function deleteRole($id){
        return $this->where(array('id'=>$id))->setField('status',0);
    }
}
