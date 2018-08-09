<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Model;
use Think\Model;
Class MemberModel extends Model {
	/*自动验证*/
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		array('modelid', 'check_modelid', '用户名不能为空', self::EXISTS_VALIDATE, 'callback', self::MODEL_BOTH),
		array('username', 'require', '用户名不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		/*array('username', '1, 20', '用户名必须为1到20个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),*/
/*		array('username', '', '用户名已被占用', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
*/		array('password', 'require', '密码不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
		array('password', '6, 20', '密码必须为6到20个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),
		array('pwdconfirm', 'password', '两次密码不一致', self::EXISTS_VALIDATE, 'confirm', self::MODEL_INSERT),
		array('email', 'require', 'Email 地址不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('email', 'email', 'Email 格式不正确', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('email', '', 'Email 地址已被占用', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
	);

	protected $_auto = array (	
		array('regdate', NOW_TIME, self::MODEL_INSERT, 'string',),		
		array('regip', 'get_client_ip', self::MODEL_INSERT, 'function'),
		array('lastip', 'get_client_ip', self::MODEL_BOTH, 'function'),
		array('lastdate', NOW_TIME, self::MODEL_BOTH, 'string'),
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {

		if ($iscreate === TRUE) {
            $data = $this->create($data, $iscreate);
        }
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}

		if (isset($data['password'])) {
			$data['password'] = md5(md5($data['password'].$data['encrypt']));
		}

         //如果开启云平台注册 且是云平台
		if(c('sso_is_open') == 1 && isset($data['userid']) && is_numeric($data['userid']) && isset($data['encrypt'])){
			$result = $this->add($data);
			if ($result === false) {
				$this->error = '新增数据失败';
				return false;
			}else{
				return $result;
			}

		}

		if (isset($data['userid']) && is_numeric($data['userid'])) {
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
		F('member_group', $result);
		return $result;
	}

	/* 检测会员模型是否合法 */
	public function check_modelid($modelid) {
		$models = getcache('model', 'commons');
		return (isset($models[$modelid])) ? TRUE : FALSE;
	}


	/*获取用户收货地址*/
	public function get_address($id){
			$list =  M('Linkage')->field('name')->where(array('linkageid'=>$id))->find();
    		return $list['name'];
		
	}



}