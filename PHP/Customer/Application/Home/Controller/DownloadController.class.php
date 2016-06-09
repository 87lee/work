<?php

namespace Home\Controller;

use Think\Exception;

/**
 * 文件下载
 *
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月1日
 * @version   1.0
 */
class DownloadController extends HomeBaseController
{

    private static $rootPathSetting = array('1' => '../../download/Upload/Question/');

    /**
     * 下载
     *
     *
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月1日
     */
    public function download()
    {
        //获取类型和下载url
        $type = I('get.type', 1, 'intval');
        $url = I('get.url', '', 'trim');

        if (empty(self::$rootPathSetting[$type])) {
            json_echo(C('FORBIDDEN'), '非法下载');
        } else {
            $rootPath = self::$rootPathSetting[$type];
        }

        try {
            //检测本地是否有文件，有则本地下载、没有远程下载
            if (file_exists($rootPath . $url)) {
                $method = 'downloadFromLocal';
                $url = $rootPath . $url;
            } else {
                $method = 'downloadFromOss';
                $url = C('DOWNLOAD_APK_PREFIX_ADDR') . $url;
            }
            if (method_exists($this, $method)) {
                call_user_func_array(array($this, $method), array($url));
            } else {
                throw new Exception('参数有误', C('BAD_REQUEST'));
            }
        } catch (\Exception $e) {
            $this->getException($e);
        }
    }

    /**
     * 下载本地文件
     *
     *
     * @param unknown $url
     * @throws Exception
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月29日
     */
    public function downloadFromLocal($url)
    {
        if (empty($url)) {
            throw new Exception('参数有误', C('BAD_REQUEST'));
        } else {
            //问题单附件下载
            \Org\Net\Http::download($url);
        }
    }

    /**
     * oss存储下载文件
     *
     *
     * @param unknown $url
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月29日
     */
    public function downloadFromOss($url)
    {
        if (empty($url)) {
            throw new Exception('参数有误', C('BAD_REQUEST'));
        } else {
            header("Content-Type: application/force-download");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . basename($url));
            readfile($url);
        }
    }
}