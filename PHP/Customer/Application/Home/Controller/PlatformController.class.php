<?php

namespace Home\Controller;

/**
 * 平台控制器
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class PlatformController extends HomeBaseController
{

    /**
     * 平台录入
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function add()
    {
        if (IS_POST) {
            $post = I('post.');
            $res = D('Platform', 'Logic')->inputPlatform($post);
            json_echo($res['code'], $res['msg'], $res['retval']);
        }
        json_echo(C('FORBIDDEN'), '非法请求');
    }

    /**
     * 平台删除
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function delete()
    {
        $ids = I('post.ids', '');
        if (empty($ids)) {
            json_echo(C('BAD_REQUEST'), '参数有误');
        }
        $res = D('Platform', 'Logic')->deleteByIds($ids);
        json_echo($res['code'], $res['msg'], $res['retval']);
    }

    /**
     * 修改平台
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function editPlatform()
    {
        /*  $_POST=array(
         'id'=>117,
         'remark'=>'这是描述'
         ); */
        $this->isPost();
        try {
            $id = $_POST['id'];
            $remark = $_POST['remark'];
            json_echo(C('SUCCESS'), '修改成功', D('Platform', 'Logic')->editPlatform($id, $remark));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取平台
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月17日
     */
    public function getPlatform()
    {
        try {
            json_echo(C('SUCCESS'), '成功', D('Platform', 'Logic')->getPlatform($_GET));
        } catch (\Exception $e) {
            json_echo(C('FORBIDDEN'), '非法操作');
        }
    }
}
