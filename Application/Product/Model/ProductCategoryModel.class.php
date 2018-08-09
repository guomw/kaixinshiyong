<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Model;
use Think\Model;
Class ProductCategoryModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		array('catname', 'require', '栏目名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	protected $_auto = array (
		array('items', 0, self::MODEL_INSERT, 'string'),
		array('setting', 'array2string', self::MODEL_BOTH, 'function'),
	);

	public function build_cache() {
        $categorys = array();
        $array = $this->select();
        foreach ($array as $v) {
            $categorys[$v['catid']] = $v;
        }
        setcache('product_category', $categorys, 'commons');		
		return TRUE;
	}

}