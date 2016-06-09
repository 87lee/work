<?php

namespace Home\Common\Lib;

use Think\Auth;

/**
 * 权限验证类
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月5日
 * @version   1.0
 */
class myAuth extends Auth
{

    /**
     * 验证规则白名单,全部为小写字母
     * @var unknown
     */
    protected $whiteList = array('index/main', 'user/login');

    /**
     * 冲突接口配置
     * @var unknown
     */
    protected $conflictApi = array('User/modifyPasswd');

    /**
     * 检查权限
     * 
     * 
     * @param unknown $name
     * @param unknown $uid
     * @param number $type
     * @param string $mode
     * @param string $relation
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月5日
     */
    public function authCheck($name, $uid, $type = 1, $mode = 'url', $relation = 'or')
    {
        //是否开启验证
        if (! $this->_config['AUTH_ON']) {
            return true;
        }
        //验证兼容接口
        if (is_string($name) && in_array($name, $this->conflictApi)) {
            if ($this->fixApiAuthConflict($name)) {
                return true;
            }
        }
        
        if (is_string($name)) {
            $name = strtolower($name);
            $name = strpos($name, ',') !== false ? explode(',', $name) : array($name);
        }
        //检查是否属于白名单
        $isWhite = $this->inWhiteList($name, $relation);
        if ($isWhite) {
            return true;
        } else {
            return $this->check($name, $uid, $type, $mode, $relation);
        }
    }

    /**
     * 检查是否属于白名单规则
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月5日
     */
    public function inWhiteList(array $name, $relation = 'or')
    {
        if (! empty($this->whiteList)) {
            $interArr = array_intersect($name, $this->whiteList);
            if ($relation == 'or') {
                if (! empty($interArr)) {
                    return true;
                }
            } else {
                if (count($interArr) == count($name)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 兼容同一接口不同操作权限控制兼容问题
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月13日
     */
    public function fixApiAuthConflict($name)
    {
        switch ($name) {
            case 'User/modifyPasswd':
                $put = I('put.');
                if (! empty($put['newPasswd']) && ! empty($put['oldPasswd'])) {
                    //个人修改密码
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                return false;
                break;
        }
    }
}