<?php

define('DOMAIN', 'http://admin.shop.com');
return array(
    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__CSS__' => DOMAIN . '/Public/css',
        '__JS__'  => DOMAIN . '/Public/js',
        '__IMG__' => DOMAIN . '/Public/images',
    ),
    
    //配置数据库连接
    'DB_TYPE'=>'mysql',
    'DB_HOST'=>'127.0.0.1',
    'DB_PORT'=>'3306',
    'DB_USER'=>'root',
    'DB_PWD'=>'123456',
    'DB_NAME'=>'tp1120',
    'DB_PREFIX'=>'',
    'DB_CHARSET'=>'utf8',
    'SHOW_PAGE_TRACE'=>true,
);
