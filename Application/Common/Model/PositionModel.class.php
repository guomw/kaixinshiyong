<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class PositionModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array('file','require','规则名称不能为空'),
		// array('urlrule','require','规则不能为空'),
	);

	protected $_auto = array (
		// array('module', 'contont', self::MODEL_BOTH, 'function'),
	);

	/* 添加或更新数据 */
	public function update($data) {
		$data = $this->create($data);
		if (empty($data)) {
			return false;
		}
		if (isset($data['posid']) && is_numeric($data['posid'])) {
			$result = $this->save();
			if (!$result) {
				$this->error = '更新信息失败';
				return false;
			}
		} else {
			$result = $this->add();
			if ($result === false) {
				$this->error = '新增信息失败';
				return false;
			}
		}
		return $result;
	}

	/* 读取单条记录 */
	public function detail($posid = 0, $field = TRUE) {
		$data = $this->field($field)->find($posid);
		if (!is_array($data)) {
			$this->error = '您查看的信息不存在';
			return false;
		}
		return $data;
	}

	/* 更新缓存 */
	public function build_cache() {
		$result = array();
		$data = (array) $this->select();
		if(empty($data)) return FALSE;
		foreach ($data as $r) {
			$result[$r['posid']] = $r;
		}
		unset($data);
		return setcache('position', $result, 'commons');
	}
}