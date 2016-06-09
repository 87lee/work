<?php
	namespace Home\Desktop;
	class DesktopLogoDesktop extends \Think\Model
	{
		protected $tableName = 'desktop_logo';
		protected $connection = 'DB_DESKTOP';

		public function addDesktopLogo($desktopId,$x,$y,$style,$isShowIndicator,$intervalTime)
		{
			$options = array(
				'desktop_id' =>$desktopId,
				'x'=>$x,
				'y'=>$y,
				'style'=>$style,
				'interval_time'=>$intervalTime,
				'is_show_indicator'=>$isShowIndicator,
			);
			return $this->add($options);
		}
		public function deleteDesktopLogo($desktopId)
		{
			if ( $this->where("`desktop_id` IN ( ".$desktopId.")")->find() ) {
				return $this->where("`desktop_id` IN ( ".$desktopId.")")->delete();
			}

		}
		public function logoLists($desktopId)
		{
			$data = $this->field('id , x,y,style,is_show_indicator as isShowIndicator,interval_time as intervalTime')->where("`desktop_id` = %d ",array($desktopId))->find();
			if ($data) {
				$res  = D('DesktopLogoInfo','Desktop')->DesktopLogoLists($data['id']);
				if (!empty($res)) {
					foreach ($res as  $value) {
						$data['logoLists'][] = $value['path'];
					}

				}else{
					$data['logoLists'] = array();
				}
				unset($data['id']);

			}else{
				$data = array();
			}
			return $data;
		}
		public function modifyDesktopLogo($desktopId,$x,$y,$style,$isShowIndicator,$intervalTime)
		{
			$res = $this->where("`desktop_id`=%d",array($desktopId))->find();
			if ($res) {
				$options = array(
					'desktop_id' =>$desktopId,
					'x'=>$x,
					'y'=>$y,
					'style'=>$style,
					'interval_time'=>$intervalTime,
					'is_show_indicator'=>$isShowIndicator,
				);
				$this->where("`id`=%d",array($res['id']))->save($options);
				return $res['id'];
			}else{
				return $this->addDesktopLogo($desktopId,$x,$y,$style,$isShowIndicator,$intervalTime);
			}


		}
		public function getValOneForDesktopId($desktopId)
		{
			return $this->field("`id`,`x`,`y`,`style`,`is_show_indicator` as isShowIndicator ,`desktop_id`,`interval_time` AS intervalTime")->where("`desktop_id`=%d",array($desktopId))->find();
		}
		public function getSqlIdForDesktopId($desktopId)
		{
			return $this->field('`id`')->where("`desktop_id`=%d",array($desktopId))->select(false);
		}
		public function getSqlIdForDesktopIdArr($desktopArrId)
		{
			return $this->field('`id`')->where("`desktop_id` IN (".$desktopArrId.")")->select(false);
		}
		public function createLogoConfig($desktopId=null,$desktop=null,$error = false)
		{
			$desktopJson = array();
			if ($desktop===null) {
				$desktop = D('Desktop','Desktop')->getValForId($desktopId);
			}
			if ($desktop) {
				$desktopJson = $this->getValOneForDesktopId($desktopId);

				if ($desktopJson) {
					$logoArray= D('DesktopLogoInfo','Desktop')->DesktopLogoLists($desktopJson['id']);

					if ($logoArray) {
						foreach ($logoArray as $key => $value) {
							$desktopJson['logoArray'][] = $value['path'];
						}
						unset($desktopJson['id']);
					}else{
						if ($error) {
							return $result = array('reason'=>'桌面logo数据没有图片信息');
							// return false;
						}else{
							result('桌面logo数据没有图片信息');
						}
					}
				}else{
					return false;
				}

			}else{
				if ($error) {
					return $result = array('reason'=>'桌面不存在');
					// return false;
				}else{
					result('桌面不存在');
				}
			}

			if (!empty($desktopJson)) {
				// echo json_encode($desktopJson,JSON_UNESCAPED_UNICODE);
				return $desktopJson;
			}else{
				return false;
			}
		}
	}