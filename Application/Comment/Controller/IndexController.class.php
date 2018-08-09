<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Comment\Controller;
use \Common\Controller\BaseController;
class IndexController extends BaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Comment');
		$this->categorys = getcache('category','commons');
		$this->models = getcache('model','commons');
	}

	public function index() {
		redirect(U('Usercp/index'));
	}

	public function send() {
		helpers('time');
		$userid = cookie('_userid');
		$catid = (int) $_POST['catid'];
		$id = (int) $_POST['id'];
		if ((int)$userid < 1) {
			$this->error('请登录后评论');
		}
		/* 初始化变量 */
		$modelid = $this->categorys[$catid]['modelid'];
		if($catid < 1 || $id < 1 || $modelid < 1) {
			$this->error('参数错误');
		}
		$info = array();
		$info['commentid'] = 'document_'.$modelid.'_'.$catid.'_'.$id;
		$info['userid'] = $userid;
		$info['username'] = '';
		$info['status'] = 1;
		$info['content'] = addslashes(htmlspecialchars($_POST['content']));
		$info['creat_at'] = NOW_TIME;
		$info['ip'] = get_client_ip();
		$result = $this->db->add($info);
		if (!$result) {
			$this->error('评论发表失败');
		} else {
			$comment_info = $this->db->where(array('id' => $result))->find();
			$comment_info['avatar'] = getavatar($userid);
			$comment_info['creat_at'] = mdate($comment_info['creat_at']);
			$this->success($comment_info);
		}
	}

	/**
	 * 删除评论
	 * @author   <jason>
	 */
	public function del($id) {
		$id = (int) $id;
		$userid = cookie('_userid');
		if ((int) $userid < 1) {
			$this->error('请登录后评论');
		}
		if(empty($id)) $this->error('参数错误');
		$row = $this->db->where(array('id' => $id))->find();
		if (!$row || $row['userid'] != $userid) {
			$this->error('您没有权限删除此评论');
		}
		$this->db->where(array('id' => $id))->delete();
		$this->success('评论删除成功');
	}

	/**
	 * 获取评论
	 */
	public function getlist($page = 0) {
		$page = (int) $page;
		$limit = 5;
		$sqlMap = array();
		$array = $this->db->where($sqlMap)->page($page, $limit)->select();
		if(empty($lists)) $this->error('没有了');
		ob_start();
		include template('item/comment_lists', 'member');
		$message = ob_get_contents();
		ob_end_clean();
		$this->success($message);
	}
}