<?php
/**
 * ====================================
 * 评论标签管理
 * ====================================
 * Author: 9009123
 * Date: 2017/08/21 15:37
 * ====================================
 * File: CommentTagController.class.php
 * ====================================
 */
namespace Cpanel\Controller;
use Common\Controller\CpanelController;

class CommentTagController extends CpanelController {
    protected $tableName = 'CommentTag';
    protected $allowAction = array(
        'select'
    );
    /**
     * 下拉菜单
     */
    public function select(){
        $no_default = I('get.no_default',0,'intval');
        $default_name = I('get.default_name','评论标签','trim');
        $list = $this->dbModel->field('id,tag_name as text')->order('display desc,sort')->select();
        $data = array();
        if($no_default <= 0){
            $data[] = array(
                'id'=>0,
                'text'=>$default_name,
            );
        }
        if(!empty($list)){
            $data = array_merge($data, $list);
        }
        $this->ajaxReturn($data);
    }
}