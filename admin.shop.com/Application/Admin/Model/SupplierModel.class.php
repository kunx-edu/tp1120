<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Model;

/**
 * Description of SupplierModel
 * 
 * @author qingf
 */
class SupplierModel extends \Think\Model{
    
    protected $_validate = array(
        /**
         * 1.名字必填
         * 2.名字唯一
         */
        array('name','require','供应商名字不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
        array('name','','供货商已存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT)
    );
    
    /**
     * 要求不使用数组函数,进行数组合并,要求如果元素键名冲突,就以第一个为准
     * @param array $cond
     * @return type
     */
    
    public function getPageResult(array $cond=array()){
        $cond = $cond + array(
            'status'=>array('gt',-1),
        );
        //获取总行数
        $count = $this->where($cond)->count();
        //获取页尺寸
        $size = C('PAGE_SIZE');
        $page_obj = new \Think\Page($count,$size);
        $page_obj->setConfig('theme', C('PAGE_THEME'));
        $page_html = $page_obj->show();
        $rows = $this->where($cond)->page(I('get.p'),$size)->select();
        return array(
            'rows'=>$rows,
            'page_html'=>$page_html,
        );
    }
}
