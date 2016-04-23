<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Model;

/**
 * Description of GoodsModel
 *
 * @author qingf
 */
class GoodsModel extends \Think\Model{
    
    /**
     * 获取指定促销状态的商品列表
     * @param integer $goods_status 商品促销状态:1精品  2新品  4热销
     * @param integer $limit        取出多少条.
     * @return array
     */
    public function getGoodsListByGoodsStatus($goods_status,$limit=5){
        $cond = [
            'goods_status & ' . $goods_status,
            'status'=>1,
            'is_on_sale'=>1,
        ];
        return $this->field('id,name,logo,shop_price')->where($cond)->select();
    }
    
    /**
     * 获取商品信息.
     * 包括相册和详细描述.
     * @param integer $id 商品id.
     * @return array|bool
     */
    public function getGoodsInfo($id){
        //1.获取基本信息
        $cond = [
            'status'=>1,
            'is_on_sale'=>1,
            'id'=>$id,
        ];
        $row = $this->where($cond)->find();
        if(!$row){
            $this->error = '您所查找的商品离家出走了';
            return false;
        }
        $row['brand_name'] = M('Brand')->where(['id'=>$row['brand_id']])->getField('name');
        //2.获取详细描述
        $row['content'] = M('GoodsIntro')->where(['goods_id'=>$id])->getField('content');
        //3.获取相册列表
        $row['galleries'] = M('GoodsGallery')->where(['goods_id'=>$id])->getField('path',true);
//        $row['click_times'] = $this->getGoodsClick($id);
        return $row;
    }
    
    /**
     * 获取商品点击数,并且保存到数据库中
     * @param integer $goods_id 商品id.
     * @return int
     */
    public function getGoodsClick($goods_id){
        $goods_click_model = M('GoodsClick');
        $count =$goods_click_model->getFieldByGoodsId($goods_id,'click_times');
        if(empty($count)){
            $goods_click_model->add(['goods_id'=>$goods_id,'click_times'=>1]);
            return 1;
        } else{
            $goods_click_model->where(['goods_id'=>$goods_id])->setInc('click_times', 1);
            return $count;
        }
    }
    
    /**
     * 从redis中获取商品的点击数,商品的点击数存放在goods_click的hash中
     * @param integer $goods_id 商品id
     */
    public function getGoodsClickFromRedis($goods_id){
        $key = 'goods_click';
        $redis = get_redis();
        return $redis->hIncrBy($key,$goods_id,1);
    }
}
