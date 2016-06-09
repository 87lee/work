<?php

namespace Home\Logic;

use Think\Model;

/**
 * 常见问题逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月21日
 * @version   1.0
 */
class CommonQuestionLogic extends Model
{

    /**
     * 添加分类
     * 
     * 
     * @param array $data
     * @throws \LogicException
     * @return int
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月21日
     */
    public function addCategory(array $data)
    {
        if (empty($data['cate_name'])) {
            throw new \LogicException('分类名不能为空', C('BAD_REQUEST'));
        } else {
            $questionCateMod = M('CommonQuestionCategory');
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
            $result = M('CommonQuestionCategory')->where(
                array('cate_id' => array('in', $ids), 'parent_id' => array('in', $ids), '_logic' => 'OR'))->delete();
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
        list ($search, $sort) = $this->getCateSortRulesAndSearch($param);
        
        $cateMod = D('CommonQuestionCategory');
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
        //获取上级菜单的节点信息
        if (! empty($list)) {
            $parentCateIds = array_unique(array_column($list, 'parent_id'));
            $parentCateInfo = $cateMod->getCateInfoByIds($parentCateIds);
            foreach ($list as $k => &$v) {
                $v['parent_cate_name'] = $v['parent_id'] == 0 ? '' : $parentCateInfo[$v['parent_id']]['cate_name'];
            }
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
    protected function getCateSortRulesAndSearch(array $data)
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
            if (! empty($data['level']) && in_array($data['level'], array(1, 2))) {
                $where['parent_id'] = $data['level'] == 1 ? 0 : array('gt', 0);
            }
        }
        return array($where, $sort);
    }

    /**
     * 添加常见问题
     * 
     * 
     * @param array $data
     * @param string $isEdit
     * @throws \LogicException
     * @return \Think\mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月21日
     */
    public function addQuestion(array $data, $isEdit = false)
    {
        if (! empty($data)) {
            $questionMod = M('CommonQuestion');
            
            //处理post数据
            ! empty($data['content']) && $data['content'] = htmlspecialchars($data['content']);
            ! empty($data['reply']) && $data['reply'] = htmlspecialchars($data['reply']);
            //附件数组过滤空字符串
            if (! empty($data['ask_attach'])) {
                $askAttachArr = array_filter($data['ask_attach']);
                if (! empty($askAttachArr)) {
                    $data['ask_attach'] = serialize($askAttachArr);
                } else {
                    $data['ask_attach'] = '';
                }
            }
            
            if (! empty($data['reply_attach'])) {
                $replyAttachArr = array_filter($data['reply_attach']);
                if (! empty($replyAttachArr)) {
                    $data['reply_attach'] = serialize($replyAttachArr);
                } else {
                    $data['reply_attach'] = '';
                }
            }
            $data['admin_id'] = session('customerIsLogin.id');
            ! $isEdit && $data['add_time'] = time();
            
            //数据验证
            $rules = array(
                array('cate_id_1', 'require', '一级分类不能为空'), 
                array('cate_id_2', 'require', '二级分类不能为空'), 
                array('content', 'require', '常见问题内容不能为空'), 
                array('reply', 'require', '常见回复内容不能为空'));
            if (! $questionMod->validate($rules)->create($data)) {
                throw new \LogicException($questionMod->getError(), C('BAD_REQUEST'));
            }
            
            //区分修改和添加
            if ($isEdit) {
                return $questionMod->save();
            } else {
                return $questionMod->add();
            }
        }
        throw new \LogicException('提交参数有误', C('BAD_REQUEST'));
    }

    /**
     * 获取常用问题单列表
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
        
        $comQuestionMod = M('CommonQuestion');
        $count = $comQuestionMod->alias('cq')
            ->join('LEFT JOIN tb_user u ON cq.admin_id=u.id')
            ->where($search)
            ->count();
        if ($isPage) {
            $list = $comQuestionMod->alias('cq')
                ->join('LEFT JOIN tb_user u ON cq.admin_id=u.id')
                ->field('cq.*,u.user AS admin_user,u.name AS admin_name')
                ->where($search)
                ->order($sort)
                ->limit(($page - 1) * $pageSize . ',' . $pageSize)
                ->select();
            $totalPage = ceil($count / $pageSize);
        } else {
            $list = $comQuestionMod->alias('cq')
                ->join('LEFT JOIN tb_user u ON cq.admin_id=u.id')
                ->field('cq.*,u.user AS admin_user,u.name AS admin_name')
                ->where($search)
                ->order($sort)
                ->select();
            $totalPage = $page = 1;
        }
        
        //附件需要反序列化成数组
        if (! empty($list)) {
            //获取分类名
            $cateId1 = array_column($list, 'cate_id_1');
            $cateId2 = array_column($list, 'cate_id_2');
            $cateId = array_merge($cateId1, $cateId2);
            $cateList = D('CommonQuestionCategory')->getCateInfoByIds($cateId);
            
            foreach ($list as $k => &$v) {
                $v['ask_attach'] = empty($v['ask_attach']) ? [] : unserialize($v['ask_attach']);
                $v['reply_attach'] = empty($v['reply_attach']) ? [] : unserialize($v['reply_attach']);
                $v['cate_name_1'] = isset($cateList[$v['cate_id_1']]) ? $cateList[$v['cate_id_1']]['cate_name'] : '';
                $v['cate_name_2'] = isset($cateList[$v['cate_id_2']]) ? $cateList[$v['cate_id_2']]['cate_name'] : '';
            }
        }
        return array('now_page' => $page, 'count' => (int) $count, 'total_page' => $totalPage, 'list' => $list);
    }

    /**
     * 获取常见问题单搜索条件和排序规则
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function getQueSortRulesAndSearch(array $data)
    {
        $where = array();
        $sort = 'cq.id DESC';
        if (! empty($data)) {
            //获取排序规则
            $sortFields = array('add_time');
            if (! empty($data['sort'])) {
                $sortArr = explode('-', $data['sort']);
                if (in_array($sortArr[0], $sortFields) && in_array($sortArr[1], array('desc', 'asc'))) {
                    $sort = 'cq.' . $sortArr[0] . ' ' . $sortArr[1];
                }
            }
            
            //获取搜索字段
            ! empty($data['name']) && $where['_complex'] = array(
                'cq.content' => array('like', '%' . $data['name'] . '%'), 
                'u.name' => array('like', '%' . $data['name'] . '%'), 
                '_logic' => 'OR');
            
            if (! empty($data['cate_id'])) {
                $catArr = explode('-', $data['cate_id']);
                if (! empty($catArr[1])) {
                    $where['cq.cate_id_' . $catArr[0]] = $catArr[1];
                }
            }
            isset($data['typical']) && $where['cq.typical'] = (int) $data['typical'];
            isset($data['id']) && $where['cq.id'] = (int) $data['id'];
        }
        return array($where, $sort);
    }

    /**
     * 根据id删除问题单
     * 
     * 
     * @param array $ids
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function deleteComQuestionByIds(array $ids)
    {
        if (! empty($ids)) {
            $ids = array_map('intval', $ids);
            return M('CommonQuestion')->where(array('id' => array('in', $ids)))->delete();
        }
        throw new \LogicException('参数有误', C('BAD_REQUEST'));
    }

    /**
     * 修改常见问题
     * 
     * 
     * @param array $data
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function editQuestion(array $data)
    {
        //判断是否有id
        if (empty($data['id'])) {
            throw new \LogicException('记录不存在', C('BAD_REQUEST'));
        }
        return $this->addQuestion($data, true);
    }
}
