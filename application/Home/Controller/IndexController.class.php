<?php
namespace Home\Controller;
use Common\Extend\WeChat;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
       echo '<h1 style="text-align: center;padding-top: 13%">瓷肌医疗门诊部-美容顾客系统</h1>';
       exit;
    }

    /**
     * 查看日记文件，用于调试支付等功能
     */
    public function showLog(){
        $log_name = I('request.log_name','','trim');
        $remove = I('request.remove',0,'intval');
        if(!empty($log_name) && file_exists(LOG_PATH . $log_name)){
            $content = file_get_contents(LOG_PATH . $log_name);
            if($remove > 0){
                unlink(LOG_PATH . $log_name);
            }
            echo $content;
        }else{
            send_http_status(404);
        }
        exit;
    }
}