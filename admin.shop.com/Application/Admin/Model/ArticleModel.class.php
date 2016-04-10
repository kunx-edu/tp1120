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
class ArticleModel extends \Think\Model{
    
    protected $_validate = array(
        /**
         * 1.名字必填
         * 2.内容必填
         */
        array('name','require','文章名字不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
        array('content','require','文章内容不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
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
    
    /**
     * 新建文章,保存文章详情到详情表.
     * @return boolean
     */
    public function addArticle(){
        if(($article_id = $this->add())===false){
            $this->error = '添加文章失败';
            return false;
        }
        $data = array(
            'article_id'=>$article_id,
            'content'=>I('post.content'),
        );
        if(M('ArticleContent')->add($data)===false){
            $this->error = '添加文章失败';
            return false;
        }
        return true;
    }
    /**
     * 编辑文章,保存文章详情到详情表.
     * @return boolean
     */
    public function updateArticle(){
        $request_data = $this->data;
        if(($article_id = $this->save())===false){
            $this->error = '编辑文章失败';
            return false;
        }
        $data = array(
            'article_id'=>$request_data['id'],
            'content'=>I('post.content'),
        );
        if(M('ArticleContent')->save($data)===false){
            $this->error = '编辑文章失败';
            return false;
        }
        return true;
    }
    
    
    public function getArticleInfo($id){
        $row = $this->find($id);
        if($row){
            $row['content'] = M('ArticleContent')->getFieldByArticleId($id,'content');
        }
        return $row;
    }
    
}
