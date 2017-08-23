<?php
/**
 * ====================================
 * 评论
 * ====================================
 * Author: 9009123
 * Date: 2017/08/21 15:38
 * ====================================
 * File: CommentModel.class.php
 * ====================================
 */
namespace Cpanel\Model;
use Common\Model\CpanelModel;

class CommentModel extends CpanelModel {
    public function filter($params) {
        $where = array();

        $params['employee_id'] = trim($params['employee_id']);
        if(isset($params['employee_id']) && !empty($params['employee_id'])){
            $where['employee_id'] = $params['employee_id'];
        }
        $params['employee_name'] = trim($params['employee_name']);
        if(isset($params['tag_id']) && !empty($params['employee_name'])){
            $where['employee_name'] = $params['employee_name'];
        }
        if(isset($params['is_satisfy']) && intval($params['is_satisfy']) >= 0){
            $where['is_satisfy'] = $params['is_satisfy'];
        }
        if(isset($params['tag_id']) && intval($params['tag_id']) > 0){
            $where['_string'] = isset($where['_string']) ? $where['_string'] . " AND FIND_IN_SET('".$params['tag_id']."', tag_ids)" : "FIND_IN_SET('".$params['tag_id']."', tag_ids)";
        }
        if(!empty($params['create_time_start'])){
            $create_time_start = strtotime($params['create_time_start']);
            $where['_string'] = isset($where['_string']) ? $where['_string'] . " AND create_time >= '".$create_time_start."'" : "create_time >= '".$create_time_start."'";
        }
        if(!empty($params['create_time_end'])){
            $create_time_end = strtotime($params['create_time_end']);
            $where['_string'] = isset($where['_string']) ? $where['_string'] . " AND create_time <= '".$create_time_end."'" : "create_time <= '".$create_time_end."'";
        }
        $this->where($where);
        return $this;
    }

    /**
     * easyui分页处理
     * @param array $params
     * @param bool $no_page
     * @return array
     */
    public function grid($params = array(), $no_page = false) {
        $orderBy = isset($params['sort']) ? trim($params['sort']) . ' ' .  trim($params['order']) : '';
        $page = isset($params['page']) && $params['page'] > 0 ? intval($params['page']) : 1;
        $pageSize = isset($params['rows']) && $params['rows'] > 0 ? intval($params['rows']) : ($no_page ? 0 : 10);

        //统计总记录数
        $options = $this->options;
        $total = $this->count();

        //排序并获取分页记录
        $options['order'] = empty($options['order']) ? $orderBy : $options['order'];
        $this->options = $options;
        if($pageSize > 0){
            $this->limit($pageSize)->page($page);
        }
        $rows = $this->getAll();
        $rows = $this->info($rows);
        return array('total' => (int)$total, 'rows' => (empty($rows) ? false : $rows), 'pagecount' => ceil($total / $pageSize));
    }

    /**
     * 处理评论详情
     * @param array $rows
     * @return array
     */
    public function info($rows = array()){
        if(!empty($rows)){
            $CommentTagModel = D('CommentTag');
            foreach($rows as $key=>$value){
                $value['tag_names'] = array();
                if(!empty($value['tag_ids'])){
                    $tag_list = $CommentTagModel->field('tag_name')->where(array('id'=>array('IN',$value['tag_ids'])))->select();
                    if(!empty($tag_list)){
                        foreach($tag_list as $k=>$val){
                            $value['tag_names'][] = $val['tag_name'];
                        }
                    }
                }
                $value['tag_names'] = !empty($value['tag_names']) ? implode(' , ',$value['tag_names']) : '';
                $rows[$key] = $value;
            }
        }
        return $rows;
    }
}