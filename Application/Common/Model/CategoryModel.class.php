<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class CategoryModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		array('catname', 'require', '栏目名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	protected $_auto = array (
		array('items', 0, self::MODEL_INSERT, 'string'),
		array('setting', 'array2string', self::MODEL_BOTH, 'function'),
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {		
		if ($iscreate == TRUE) {
			$data = $this->create($data);
		}
		if (empty($data)) {
			return false;
		}
		if (isset($data['catid']) && is_numeric($data['catid'])) {
			$result = $this->save($data);
			if (!$result) {
				$this->error = '更新栏目失败';
				return false;
			}
		} else {
			$result = $this->add($data);
			if ($result === false) {
				$this->error = '新增栏目失败';
				return false;
			}
		}
		return $result;
	}

	/* 读取单条记录 */
	public function detail($id, $field = TRUE) {
		$data = $this->field($field)->find($id);
		if (!is_array($data)) {
			$this->error = '您查看的文章不存在';
			return false;
		}
		return $data;
	}

	public function build_cache() {
        $categorys = array();
        $models = getcache('model', 'commons');
        foreach ($models as $modelid => $model) {
            $datas = $this->where(array('modelid'=>$modelid))->getField('catid,type,items');
            $array = array();
            foreach ($datas as $r) {
                if($r['type']==0) $array[$r['catid']] = $r['items'];
            }
            setcache('category_items_'.$modelid, $array,'commons');
        }
        $array = $this->select();
        foreach ($array as $v) {
            $categorys[$v['catid']] = $v;
        }
        setcache('category', $categorys, 'commons');		
		return TRUE;
	}

}