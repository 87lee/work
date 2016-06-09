<?php

namespace Home\Controller;

/**
 * VendorID控制器
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月16日
 * @version   1.0
 */
class VendoridController extends HomeBaseController
{

    /**
     * VendorID录入
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function add()
    {
        if (IS_POST) {
            $post = I('post.');
            $res = D('Vendorid', 'Logic')->inputVendorid($post);
            json_echo($res['code'], $res['msg'], $res['retval']);
        }
        json_echo(C('FORBIDDEN'), '非法请求');
    }

    /**
     * VendorID删除
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月16日
     */
    public function delete()
    {
        $ids = I('post.ids', '', 'trim');
        if (empty($ids)) {
            json_echo(C('BAD_REQUEST'), '参数有误');
        }
        $res = D('Vendorid', 'Logic')->deleteByIds($ids);
        json_echo($res['code'], $res['msg'], $res['retval']);
    }

    /**
     * 获取Vendorid
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月17日
     */
    public function getVendorid()
    {
        $get = $_GET;
        try {
            json_echo(C('SUCCESS'), '成功', D('Vendorid', 'Logic')->getVendorid($get));
        } catch (\Exception $e) {
            json_echo(C('FORBIDDEN'), '非法操作');
        }
    }

    /**
     * 修改Vendorid
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function editVendorid()
    {
        /* $_POST=array(
         'id'=>4,
         'remark'=>'这是描述99'
         ); */
        $this->isPost();
        try {
            $id = $_POST['id'];
            $remark = $_POST['remark'];
            json_echo(C('SUCCESS'), '修改成功', D('Vendorid', 'Logic')->editVendorid($id, $remark));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
}
