<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Model;
use Think\Model;
Class ErrorModel extends Model {
	/*自动验证*/
	// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	protected $_validate = array (
		array('title','require','页面标题不能为空'),
		array('url','require','错误不能为空'),
		array('content','require','错误不能为空'),
	);

	protected $_auto = array (
		array('dateline', NOW_TIME, self::MODEL_BOTH, 'string'),
		array('ip', 'get_client_ip', self::MODEL_BOTH, 'function'),
	);

	/* 添加或更新数据 */
	public function update($data) {
		$data = $this->create($data);
		if (empty($data)) {
			return false;
		}
		if (isset($data['errorid']) && is_numeric($data['errorid'])) {
			$result = $this->save();
			if (!$result) {
				$this->error = '更新数据失败';
				return false;
			}
		} else {
			$result = $this->add();
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
			$this->error = '您查看的文章不存在';
			return false;
		}
		return $data;
	}

	/**
	 * 按父ID查找菜单子项目
	 * @param  int  $parentid 父菜单ID
	 * @param  bool $self     是否包括自己
	 * @return array
	 */
	public function getmenu($parentid, $self = 0) {
		$parentid = intval($parentid);
		$where = array('parentid'=>$parentid,'display'=>1);
		$order = "listorder ASC, id ASC";
		$result = (array) $this->where($where)->order($order)->select();
		$mymenu = array();
		if($self == 1 && !empty($result)) {
			$mymenu[] = $this->find($parentid);
			$result = array_merge($mymenu,$result);
		}
		return $result;
	}

	/**
	 * 按父ID查找上级菜单
	 * @param integer $parentid   父菜单ID  
	 * @param integer $self  		是否包括他自己
	 */
	public function get_menu_ids_by_id($id, $self = 1) {
		$string = '';
		$pRow = $this->field('id, name, parentid')->find($id);
		if (is_array($pRow)) {
			$string .= $pRow['id'].',';	
			if ($pRow['parentid'] > 0) {
				$string .= $this->get_menu_ids_by_id($pRow['parentid'], 0);
			}
		}
		return rtrim($string, ',');
	}

	/**
	 * 获取菜单深度
	 * @param $id
	 * @param $array
	 * @param $i
	 */
	public function get_level($id,$array=array(),$i=0) {
		foreach($array as $n=>$value){
			if($value['id'] == $id) {
				if($value['parentid'] == '0') return $i;
				$i++;
				return $this->get_level($value['parentid'],$array,$i);
			}
		}
	}

	/**
	 *  检查指定菜单是否有权限
	 * @param array $data menu表中数组
	 * @param int $roleid 需要检查的角色ID
	 */
	public function is_checked($menuid,$roleid) {
		$roles = F('Role');
		$loadRole = unserialize($roles[$roleid]['role_priv']);
		if (empty($roles) || !is_array($loadRole) || empty($loadRole)) {
			return false;
		}
		return in_array($menuid, $loadRole) ? TRUE : FALSE;
	}

	public function getsubmenu($parentid) {
		$array = $this->getmenu($parentid, 1);
		$result = array();
		$string = '';
		foreach ($array as $k => $v) {
			$classname = (strtolower($v['c']) == strtolower(CONTROLLER_NAME) && (strtolower($v['a']) == strtolower(ACTION_NAME))) ? 'class="on"' : '';
			if (!empty($v['data'])) {
				$classname = (strpos($_SERVER['QUERY_STRING'], $v['data']) !== FALSE) ? 'class="on"' : '';
			}
			if($classname) {
				$string .= "<a href='javascript:;' $classname><em>".$v['name']."</em></a><span>|</span>";
			} else {
				$string .= "<a href='".U($v['c'].'/'.$v['a'], $v['data'].'&menuid='.$parentid)."'><em>".$v['name']."</em></a><span>|</span>";
			}
		}
		$string = substr($string,0,-14);
		return $string;
	}

	public function current_pos($id) {
		$r = $this->find($id);
		$str = '';
		if($r['parentid']) {
			$str = $this->current_pos($r['parentid']);
		}
		return $str.$r['name'].' > ';
	}


	public function check_parentid($parentid) {
		$parentid = (int) $parentid;
		return ($parentid >= 0);
	}
}