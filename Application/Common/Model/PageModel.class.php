<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class PageModel extends Model {
	/*自动验证*/
	protected $_validate = array (
	);

	protected $_auto = array (
		array('updatetime', NOW_TIME, self::MODEL_BOTH, 'string'),
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {		
		if ($iscreate == TRUE) $data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (!isset($data['catid']) || empty($data['catid'])) {
			$this->error = '栏目ID不能为空';
			return false;
		}

		$result = $this->where(array('catid' => $data['catid']))->save($data);
		if (!$result) {
			if (!$this->add($data)) {
				$this->error = '数据提交失败';
				return FALSE;
			}
		}
		return TRUE;
	}

	/* 读取单条记录 */
	public function detail($catid, $field) {
		$data = $this->field($field)->find($catid);
		if (!is_array($data)) {
			$this->error = '您查看的文章不存在';
			return false;
		}
		return $data;
	}
}