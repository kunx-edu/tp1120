<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Model;

/**
 * Description of DeliveryModel
 *
 * @author qingf
 */
class DeliveryModel extends \Think\Model{
    public function getList(){
        return $this->where(['status'=>1])->select();
    }
}
