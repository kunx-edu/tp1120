<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Controller;

/**
 * Description of GoodsGalleryController
 *
 * @author qingf
 */
class GoodsGalleryController extends \Think\Controller{
    /**
     * 用于删除相册中的一张图片,使用ajax
     */
    public function delete($id){
        $gallery_model = M('GoodsGallery');
        if(M('GoodsGallery')->delete($id) === false){
            $this->error(get_error($gallery_model->getError()));
        }else{
            $this->success('删除成功');
        }
    }
}
