<?php
namespace Home\Controller;
use Common\Extend\WeChat;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $openId = WeChat::getOpenId();
        print_r($openId);
        exit;
    }
}