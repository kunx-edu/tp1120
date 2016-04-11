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
        $config    = C('UPLOAD_SETTING');
        $upload    = new \Think\Upload($config);
//        var_dump($_FILES);
        //2.执行上传
        $file_info = $upload->upload($_FILES);
        //3.返回上传的结果
        if ($config['driver'] == 'Qiniu') {
            $file_url = $file_info['file']['savepath'] . $file_info['file']['savename'];
            $file_url = str_replace('/', '_', $file_url);
        } else {
            $file_url = $file_info['file']['savepath'] . $file_info['file']['savename'];
        }
//        echo $file_url;
        $return = array(
            'status'     => $file_info ? 1 : 0,
            'file_url'   => $file_url,
            'url_prefix' => $config['URL_PREFIX'],
            'msg'        => $upload->getError(),
            'file_info'  => $file_info,
        );
        $this->ajaxReturn($return);
//        echo json_encode($return);
//        exit;
    }

}
