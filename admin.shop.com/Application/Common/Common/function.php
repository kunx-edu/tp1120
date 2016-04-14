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


function arr2select($data,$name,$value_field='id',$name_field='name'){
    $html = '<select name="' . $name .'">';
    $html .= '<option value="">请选择...</option>';
    foreach($data as $value){
        $html .= '<option value="'.$value[$value_field].'">' .$value[$name_field]. '</option>';
    }
    $html .='</select>';
    return $html;
}