<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {

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
            'index'    => '啊咿呀哟母婴商城',
            'register' => '用户注册',
            'edit'     => '修改商品分类',
            'delete'   => '删除商品分类',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '啊咿呀哟母婴商城';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Article');
        
        //使用数据缓存出处商品分类和文章列表
        if(!$categories = S('goods_categories')){
            //获取所有的商品分类
            $categories =  D('GoodsCategory')->getList();
            S('goods_categories',$categories);
        }
        $this->assign('categories', $categories);
        
        if(!$help_articles=S('help_articles')){
            $help_articles = $this->_model->getHelpArticleList();
            S('help_articles',$help_articles);
        }
        //获取帮助文章列表
        $this->assign('help_articles',$help_articles);
        
        //首页才展示分类列表
        if(ACTION_NAME == 'index'){
            $this->assign('show_category', true);
        }else{
            $this->assign('show_category', false);
        }
    }

    /**
     * 首页
     */
    public function index() {
        //取出精品\新品\热销商品列表
        $goods_model = D('Goods');
        $goods_list['best_list'] = $goods_model->getGoodsListByGoodsStatus(1);
        $goods_list['new_list'] = $goods_model->getGoodsListByGoodsStatus(2);
        $goods_list['hot_list'] = $goods_model->getGoodsListByGoodsStatus(4);
        $this->assign($goods_list);
        $this->display();
    }
    
    /**
     * 商品详情页.
     * @param integer $id 商品id.
     */
    public function goods($id){
        
        //获取商品基本内容和详情还有相册
        $goods_model = D('Goods');
        //获取商品信息,如果出错,就跳回到首页
        if(!$row = $goods_model->getGoodsInfo($id)){
            $this->error(get_error($goods_model->getError()),U('index'));
        }
        $this->assign('row',$row);
        //取出商品的会员价格
        $this->display();
    }
    
    /**
     * 获取到点击次数.
     * @param integer $goods_id 商品id
     */
    public function getGoodsClickTimes($goods_id){
        $goods_model = D('Goods');
        $click_times = $goods_model->getGoodsClickFromRedis($goods_id);
        $data =['click_times'=>$click_times];
        die(json_encode($data));
    }

    /**
     * 添加到购物车
     * @param integer $goods_id 商品id.
     * @param integer $amount   购买数量.
     */
    public function add2Car($goods_id,$amount) {
        //区分是否是已登录状态
        $shopping_car_model = D('ShoppingCar');
        $shopping_car_model->add2Car($goods_id,$amount);
        $this->success('添加购物车成功',U('ShoppingCar/flow1'));
    }
    
}
