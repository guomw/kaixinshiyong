<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class AdminRoleModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		array('rolename','require','角色名称不能为空'),
		array('listorder','number','排序必须为数字'),
		array('disabled', array(0, 1),'角色状态数据非法', '', 'IN'),
	);

	protected $_auto = array (
		array('updatetime', NOW_TIME, self::MODEL_BOTH, 'string'),
	);

	/* 添加或更新数据 */
	public function update($data) {
		$data = $this->create($data);
		if (empty($data)) {
			return false;
		}
		if (isset($data['roleid']) && is_numeric($data['roleid'])) {
			$result = $this->save();
			if (!$result) {
				$this->error = '更新数据失败';
				return false;
			}
		} else {
			$result = $this->add();
			if ($result === false) {
				$this->error = '新增数据失败';
				return false;
			}
		}
		return $result;
	}
	/* 读取单条记录 */
	public function delete_by_roleid($roleid) {
		$where = array();
		if (is_array($roleid)) {
			$where['roleid'] = array("IN", $roleid);
		} else {
			$where['roleid'] = $roleid;
		}
		return $this->delete($roleid);
	}


	/* 读取单条记录 */
	public function detail($roleid, $field = TRUE) {
		$data = $this->field($field)->find($roleid);
		if (!is_array($data)) {
			$this->error = '记录不存在';
			return false;
		}
		return $data;
	}

	/* 生成缓存 */
	public function build_cache() {
		$array = (array) $this->select();
		$result = array();
		foreach ($array as $key => $value) {
			$result[$value['roleid']] = $value;
		}
		return setcache('role', $result, 'commons');
	}
}