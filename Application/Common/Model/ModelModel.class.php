<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class ModelModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		array('name', 'require', '模型名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('tablename', 'require', '模型表名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('tablename', '', '模型表名已存在', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
	);

	protected $_auto = array (
		array('inputtime', NOW_TIME, self::MODEL_BOTH, string),
		array('setting', 'array2string', self::MODEL_BOTH, 'function'),
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
		if (isset($data['modelid']) && is_numeric($data['modelid'])) {
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
	 * 检查表是否存在
	 * @param $table 表名
	 * @return boolean
	 */
	public function table_exists($table) {
		$tables = $this->getTables();
		return in_array($table, $tables) ? 1 : 0;
	}	

    /**
     * 获取指定数据库的所有表名
     * @author huajie <banhuajie@163.com>
     */
    public function getTables($connection = NULL){
    	$tables = M()->query('SHOW TABLES;');
    	foreach ($tables as $key=>$value){
    		$tables[$key] = $value['Tables_in_'.C('DB_NAME')];
    	}
    	return $tables;
    }

    public function drop_table($tablename = NULL) {
    	if (empty($tablename)) {
    		return FALSE;
    	}
    	$tablename = C('DB_PREFIX').$tablename;
    	$sqlquery = "DROP TABLE IF EXISTS `{$tablename}`;";
    	return sqlexecute($sqlquery);
    }

	/**
	 * 更新缓存
	 * @author   <jason>
	 */
	public function _cache() {
		$data = $this->select();
		$result = array();
		if (is_array($data)) {
			foreach ($data as $v) {
				$result[$v['modelid']] = $v;
			}
		}
		F('Model', $result);
		return $result;
	}
}