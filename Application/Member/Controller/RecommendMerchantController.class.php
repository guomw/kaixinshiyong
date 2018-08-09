<?php
namespace Member\Controller;
use \Admin\Controller\InitController;
class RecommendMerchantController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->pagecurr = max(1, I('page', 0, 'intval'));
		$this->pagesize = 10;
	}
	public function init(){
		$sqlmap = array();
		$sqlmap['isrecommend'] = 1;
		if (IS_GET) {
			$info = I('get.');
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 注册时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlmap['regdate'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
					$sqlmap['regdate'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlmap['regdate'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			$info['status'] = (int) $info['status'];
			if ($info['status'] > 0) {
				$sqlmap['islock'] = $info['status'];
			}
			/* 会员组 */
			$info['groupid'] = (int) $info['groupid'];
			if ($info['groupid'] > 0) {
				$sqlmap['groupid'] = $info['groupid'];
			}
			/* 关键字搜索类型 */
			$info['type'] = (int) $info['type'];
			if (trim($info['keyword'])) {
				switch ($info['type']) {
					case '1': //用户名
						$sqlmap['username'] = array("LIKE", "%".$info['keyword']."%");
						break;
					case '2': // 用户ID
						$sqlmap['userid'] = array("LIKE", "%".intval($info['keyword'])."%");
						break;
					case '3': // 注册邮箱
						$sqlmap['email'] = array("LIKE", "%".$info['keyword']."%");
						break;
					case '4': // 注册IP
						$sqlmap['regip'] = array("LIKE", "%".$info['keyword']."%");
						break;
					default:
						$sqlmap['nickname'] = array("LIKE", "%".$info['keyword']."%");
						break;
				}
			}
		}
		$count = model('member_merchant')->where($sqlmap)->count();
		$memberlist = model('member_merchant')->where($sqlmap)->page($this->pagecurr,$this->pagesize)->select();
		$pages = page($count,$this->pagesize);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('member_recommend_add').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('member_recommend_add'));
		$form = new \Common\Library\form();
		include $this->admin_tpl('recommend_merchant_lists');
	}
	
	public function add(){
		$sqlMap = array();
		$sqlMap['isrecommend'] = 0;
		$count = model('member_merchant')->where($sqlMap)->count();
		$infos = model('member_merchant')->where($sqlMap)->page($this->pagecurr, $this->pagesize)->select();
		$pages = page($count, $this->pagesize);
		$form =  new \Common\Library\form();
		$show_header = TRUE;
		include $this->admin_tpl('recommend_merchant_dialog');
	}
	/*推荐*/
	public function recommend(){
		$id = (int) I('company_id_text');
		//判断该商家是否已经是推荐后的
		$rs = model('member_merchant')->where(array('userid'=>id,'isrecommend'=>1))->find();
		if($rs){
			$this->error('该商家已经推荐过，请重新选取');
		}else{
			$result = model('member_merchant')->where(array('userid'=>$id))->setField('isrecommend',1);
			if($result){
				$this->success('推荐商家成功','javascript:close_dialog();');
			}else{
				$this->error('推荐商家失败');
			}
		}
	}
	/*取消推荐*/
	public function delete_recommend($userid = array()){
		$userid = (array) $userid;
		if(empty($userid)) $this->error('参数错误');
		foreach ($userid as $k=>$v) {
			model('member_merchant')->where(array('userid'=>$v))->setField('isrecommend',0);
		}
		$this->success('取消指定商家成功');
	}
}