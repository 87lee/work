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
        G('begin');
        //判断是否登陆和登录过期
        $this->isLogin();
        
        //白名单
        $whiteList = array('Index/main', 'User/login');
        if (in_array(CONTROLLER_NAME . '/' . ACTION_NAME, $whiteList)) {
            return true;
        }
        
        //权限验证,权限节点开启的才需要验证
        $auth = new myAuth();
        $uid = session('customerIsLogin.id');
        $res = D('AuthRule')->where(array('name' => CONTROLLER_NAME . '/' . ACTION_NAME, 'status' => 1, 'level' => 3,'auth_type'=>array('gt',1)))->find();
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
        $whiteList = array('User/login', 'User/sendFindPwdEmail', 'User/userResetPwd');
        if (! in_array(CONTROLLER_NAME . '/' . ACTION_NAME, $whiteList) && empty(session('customerIsLogin'))) {
            json_echo(C('UNAUTHORIZED'), '未登录，拒绝访问');
        } else {
            //判断是否超时，重新登录
            $this->isExpired();
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
        $whiteList = array('User/login', 'User/sendFindPwdEmail', 'User/userResetPwd');
        if (empty(cookie('last_action_time')) && ! in_array(CONTROLLER_NAME . '/' . ACTION_NAME, $whiteList)) {
            session('customerIsLogin', null);
            json_echo(C('LOGIN_EXPIRED'), '登录超时，请重新登录');
        } else {
            cookie('last_action_time', time(), C('NO_ACTION_TIME'));
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
