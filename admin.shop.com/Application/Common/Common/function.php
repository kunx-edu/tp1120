<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

/**
 * 将错误信息转换成一个有序列表字符串.
 * @param array|string $errors 错误信息.
 * @return string
 */
function get_error($errors) {
    if (!is_array($errors)) {
        $errors = array($errors);
    }
    $html = '<ol>';
    foreach ($errors as $error) {
        $html .= '<li>' . $error . '</li>';
    }
    $html .= '<ol>';
    return $html;
}

/**
 * 将数据库中取出的结果集形成一个下拉列表
 * @param array  $data        二维数组结果集.
 * @param string $name        用于提交表单的名字.
 * @param string $value_field value属性数据来源.
 * @param string $name_field  文案数据来源.
 * @param string $select      回显的选项.
 * @return string
 */
function arr2select(array $data,$name,$value_field='id',$name_field='name',$select=''){
    $html = '<select name="' . $name .'">';
    $html .= '<option value="">请选择...</option>';
    foreach($data as $value){
        if($value[$value_field] == $select){
            $html .= '<option value="'.$value[$value_field].'" selected="selected">' .$value[$name_field]. '</option>';
        }else{
            $html .= '<option value="'.$value[$value_field].'">' .$value[$name_field]. '</option>';
        }
    }
    $html .='</select>';
    return $html;
}