<?php

namespace Home\Logic;

use Think\Model;

/**
 * 发布规则逻辑层
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年5月18日
 * @version   1.0
 */
class AppPublishRuleLogic extends Model
{

    /**
     * 检测发布规则
     * 
     * 
     * @param unknown $xmlArr
     * @throws \LogicException
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    public function checkRules($xmlArr)
    {
        //判断发布规则
        $publishRule = D('AppPublishRule')->getAll();
        if (! empty($publishRule)) {
            foreach ($publishRule as $key => $value) {
                //是否指定app
                $specifiedApp = json_decode($value['specifiedApp']);
                if (! empty($specifiedApp) && is_array($specifiedApp)) {
                    if (! in_array($xmlArr['package_name'], $specifiedApp)) {
                        continue;
                    }
                }
                
                //是否为子节点设置规则
                if (! empty($value['attrNode'])) {
                    $attrNode = explode('.', $value['attrNode']);
                    $dependency = end($attrNode);
                    if (isset($xmlArr[$dependency])) {
                        $checkNode = $xmlArr[$dependency];
                    } else {
                        throw new \LogicException('上传失败，配置文件没有' . $dependency . '子节点！', C('BAD_REQUEST'));
                    }
                    
                    if (! empty($checkNode[0])) {
                        //依赖包节点
                        foreach ($checkNode as $k => $v) {
                            if (! empty($value['attrName'])) {
                                if ($v['@attributes'][$value['attrName']] != $value['attrValue']) {
                                    continue;
                                }
                            }
                            if (empty($v[$value['column']])) {
                                throw new \LogicException('上传失败，配置文件没有' . $dependency . '子节点' . $value['column'] . '字段！', C('BAD_REQUEST'));
                            }
                            $this->checkAppPublishRule($v, $value, $value['attrNode'] . '.base');
                        }
                    } else {
                        if (! empty($value['attrName'])) {
                            if ($checkNode['@attributes'][$value['attrName']] != $value['attrValue']) {
                                continue;
                            }
                        }
                        if (empty($checkNode[$value['column']])) {
                            throw new \LogicException('上传失败，配置文件没有' . $dependency . '子节点' . $value['column'] . '字段！', C('BAD_REQUEST'));
                        }
                        $this->checkAppPublishRule($checkNode, $value, $value['attrNode'] . '.base');
                    }
                } else {
                    if (! empty($value['attrName'])) {
                        if ($xmlArr['@attributes'][$value['attrName']] != $value['attrValue']) {
                            continue;
                        }
                    }
                    if (empty($xmlArr[$value['column']])) {
                        throw new \LogicException('上传失败，配置文件没有' . $value['column'] . '字段！', C('BAD_REQUEST'));
                    }
                    
                    $this->checkAppPublishRule($xmlArr, $value, 'apk');
                }
            }
        }
    }

    /**
     * 条件检验
     * 
     * 
     * @param unknown $xmlArr
     * @param unknown $value
     * @param unknown $dependency
     * @throws \LogicException
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    public function checkAppPublishRule($xmlArr, $value, $dependency)
    {
        $msg = $this->checkOperatorAndCondition($xmlArr, $value, $dependency);
        if ($msg !== true) {
            throw new \LogicException($msg, C('BAD_REQUEST'));
        }
    }

    /**
     * 验证运算
     * 
     * 
     * @param unknown $xmlArr
     * @param unknown $value
     * @param unknown $dependency
     * @author 张涛<1353178739@qq.com>
     * @since  2016年5月18日
     */
    public function checkOperatorAndCondition($xmlArr, $value, $dependency)
    {
        $msg = '上传失败，' . $dependency . '的字段' . $value['column'] . '不符合发布规则！';
        
        if (! empty($value['operator'])) {
            switch ($value['condition']) {
                case '==':
                    switch ($value['operator']) {
                        case '+':
                            if (floatval($xmlArr[$value['column']]) + floatval($value['param']) != floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '-':
                            if (floatval($xmlArr[$value['column']]) - floatval($value['param']) != floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '*':
                            if (floatval($xmlArr[$value['column']]) * floatval($value['param']) != floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '/':
                            if (floatval($xmlArr[$value['column']]) / floatval($value['param']) != floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '%':
                            
                            if (floatval($xmlArr[$value['column']]) % floatval($value['param']) != floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        default:
                            return $msg;
                            break;
                    }
                    break;
                case '!=':
                    switch ($value['operator']) {
                        case '+':
                            if (floatval($xmlArr[$value['column']]) + floatval($value['param']) == floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '-':
                            if (floatval($xmlArr[$value['column']]) - floatval($value['param']) == floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '*':
                            if (floatval($xmlArr[$value['column']]) * floatval($value['param']) == floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '/':
                            if (floatval($xmlArr[$value['column']]) / floatval($value['param']) == floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '%':
                            if (floatval($xmlArr[$value['column']]) % floatval($value['param']) == floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        default:
                            return $msg;
                            break;
                    }
                    break;
                case '>=':
                    switch ($value['operator']) {
                        case '+':
                            if (floatval($xmlArr[$value['column']]) + floatval($value['param']) < floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '-':
                            if (floatval($xmlArr[$value['column']]) - floatval($value['param']) < floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '*':
                            if (floatval($xmlArr[$value['column']]) * floatval($value['param']) < floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '/':
                            if (floatval($xmlArr[$value['column']]) / floatval($value['param']) < floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '%':
                            if (floatval($xmlArr[$value['column']]) % floatval($value['param']) < floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        default:
                            return $msg;
                            break;
                    }
                    break;
                case '<=':
                    switch ($value['operator']) {
                        case '+':
                            if (floatval($xmlArr[$value['column']]) + floatval($value['param']) > floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '-':
                            if (floatval($xmlArr[$value['column']]) - floatval($value['param']) > floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '*':
                            if (floatval($xmlArr[$value['column']]) * floatval($value['param']) > floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '/':
                            if (floatval($xmlArr[$value['column']]) / floatval($value['param']) > floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '%':
                            if (floatval($xmlArr[$value['column']]) % floatval($value['param']) > floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        default:
                            return $msg;
                            break;
                    }
                    break;
                case '>':
                    switch ($value['operator']) {
                        case '+':
                            if (floatval($xmlArr[$value['column']]) + floatval($value['param']) <= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '-':
                            if (floatval($xmlArr[$value['column']]) - floatval($value['param']) <= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '*':
                            if (floatval($xmlArr[$value['column']]) * floatval($value['param']) <= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '/':
                            if (floatval($xmlArr[$value['column']]) / floatval($value['param']) <= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '%':
                            if (floatval($xmlArr[$value['column']]) % floatval($value['param']) <= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        default:
                            return $msg;
                            break;
                    }
                    break;
                case '<':
                    switch ($value['operator']) {
                        case '+':
                            if (floatval($xmlArr[$value['column']]) + floatval($value['param']) >= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '-':
                            if (floatval($xmlArr[$value['column']]) - floatval($value['param']) >= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '*':
                            if (floatval($xmlArr[$value['column']]) * floatval($value['param']) >= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '/':
                            if (floatval($xmlArr[$value['column']]) / floatval($value['param']) >= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        case '%':
                            if (floatval($xmlArr[$value['column']]) % floatval($value['param']) >= floatval($value['value'])) {
                                return $msg;
                            }
                            break;
                        default:
                            return $msg;
                            break;
                    }
                    break;
                default:
                    return $msg;
                    break;
            }
        } else {
            switch ($value['condition']) {
                case '==':
                    if ($xmlArr[$value['column']] != $value['value']) {
                        return $msg;
                    }
                    break;
                case '!=':
                    
                    if ($xmlArr[$value['column']] == $value['value']) {
                        return $msg;
                    }
                    break;
                case '>=':
                    if ($xmlArr[$value['column']] < $value['value']) {
                        return $msg;
                    }
                    break;
                case '<=':
                    if ($xmlArr[$value['column']] > $value['value']) {
                        return $msg;
                    }
                    break;
                case '>':
                    if ($xmlArr[$value['column']] <= $value['value']) {
                        return $msg;
                    }
                    break;
                case '<':
                    if ($xmlArr[$value['column']] >= $value['value']) {
                        return $msg;
                    }
                    
                    break;
                default:
                    return $msg;
                    break;
            }
        }
        
        return true;
    }
}
    
