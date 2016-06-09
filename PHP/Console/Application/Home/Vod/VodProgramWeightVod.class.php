<?php
	namespace Home\Vod;
	class VodProgramWeightVod extends \Think\Model
	{
		protected $tableName = 'vod_program_weight';
		protected $tablePrefix = '' ;
		// protected $tablePrefix = isset( $dbVod = C('DB_VOD') ) ? $dbVod['DB_PREFIX']:DB_PREFIX;
		protected $connection = 'DB_VOD';
		/*protected $_map = array(
			'appName' =>'app_name',
			'pkgName'  =>'pkgname',
		);*/
		protected $_validate = array(
			array('publish','','该版本已存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);
		public function modifyProgramSort($put)
		{
			if ( !isset($put['recommend']) || empty($put['ids']) || !is_array($put['ids'])) {
				result('param');
			}

			$put['ids'] = array_map('intval', $put['ids']);

			$put['ids'] = array_unique($put['ids']);

			$put['recommend'] = intval($put['recommend']);

			if ( 0 > $put['recommend']  || $put['recommend'] > 100) {
				result('排序只能0-100');
			}

			$idSql = implode(',', $put['ids']);
			$modifyArr = array();
			if ($isRecommend = $this->getArrForIdSql($idSql)) {
				$options = array(
					'recommend'=>$put['recommend'],
				);
				foreach ($isRecommend as  $value) {
					$modifyArr[] = $value['id'];
				}
				$idSql =  implode(',', $modifyArr);
				$this->where("id in (".$idSql.")")->save($options);
			}
			$addArr = array_diff($put['ids'],$modifyArr);
			if (!empty($addArr)) {
				$options = array();
				foreach ($addArr as  $value) {
					$options[] = array(
						'id' =>$value,
						'recommend'=>$put['recommend'],
					);
				}
				$this->addAll($options);
			}
			return ;
		}
		public function getArrForIdSql($idSql)
		{
			return $this->where("id in (".$idSql.")")->select();
		}

	}