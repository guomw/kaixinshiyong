<?php
namespace Order\Controller;
use \Admin\Controller\InitController;
class CheckController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('order');
		$this->pagecurr = max(1,I('page','','intval'));
		$this->pagesize = 10;
		$this->userid = session('userid');
		//亚马逊等级
		$this->grade = array('安全','一般','危险');
		//商家类型
		$group = getcache('merchant_group','member');
		$attr = array();
		foreach ($group as $k=>$v) {
			$attr[] = $v['name'];
		}
		$this->group = $attr;
	}
	/**
	 * 审核试用商品列表
	 */
	public function init(){
	    $info = I('param.');
		//所属专员
		$roleid = session('roleid');
		$sqlMap = array();
        $sqlMap['status'] = array('EQ','1');
        //如果当前是招商专员
        if(session('roleid') ==6){
            $ids = model('member')->where(array('agent_id'=>$this->userid))->getField('userid',true);
            $sqlMap['seller_id'] = array('in',$ids);
            $attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>6,'userid' =>cookie('userid')))->select();
            //只查询当前专员商品下授权待审的的
            $condition = array();
            $condition['goods_impower'] = array('EQ',1);
            $gidss = model('product_trial')->where(array('goods_impower'=>array("EQ", 1),'company_id' =>array('in',$ids)))->where($condition)->getfield('id',true);
            $sqlMap['goods_id'] = array("IN", $gidss);

        }else{
            $attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>6))->select();
            $attract = (isset($search['attract'])) ? $search['attract'] : -99;
            //所属专员查询
            if(isset($info['attract']) && $info['attract'] > -99){
                //查出该专员下的商家id
                $ids = model('member')->where(array('agent_id'=>$info['attract']))->getField('userid',true);
                $sqlMap['seller_id'] = array('in',$ids);
                $condition['goods_impower'] = array('EQ',1);
                $gidss = model('product_trial')->where(array('goods_impower'=>array("EQ", 1),'company_id' =>array('in',$ids)))->getfield('id',true);
                $sqlMap['goods_id'] = array("IN", $gidss);
            }else{
            	$gidss = model('product_trial')->where(array('goods_impower'=>array("EQ", 1)))->where($condition)->getfield('id',true);
                $sqlMap['goods_id'] = array("IN", $gidss);
            }


        }

        if ($info['search']) {
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 下单时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['create_time'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
				$sqlMap['create_time'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['create_time'] = array("ELT", $info['end_time']);
				}
			}

			$info['type'] = (int) $info['type'];
			$info['keyword'] = trim($info['keyword']);
			if ($info['keyword']) {
				switch ($info['type']) {
					case '5': //订单ID
						$sqlMap['id'] = $info['keyword'];
						continue;
					case '4': //订单号
						$sqlMap['trade_sn'] = array("LIKE", "%".$info['keyword']."%");
						continue;
					case '2': //会员邮箱
						$uids = model('member')->where(array('email'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
						$sqlMap['buyer_id'] = array("IN", $uids);
						continue;
					case '3': //商家店铺
						$uids = model('member_merchant')->where(array('store_name'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
						$sqlMap['seller_id'] = array("IN", $uids);
						continue;
					case '1': //商品标题
						$gids = model('product')->where(array('title'=>array("LIKE", "%".$info['keyword']."%"),'goods_impower' =>1))->getfield('id',true);
						$sqlMap['goods_id'] = array("IN", $gids);
						continue;
				}
			}
		}

		$sqlMap['act_mod'] = 'trial';
		$count = model('order')->where($sqlMap)->count();
		$order_lists = model('order')->where($sqlMap)->page($this->pagecurr,$this->pagesize)->order('id desc')->select();
		foreach ($order_lists as $k=>$v){
		    //通过goods_id查出商品是否可以待审
		    $goods_impower =  model('product')->alias('p')->join(C('DB_PREFIX').'product_'.$v['act_mod'].' AS t ON p.id=t.id')->field('t.goods_impower')->where(array('p.id'=>$v['goods_id']))->find();
			if($goods_impower['goods_impower'] == 0){
			    unset($order_lists[$k]);
			    continue;
			}
			
			//用户信息
			$member_info = model('member')->getByUserid($v['buyer_id']);
			$member_info['avatar'] = getavatar($v['buyer_id']);
			//用户试用次数
			$trial = array();
			$trial['buyer_id'] = $v['buyer_id'];
			$trial['goods_id'] = $v['goods_id'];
			$trial['act_mod'] = $v['act_mod'];
			$trial['status'] = 7;
			$member_info['trial_num'] = $this->db->where($trial)->count();
			//用户真实姓名
			$r = get_personal($v['buyer_id'],'identity');
			$member_info['realname'] = $r['name'];
			//用户绑定亚马逊账号信息
			$taobao = model('member_bind')->where(array('userid'=>$v['buyer_id'],'status'=>array('NEQ',2)))->find();
			$member_info['taobao'] = $taobao;
			
			$order_lists[$k]['member_info'] = $member_info;
			//商家信息
			$merchant = model('member')->alias('m')->join(C('DB_PREFIX').'member_merchant AS t ON m.userid = t.userid')->where(array('m.userid'=>$v['seller_id']))->find();
			//商家专员
			$merchant['role_name'] = model('admin')->getFieldByUserid($merchant['agent_id'],'username');
			$order_lists[$k]['merchant_info'] = $merchant;
			
			//商品信息
			$factory = new \Product\Factory\product($v['goods_id']);
			$order_lists[$k]['product_info'] = $factory->product_info;
		}
		
		$pages = page($count, $this->pagesize);
		$form = new \Common\Library\form();
		$format = new \Common\Library\format();
		include $this->admin_tpl('order_check_list');
	}
	
	/**
	 * 审核资格通过
	 * @param int $oid订单id
	 */
	public function pass($oid = 0, $state = 0 , $mod = ''){
		$oid = (int) $oid;
		if( $oid < 1) $this->error('该订单不存在');
         $factory = new \Product\Factory\order($oid);
        //判断库存
        if($factory->product_info['goods_number'] - $factory->product_info['already_num'] <= 0){
        	$this->error('该产品库存为0，请追加');
        }
        if ((int)$state == 1) {
            $result = $factory->set_status(2,'确认免费试用资格通过');
            if ($result) {
                runhook('order_check_trade_no',array('order_id' => $oid, 'userid' => $factory->order_info['buyer_id'],'result' => 1,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod']));
                model('product')->where(array('id' => $factory->product_info['id']))->setInc('already_num');
            }
        }else{
            $result = $factory->set_status(0,'商家确认免费试用资格不通过');
            if ($result) {
                runhook('order_check_trade_no',array('order_id' => $oid, 'userid' => $factory->order_info['buyer_id'],'result' => 0,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod']));
            }
        }
        if(!$result) $this->error($factory->getError());
        // 跳转到  待审核资格   页面
        $this->success('操作成功',U('Order/Check/init',array('act_mod' => $factory->product_info['mod'])));
   }
}