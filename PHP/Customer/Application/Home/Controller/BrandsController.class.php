<?php

namespace Home\Controller;

/**
 * 品牌控制器
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月26日
 * @version   1.0
 */
class BrandsController extends HomeBaseController
{

    /**
     * 创建品牌
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function addBrand()
    {
        //$_POST = array('brand_name' => '品牌一2', 'customer' => 'admin','remark'=>'备注');
        $this->isPost();
        try {
            $brandId = D('Brands', 'Logic')->addBrand(I('post.'));
            if ($brandId > 0) {
                json_echo(C('SUCCESS'), '新增品牌成功', array('id' => $brandId));
            } else {
                json_echo(C('UNKNOWN_ERROR'), '新增品牌失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 删除品牌
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function delBrand()
    {
        //$_POST['ids'] = [1, 2, 3];
        $this->isPost();
        try {
            $res = D('Brands', 'Logic')->delBrandByIds(I('post.ids'));
            if (false !== $res) {
                json_echo(C('SUCCESS'), '删除成功');
            } else {
                json_echo(C('UNKNOWN_ERROR'), '删除失败');
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
    
    /**
     * 品牌修改
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月23日
     */
    public function editBrand()
    {
        /* $_POST=array(
         'id'=>4,
         'remark'=>'这是描述99'
        ); */
        $this->isPost();
        try {
            $id = $_POST['id'];
            $remark = $_POST['remark'];
            json_echo(C('SUCCESS'), '修改成功', D('Brands', 'Logic')->editBrand($id, $remark));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 获取品牌
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function getBrand()
    {
        try {
            json_echo(C('SUCCESS'), '成功', D('Brands', 'Logic')->getBrand(I('get.')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 根据客户用户名获取品牌
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月26日
     */
    public function getBrandByUser()
    {
        try {
            json_echo(C('SUCCESS'), '成功', D('Brands', 'Logic')->getBrandByUser(I('get.user')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
    
    /**
     * 根据品牌，查找品牌关联的固件所属的平台
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月12日
     */
    public function getPlatform(){
        try {
            json_echo(C('SUCCESS'), '成功', D('Brands', 'Logic')->getPlatform(I('get.')));
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }
}