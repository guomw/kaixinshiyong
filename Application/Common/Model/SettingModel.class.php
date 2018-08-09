<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class SettingModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		array('key','require','字段名称不能为空'),
		array('key','','字段名称已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_INSERT),
		// array('groupid','number','字段分组只能为数字'),
		array('listorder','number','排序必须为数字', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	protected $_auto = array (
		array('config', 'serialize', self::MODEL_BOTH, 'function'),
	);

	/* 添加或更新数据 */
	public function update($data, $type = 'add') {
		$data = $this->create($data);
		if (empty($data)) {
			return false;
		}
		if ($type == 'add') {
			$result = $this->add();
			if ($result === false) {
				$this->error = '新增字段失败';
				return false;
			}
		} else {
			$result = $this->save();
			if (!$result) {
				$this->error = '更新字段失败';
				return false;
			}
		}
		return $result;
	}

	/* 读取单条记录 */
	public function detail_by_key($key, $field = TRUE) {
		$data = $this->field($field)->find($key);
		if (!is_array($data)) {
			$this->error = '您查看的信息不存在';
			return false;
		}
		return $data;
	}

	/**
	 * 更新缓存
	 * @author   <jason>
	 */
	public function build_cache() {
		$lists = $this->field('key,value')->order('listorder ASC')->select();
		if (empty($lists)) return FALSE;
		$result = array();
		foreach ($lists as $list) {
			$key = strtoupper($list['key']);
			$value = $list['value'];
			$result[$key] = $value;
		}
		return file_put_contents(CONF_PATH.'setting.php', "<?php \n return ".array2string($result).";\t ?>");
	}
}