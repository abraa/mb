<?php
/**
 * ====================================
 * 公共控制器
 * ====================================
 * Author: 9004396
 * Date: 2017-01-10 19:49
 * ====================================
 * Project: new.m.chinaskin.cn
 * File: PublicController.class.php
 * ====================================
 */
namespace Common\Controller;

use Common\Extend\Base\Config;

class PublicController extends InitController {
    public $jumpUrl = '';

    public function __construct() {
        parent::__construct();
        Config::init();
    }

}