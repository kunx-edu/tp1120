<?php

define('DOMAIN', 'http://admin.shop.com');
return array(
//'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__CSS__'       => DOMAIN . '/Public/css',
        '__JS__'        => DOMAIN . '/Public/js',
        '__IMG__'       => DOMAIN . '/Public/images',
        '__UPLOADIFY__' => DOMAIN . '/Public/ext/uploadify',
        '__LAYER__'     => DOMAIN . '/Public/ext/layer',
        '__ZTREE__'     => DOMAIN . '/Public/ext/ztree',
        '__UPLOAD_URL_PREFIX__'   => 'http://7xsucm.com1.z0.glb.clouddn.com',
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
    'PAGE_SIZE'         => 2,
    'PAGE_THEME'        => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    'URL_MODEL'         => 2,
    //文件上传的相关配置.
    'UPLOAD_SETTING'    => array(
        'maxSize'      => 0, //上传的文件大小限制 (0-不做限制)
        'exts'         => array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
        'autoSub'      => true, //自动子目录保存文件
        'subName'      => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'     => './Uploads/', //保存根路径
        'savePath'     => '', //保存路径
        'saveName'     => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'      => '', //文件保存后缀，空则使用原后缀
        'replace'      => false, //存在同名是否覆盖
        'hash'         => true, //是否生成hash编码
        'callback'     => false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver'       => 'Qiniu', // 文件上传驱动
        'driverConfig' => array(
            'secrectKey' => 'KBYoPnqTbgX4a65rXNI9f-6_kCKwwnHMSnLOGLNk', //七牛sk
            'accessKey'  => 'qJHe4wo24q6X6AWSXsv-syl8PkhHjo6i5WXc-to5', //七牛ak
            'domain'     => '7xsucm.com1.z0.glb.clouddn.com', //空间域名
            'bucket'     => 'tp1120', //空间名称
            'timeout'    => 300, //超时时间
        ), // 上传驱动配置
    ),
);
