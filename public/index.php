<?php
/**
 * ====================================
 * 应用入口文件
 * ====================================
 * Author: 9004396
 * Date: 2017-07-26 13:36
 * ====================================
 * Project: vtm.chinaskin.cn
 * File: index.php
 * ====================================
 */
header("Content-Type: text/html; charset=utf-8");

header('Server:ChinaSkin Inc.');
ini_set ('eaccelerator.enable',0);
ini_set ('eaccelerator.optimizer',0);

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 设置项目根目录
define('APP_ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

//框架核心路径
define('FRAME_PATH', APP_ROOT.'../frame/');

//定义应用目录
define('APP_PATH', APP_ROOT.'../application/');

//设置临时文件路径
define('RUNTIME_PATH', APP_ROOT . '../runtime/');

//设置第三方库路径
define('VENDOR_PATH', APP_ROOT . '../vendor/');

// 定义当前文件名，以防fcgi模式下发生错误
define('_PHP_FILE_',    rtrim($_SERVER['SCRIPT_NAME'],'/'));

// 引入第三方库自动加载类
require VENDOR_PATH.'autoload.php';

// 引入ThinkPHP入口文件
require FRAME_PATH.'ThinkPHP.php';


