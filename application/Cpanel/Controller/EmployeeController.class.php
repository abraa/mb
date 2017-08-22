<?php
/**
 * ====================================
 * 菜单管理
 * ====================================
 * Author: Hugo
 * Date: 14-5-20 下午9:58
 * ====================================
 * File: MenuController.class.php
 * ====================================
 */

namespace Cpanel\Controller;
use Common\Model\CpanelModel;
use Common\Extend\Base\Common;
use Common\Controller\CpanelController;

class EmployeeController extends CpanelController {
    protected $tableName = 'Employee';

    /**
     *  获取职业 id树结构 position ID
     */
    function getPositionIdTree(){
        $params = I('request.');
        $data = D("position")->field("*,id AS tree_id, name as text")->order("pid ASC")->getAll();
        if($data){
            foreach($data as $key => $row){
                $data[$key] = $row;
            }
            Common::tree($data, $params['selected'], $params['type']);
            $this->ajaxReturn($data);
            exit;
        }
    }
}