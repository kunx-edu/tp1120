<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Admin\Controller;

/**
 * Description of EditorController
 *
 * @author qingf
 */
class EditorController extends \Think\Controller{
    public function ueditor(){
        $data = new \Org\Util\Ueditor(C('UPLOAD_SETTING'));
        echo $data->output();
        exit;
    }
}
