<?php

namespace Home\Controller;

use Think\Controller;
use Home\Common\Lib\myAuth;

/**
 * Home模块基类
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class HomeBaseController extends Controller
{

    /**
     * 初始化
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    protected function _initialize()
    {
        //判断是否登陆和登录过期
        $this->isLogin();
        $this->isExpired();
        
        //白名单
        $whiteList = array('User/login');
        if (in_array(CONTROLLER_NAME . '/' . ACTION_NAME, $whiteList)) {
            return true;
        }
        
        //权限验证,权限节点开启的才需要验证
        $auth = new myAuth();
        $uid = session('androidIsLogin.id');
        $res = D('AuthRule')->where(array('name' => CONTROLLER_NAME . '/' . ACTION_NAME, 'status' => 1, 'auth_type' => array('gt', 1)))->find();
        if ($res && ! $auth->authCheck(CONTROLLER_NAME . '/' . ACTION_NAME, $uid)) {
            json_echo(C('INVALID_ACCESS'), L('_VALID_ACCESS_'));
        }
    }

    /**
     * 检测是否登陆
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月17日
     */
    protected function isLogin()
    {
        //白名单不需要验证登录
        $whiteList = array('User/login');
        if (! in_array(CONTROLLER_NAME . '/' . ACTION_NAME, $whiteList)) {
            if (empty(session('androidIsLogin'))) {
                //判断是否登陆
                result('login');
            } else {
                //判断账号是否已登陆
                $isLogin = session('androidIsLogin');
                $user = D('User')->getOneForID($isLogin['id']);
                if ($user) {
                    if ($user['loginIp'] != $isLogin['loginIp']) {
                        result('loginIp');
                    }
                }
            }
        }
    }

    /**
     * 判断用户是否长时间停留，重新登录
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月28日
     */
    protected function isExpired()
    {
        //判断是否长时间未操作
        $expiredKey = md5(session_id());
        $whiteList = array('User/login');
        if (! in_array(CONTROLLER_NAME . '/' . ACTION_NAME, $whiteList)) {
            if (empty(cookie($expiredKey))) {
                session('androidIsLogin', null);
                result('登录超时，请重新登录');
            } else {
                cookie($expiredKey, time(), C('NO_ACTION_TIME'));
            }
        }
    }

    /**
     * 判断是否post提交
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    protected function isPost()
    {
        ! IS_POST && json_echo(C('FORBIDDEN'), '请求方式有误');
    }

    /**
     * 获取异常信息
     * 
     * 
     * @param \Exception $e
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    protected function getException(\Exception $e)
    {
        json_echo($e->getCode(), $e->getMessage());
    }
}