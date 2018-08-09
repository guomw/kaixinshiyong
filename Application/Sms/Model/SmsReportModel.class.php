<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Sms\Model;
use Think\Model;
Class SmsReportModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		array('mobile', 'require', '手机号码不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('enum', array('notify','password','register'), '手机号码不能为空', self::EXISTS_VALIDATE, 'in', self::MODEL_BOTH),
	);

	protected $_auto = array (
		// array(完成字段1,完成规则,[完成条件,附加规则]),		
		array('posttime', NOW_TIME, self::MODEL_BOTH, 'string',),		
		array('ip', 'get_client_ip', self::MODEL_BOTH, 'function'),
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = NULL) {		
		if ($iscreate !== NULL) {
			$data = $this->create($data, $iscreate);
		}
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data['id']) && is_numeric($data['id'])) {
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

}