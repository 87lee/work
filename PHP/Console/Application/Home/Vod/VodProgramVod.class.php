<?php
	namespace Home\Vod;
	class VodProgramVod extends \Think\Model
	{
		protected $tableName = 'vod_program';
		protected $tablePrefix = '' ;
		protected $connection = 'DB_VOD';

		/*protected $_map = array(
			'picUrl'=>'thumb',
			'bigPicUrl'=>'picture',
		);*/
		public function getProgramLists($get)
		{
			$field = "a.id,c.name as type,a.name, CASE WHEN b.recommend IS NULL  THEN 0 ELSE b.recommend END AS recommend ";
			if ( empty($get['id']) ) {
				$where = '';
				$order = "b.recommend desc";

				if (!empty($get['name'])) {
					$where = "`a`.`name` like '%".$get['name']."%' ";
				}

				if (empty($get['page']) || empty($get['pageSize'])) {
					$get['page'] = 1;
					$get['pageSize'] = 30;
				}
				$get['page'] = $get['page']*$get['pageSize'] - $get['pageSize'];
				$res['extra'] = $this->limit($get['page'],$get['pageSize'])->field($field)->alias(' as a')->join(' LEFT JOIN vod_program_weight  as b ON  a.id = b.id')->join(' LEFT JOIN vod_program_type  as c ON  a.program_type_id = c.id')->where($where)->order($order)->select();
				$res['count'] = $this->field($field)->alias(' as a')->join(' LEFT JOIN vod_program_weight  as b ON  a.id = b.id')->join(' LEFT JOIN vod_program_type  as c ON  a.program_type_id = c.id')->where($where)->count();
				if (empty($res['extra'])) {
					$res['extra'] = array();
					$res['count'] = isset($res['count'])?$res['count']:'0';
				}
			}else{
				$res['extra'] = $this->field($field)->find($id);
				if (empty($res['extra'])) {
					$res['extra'] = array();
				}
			}
			return $res;
		}
		/**
		 * [getAllName 获取所有名称]
		 *  get /search/getAllProgram
		 * @return [type] [description]
		 */
		public function getAllName($get)
		{
			$where = '';
			$field = 'a.id,a.name,CASE WHEN a.douban_id IS NULL THEN "" WHEN a.douban_id IS NOT NULL THEN a.douban_id END  as doubanid,CASE WHEN a.douban_score IS NULL THEN "" WHEN a.douban_score IS NOT NULL THEN a.douban_score END  AS score,a.down_pic AS thumb,CASE WHEN a.big_pic_url IS NULL THEN "" WHEN a.big_pic_url IS NOT NULL THEN a.big_pic_url END  AS picture ,b.name AS type';
			$join = " LEFT JOIN vod_program_type AS b ON b.id = a.program_type_id";
			if (!empty($get['name'])) {
				$where = " a.name LIKE '".$get['name']."%'";
			}
			if (empty($get['page']) || empty($get['pageSize'])) {
				$get['page'] = 1;
				$get['pageSize'] = 100;
			}
			$get['page'] = $get['page']*$get['pageSize']-$get['pageSize'];

			return $this->alias(" AS a")->field($field)->where($where)->join($join)->limit((int)$get['page'],(int)$get['pageSize'])->select();
		}
		public function getHotName($get)
		{
			$where = '';
			$field = ' distinct a.name';
			$order = 'b.count desc';
			if (empty($get['page']) || empty($get['pageSize'])) {
				$get['page'] = 1;
				$get['pageSize'] = 100;
			}
			if (!empty($get['name'])) {
				$where = 'name LIKE  "%'.$get['name'].'%"';
			}
			$get['page'] = $get['page']*$get['pageSize']-$get['pageSize'];

			$res['extra'] = $this->field($field)->alias(" AS a")->join(" LEFT JOIN vod_video_click AS b ON a.id = b.video_id")->where($where)->limit($get['page'],$get['pageSize'])->order($order)->select();
			$res['count'] =  $this->alias(" AS a")->join(" LEFT JOIN vod_video_click AS b ON a.id = b.video_id")->where($where)->count();
			if (empty($res['extra'])) {
				$res['extra'] = [];
				$res['count'] = 0;
			}
			return $res;
		}

	}