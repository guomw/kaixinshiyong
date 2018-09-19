<?php
namespace Member\Controller;
use \Admin\Controller\InitController;
class CheckController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('member_attesta');
	}
	/*实名认证*/
	public function real_name(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 10;
		$sqlMap = array();
		$sqlMap['type'] = 'identity';
		$info = I('param.');
		if ($info['search']) {
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 注册时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['inputtime'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
					$sqlMap['inputtime'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['inputtime'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			$info['status'] =  $info['status'];
			if ($info['status'] != 5) {
				$sqlMap['status'] = $info['status'];
			}
			$keyword = $info['keyword'];
			if (trim($keyword)) {
				$sqlMap['infos'] = array("LIKE","%$keyword%");
			}
		}
		
		$count = $this->db->where($sqlMap)->count();
		$lists = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->order('id DESC')->select();
		foreach ($lists as $k=>$v){
			$lists[$k]['username'] = M('Member')->getFieldByUserid($v['userid'],'nickname');
			if($lists[$k]['username']=='') $lists[$k]['username'] = M('Member')->getFieldByUserid($v['userid'],'email');
			$infos = string2array($v['infos']);
			$lists[$k]['realname'] = $infos['name'];
			$lists[$k]['id_number'] = $infos['id_number'];
			$lists[$k]['face_img']=$infos['face_img'];
            $lists[$k]['back_img']=$infos['back_img'];
            $lists[$k]['person_img']=$infos['person_img'];
			$lists[$k]['img_url_up'] = $infos['img_url'][1];
			$lists[$k]['img_url_down'] = $infos['img_url'][2];
		}
		$pages = page($count,$pagesize);
		$form = new \Common\Library\form();
		//$show_header = true;
		include $this->admin_tpl('member_realname_lists');
	}
	
	/*删除*/
	public function delete($ids = array()){
		$ids = (array) $ids;
		if(empty($ids)) $this->error('参数错误');
		foreach ($ids as $k=>$id){
			$id = (int) $id;
			$this->db->where(array('id'=>$id))->delete();
		}
		$this->success('删除成功');
	}

	/*审核通过*/
	public function check($ids = array()){
		$ids = (array) $ids;
		if(empty($ids)) $this->error('参数错误');
		if(submitcheck('dosubmit')){
			foreach ($ids as $k=>$v){
				$this->db->where(array('id'=>$v))->setField('status',1);
				$uid = $this->db->where(array('id'=>$v))->getField('userid');
				runhook('member_attesta_name',array('userid' =>$uid,'id' =>$v));


				//实名认证给用户加积分
                $modelid = model('member')->where('userid='.$uid)->getField('modelid');
                if($modelid==1)
                {
                    $reward = model("task")->where('type="name"  and task_status=1')->getField('task_reward');
//                    model('member')->where('userid='.$uid)->setInc("point",$reward);


                    if($reward>0) action_finance_log($uid, $reward,'point', '实名认证', '');
                }

			}
			$this->success('审核成功');
		}else{
			$this->error('请勿非法提交');
		}
	}

	/* 审核不通过*/
	public function uncheck($ids = array()){
		$ids = (array) $ids;
		if(empty($ids)) $this->error('参数错误');
		if(submitcheck('dosubmit')){
			foreach ($ids as $k=>$v){
				$this->db->where(array('id'=>$v))->setField('status',-1);
				$uid = $this->db->where(array('id'=>$v))->getField('userid');
				/*发送站内信和邮件*/
                
			}
			$this->success('操作成功');
		}else{
			$this->error('请勿非法提交');
		}
	}

	/*品牌认证*/
	public function brand(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 10;
		$sqlmap = array();
		$sqlmap['type'] = 'brand';
		$info = I('param.');
		if ($info['search']) {
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 注册时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlmap['inputtime'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
					$sqlmap['inputtime'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlmap['inputtime'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			$info['status'] =  $info['status'];
			if ($info['status'] != 5) {
				$sqlmap['status'] = $info['status'];
			}
			$keyword = $info['keyword'];
			if (trim($keyword)) {
				$sqlmap['infos'] = array("LIKE","%$keyword%");
			}
		}
		$count = $this->db->where($sqlmap)->count();
		$lists = $this->db->where($sqlmap)->page($pagecurr,$pagesize)->order('id DESC')->select();
		foreach ($lists as $k=>$v) {
			$lists[$k]['username'] = M('Member')->getFieldByUserid($v['userid'],'nickname');
			$infos = string2array($v['infos']);
			$lists[$k]['img_logo'] = $infos['img_logo'];
			$lists[$k]['img_auth'] = $infos['img_auth'];
			$lists[$k]['content'] = $infos['content'];
		}
		$pages = page($count,$pagesize);
		$form = new \Common\Library\form();
		include $this->admin_tpl('member_brand_lists');
	}
}