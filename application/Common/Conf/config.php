<?php
return array(
	//'配置项'=>'配置值'
    'MODULE_ALLOW_LIST'     =>  array('Home','Cpanel'),
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称
    'DEFAULT_CHARSET'       =>  'utf-8', // 默认输出编码
    'DEFAULT_TIMEZONE'      =>  'PRC',  // 默认时区

    'URL_CASE_INSENSITIVE'      => true, //设置URL是否大小写敏感
    'LOAD_EXT_CONFIG'           => 'mysql,constant', //加载扩展配置
    'LOAD_EXT_FILE'             => 'verify,string',

    /*======== 语言配置 ========*/
    'LANG_SWITCH_ON'            => true,   // 开启语言包功能
    'DEFAULT_LANG'              => 'zh-cn', // 允许切换的语言列表 用逗号分隔

    'URL_MODEL'                 => 2, //设置URL模式
    'URL_HTML_SUFFIX'           => 'html|shtml|json',



    'VIEW_PATH'                 => './../template/Home/',


    'TMPL_ACTION_ERROR'     =>  'Public/error', // 默认错误跳转对应的模板文件
);