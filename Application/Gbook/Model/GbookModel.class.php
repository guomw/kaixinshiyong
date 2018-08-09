<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Gbook\Model;
use Think\Model;
/*  */
class GbookModel extends Model 
{
	protected $_validate = array (
		// array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
		array('email', 'email', '邮件地址不合法', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
	);

	protected $_auto = array (
		array('dateline', NOW_TIME, self::MODEL_BOTH, 'string',),		
		array('ip', 'get_client_ip', self::MODEL_BOTH, 'function'),
	);

	public function update($data, $iscreate = TRUE) {
		if ($iscreate === TRUE) $data = $this->verify($data);;
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
				$this->error = '添加数据失败';
				return false;
			}
		}
		return $result;
	}

	/* 数据校验 */
	public function verify($data) {
		$ip = get_client_ip();
		$sqlMap = array();
		$sqlMap['ip'] = $ip;

		$setting = getcache('gbook_setting', 'module');
		$userid = (int) cookie('_userid');
		if (!$setting) {
			return TRUE;
		}
		/* 是否关闭留言板 */
		if (!$setting['gbook_enable']) {
			$this->error = '留言薄尚未开启';
			return false;
		}
		/* 不允许游客留言 */
		if (!$setting['gbook_allow_visitors'] && $userid < 1) {
			$this->error = '您必须登录才可评论';
			return false;
		}
		/* 如果开启验证码 */
		if ($setting['gbook_need_to_validate'] == 1 && !checkVerify($data['verify'])) {
			$this->error = '验证码不正确';
			return FALSE;
		}
		$setting['gbook_submit_interval'] = (int) $setting['gbook_submit_interval'];
		$setting['gbook_submit_total_day'] = (int) $setting['gbook_submit_total_day'];
		/* 两次提交间隔时间 */
		if ($setting['gbook_submit_interval'] > 0) {
			$result = $this->where($sqlMap)->field('dateline, ip')->limit(1)->order('id DESC')->find();
			if ($result && NOW_TIME - $result['dateline'] < $setting['gbook_submit_interval']) {
				$this->error = '您发表的频率太快，请休息一会儿~';
				return FALSE;
			}
		}
		/* 24小时内的限制 */
		if ($setting['gbook_submit_total_day'] > 0) {
			$count = $this->where($sqlMap)->count();
			if ($count >= $setting['gbook_submit_total_day']) {
				$this->error = '您24小时内只能发表'.$setting['gbook_submit_total_day'].'条留言';
				return FALSE;
			}
		}
		return $this->create($data);
	}

	/* 后置操作（发通知） */
	public function after_update() {
		
	}

}