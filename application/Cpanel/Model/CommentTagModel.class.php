<?php
/**
 * ====================================
 * 评论标签
 * ====================================
 * Author: 9009123
 * Date: 2017/08/21 15:38
 * ====================================
 * File: CommentTagModel.class.php
 * ====================================
 */
namespace Cpanel\Model;
use Common\Model\CpanelModel;

class CommentTagModel extends CpanelModel {
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected $_validate = array(
        array('tag_name','require','{%TAG_NAME_LOST}'),
    );

    public function filter($params) {
        $where = array();
        if($params['tag_name']) {
            $where['tag_name'] = array('like', "%{$params['tag_name']}%");
        }
        if(isset($params['display']) && intval($params['display']) >= 0){
            $where['display'] = $params['display'];
        }
        if(isset($params['is_client']) && intval($params['is_client']) >= 0){
            $where['is_client'] = $params['is_client'];
        }
        if(isset($params['is_satisfy']) && intval($params['is_satisfy']) >= 0){
            $where['is_satisfy'] = $params['is_satisfy'];
        }
        $this->where($where);
        return $this;
    }
}