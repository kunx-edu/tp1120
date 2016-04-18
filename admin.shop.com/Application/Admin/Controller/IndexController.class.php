<?php

namespace Admin\Controller;

use Think\Controller;

class IndexController extends Controller {

    /**
     * 展示后台首页框架集
     */
    public function index() {
        $this->display();
    }

    /**
     * 展示顶部banner
     */
    public function top() {
        $this->display();
    }

    /**
     * 展示菜单列表.
     */
    public function menu() {
        $menu_model = D('Menu');
        $menus      = $menu_model->getMenuList();
        $this->assign('menus', $menus);
        $this->display();
    }

    /**
     * 展示主体内容区域
     */
    public function main() {
        $this->display();
    }

}
