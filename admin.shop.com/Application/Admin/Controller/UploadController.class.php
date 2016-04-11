<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Controller;

/**
 * Description of UploadController
 *
 * @author qingf
 */
class UploadController extends \Think\Controller {

    /**
     * 用于上传文件.
     */
    public function upload() {
        
        //1.创建upload对象
        $upload     = new \Think\Upload(C('UPLOAD_SETTING'));
//        var_dump($_FILES);
        //2.执行上传
        $file_info = $upload->upload($_FILES);
        //3.返回上传的结果
        $file_url = $file_info['file']['savepath'] . $file_info['file']['savename'];
//        echo $file_url;
        $return = array(
            'status'=>$file_info?1:0,
            'file_url'=>$file_url,
            'msg'=>$upload->getError(),
        );
        $this->ajaxReturn($return);
//        echo json_encode($return);
//        exit;
    }

}
