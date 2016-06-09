<?php
	namespace Home\Desktop;
	class FragmentDesktop extends \Think\Model
	{
		protected $tableName = 'fragment';
		protected $connection = 'DB_DESKTOP';

		public function addFragment($options)
		{
			if (!empty($options)) {
				foreach ($options as $key => $value) {

					if ($res = $this->create($value)) {
						$addOptions[] = $res;
					}
				}
			}
			if (!empty($addOptions)) {
				$this->addAll($addOptions);
			}
		}
		public function deleteFragment($id)
		{
			$res = $this->where("`fragment_type_id`=%d",array($id))->find();
			if ($res) {
				$res = D('FragmentType','Desktop')->getValOneForId($id);
				if ($res) {
					$this->where("`fragment_type_id`=%d",array($id))->delete();
					D('FragmentType','Desktop')->deleteFragmentType($id);

				}else{
					$this->where("`fragment_type_id`=%d",array($id))->delete();
					result('屏不存在');
				}
			}else{
				$res = D('FragmentType','Desktop')->getValOneForId($id);
				if ($res) {
					D('FragmentType','Desktop')->deleteFragmentType($id);

				}else{
					result('屏不存在');
				}
			}
		}
		public function fragmentLists($id = null)
		{
			if (!empty($id)) {
				$data = array();
				$fragmentType = D('FragmentType','Desktop')->fragmentTypeLists($id);
				if ($fragmentType) {
					$data['extra'] = $fragmentType;
					$fragmentoPeration = $this->field("`w`,`h`,`yw`,`yh`,`bg`,`y`,`x`,`type`")->where("`fragment_type_id`=%d ",array($fragmentType['id']))->select();

					if ($fragmentoPeration) {
						$data['extra']['property'] = $fragmentoPeration;
					}else{
						$data['extra']['property'] = array();
					}
				}else{
					$data['extra'] = array();
				}

				result(true,$data);
			}else{
				$data = array();
				$fragmentTypeLists = D('FragmentType','Desktop')->fragmentTypeLists();
				if ($fragmentTypeLists) {
					foreach ($fragmentTypeLists as $key => $value) {

						$fragmentoPeration = $this->field("`w`,`h`,`yw`,`yh`,`bg`,`y`,`x`,`type`")->where("`fragment_type_id`=%d ",array($value['id']))->select();
						if ($fragmentoPeration) {
							$fragmentTypeLists[$key]['property'] = $fragmentoPeration;
						}else{
							$fragmentTypeLists[$key]['property'] = array();
						}

					}
					$data['extra'] = $fragmentTypeLists;
				}else{
					$data['extra'] = array();
				}
				result(true,$data);
			}

		}
		public function modifyFragment($id,$options = null)
		{
			$fragment = $this->where("`fragment_type_id`=%d",array($id))->select();

			if($options ===null){

				if ($fragment) {
					$this->where("`fragment_type_id`=%d",array($id))->delete();
				}
				result();
			}else{
				if ($fragment) {
					if (count($fragment) == count($options)) {
						foreach ($fragment as $key => $value) {
							$this->where("`id`=%d",array($value['id']))->save($options[$key]);
						}
					}elseif (count($fragment) > count($options)) {

						$modifyId = count($options);

						foreach ($fragment as $key => $value) {
							if ($modifyId <= 0) {
								$this->where("`id`=%d",array($value['id']))->delete();
							}else{
								$this->where("`id`=%d",array($value['id']))->save($options[$key]);
							}
							$modifyId--;
						}
					}elseif (count($fragment) < count($options)) {

						$modifyId = 0;
						foreach ($fragment as $key => $value) {
							$this->where("`id`=%d",array($value['id']))->save($options[$key]);
							$modifyId++;
						}
						for ($i=0; $i < $modifyId; $i++) {
							unset($options[$i]);
						}
						$options = array_values($options);

						$this->addAll($options);
					}
				}else{

					$this->addAll($options);
				}
				result();
			}

		}
	}