<?php

define('DOMAIN', 'http://www.shop.com');
return array(
//'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__CSS__'               => DOMAIN . '/Public/css',
        '__JS__'                => DOMAIN . '/Public/js',
        '__IMG__'               => DOMAIN . '/Public/images',
        '__JQ_VALIDATION__'               => DOMAIN . '/Public/ext/jquery_validation',
        
    ),
    //配置数据库连接
    'DB_TYPE'           => 'mysql',
    'DB_HOST'           => '127.0.0.1',
    'DB_PORT'           => '3306',
    'DB_USER'           => 'root',
    'DB_PWD'            => '123456',
    'DB_NAME'           => 'tp1120',
    'DB_PREFIX'         => '',
    'DB_CHARSET'        => 'utf8',
    'SHOW_PAGE_TRACE'   => true,
    
    'ALIDAYU_SETTING'=>[
        'ak'=>'23349782',
        'sk'=>'031fe4c712dafbb29fb1f82e82b4ba19',
    ],
    
    'CAPTCHA_SETTING'   => [
        'length' => 4,
    ],
    
    'EMAIL_SETTING'=>[
//        'host'=>'smtp.163.com',
//        'username'=>'13036395508@163.com',
//        'password'=>'yuyuangang28',
//        'smtpsecure'=>'ssl',
//        'port'=>465,
        'host'=>'smtp.126.com',
        'username'=>'kunx_edu@126.com',
        'password'=>'iam4ge',
        'smtpsecure'=>'ssl',
        'port'=>465,
    ],
    
    
    
    
    //Redis Session配置
    'SESSION_AUTO_START'    =>  true,    // 是否自动开启Session
    'SESSION_TYPE'            =>  'Redis',    //session类型
    'SESSION_PERSISTENT'    =>  1,        //是否长连接(对于php来说0和1都一样)
    'SESSION_CACHE_TIME'    =>  1,        //连接超时时间(秒)
    'SESSION_EXPIRE'        =>  0,        //session有效期(单位:秒) 0表示永久缓存
    'SESSION_PREFIX'        =>  'sess_',        //session前缀
    'SESSION_REDIS_HOST'    =>  '127.0.0.1', //分布式Redis,默认第一个为主服务器
    'SESSION_REDIS_PORT'    =>  '6379',           //端口,如果相同只填一个,用英文逗号分隔
    'SESSION_REDIS_AUTH'    =>  '',    //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔
);
