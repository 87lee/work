<?php

namespace Home\Model;

/**
 * 问题单追加表
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月24日
 * @version   1.0
 */
class QuestionAppendModel extends \Think\Model
{

    /**
     * 追加提问
     * @var unknown
     */
    const ASK_APPEND = 1;

    /**
     * 追加回复
     * @var unknown
     */
    const REPLY_APPEND = 2;

    /**
     * 获取问题单状态
     * 
     * 
     * @param string $key
     * @return mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function getType($key = 'ASK_APPEND')
    {
        return constant(get_class($this) . '::' . $key);
    }

    /**
     * 根据问题id获取追问记录
     * 
     * 
     * @param array $qids
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月30日
     */
    public function getAppendByQuestionIds(array $qids)
    {
        return $this->alias('qa')->join('left join tb_question q ON qa.q_id=q.id')->field('qa.*,q.asker_id,q.reply_id')->where(array('qa.q_id' => array('in', $qids)))
            ->order('qa.append_time asc')
            ->select();
    }
}
