<?php
/**
 * 专员统计
 */
namespace Admin\Controller;
class StatisticsController extends InitController{
    public function _initialize(){
        parent::_initialize();
        $this->db = model('admin');
        $this->pagecurr = max(1,I('page','0','intval'));
        $this->pagesize = 10;
        //今日起始时间
        $this->starttime = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $this->endtime = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
    }
    
     public function init(){
        //商家类型
        $merchant_type = getcache('merchant_group','member');
        $m_names = array();
        foreach ($merchant_type as $_k=>$_v) {
            $m_names[] = array('id'=>$_v['groupid'],'name'=>$_v['name'],'money'=>substr($_v['pricetype'],strpos($_v['pricetype'],',') + 1));
        } 
        //提出普通商家
        array_shift($m_names);
        $sqlMap = array();
        $param = I('param.');
        $roleid = model('admin_role')->getFieldByRolename('招商专员','roleid');
        $sqlMap['roleid'] = $roleid;
        //查出所有的招商专员
        $count = $this->db->where($sqlMap)->count();
        $attract_lists = $this->db->where($sqlMap)->page($this->pagecurr,$this->pagesize)->select();
        foreach ($attract_lists as $k=>$v) {
            //今日新增商家
            $sql = array();
            $sql['modelid'] = 2;
            $sql['regdate'] = array('BETWEEN',array($this->starttime,$this->endtime));
            $sql['agent_id'] = $v['userid'];
            $attract_lists[$k]['add_merchant'] = model('member')->where($sql)->count();
            //本月新增商家
            $con = array();
            $con['modelid'] = array('EQ',2);
            $con['agent_id'] = array('EQ',$v['userid']);
            $con['regdate'] = array('EGT',strtotime(date('Y-m-d')));
            $attract_lists[$k]['month_seller'] = model('member')->where($con)->count();

            /*今日提成金额*/
            $sql2 = array();
            $sql2['time'] = array('BETWEEN',array($this->starttime,$this->endtime));
            $sql2['agent_id'] = $v['userid'];
            $attract_lists[$k]['today_money'] = model('company_log')->where($sql2)->sum('money');
            /*本月提成金额*/

            $sqlmap = array();
            $sqlmap['agent_id'] = $v['userid'];
            $sqlmap['time'] = array('EGT',strtotime(date('Y-m-d')));
            $attract_lists[$k]['month_money'] = model('company_log')->where($sqlmap)->sum('money');

           
        }
        //排序
        if(submitcheck('search','G')){
            $sort = array(
                'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => $param['sort'],    //排序字段
            );
            $arrSort = array();
            foreach($attract_lists AS $uniqid => $row){
                foreach($row AS $key=>$value){
                    $arrSort[$key][$uniqid] = $value;
                }
            }
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $attract_lists);
        }
        //缓存
        //setcache('today_attract_list',$attract_lists,'commons');
        $pages = page($count,$this->pagesize);
        $form = new \Common\Library\form();
        include $this->admin_tpl('statistics_list');
    }
    
    /**
     * 历史记录
     * @param $userid  业务员id
     */
    public function history($userid = 0){
        $userid = (int) $userid;
        $sqlMap = array();
        $sqlMap['userid'] = $userid;
        $param = I('param.');
        $starttime = (!empty($param['start_time'])) ? strtotime($param['start_time']) : 0;
        $endtime = (!empty($param['end_time'])) ? strtotime($param['end_time']) : 0;
        $history_lists = array();
        //查询时间段的记录
        $j = 0;
        for($i = $starttime;$i <= $endtime;$i+=86400){
            $y=mktime(0,0,0,date('m',$starttime),date('d',$starttime),date('Y',$starttime));
            $today=date("Y-m-d",$y+$j*24*3600);
            //查询当天的记录
            $todaytime = strtotime($today);
            $d_start = mktime(0,0,0,date('m',$todaytime),date('d',$todaytime),date('Y',$todaytime));
            $d_end = mktime(0,0,0,date('m',$todaytime),date('d',$todaytime)+1,date('Y',$todaytime)) - 1;
            $sqlMap['inputtime'] = array("between",array($d_start,$d_end));
            $lists = model('today_statistics')->where($sqlMap)->find();
            $history_lists[$today] = $lists;
            $j++;
        }
        //商家总各数 返利\返利活动 试用\返利活动担保金 试用\返利活动退还 商家升级vip数量 商家升级费用总计
        $merchant_num = $rebate_num = $trial_num = $trial_deposite_num = $rebate_deposite_num = 0;
        $trial_back_num = $rebate_back_num = $vip_num = $money = 0;
        foreach ($history_lists as $k=>$v){
            if($v['merchant_num'] > 0){
                $merchant_num += $v['merchant_num'];
            }
            if($v['rebate_num'] > 0){
                $rebate_num += $v['rebate_num'];
            }
            if($v['trial_num'] > 0){
                $trial_num += $v['trial_num'];
            }
            if($v['trial_deposite'] > 0){
                $trial_deposite_num += $v['trial_deposite'];
            }
            if($v['rebate_deposite'] > 0){
                $rebate_deposite_num += $v['rebate_deposite'];
            }
            if($v['trial_back'] > 0){
                $trial_back_num += $v['trial_back'];
            }
            if($v['rebate_back'] > 0){
                $rebate_back_num = $v['rebate_back'];
            }
            if($v['merchant_vipnum'] > 0 ){
                $vip_num += $v['merchant_vipnum'];
            }
            if($v['merchant_money'] > 0 ){
                $money += $v['merchant_money'];
            }
        }
        $form =  new \Common\Library\form();        
        include $this->admin_tpl('statistics_history');
    }


    /*招商详细信息*/
    public function detail($userid){
        $userid = (int) $userid;
        $sqlMap = array();
        $sqlMap['userid'] = $userid;
        $admininfo = model('admin')->where($sqlMap)->find();
        /*查看本月累计提成*/
        $admininfo['month_money'] = $this->show('month',0,$userid);
         //今日新增商家
        $sql = array();
        $sql['modelid'] = 2;
        $sql['regdate'] = array('BETWEEN',array($this->starttime,$this->endtime));
        $sql['agent_id'] = $userid;
        $admininfo['today_seller'] = model('member')->where($sql)->count();
        //本月新增商家
        $con = array();
        $con['modelid'] = array('EQ',2);
        $con['agent_id'] = array('EQ',$userid);
        $con['regdate'] = array('EGT',strtotime(date('Y-m-d')));
        $admininfo['month_seller'] = model('member')->where($con)->count();
        /*查询本周新增商家*/
        $date = date("Y-m-d");
        $first=1; 
        $w = date("w", strtotime($date)); 
        $d = $w ? $w - $first : 6;  
        $now_start = date("Y-m-d", strtotime("$date -".$d." days"));
        $week =  strtotime($now_start);
        $sqlMap = array();
        $sqlMap['modelid'] = array('EQ',2);
        $sqlMap['agent_id'] = array('EQ',$userid);
        $sqlMap['regdate'] = array('EGT',$week);
        $admininfo['week_seller'] = model('member')->where($sqlMap)->count();

        /*查询当天升级vip*/
        $admininfo['day_group'] = $this->vip('day',2,$userid);
         /*查询本周升级vip*/
        $admininfo['week_group'] = $this->vip('week',2,$userid);
         /*查询本月升级vip*/
        $admininfo['month_group'] = $this->vip('month',2,$userid);

                /*查询当天升级vip*/
        $admininfo['day_group_3'] = $this->vip('day',3,$userid);
         /*查询本周升级vip*/
        $admininfo['week_group_3'] = $this->vip('week',3,$userid);
         /*查询本月升级vip*/
        $admininfo['month_group_3'] = $this->vip('month',3,$userid);

                /*查询当天升级vip*/
        $admininfo['day_order'] = $this->order('day',$userid);
         /*查询本周升级vip*/
        $admininfo['week_order'] = $this->order('week',$userid);
         /*查询本月升级vip*/
        $admininfo['month_order'] = $this->order('month',$userid);

        /*单笔下单提成*/
        $admininfo['day_one_order'] = $this->show('today',2,$userid);
        $admininfo['day_goods_service'] = $this->show('today',1,$userid);

           /*单笔下单提成*/
        $admininfo['month_one_order'] = $this->show('month',2,$userid);
        $admininfo['month_goods_service'] = $this->show('month',1,$userid);
        $admininfo['day_money_zuan'] = $this->show('today',3,$userid);
        $admininfo['day_money_huang'] = $this->show('today',4,$userid);
        $admininfo['month_money_zuan'] = $this->show('month',3,$userid);
        $admininfo['month_money_huang'] = $this->show('month',4,$userid);


        /*总计商家好多名*/
        $admininfo['total_seller'] = model('member')->where(array('agent_id'=>$userid,'modelid'=>2))->count();
        $admininfo['total_zuan_group'] = model('member')->where(array('agent_id'=>$userid,'modelid'=>2,'groupid'=>2))->count();
        $admininfo['total_huang_group'] = model('member')->where(array('agent_id'=>$userid,'modelid'=>2,'groupid'=>3))->count();




        include $this->admin_tpl('attract_detail');
    }

    public function order($time,$userid){
         $sqlMap = array();
        if ($time == 'day') {
            $sqlMap['complete_time'] = array('EGT',strtotime(date('Y-m-d')));
        }elseif($time == 'week'){
            $date = date("Y-m-d");
            $first=1; 
            $w = date("w", strtotime($date)); 
            $d = $w ? $w - $first : 6;  
            $now_start = date("Y-m-d", strtotime("$date -".$d." days"));
            $sqlMap['complete_time'] = array('EGT',strtotime($now_start));

        }elseif($time == 'month'){
            $sqlMap['complete_time'] = array('EGT',strtotime(date('Y-m')));

        }
        $order_sum = '';
        $seller = model('member')->where(array('agent_id'=>$userid,'modelid'=>2))->field('userid')->select();
        foreach ($seller as $k => $v) {
            $sqlMap['seller_id'] = $v['userid'];
            $order_sum += model('order')->where($sqlMap)->count();
        }

        return $order_sum;



    }

    public function vip($time,$type,$userid){
        $sqlMap = array();
        if ($time == 'day') {
            $sqlMap['grouptime'] = array('EGT',strtotime(date('Y-m-d')));
        }elseif($time == 'week'){
            $date = date("Y-m-d");
            $first=1; 
            $w = date("w", strtotime($date)); 
            $d = $w ? $w - $first : 6;  
            $now_start = date("Y-m-d", strtotime("$date -".$d." days"));
            $sqlMap['grouptime'] = array('EGT',strtotime($now_start));

        }elseif($time == 'month'){
            $sqlMap['grouptime'] = array('EGT',strtotime(date('Y-m')));

        }

        $sqlMap['groupid'] = $type;
        $sqlMap['modelid'] = 2;
        $sqlMap['agent_id'] = $userid;
        return model('member')->where($sqlMap)->count();
        

    }





    public function show($time,$type=0,$userid){
            $sqlmap = array();
            $sqlmap['agent_id'] = $userid;
        if ($time == 'today') {
            $sqlmap['time'] = array('EGT',strtotime(date('Y-m-d')));
        }elseif($time == 'month'){
            $sqlmap['time'] = array('EGT',strtotime(date('Y-m')));

        }
        if($type > 0){
            $sqlmap['type'] = $type;
        }

        return model('company_log')->where($sqlmap)->sum('money');

    }
    
    /**
     * 查询每日信息
     * @param $time string 日期
     */
    public function search($time='',$userid=0){
        $time = (string) $time;
        $currenttime = strtotime($time);
        $t_time = mktime(0,0,0,date('m',$currenttime),date('d',$currenttime),date('Y',$currenttime));
        $e_time = mktime(0,0,0,date('m',$currenttime),date('d',$currenttime) + 1,date('Y',$currenttime)) - 1;
        //统计该日期的数据
        $userid = (int) $userid;
        if($userid < 0) $this->error('参数错误，无效业务员');
        $sqlMap = array();
        $sqlMap['userid'] = $userid;
        //查询业务的名称
        $sqlMap['username'] = model('admin')->getFieldByUserid($userid,'username');
        //新增商家的数量
        $m = array();
        $m['agent_id'] = $userid;
        $m['regdate'] = array("between",array($t_time,$e_time));
        $sqlMap['merchant_num'] = model('member')->where($m)->count();
        $attr = array('rebate','trial');
        //查询该专员下的所有商家id
        $ids = model('member')->where(array('agent_id'=>$userid))->getField('userid',true);
        $aa = $money = $f = array();
        foreach ($attr as $k=>$v) {
            //新增返利活动  新增试用活动
            $f['company_id'] = array("in",$ids);
            $f['mod'] = $v;
            $f['inputtime'] = array("between",array($t_time,$e_time));
            $sqlMap[$v.'_num'] = model('product')->where($f)->count();
            //试用活动保证金  返利活动保证金
            $deposite = model('product')->alias('p')->join(C('DB_PREFIX').'product_'.$v.' AS t ON p.id=t.id')->where($f)->sum('goods_deposit');
            $sqlMap[$v.'_deposite'] = ($deposite) ? $deposite : 0;
            //试用活动退还  返利活动退还
            $aa['num'] = array("ELT",0);
            $aa['type'] = 'deposite'; 
            $aa['userid'] = array('in',$ids);
            $aa['inputtime'] = array("between",array($t_time,$e_time));
            $r = model('member_finance_log')->where($aa)->select();
            if($r){
                foreach ($r as $_k=>$_v) {
                    $mod = model('product')->getFieldById($_v['goods_id'],'mod');
                    if($mod == $v){
                        $sqlMap[$v.'_back'] += $_v['num'];
                    }
                }
            }else{
                $sqlMap[$v.'_back'] = 0;
            }
        }
        //商家升级vip数量
        $vip = array();
        $vip['agent_id'] = $userid;
        $vip['viptime'] = array('between',array($t_time,$e_time));
        $sqlMap['merchant_vipnum'] = model('member')->where($vip)->count();
        //商家升级总费用 1、查出升级所需的费用
        $merchant_type = getcache('merchant_group','member');
        $m_names = array();
        foreach ($merchant_type as $_k=>$_v) {
            $m_names[] = array('id'=>$_v['groupid'],'name'=>$_v['name'],'money'=>substr($_v['pricetype'],strpos($_v['pricetype'],',') + 1));
        }
        //踢出普通商家
        array_shift($m_names);
        $totalmoney = 0;
        foreach ($m_names as $key=>$val) {
            //查出该升级类型商家总数
            $vip['groupid'] = $val['id'];
            $count = model('member')->where($vip)->count();
            $totalmoney = $totalmoney + (int) $count * (int) $val['num'];
        }
        $sqlMap['merchant_money'] = $totalmoney;
        //统计时间
        $sqlMap['inputtime'] = $currenttime;
        $sqlMap['updatetime'] = NOW_TIME;
        //将查询的数据插入数据库表（tp_today_statistics）
        $a = model('today_statistics')->where(array('inputtime'=>array('between',array($t_time,$e_time))))->find();
        if(!$a){
            $rs = model('today_statistics')->add($sqlMap);
        }else{
            $rs = model('today_statistics')->where(array('id'=>$a['id']))->save($sqlMap);
        }
        if($rs !== false){
            $this->success('更新成功');
        }
    }
}