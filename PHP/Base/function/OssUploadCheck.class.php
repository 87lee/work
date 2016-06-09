<?php

/**
 * OSS上传检测类
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月28日
 * @version   1.0
 */
class OssUploadCheck
{

    /**
     * 允许上传的文件大小、文件后缀、文件mime类型
     * @var unknown
     */
    private $config = array('maxSize' => 0, 'exts' => array(), 'mimes' => array());

    /**
     * 上传错误信息
     * @var unknown
     */
    private $errorMsg = '';

    /**
     * 构造函数
     * 
     * 
     * @param unknown $config
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 设置
     * 
     * 
     * @param unknown $name
     * @param unknown $value
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    public function __set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 获取
     * 
     * 
     * @param unknown $name
     * @return unknown
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    public function __get($name)
    {
        return $this->config[$name];
    }

    /**
     * 检测oss上传的form数据
     * 
     * 
     * @param unknown $files
     * @return boolean|Ambigous <boolean, unknown>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    public function checkFiles($files)
    {
        if (empty($files)) {
            $this->errorMsg = '没有上传文件数据';
            return false;
        } else {
            foreach ($files as $k => $file) {
                if (is_array($file[0])) {
                    //同一个key多个文件上传
                    $len = count($file);
                    for ($i = 0; $i < $len; $i ++) {
                        if (! $this->check($file[$i])) {
                            break;
                        }
                    }
                } else {
                    //同一个key单一文件上传
                    $this->check($file);
                }
                
                if (! empty($this->errorMsg)) {
                    break;
                }
            }
            return empty($this->errorMsg) ? true : $this->errorMsg;
        }
    }

    /**
     * 检测文件
     * 
     * 
     * @param unknown $file
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    public function check($file)
    {
        //检测文件大小
        if (! $this->checkMaxSize($file['size'])) {
            $this->errorMsg = '文件过大,单个文件不能大于' . round(($this->maxSize / (1024 * 1024)), 2) . 'M';
            return false;
        }
        //检测文件后缀
        if (! $this->checkExts($file['extension'])) {
            $this->errorMsg = '不支持该上传文件后缀';
            return false;
        }
        //检测文件mime类型,暂未启用
        if (! $this->checkMimes(true)) {
            $this->errorMsg = '不支持该上传文件mime类型';
            return false;
        }
        return true;
    }

    /**
     * 检测文件大小
     * 
     * 
     * @param unknown $size
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    private function checkMaxSize($size)
    {
        return $this->maxSize == 0 ? true : ($size <= $this->maxSize);
    }

    /**
     * 检测文件后缀
     * 
     * 
     * @param unknown $exts
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    private function checkExts($exts)
    {
        return empty($this->config['exts']) ? true : in_array(strtolower($exts), $this->exts);
    }

    /**
     * 检测文件mime类型,暂未启用
     * 
     * 
     * @param unknown $mimes
     * @return boolean
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月28日
     */
    private function checkMimes($mimes)
    {
        return true;
    }

    /**
     * 获取错误信息
     * 
     * 
     * @return unknown
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月5日
     */
    public function getErrors()
    {
        return $this->errorMsg;
    }
}
?>
