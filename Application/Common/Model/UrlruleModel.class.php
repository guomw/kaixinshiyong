<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class UrlruleModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		array('name','require','规则名称不能为空'),
		array('urlrule','require','规则不能为空'),
	);

	protected $_auto = array (
	);

	/* 添加或更新数据 */
	public function update($data) {
		$data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data['urlruleid']) && is_numeric($data['urlruleid'])) {
			$result = $this->save();
			if (!$result) {
				$this->error = '更新规则失败';
				return false;
			}
		} else {
			$result = $this->add();
			if ($result === false) {
				$this->error = '新增规则失败';
				return false;
			}
		}
		return $result;
	}

	/* 读取单条记录 */
	public function detail($urlruleid = 0, $field = TRUE) {
		$data = $this->field($field)->find($urlruleid);
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
			$result[$r['urlruleid']] = $r;
		}
		return setcache('urlrule', $result, 'commons');
	}
}