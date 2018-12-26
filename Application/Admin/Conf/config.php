<?php

return array(
    'SESSION_PREFIX' => 'admin_', //session前缀
    'COOKIE_PREFIX' => 'admin_', // Cookie前缀 避免冲突
    'LOAD_EXT_FILE'=>'data',
//    'SHOW_PAGE_TRACE' => true,
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__ . '/Public',
        '__IMG__' => __ROOT__ . '/Public/img',
        '__CSS__' => __ROOT__ . '/Public/css',
        '__JS__' => __ROOT__ . '/Public/js',
        '__SKIN__' => __ROOT__ . '/Public/skin',
    ),
    'NOT_CHECK_LOGIN_ACTION' => array(
        'public/login', //登录页面
        'public/verify_code', //图片验证码
        'public/test', //图片验证码
        'public/auto_into_waters', //客户掉入公海-自动任务
    ),
);
