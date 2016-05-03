<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Model;

/**
 * Description of AdminModel
 *
 * @author qingf
 */
class AdminModel extends \Think\Model {
    
    protected $_validate = array(
        /**
         * name 不能为空 唯一  长度6-16位
         * password 不能为空 长度6-16位
         * email 不能为空 email格式
         */
        array('username', 'require', '用户名必填', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('username', '', '用户名已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('username', '2,16', '用户名长度不合法', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        array('password', 'require', '密码必填', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('password', '6,16', '密码长度不合法', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
        array('email', 'require', '邮箱必填', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('email', 'email', '邮箱不合法', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('username', 'require', '用户名必填', self::MUST_VALIDATE, '', 'login'),
        array('password', 'require', '密码必填', self::MUST_VALIDATE, '', 'login'),
        array('captcha', 'require', '验证码必填', self::MUST_VALIDATE, '', 'login'),
        array('captcha', 'check_captcha', '验证码不正确', self::MUST_VALIDATE, 'callback', 'login'),
    );
    
    protected $_auto     = array(
        array('salt', '\Org\Util\String::randString', self::MODEL_INSERT, 'function', 6),
        array('salt', '\Org\Util\String::randString', 'reset_pwd', 'function', 6),//当重置密码是自动生成一个盐
        array('add_time', NOW_TIME, self::MODEL_INSERT),
    );
    
    /**
     * 验证验证码
     * @param string $code 用户提交的验证码.
     * @return bool
     */
    protected function check_captcha($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }


    /**
     * 管理员-角色关联模型.
     * @var \Think\Model 
     */
    private $_admin_role_model = null;
    
    /**
     * 管理员-权限关联模型.
     * @var \Think\Model 
     */
    private $_admin_permission_model = null;

    /**
     * 重写父类的构造方法,创建了两个基本都会使用到的模型对象.
     * @param type $name
     * @param type $tablePrefix
     * @param type $connection
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        parent::__construct($name, $tablePrefix, $connection);
        $this->_admin_role_model = M('AdminRole');
        $this->_admin_permission_model = M('AdminPermission');
    }
    
    /**
     * 获取列表.
     * @param string $field
     * @return type
     */
    public function getList($field = '*') {
        return $this->field($field)->where(array('status' => 1))->select();
    }

    /**
     * 添加管理员账户.
     * 关联角色
     * 关联额外权限
     * @return integer|bool
     */
    public function addAdmin() {
        //保存基本信息
        unset($this->data['id']);
        $this->data['password'] = salt_password($this->data['password'], $this->data['salt']);
        if (($admin_id               = $this->add()) === false) {
            return false;
        }
        //保存关联角色
        if ($this->_save_role($admin_id) === false) {
            $this->error = '保存角色失败';
            return false;
        }
        //保存关联权限
        if ($this->_save_permission($admin_id) === false) {
            $this->error = '保存额外权限失败';
            return false;
        }
        return true;
    }

    /**
     * 修改管理员,包括角色和权限.
     * @return boolean
     */
    public function updateAdmin() {
        $request_data = $this->data;
        //保存角色关联
        if ($this->_save_role($request_data['id'], false) === false) {
            $this->error = '更新角色关联失败';
            return false;
        }
        //保存权限关联
        if ($this->_save_permission($request_data['id'], false) === false) {
            $this->error = '更新权限关联失败';
            return false;
        }
        return true;
    }

    /**
     * 删除管理员,并删除关联的角色和权限.
     * @param integer $id 管理员id.
     * @return boolean
     */
    public function deleteAdmin($id) {
        if ($this->delete($id) === false) {
            return false;
        }
        
        if($this->_delete_role($id)===false){
            $this->error = '删除角色关联失败';
            return false;
        }
        if($this->_delete_permission($id)===false){
            $this->error = '删除权限关联失败';
            return false;
        }
        
        return true;
    }

    /**
     * 获取管理员详细信息,包括角色和权限.
     * @param integer $id
     * @return boolean
     */
    public function getAdminInfo($id) {
        $row = $this->find($id);
        if (empty($row)) {
            $this->error = '管理员不存在';
            return false;
        }
        $role_ids  = $this->_admin_role_model->where(array('admin_id' => $id))->getField('role_id', true);
        $row['role_ids'] = $role_ids;

        $permission_ids        = $this->_admin_permission_model->where(array('admin_id' => $id))->getField('permission_id', true);
        $row['permission_ids'] = json_encode($permission_ids);
        return $row;
    }

    /**
     * 保存角色关联,如果是修改,先删除原来的.
     * @param integer $admin_id 管理员id.
     * @param bool    $is_new   是否是创建管理员,是true 否false.
     * @return boolean
     */
    private function _save_role($admin_id, $is_new = true) {
        if (!$is_new) {
            if($this->_delete_role($admin_id)===false){
                return false;
            }
        }
        //获取勾选的角色,如果没有勾选任何角色,就不再执行添加操作.
        $roles = I('post.role');
        if (empty($roles)) {
            return true;
        }
        $data = array();
        foreach ($roles as $role) {
            $data[] = array(
                'admin_id' => $admin_id,
                'role_id'  => $role,
            );
        }
        return $this->_admin_role_model->addAll($data);
    }

    /**
     * 保存权限关联,如果是修改,先删除原来的.
     * @param integer $admin_id 管理员id.
     * @param bool    $is_new   是否是创建管理员,是true 否false.
     * @return boolean
     */
    private function _save_permission($admin_id, $is_new = true) {
        if (!$is_new) {
            if($this->_delete_permission($admin_id)===false){
                return false;
            }
        }
        //获取勾选的权限,如果没有勾选任何权限,就不再执行添加操作.
        $perms = I('post.perm');
        if (empty($perms)) {
            return true;
        }
        $data = array();
        foreach ($perms as $perm) {
            $data[] = array(
                'admin_id'      => $admin_id,
                'permission_id' => $perm,
            );
        }
        return $this->_admin_permission_model->addAll($data);
    }

    /**
     * 删除管理员-角色关联.
     * @param integer $admin_id 管理员id.
     * @return boolean
     */
    private function _delete_role($admin_id) {
        if ($this->_admin_role_model->where(array('admin_id' => $admin_id))->delete() === false) {
            return false;
        }
    }

    /**
     * 删除管理员-额外权限关联.
     * @param integer $admin_id 管理员id.
     * @return boolean
     */
    private function _delete_permission($admin_id) {
        if ($this->_admin_permission_model->where(array('admin_id' => $admin_id))->delete() === false) {
            return false;
        }
    }
    
    /**
     * 重置密码.
     * @param integer $id 管理员id.
     * @return string|false 成功返回新密码,失败返回false.
     */
    public function resetPwd($id) {
        //获取数据
        $password = I('post.password');
        if(empty($password)){
            //使用系统自带的随机字符串生成方法生成
            $len = mt_rand(8, 10);
            $password = \Org\Util\String::randString($len, '', '-=_+_,./?!@#$%^&*()~');
        }
        $this->data['password'] = salt_password($password, $this->data['salt']);
        return $this->save()?$password:false;
    }

    /**
     * 1.验证验证码[自动验证]
     * 2.用户名和密码必填[自动验证]
     * 3.验证用户名是否存在
     * 4.验证密码是否匹配
     */
    public function login(){
        //为了安全我们将用户信息都删除
        session('USERINFO',null);
        $request_data = $this->data;
        //1.验证用户名是否存在
        $userinfo = $this->getByUsername($this->data['username']);
        if(empty($userinfo)){
            $this->error = '用户不存在';
            return false;
        }
        //2.进行密码匹配验证
        $password = salt_password($request_data['password'], $userinfo['salt']);
        if($password != $userinfo['password']){
            $this->error = '密码不正确';
            return false;
        }
        //为了后续会话获取用户信息,我们存下来
        session('USERINFO',$userinfo);
        $this->_getPermissions($userinfo['id']);
        
        //保存自动登陆信息
        $this->_saveToken($userinfo['id']);
        return true;
    }
    
    /**
     * 判断用户是否需要自动登陆,如果需要就保存令牌到cookie和数据表中.
     * @param integer $admin_id 管理员id.
     */
    private function _saveToken($admin_id){
        //清空原有的令牌
        $token_model = M('AdminToken');
        cookie('AUTO_LOGIN_TOKEN',null);
        $token_model->delete($admin_id);
        
        //判断是否需要自动登陆
        $remeber = I('post.remember');
        if(!$remeber){
            return true;
        }
        $data = [
            'admin_id'=>$admin_id,
            'token'=>sha1(mcrypt_create_iv(32)),
        ];
        //存到cookie和数据表中
        cookie('AUTO_LOGIN_TOKEN',$data,604800);
        return $token_model->add($data);
    }
    
    /**
     * 检查令牌信息是否匹配
     */
    public function autoLogin(){
        $data = cookie('AUTO_LOGIN_TOKEN');
        $token_model = M('AdminToken');
        if(empty($data) || !$token_model->where($data)->count()){
            return false;
        }
        
        //发现令牌匹配,原令牌经应当失效,避免cookie被黑客获取
        cookie('AUTO_LOGIN_TOKEN',null);
        $token_model->delete($data['admin_id']);
        
        //获取用户信息,并保存到session中
        $userinfo = $this->find($data['admin_id']);
        session('USERINFO',$userinfo);
        
        //为了安全,我们把令牌重新成成一次
        $data = [
            'admin_id'=>$data['admin_id'],
            'token'=>sha1(mcrypt_create_iv(32)),
        ];
        
        //存到cookie和数据表中
        cookie('AUTO_LOGIN_TOKEN',$data,604800);
        if($token_model->add($data) === false){
            return false;
        } else{
            //如果发现操作都成功,就获取当前用户的权限信息
            $this->_getPermissions($data['admin_id']);
            return true;
        }
    }
    
    /**
     * 获取用户权限列表,从角色和额外权限的关联表中获取.
     * @param integer $admin_id
     */
    private function _getPermissions($admin_id){
        session('PATHS',null);
        session('PERM_IDS',null);
        //获取通过角色得到的权限
        $role_permssions = $this->distinct(true)->table('__ADMIN_ROLE__ as ar')->join('__ROLE_PERMISSION__ as rp ON ar.`role_id`=rp.`role_id`')->join('__PERMISSION__ as p ON rp.`permission_id`=p.`id`')->where(['admin_id'=>$admin_id ,'path'=>['neq','']])->getField('permission_id,path',true);
        //获取额外权限
        $admin_permissions = $this->distinct(true)->table('__ADMIN_PERMISSION__ as ap')->join('__PERMISSION__ as p ON ap.`permission_id` = p.`id`')->where(['admin_id'=>$admin_id ,'path'=>['neq','']])->getField('permission_id,path',true);
//        $paths = array_merge($role_permssions,$admin_permissions);
        //由于前面获取的都是关联数组,+合并会自动合并键名相同的元素,也就等同于做了去重
        $role_permssions=$role_permssions?:[];
        $admin_permissions=$admin_permissions?:[];
        $permissions = $role_permssions+$admin_permissions;
        //获取权限id列表
        $permission_ids = array_keys($permissions);
        //获取权限路径列表
        $paths = array_values($permissions);
        session('PATHS',$paths);
        session('PERM_IDS',$permission_ids);
    }
}
