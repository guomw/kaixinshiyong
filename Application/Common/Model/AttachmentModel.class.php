<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class AttachmentModel extends Model {
	/*自动验证*/
	protected $_validate = array (
	);

	protected $_auto = array (
		array('uploadtime', NOW_TIME, self::MODEL_INSERT, 'string'),
		array('uploadip', 'get_client_ip', self::MODEL_INSERT, 'function'),
	);

	/* 添加或更新数据 */
	public function update($data) {
		$data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data['aid']) && is_numeric($data['aid'])) {
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
	public function detail($urlruleid = 0, $field = TRUE) {
		$data = $this->field($field)->find($urlruleid);
		if (!is_array($data)) {
			$this->error = '您查看的信息不存在';
			return false;
		}
		return $data;
	}
}