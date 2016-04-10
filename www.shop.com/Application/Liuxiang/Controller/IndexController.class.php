<?php
namespace liuxiang\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function login(){
       $user_moedl=M('Admin');
      $rows= $user_moedl->select();
       $this->assign('rows',$rows);
       $this->display();
    }
}