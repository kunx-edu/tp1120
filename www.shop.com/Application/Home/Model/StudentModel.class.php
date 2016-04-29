<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */
namespace Home\Model;

class StudentModel extends \Think\Model\RelationModel{
    protected $_link = array(
        'detail'=>array(
            'mapping_type'=>self::HAS_ONE,
            'class_name'=>'StudentDetail',
            'foreign_key'=>'id',
//            'mapping_name'=>'abc',
            'as_fields'=>'intro',
        ),
        'wenzhang'=>array(
            'mapping_type'=>self::HAS_MANY,
            'class_name'=>'Wenzhang',
            'foreign_key'=>'sid',
        ),
//        'demo2'=>array(
//            'mapping_type'=>self::HAS_ONE,
//            'class_name'=>'StudentDetail2',
//            'foreign_key'=>'id',
//            
//        ),
        
    );
}
