<?php

namespace Home\Model;

/**
 * 客户问题模型
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月21日
 * @version   1.0
 */
class QuestionModel extends \Think\Model
{

    /**
     * 未指派
     * @var unknown
     */
    const NOT_ASSIGN = 0;

    /**
     * 已指派
     * @var unknown
     */
    const HAS_ASSIGN = 1;

    /**
     * 已回复
     * @var unknown
     */
    const HAS_REPLY = 2;

    /**
     * 获取问题单状态
     * 
     * 
     * @param string $key
     * @return mixed
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function getStatus($key = 'NOT_ASSIGN')
    {
        return constant(get_class($this) . '::' . $key);
    }
}