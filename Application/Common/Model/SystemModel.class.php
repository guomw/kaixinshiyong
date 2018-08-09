<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class SystemModel extends Model {
	protected $_validate = array();
	protected $_auto = array();
	/* 读取单条记录 */
	public function detail($id, $field = TRUE) {
		$data = $this->field($field)->find($id);
		if (!is_array($data)) {
			$this->error = '您查看的文章不存在';
			return false;
		}
		return $data;
	}

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {
		if ($iscreate == TRUE) {
			$data = $this->create($data);
		}
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data[$this->pk]) && is_numeric($data[$this->pk])) {
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
}