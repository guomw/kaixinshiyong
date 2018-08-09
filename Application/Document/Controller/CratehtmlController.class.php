<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Document\Controller;
use \Admin\Controller\InitController;
Class CratehtmlController extends InitController 
{
	/* initialize */
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Document');
		$this->categorys = getcache('category','commons');
		$this->models = getcache('model','commons');
	}

	/**
	 * 批量更新URL
	 * @author   <jason>
	 */
	public function update_urls() {
		if(getgpc('dosubmit')) {
			$page = max(1, (int) I('page'));
			$this->url = new \Common\Library\url();
			$params = I('param.');
			extract($params, EXTR_SKIP);
			$modelid = (int) $modelid;
			/* 当已选择模型 */
			if ($modelid) {
				$tableName = $this->models[$modelid]['tablename'];
				$this->db = M($tableName);
				if($type == 'lastinput') {
					$offset = 0;
				} else {
					$page = max(intval($page), 1);
					$offset = $pagesize*($page-1);
				}
				$sqlMap = array();
				$sqlMap['status'] = 1;
				if (is_array($catids) && !empty($catids) && $catids[0] != 0) {
					$sqlMap['catid'] = array("IN", $catids);
				}
				if (!isset($first)) {
					$first = 1;
				} else {
					$first = 0;
				}
				if($type == 'lastinput' && $number) {
					$offset = 0;
					$pagesize = $number;
					$order = 'DESC';
				} elseif($type == 'date') {
					if($fromdate) {
						$fromtime = strtotime($fromdate.' 00:00:00');
						$sqlMap['inputtime'] = array("EGT", $fromtime);
					}
					if($todate) {
						$totime = strtotime($todate.' 23:59:59');
						$sqlMap['inputtime'] = array("ELT", $totime);
					}
				} elseif($type == 'id') {
					$fromid = intval($fromid);
					$toid = intval($toid);
					if ($fromid) $sqlMap['id'] = array('EGT', $fromid);
					if ($toid) $sqlMap['id'] = array('ELT', $toid);
				}

				if(!isset($total) && $type != 'lastinput') {
					$total = $this->db->where($sqlMap)->count();
					$pages = ceil($total/$pagesize);
					$start = 1;
				}

				$rs = $this->db->where($sqlMap)->limit($offset, $pagesize)->order("`id` ".$order)->select();
				foreach($rs as $r) {
					if($r['islink'] || $r['upgrade']) continue;
					//更新URL链接
					$this->urls($r['id'], $r['catid'], $r['inputtime'], $r['prefix']);

				}
				if($pages > $page) {
					$page++;
					$http_url = get_url();
					$creatednum = $offset + count($data);
					$percent = round($creatednum/$total, 2)*100;
					
					$message = '共需更新 <font color="red">'.$total.'</font> 条信息 - 已完成 <font color="red">'.$creatednum.'</font> 条（<font color="red">'.$percent.'%</font>）';
					if ($start) {
						$arr = array(
							'dosubmit' => 1,
							'type'     => $type,
							'first'    => $first,
							'fromid'   => $fromid,
							'toid'     => $toid,
							'fromdate' => $fromdate,
							'todate'   => $todate,
							'pagesize' => $pagesize,
							'page'     => $page,
							'pages'    => $pages,
							'total'    => $total,
							'modelid'  => $modelid,
						);
						$forward = U('update_urls', $arr);
					}
				} else {
					$message = '更新完成！ ...';
					$forward = U('update_urls');
				}
				$this->error($message,$forward,2);

			}
			/* 未选择模型 */
			else {
				//当没有选择模型时，需要按照栏目来更新
				if(!isset($set_catid)) {
					if($catids[0] != 0) {
						$update_url_catids = $catids;
					} else {
						foreach($this->categorys as $catid=>$cat) {
							if($cat['child'] || $cat['type']!=0) continue;
							$update_url_catids[] = $catid;
						}
					}
					setcache('update_url_catid'.'-'.session('userid'), $update_url_catids,'document');
					$message = '开始更新';
					$forward = U('update_urls', array('set_catid' => 1, 'pagesize' => $pagesize, 'dosubmit' => 1));
					$this->error($message,$forward);
				}

				$catid_arr = getcache('update_url_catid'.'-'.session('userid'), 'document');
				$autoid = $autoid ? intval($autoid) : 0;
				if(!isset($catid_arr[$autoid])) {
					$this->error('更新完成！ ...', U('update_urls'));
				}
				$catid = $catid_arr[$autoid];
				$modelid = $this->categorys[$catid]['modelid'];
				$tableName = $this->models[$modelid]['tablename'];
				$this->db = M($tableName);

				$page = max(1, intval($page));
				$offset = $pagesize*($page-1);
				$sqlMap = array();
				$sqlMap['status'] = 1;
				$sqlMap['catid'] = $catid;
				if(!isset($total)) {
					$total = $this->db->where($sqlMap)->count(); 
					$pages = ceil($total/$pagesize);
					$start = 1;
				}

				$rs = $this->db->where($sqlMap)->limit($offset, $pagesize)->order("`id` ".$order)->select();
				foreach($rs as $r) {
					if($r['islink'] || $r['upgrade']) continue;
					//更新URL链接
					$this->urls($r['id'], $r['catid'], $r['inputtime'], $r['prefix']);
				}

				if($pages > $page) {
					$page++;
					$http_url = get_url();
					$creatednum = $offset + count($rs);
					$percent = round($creatednum/$total, 2)*100;
					$message = '【'.$this->categorys[$catid]['catname'].'】 '.'有 <font color="red">'.$total.'</font> 条信息 - 已完成 <font color="red">'.$creatednum.'</font> 条（<font color="red">'.$percent.'%</font>）';
					$arr = array(
						'type' => $type,
						'dosubmit' => 1,
						'first' => 1,
						'fromid' => $fromid,
						'toid' => $toid,
						'fromdate' => $fromdate,
						'todate' => $todate,
						'pagesize' => $pagesize,
						'page' => $page,
						'pages' => $pages,
						'total' => $total,
						'autoid' => $autoid,
						'set_catid' => 1
					);
					$forward = U('update_urls', $arr);
				} else {
					$autoid++;
					$message = '正在更新【'.$this->categorys[$catid]['catname']."】 ...";
					$forward = U('update_urls', array('set_catid' => 1, 'pagesize' => $pagesize, 'dosubmit' => 1, 'autoid' => $autoid));
				}
				$this->error($message, $forward, 1);
			}
		} else {
			$modelid = (int) I('modelid');
			$tree = new \Common\Library\tree();
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$categorys = array();
			if(!empty($this->categorys)) {
				foreach($this->categorys as $catid=>$r) {
					if($r['type'] != 0 && $r['child'] == 0) continue;
					if($modelid && $modelid != $r['modelid']) continue;
					$r['disabled'] = $r['child'] ? 'disabled' : '';
					$categorys[$catid] = $r;
				}
			}
			$str  = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";

			$tree->init($categorys);
			$string .= $tree->get_tree(0, $str);

			$show_header = $show_dialog  = '';
			$form = new \Common\Library\form();
			include $this->admin_tpl('update_urls');
		}
	}

	private function urls($id, $catid= 0, $inputtime = 0, $prefix = ''){
		$urls = $this->url->show($id, 0, $catid, $inputtime, $prefix,'','edit');
		//更新到数据库
		$url = $urls[0];
		$this->db->where(array('id'=>$id))->save(array('url'=>$url));
		return $urls;
	}	
}