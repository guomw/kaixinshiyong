<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Order\Model;
use Think\Model;
Class OrderModel extends Model {
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

}