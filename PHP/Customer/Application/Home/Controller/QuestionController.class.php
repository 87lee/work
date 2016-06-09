<?php

namespace Home\Controller;

/**
 * 问题单
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月21日
 * @version   1.0
 */
class QuestionController extends HomeBaseController
{

    /**
     * 添加问题分类
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function addCategory()
    {
        //$_POST = ['cate_name' => '', 'parent_id' => 10, 'sort' => '5', 'if_show' => 1];
        $this->isPost();
        $isCommon = I('post.is_common', 0, 'intval');
        try {
            $logicInstance = $isCommon == 1 ? D('CommonQuestion', 'Logic') : D('Question', 'Logic');
            $cateId = $logicInstance->addCategory(I('post.'));
            if ($cateId !== false) {
                json_echo(C('SUCCESS'), '添加成功', array('id' => $cateId));
            }
            json_echo(C('UNKNOWN_ERROR'), '添加失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 删除分类
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function deleteCategory()
    {
        //          $_POST = ['is_common' => '0', 'ids' => [1,1000]];
        $this->isPost();
        $isCommon = I('post.is_common', 0, 'intval');
        try {
            $logicInstance = $isCommon == 1 ? D('CommonQuestion', 'Logic') : D('Question', 'Logic');
            $deleteResult = $logicInstance->deleteCategoryByIds(I('post.ids'));
            if ($deleteResult !== false) {
                json_echo(C('SUCCESS'), '删除成功');
            }
            json_echo(C('UNKNOWN_ERROR'), '删除失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取分类
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function getCategory()
    {
        /*  $_GET=[
         'is_common'=>1,
         'parent_id'=>2
         ]; */
        try {
            $logicInstance = I('get.is_common') == 1 ? D('CommonQuestion', 'Logic') : D('Question', 'Logic');
            $result = $logicInstance->getAllCategory($_GET);
            json_echo(C('SUCCESS'), '成功', $result);
        } catch (\Exception $e) {
            json_echo(C('FORBIDDEN'), '非法操作');
        }
    }

    /**
     * 添加常用问题单
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月21日
     */
    public function addCommonQuestion()
    {
        /* $_POST = [
         'cate_id_1' => '10', 
         'cate_id_2' => '12', 
         'content' => 'PHP程序员职业规划', 
         'reply' => '', 
         'ask_attach' => '我是附件', 
         'reply_attach' => '回复附件']; */
        $this->isPost();
        try {
            $fileList = empty($_FILES) ? array() : R('Upload/upload');
            $postData = array_merge($_POST, $fileList);
            $qId = D('CommonQuestion', 'Logic')->addQuestion($postData);
            if ($qId !== false) {
                json_echo(C('SUCCESS'), '添加成功', array('id' => $qId));
            }
            json_echo(C('UNKNOWN_ERROR'), '添加失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取常用问题单列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function getComQuestionList()
    {
        try {
            $result = D('CommonQuestion', 'Logic')->getQuestionList($_GET);
            json_echo(C('SUCCESS'), '成功', $result);
        } catch (\Exception $e) {
            json_echo(C('FORBIDDEN'), '非法操作');
        }
    }

    /**
     * 删除常用问题
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function deleteComQuestion()
    {
        //$_POST = ['is_common' => '0', 'ids' => [3, 4,5]];
        $this->isPost();
        try {
            $deleteResult = D('CommonQuestion', 'Logic')->deleteComQuestionByIds(I('post.ids'));
            if ($deleteResult !== false) {
                json_echo(C('SUCCESS'), '删除成功');
            }
            json_echo(C('UNKNOWN_ERROR'), '删除失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 修改常见问题
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function editComQuestion()
    {
        /* $_POST = [
         'id'=>'7',
         'cate_id_1' => '7',
         'cate_id_2' => '127',
         'content' => 'PHP程序员职业规划7',
         'reply' => '我是id为7的回复',
         'ask_attach' => ['http://www.baidu.com/1.php','http://ww.baidu.com/2.php'],
         'reply_attach' => ['http://www.baidu.com/3.php','http://ww.baidu.com/4.php']]; */
        $this->isPost();
        try {
            $fileList = empty($_FILES) ? array() : R('Upload/upload');
            $postData = array_merge($_POST, $fileList);
            $qId = D('CommonQuestion', 'Logic')->editQuestion($postData);
            if ($qId !== false) {
                json_echo(C('SUCCESS'), '修改成功');
            }
            json_echo(C('UNKNOWN_ERROR'), '修改失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 添加客户问题
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function addQuestion()
    {
        //$_POST = ['cate_id_1' => '10', 'content' => 'PHP程序员职业规划' . rand(1, 100), 'ask_attach' => ['url1', 'url2']];
        $this->isPost();
        try {
            $fileList = empty($_FILES) ? array() : R('Upload/upload');
            $postData = array_merge($_POST, $fileList);
            $qId = D('Question', 'Logic')->addQuestion($postData);
            if ($qId > 0) {
                json_echo(C('SUCCESS'), '创建成功', array('id' => $qId));
            }
            json_echo(C('UNKNOWN_ERROR'), '创建失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取客户问题单列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function getQuestionList()
    {
        try {
            $result = D('Question', 'Logic')->getQuestionList($_GET);
            json_echo(C('SUCCESS'), '成功', $result);
        } catch (\Exception $e) {
            json_echo(C('FORBIDDEN'), '非法操作');
        }
    }

    /**
     * 删除客户问题单
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function deleteQuestion()
    {
        //$_POST = ['is_common' => '0', 'ids' => [1]];
        $this->isPost();
        try {
            $deleteResult = D('Question', 'Logic')->deleteQuestionByIds(I('post.ids'));
            if ($deleteResult !== false) {
                json_echo(C('SUCCESS'), '删除成功');
            }
            json_echo(C('UNKNOWN_ERROR'), '删除失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 客户问题单指派
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月23日
     */
    public function assignQuestion()
    {
        //$_POST = ['qids' => [4,5], 'user_id' => 0, 'cate_id' => ''];
        $this->isPost();
        try {
            $result = D('Question', 'Logic')->assignQuestion(I('post.'));
            if ($result !== false) {
                json_echo(C('SUCCESS'), '指派成功');
            }
            json_echo(C('UNKNOWN_ERROR'), '指派失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 客户问题单追加提问
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function askAppendQuestion()
    {
        //$_POST = ['q_id' => 17, 'content' => '我是追加内容', 'append_attach' => ['url1', 'url2']];
        $this->isPost();
        try {
            $fileList = empty($_FILES) ? array() : R('Upload/upload');
            $postData = array_merge($_POST, $fileList);
            $appendId = D('Question', 'Logic')->askAppendQuestion($postData);
            if ($appendId > 0) {
                json_echo(C('SUCCESS'), '追加提问成功', array('id' => $appendId));
            }
            json_echo(C('UNKNOWN_ERROR'), '追加提问失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 客服追加问题单回复
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function replyAppendQuestion()
    {
        //$_POST = ['q_id' => 17, 'content' => '我是追加回复', 'append_attach' => ['url1', 'url2']];
        $this->isPost();
        try {
            $fileList = empty($_FILES) ? array() : R('Upload/upload');
            $postData = array_merge($_POST, $fileList);
            $appendId = D('Question', 'Logic')->replyAppendQuestion($postData);
            if ($appendId > 0) {
                json_echo(C('SUCCESS'), '追加回复成功', array('id' => $appendId));
            }
            json_echo(C('UNKNOWN_ERROR'), '追加回复失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 客服回复问题单
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function replyQuestion()
    {
        //$_POST = ['q_id' => 17, 'reply' => '我是追加回复', 'reply_attach' => ['url1', 'url2']];
        $this->isPost();
        try {
            $fileList = empty($_FILES) ? array() : R('Upload/upload');
            $postData = array_merge($_POST, $fileList);
            $replyResult = D('Question', 'Logic')->replyQuestion($postData);
            if ($replyResult !== false) {
                json_echo(C('SUCCESS'), '回复成功');
            }
            json_echo(C('UNKNOWN_ERROR'), '回复失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 评价问题单
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月24日
     */
    public function questionComment()
    {
        /* $_POST=[
         'question_id'=>17,
         'score'=>1,
         'content'=>'评价内容'
         ]; */
        $this->isPost();
        try {
            $commetId = D('Question', 'Logic')->questionComment(I('post.'));
            if ($commetId > 0) {
                json_echo(C('SUCCESS'), '评价成功', array('c_id' => $commetId));
            }
            json_echo(C('UNKNOWN_ERROR'), '评价失败');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取问题单追问
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月30日
     */
    public function getAppend()
    {
        try {
            json_echo(C('SUCCESS'), '获取成功', D('Question', 'Logic')->getAppendByQids(I('get.id')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 修改常见问题单分类
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月30日
     */
    public function editComCategory()
    {
        /* $_POST=array(
            'cate_id'=>183,
            'cate_name'=>'无法播放183',
            'remark'=>'**************'
        ); */
        $this->isPost();
        try {
            json_echo(C('SUCCESS'), '修改成功', D('Question', 'Logic')->editCategory(I('post.cate_id', 0, 'intval'), I('post.'), true));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 修改客户问题单分类
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月30日
     */
    public function editCategory()
    {
        /* $_POST=array(
            'cate_id'=>132,
            'cate_name'=>'点播',
            'remark'=>'********'
        ); */
        $this->isPost();
        try {
            json_echo(C('SUCCESS'), '修改成功', D('Question', 'Logic')->editCategory(I('post.cate_id', 0, 'intval'), I('post.')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
}
