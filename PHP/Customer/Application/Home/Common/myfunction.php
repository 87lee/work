<?php

/**
 * 解析邮件模板内容
 * 
 * 
 * @param unknown $temp_name
 * @param unknown $data
 * @return multitype:
 * @author 张涛<1353178739@qq.com>
 * @since  2016年3月30日
 */
function parse_email_template($temp_name, $data)
{
    $tempFile = APP_PATH . 'Home/Expand/EmailTemplate/' . $temp_name . '.php';
    if (file_exists($tempFile)) {
        $tempArr = require $tempFile;
        foreach ($data as $k => $v) {
            if (! empty($v) && ! empty($tempArr[$k])) {
                foreach ($v as $k2 => $v2) {
                    $tempArr[$k] = str_replace('{<$' . $k2 . '>}', $v2, $tempArr[$k]);
                }
            }
        }
        return $tempArr;
    }
    return array();
}

/**
 * 设置文件上传子目录规则
 * 
 * 
 * @param string $type
 * @return string
 * @author 张涛<1353178739@qq.com>
 * @since  2016年4月1日
 */
function upload_sub_name($type = 'question')
{
    $dir = '';
    switch ($type) {
        case 'question':
            //$dir = date('Ymd') . DIRECTORY_SEPARATOR . rand_code(1);
            $dir = date('Ymd') . '/' . rand_code(6);
            break;
        default:
            break;
    }
    return $dir;
}