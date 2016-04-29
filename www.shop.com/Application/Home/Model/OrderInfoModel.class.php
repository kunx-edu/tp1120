<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Model;

/**
 * Description of OrderInfoModel
 *
 * @author qingf
 */
class OrderInfoModel extends \Think\Model {

    //订单状态
    public $order_statuses = [
        0 => '已关闭',
        1 => '待支付',
        2 => '待发货',
        3 => '待收货',
        4 => '已完成',
    ];
    protected $_auto = [
        ['inputtime', NOW_TIME, self::MODEL_INSERT]
    ];

//    public function addOrder(){
//        $flag = $this->_addOrder();
//        $this->_unlock($shopping_car);
//        $this->_clearShoppingCar($userinfo);
//        return $flag;
//    }
    public function addOrder() {
        $this->startTrans();
        $userinfo           = session('MEMBER_INFO');
        $shopping_car_model = D('ShoppingCar');
        $shopping_car       = $shopping_car_model->getShoppingCar();
        //检查商品是否正在被购买
        if($this->_checkGoodsLock($shopping_car) === false){
            $this->error = '商品太火爆了,请稍后再买';
            return false;
        }
        
        //获取库存,判断是否足够
        if ($this->_checkAndReduceStock($shopping_car) === false) {
            $this->error = '商品库存不足';
            $this->_unlock($shopping_car);
            $this->rollback();
            return false;
        }

        $address_id          = I('post.address_id');
        //1.1.1查询地址表,获取地址信息
        $this->data['price'] = $shopping_car['total_price']; //商品总价
        $address_info        = D('Address')->getAddressById($address_id);
        if (($order_id            = $this->_save_order($userinfo, $address_info)) === false) {
            $this->rollback();
            $this->_unlock($shopping_car);
            return false;
        }

        //2.保存订单详细信息
        if ($this->_save_order_detail($order_id, $shopping_car) === false) {
            $this->error = '保存订单详情失败';
            $this->_unlock($shopping_car);
            $this->rollback();
            return false;
        }

        //3.创建发票信息
        if ($this->_save_invoice($order_id, $userinfo, $address_info, $shopping_car) === false) {
            $this->error = '发票开具失败';
            $this->_unlock($shopping_car);
            $this->rollback();
            return false;
        }
        $this->commit();
        $this->_unlock($shopping_car);
        //已经创建了订单,购物车中的数据应当清除了
        $this->_clearShoppingCar($userinfo);
        return true;
    }

    /**
     * 保存订单的基本信息.
     * @param array $userinfo
     * @param array $address_info
     * @return type
     */
    private function _save_order(array $userinfo, array $address_info) {
        //1.保存基本订单信息
        //1.1准备收货信息

        $this->data['member_id']      = $userinfo['id'];
        $this->data['name']           = $address_info['name'];
        $this->data['province_name']  = $address_info['province_name'];
        $this->data['city_name']      = $address_info['city_name'];
        $this->data['area_name']      = $address_info['area_name'];
        $this->data['detail_address'] = $address_info['detail_address'];
        $this->data['tel']            = $address_info['tel'];
        return $order_id                     = $this->add();
    }

    /**
     * 保存订单详情
     * @param type $order_id
     * @param type $shopping_car
     * @return type
     */
    private function _save_order_detail($order_id, $shopping_car) {

        $data = [];
        foreach ($shopping_car['goods_infos'] as $goods) {
            $data[] = [
                'order_info_id' => $order_id,
                'goods_id'      => $goods['id'],
                'goods_name'    => $goods['name'],
                'logo'          => $goods['logo'],
                'price'         => $goods['member_price'],
                'amount'        => $goods['amount'],
                'total_price'   => $goods['sub_total'],
            ];
        }
        return M('OrderInfoItem')->addAll($data);
    }

    /**
     * 保存发票.
     * @param type $order_id
     * @param type $userinfo
     * @param type $address_info
     * @param type $shopping_car
     * @return type
     */
    private function _save_invoice($order_id, $userinfo, $address_info, $shopping_car) {
        //开发票
        $invoice_type    = I('post.type');
        $invoice_content = '';
        /**
         * 抬头
         *  iPhone7  5299 × 1
         * 总计
         *  5299
         */
        if ($invoice_type == 1) {
            $invoice_name = $address_info['name'];
        } else {
            $invoice_name = I('post.company_name');
        }
        //准备发票明细

        $invoice_content_type = I('post.content');
        switch ($invoice_content_type) {
            case 1:
                foreach ($shopping_car['goods_infos'] as $goods) {
                    $invoice_content .= $goods['name'] . ' ' . $goods['shop_price'] . ' × ' . $goods['amount'] . "\n";
                }
                break;
            case 2:
                $invoice_content .= "办公用品\n";
                break;
            case 3:
                $invoice_content .= "体育休闲\n";
                break;
            case 4:
                $invoice_content .= "耗材\n";
                break;
        }
        $invoice_content = $invoice_name . "\n" . $invoice_content . '总计:' . $shopping_car['total_price'];
        //添加到数据表

        $data = [
            'name'          => $invoice_name,
            'content'       => $invoice_content,
            'order_info_id' => $order_id,
            'member_id'     => $userinfo['id'],
            'inputtime'     => NOW_TIME,
            'price'         => $shopping_car['total_price'],
        ];
        return M('Invoice')->add($data);
    }

    public function getList() {
        $userinfo              = session('MEMBER_INFO');
        //订单基本信息
        $rows                  = $this->where(['member_id' => $userinfo['id']])->select();
        //获取各个订单的商品列表
        $order_info_item_model = M('OrderInfoItem');
        foreach ($rows as $key => $value) {
            $value['goods_list'] = $order_info_item_model->getFieldByOrderInfoId($value['id'], 'goods_id,logo');
            $rows[$key]          = $value;
        }
        return $rows;
    }

    /**
     * 检查并扣除库存.
     * @param array $shopping_car 购物车数据
     * @return boolean
     */
    private function _checkAndReduceStock(array $shopping_car) {
        //检查库存是否足够
        foreach ($shopping_car['goods_infos'] as $goods) {
            if ($goods['amount'] > $goods['stock']) {
                return false;
            }
        }
        $goods_model = M('Goods');
        //扣除库存
        $flag        = true;
        foreach ($shopping_car['goods_infos'] as $goods) {
            $flag = $flag && $goods_model->where(['id' => $goods['id']])->setDec('stock', $goods['amount']);
        }
        return $flag;
    }

    /**
     * 清除购物车
     * @param array $userinfo     用户信息
     */
    private function _clearShoppingCar(array $userinfo) {
        return M('ShoppingCar')->where(['member_id' => $userinfo['id']])->delete();
    }

    /**
     * 检查商品是否在购物流程中,如果是,就返回错误,否者添加进去.
     * @param array $shopping_car
     * @return boolean
     * 
     * 1 ,4 ,6,2,3
     * 2,3,4
     */
    private function _checkGoodsLock(array $shopping_car) {
        $redis = get_redis();
        $key   = 'goods_lock';
        //遍历所有的商品,判断是否在购买流程中
        $flag  = true; //表示可以购买
        foreach ($shopping_car['goods_infos'] as $goods) {
            if ($redis->sismember($key, $goods['id'])) {
                $flag = false;
                break;
            }
        }
        if ($flag) {
            foreach ($shopping_car['goods_infos'] as $goods) {
                $redis->sAdd($key,$goods['id']);
            }
        }
    }

    /**
     * 从redis中释放购物车商品.
     * @param array $shopping_car
     */
    private function _unlock(array $shopping_car) {
        $redis = get_redis();
        $key   = 'goods_lock';
        //遍历所有的商品,判断是否在购买流程中
        $flag = true;
        foreach ($shopping_car['goods_infos'] as $goods) {
            $flag = $flag && $redis->srem($key,$goods['id']);
        }
        return $flag;
    }
}
