<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Model;

/**
 * Description of ShoppingCarModel
 *
 * @author qingf
 */
class ShoppingCarModel extends \Think\Model{
    public function add2Car($goods_id,$amount) {
        /**
         * 1.已登录
         *  存入数据表
         * 2.未登录
         *  存入cookie中
         * [
         *  goods_id=>amount
         * ]
         */
        $userinfo = session('MEMBER_INFO');
        if($userinfo){
            //判断当前的商品是否已经在购物车
            $cond = [
                'goods_id'=>$goods_id,
                'member_id'=>$userinfo['id'],
            ];
            if($this->where($cond)->count()){
                //执行添加操作
                $this->where($cond)->setInc('amount',$amount);
            } else{
                $data = array_merge($cond,['amount'=>$amount]);
                $this->add($data);
            }
        }else{
            //假设我们把未登录用户的购物车存放在SHOPPING_CAR
            $shopping_car = cookie('SHOPPING_CAR');
            if(isset($shopping_car[$goods_id])){
                $shopping_car[$goods_id] += $amount;
            }else{
                $shopping_car[$goods_id] = $amount;
            }
            //将新的购物车信息保存到cookie中
            cookie('SHOPPING_CAR',$shopping_car,604800);
        }
    }
    
    /**
     * 获取商品的名字 logo 价格 数量  id  shop_price  小计  总计
     */
    public function getShoppingCar(){
        //是否登陆
        $userinfo = session('MEMBER_INFO');
        //1.如果登陆,从数据表中获取
        if($userinfo){
            $cond = [
                'member_id'=>$userinfo['id'],
            ];
            $car_infos = $this->where($cond)->getField('goods_id,amount',true);
        //2.如果没有登录从cookie中获取
        }else{
            $car_infos = cookie('SHOPPING_CAR');
        }
        if($car_infos){
            //取出商品的id
            $goods_ids = array_keys($car_infos);
            //取出商品的详细信息
            $goods_infos = M('Goods')->where(['id'=>['in',$goods_ids]])->getField('id,logo,name,shop_price');
            $total_price = 0;
            //根据详细信息计算金额
            foreach($goods_infos as $key=>$value){
                $value['sub_total'] = money_format($value['shop_price'] * $car_infos[$key]);
                $value['amount'] = $car_infos[$key];
                $total_price = money_format($total_price + $value['sub_total']);
                $goods_infos[$key]=$value;
            }
            
            return [
                'total_price'=>$total_price,
                'goods_infos'=>$goods_infos,
            ];
        } else{
            return [
                'total_price'=>0,
                'goods_infos'=>[],
            ];
        }
    }
    
    /**
     * 将cookie中的数据保存到数据库中
     * @return boolean
     */
    public function cookie2db(){
        //假设我们把未登录用户的购物车存放在SHOPPING_CAR
        $shopping_car = cookie('SHOPPING_CAR');
        cookie('SHOPPING_CAR',null);
        if($shopping_car){
            $userinfo = session('MEMBER_INFO');
            $goods_ids = array_keys($shopping_car);
            if($this->where(['goods_id'=>['in',$goods_ids],'member_id'=>$userinfo['id']])->delete()===false){
                return false;
            } else{
                $data = [];
                foreach($shopping_car as $key=>$value){
                    $data[] = [
                        'goods_id'=>$key,
                        'amount'=>$value,
                        'member_id'=>$userinfo['id'],
                    ];
                }
                return $this->addAll($data) !== false;
            }
            
        }else{
            return true;
        }
    }
}
