<?php

namespace Home\Controller;

use Think\Exception;

/**
 * 文件上传
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年3月22日
 * @version   1.0
 */
class UploadController extends HomeBaseController
{

    /**
     * 上传
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年3月22日
     */
    public function upload($channel = 'LOCAL', $type = 'QUESTION_ATTACH')
    {
        if (in_array(strtolower($channel), array('oss', 'local'))) {
            $method = 'uploadTo' . ucfirst($channel);
            if (method_exists($this, $method)) {
                if (IS_AJAX) {
                    header("Content-type: text/plain");
                    try {
                        echo json_encode(array('success' => true, 'retval' => $this->$method($type)), JSON_UNESCAPED_UNICODE);
                    } catch (\Exception $e) {
                        echo json_encode(array('success' => false, 'error' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
                    }
                    return;
                } else {
                    return $this->$method($type);
                }
            } else {
                throw new Exception('非法上传', 401);
            }
        } else {
            throw new Exception('非法上传', 401);
        }
    }

    /**
     * 上传至阿里云存储
     * 
     * 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    private function uploadToOss($type)
    {
        require_once '../Base/function/Form.class.php';
        require_once '../Base/function/OssUploadCheck.class.php';
        require_once '../Base/Ossupclass/OssBase.class.php';
        
        //生成oss数据格式
        $form = new \form();
        $formData = $form->getFormUploadFiles();
        //检测
        $config = empty(C($type . '.OSS')) ? array() : C($type . '.OSS');
        $check = new \OssUploadCheck($config);
        if ($check->checkFiles($formData) !== true) {
            throw new \LogicException($check->getErrors(), 400);
        }
        //oss上传
        $base = new \base(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_ENDPOINT, OSS_BUCKET);
        $files = array();
        foreach ($formData as $k => $v) {
            if(is_array($v[0])){
                $res = $base->uploadFile($v);
            }else{
                $res = $base->uploadFile($formData);
            }
            if (is_array($res)) {
                $files[$k] = array_column($res, 'oss');
            }
        }
        return $files;
    }

    /**
     * 上传文件的到本地
     * 
     * 
     * @throws \LogicException
     * @return string
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    private function uploadToLocal($type)
    {
        $config = empty(C($type . '.LOCAL')) ? array() : C($type . '.LOCAL');
        $upload = new \Think\Upload($config);
        
        // 上传文件
        $info = $upload->upload();
        if (! $info) {
            // 上传错误提示错误信息
            throw new \LogicException($upload->getError(), C('UNKNOWN_ERROR'));
        } else {
            // 上传成功 获取上传文件信息
            $fileList = $returnList = array();
            foreach ($info as $file) {
                $filePath = $file['savepath'] . $file['savename'];
                $returnList[$file['key']][] = $filePath;
                
                $fileList[] = array(
                    'belong' => strtolower($type), 
                    'path' => $filePath, 
                    'type' => $file['type'], 
                    'size' => $file['size'], 
                    'add_time' => time());
            }
            ! empty($fileList) && M('UploadFiles')->addAll($fileList);
            return $returnList;
        }
    }
}
