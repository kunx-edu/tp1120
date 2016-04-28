<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of ShoppingCarController
 *
 * @author qingf
 */
class ShoppingCarController extends \Think\Controller {

    /**
     * @var \Home\Model\ShoppingCarModel 
     */
    private $_model = null;

    protected function _initialize() {
        $meta_titles  = array(
            'flow1' => '我的购物车',
            'flow2' => '填写核对订单信息',
            'flow3' => '成功提交订单',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '我的购物车';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('ShoppingCar');
    }

    public function test(){
        $sub_query = M()->table('hx_shop_record')->distinct(true)->field('pid')->order('create_time desc')->buildSql();
        echo M()->table('Goods')->where('id in ' .$sub_query)->order('status asc,inputtime desc')->page(1,20)->select(false);
        exit;
    }
    /**
     * 购物车列表展示.
     */
    public function flow1() {
        //取出购物车数据
        $rows = $this->_model->getShoppingCar();
        $this->assign($rows);
        $this->display();
    }

    /**
     * 用户提交订单信息.
     * 必须登录,如果没有登录就跳转到登录页,登陆后再回来.
     */
    public function flow2() {
        //如果是登陆成功才能看到
        check_login();
        //获取地址列表
        $address_model  = D('Address');
        $addresses      = $address_model->getList();
        $this->assign('addresses', $addresses);
        //获取配送方式列表
        $delivery_model = D('Delivery');
        $deliveries     = $delivery_model->getList();
        $this->assign('deliveries', $deliveries);
        $delivery_price = 0;
        foreach ($deliveries as $delivery) {
            if ($delivery['is_default'] == 1) {
                $delivery_price = $delivery['price'];
                break;
            }
        }
        $this->assign('delivery_price', $delivery_price);
        //获取支付方式列表
        $payment_model = D('Payment');
        $payments      = $payment_model->getList();
        $this->assign('payments', $payments);
        //获取购物车信息
        //取出购物车数据
        $rows          = $this->_model->getShoppingCar();
        $this->assign($rows);

        $this->display('flow2');
    }

}
