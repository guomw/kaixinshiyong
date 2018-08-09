<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class AdminModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		array('username','require','用户名不能为空'),
		array('username','3,20','用户名长度为3到20个', self::VALUE_VALIDATE, 'length'),
		array('username','','用户名已存在', self::VALUE_VALIDATE, 'unique'),

		// 邮箱地址（存在就验证）
		array('email','require','邮箱地址不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('email','email','邮箱地址不能为空', self::VALUE_VALIDATE, '', self::MODEL_BOTH),
	);

	protected $_auto = array (		
		array('inputtime', NOW_TIME, self::MODEL_INSERT, 'string'),
		array('updatetime', NOW_TIME, self::MODEL_BOTH, 'string'),
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {		
		if ($iscreate == TRUE) $data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data['userid']) && is_numeric($data['userid'])) {
			$result = $this->save($data);
			if (!$result) {
				$this->error = '更新数据失败';
				return false;
			}
		} else {
			$result = $this->add($data);
			if ($result === false) {
				$this->error = '添加数据失败';
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