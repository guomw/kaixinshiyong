<?php
namespace Member\Controller;
use Common\Library\template;
use \Member\Controller\InitController;
class OrderController extends InitController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('order');
        $this->status = array('已关闭','已抢购', '已确认','待审核', '审核失败', '审核通过', '申诉中', '已完成');
    }
    /**
     * 订单管理 根据订单类型
     */
    public function order_manage($mod=''){
        $mod = (string) $mod;
        //用户绑定淘宝账号的安全等级
        $level = array('安全','一般','危险');
        //进行中的活动
        $sql = array();
        $sql['mod'] = $mod;
        $sql['status'] = array("in","1,2");
        $sql['company_id'] = $this->userid;
        $activity_lists = model('product')->where($sql)->getField('id,title');
        $sqlMap = array();
        $pagecurr = max(1,I('page',0,'intval'));
        $pagesize = 20;
        $param = I('param.');
        $status = (isset($param['status'])) ? $param['status'] : -99;
        $type = $param['type'];
        $keyword = $param['keyword'];
        if(IS_GET){
            if($status > -99){
                $sqlMap['status'] = $status;
            }
            if(!empty($keyword)){
                switch ($type){
                    case 1://淘宝账号
                        $account = model('member_bind')->where(array('account'=>$keyword))->getField('bind_id');                        
                        $sqlMap['bind_id'] = $account;
                        break;
                    case 2://订单号
                        $sqlMap['trade_sn'] = array("LIKE","%$keyword%");
                        break;
                }
            }
            if(!empty($param['activity']) && $param['activity'] > -99){
                $sqlMap['goods_id'] = $param['activity'];
            }
        }
        $sqlMap['act_mod'] = $mod;
        $sqlMap['seller_id'] = $this->userid;
        $count = $this->db->where($sqlMap)->count();
        $order_list = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->order('id DESC')->select();
        foreach ($order_list as $k=>$v) {
            //查询用户信息
            $order_list[$k]['userinfo'] = getUserInfo($v['buyer_id']);
            //绑定的淘宝账号
            if (!empty($v['bind_id'])) {
                $order_list[$k]['taobao'] = model('member_bind')->getById($v['bind_id']);
            }
            //活动名称
            $order_list[$k]['title'] = model('product')->getFieldById($v['goods_id'],'title');
        }
        $pages = page($count,10);
        include template('merchant/'.$mod.'_order');
    }
    /**
     * 订单管理  根据good_id
     */
    public function manage() {
        if(DEFAULT_THEME == 'wap'){
            redirect(U('Member/Profile/index'));
            exit;
        }

        $SEO = seo('','订单管理');
        $sqlmap = array();
        if($this->userinfo['modelid'] == 2) {
            $state = I('state', 3, 'intval');
            $mod = I('mod', 'rebate');
            $goods_id = I('goods_id', 0, 'intval');
            if($goods_id < 1) {
                $this->error('请勿非法访问');
            }
            $sqlmap['goods_id'] = $goods_id;
            $sqlmap['seller_id'] = $this->userid;
            if ($state > -1) $sqlmap['status'] = $state;
            $factory = new \Product\Factory\product($goods_id);
            $pro = $factory->product_info;
            $tpl = 'merchant/order_manage';
        } else {
            $state = I('state', -1, 'intval');
            $mod = I('mod', 'rebate');
            $sqlmap['buyer_id'] = $this->userid;
            $sqlmap['act_mod'] = $mod;
            if($state > -1) {
                $sqlmap['status'] = $state;
            }
            $tpl = 'buyer/order_manage';
        }
        $states = $this->status;
        if ($mod == 'rebate') {
            unset($states['1']);
            $states['2'] = '已抢购';
        }else{
            $states['1'] = '已申请';
            $states['2'] = '已获得资格';
            unset($states['5']);
        }
        $count = $this->db->where($sqlmap)->count();
        $lists = $this->db->where($sqlmap)->page(PAGE, 10)->order("id DESC")->select();
        foreach ($lists as $key => $value) {
            $value['product_info'] = model('product/product')->detail($value['goods_id']);
            $value['store_name'] = model('member_merchant')->getFieldByUserid($value['seller_id'], 'store_name');
            $value['contact_want'] = model('member_merchant')->getFieldByUserid($value['seller_id'], 'contact_want');
            if (!$value['contact_want']) $value['contact_want'] = '';
            // 如果有申诉记录就查询出最后一次的 (用于会员查看申诉)
            $value['appeal_id'] = model('appeal')->order('id DESC')->getFieldByOrder_id($value['id']);
            // 查出该商品的申诉信息
            $value['appeal'] = model('appeal')->where(array('order_id'=>$value['id']))->find();
            // 查询该商品是否已晒单
            $value['report_count'] = model('report')->where(array('goods_id'=>$value['goods_id'],'userid'=>$this->userid))->count();
            // 买家平台账号
            $value['nickname'] = model('member')->where(array('userid'=>$value['buyer_id']))->getField("nickname");
            //绑定的淘宝账号
            if (!empty($value['bind_id'])) {
                $value['bind_account'] = model('member_bind')->where(array('id'=>$value['bind_id']))->getField('account');

            }
            // 买家是否有试用报告
            if ($value['act_mod'] == 'trial'){
                $value['trial_report'] = model('trial_report')->where(array('order_id'=>$value['id']))->find();
            }
            
            $lists[$key] = $value;
        }
        if (!empty($lists)) {
            $factory = new \Product\Factory\product($lists[0]['goods_id']);
            $act_config = $factory->getConfig();
        }
        $pages = showPage($count, PAGE, 10);
        include template($tpl);
    }


    
    /* 查看买家信息 */
    public function userInfo() {
        $userid = (int) $_GET['userid'];
        $order_id = (int)$_GET['id'];
        if ($userid < 1)   $this->error('该会员信息有误！');
        if ($order_id < 1)   $this->error('该订单信息有误！');
        $userinfo = member_info($userid);
        $order_info = $this->db->field('act_mod,bind_id')->find($order_id);
        // 总抢购/申请试用 次数
        $sqlmap = array();
        $sqlmap['buyer_id'] = $userid;
        $sqlmap['act_mod'] = $order_info['act_mod'];
        $userinfo['order_count'] = model('order')->where($sqlmap)->count();
        // 已完成的抢购/申请试用 次数
        $sqlmap['status'] = 7;
        $userinfo['already_over'] = model('order')->where($sqlmap)->count();
        // 上次抢购/申请试用时间
        $sqlmap['status'] = array('NEQ',0);
        $userinfo['last_time'] = dgmdate(model('order')->where($sqlmap)->order('id DESC')->getfield('create_time'),'Y-m-d');
        //  精华报告
        $sqlmap['appraised'] = 1;
        $sqlmap['act_mod'] = 'trial';
        $sqlmap['status'] = 7;
        $userinfo['appraised_count'] = model('order')->where($sqlmap)->count();

        // 淘宝帐号
        $userinfo['taobao'] = model('member_bind')->find($order_info['bind_id']);
        if ($order_info['act_mod'] == 'trial') {
            // 读取后台活动设置：参与条件
            $bind_set = string2array(C_READ('buyer_join_condition','trial'));
            /* 更新已绑定淘宝帐号信息 */
            if ($bind_set['bind_taobao'] == 4) {
                runhook('get_bind_taobao',array('id'=>$order_info['bind_id']));
            }

        }

        // 抢购/试用 完成的商品
        $_sqlmap = array();
        $_sqlmap['buyer_id'] = $userid;
        $_sqlmap['act_mod'] = $order_info['act_mod'];
        $_sqlmap['status'] = 7;
        $ids = model('order')->where($_sqlmap)->order('id DESC')->limit(4)->getField('goods_id',TRUE);
        if (count($ids) > 0) {
            foreach ($ids as $id) {
                $pro[] = D('Product/Product')->detail($id);
            }
            $userinfo['pro'] = $pro;
        }
        // 获取最新的试用报告
        $_sqlmap_ = array();
        $_sqlmap_['userid'] = $userid;
        $_sqlmap_['status'] = 1;
        $r_ids = model('trial_report')->where($_sqlmap_)->order('id desc')->field('id,goods_id,inputtime,thumb')->limit(4)->select();
        if (count($r_ids) > 0) {
            foreach ($r_ids as $k => $v) {
                $v['title'] = model('product')->getFieldById($v['goods_id'],'title');
                $userinfo['report'][$k] = $v;
            }
        }
        // 年龄
        $userinfo['age'] = date('Y') - $userinfo['birth']['year'];
        // 所在城市
        $userinfo['address'] = string2array($userinfo['address']);
        $userinfo['city'] = model('linkage')->where(array('linkageid' => $userinfo['address']['provice']))->getField('name');
        // ip
        $userinfo['ip'] = $ip = model('member')->getFieldByUserid($userid,'lastip');
        $is_copy = model('member')->where(array('lastip'=>$ip))->find();//判断是否有重复

        $infos = model('member_attesta')->where(array('userid'=>$userid,'type' =>'identity'))->getField('infos');

        $userinfo['infos'] =string2array($infos);       
        if (empty($userinfo)) $this->error('会员信息不存在！');

        include template('merchant/alert_card');
    }
    
    /* 通过审核 */
    public function pass($order_id = 0,$v2='') {
        if(!$this->checkSellAuth($order_id)) $this->error('您没有权限进行此操作');
        $factory = new \Product\Factory\order($order_id);
        $result = $factory->pass();
        if(!$result) $this->error($factory->getError());
        runhook('order_check_trade_no',array('order_id' => $order_id, 'userid' => $factory->order_info['buyer_id'],'result' => 1,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod'],'order_sn'=>$factory->order_info['order_sn']));
        if ($v2) {
            $url =  $url = U('Member/Order/v2_manage', array('goods_id' => $factory->product_info['id'],'mod'=>$factory->product_info['mod'],'state'=>1,'status'=>4));
        }else{
            $url = U('member/order/manage',array('mod'=>$factory->product_info['mod'],'goods_id'=>$factory->product_info['id']));
        }
        $this->success('订单审核成功',$url);
    }

    /* 试用更新流程：审核订单号 */
    public function check_ordersn($order_id = 0,$ispass = 2,$v2 = '') {
        if(!$this->checkSellAuth($order_id)) $this->error('您没有权限进行此操作');
        $factory = new \Product\Factory\order($order_id);
        if ($factory->product_info['mod'] != 'trial') $this->error('该活动不能进行该操作');
        $result = $factory->check_ordersn((int)$ispass);
        if(!$result) $this->error($factory->getError());
        if ($ispass == 1) {
             runhook('order_check_trade_no',array('order_id' => $order_id, 'userid' => $factory->order_info['buyer_id'],'result' => 1,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod'],'order_sn'=>$factory->order_info['order_sn']));
        }else{

            runhook('order_check_trade_no',array('order_id' => $order_id, 'userid' => $factory->order_info['buyer_id'],'result' => 0,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod'],'order_sn'=>$factory->order_info['order_sn']));

        }
       
        if ($v2 > 0) {
            if ($ispass == 1) {

                $url = U('Member/Order/v2_manage', array('goods_id' => $factory->product_info['id'],'mod'=>$factory->product_info['mod'],'state'=>1,'status'=>3));
               
            }else{
                 
                $url = U('Member/Order/v2_manage', array('goods_id' => $factory->product_info['id'],'mod'=>$factory->product_info['mod'],'state'=>5)); 
            }
        $this->success('订单号审核成功',$url);
        }else{
        $this->success('订单号审核成功',U('member/order/manage',array('mod'=>$factory->product_info['mod'],'goods_id'=>$factory->product_info['id'],'state'=>8)));
        }
    }
    
    /* 订单付款 */
    public function pay() {
        $oids = (array) I('order_id');
        if(empty($oids)) $this->error('参数错误');
        $mod = I('mod', 'rebate');
        foreach ($oids as $oid) {
            $oid = (int) $oid;
            if($oid < 1 || !$this->checkSellAuth($oid)) $this->error('您没有权限进行此操作');
            $factory = new \Product\Factory\order($oid);
            $result = $factory->pay();
            if (!$result) {
                $this->error($factory->getError());
            }
            if($factory->order_info['act_mod'] == 'rebate' || $factory->order_info['act_mod'] =='commission'){
                 if($factory->order_info['act_mod'] == 'rebate') $mod = '返利';
                 if($factory->order_info['act_mod'] == 'commission') $mod = '闪电试用';

                /*招商专员--start*/
                $companys = model('member')->field('agent_id')->find($factory->order_info['seller_id']);
                if ($companys['agent_id'] > 0) {
                    $agent = model('admin')->where(array('userid'=>$companys['agent_id']))->field('fee_type,service_fee,roleid')->find();
                    if ($agent) {
                        if ($agent['roleid'] == 6) {
                            //按照下单价提成
                            if($agent['fee_type'] == 2){
                                $money =sprintf("%.2f", $agent['service_fee'] / 100 * $factory->product_info['goods_price']);
                                if($money > 0){
                                    $msg = "买家(id:".$factory->order_info['buyer_id']."),完成".$mod."活动(id:".$factory->order_info['goods_id']."),订单(id:".$factory->order_info['id'].")，您获得单笔下单价提成".$money."元";
                                        $infos = array();
                                        $infos['time'] = NOW_TIME;
                                        $infos['type'] = 2;
                                        $infos['money'] = $money;
                                        $infos['agent_id']= $companys['agent_id'];
                                        $infos['body'] = $msg;
                                        model('company_log')->add($infos);
                                }

                            }

                            //返利按照下单服务费提成  服务费 = 下单价 *服务费百分比
                            if($factory->order_info['act_mod'] == 'rebate'){
                                if($agent['fee_type'] == 3 && $factory->product_info['goods_service'] >0){
                                    $money =sprintf("%.2f", $agent['service_fee'] / 100 * ($factory->product_info['goods_price'] /100 * $factory->product_info['goods_service']));
                                        $msg = "买家(id:".$factory->order_info['buyer_id']."),完成".$mod."活动(id:".$factory->order_info['goods_id']."),订单(id:".$factory->order_info['id'].")，您获得单笔服务费 ".$agent['service_fee']."% 的提成".$money."元";
                                        $infos = array();
                                        $infos['time'] = NOW_TIME;
                                        $infos['type'] = 5;
                                        $infos['money'] = $money;
                                        $infos['agent_id']= $companys['agent_id'];
                                        $infos['body'] = $msg;
                                        model('company_log')->add($infos);
                                }
                            }

                            //闪电试用按照下单服务费提成  服务费 = 单份服务费
                            if($factory->order_info['act_mod'] == 'commission'){
                                if($agent['fee_type'] == 3 && $factory->product_info['goods_service'] >0){
                                    $money =sprintf("%.2f", $agent['service_fee'] / 100 * $factory->product_info['goods_service']);
                                        $msg = "买家(id:".$factory->order_info['buyer_id']."),完成".$mod."活动(id:".$factory->order_info['goods_id']."),订单(id:".$factory->order_info['id'].")，您获得单笔服务费 ".$agent['service_fee']."% 的提成".$money."元";
                                        $infos = array();
                                        $infos['time'] = NOW_TIME;
                                        $infos['type'] = 5;
                                        $infos['money'] = $money;
                                        $infos['agent_id']= $companys['agent_id'];
                                        $infos['body'] = $msg;
                                        model('company_log')->add($infos);
                                }
                            }


                        }
                    }
                }
              /*招商专员--end*/
            }
            runhook('order_balance',array('order_id' => $oid, 'userid' => $factory->order_info['buyer_id'],'title' => $factory->product_info['title'],'result' => 1));
            runhook('goods_friends_order_reward', array('userid' => $factory->order_info['buyer_id']));
        }
        $this->success('订单付款成功');
    }
    
    /* 撤销通过 */
    public function cancel($order_id = 0) {
        if(!$this->checkSellAuth($order_id)) $this->error('您没有权限进行此操作');
        $factory = new \Product\Factory\order($order_id);
        $result = $factory->cancel();
        if(!$result) $this->error($factory->getError());
        runhook('order_balance',array('order_id' => $order_id, 'userid' => $factory->order_info['buyer_id'],'title' => $factory->product_info['title'],'result' => 0));
        $this->success('订单撤销审核成功');
    }
    
    /* 拒绝通过 */
    public function refuse() {
        $info = $_POST;
        $info['content'] = trim($info['content']);
        if ($info['content'] == '') $this->error('请填写拒绝通过原因');
        if(!$this->checkSellAuth($info['oid'])) $this->error('您没有权限进行此操作');
        $factory = new \Product\Factory\order($info['oid']);
        $info['content'] = '审核未通过：'.$info['content'];
        $result = $factory->refuse($info['content']);
        if(!$result) $this->error($factory->getError());
        runhook('order_check_trade_no',array('order_id' => $order_id, 'userid' => $factory->order_info['buyer_id'],'result' => 0,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod'],'cause' => $info['content']));
        $this->success('订单撤销审核成功');
    }

    /* 检测商家权限 */
    private function checkSellAuth($order_id = 0) {
        $seller_id = model('order')->getFieldById($order_id, 'seller_id');
        return ($seller_id > 0 && $seller_id == $this->userid);
    }

    /* 
     * 扣除时检测商家余额
     * $value : 要扣除的值
     * $type  : 要扣除的属性(money:钱/point:积分/exp:经验值)
    */

    private function checkSellerCheck($value,$type='money') {
        if ($this->userinfo[$type] < $value) {
            return FALSE;
        }else{
            return TRUE;
        }
    }
    
    /* 检测会员权限 */
    private function checkBuyerAuth($order_id = 0) {
        $buyer_id = model('order')->getFieldById($order_id, 'buyer_id');
        return ($buyer_id > 0 && $buyer_id == $this->userid);
    }

    /* 查看抢购记录 */
    public function view_log() {
        $info = I('post.');
        if (!$info['oid']) $this->error('记录查看失败！');
        if (!$info['userid']) $this->error('请登录账号后查看抢购记录！');
        if (!$info['modelid'])   $this->error('您的账号有误，请联系平台管理员或重新申请');
        $sqlMap = array();
        $sqlMap['order_id'] = $info['oid'];
        ($info['modelid'] == '1') ? $sqlMap['buyer_id'] = $info['userid'] : $sqlMap['seller_id'] = $info['userid'] ;
        $logs = model('order_log')->where($sqlMap)->order('id ASC')->select();
        if (!$logs) $this->error('暂无记录信息！');
        foreach ($logs as $k => $log) {
            $log['inputtime'] = dgmdate($log['inputtime']);
            $logs[$k] = $log;
        }
        $this->success($logs);
    }

    public function seller_complete_log(){
        $info = I('post.');
        if (!$info['oid']) $this->error('订单信息查看失败！');
        if (!$info['userid']) $this->error('请登录账号后查看相关记录！');
        if (!$info['modelid'])   $this->error('您的账号有误，请联系平台管理员或重新申请');
        if (!$info['buyer_id'])   $this->error('账号信息错误');
        $sqlMap = array();
        $sqlMap['buyer_id'] = $info['buyer_id'];
        $sqlMap['seller_id'] = $info['seller_id'];
        $sqlMap['act_mod'] = $info['mod'];
        $sqlMap['status'] = 7;

        $count = model('order')->where($sqlMap)->count();
        $lists = model('order')->where($sqlMap)->order('id DESC')->select();
        foreach ($lists as $k => $v) {
          $product_info = D('Product/Product')->detail($v['goods_id']);
          $lists[$k]['title'] =  str_cut($product_info['title'],45);  
          $lists[$k]['goods_price'] =  $product_info['goods_price'];
          $lists[$k]['complete_time'] = dgmdate($v['complete_time']);
   
 
        }
        if(!$lists)$this->error('暂无完成订单信息！');
        $this->success($lists);





    }

    /* 关闭订单 */
    public function close() {
        $info = I('post.');
        if (!$info['oid'])  $this->error('该订单不存在');
        if (!$info['userid'])  $this->error('请登录后再关闭订单');
        $factory = new \Product\Factory\order($info['oid']);
        $result = $factory->close();
        if (!$result)   $this->error('订单关闭失败，请稍后再试！');
        $this->success('订单关闭成功');
    }

    /* 试用-> 确认试用资格 */
    public function trial_pass($order_id = 0,$state = 0,$mod = '',$v2 = '') {
        if(!$this->checkSellAuth($order_id)) $this->error('您没有权限进行此操作');
        $factory = new \Product\Factory\order($order_id);

        if($factory->order_info['status'] != 1){
           $this->error('当前订单状态不是待审核试用资格状态！');
        }

        //判断库存
        $already_num = model('product')->where(array('id' => $factory->product_info['id']))->getField('already_num'); // 已抢购数量
        $goods_number = model('product_trial')->where(array('id' => $factory->product_info['id']))->getField('goods_number'); //商品总量
        if((int)$already_num > (int)$goods_number ){
         //写入群发告警邮件队列
           $sqlmap =array();
           $sqlmap['email'] = model('admin')->where(array('userid' => 1))->getField('email');
           $sqlmap['title'] = '活动商品库存异常监测提醒';
           $sqlmap['body'] = '尊敬的管理员您好，系统监测到平台活动id：'.$factory->product_info['id'].'库存异常！请检查！';
           sendmail($sqlmap['email'],$sqlmap['title'], $sqlmap['body']);

        }
        if((int)$already_num >= (int)$goods_number) $this->error('该产品库存为0，请追加');
        if($factory->product_info['goods_number'] - $factory->product_info['already_num'] <= 0){
        	$this->error('该产品库存为0，请追加');
        }
        if ((int)$state == 1) {
            $result = $factory->set_status(2,'确认免费试用资格通过');
            if ($result) {
                if((int)$already_num >= (int)$goods_number) $this->error('该产品库存为0，请追加');
                runhook('order_check_trade_no',array('order_id' => $order_id, 'userid' => $factory->order_info['buyer_id'],'result' => 1,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod'],'order_sn'=>$factory->order_info['order_sn']));
                model('product')->where(array('id' => $factory->product_info['id']))->setInc('already_num');
            }
        }else{
            $result = $factory->set_status(0,'商家确认免费试用资格不通过');
            if ($result) {
                runhook('order_check_trade_no',array('order_id' => $order_id, 'userid' => $factory->order_info['buyer_id'],'result' => 0,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod'],'order_sn'=>$factory->order_info['order_sn']));
            }
        }
        if(!$result) $this->error($factory->getError());
        // 跳转到  待审核资格   页面
        if ($v2) {
            if ($state == 1) {
                 $msg = '审核试用资格成功';
                 $url = U('Member/Order/v2_manage', array('goods_id' => $factory->product_info['id'],'mod'=>$mod,'state'=>1,'status'=>2));
            }else{
                 $msg = '拒绝试用资格成功';
                 $url = U('Member/Order/v2_manage', array('goods_id' => $factory->product_info['id'],'mod'=>$mod,'state'=>3));

            }
            $this->success($msg,$url);
        }else{
             redirect(U('Member/Order/manage',array('state' => 1,'goods_id'=>$factory->product_info['id'],'mod'=>$mod)), 0, '页面跳转中...');
        }
    }

    /* 试用-> 填写试用报告 */
    public function trial_report($order_id = 0){
        $SEO = seo('','填写试用报告');
        $mod = I('mod', 'trial');
        if (IS_POST){
            $info = $_POST;
            $info = array_filter($info);
            if (!$info)   $this->error('该信息不完整，请重新填写试用报告！');
            if ((int)$info['order_id'] < 1 )  $this->error('该订单不存在');
            if ((int)$info['report']['star']   == 0)    $this->error('请您为本次试用选择星星打分');
            if ($info['report']['background']  == '')   $this->error('请填写试客背景');
            if ((int)$info['report']['height'] == 0)    $this->error('请填写您的身高');
            if ((int)$info['report']['weight'] == 0)    $this->error('请填写您的体重');
            if ((int)$info['report']['age']    == 0)    $this->error('请填写您的年龄');
            if ($info['report']['job']         == '')   $this->error('请填写您的职业');
            if ($info['report']['content']     == '')   $this->error('请填写试用过程及体验');
            if(!$this->checkBuyerAuth($info['order_id'])) $this->error('您没有权限进行此操作');
            $factory = new \Product\Factory\order($info['order_id']);
            /*if ($factory->order_info['trial_report'])   $this->error('您已填写试用报告，请等待商家审核哦~');*/
            $data = array();
            if ($info['id']) {
                $data['id'] = $info['id'];
            }
            $data['content'] = $info['report']['content'];
                unset($info['report']['content']);
                $report = array2string($info['report']);            
                if (!$report)   $this->error('该信息不完整，请重新填写试用报告！');
            $data['base_info'] = $report;
            // 提取内容首图
            preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", stripslashes($_POST['report']['content']), $matches);
            $data['thumb'] = (string)$matches[3][0];

            //手机端上传图片 没有文本编辑器的处理方法 BY cz
            if(!empty($info['report']['images'])){
                $data['thumb'] = $info['report']['images'][0];
                $string = '';
                foreach($info['report']['images'] as $k=>$v){
                    $string .= '<img src=\"'.$v.'\"><br>';
                }
                $data['content'] = $string.$data['content'];
            }

            $data['inputtime'] = NOW_TIME;
            $data['goods_id'] = $factory->order_info['goods_id'];
            $data['order_id'] = $info['order_id'];
            $data['userid'] = $factory->order_info['buyer_id'];
            $data['status'] = '0';
            $data['ip'] = get_client_ip();
            if (!$data)   $this->error('该信息不完整，请重新填写试用报告！');
            $result = $factory->fill_trial_report($data);
            if(!$result) {
                $this->error($factory->getError());
            }
            runhook('order_fill_report',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title']));
            $this->success('填写试用报告成功，请等待商家审核哦~',U('Member/order/manage',array('mod'=>'trial','state'=>3)));  
        }else{
            if ($order_id < 1) $this->error('该订单不存在');
            if(!$this->checkBuyerAuth($order_id)) $this->error('您没有权限进行此操作');
            //查看订单号是否已经填写试用报告
               $count = model('trial_report')->where(array('order_id'=>$order_id))->count();
            if($count >= 1){
                $trial_report = model('trial_report')->where(array('order_id'=>$order_id))->find();
                $infos = string2array($trial_report['base_info']);
                $trial_report['height'] = $infos['height'];
                $trial_report['weight'] = $infos['weight'];
                $trial_report['star'] = $infos['star'];
                $trial_report['age'] = $infos['age'];
                $trial_report['job'] = $infos['job'];
                $trial_report['background'] = $infos['background'];
               
               
            }

            $factory = new \Product\Factory\order($order_id);
            $product = $factory->product_info;
            $form =  new \Common\Library\form();
            include template('buyer/trial_report');
        }
    }

    /* 试用-> 查看试用报告 */
    public function view_report(){
        (int)$order_id = I('order_id');
        if ($order_id < 1) $this->error('该订单不存在');
        $factory = new \Product\Factory\order($order_id);
        //判断订单是否已经完成
        $status = intval($factory->order_info['status']);
       
        if($status < 3){
        	$this->error('该订单不是待审核的状态');
        }
        
        $product_info = $factory->product_info;
        $order_info = $factory->order_info;
        $order_info['trial_report']['content'] = html_entity_decode(stripslashes($order_info['trial_report']['content']));
        $order_info['trial_report']['base_info'] = string2array($order_info['trial_report']['base_info']);
        // 买家会员信息
        $userinfo = model('member')->find($order_info['buyer_id']);

        include template('merchant/alert_report'); 
    }

    /* 试用-> 付款并审核试用报告 */
    public function pay_report(){
        $info = I('post.');
        if ((int)$info['order_id'] < 1 )    $this->error('该订单不存在，请重新下单！');
        $factory = new \Product\Factory\order($info['order_id']);

        if ($info['order_id'] != $factory->order_info['id']) $this->error('该订单号有误，请重新付款');

        if($factory->order_info['status'] != 3){
            $this->error('当前订单不是待审核状态');
        }
         
        if (!$info['pay_appraised']) $this->error('未选择付款操作');
        if ($info['pay_appraised'] == 2){ // 付款并优评
            if ((int)$info['val'] < 1)   $this->error('赠送金额必须大于等于1元');
            // 校验余额
            $check = $this->checkSellerCheck((int)$info['val']);
            if (!$check)   $this->error('您当前余额不足，请充值！');
        }else{
            (int)$info['val'] = 0 ;
        }

        $result = $factory->pay($factory->product_info['goods_price'],$info['pay_appraised'],(int)$info['val'],'seller');
        if (!$result) {
            $this->error($factory->getError());
        }
        runhook('order_balance',array('userid' => $factory->order_info['buyer_id'],'title' => $factory->product_info['title'],'result' => 1));
        runhook('goods_friends_reward', array('userid' => $factory->order_info['buyer_id']));
        runhook('goods_friends_order_reward', array('userid' => $factory->order_info['buyer_id']));

        if ($info['v2']) {
        $this->success('试用报告审核成功',U('Member/Order/v2_manage', array('goods_id' => $factory->order_info['goods_id'],'mod'=>$factory->order_info['act_mod'],'state'=>2)));

        }else{
        $this->success('试用报告操作成功');
        }
    }

    /*
     * 获取订单json
     */
    public function getlists(){
        $param = I('param.');
        if(empty($param)){
            exit(0);
        }
        extract($param);
        $page = max(1, (int) $page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 20;
        $sqlmap = array();
        //查出购物返利的2和5状态和申请试用的1和3的状态
        if($status == 3){
            $sqlmap['_string'] = "`act_mod` = 'rebate' and `status` = '3' or `act_mod` = 'rebate' and `status` = '5' or `act_mod` = 'trial' and `status` = '1' or `act_mod` = 'trial' and `status` = '3'  or `act_mod` = 'trial' and `status` = '8'";
        }else{
            $sqlmap['status'] = $status;
        }

        if($userid == 1){
            $sqlmap['buyer_id'] = $this->userid;
        }
        $count = model('order')->where($sqlmap)->count();

        $lists = model('order')->where($sqlmap)->page($page, $num)->order($orderby.' '.$orderway)->select();
       // echo model('order')->getLastSql();


        if($lists == ""){
            $result['status'] = 0;
            echo json_encode($result);
            exit;
        }
        foreach($lists as $k=>$v){
            $factory = new \Product\Factory\product($v['goods_id']);
            $lists[$k]['goods'] = $factory->product_info;
            $lists[$k]['mod_unit'] = activitiy_price_name($lists[$k]['goods']['mod']);
            $lists[$k]['create_time2'] = date("Y-m-d H:i:s",$v['create_time']);
            if($status == 7 && $v['mod'] != 'trial'){
                //查看是否晒单
                $report = model('report')->where(array('order_id'=>$v['id'],'userid'=>$this->userid))->count();
                $lists[$k]['report_count'] = $report;
            }
            if($status == 2 && $v['mod'] == 'trial' && $v['order_sn']){
                $trial_report = model('trial_report')->where(array('order_id'=>$value['id']))->find();
                $lists[$k]['trial_report'] = $trial_report;
            }
        }

        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['data'] = array(
            'count' => $count,
            'lists' => $lists,
            'pages' => $pages
        );
        echo json_encode($result);
    }

    /*
     * 填写订单号
     */
    public function edit_ordernum(){
        $SEO['title'] = '填写订单号';
        $orderid = I('orderid');

        $buyer_id = model('order')->field('buyer_id,goods_id,status,order_sn,act_mod')->where(array('id'=>$orderid))->find();

        if($buyer_id['buyer_id'] != $this->userid || $buyer_id['status'] != 2){
            redirect(U('member/profile/index'));
            exit;
        }

        if($buyer_id['act_mod'] == "trial" && $buyer_id['order_sn'] != ""){
            redirect(U('member/profile/index'));
            exit;
        }

        $factory = new \Product\Factory\product($buyer_id['goods_id']);
        $product_info = $factory->product_info;
        if ($product_info['goods_ww']) {
            $product_info['__seller__']['contact_want'] = $product_info['goods_ww'];
        }elseif($product_info['company_id'] && $product_info['goods_ww'] == '' ) {
            $product_info['__seller__'] = model('member_merchant')->detail($rs['company_id']);
            if (!$product_info['__seller__']['contact_want']) $product_info['__seller__']['contact_want'] = '';
        }
        $product_info['act_config'] = $factory->getConfig();

        include template('buyer/edit_ordernum');
    }

    /*
     * 购物返利 晒单
     */
    public function rebate_report($order_id = 0){

        if ($order_id < 1) $this->error('该订单不存在');
        if(!$this->checkBuyerAuth($order_id)) $this->error('您没有权限进行此操作');
        $factory = new \Product\Factory\order($order_id);
        $product = $factory->product_info;

        // 查询该订单是否已晒单
        $report = model('report')->where(array('order_id'=>$order_id,'userid'=>$this->userid))->find();


        include template("buyer/rebate_report");
    }

    /*
     * 答案下单
     */
    public function answer(){
        $goods_id = I('goods_id');
        if ($goods_id < 1) $this->error('该商品不存在');

        //查看这个商品是否是答案下单
        $factory = new \Product\Factory\product($goods_id);
        $product_info = $factory->product_info;

        $goods = $product_info;
        if($product_info['type'] != 'ask'){
            $this->error('该商品不是答案下单');
        }
        $SEO['title'] = '答题下单-'.C('WEBNAME');
        $SEO['keyword'] = '答题下单-'.C('WEBNAME');
        $SEO['description'] = '答题下单-'.C('WEBNAME');
        include template("answer");
    }

    /*j加入黑名单*/

    public function blacklist(){
        $order_id = I('order_id');
        $cause = I('cause');
         if ($order_id < 1) $this->error('该订单不存在');
         if (!$cause) $this->error('请输入拉黑理由');
        $factory = new \Product\Factory\order($order_id);
        $product = $factory->order_info;
        $sqlMap = array();
        $sqlMap['buyer_id'] = $product['buyer_id'];
        $sqlMap['seller_id'] = $product['seller_id'];
        $sqlMap['order_id'] = $order_id;
        $sqlMap['cause'] = $cause;
        $sqlMap['status'] = 0;
        $sqlMap['posttime'] =NOW_TIME;
        $count = model('member_blacklist')->where(array('buyer_id'=>$product['buyer_id'],'seller_id'=>$product['seller_id']))->count();
        if ($count > 0) {
           $result = model('member_blacklist')->where(array('buyer_id'=>$product['buyer_id'],'seller_id'=>$product['seller_id']))->save($sqlMap);
        }else{
            $result = model('member_blacklist')->add($sqlMap);
        }

        if ($result) {
            $this->success('已成功移入黑名单中');
        }else{
            $this->error('服务器繁忙，请稍后再尝试');

        }

    }

    /*解除黑名单*/
    public function unbind_blacklist(){
        $id = I('id');
        if ($id < 1) $this->error('该信息有误');
        $result = model('member_blacklist')->where(array('id'=>$id))->delete();
        if ($result) {
            $this->success('已成功移出黑名单');
        }
    }

    
    public function get_blacklists(){
        $sqlMap = array();
        $sqlMap['seller_id'] = $this->userid;
        $count = model('member_blacklist')->where($sqlMap)->count();
        $pagecurr = max(1,I('page',0,'intval'));
        $pagesize = 10;
        $blacklist = model('member_blacklist')->where($sqlMap)->page($pagecurr,$pagesize)->order('id DESC')->select();
        $pages = page($count,$pagesize);
        include template('merchant/blacklist');


    }

    public function get_trial_report(){
        $sqlMap = array();
        $sqlMap['userid'] = $this->userid;
        $states = $this->status;
        $status = I('status');
        $pagecurr = max(1,I('page',0,'intval'));
        $pagesize = 10;
        $sqlMap['status'] = array('EQ',$status);
        $no_pass = model('trial_report')->where(array('userid'=>$this->userid,'status'=>'-1'))->count();
        $center_pass = model('trial_report')->where(array('userid'=>$this->userid,'status'=>'0'))->count();
        $pass = model('trial_report')->where(array('userid'=>$this->userid,'status'=>'1'))->count();

          
     
        
        $count = model('trial_report')->where($sqlMap)->count();
        $lists = model('trial_report')->where($sqlMap)->page($pagecurr,$pagesize)->order('id DESC')->field('order_id,goods_id,inputtime,status')->select();
      //  echo model('trial_report')->getLastSql();
        foreach ($lists as $k => $v) {
            $factory = new \Product\Factory\order($v['order_id']);
           // var_dump($v);
                $lists[$k]['title'] = $factory->product_info['title'];
                $lists[$k]['url'] = $factory->product_info['url'];
                $lists[$k]['goods_thumb'] = $factory->product_info['thumb'];
                $lists[$k]['goods_bonus'] = $factory->product_info['goods_bonus'];
                $lists[$k]['goods_price'] = $factory->product_info['goods_price'];
                $lists[$k]['goods_url'] = $factory->product_info['goods_url'];
                $lists[$k]['order_sn'] = $factory->order_info['order_sn'];
                $lists[$k]['userid'] = $factory->order_info['buyer_id'];
                $lists[$k]['seller_id'] = $factory->order_info['seller_id'];
                $lists[$k]['check_time'] = $factory->order_info['check_time'];
                $lists[$k]['create_time'] = $factory->order_info['create_time'];
                $lists[$k]['status'] = $factory->order_info['status'];
                $lists[$k]['goods_id'] = $factory->order_info['goods_id'];
                $lists[$k]['report_status'] = $v['status'];
              //  $lists[$k]['id'] = $v['order_id'];

        }

       // var_dump($lists);

        $v2_pages = v2_page_3($count,$pagesize);
        include template('buyer/trial_report_lists');

    }


     /**
     * 订单管理  根据good_id
     */
    public function v2_manage() {
        if(DEFAULT_THEME == 'wap'){
            redirect(U('Member/Profile/index'));
            exit;
        }
        $SEO = seo('','订单管理');
        $sqlmap = array();
        if($this->userinfo['modelid'] == 2) {
            $mod = I('mod')?I('mod'):'trial';

            $state = I('state', 1, 'intval');
            if ($mod == 'trial') {
              $status = I('status', 1, 'intval');

            }elseif($mod == 'commission' || $mod == 'rebate'){
             $status = I('status', 2, 'intval');

            }

            $goods_id = I('goods_id', 0, 'intval');
            if($goods_id < 1) {
                $this->error('请勿非法访问');
            }
            $sqlmap['goods_id'] = $goods_id;
            $sqlmap['seller_id'] = $this->userid;
            if ($state == 1) {
               
                if ($mod == 'trial') {

                    $check_zhige = model('order')->where(array('status'=>1,'act_mod'=>$mod,'seller_id'=>$this->userid,'goods_id'=>$goods_id))->count();
                    $seller_ordersn = model('order')->where(array('status'=>2,'act_mod'=>$mod,'seller_id'=>$this->userid,'goods_id'=>$goods_id))->count();
                    $trial_report_count = model('order')->where(array('status'=>array(array('EQ',3)),'act_mod'=>$mod,'seller_id'=>$this->userid,'goods_id'=>$goods_id))->count();

                  //  $trial_report_count = model('order')->where(array('status'=>array(array('EQ',3),array('EQ',8), 'or'),'act_mod'=>$mod,'seller_id'=>$this->userid,'goods_id'=>$goods_id))->count();
                 }elseif($mod == 'commission'){
                     $seller_ordersn = model('order')->where(array('status'=>3,'act_mod'=>$mod,'seller_id'=>$this->userid,'goods_id'=>$goods_id))->count();
                     $pay_count =  model('order')->where(array('status'=>5,'act_mod'=>$mod,'seller_id'=>$this->userid,'goods_id'=>$goods_id))->count();
                 }
              


            }

            if ($state == 6) {
                 $trial_write_count = model('order')->where(array('status'=>array(array('EQ',8)),'act_mod'=>$mod,'seller_id'=>$this->userid,'goods_id'=>$goods_id))->count();
               
                  $trial_write_order_sn = model('order')->where(array('status'=>array(array('EQ',2)),'order_sn'=>array('EQ',''),'act_mod'=>$mod,'seller_id'=>$this->userid,'goods_id'=>$goods_id))->count();
            }
            $_where = '`status` !=0 and `status` != 7 and act_mod="'.$mod.'" and goods_id='.$goods_id.' and seller_id='.$this->userid;
            $ing = model('order')->where($_where)->count();
            switch ($state) {
                case '1':
                        switch ($status) {
                            case '1':
                                $sqlmap['status'] = array('EQ',1);
                                break;
                             case '2':
                             if ($mod == 'trial') {
                                    $sqlmap['status'] = array('EQ',2);

                             }elseif($mod == 'commission' || $mod == 'rebate'){
                                $sqlmap['status'] = array('EQ',3);

                             }
                              
                            break;
                            case '3':
                             //   $sqlmap['status'] = array(array('EQ',3),array('EQ',8), 'or');
                                $sqlmap['status'] = array('EQ',3);
                                break;
                             case '4': 
                                 $sqlmap['status'] = array('EQ',5);

                                break;
                              case '5':
                                 $sqlmap['status'] = array('EQ',8);

                                break;
                            
                            
                            
                            default:
                                # code...
                                break;
                        }


                   
                    break;
                case '2':
                        $sqlmap['status'] = array('EQ',7);
                   
                    break;

                case '3':
                      $sqlmap['status'] = array('EQ',0);

                   
                    break;
                case '4':

                     $sqlmap['status'] = array('EQ',6);

                   
                    break;

                case '5':

                     $sqlmap['status'] = array('EQ',4);

                   
                    break;
                case '6':
                   
                    switch ($status) {
                                
                              case '5':
                                 $sqlmap['status'] = array('EQ',8);

                                break;
                             case '6':
                                 $sqlmap['status'] = array('EQ',2);
                                 $sqlmap['order_sn'] = array('EQ','');


                                break;
                            
                            
                            default:
                                # code...
                                break;
                        }




                   
                    break;
                
                default:
                    # code...
                    break;
            }

           
            $factory = new \Product\Factory\product($goods_id);
            $pro = $factory->product_info;
            $tpl = 'merchant/v2_order_manage';
        } else {

            $mod = I('mod')?I('mod'):'trial';
             if ($mod == 'trial') {
                $tpl = 'buyer/trial_order_manage';

            }

             if ($mod == 'commission') {
                $tpl = 'buyer/commission_order_manage';

            }

             if ($mod == 'rebate') {
                $tpl = 'buyer/rebate_order_manage';

            }
            $state = I('state', 2, 'intval');
            $sqlmap['buyer_id'] = $this->userid;
            $sqlmap['act_mod'] = $mod;

             /*已通过待填写订单号*/
           $no_ordersn_count = $this->db->where(array('buyer_id'=>$this->userid,'act_mod'=>$mod,'status'=>2,'order_sn'=>array('EQ','')))->count();
          /*已填写订单号带审核订单号*/
           $check_ordersn_count = $this->db->where(array('buyer_id'=>$this->userid,'act_mod'=>$mod,'status'=>2,'order_sn'=>array('NEQ','')))->count();
           $write_report_count = $this->db->where(array('buyer_id'=>$this->userid,'act_mod'=>$mod,'status'=>8,'order_sn'=>array('NEQ','')))->count();
           $check_report_count = $this->db->where(array('buyer_id'=>$this->userid,'act_mod'=>$mod,'status'=>3,'order_sn'=>array('NEQ','')))->count();
           $no_passreport_count = $this->db->where(array('buyer_id'=>$this->userid,'act_mod'=>$mod,'status'=>4,'order_sn'=>array('NEQ','')))->count();
           $pay_count = $this->db->where(array('buyer_id'=>$this->userid,'act_mod'=>$mod,'status'=>5,'order_sn'=>array('NEQ','')))->count();
           $appeal_count = $this->db->where(array('buyer_id'=>$this->userid,'act_mod'=>$mod,'status'=>6,'order_sn'=>array('NEQ','')))->count();

           $finish_count = $this->db->where(array('buyer_id'=>$this->userid,'act_mod'=>$mod,'status'=>7,'order_sn'=>array('NEQ','')))->count();

            $state = I('state', 2, 'intval');
            $sqlmap['buyer_id'] = $this->userid;
            $sqlmap['act_mod'] = $mod;
            if($state > 1) {
                
                    $search_status =  $_GET['search_status'];
                    $com_status =  $_GET['com_status'] ;
                    if (!$search_status && !$com_status  ) {

                        $sqlmap['status'] = array('EGT',$state);
                    }else{



                        if($mod == 'trial'){

                            switch ($search_status) {
                                case '1':
                                    $sqlmap['status'] = array('EQ','2');
                                    $sqlmap['order_sn'] = array('EQ','');
                                    break;
                                case '2':
                                    $sqlmap['status'] = array('EQ','2');
                                    $sqlmap['order_sn'] = array('NEQ','');
                                   
                                    break;
                                case '3':
                                    $sqlmap['status'] = array('EQ','8');
                                    $sqlmap['order_sn'] = array('NEQ','');
                                    break;
                                case '4':
                                  $sqlmap['status'] = array('EQ','3');
                                    # code...
                                    break;
                                case '5':
                                  $sqlmap['status'] = array('EQ','4');
                                   break;
                                case '6':
                                  $sqlmap['status'] = array('EQ','6');
                                    # code...
                                    break;
                                case '7':
                                  $sqlmap['status'] = array('EQ','7');
                                    # code...
                                    break;
                                
                                
                                default:
                                    # code...
                                    break;
                            }
                        }elseif($mod == 'commission'|| $mod == 'rebate'){
                        
                         $sqlmap['status'] =  array('EQ',$com_status);
                     }

                }

                 

                
            }else{
               $sqlmap['status'] = array('EQ',$state);

            }


            if (IS_GET) {
                $search_type = I('search_type');
                $key = $_GET['keywords'];

                if ((int)$search_type > 0) {
                    switch ($search_type) {
                       case '1':
                       
                        $sqlmap['order_sn'] = array("LIKE", "%".$key."%");
                      
                       break;

                        case '2':
                        $gids = model('product')->where(array('title'=>array("LIKE", "%".$key."%")))->getfield('id',true);
                            $sqlmap['goods_id'] = array("IN", $gids);
                      
                       break;
                        
                        default:
                            # code...
                            break;
                         }
                }
               
            }


        }



        $states = $this->status;
        if ($mod == 'commission') {
            unset($states['1']);
            $states['2'] = '已抢购';
        }else{
            $states['1'] = '已申请';
            $states['2'] = '已获得资格';
            unset($states['5']);
        }
        
        $count = $this->db->where($sqlmap)->count();
        $lists = $this->db->where($sqlmap)->page(PAGE, 10)->order("id DESC")->select();

       // echo $this->db->getLastSql();
        foreach ($lists as $key => $value) {
            $value['product_info'] = model('product/product')->detail($value['goods_id']);
            $value['store_name'] = model('member_merchant')->getFieldByUserid($value['seller_id'], 'store_name');
            if ($value['act_mod'] == 'trial') {
                if ($value['product_info']['goods_ww'] != '') {
                    $value['contact_want'] = $value['product_info']['goods_ww'];
                    # code...
                }else{
                    $value['contact_want'] = model('member_merchant')->getFieldByUserid($value['seller_id'], 'contact_want');
                    if (!$value['contact_want']) $value['contact_want'] = '';


                }
            }else{
                $value['contact_want'] = model('member_merchant')->getFieldByUserid($value['seller_id'], 'contact_want');
                 if (!$value['contact_want']) $value['contact_want'] = '';

            }
            
            // 如果有申诉记录就查询出最后一次的 (用于会员查看申诉)
            $value['appeal_id'] = model('appeal')->order('id DESC')->getFieldByOrder_id($value['id']);
            // 查出该商品的申诉信息
            $value['appeal'] = model('appeal')->where(array('order_id'=>$value['id']))->find();
            // 查询该商品是否已晒单
            $value['report_count'] = model('report')->where(array('goods_id'=>$value['goods_id'],'userid'=>$this->userid))->count();
            // 买家平台账号
            $value['nickname'] = model('member')->where(array('userid'=>$value['buyer_id']))->getField("nickname");
            $value['sex'] = model('member_detail')->where(array('userid'=>$value['buyer_id']))->getField("sex");
            $value['close_cause'] = model('order_log')->where(array('goods_id'=>$value['goods_id'],'seller_id'=>$value['seller_id'],'order_id'=>$value['id']))->order('id DESC')->limit(1)->getField("cause");


            $provice = string2array(model('member')->where(array('userid'=>$value['buyer_id']))->getField("address"));
            $value['provice'] = model('linkage')->getFieldByLinkageid($provice['provice'],'name');

            //绑定的淘宝账号
            if (!empty($value['bind_id'])) {
                $value['bind_account'] = model('member_bind')->where(array('id'=>$value['bind_id']))->getField('account');
                $value['account_level'] = model('member_bind')->where(array('id'=>$value['bind_id']))->getField('account_level');
                $value['taobao_img'] = model('member_bind')->where(array('id'=>$value['bind_id']))->getField('taobao_img');
            }

            $infos = model('member_attesta')->where(array('userid'=>$value['buyer_id'],'type'=>'identity'))->find();
            $real_name = string2array($infos['infos']);
            $value['real_name'] = $real_name['name'];
            $value['name_status'] = $infos['status'];


            //累计申请本店1次

             $value['total_apply'] = model('order')->where(array('buyer_id'=>$value['buyer_id'],'seller_id'=>$value['seller_id'],'act_mod'=>$value['act_mod']))->count();

              //正在进行中

             $value['total_ing'] = model('order')->where(array('buyer_id'=>$value['buyer_id'],'status'=>array('NOT IN','0,7'),'seller_id'=>$value['seller_id'],'act_mod'=>$value['act_mod']))->count();

              //已完成

             $value['total_complete'] = model('order')->where(array('buyer_id'=>$value['buyer_id'],'seller_id'=>$value['seller_id'],'status'=>7,'act_mod'=>$value['act_mod']))->count();


            // 买家是否有试用报告
            if ($value['act_mod'] == 'trial'){
                $value['trial_report'] = model('trial_report')->where(array('order_id'=>$value['id']))->find();
            }

            
            $lists[$key] = $value;
        }
        if (!empty($lists)) {
            $factory = new \Product\Factory\product($lists[0]['goods_id']);
            $act_config = $factory->getConfig();
        }
        $v2_pages = v2_page_3($count,10);
        include template($tpl);
    }

     public function v2_trial_report($order_id = 0){
        $SEO = seo('','填写试用报告');
        $mod = I('mod', 'trial');
        if (IS_POST){
            $info = $_POST;
            $info = array_filter($info);
            if (!$info)   $this->error('该信息不完整，请重新填写试用报告！');
            if ((int)$info['order_id'] < 1 )  $this->error('该订单不存在');
            if ((int)$info['star']   == 0)    $this->error('请您为本次试用选择星星打分');
            if (!$this->checkBuyerAuth($info['order_id'])) $this->error('您没有权限进行此操作');
            $factory = new \Product\Factory\order($info['order_id']);
            if($factory->order_info['status'] ==8 || $factory->order_info['status'] ==4){
                if ($factory->order_info['trial_report'] && $factory->order_info['status'] == 3) $this->error('您已填写试用报告，请等待商家审核哦~',U('Member/order/v2_manage'));
                $data = array();
                if ($info['id']) {
                    $data['id'] = $info['id'];
                }
                $data['content'] = $info['content'];
                $report = array2string($info['star']);            
                if (!$report)   $this->error('该信息不完整，请重新填写试用报告！');
                $data['base_info'] = $report;
                $data['inputtime'] = NOW_TIME;
                $data['goods_id'] = $factory->order_info['goods_id'];
                $data['order_id'] = $info['order_id'];
                $data['thumb'] = $info['thumb'];
                $data['userid'] = $factory->order_info['buyer_id'];
                $data['status'] = '0';
                $data['ip'] = get_client_ip();
                if (!$data)   $this->error('该信息不完整，请重新填写试用报告！');
                $result = $factory->fill_trial_report($data);
                if(!$result) {
                    $this->error($factory->getError());
                }
                runhook('order_fill_report',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title']));

                if($factory->order_info['status'] ==4) $msg = '修改试用报告成功，请耐心等待商家重新审核哦';
                if($factory->order_info['status'] ==8) $msg = '填写试用报告成功，请耐心等待商家审核哦~';
                $this->success($msg,U('Member/order/v2_manage',array('mod'=>'trial','state'=>2,'search_status'=>4)));  
            }else{
                $this->error('当前订单状态不允许填写试用报告');
            }

        }else{
            if ($order_id < 1) $this->error('该订单不存在');
            if(!$this->checkBuyerAuth($order_id)) $this->error('您没有权限进行此操作');
            //查看订单号是否已经填写试用报告
               $count = model('trial_report')->where(array('order_id'=>$order_id))->count();
            if($count >= 1){
                $trial_report = model('trial_report')->where(array('order_id'=>$order_id))->find();
                $infos = string2array($trial_report['base_info']);
               
                $trial_report['star'] = $infos['star'];
            }

            $factory = new \Product\Factory\order($order_id);
            $product = $factory->product_info;
            $form =  new \Common\Library\form();
            include template('buyer/v2_trial_report');
        }
    }


}
