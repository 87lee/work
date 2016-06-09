<?php

namespace Home\Logic;

use Think\Model;

/**
 * 客户问题单
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月22日
 * @version   1.0
 */
class QuestionLogic extends Model
{

    /**
     * 添加分类
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return int
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function addCategory(array $data)
    {
        if (empty($data['cate_name'])) {
            throw new \LogicException('分类名不能为空', C('BAD_REQUEST'));
        } else {
            $questionCateMod = M('QuestionCategory');
            //判断分类是否存在
            if ($questionCateMod->where(
                array('cate_name' => $data['cate_name'], 'parent_id' => isset($data['parent_id']) ? (int) $data['parent_id'] : 0))->find()) {
                throw new \LogicException('分类已经存在，请更改', C('BAD_REQUEST'));
            }
            
            $data['add_time'] = time();
            if (! $questionCateMod->create($data)) {
                throw new \LogicException('参数有误', C('BAD_REQUEST'));
            } else {
                return $questionCateMod->add();
            }
        }
    }

    /**
     * 根据id删除分类
     *
     *
     * @param array $ids
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function deleteCategoryByIds(array $ids)
    {
        if (! empty($ids)) {
            $ids = array_map('intval', $ids);
            $result = M('QuestionCategory')->where(array('cate_id' => array('in', $ids)))->delete();
            return array('code' => C('SUCCESS'), 'msg' => $result !== false ? '删除成功' : '删除失败', 'retval' => array());
        }
        return array('code' => C('BAD_REQUEST'), 'msg' => '参数有误', 'retval' => array());
    }

    /**
     * 根据id获取分类
     *
     *
     * @param array $param
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function getAllCategory(array $param)
    {
        $isPage = isset($param['page']) && isset($param['pageSize']);
        $page = (int) $param['page'];
        $pageSize = (int) $param['pageSize'];
        list ($search, $sort) = $this->getSortRulesAndSearch($param);
        
        $cateMod = M('QuestionCategory');
        $count = $cateMod->where($search)->count();
        if ($isPage) {
            $list = $cateMod->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $cateMod->where($search)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取分类搜索条件和排序规则
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    protected function getSortRulesAndSearch(array $data)
    {
        $where = array();
        $sort = 'cate_id DESC';
        if (! empty($data)) {
            //获取排序规则
            $sortFields = array('cate_name');
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $sortFields) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['cate_name'] = array('like', '%' . $data['name'] . '%');
            isset($data['parent_id']) && $where['parent_id'] = (int) $data['parent_id'];
        }
        return array($where, $sort);
    }

    /**
     * 新建客户问题单
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function addQuestion(array $data)
    {
        if (! empty($data)) {
            //判断问题提问内容是否为空
            if (empty($data['content'])) {
                throw new \LogicException('问题描述不能为空', C('BAD_REQUEST'));
            }
            
            //处理post数据
            $data['content'] = htmlspecialchars($data['content']);
            $data['asker_id'] = session('customerIsLogin.id');
            $data['ask_time'] = time();
            if (! empty($data['ask_attach'])) {
                $askAttachArr = array_filter($data['ask_attach']);
                if (! empty($askAttachArr)) {
                    $data['ask_attach'] = serialize($askAttachArr);
                }else{
                    $data['ask_attach']='';
                }
            }
            
            //保存
            $questionMod = M('Question');
            if (! $questionMod->create($data)) {
                throw new \LogicException('创建失败', C('INTERNAL_SERVER_ERROR'));
            }
            return $questionMod->add();
        }
        throw new \LogicException('请求参数有误', C('BAD_REQUEST'));
    }

    /**
     * 获取客户问题单列表
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function getQuestionList(array $data)
    {
        $isPage = isset($data['page']) && isset($data['pageSize']);
        $page = (int) $data['page'];
        $pageSize = (int) $data['pageSize'];
        list ($search, $sort) = $this->getQueSortRulesAndSearch($data);
        
        $questionMod = M('Question');
        $count = $questionMod->alias('q')
            ->join('LEFT JOIN tb_user u ON q.asker_id=u.id')
            ->where($search)
            ->count();
        if ($isPage) {
            $list = $questionMod->alias('q')
                ->join('LEFT JOIN tb_user u ON q.asker_id=u.id  LEFT JOIN tb_question_category qc ON qc.cate_id=q.cate_id_1')
                ->field('q.*,u.user AS asker_user,u.name AS asker_name,qc.cate_name')
                ->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $questionMod->alias('q')
                ->join('LEFT JOIN tb_user u ON q.asker_id=u.id LEFT JOIN tb_question_category qc ON qc.cate_id=q.cate_id_1')
                ->field('q.*,u.user AS asker_user,u.name AS asker_name,qc.cate_name')
                ->where($search)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        //获取提问者、回复者用户名和昵称
        if (! empty($list)) {
            $replyids = array_column($list, 'reply_id');
            $userlist = D('User')->getUserByIds($replyids, true);
        }
        
        //附件需要反序列化成数组
        if (! empty($list)) {
            foreach ($list as $k => &$v) {
                $v['ask_attach'] = unserialize($v['ask_attach']);
                $v['reply_attach'] = unserialize($v['reply_attach']);
                $v['reply_user'] = empty($userlist[$v['reply_id']]) ? '' : $userlist[$v['reply_id']]['user'];
                $v['reply_name'] = empty($userlist[$v['reply_id']]) ? '' : $userlist[$v['reply_id']]['name'];
            }
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取问题单搜索条件和排序规则
     *
     *
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function getQueSortRulesAndSearch(array $data)
    {
        $where = array();
        $sort = 'q.id DESC';
        if (! empty($data)) {
            //获取排序规则
            $sortFields = array('ask_time', 'reply_time');
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $sortFields) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = 'q.' . $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['_complex'] = array(
                'q.content' => array('like', '%' . $data['name'] . '%'), 
                'u.name' => array('like', '%' . $data['name'] . '%'), 
                '_logic' => 'OR');
            
            //0：待指派，1:已指派，待回复，2：已回复      【状态2属于已解决，其他属于未解决】
            if (isset($data['status'])) {
                if (strpos($data['status'], '-') === false) {
                    $where['q.status'] = (int) $data['status'];
                } else {
                    $where['q.status'] = array('in', explode('-', $data['status']));
                }
            }
            isset($data['asker_id']) && $where['q.asker_id'] = (int) $data['asker_id'];
            isset($data['reply_id']) && $where['q.reply_id'] = (int) $data['reply_id'];
            isset($data['assign_id']) && $where['q.assign_id'] = (int) $data['assign_id'];
            isset($data['id']) && $where['q.id'] = (int) $data['id'];
            //分类条件
            if (! empty($data['cate_id'])) {
                $catArr = explode('-', $data['cate_id']);
                if (! empty($catArr[1])) {
                    $where['q.cate_id_' . $catArr[0]] = $catArr[1];
                }
            }
        }
        return array($where, $sort);
    }

    /**
     * 根据id删除客户问题单
     * 
     * 
     * @param array $ids
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function deleteQuestionByIds(array $ids)
    {
        if (! empty($ids)) {
            //检测问题单是否属于该登录客户
            $ids = array_map('intval', $ids);
            $questionMod = M('Question');
            $where = array('id' => array('in', $ids), 'asker_id' => session('customerIsLogin.id'));
            $count = $questionMod->where($where)->count();
            if (count($ids) != $count) {
                throw new \LogicException('非法删除', C('FORBIDDEN'));
            }
            
            return $questionMod->where($where)->delete();
        }
        throw new \LogicException('参数有误', C('BAD_REQUEST'));
    }

    /**
     * 客户问题单指派
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function assignQuestion(array $data)
    {
        if (! empty($data)) {
            $qids = isset($data['qids']) ? $data['qids'] : array();
            $assignUid = isset($data['user_id']) ? (int) $data['user_id'] : 0;
            $cateId = isset($data['cate_id']) ? (int) $data['cate_id'] : 0;
            
            //判断是否选择问题单、指派人
            if (empty($qids)) {
                throw new \LogicException('请选择问题单', C('BAD_REQUEST'));
            }
            if (empty($assignUid)) {
                throw new \LogicException('请选择指派用户', C('BAD_REQUEST'));
            }
            
            //判断是否选择新分类
            $questionMod = D('Question');
            $updateInfo = array(
                'reply_id' => $assignUid, 
                'status' => $questionMod->getStatus('HAS_ASSIGN'), 
                'assign_id' => session('customerIsLogin.id'), 
                'assign_time' => time());
            ! empty($cateId) && $updateInfo['cate_id_1'] = $cateId;
            return $questionMod->where(array('id' => array('in', $qids)))->save($updateInfo);
        }
        throw new \LogicException('参数有误', C('BAD_REQUEST'));
    }

    /**
     * 客户问题单追问
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function askAppendQuestion(array $data)
    {
        if (! empty($data)) {
            //基础检测
            if (empty($data['q_id'])) {
                throw new \LogicException('请先选择问题单', C('BAD_REQUEST'));
            }
            if (empty($data['content'])) {
                throw new \LogicException('追加提问内容不能为空', C('BAD_REQUEST'));
            }
            
            $qId = (int) $data['q_id'];
            $questionMod = D('Question');
            $questionAppendMod = D('QuestionAppend');
            
            //判断问题单所属人
            $question = $questionMod->where(array('id' => $qId, 'asker_id' => session('customerIsLogin.id')))->find();
            if (empty($question)) {
                throw new \LogicException('只能追加自己的问题单', C('BAD_REQUEST'));
            }
            
            //重置问题状态
            if ($question['status'] == $questionMod->getStatus('HAS_REPLY')) {
                $questionMod->where(array('id' => $qId))->setField('status', $questionMod->getStatus('HAS_ASSIGN'));
            }
            
            //追加提问附件
            if (isset($data['append_attach'])) {
                $data['append_attach'] = array_filter($data['append_attach']);
            }
            
            //保存到追问表
            $appendData = array(
                'q_id' => $qId, 
                'content' => htmlspecialchars($data['content']), 
                'append_time' => time(), 
                'append_attach' => !empty($data['append_attach']) ? serialize($data['append_attach']) : '', 
                'type' => $questionAppendMod->getType('ASK_APPEND'));
            return $questionAppendMod->add($appendData);
        }
        throw new \LogicException('参数有误', C('BAD_REQUEST'));
    }

    /**
     * 客服问题单追加回复
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function replyAppendQuestion(array $data)
    {
        if (! empty($data)) {
            //基础检测
            if (empty($data['q_id'])) {
                throw new \LogicException('请先选择问题单', C('BAD_REQUEST'));
            }
            if (empty($data['content'])) {
                throw new \LogicException('追加回复内容不能为空', C('BAD_REQUEST'));
            }
            
            $qId = (int) $data['q_id'];
            $questionMod = D('Question');
            $questionAppendMod = D('QuestionAppend');
            
            //判断问题单指派人
            $question = $questionMod->where(array('id' => $qId, 'reply_id' => session('customerIsLogin.id')))->find();
            if (empty($question)) {
                throw new \LogicException('该问题单没有指派给你，拒绝追加回复', C('BAD_REQUEST'));
            } elseif ((int) $question['reply_time'] <= 0) {
                throw new \LogicException('该问题单还未回复，不能追加回复', C('BAD_REQUEST'));
            }
            
            //重置问题状态
            $questionMod->where(array('id' => $qId))->setField('status', $questionMod->getStatus('HAS_REPLY'));
            
            //追加提问附件
            if (isset($data['append_attach'])) {
                $data['append_attach'] = array_filter($data['append_attach']);
            }
            
            //保存到追问表
            $appendData = array(
                'q_id' => $qId, 
                'content' => htmlspecialchars($data['content']), 
                'append_time' => time(), 
                'append_attach' => !empty($data['append_attach']) ? serialize($data['append_attach']) : '', 
                'type' => $questionAppendMod->getType('REPLY_APPEND'));
            $appendId = $questionAppendMod->add($appendData);
            if ($appendId > 0) {
                //回复问题单成功，发送问题单回复邮件通知
                $askerInfo = M('User')->field('user,name,email')->find($question['asker_id']);
                $this->sendReplyEmail($askerInfo['email'], $askerInfo['name'], $question['content']);
                return $appendId;
            } else {
                throw new \LogicException('追加回复出错', C('UNKNOWN_ERROR'));
            }
        }
        throw new \LogicException('参数有误', C('BAD_REQUEST'));
    }

    /**
     * 客服回复问题单
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function replyQuestion(array $data)
    {
        if (! empty($data)) {
            //基础检测
            if (empty($data['q_id'])) {
                throw new \LogicException('请先选择问题单', C('BAD_REQUEST'));
            }
            if (empty($data['reply'])) {
                throw new \LogicException('回复内容不能为空', C('BAD_REQUEST'));
            }
            
            $qId = (int) $data['q_id'];
            $questionMod = D('Question');
            //判断问题单指派人
            $question = $questionMod->where(array('id' => $qId, 'reply_id' => session('customerIsLogin.id')))->find();
            if (empty($question)) {
                throw new \LogicException('该问题单没有指派给你，拒绝回复', C('BAD_REQUEST'));
            }
            if ($question['status'] == $questionMod->getStatus('HAS_REPLY')) {
                throw new \LogicException('该问题单已经回复，您可以追加回复', C('BAD_REQUEST'));
            }
            //回复附件
            if (isset($data['reply_attach'])) {
                $data['reply_attach'] = array_filter($data['reply_attach']);
            }
            
            //重置问题状态
            $replyData = array(
                'reply' => htmlspecialchars($data['reply']), 
                'reply_time' => time(), 
                'reply_attach' => !empty($data['reply_attach']) ? serialize($data['reply_attach']) : '', 
                'status' => $questionMod->getStatus('HAS_REPLY'));
            if (false !== $questionMod->where(array('id' => $qId))->save($replyData)) {
                //回复问题单成功，发送问题单回复邮件通知
                $askerInfo = M('User')->field('user,name,email')->find($question['asker_id']);
                $this->sendReplyEmail($askerInfo['email'], $askerInfo['name'], $question['content']);
                return true;
            } else {
                throw new \LogicException('回复问题单出错', C('UNKNOWN_ERROR'));
            }
            return;
        }
        throw new \LogicException('参数有误', C('BAD_REQUEST'));
    }

    /**
     * 发送客户问题单回复邮件通知
     * 
     * 
     * @param unknown $email  邮箱地址
     * @param unknown $nickName  提问者昵称
     * @param unknown $content
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月10日
     */
    protected function sendReplyEmail($email, $nickName, $content)
    {
        if (! empty($email) && ! empty($nickName) && ! empty($content)) {
            $data = array('content' => array('nickName' => $nickName, 'loginUrl' => C('LOGIN_URL'), 'askContent' => $content));
            $parseArr = parse_email_template('replyNotifyEmail', $data);
            $mail = array(
                'mail_to' => $email, 
                'subject' => htmlspecialchars($parseArr['subject']), 
                'body' => htmlspecialchars($parseArr['content']), 
                'type' => 'replyQuestion', 
                'add_time' => time(), 
                'priority' => C('QUESTION_REPLY_PRIORITY'));
            M('Mail')->add($mail);
        }
    }

    /**
     * 客户评价问题单
     * 
     * 
     * @param unknown $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function questionComment(array $data)
    {
        if (! empty($data)) {
            if (empty($data['question_id'])) {
                throw new \LogicException('请先选择问题单', C('BAD_REQUEST'));
            }
            if (empty($data['score']) || ! is_numeric($data['score'])) {
                throw new \LogicException('请选择评分', C('BAD_REQUEST'));
            }
            if (empty($data['content'])) {
                throw new \LogicException('评价内容不能为空', C('BAD_REQUEST'));
            }
            
            $questionCommetMod = M('QuestionComments');
            if ($questionCommetMod->where(array('question_id' => $data['question_id']))->find()) {
                throw new \LogicException('问题单已经评价', C('BAD_REQUEST'));
            }
            
            $commentData = array(
                'question_id' => (int) $data['question_id'], 
                'comment_id' => session('customerIsLogin.id'), 
                'score' => (int) $data['score'], 
                'content' => htmlspecialchars($data['content']), 
                'add_time' => time());
            return $questionCommetMod->add($commentData);
        }
        throw new \LogicException('参数有误', C('BAD_REQUEST'));
    }

    /**
     * 根据问题id获取追问
     * 
     * 
     * @param unknown $ids
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月30日
     */
    public function getAppendByQids($ids)
    {
        if (! empty($ids)) {
            $ids = is_array($ids) ? $ids : explode('-', $ids);
            $list = D('QuestionAppend')->getAppendByQuestionIds($ids);
            
            //获取提问者、回复者用户名和昵称
            if (! empty($list)) {
                $askids = array_column($list, 'asker_id');
                $replyids = array_column($list, 'reply_id');
                $uids = array_unique(array_merge($askids, $replyids));
                $userlist = D('User')->getUserByIds($uids, true);
            }
            $append = array();
            if (! empty($list)) {
                foreach ($list as $k => $l) {
                    //empty($append[$l['q_id']]) && $append[$l['q_id']] = array('ask' => array(), 'reply' => array());
                    empty($append[$l['q_id']]) && $append[$l['q_id']] = array();
                    
                    $l['append_attach'] = unserialize($l['append_attach']);
                    $l['asker_user'] = empty($userlist[$l['asker_id']]) ? '' : $userlist[$l['asker_id']]['user'];
                    $l['asker_name'] = empty($userlist[$l['asker_id']]) ? '' : $userlist[$l['asker_id']]['name'];
                    $l['reply_user'] = empty($userlist[$l['reply_id']]) ? '' : $userlist[$l['reply_id']]['user'];
                    $l['reply_name'] = empty($userlist[$l['reply_id']]) ? '' : $userlist[$l['reply_id']]['name'];
                    $append[$l['q_id']][] = $l;
                    /* if ($l['type'] == 1) {
                     $append[$l['q_id']]['ask'][] = $l;
                     } else {
                     $append[$l['q_id']]['reply'][] = $l;
                     } */
                }
            }
            return $append;
        }
        throw new \LogicException('参数有误', C('BAD_REQUEST'));
    }

    /**
     * 修改问题单分类
     * 
     * 
     * @param unknown $cid
     * @param unknown $data
     * @param string $isCom
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月30日
     */
    public function editCategory($cid, $data, $isCom = false)
    {
        if ($cid <= 0 || empty($data['cate_name'])) {
            throw new \LogicException('参数有误', C('BAD_REQUEST'));
        } else {
            $cateMod = $isCom ? M('CommonQuestionCategory') : M('QuestionCategory');
            $thisCate = $cateMod->where(array('cate_id' => $cid))->find();
            if (! $thisCate) {
                throw new \LogicException('分类不存在', C('BAD_REQUEST'));
            } else {
                $cateName = $data['cate_name'];
                if ($cateMod->where(
                    array('cate_name' => $cateName, 'parent_id' => $thisCate['parent_id'], 'cate_id' => array('neq', $thisCate['cate_id'])))->find()) {
                    throw new \LogicException('分类名已经存在，请更改', C('BAD_REQUEST'));
                }
                $editData = array('cate_name' => $cateName);
                isset($data['remark']) && $editData['remark'] = $data['remark'];
                return $cateMod->where(array('cate_id' => $cid))->save($editData);
            }
        }
    }
}
