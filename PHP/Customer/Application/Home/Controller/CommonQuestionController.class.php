<?php

namespace Home\Controller;

/**
 * 常见问题单
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月21日
 * @version   1.0
 */
class CommonQuestionController extends HomeBaseController
{

    /**
     * 添加分类
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月21日
     */
    public function addCategory()
    {
        ! IS_POST && json_echo(C('FORBIDDEN'), '请求方式有误');
        try {
            $cateId = D('CommonQuestion', 'Logic')->addCategory(I('post.'));
            if ($cateId !== false) {
                json_echo(C('SUCCESS'), '添加成功', array('id' => $cateId));
            }
            json_echo(C('UNKNOWN_ERROR'), '添加失败');
        } catch (\Exception $e) {
            json_echo($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 添加常用问题单
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月21日
     */
    public function addQuestion()
    {
        /* $_POST = [
         'cate_id_1' => '10', 
         'cate_id_2' => '12', 
         'content' => 'PHP程序员职业规划', 
         'reply' => '我是常用问题回复', 
         'ask_attach' => '我是附件', 
         'reply_attach' => '回复附件']; */
        ! IS_POST && json_echo(C('FORBIDDEN'), '请求方式有误');
        try {
            $qId = D('CommonQuestion', 'Logic')->addQuestion(I('post.'));
            if ($qId !== false) {
                json_echo(C('SUCCESS'), '添加成功', array('id' => $qId));
            }
            json_echo(C('UNKNOWN_ERROR'), '添加失败');
        } catch (\Exception $e) {
            json_echo($e->getCode(), $e->getMessage());
        }
    }
}