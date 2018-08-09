<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Model;
use Think\Model;
Class MemberGroupModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		array('name', 'require', '用户组名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
		array('name', '', '用户组名已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
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
		if (isset($data['groupid']) && is_numeric($data['groupid'])) {
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
			$this->error = '您查看的模型不存在';
			return false;
		}
		return $data;
	}

	/**
	 * 更新缓存
	 * @author   <jason>
	 */
	public function build_cache() {
		$data = $this->select();
		$result = array();
		if (is_array($data)) {
			foreach ($data as $v) {
				$result[$v['groupid']] = $v;
			}
		}
		setcache('member_group', $result,'member');
		return $result;
	}
}