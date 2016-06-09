<?php

namespace Home\Logic;

use Think\Model;

/**
 * app状态修改记录逻辑
 * 
 * @author    张涛<1353178739@qq.com>
 * @since     2016年4月22日
 * @version   1.0
 */
class AppStatusModifyRecordLogic extends Model
{

    /**
     * 获取总的测试记录
     * 
     * 
     * @param unknown $pkgName
     * @param unknown $sTime
     * @param unknown $eTime
     * @return Ambigous <multitype:multitype:number unknown  , unknown>
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function getSumRecordStatics($pkgName = array(), $sTime, $eTime)
    {
        if (! empty($pkgName)) {
            $where = 'asmr.pkg_name IN (' . implode(',', 
                array_map(function ($v)
                {
                    return "'" . $v . "'";
                }, $pkgName)) . ") AND u.tester = 'true'";
        } else {
            $where = "u.tester = 'true'";
        }
        ! empty($sTime) && $where .= ' AND asmr.modify_time >=' . $sTime;
        ! empty($eTime) && $where .= ' AND asmr.modify_time <=' . $eTime;
        $where = trim(trim($where), 'AND');
        //查看打回的条件
        $backWhere = trim(trim($where . " AND asmr.status_after='back'"), 'AND');
        
        $records = $this->alias('asmr')
            ->join(
            'LEFT JOIN tb_user u ON asmr.modified_by=u.user INNER JOIN tb_app_publish ap ON (asmr.pkg_name = ap.pkg_name AND asmr.version_code = ap.version_code)')
            ->field('asmr.id,asmr.pkg_name,asmr.status_after,asmr.modified_by,u.user,u.name,count(1) AS count')
            ->where($where)
            ->group('asmr.modified_by,asmr.status_after')
            ->select();
        
        $statics = $backIds = array();
        if (! empty($records)) {
            foreach ($records as $k => $v) {
                if (! isset($statics[$v['user']])) {
                    $statics[$v['user']] = array(
                        'user' => $v['modified_by'], 
                        'name' => $v['name'], 
                        'pass' => 0, 
                        'not_pass' => 0, 
                        'regress' => 0, 
                        'back' => 0);
                }
                
                switch ($v['status_after']) {
                    case 'true':
                        $statics[$v['modified_by']]['pass'] += $v['count'];
                        break;
                    case 'false':
                        $statics[$v['modified_by']]['not_pass'] += $v['count'];
                        break;
                    case 'regress':
                        $statics[$v['modified_by']]['regress'] += $v['count'];
                        break;
                    case 'back':
                    //打回单独处理
                    // $statics[$v['modified_by']]['back'] += $v['count'];
                    //break;
                    default:
                        break;
                }
            }
            
            //打回是产品，这里无需判断
            $backWhere= str_replace("u.tester = 'true'", 'true', $backWhere);
            $backIds = $this->alias('asmr')
                ->join(
                'LEFT JOIN tb_user u ON asmr.modified_by=u.user INNER JOIN tb_app_publish ap ON (asmr.pkg_name = ap.pkg_name AND asmr.version_code = ap.version_code)')
                ->field('asmr.id')
                ->where($backWhere)
                ->select();
            //处理打回状态统计
            if (! empty($backIds)) {
                $backIds = array_column($backIds, 'id');
                $sql = "SELECT
		asmr.pkg_name,asmr.version_code,asmr.status_after,asmr.modified_by, (
		SELECT
			asmr2.modified_by
		FROM
			tb_app_status_modify_record asmr2
		WHERE
			asmr2.pkg_name = asmr.pkg_name
		AND asmr2.version_code = asmr.version_code
		AND (asmr2.status_after = 'true' OR asmr2.status_after = 'regress')
	) AS tester,count(1) AS count
FROM
	tb_app_status_modify_record asmr
WHERE ";
                
                $where2 = 'asmr.id IN (' . implode(',', $backIds) . ')';
                $sql .= $where2 . ' GROUP BY tester';
                $backList = $this->query($sql);
                if (! empty($backList)) {
                    $back = array_column($backList, 'count', 'tester');
                    $testerIds = array_keys($back);
                    $testerInfo = M('User')->field('user,name')
                        ->where(array('user' => array('IN', $testerIds)))
                        ->index('user')
                        ->select();
                    foreach ($back as $k => $v) {
                        if (isset($statics[$k]['back'])) {
                            $statics[$k]['back'] += $v;
                        } else {
                            if (! empty($k)) {
                                $statics[$k] = array(
                                    'user' => $testerInfo[$k]['user'], 
                                    'name' => $testerInfo[$k]['name'], 
                                    'pass' => 0, 
                                    'not_pass' => 0, 
                                    'regress' => 0, 
                                    'back' => $v);
                            }
                        }
                    }
                }
            }
        }
        return $statics;
    }

    /**
     * 根据应用包名获取统计数据
     * 
     * 
     * @param unknown $pkgNames
     * @param unknown $sTime
     * @param unknown $eTime
     * @return multitype:multitype: 
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function getPkgNameStatics($pkgNames = array(), $sTime, $eTime)
    {
        $appStatic = array();
        if (! empty($pkgNames) && is_array($pkgNames)) {
            $search = '';
            ! empty($sTime) && $search .= ' AND modify_time >=' . $sTime;
            ! empty($eTime) && $search .= ' AND modify_time <=' . $eTime;
            
            $appStatic['test'] = $this->getStaticsByPkgName($pkgNames, 'test', $search);
            $appStatic['not_pass'] = $this->getStaticsByPkgName($pkgNames, 'not_pass', $search);
            $appStatic['regress'] = $this->getStaticsByPkgName($pkgNames, 'regress', $search);
            $appStatic['one_pass'] = $this->getStaticsByPkgName($pkgNames, 'one_pass', $search);
            $appStatic['back'] = $this->getStaticsByPkgName($pkgNames, 'back', $search);
        }
        return $appStatic;
    }

    /**
     * 获取应用包对应的统计数据
     * 
     * 
     * @param unknown $pkgNames
     * @param unknown $type
     * @param unknown $search
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月25日
     */
    public function getStaticsByPkgName($pkgNames, $type, $search)
    {
        if (! empty($pkgNames) && ! empty($type)) {
            $where = 'asmr.pkg_name IN (' . implode(',', 
                array_map(function ($v)
                {
                    return "'" . $v . "'";
                }, $pkgNames)) . ')';
            switch ($type) {
                case 'test':
                    $where .= " AND asmr.status_after='test'";
                    break;
                case 'not_pass':
                    $where .= " AND asmr.status_after='false'";
                    break;
                case 'regress':
                    $where .= " AND asmr.status_after='regress'";
                    break;
                case 'one_pass':
                    $where .= " AND asmr.status_before='test' AND asmr.status_after='true'";
                    break;
                case 'back':
                    $where .= " AND asmr.status_after='back'";
                    break;
                default:
                    $where = '';
                    break;
            }
            if (! empty($where)) {
                return $this->alias('asmr')
                    ->field('asmr.pkg_name,count(1) AS count')
                    ->join('INNER JOIN tb_app_publish ap ON (asmr.pkg_name = ap.pkg_name AND asmr.version_code = ap.version_code)')
                    ->where($where . $search)
                    ->group('asmr.pkg_name')
                    ->index('pkg_name')
                    ->select();
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * 根据app应用报名获取状态修改记录
     * 
     * 
     * @param string $pkgName
     * @param string $sTime
     * @param string $eTime
     * @return multitype:
     * @author 张涛<1353178739@qq.com>
     * @since  2016年4月27日
     */
    public function getAppStatusRecordByApp($pkgName, $sTime, $eTime)
    {
        $statics = array(
            'statistics' => array('test' => 0, 'not_pass' => 0, 'regress' => 0, 'one_pass' => 0, 'back' => 0), 
            'records' => array());
        if (! empty($pkgName)) {
            $sTime = isset($sTime) ? strtotime($sTime) : 0;
            $eTime = isset($eTime) ? strtotime($eTime . ' 23:59:59') : 0;
            
            $where = 'asmr.pkg_name="' . $pkgName . '" ';
            ! empty($sTime) && $where .= ' AND asmr.modify_time >=' . $sTime;
            ! empty($eTime) && $where .= ' AND asmr.modify_time <=' . $eTime;
            
            $statics['records'] = $this->alias('asmr')
                ->join(
                'LEFT JOIN tb_user u ON asmr.modified_by=u.user INNER JOIN tb_app_publish ap ON (asmr.pkg_name = ap.pkg_name AND asmr.version_code = ap.version_code)')
                ->field('asmr.*,u.name AS modifyer')
                ->where($where)
                ->order('modify_time ASC')
                ->select();
            //获取统计
            if (! empty($statics['records'])) {
                $pkgStatics = $this->getPkgNameStatics(array($pkgName), $sTime, $eTime);
                isset($pkgStatics['test'][$pkgName]) && $statics['statistics']['test'] = $pkgStatics['test'][$pkgName]['count'];
                isset($pkgStatics['not_pass'][$pkgName]) && $statics['statistics']['not_pass'] = $pkgStatics['not_pass'][$pkgName]['count'];
                isset($pkgStatics['regress'][$pkgName]) && $statics['statistics']['regress'] = $pkgStatics['regress'][$pkgName]['count'];
                isset($pkgStatics['one_pass'][$pkgName]) && $statics['statistics']['one_pass'] = $pkgStatics['one_pass'][$pkgName]['count'];
                isset($pkgStatics['back'][$pkgName]) && $statics['statistics']['back'] = $pkgStatics['back'][$pkgName]['count'];
            }
        }
        return $statics;
    }
}
