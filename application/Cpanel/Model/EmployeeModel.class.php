<?php
/**
 * ====================================
 * 菜单模型
 * ====================================
 * Author: Hugo
 * Date: 14-5-20 下午9:28
 * ====================================
 * File: MenuModel.class.php
 * ====================================
 */
namespace Cpanel\Model;
use Common\Model\CpanelModel;
use Common\Extend\Base\Common;

class EmployeeModel extends CpanelModel {
    protected $_validate = array(
        array('employee_name','require','{%employee_name_lost}'),
        array('employee_id', 'require', '{%employee_id_lost}', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('position_id', '_validatePid', '{%position_id_lost}', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
    );


    /**
     * 验证position ID是否为空
     * @param $pid
     * @return bool
     */
    protected function _validatePid($pid) {
        $res = M("position")->where(array("id"=>$pid))->count();
        if($res){return true;}
        return false;
    }
    /**
     * 读取树型数据
     * @return mixed
     */
    public function grid($params = array()) {
        $positionModel = M("position");
        $position_ids = $this->getField("position_id",true);
        $positions = $positionModel->where(array("id"=>array("in",$position_ids)))->getField("id,name",true);
       $data = $this->field("*, id AS tree_id")->order("position_id ASC")->getAll();
        if($data){
            foreach($data as $key => $row){
                $row['position_name'] = $positions[$row['position_id']];
                $data[$key] = $row;
            }
            Common::tree($data, $params['selected'], $params['type']);
        }
        return $data ? $data : array();
    }

}