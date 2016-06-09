<?php

namespace Home\Controller;

use Think\Exception;

/**
 * 获取菜单节点
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月7日
 * @version   1.0
 */
class MenuController extends HomeBaseController
{

    /**
     * 获取一级导航菜单节点
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月7日
     */
    public function getMenu()
    {
        try {
            $uid = I('get.uid', 0, 'intval');
            if (empty($uid)) {
                throw new Exception('参数有误', '400');
            }
            
            //获取规则字符串
            $rulestr = M('AuthGroupAccess')->alias('aga')
                ->join('LEFT JOIN tb_auth_group ag ON aga.group_id=ag.id')
                ->field('ag.rules')
                ->where(array('aga.uid' => (int) $uid, 'ag.status' => 1))
                ->select();
            $ruleIds = array();
            if (! empty($rulestr)) {
                foreach ($rulestr as $v) {
                    $ruleIds = array_merge($ruleIds, explode(',', trim($v['rules'], ',')));
                }
            }
            
            //获取节点描述
            $rule = M('AuthRule')->where(array('id' => array('in', $ruleIds), 'status' => 1, 'pid' => 0, 'auth_type' => array('neq', 2)))
                ->order('sort desc')
                ->select();
            //配置左边导航节点
            $leftMenu = array('app/version', 'text/perform', 'report/monthReport');
            if (! empty($rule)) {
                foreach ($rule as $k => $v2) {
                    $rule[$k]['position'] = in_array(trim($v2['name']), $leftMenu) ? 'left' : 'head';
                }
            }
            json_echo('200', '成功', $rule);
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取二级菜单节点
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月8日
     */
    public function getSecondMenu()
    {
        try {
            $uid = I('get.uid', 0, 'intval');
            $pid = I('get.pid');
            if (empty($uid) || empty($pid)) {
                throw new Exception('参数有误', '400');
            }
            if (is_int($pid)) {
                $pid = (int) $pid;
            } else {
                //特殊处理 
                $special = array(
                    'user/admin' => array('user/admin', 'user/adminLog', 'user/permissions', 'user/roleManager'), 
                    'text/perform' => array('text/perform', 'text/useCases', 'text/item'));
                foreach ($special as $k => $v) {
                    if (in_array($pid, $v)) {
                        $pid = $k;
                        break;
                    }
                }
                
                $pid = M('AuthRule')->where(array('name' => trim($pid), 'level' => 1))->getField('id');
            }
            
            //获取规则字符串
            $rulestr = M('AuthGroupAccess')->alias('aga')
                ->join('LEFT JOIN tb_auth_group ag ON aga.group_id=ag.id')
                ->field('ag.rules')
                ->where(array('aga.uid' => (int) $uid, 'ag.status' => 1))
                ->select();
            $ruleIds = array();
            if (! empty($rulestr)) {
                foreach ($rulestr as $v) {
                    $ruleIds = array_merge($ruleIds, explode(',', trim($v['rules'], ',')));
                }
            }
            if (empty($ruleIds) || ! in_array($pid, $ruleIds)) {
                json_echo(C('INVALID_ACCESS'), L('_VALID_ACCESS_'));
            }
            
            //获取节点描述
            $where = array('ar.id' => array('in', $ruleIds), 'ar.status' => 1, 'ar.auth_type' => array('neq', 2));
            if (! $pid) {
                $where['ar2.pid'] = array('egt', 0);
            } else {
                $where['_complex'] = array('ar.pid' => $pid, 'ar2.pid' => $pid, '_logic' => 'OR');
            }
            $rule = M('AuthRule')->alias('ar')
                ->join('LEFT JOIN tb_auth_rule ar2 ON ar.pid=ar2.id')
                ->field('ar.*,ar2.pid AS ppid,ar2.name AS pid_name')
                ->where($where)
                ->order('ar.sort desc')
                ->select();
            
            //一级菜单 ppid=null    二级菜单ppid=0   三级菜单 ppid>0
            if (! empty($rule)) {
                $secondMenu = array();
                foreach ($rule as $k => $v) {
                    if ($v['ppid'] == 0) {
                        $secondMenu[$v['name']] = array_merge($v, ! isset($secondMenu[$v['name']]) ? array() : $secondMenu[$v['name']]);
                        //$secondMenu[$v['id']] = array_merge($v, ! isset($secondMenu[$v['id']]) ? array() : $secondMenu[$v['id']]);
                    } else {
                        ! isset($secondMenu[$v['pid_name']]) && $secondMenu[$v['pid_name']] = array();
                        $secondMenu[$v['pid_name']]['children'][] = $v;
                        //$secondMenu[$v['pid']]['children'][] = $v;
                    }
                }
                $sortArr = array_column($secondMenu, 'sort');
                array_multisort($sortArr, SORT_DESC, SORT_NUMERIC, $secondMenu);
                json_echo(200, '成功', $secondMenu);
            } else {
                json_echo(200, '没有数据');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
}
