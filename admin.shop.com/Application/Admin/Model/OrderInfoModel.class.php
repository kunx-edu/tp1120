<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Model;

/**
 * Description of OrderInfoModel
 *
 * @author qingf
 */
class OrderInfoModel extends \Think\Model{
    //订单状态
    public $order_statuses = [
        0=>'已关闭',
        1=>'待支付',
        2=>'待发货',
        3=>'待收货',
        4=>'已完成',
    ];
    /**
     * 要求不使用数组函数,进行数组合并,要求如果元素键名冲突,就以第一个为准
     * @param array $cond
     * @return type
     */
    public function getPageResult(array $cond = array()) {
        //获取总行数
        $count     = $this->where($cond)->count();
        //获取页尺寸
        $size      = C('PAGE_SIZE');
        $page_obj  = new \Think\Page($count, $size);
        $page_obj->setConfig('theme', C('PAGE_THEME'));
        $page_html = $page_obj->show();
        $rows      = $this->where($cond)->page(I('get.p'), $size)->select();
        return array(
            'rows'      => $rows,
            'page_html' => $page_html,
        );
    }
}
