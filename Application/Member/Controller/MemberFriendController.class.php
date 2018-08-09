<?php
namespace Member\Controller;
use Common\Controller\SystemController;
Class MemberFriendController extends SystemController{
    public function _initialize(){
        parent::_initialize();
        $this->userid = cookie('_userid');
        $this->db = model('member');
        $this->pagesize = 10;
		$this->pagecurr = max(1,I('page',1,'intval'));
    }
    
    public function index(){
        //邀请好友记录
       	$count = $this->db->where(array('agent_id'=>$this->userid))->count();
        //以获取的奖励
        $inter_count = get_reword($this->userid,'point');
        //现金奖励
        $money_count0 = get_reword($this->userid,'money');
        //邀请好友列表
        $count = $this->db->where(array('agent_id'=>$this->userid))->count();
		$agent = $this->db->where(array('agent_id'=>$this->userid))->field('userid,phone,phone_status,regdate')->page($this->pagecurr, $this->pagesize)->select();

		$uids = array();
		foreach ($agent as $key => $v) {
			$uids[] = $v['userid'];
			//累积完成订单数
			$sqlMap['status'] = 7;
			$sqlMap['buyer_id'] = $v['userid'];
			//当前用户完成的总 订单数
			$agent[$key]['order_count'] = model('order')->where($sqlMap)->count();
            /*统计当前好友累计为推荐人贡献的金钱奖励*/
            $n1 = model('member_finance_log')->where(array('userid' =>$this->userid,'type' =>'money','only' =>'3-7-'.$this->userid.'-'.$v['userid']))->sum('num');
            $n2 = model('member_finance_log')->where(array('userid' =>$this->userid,'type' =>'money','only' =>array('like','3-6-'.$v['userid'].'-'.$this->userid.'-%')))->sum('num');
            $n3 = model('member_finance_log')->where(array('userid' =>$this->userid,'type' =>'money','only' =>'11-'.$this->userid.'-'.$v['userid']))->sum('num');
            $agent[$key]['total_money'] = $n1+ $n2 +$n3;
            unset($n1);
            unset($n2);
            unset($n3);

            /*统计当前好友累计为推荐人贡献的积分奖励*/
            $n1 = model('member_finance_log')->where(array('userid' =>$this->userid,'type' =>'point','only' =>'3-7-'.$this->userid.'-'.$v['userid']))->sum('num');
            $n2 = model('member_finance_log')->where(array('userid' =>$this->userid,'type' =>'point','only' =>array('like','3-6-'.$v['userid'].'-'.$this->userid.'-%')))->sum('num');
            $n3 = model('member_finance_log')->where(array('userid' =>$this->userid,'type' =>'point','only' =>'11-'.$this->userid.'-'.$v['userid']))->sum('num');
            $agent[$key]['total_point'] = $n1+ $n2 +$n3;
            unset($n1);
            unset($n2);
            unset($n3);
		}

	 	$pages = showPage($count,$this->pagecurr,$this->pagesize);
        //后台设置
        $friend_setting = getcache('friend_setting','member');
        $setting = $friend_setting['setting'];
        $levels = $friend_setting['friend'];
        $setting = $friend_setting['setting'];


        /*每邀请一位好友最多可以获得的奖励 */
        foreach ($setting as $k) {

             if($k['type'] == 'money'){
                $money1 = $money1 + $k['cost'];
             }else{
                $print1 = $print1 + $k['cost'];
             }
        }

         /*统计已发放的邀请好友奖励总额*/
         
         /*统计 邀请人完成手机注册赠送的奖励*/
         $num1 = model('member_finance_log')->where(array('type'=>'money','only' =>array('like','3-7-%')))->sum('num');
         /*统计 被邀请人完成手机注册赠送的奖励*/
         $num2 = model('member_finance_log')->where(array('type'=>'money','only' =>array('like','10-%')))->sum('num');
         /*统计被邀请者完成三笔订单 发放的奖励总额*/
         $num3 = model('member_finance_log')->where(array('type'=>'money','only' =>array('like','13-%')))->sum('num');
         /*统计邀请好友累计订单奖励 发放总金额*/
         $num4 = model('member_finance_log')->where(array('type'=>'money','only' =>array('like','3-6-%')))->sum('num');
         /*统计邀请好友 多级推荐奖励 发放总额*/
         $num5 = model('member_finance_log')->where(array('type'=>'money','only' =>array('like','11-%')))->sum('num');
         $z_num = $num1+ $num2 +$num3 +$num4 +$num5;



        //邀请好友获取奖励排行榜  金钱
        $friend_list = invite_among('money');
        //积分排行榜
        $point_list = invite_among('point');


        $siteUrl =  U('Member/Index/userregister',array('agent_id'=>$this->userid),'',TRUE,TRUE);

        $weixin_url = 'http://'.$_SERVER['HTTP_HOST'].'/yq/'.$this->userid;

        $type =  $friend_setting[fix][type] == 'money' ? '元' :'积分';

        $siteTitle = '我已经在'.C('WEBNAME').'免费试用网领取了好多试用品，通过率高达80%以上，现在喊你一起来'.C('WEBNAME').',立即注册你还可以【免费】得到'.$friend_setting[fix][cost].$type.'新人福袋奖励，快来注册！';
        $summary = $siteTitle;

        include template('member_friend');
    }

    public function test(){
        //邀请好友记录
        $count = $this->db->where(array('agent_id'=>$this->userid))->count();
        //以获取的奖励
        $inter_count = get_reword($this->userid,'point');
        //现金奖励
        $money_count = get_reword($this->userid,'money');
        //邀请的好友列表
        $count = $this->db->where(array('agent_id'=>$this->userid))->count();
        $agent = $this->db->where(array('agent_id'=>$this->userid))->page($this->pagecurr, $this->pagesize)->select();

        $uids = array();
        foreach ($agent as $key => $v) {
            $uids[] = $v['userid'];
            //累积完成订单数
            $sqlMap['status'] = 7;
            $sqlMap['buyer_id'] = $v['userid'];
            //当前用户完成的总 订单数
            $agent[$key]['order_count'] = model('order')->where($sqlMap)->count();
        }
        $pages = showPage($count,$this->pagecurr,$this->pagesize);
        
        //后台设置
        $friend_setting = getcache('friend_setting','member');
        $setting = $friend_setting['setting'];
        $levels = $friend_setting['friend'];


        //邀请好友获取奖励排行榜  金钱
        $friend_list = invite_among('money');
        //积分排行榜
        $point_list = invite_among('point');
        if ($this->userid) {
            include template('share');

        }else{
            include template('rec');

        }
    }
} 