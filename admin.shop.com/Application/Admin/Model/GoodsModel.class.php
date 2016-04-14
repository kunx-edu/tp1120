<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Model;

/**
 * Description of GoodsModel
 *
 * @author qingf
 */
class GoodsModel extends \Think\Model{
    /**
     * 1.商品名称不能为空
     * 2.商品的库存不能为空
     * 3.商品的价格不能为空
     * @var type 
     */
    protected $_validate = array(
        array('name','require','商品名称不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
        array('stock','require','商品库存不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
        array('shop_price','require','市场价不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
        array('market_price','require','售价不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
    );
    
    /**
     * 自动完成,在插入的时候将商品状态执行按位或
     * 添加的时间应当是当前时间
     * @var type 
     */
    protected $_auto = array(
        array('goods_status','array_sum',self::MODEL_INSERT,'function'),
        array('inputtime',NOW_TIME,self::MODEL_INSERT),
    );
    


    /**
     * 新增商品.
     * 1.保存基本信息
     * 1.1计算货号
     * 2.保存详细描述
     * 3.保存相册信息
     */
    public function addGoods(){
        $this->calc_sn();
        //保存基本信息
        return $this->add();
        
        //保存详细描述
        
        //保存相册信息
    }
    
    private function calc_sn(){
        //1.货号计算
        $sn = $this->data['sn'];
        //如果没有传递货号,就生成SN年月日当天第几个商品
        if(empty($sn)){
            $day = date('Ymd');
            //我们需要知道当天已经创建了多少个商品了
            $goods_count_model = M('GoodsDayCount');
            if(!($count = $goods_count_model->getFieldByDay($day,'count'))){
                $count = 1;
                $data = array(
                    'day'=>$day,
                    'count'=>$count,
                );
                $goods_count_model->add($data);
            }else{
                $count++;
                $goods_count_model->where(array('day'=>$day))->setInc('count', 1);
            }
        }
        $this->data['sn'] = 'SN' . $day . str_pad($count, 5, '0',STR_PAD_LEFT);
    }
}
