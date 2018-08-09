<?php
namespace Member\Controller;
use \Member\Controller\InitController;
class MerchantTaskController extends InitController{
	public function _initialize(){
		parent::_initialize();
		//商品来源
		$this->source = model('shop_set')->getField('id, name', TRUE);
		//日赚任务状态
		$this->task_state = C('TASK_STATUS');
		$this->task= D('Task/TaskDay');
		$this->modelid = model('member')->getFieldByUserid($this->userid,'modelid');
		if($this->modelid != 2){
			$this->error('您不是商家会员，无权访问该页面');
		}
	}

	/*任务报名*/
	public function task_add(){
		//获取后台设置
		$setting = model('activity_set')->where(array('activity_type'=>'task'))->getField('key,value');
		extract($setting);
		//判断该活动是否开启
		if($task_isopen != 1){
			$this->error('抱歉，该活动还没有开启');
		}
		$task_num = ($task_num) ? (int) $task_num : 0;
		$task_price = ($task_price) ? sprintf('%.2f', $task_price) : 0;
		$store_infos = model('merchant_store')->where(array('userid'=>$this->userid))->order('is_default DESC')->select();
		if (!$store_infos) {
			$store_info = model('member_merchant')->where(array('userid'=>$this->userid))->select();
		}else{
			$store_info = $store_infos;
		}
		if(IS_POST){
			$info = I('info');
			$info['company_id'] = $this->userid;
			$info['status'] = -3;
			$result = $this->task->update($info);
			if(!$result){
				$this->error($this->task->getError());
			}else{
				//加入任务记录表
				task_log($result,'-3',0,$this->userid,'添加任务');
				$this->success('任务添加成功',U('task_price',array('id'=>$result)));
			}
		}else{
			$show_validator = 1;
			$form = new \Common\Library\form();
			include template('merchant/add_task');
		}
	}
	/**
	 * 任务佣金
	 */
	public function task_price($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('该任务不存在');
		$info = $this->task->getById($id);
		if(empty($info)) $this->error('参数错误');
		extract($info);
		if($this->userid != $company_id || $status != -3) $this->error('您没有权限编辑该商品');
		if(IS_POST){
			//总佣金
			$total = sprintf('%.2f', $goods_number * $goods_price);
			//用户当前余额
			$money = $this->db->getFieldByUserid($this->userid,'money');
			if ($money < $total) $this->error('您的余额不足，请充值！当前余额'.$money.'元,<span style="color:red;">还需充值'.($total-$money).'元</span>');
			//将该商品的状态改为未审核已付款(待审核)
			$rs = $this->task->where(array('id'=>$id))->setField('status',-2);
			if($rs){
				// 将保证金写入商家保证金字段
				model('member_merchant')->where(array('userid'=>$this->userid))->setInc('frozen_deposit',$total);
			}else{
				$this->error('交付失败');
			}
			//将此信息降入记录表中
			$msg = 'id:'.$id.','.$info['title'].'交付任务佣金';
			$sign = '2-3-'.$this->userid.'-'.$id.'-1';
			$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
			if(!$rs){
			    action_finance_log($this->userid,-$total,'money',$msg,$sign,array('goods_id'=>$id));
			    //加入任务记录表
			    task_log($id,'-2',0,$this->userid,'交付任务佣金');
			    $this->success('交付成功');
			}else{
			    $this->success('交付失败，重复操作');
			}
		}else{
			include template('merchant/bailbond_task');
		}
	}
	/**
	 * 任务总佣金
	 */
	public function task_total(){
		$id = (int) I('id');
		if($id < 1) $this->error('参数错误');
		$info = $this->task->getById($id);
		$total = $info['goods_number'] * $info['goods_price'];
		$total = sprintf('%.2f', $total);
		echo $total;
	}
	/**
	 * 任务管理
	 */
	public function task_list(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 5;
		$sqlMap = array();
		$sqlMap['company_id'] = $this->userid;
		$param = I('param.');
		$task_state = (isset($param['task_state'])) ? $param['task_state'] : -99;
		$keyword = $param['keyword'];
		if(IS_GET){
			if($task_state > -99){
				$sqlMap['status'] = $task_state;
			}
			if(!empty($keyword)){
				$sqlMap['keyword|title'] = array("LIKE","%$keyword%");
			}
		}
	    $count = $this->task->where($sqlMap)->count();
		$lists = $this->task->where($sqlMap)->page($pagecurr,$pagesize)->order('inputtime DESC')->select();
		$pages = showPage($count,$pagecurr,$pagesize);
		$v2_pages = v2_page_3($count,$pagesize);
		$form = new \Common\Library\form();
		include template('merchant/task_list');
	}
	/**
	 * 任务日志
	 */
	public function task_log($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		$sqlMap = array();
		$sqlMap['t_id'] = $id;
		$sqlMap['userid'] = $this->userid;
		$result = model('task_log')->where($sqlMap)->select();
		foreach ($result as $k=>$v) {
			$result[$k]['nickname'] = $this->db->getFieldByUserid($v['userid'],'nickname');
		}
		include template('merchant/task_log');
	}
	/**
	 * 任务修改
	 */
	public function task_edit($id = 0){
		$id = (int) $id;		
		if($id < 1) $this->error('该任务不存在');
		//获取后台设置
		$setting = model('activity_set')->where(array('activity_type'=>'task'))->getField('key,value');
		extract($setting);
		//判断该活动是否开启
		if($task_isopen != 1){
			$this->error('抱歉，该活动还没有开启');
		}
		$task_num = ($task_num) ? (int) $task_num : 0;
		$task_price = ($task_price) ? sprintf('%.2f', $task_price) : 0;
		
		$info = $this->task->getById($id);
		if(empty($info)) $this->error('参数错误');
		extract($info);
		if($this->userid != $company_id && $status != -3){
			$this->error('您没有权限编辑改产品');
		}

		$store_infos = model('merchant_store')->where(array('userid'=>$this->userid))->order('is_default DESC')->select();
		if (!$store_infos) {
			$store_info = model('member_merchant')->where(array('userid'=>$this->userid))->select();
		}else{
			$store_info = $store_infos;
		}
		$goods_albums = string2array($goods_albums);
		if(IS_POST){
			$info = I('info');
			$info['id'] = $id;
			$info['company_id'] = $this->userid;
			$info['status'] = -3;
			$result = $this->task->update($info);
			if(!$result){
				$this->error($this->task->getError());
			}else{
				//加入任务记录表
				task_log($id,'-3',0,$this->userid,'修改任务');
				$this->success('任务修改成功',U('task_list'));
			}
		}else{
			$form = new \Common\Library\form();
			include template('merchant/edit_task');
		}
	}
	/**
	 * 删除图片
	 */
	public function delete_image(){
		$name = (string) I('name');
		$id = (int) I('id');
		if($id < 1) $this->error('该任务不存在');
		$info = $this->task->getById($id);
		if(empty($info)) $this->error('参数错误');
		$goods_albums = string2array($info['goods_albums']);
		if($this->userid != $info['company_id']){
			$this->error('您没有权限删除该图片');
		}
		foreach ($goods_albums as $k=>$v) {
			if($v['url'] === $name){
				unset($goods_albums[$k]);
			}
		}
		$goods_album = array2string($goods_albums);
		$result = $this->task->where(array('id'=>$id))->save(array('goods_albums'=>$goods_album));
		if(!$result){
			$this->error('删除失败');
		}
		$this->success('删除成功');
	}
	/**
	 * 用户记录
	 */
	public function person_log($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('该任务不存在');
		$sqlMap = array();
		$sqlMap['tid'] = $id;
		$result = model('task_records')->where($sqlMap)->select();
		foreach ($result as $k=>$v) {
			$result[$k]['nickname'] = $this->db->getFieldByUserid($v['userid'],'nickname');
		}
		include template('merchant/person_records');
	}
}