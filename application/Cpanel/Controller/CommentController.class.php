<?php
/**
 * ====================================
 * 评论管理
 * ====================================
 * Author: 9009123
 * Date: 2017/08/21 15:37
 * ====================================
 * File: CommentController.class.php
 * ====================================
 */
namespace Cpanel\Controller;
use Common\Controller\CpanelController;

class CommentController extends CpanelController {
    protected $tableName = 'Comment';

    /**
     * 导出报表
     */
    public function doExport(){
        $params = I('post.');
        if(!isset($params['create_time_start']) || empty($params['create_time_start'])){
            $params['create_time_start'] = date('Y-m-d 00:00:00');
        }
        if(!isset($params['create_time_end']) || empty($params['create_time_end'])){
            $params['create_time_end'] = date('Y-m-d 23:59:59');
        }
        $list = $this->dbModel->filter($params)->grid($params, true);
        $title = array(
            '员工工号',
            '员工名称',
            '是否满意',
            '评价标签',
            '评价时间',
        );
        $data = array();
        if(!empty($list['rows'])){
            foreach($list['rows'] as $value){
                $data[] = array(
                    $value['employee_id'],
                    $value['employee_name'],
                    ($value['is_satisfy']==1 ? '满意' : '不满意'),
                    $value['tag_names'],
                    $value['create_time'],
                );
            }
        }
        export_data(array('title'=>$title, 'data'=>$data));
    }
}