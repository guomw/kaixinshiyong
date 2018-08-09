<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Message\Model;
use Think\Model;
Class MessageModel extends Model 
{
	protected $_auto = array (
		// array(完成字段1,完成规则,[完成条件,附加规则]),		
		array('message_time', NOW_TIME, self::MODEL_BOTH, 'string',),		
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
		if (isset($data['messageid']) && is_numeric($data['messageid'])) {
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