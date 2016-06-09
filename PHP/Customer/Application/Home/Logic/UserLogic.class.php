<?php

namespace Home\Logic;

use Think\Model;

/**
 * 用户逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class UserLogic extends Model
{

    static $findPwdCallBackUrl = 'http://192.168.1.199:180/Pages/customService/updatePwd.html';

    /**
     * 根据用户权限获取列表
     * 
     * 
     * @param unknown $type
     * @param string $fields
     * @param string $name
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月30日
     */
    public function getUserByPermission($type, $name = '', $fields = true)
    {
        if (empty($type)) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        } else {
            switch ($type) {
                //排除超级管理员和客户
                case 'notCustomer':
                    $where = array('aga.group_id' => array('notin', array(1, 5)));
                    break;
                case 'customer':
                    $where = array('aga.group_id' => 5);
                    break;
                default:
                    $where = array('aga.group_id' => 5);
                    break;
            }
            ! empty($name) && $where['u.name'] = array('LIKE', '%' . $name . '%');
            $where['u.status'] = 1;
            return M('User')->alias('u')
                ->join('INNER JOIN tb_auth_group_access aga ON u.id=aga.uid')
                ->field($fields)
                ->where($where)
                ->select();
        }
    }

    /**
     * 发送找回密码邮件
     * 
     * 
     * @param unknown $email
     * @throws \LogicException
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月31日
     */
    public function sendFindPwdEmail($email)
    {
        if (empty($email)) {
            throw new \LogicException('邮箱不能为空', C('BAD_REQUEST'));
        }
        
        $user = M('User')->where(array('email' => $email))->find();
        if (empty($user)) {
            throw new \LogicException('邮箱账号不存在', C('BAD_REQUEST'));
        }
        
        //5分钟邮件频繁发送检测
        $cacheKey = md5($user['id'] . 'sendFindPwdEmail' . $email);
        $cache = S($cacheKey);
        if ($cache) {
            throw new \LogicException('邮件已发送，请勿频繁发送', C('BAD_REQUEST'));
        }
        
        //当前时间戳+用户id+16未随机数字+邮箱 ,然后base64，反转字符串
        $randStr = rand_code(16);
        $key = strrev(base64_encode(time() . $randStr . $user['id']));
        $url = self::$findPwdCallBackUrl . '?key=' . strtoupper(md5($randStr)) . $key;
        $data = array('content' => array('email' => $email, 'url' => $url, 'expire' => C('FIND_PASSWORD_EMAIL_EXPIRE') / 60));
        $parseArr = parse_email_template('userFindPwdEmail', $data);
        import('Vendor.PHPMailer.Mail');
        $res = \Mail::getInstance(C('MAIL_CONFIG'))->send($email, $parseArr['subject'], $parseArr['content']);
        
        //邮件发送成功，写入缓存
        if (true === $res) {
            S($cacheKey, true, 300);
        }
        return $res;
    }

    /**
     * 忘记密码重置密码
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return Ambigous <boolean, unknown>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月31日
     */
    public function userResetPwdByEmail(array $data)
    {
        //解析获取前32位是随机字符串的md5加密字符串、申请重置密码时间、随机16位字符串，用户id
        $md5Str = substr($data['key'], 0, 32);
        $key = base64_decode(strrev(substr($data['key'], 32)));
        $resetTime = substr($key, 0, 10);
        $randStr = substr($key, 10, 16);
        $uid = substr($key, 26);
        
        if (empty($data['key']) || empty($data['pwd']) || empty($data['re_pwd'])) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        }
        if ($data['pwd'] !== $data['re_pwd']) {
            throw new \LogicException('确认密码不正确', C('BAD_REQUEST'));
        }
        if (strtoupper(md5($randStr)) != $md5Str) {
            throw new \LogicException('非法操作', C('FORBIDDEN'));
        }
        if (time() - $resetTime > C('FIND_PASSWORD_EMAIL_EXPIRE')) {
            throw new \LogicException('重置链接失效，请重新申请', C('BAD_REQUEST'));
        }
        
        return M('User')->where(array('id' => (int) $uid))->setField('passwd', md5($data['pwd']));
    }

    /**
     * 获取用户列表，支持分页和检索
     * 
     * 
     * @param unknown $data
     * @param unknown $groupId
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月12日
     */
    public function getUserList($data, $groupId)
    {
        $isPage = isset($data['page']) && isset($data['pageSize']);
        list ($search, $sort) = $this->getSortRulesAndSearch($data, $groupId);
        $count = $this->alias('u')
            ->join('INNER JOIN tb_auth_group_access aga ON u.id=aga.uid')
            ->where($search)
            ->count();
        if ($isPage) {
            $page = (int) $data['page'];
            $pageSize = (int) $data['pageSize'];
            $list = $this->alias('u')
                ->join('INNER JOIN tb_auth_group_access aga ON u.id=aga.uid')
                ->field('u.id,u.user,u.name,u.email,aga.group_id')
                ->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $this->alias('u')
                ->join('INNER JOIN tb_auth_group_access aga ON u.id=aga.uid')
                ->field('u.id,u.user,u.name,u.email,aga.group_id')
                ->where($search)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取客户列表检索和排序规则
     * 
     * 
     * @param unknown $data
     * @param unknown $groupId
     * @return multitype:string multitype:number multitype:string   
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月12日
     */
    public function getSortRulesAndSearch($data, $groupId)
    {
        $where = array('u.status' => 1);
        //这里可以后续拓展
        if (! empty($groupId)) {
            $where['aga.group_id'] = $groupId;
        }
        
        $sort = 'u.id DESC';
        if (! empty($data)) {
            $map = array('name', 'id');
            //获取排序规则
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $map) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = 'u.' . $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['u.name'] = array('like', '%' . $data['name'] . '%');
        }
        return array($where, $sort);
    }
}
