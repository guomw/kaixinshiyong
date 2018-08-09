<?php
namespace Member\Controller;
use Member\Controller\InitController;
class AnnounceController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->message_db = model('message');
		$this->mgroup_db = model('message_group');
	}
	/*网站公告显示*/
	public function announce(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 10;
		$sqlMap = array();
		$type = (int)I('type');
	    $last_day =  strtotime(date('Y-m-d H:i:s', time() - 60*60*24*30));    //最近三十天的记录

		$people =  $this->message_db->where(array('send_to_id'=>$this->userid,'del_type'=>array('NEQ',1),'message_time'=>array('EGT',$last_day)))->count();
	    
	   	$r =model('member')->where(array('userid'=>$this->userid,'modelid'=>1))->field('groupid')->find();


		//$sqlMap['groupid'] = $sr['groupid'];
		//$system = $this->mgroup_db->where(array('groupid'=>$r['groupid']))->count();
	    $delgroup = model('message_data')->where(array('userid'=>$this->userid,'isdelete'=>1,'group_message_id'=>array('gt',0)))->count();
	     $srtotal = $this->mgroup_db->where(array('groupid'=>$r['groupid']))->count();

	   // echo model('message_data')->getLastSql();
		$system = $srtotal-$delgroup;
		if($type == 1){//私信
			$sqlMap['send_to_id'] = $this->userid;
		    $sqlMap['del_type'] = 0;
			$sqlMap['message_time'] = array('EGT',$last_day);
			$announce_count = $this->message_db->where($sqlMap)->count();
			$announce_lists = $this->message_db->where($sqlMap)->page($pagecurr,$pagesize)->order("messageid DESC,message_time DESC")->select();
			foreach ($announce_lists as $k=>$v){
				$rs = model('message_data')->where(array('message_id'=>$v['messageid'],'userid'=>$this->userid))->find();
				$announce_lists[$k]['message_id'] = $rs['message_id'];
				$announce_lists[$k]['isdelete'] = $rs['isdelete'];
			}
			$pages = showPage($announce_count,$pagecurr,$pagesize);
			//$v2_pages = v2_page_2($announce_count,$pagesize);
			$v2_pages = v2_page_3($announce_count,$pagesize);


		}else{//系统消息
			//查出当前会员的会员组
			$sqlMap['groupid'] = $r['groupid'];
			$sqlMap['inputtime'] = array('EGT',$last_day);
			$announce_count = $this->mgroup_db->where($sqlMap)->count();
			$announce_lists = $this->mgroup_db->where($sqlMap)->page($pagecurr,$pagesize)->select();
			foreach ($announce_lists as $k=>$v){
				$rs = model('message_data')->where(array('group_message_id'=>$v['id'],'userid'=>$this->userid))->find();
				$announce_lists[$k]['group_id'] = $rs['group_message_id'];
				$announce_lists[$k]['isdelete'] = $rs['isdelete'];
			}
		
			foreach ($announce_lists as $k=>$v){
			$delete = $v['isdelete'];
			if($delete == 1){
				unset($announce_lists[$k]);
			}

			$count = count($announce_lists);

		   $pages = showPage($count,$pagecurr,$pagesize);
			$v2_pages = v2_page_3($count,$pagesize);
		}

		}
		
		$modelid = model('member')->getFieldByUserid($this->userid,'modelid');
		$SEO = seo(0,'站内信-个人中心');
		include template('announce_lists');
	}
	/**
	 * 标注已读
	 */
	public function read(){
		$ids = (array)$_POST['ids'];
		if (!$ids) exit();
		$sqlMap = array();
		$type = I('type',1);
		if ($type == 1) {	//站内信
			$sqlMap['messageid'] = array('IN',$ids);
			$sqlMap['send_to_id'] = $this->userid;
			$result = $this->message_db->where($sqlMap)->save(array('status'=>1));
			if (!$result) $this->error('标记失败');
			$this->success('标记成功');
		}else{	//	群发短消息
			foreach ($ids as $k => $id) {
				unset($sqlMap);
				$sqlMap['userid'] = $this->userid;
				$sqlMap['group_message_id'] = $id;			
				$count = model('message_data')->where($sqlMap)->count();
				if (!$count) {
					$result = model('message_data')->add($sqlMap);
				}
			}			
			if (!$result) $this->error('标记失败');
			$this->success('标记成功');
		}		
	}
	/**
	 * 删除标注信息
	 */
	public function delete(){
		$ids = I('ids');//需删除的信息
		$type = I('type');
		$ids = rtrim($ids,',');
		$sqlMap = array();
		$attr = explode(',',$ids);
		foreach ($attr as $k=>$v) {
			$sqlMap['userid'] = $this->userid;
			//查看是否有记录
			if($type == 1){ //站内信
				/*$count =  model('message_data')->where(array('message_id'=>$v))->count();
				if($count >0){
					$sqlMap['isdelete'] = 1;
					$result = model('message_data')->where(array('message_id'=>$v))->save($sqlMap);
				}else{
					$info               = array();
					$info['message_id'] = $v;
					$info['userid']     = $this->userid;
					$info['isdelete']   = 1;
					$result = model('message_data')->add($info);
				}*/

				$result = $this->message_db->where(array('send_to_id'=>$this->userid,'messageid'=>$v))->setField('del_type',1);


				//$this->message_db->where(array('send_to_id'=>$this->userid,'messageid'=>$v))->setField('status',1);
			}else{
				$count =  model('message_data')->where(array('group_message_id'=>$v))->count();
				if($count >0){
					$sqlMap['isdelete'] = 1;
					$result = model('message_data')->where(array('group_message_id'=>$v))->save($sqlMap);
				}else{
					$info                     = array();
					$info['group_message_id'] = $v;
					$info['userid']           = $this->userid;
					$info['isdelete']         = 1;
					$result =  model('message_data')->add($info);
				}
			}
		}
		echo ($result) ? 1 : 0 ;
	}
	/*清空所有*/
	public function deleteall(){
		$type = I('type');
		if($type == 1){//私信
			//查出该用户所有的私信
			$info = $this->message_db->where(array('send_to_id'=>$this->userid))->select();
			$this->message_db->where(array('send_to_id'=>$this->userid))->setField('status',1);
			foreach ($info as $k=>$v) {
				$result = $this->message_db->where(array('send_to_id'=>$this->userid,'messageid'=>$v['messageid']))->setField('del_type',1);

				//判断是否有
				/*$isr = model('message_data')->where(array('userid'=>$this->userid,'message_id'=>$v['messageid']))->find();
				if($isr){
					$sqlMap['isdelete'] = 1;
					$result = model('message_data')->where(array('userid'=>$this->userid,'message_id'=>$v['messageid']))->save($sqlMap);
				}else{
					$sqlMap = array();
					$sqlMap['userid'] = $this->userid;
					$sqlMap['message_id'] = $v['messageid'];
					$sqlMap['isdelete'] = 1;
					$result = model('message_data')->add($sqlMap);
				}*/
			}
		}else{
			//查出该用户所有的群发信息
			$groupid = $this->userinfo['groupid'];
			$info = $this->mgroup_db->where(array('groupid'=>$groupid))->select();
			foreach ($info as $k=>$v) {
				$isr = model('message_data')->where(array('userid'=>$this->userid,'group_message_id'=>$v['id']))->find();
				if($isr){
					$sqlMap['isdelete'] = 1;
					$result = model('message_data')->where(array('userid'=>$this->userid,'group_message_id'=>$v['id']))->save($sqlMap);
				}else{
					$sqlMap = array();
					$sqlMap['userid'] = $this->userid;
					$sqlMap['group_message_id'] = $v['id'];
					$sqlMap['isdelete'] = 1;
					$result = model('message_data')->add($sqlMap);
				}
			}
		}
		if(!$result){
			exit('0');
		}else{
			exit('1');
		}
	}


	public function v2_delete(){
		$ids = I('ids');//需删除的信息
		$type = I('type');
		$sqlMap = array();
		$sqlMap['userid'] = $this->userid;
		//查看是否有记录

		if($type == 1){ //站内信
			 $result = $this->message_db->where(array('send_to_id'=>$this->userid,'messageid'=>$ids))->setField('del_type',1);

			/*$count =  model('message_data')->where(array('message_id'=>$ids))->count();
			if($count >0){
				$sqlMap['isdelete'] = 1;
				$result = model('message_data')->where(array('message_id'=>$ids))->save($sqlMap);
			}else{
				$info               = array();
				$info['message_id'] = $ids;
				$info['userid']     = $this->userid;
				$info['isdelete']   = 1;
				$result = model('message_data')->add($info);
			}*/
		}else{
			$count =  model('message_data')->where(array('group_message_id'=>$ids))->count();
			if($count >0){
				$sqlMap['isdelete'] = 1;
				$result = model('message_data')->where(array('group_message_id'=>$ids))->save($sqlMap);
			}else{
				$info                     = array();
				$info['group_message_id'] = $v;
				$info['userid']           = $this->userid;
				$info['isdelete']         = 1;
				$result =  model('message_data')->add($info);
			}
		}
		
		echo ($result) ? 1 : 0 ;
	}
}