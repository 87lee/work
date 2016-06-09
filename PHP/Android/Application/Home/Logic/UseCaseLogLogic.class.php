<?php

namespace Home\Logic;

use Think\Model;

/**
 * 用例日志记录逻辑
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月27日
 * @version   1.0
 */
class UseCaseLogLogic extends Model
{

    private static $attributeLabel = array(
        'project_id' => '项目id', 
        'number' => '用例编号', 
        'pre_condition' => '前置条件', 
        'module' => '功能模块', 
        'steps' => '操作步骤', 
        'expect_result' => '预期结果', 
        'real_result' => '实际结果', 
        'status' => '执行状态', 
        'remark' => '备注');

    /**
     * 记录测试用例操作日志记录
     * 
     * 
     * @param unknown $id
     * @param unknown $data
     * @return multitype:string 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月5日
     */
    public function recordLog($id, $data, $type = false)
    {
        $useCaseLogMod = D('UseCaseLog');
        $attribute = self::$attributeLabel;
        $logData = array();
        switch ($type) {
            case $useCaseLogMod::IS_ADD:
                //新增
                foreach ($data as $k => $v) {
                    if (isset($attribute[$k])) {
                        $logContent .= $attribute[$k] . '为"' . $v . '"' . ",\r\n";
                    }
                }
                
                ! empty($logContent) && $logData[] = array(
                    'use_case_id' => $id, 
                    'type' => $useCaseLogMod::IS_ADD, 
                    'content' => '【新增】' . $logContent, 
                    'admin_user' => session('androidIsLogin.user'), 
                    'admin_id' => session('androidIsLogin.id'), 
                    'log_time' => time());
                
                break;
            case $useCaseLogMod::IS_UPDATE:
                //更新
                $useCaseLogMod = D('UseCaseLog');
                $attribute = self::$attributeLabel;
                $logContent = '';
                $oldData = M('UseCase')->field('number,pre_condition,module,steps,expect_result,real_result,status,remark')->find($id);
                
                foreach ($data as $k => $v) {
                    if (isset($oldData[$k]) && $oldData[$k] != $v) {
                        $logContent .= $attribute[$k] . ':由"' . $oldData[$k] . '"修改为"' . $v . '"' . "\r\n";
                    }
                }
                
                ! empty($logContent) && $logData[] = array(
                    'use_case_id' => $id, 
                    'type' => $useCaseLogMod::IS_UPDATE, 
                    'content' => '【修改】' . $logContent, 
                    'admin_user' => session('androidIsLogin.user'), 
                    'admin_id' => session('androidIsLogin.id'), 
                    'log_time' => time());
                break;
            case $useCaseLogMod::IS_DELETE:
                //删除
                $ids = is_array($id) ? $id : array($id);
                $list = M('UseCase')->field('id,project_id,number')
                    ->where(array('id' => array('IN', $ids)))
                    ->select();
                if (! empty($list)) {
                    foreach ($list as $k => $v) {
                        $logData[] = array(
                            'use_case_id' => $v['id'], 
                            'type' => $useCaseLogMod::IS_DELETE, 
                            'content' => '【删除】' . '项目id:' . $v['project_id'] . ',  用例编号:' . $v['number'] . "\r\n", 
                            'admin_user' => session('androidIsLogin.user'), 
                            'admin_id' => session('androidIsLogin.id'), 
                            'log_time' => time());
                    }
                }
                
                break;
            case $useCaseLogMod::IS_RESET:
                if (is_array($id)) {
                    //根据id重置
                    $msg = '【重置】' . '用例id如下：' . implode(',', $id);
                } else {
                    //根据项目id重置
                    $msg = '【重置】' . '项目id如下：' . $id;
                }
                if (! empty($id)) {
                    $logData[] = array(
                        'type' => $useCaseLogMod::IS_RESET, 
                        'content' => $msg . "\r\n", 
                        'admin_user' => session('androidIsLogin.user'), 
                        'admin_id' => session('androidIsLogin.id'), 
                        'log_time' => time());
                }
                break;
            default:
                break;
        }
        
        ! empty($logData) && $useCaseLogMod->addAll($logData);
    }
}