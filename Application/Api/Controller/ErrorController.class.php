<?php 
namespace Api\Controller;
use \Common\Controller\BaseController;
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
class ErrorController extends BaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Error');
	}
	/**
	 * 获取指定
	 * @author   <jason>
	 */
	public function add($title, $url, $content) {
		$info = array();
		$info['title'] = htmlspecialchars($title);
		$info['url'] = htmlspecialchars($url);
		$info['content'] = htmlspecialchars($content);
		$result = $this->db->update($info);
		if (!$result) {
			$this->error($this->db->getError());
		} else {
			$this->success('操作成功');
		}
	}

	/**
	 * 统计数据更新
	 */
	public function hits($hitsid, $catid) {
		$r = $this->db->where(array('hitsid'=>$hitsid))->find();
		if(!$r) return false;
		$info['views'] = $r['views'] + 1;
		$info['yesterdayviews'] = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $r['dayviews'] : $r['yesterdayviews'];
		$info['dayviews'] = (date('Ymd', $r['updatetime']) == date('Ymd', SYS_TIME)) ? ($r['dayviews'] + 1) : 1;
		$info['weekviews'] = (date('YW', $r['updatetime']) == date('YW', SYS_TIME)) ? ($r['weekviews'] + 1) : 1;
		$info['monthviews'] = (date('Ym', $r['updatetime']) == date('Ym', SYS_TIME)) ? ($r['monthviews'] + 1) : 1;
		$info['updatetime'] = NOW_TIME;
	    return $this->db->where(array('hitsid'=>$hitsid))->save($info);
	}
}