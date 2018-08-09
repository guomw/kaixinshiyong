<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Model;
use Think\Model;
Class MemberSpaceModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		//array('name', 'require', '用户组名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
	);

	protected $_auto = array (
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {		
		if ($iscreate == TRUE) {
			$data = $this->create($data);
		}
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data['spaceid']) && is_numeric($data['spaceid']) && $data['spaceid'] != 0) {
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
}