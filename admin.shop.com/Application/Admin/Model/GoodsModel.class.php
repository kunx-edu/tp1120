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
class GoodsModel extends \Think\Model {

    //商品的状态
    public $goods_statuses = array(
        1 => '精品',
        2 => '新品',
        4 => '热销',
    );
    //商品的售卖状态
    public $is_on_sales = array(
        1 => '上架',
        0 => '下架',
    );

    /**
     * 1.商品名称不能为空
     * 2.商品分类不能为空
     * 3.商品的库存不能为空
     * 4.商品的价格不能为空
     * @var type 
     */
    protected $_validate = array(
        array('name', 'require', '商品名称不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('goods_category_id', 'require', '商品分类不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('stock', 'require', '商品库存不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('shop_price', 'require', '市场价不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('market_price', 'require', '售价不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
    );

    /**
     * 自动完成,在插入的时候将商品状态执行按位或
     * 添加的时间应当是当前时间
     * @var type 
     */
    protected $_auto = array(
        array('goods_status', 'array_sum', self::MODEL_INSERT, 'function'),
        array('inputtime', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 新增商品.
     * 1.保存基本信息
     * 1.1计算货号
     * 2.保存详细描述
     * 3.TODO:保存相册信息
     */
    public function addGoods() {
        unset($this->data['id']);
        $this->_calc_sn();
        //保存基本信息
        if (($goods_id = $this->add()) === false) {
            return false;
        }

        //保存详细描述
        if ($this->_save_goods_content($goods_id) === false) {
            $this->error = '保存商品详细描述失败';
            return false;
        }
        //保存相册信息
        if ($this->_save_gallery($goods_id) === false) {
            $this->error = '保存相册失败';
            return false;
        }
        
        //保存会员价
        if ($this->_save_member_price($goods_id) === false) {
            $this->error = '保存会员价失败';
            return false;
        }
        return true;
    }

    /**
     * 计算货号.
     */
    private function _calc_sn() {
        //1.货号计算
        $sn = $this->data['sn'];
        //如果没有传递货号,就生成SN年月日当天第几个商品
        if (empty($sn)) {
            $day               = date('Ymd');
            //我们需要知道当天已经创建了多少个商品了
            $goods_count_model = M('GoodsDayCount');
            //如果是今天的第一个商品,就插入一条记录
            if (!($count             = $goods_count_model->getFieldByDay($day, 'count'))) {
                $count = 1;
                $data  = array(
                    'day'   => $day,
                    'count' => $count,
                );
                $goods_count_model->add($data);
                //如果不是第一个商品,就执行更新操作
            } else {
                $count++;
                $goods_count_model->where(array('day' => $day))->setInc('count', 1);
            }
        }
        $this->data['sn'] = 'SN' . $day . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * 保存相册.
     * @param integer $goods_id 商品id
     * @return boolean
     */
    private function _save_gallery($goods_id) {
        $paths = I('post.path');
        if (!$paths) {
            return true;
        }
        $gallery_model = M('GoodsGallery');
        //用于保存所有的图片信息
        $data          = array();
        foreach ($paths as $path) {
            $data[] = array(
                'goods_id' => $goods_id,
                'path'     => $path,
            );
        }
        return $gallery_model->addAll($data);
    }

    /**
     * 保存商品的详细描述信息到商品详情表.
     * @param integer $goods_id 商品的id.
     * @param bool    $is_new   是否新增.
     * @return integer|bool
     */
    private function _save_goods_content($goods_id, $is_new = true) {
        $content = I('post.content', '', false);
        $data    = array(
            'goods_id' => $goods_id,
            'content'  => $content,
        );
        if ($is_new) {
            return M('GoodsIntro')->add($data);
        } else {
            return M('GoodsIntro')->save($data);
        }
    }

    /**
     * 获取商品列表.
     * @param type $field
     * @return type
     */
    public function getList($field = '*') {
        return $this->field($field)->where(array('status' => 1))->select();
    }

    /**
     * 要求不使用数组函数,进行数组合并,要求如果元素键名冲突,就以第一个为准
     * @param array $cond
     * @return type
     */
    public function getPageResult(array $cond = array()) {
        $cond      = $cond + array(
            'status' => 1,
        );
        //获取总行数
        $count     = $this->where($cond)->count();
        //获取页尺寸
        $size      = C('PAGE_SIZE');
        $page_obj  = new \Think\Page($count, $size);
        $page_obj->setConfig('theme', C('PAGE_THEME'));
        $page_html = $page_obj->show();
        $rows      = $this->where($cond)->page(I('get.p'), $size)->select();
        foreach ($rows as $key => $value) {
            $rows[$key]['is_best'] = $value['goods_status'] & 1 ? 1 : 0;
            $rows[$key]['is_new']  = $value['goods_status'] & 2 ? 1 : 0;
            $rows[$key]['is_hot']  = $value['goods_status'] & 4 ? 1 : 0;
        }
        return array(
            'rows'      => $rows,
            'page_html' => $page_html,
        );
    }

    /**
     * 通过商品的id获取详细信息.
     * 基本信息
     * 商品详细描述
     * TODO:商品的相册
     * @param integer $goods_id
     */
    public function getGoodsInfo($goods_id) {
        $cond = array(
            'status' => 1,
        );
        $row  = $this->where($cond)->find($goods_id);
        if (empty($row)) {
            $this->error = '商品不存在';
            return false;
        }
        //为了使前端比较方便的回显数据,我们将goods_status数据处理后交给模板
        $tmp_status          = $row['goods_status'];
        $row['goods_status'] = array();
        if ($tmp_status & 1) {
            $row['goods_status'][] = 1;
        }
        if ($tmp_status & 2) {
            $row['goods_status'][] = 2;
        }
        if ($tmp_status & 4) {
            $row['goods_status'][] = 4;
        }
        $row['goods_status'] = json_encode($row['goods_status']);
        //取得详细描述
        $content             = M('GoodsIntro')->getFieldByGoodsId($goods_id, 'content');
        $row['content']      = $content ? $content : '';

        //取得相册内容
        $paths        = M('GoodsGallery')->where(array('goods_id' => $goods_id))->getField('id,id,path', true);
        $row['paths'] = $paths ? $paths : array();
        
        //读取会员价格
        $member_price_model = M('MemberGoodsPrice');
        $member_prices = $member_price_model->where(['goods_id'=>$goods_id])->getField('member_level_id,price');
        $row['member_prices'] = $member_prices;
        return $row;
    }

    /**
     * 修改商品
     * @return boolean
     */
    public function updateGoods() {
        $request_data = $this->data;
        //1.保存基本信息
        if ($this->save() === false) {
            return false;
        }
        //2.保存详细描述
        if ($this->_save_goods_content($request_data['id'], false) === false) {
            $this->error = '保存商品详细描述失败';
            return false;
        }

        //3.TODO:保存相册信息
        if ($this->_save_gallery($request_data['id']) === false) {
            $this->error = '保存相册失败';
            return false;
        }

        //保存会员价
        if ($this->_save_member_price($request_data['id'],false) === false) {
            $this->error = '保存会员价失败';
            return false;
        }
        return true;
    }

    /**
     * 会员价格
     * @param integer $goods_id 商品id.
     */
    private function _save_member_price($goods_id,$is_new=true){
        $member_prices = I('post.member_price');
        if (!$member_prices) {
            return true;
        }
        $member_price_model = M('MemberGoodsPrice');
        //用于保存所有的会员价格
        $data          = array();
        foreach ($member_prices as $key=>$value) {
            //如果没有填写,就按照级别折扣,不需要创建记录
            if(empty($value)){
                continue;
            }
            $data[] = array(
                'goods_id' => $goods_id,
                'member_level_id'=>$key,
                'price'     => $value,
            );
        }
        if(!$is_new){
            $member_price_model->where(['goods_id'=>$goods_id])->delete();
        }
        //如果会员价没有设置,直接返回成立
        if(empty($data)){
            return true;
        }
        return $member_price_model->addAll($data);
    }
}
