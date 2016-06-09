<?php

namespace Home\Controller;

/**
 * 发布固件控制器
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class FirmwarePublishController extends HomeBaseController
{

    /**
     * 发布固件
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function publish()
    {
        if (IS_POST) {
            $post = I('post.');
            $res = D('FirmwarePublish', 'Logic')->publish($post);
            json_echo($res['code'], $res['msg'], $res['retval']);
        } else {
            json_echo(C('FORBIDDEN'), '非法请求');
        }
    }

    /**
     * 固件删除
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
        } else {
            $res = D('FirmwarePublish', 'Logic')->deleteByIds($ids);
            json_echo($res['code'], $res['msg'], $res['retval']);
        }
    }

    /**
     * 获取固件发布列表
     * get /Customer/Home/FirmwarePublish/getFirmwarePublish?id=x
     * get /Customer/Home/FirmwarePublish/getFirmwarePublish?page=x&pageSize=x
     * get /Customer/Home/FirmwarePublish/getFirmwarePublish/pform/a/firmv/1/sort/time-asc/page/1/pageSize/2
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function getFirmwarePublish()
    {
        $get = $_GET;
        try {
            json_echo(C('SUCCESS'), '成功', D('FirmwarePublish', 'Logic')->getFirmwarePublish($get));
        } catch (\Exception $e) {
            json_echo(C('UNKNOWN_ERROR'), '获取失败');
        }
    }

    /**
     * 固件评论
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月8日
     */
    public function firmComment()
    {
        //$_POST = ['firm_id' => 65, 'content' => '我是固件评论'];
        $this->isPost();
        try {
            D('FirmwarePublish', 'Logic')->firmComment(I('post.'));
            result();
        } catch (\Exception $e) {
            result($e->getMessage());
        }
    }

    /**
     * 获取固件评论列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月8日
     */
    public function getCommetList()
    {
        try {
            result(true, D('FirmwarePublish', 'Logic')->getCommetList(I('get.')));
        } catch (\Exception $e) {
            result($e->getMessage());
        }
    }

    /**
     * 删除评论
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月8日
     */
    public function delCommet()
    {
        $this->isPost();
        try {
            D('FirmwarePublish', 'Logic')->delCommetByIds(I('post.id'));
            result();
        } catch (\Exception $e) {
            result($e->getMessage());
        }
    }

    /**
     * 固件列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function firmwarelist()
    {
        $this->display();
    }

    /**
     * 客服栏测试页面
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function test()
    {
        $this->display('version');
    }
}
