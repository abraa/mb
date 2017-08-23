<?php
/**
 * ====================================
 * 评论入口
 * ====================================
 * Author: 9009123
 * Date: 2017-08-21
 * ====================================
 * File: CommentController.class.php
 * ====================================
 */
namespace Home\Controller;
use Think\Controller;
use Common\Extend\WeChat;

class CommentController extends Controller {
    /**
     * 微信理发师评论
     */
    public function HaircutWechat(){
        $openid = WeChat::getOpenId();
        $user_info = WeChat::getWeChatInfo($openid);
        if(!empty($user_info)){
            $WechatUserModel = D('WechatUser');
            $id = $WechatUserModel->where(array('openid'=>$openid))->getField('id');
            if($id > 0){
                $WechatUserModel->where(array('id'=>$id))->save($user_info);
            }else{
                $WechatUserModel->add($user_info);
            }
        }
        if(IS_POST && IS_AJAX){
            $employee_id = I('post.employee_id','','trim');
            $is_satisfy = I('post.is_satisfy',0,'intval');
            $tag_list = I('post.tag_list','','trim');
            $content = I('post.content','','trim');

            if(empty($employee_id)){
                $this->error('请确认发型师工号！');
            }
            $tag_list = !empty($tag_list) ? json_decode($tag_list) : array();
            if(empty($tag_list)){
                $this->error('请对发型师选择一些适合的标签！');
            }
            $employee_name = D('Employee')->where(array('employee_id'=>$employee_id))->getField('employee_name');
            if(empty($employee_name)){
                $this->error('当前发型师工号不存在！');
            }
            $tag_ids = array();
            foreach($tag_list as $tag){
                $tag_ids[] = isset($tag->id) ? $tag->id : $tag['id'];
            }
            $tagList = D('CommentTag')->field('id')->where(array('id'=>array('IN',implode($tag_ids,',')),'is_satisfy'=>$is_satisfy))->select();
            if(empty($tagList)){
                $this->error('选择的标签不存在！');
            }
            $tag_ids = array();
            foreach($tagList as $tag){
                $tag_ids[] = $tag['id'];
            }
            $data = array(
                'openid'=>$openid,
                'employee_id'=>$employee_id,
                'employee_name'=>$employee_name,
                'content'=>(empty($content) ? '' : $content),
                'tag_ids'=>implode(',',$tag_ids),
                'is_satisfy'=>$is_satisfy,
                'create_time'=>time()
            );
            $result = D('Comment')->add($data);
            if($result === false){
                $result = D('Comment')->add($data);  //重试
            }
            if($result === false){
                $this->error('评价失败，请重试！');
            }
            $this->success();
        }
        $this->display();
    }

    /**
     * 获取评论标签
     */
    public function getTag(){
        $satisfy = I('request.satisfy',0,'intval');
        $list = D('CommentTag')->field('id,tag_name')->where(array('is_satisfy'=>$satisfy,'display'=>1))->order('sort')->select();
        $this->success(!empty($list) ? $list : array());
    }

    /**
     * 理发师查询
     */
    public function HaircutInfo(){
        $employee_id = I('request.employee_id','','trim');
        $data = array();
        if(!empty($employee_id)){
            $employee_name = D('Employee')->where(array('employee_id'=>$employee_id))->getField('employee_name');
            if(!empty($employee_name)){
                $data = array(
                    'employee_id'=>$employee_id,
                    'employee_name'=>$employee_name
                );
            }
        }
        $this->success($data);
    }

    /**
     * 默认方法暂时不开启
     */
    public function index(){
        send_http_status(404);
    }
}