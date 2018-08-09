<?php
namespace Poster\Controller;
use \Common\Controller\BaseController;
class IndexController extends BaseController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('poster');
		$this->s_db = model('poster_stat');
		$this->siteid = I('siteid',1,'intval');
	}
	public function init(){
		
	}
	/**
	 * 统计广告点击次数
	 *
	 */
	public function poster_click(){
		$id = (int) I('id');
		$id = isset($id) ? intval($id) : 0;
		$r = $this->db->where(array('id'=>$id))->find();
		if (!is_array($r) && empty($r)) return false;
		$ip_area = new \Common\Library\IpLocation();
		$ip = get_client_ip();
		$area = $ip_area->getlocation($ip);
		$username = cookie('_userid') ? cookie('_userid') : '';
		if($id) {
			$siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : $this->siteid;
			$sqlMap = array();
			$sqlMap['siteid'] = $siteid;
			$sqlMap['pid'] = $id;
			$sqlMap['spaceid'] = $r['spaceid'];
			$sqlMap['username'] = $username;
			$sqlMap['area'] = $area['country'];
			$sqlMap['ip'] = $ip;
			$sqlMap['referer'] =  safe_replace(HTTP_REFERER);
			$sqlMap['clicktime'] = NOW_TIME;
			$sqlMap['type'] = 1;
			$this->s_db->add($sqlMap);
		}
		$this->db->where(array('id'=>$id))->setInc('clicks',1);
		$setting = string2array($r['setting']);
		if (count($setting)==1) {
			$url = $setting['1']['linkurl'];
		} else {
			$url = isset($_GET['url']) ? $_GET['url'] : $setting['1']['linkurl'];
		}
		header('Location: '.$url);
	}
	/**
	 * php 方式展示广告
	 */
	public function poster_show() {
		if(!$_GET['id']) exit();
		$id = intval($_GET['id']);
		$this->sdb = model('poster_space');
		$now = NOW_TIME;
		$siteid = I('siteid',1,'intval');
		$r = $this->sdb->where(array('siteid'=>$siteid, 'spaceid'=>$id))->find();
		if(!$r) exit();
		if ($r['setting']) $space_setting = string2array($r['setting']);
		$poster_template = getcache('poster_template_'.$siteid, 'commons');
		if ($poster_template[$r['type']]['option']) {
			$where = "`spaceid`='".$id."' AND `disabled`=0 AND `startdate`<='".$now."' AND (`enddate`>='".$now."' OR `enddate`=0) ";
			$pinfo = $this->db->where($where)->order('listorder ASC,id DESC')->select();
			if (is_array($pinfo) && !empty($pinfo)) {
				foreach ($pinfo as $k => $rs) {
					if ($rs['setting']) {
						$rs['setting'] = string2array($rs['setting']);
						$pinfo[$k] = $rs;
					} else {
						unset($pinfo[$k]);
					}
				}
				$p_setting = $pinfo[0]['setting'];
				extract($r);
			} else {
				return true;
			}
		} else {
			$where = " `spaceid`='".$id."' AND `disabled`=0 AND `startdate`<='".$now."' AND (`enddate`>='".$now."' OR `enddate`=0)";
			$pinfo = $this->db->where($where)->order('listorder ASC,id DESC')->select();
			if (is_array($pinfo) && !empty($pinfo)) {
				foreach ($pinfo as $k=>$v){
					if($v['setting']){
						$v['setting'] = string2array($v['setting']);
						$pinfo[$k] = $v;
					}else{
						unset($pinfo[$k]);
					}
				}
				$p_setting = $pinfo[0]['setting'];
			}
			extract($r);
			if (!is_array($pinfo) || empty($pinfo)) return true;
			extract($pinfo, EXTR_PREFIX_SAME , 'p');
		}
		include APP_PATH.'Poster/Fields/'.$type.'.html';
	}
	/**
	 * js传值，统计展示次数
	 */
	public function show_total(){
		$siteid = $_GET['siteid'] ? intval($_GET['siteid']) : $this->siteid;
		$spaceid = $_GET['spaceid'] ? intval($_GET['spaceid']) : 0;
		$id = $_GET['id'] ? intval($_GET['id']) : 0;
		if (!$spaceid || !$id) {
			exit(0);
		} else {
			$this->show_stat($siteid, $spaceid, $id);
		}
	}
	public function show_stat(){
		$poster = getcache('poster', 'commons');
		//产看配置文件中 是否需要统计广告次数 enablehits=1 统计  反之，不统计
		if($poster['enablehits']==0) return true;
		$spaceid = (int) $spaceid;
		$id = (int)$id;
		if(!$id) return false;
		if(!$siteid || !$spaceid) {
			$r = $this->db->where(array('id'=>$id))->field('siteid, spaceid')->find();
			$siteid = $r['id'];
			$spaceid = $r['spaceid'];
		}
		$ip = get_client_ip();
		$ip_area = new \Common\Library\IpLocation();
		$area = $ip_area->getlocation($ip);
		$username = cookie('_userid') ? cookie('_userid') : '';
		$this->db->where(array('id'=>$id))->setInc('hits',1);
		$sqlMap = array();
		$sqlMap['pid'] = $id;
		$sqlMap['siteid'] = $siteid;
		$sqlMap['spaceid'] = $spaceid;
		$sqlMap['username'] = $username;
		$sqlMap['area'] = $area['country'];
		$sqlMap['ip'] = $ip;
		$sqlMap['referer'] = safe_replace(HTTP_REFERER);
		$sqlMap['clicktime'] = NOW_TIME;
		$sqlMap['type'] = 0;
		$this->s_db->add($sqlMap);
		return true;
	}
}