<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class ModelFieldModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	);

	protected $_auto = array (
		array('setting', 'array2string', self::MODEL_BOTH, 'function')
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {		
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if ($iscreate == TRUE) {
			$data = $this->create($data);
		}
		if (isset($data['fieldid']) && is_numeric($data['fieldid'])) {
			$result = $this->save($data);
			if (!$result) {
				$this->error = '编辑模型字段失败';
				return false;
			}
		} else {
			$result = $this->add($data);
			if ($result === false) {
				$this->error = '新增模型字段失败';
				return false;
			}
		}
		return $result;
	}

	/* 读取单条记录 */
	public function detail($id, $field) {
		$data = $this->field($field)->find($id);
		if (!is_array($data)) {
			$this->error = '您查看的文章不存在';
			return false;
		}
		return $data;
	}
}