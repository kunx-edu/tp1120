<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of MemberController
 *
 * @author qingf
 */
class MemberController extends \Think\Controller{
    /**
     * 存储模型对象.
     * @var \Admin\Model\MemberModel 
     */
    private $_model = null;

    /**
     * 设置标题和初始化模型.
     */
    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '啊咿呀哟母婴商城',
            'register'    => '用户注册',
            'edit'   => '修改商品分类',
            'delete' => '删除商品分类',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME])?$meta_titles[ACTION_NAME]:'啊咿呀哟母婴商城';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Member');
    }
    
    public function register(){
        if(IS_POST){
            //1.收集数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model->getError()));
            }
            //2.执行插入
            if($this->_model->addMember() === false){
                $this->error(get_error($this->_model->getError()));
            }
            //3.提示跳转
            $this->success('注册成功',U('login'));
        }else{
            $this->display();
        }
    }
    
    /**
     * 验证是否唯一.
     */
    public function checkUniqueByParams(){
        $model = D('Member');
        $cond = I('get.');
        if($cond){
            if($model->where($cond)->count()){
                $this->ajaxReturn(false);
            }
        }
        $this->ajaxReturn(true);
    }
    
    /**
     * 使用ajax发送验证码.
     * @param string $telphone
     */
    public function sendSMS($telphone){
        $code = \Org\Util\String::randNumber(1000, 9999);
        //存session
        session('TEL_CAPTCHA',$code);
        //发短信
        $data = [
            'code'=>$code,
            'product'=>'啊咿呀哟母婴商城',
        ];
        if(sendSMS($telphone, $data)){
            $this->success('发送成功');
        }else{
            $this->error('发送失败');
        }
    }
}
