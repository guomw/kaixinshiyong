<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class AdminPanelModel extends Model {
	/*自动验证*/
	protected $_validate = array (
	);

	protected $_auto = array (
		array('datetime', NOW_TIME, self::MODEL_BOTH, 'string'),
	);

	/* 添加或更新数据 */
	public function update($data) {
		$data = $this->create($data);
		if (empty($data)) {
			return false;
		}
		if (isset($data['menuid']) && is_numeric($data['menuid'])) {
			$result = $this->save($data);
			if (!$result) {
				$this->error = '更新数据失败';
				return false;
			}
		} else {
			$result = $this->add($data);
			if ($result === false) {
				$this->error = '新增数据失败';
				return false;
			}
		}
		return $result;
	}

	/* 读取单条记录 */
	public function detail($id, $field = TRUE) {
		$data = $this->field($field)->find($id);
		if (!is_array($data)) {
			$this->error = '您查看的文章不存在';
			return false;
		}
		return $data;
	}

	/* 删除记录 */
	public function _delete($id) {
		$where = array();
		if (is_array($id)) {
			$where['menuid'] = array("IN", $id);
		} else {
			$where['menuid'] = (int) $id;
		}
		$result = $this->where($where)->delete();
		if (!$result) {
			$this->error = '数据删除失败';
			return false;
		}
		return TRUE;
	}
}