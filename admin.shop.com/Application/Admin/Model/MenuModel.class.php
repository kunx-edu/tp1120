<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Model;

/**
 * Description of MenuModel
 *
 * @author qingf
 */
class MenuModel extends \Think\Model{
    /**
     * 自动验证规则.
     * @var array 
     */
    protected $_validate = array(
        array('name','require','菜单名称必填',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );
    
    /**
     * 获取菜单列表.
     * @param string $field 字段列表.
     * @return type
     */
    public function getList($field='*'){
        $cond = array(
            'status'=>1,
        );
        return $this->field($field)->where($cond)->order('lft')->select();
    }
    
    /**
     * 获取菜单信息,包括权限列表.
     * @param integer $id 菜单id.
     * @return boolean
     */
    public function getMenuInfo($id) {
        $row = $this->where(['status'=>1])->find($id);
        if(empty($row)){
            $this->error = '菜单不存在';
            return false;
        }
        //获取权限列表
        $menu_permission_model = M('MenuPermission');
        $cond = [
            'menu_id'=>$id,
        ];
        $permission_ids = $menu_permission_model->where($cond)->getField('permission_id',true);
        $row['permission_ids'] = json_encode($permission_ids);
        return $row;
    }
    
    /**
     * 添加菜单.
     * 保存基本信息
     * 保存和权限的关联关系.
     */
    public function addMenu(){
        $nestedsets = $this->_get_nestedsets();
        if(($menu_id = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom'))===false){
            $this->error = '添加菜单失败';
            return false;
        }
        
        //保存权限关系
        if($this->_save_permission($menu_id) === false){
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }
    
    public function updateMenu(){
        $request_data = $this->data;
        //判断是否修改了父级菜单,如果修改了才会使用nestedsets
        $parent_id = $this->where(['id'=>  $this->data['id']])->getField('parent_id');
        if($parent_id != $this->data['parent_id']){
            $nestedsets = $this->_get_nestedsets();
            if($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom')===false){
                $this->error = '不能将菜单移动到其后代菜单下';
                return false;
            }
        }
        //保存基本信息
        if($this->save()===false){
            return false;
        }
        //保存权限关联关系
        if($this->_save_permission($request_data['id']) === false){
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }
    
    private function _get_nestedsets(){
        $mysql_db = D('DbMySql','Logic');
        return new \Admin\Service\NestedSets($mysql_db, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
    }
    
    /**
     * 保存菜单-权限关联关系
     * @param integer $menu_id 菜单id.
     * @param bool    $is_new  新增还是修改,新增true 修改false.
     * @return boolean
     */
    private function _save_permission($menu_id,$is_new=true){
        //获取权限列表
        $perms = I('post.perm');
        if(empty($perms)){
            return true;
        }
        //生成一个数组
        $data = array();
        foreach($perms as $perm){
            $data[] = array(
                'menu_id'=>$menu_id,
                'permission_id'=>$perm,
            );
        }
        //添加
        $model = M('MenuPermission');
        if(!$is_new){
            $model->where(['menu_id'=>$menu_id])->delete();
        }
        return $model->addAll($data);
    }
    
    /**
     * 删除菜单及其后代菜单,逻辑删除
     * @param integer $id
     */
    public function deleteMenu($id) {
        //获取到菜单的左右节点
        $menu_info = $this->where(['status'=>1,'id'=>$id])->getField('id,lft,rght');
        $cond = [
            'lft'=>array('egt',$menu_info[$id]['lft']),
            'rght'=>array('elt',$menu_info[$id]['rght']),
        ];
        return $this->where($cond)->setField('status',0);
    }
}
