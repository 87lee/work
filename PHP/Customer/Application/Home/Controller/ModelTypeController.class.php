<?php

namespace Home\Controller;

/**
 * 型号控制器
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年5月23日
 * @version   1.0
 */
class ModelTypeController extends HomeBaseController
{

    /**
     * 新增型号
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function addModelType()
    {
        //$_POST = array('type_name' => 'banxing252', 'remark' => '板型备注2');
        $this->isPost();
        try {
            json_echo(C('SUCCESS'), '新增成功', array('id' => D('ModelType', 'Logic')->addModelType(I('post.'))));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 删除型号
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function delModelType()
    {
        //$_POST = ['ids' => [1, 2]];
        $this->isPost();
        try {
            D('ModelType', 'Logic')->delModelTypeByIds(I('post.ids'));
            json_echo(C('SUCCESS'), '删除成功');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 修改型号
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function editModelType()
    {
        /* $_POST=[
         'id'=>2,
         'remark'=>'998型号备注'
         ]; */
        $this->isPost();
        try {
            D('ModelType', 'Logic')->editModelType($_POST['id'], $_POST['remark']);
            json_echo(C('SUCCESS'), '修改成功');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取型号列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function modelTypeList()
    {
        try {
            json_echo(C('SUCCESS'), '获取列表成功', D('ModelType', 'Logic')->modelTypeList(I('get.')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 型号下拉框
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function modelTypeOption()
    {
        try {
            json_echo(C('SUCCESS'), '获取列表成功', D('ModelType', 'Logic')->modelTypeOption(I('get.name')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
}