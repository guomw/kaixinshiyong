<?php
namespace Announce\Model;
use Think\Model;
class AnnounceModel extends Model{
	/*自动验证*/
	protected $_validate = array (
			// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),unique 验证是否唯一
			//self::MUST_VALIDATE 必须验证     self::VALUE_VAILIDATE不为空的时候验证  self::EXISTS_VAILIDATE 表单存在的字段验证
			array('title','','公告标题重复',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),//验证title名称
	);
	
	protected $_auto = array (
			//array(填充字段,填充内容,填充条件,附加规则)
			//填充条件包括： ADD 新增数据的时候处理（默认方式） Update 更新数据的时候处理   ALL所有情况下都进行处理
			//附加规则包括： function 使用函数  callback 回调方法  field 用其它字段填充  string 字符串（默认方式）
			array('updatetime','time',self::MODEL_UPDATE,'function'),
			array('inputtime','time',self::MODEL_INSERT,'function'),
	);
	
	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {
// 		if ($iscreate == TRUE) {
// 			$data = $this->create($data);
// 		}
// 		if (empty($data)) {
// 			$this->error = $this->getError();
// 			return false;
// 		}
		//is_numeric 检测变量是否位数字或数字字符串
		if (isset($data['announceid']) && is_numeric($data['announceid'])) {
			$result = $this->save($data);//更新数据
			if (!$result) {
				$this->error = '更新数据失败';
				return false;
			}
		} else {
			$result = $this->add($data);//添加数据
			if ($result === false) {
				$this->error = '新增数据失败';
				return false;
			}
		}
		return $result;
	}
}