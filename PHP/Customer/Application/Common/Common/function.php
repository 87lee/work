<?php

/**
 * 返回接口数据json格式
 * {
 *      "code":403,  状态码
 *      "msg":"非法请求",     描述
 *      "retval":[]        返回的数据
 * }
 * 
 * 
 * @param unknown $code
 * @param string $msg
 * @param unknown $retval
 * @author 张涛<1353178739@qq.com>
 * @since  2016年3月16日
 */
function json_echo($code, $msg = '', $retval = array())
{
    $result = array('code' => $code, 'msg' => $msg, 'retval' => $retval);
    trace('echo前耗时:'.G('begin','end',6).'s'.'  开销：'.G('begin','end','m').'k','','',true);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    trace('echo后耗时:'.G('begin','end',6).'s'.'  开销：'.G('begin','end','m').'k','','',true);
    exit();
}

/**
 * 获取固定长度的随机字符串
 * 
 * 
 * @param number $len
 * @return string
 * @author 张涛<1353178739@qq.com>
 * @since  2016年3月30日
 */
function rand_code($len = 15)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $count = strlen($chars);
    for ($i = 0; $i < $len; $i ++) {
        $arr[$i] = $chars[mt_rand(0, $count - 1)];
    }
    shuffle($arr);
    return implode('', $arr);
}


