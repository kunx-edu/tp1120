<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Model;

/**
 * Description of ClassModel
 *
 * @author qingf
 */
class ClassModel extends \Think\Model\RelationModel{
    protected $_link = array(
        'stu'=>array(
            'mapping_type'=>self::HAS_MANY,
            'class_name'=>'Student',
            'foreign_key'=>'cid',
        ),
    );
}
