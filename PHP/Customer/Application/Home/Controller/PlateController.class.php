<?php

namespace Home\Controller;

/**
 * 板型控制器
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年5月23日
 * @version   1.0
 */
class PlateController extends HomeBaseController
{

    /**
     * 新增板型
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function addPlate()
    {
        //$_POST = array('plate_name' => 'banxing252', 'remark' => '板型备注2');
        $this->isPost();
        try {
            json_echo(C('SUCCESS'), '新增成功', array('id' => D('Plate', 'Logic')->addPlate(I('post.'))));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 删除板型
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function delPlate()
    {
        //$_POST = ['ids' => [1, 2]];
        $this->isPost();
        try {
            D('Plate', 'Logic')->delPlateByIds(I('post.ids'));
            json_echo(C('SUCCESS'), '删除成功');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 修改板型
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function editPlate()
    {
        /* $_POST=[
         'id'=>3,
         'remark'=>'998备注'
         ]; */
        $this->isPost();
        try {
            D('Plate', 'Logic')->editPlate($_POST['id'], $_POST['remark']);
            json_echo(C('SUCCESS'), '修改成功');
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取板型列表
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function plateList()
    {
        try {
            json_echo(C('SUCCESS'), '获取列表成功', D('Plate', 'Logic')->plateList(I('get.')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 板型下拉框
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function plateOption()
    {
        try {
            json_echo(C('SUCCESS'), '获取列表成功', D('Plate', 'Logic')->plateOption(I('get.name')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
}