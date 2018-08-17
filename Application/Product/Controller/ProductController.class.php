<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Controller;
use \Admin\Controller\InitController;
class ProductController extends InitController
{
    public function _initialize(){
        parent::_initialize();
        $this->db = D('product');
        $this->activity_lists = C('ACTIVITY_LISTS');
        $this->activity_status = C('ACTIVITY_STATUS');
        $this->source = model('shop_set')->getField('id, name', TRUE);
        $this->userid = session('userid');//后台用户登陆id
    }

    public function manage(){
        $form = new \Common\Library\form();
        //所属专员
        $roleid = session('roleid');
        $sqlmap = array();
        //如果当前是招商专员
        if(session('roleid') ==6){
            $ids = model('member')->where(array('agent_id'=>$this->userid))->getField('userid',true);
            $sqlmap['company_id'] = array('in',$ids);
            $attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>6,'userid' =>cookie('userid')))->select();
        }else{
            $attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>6))->select();
            $attract = (isset($search['attract'])) ? $search['attract'] : -99;
            //所属专员查询
            if(isset($search['attract']) && $search['attract'] > -99){
                //查出该专员下的商家id
                $ids = model('member')->where(array('agent_id'=>$search['attract']))->getField('userid',true);
                $sqlmap['company_id'] = array('in',$ids);
            }
        }

        $param = I('param.');
        $search = $param['search'];
        $status = (isset($search['status'])) ? (int) $search['status'] : -99;

        if(isset($_GET['mod']) && !empty($_GET['mod'])) {
            $sqlmap['mod'] = $_GET['mod'];
        }
        if(submitcheck('searchbtn', 'GP')) {          
            if($status > -99) {
                $sqlmap['status'] = $search['status'];
            }
           
            if(isset($search['recommend'])){
                if ($search['recommend'] < 99) {
                     $sqlmap['isrecommend'] = $search['recommend'];
                }
            }

            if(isset($search['start_time']) && !empty($search['start_time'])) {
                $start_time = strtotime($search['start_time']. '00:00:00');
                $sqlmap['start_time'] = array("GT", $start_time);
            }
            
            if(isset($search['end_time']) && !empty($search['end_time'])) { 
                $end_time = strtotime($search['end_time'].' 23:59:59');
                $sqlmap['end_time'] = array("LT", $end_time);
            }
            
            if(isset($search['keyword']) && !empty($search['keyword'])) {
                if($search['type'] == 'product') {
                    $sqlmap['title|keyword'] = array("LIKE", "%".$search['keyword']."%");
                } elseif($search['type'] == 'company') {
                    $company_map = array();
                    $company_map['store_name'] = array("LIKE", "%".$search['keyword']."%");
                    $company_ids = model('member_merchant')->where($company_map)->getField('userid', 'TRUE');
                    $company_ids = (empty($company_ids)) ? array('-1') : $company_ids;
                    $sqlmap['company_id'] = array("IN", $company_ids);
                }elseif($search['type'] == 'goods_id'){
                    $sqlmap['id'] = $search['keyword'];
                }elseif($search['type'] == 'shop'){
                   $sqlmap['company_id'] = model('merchant_store')->where(array('store_name' => $search['keyword']))->getField('userid');
                }elseif($search['type'] == 'phone'){
                      $sqlmap['company_id'] = model('member')->where(array('phone' =>$search['keyword']))->getField('userid');
                }elseif($search['type'] == 'email'){
                      $sqlmap['company_id'] = model('member')->where(array('email' =>$search['keyword']))->getField('userid');
                }elseif ($search['type'] == 'userid') {
                   $sqlmap['company_id'] = $search['keyword'];
                }
            }
        }
        $count = $this->db->where($sqlmap)->count();
        $lists = $this->db->where($sqlmap)->page(PAGE, 10)->order('updatetime DESC,id DESC')->select();
        foreach ($lists as $k=>$v){
            $factory = new \Product\Factory\product($v['id']);
            $v = $factory->product_info;
            if($v['isstem'] == 0) {
                $lists[$k]['nickname'] = m('member')->getFieldByUserid($v['userid'],'nickname');
            }else{
                $lists[$k]['username'] = m('admin')->getFieldByUserid($v['userid'], 'realname');
            }
            $v['store_name'] = model('member_merchant')->where(array('userid' => $v['company_id']))->getField('store_name');

           
            //所属专员
            //1、查出该产品的商家的所属专员id
            $agent_id = model('member')->getFieldByUserid($v['company_id'],'agent_id');
            $v['attract'] = model('admin')->getFieldByUserid($agent_id,'username');
            $lists[$k] = $v;
        }

        $pages = page($count, 10);
        $show_header = FALSE;
        $show_dialog = FALSE;
        include $this->admin_tpl('product_manage'); 
    }
    
    /* 审核报名商品 */
    public function check() {
        //查看该会员是否为招商专员
        $r = model('admin_role')->field('roleid')->where(array('rolename'=>'招商专员'))->find();
        $roleid = session('roleid');
        $status = I('status', -2);
        $form = new \Common\Library\form();
        $sqlmap = array();
        $param = I('param.');
        $search = $param['search'];
         
        //如果当前是招商专员
        if(session('roleid') ==6){
            $ids = model('member')->where(array('agent_id'=>$this->userid))->getField('userid',true);
            $sqlmap['company_id'] = array('in',$ids);
            $attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>6,'userid' =>cookie('userid')))->select();
        }else{
            $attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>6))->select();
            $attract = (isset($search['attract'])) ? $search['attract'] : -99;
            //所属专员查询
            if(isset($search['attract']) && $search['attract'] > -99){
                //查出该专员下的商家id
                $ids = model('member')->where(array('agent_id'=>$search['attract']))->getField('userid',true);
                $sqlmap['company_id'] = array('in',$ids);
            }
        }

        if(isset($search['status'])) {
            $sqlmap['status'] = $search['status'];
            $status = $search['status'];
        }else{
            $sqlmap['status'] = $status;
        }
        if(submitcheck('searchbtn', 'GP')) {
            // $sqlmap['mod'] = array("IN", array('rebate', 'trial','postal'));
            if(isset($search['mod_type']) && !empty($search['mod_type'])) {
                switch ($search['mod_type']) {
                    case '1':
                        $ids = model('product_rebate')->where(array('rebate_type'=>1))->getField('id',TRUE);
                        $sqlmap['id'] = array('in',$ids);
                        $sqlmap['mod'] = 'rebate';
                        break;
                    case '2':
                    $sqlmap['mod'] = 'rebate';
                   //$sqlmap['rebate_type'] = 2;
                    $ids = model('product_rebate')->where(array('rebate_type'=>2))->getField('id',TRUE);
                    $sqlmap['id'] = array('in',$ids);
                    break;
                    default:
                        $sqlmap['mod'] = 'postal';
                        break;
                }
                
            }

            if(isset($search['start_time']) && !empty($search['start_time'])) {
                $start_time = strtotime($search['start_time']. '00:00:00');
                $sqlmap['start_time'] = array("GT", $start_time);
            }
            if(isset($search['end_time']) && !empty($search['end_time'])) {
                $end_time = strtotime($search['end_time'].' 23:59:59');
                $sqlmap['end_time'] = array("LT", $end_time);
            }
            if(isset($search['keyword']) && !empty($search['keyword'])) {
                if($search['type'] == 'product') {
                    $sqlmap['title|keyword'] = array("LIKE", "%".$search['keyword']."%");
                } elseif($search['type'] == 'company') {
                    $company_map = array();
                    $company_map['store_name'] = array("LIKE", "%".$search['keyword']."%");
                    $company_ids = model('merchant_store')->where($company_map)->getField('userid', 'TRUE');
                    $company_ids = (empty($company_ids)) ? array('-1') : $company_ids;
                    $sqlmap['company_id'] = array("IN", $company_ids);
                }elseif($search['type'] == 'phone'){
                      $sqlmap['company_id'] = model('member')->where(array('phone' =>$search['keyword']))->getField('userid');
                }elseif($search['type'] == 'phone'){
                      $sqlmap['company_id'] = model('member')->where(array('email' =>$search['keyword']))->getField('userid');
                }elseif ($search['type'] == 'userid') {
                   $sqlmap['company_id'] = $search['keyword'];

                    # code...
                }
            }
        }
         
        $count =$this->db->where($sqlmap)->count();
        $gids =$this->db->where($sqlmap)->page(PAGE, 10)->order('id DESC')->getField('id', TRUE);
        foreach ($gids as $gid){
            $factory = new \Product\Factory\product($gid);
            $r = $factory->product_info;
            $r['store_name'] = store_name($r['company_id']);
            //所属专员
            //1、查出该产品的商家的所属专员id
            $agent_id = model('member')->getFieldByUserid($r['company_id'],'agent_id');
            $r['attract'] = model('admin')->getFieldByUserid($agent_id,'username');
            $lists[$gid] = $r;
        }
        $pages = page($count, 10);
        $show_header = FALSE;
        $show_dialog = FALSE;
        include $this->admin_tpl('product_check');
    }

    
    /**
     * 审核通过
     * @param int $product_id 商品ID
     */
    public function pass($product_id) {
        $product_id = (int) $product_id;
        if ($product_id < 1) $this->error('商品ID有误');
        $factory = new \Product\Factory\product($product_id);
        $rs = $factory->product_info;
        if(!$rs) $this->error('参数错误');
        if(IS_POST) {
            extract($_POST);
            if (empty($start_time)) {
                $this->error('请设置上线时间');
            }
            if(is_array($start_time)) {
                $start_time = intval($start_time['Y']).'-'.intval($start_time['m']).'-'.intval($start_time['d']).' '.intval($start_time['H']).':'.intval($start_time['i']);
                $start_time = strtotime($start_time);
            }
            $result = $factory->pass($start_time, $msg);
            if(!$result) {
                $message = ($factory->getError()) ? $factory->getError() : '操作失败';
                $this->error($message);
            } else {
                /*招商专员--start*/
                 $companys = model('member')->field('agent_id')->find($rs['company_id']);
                if ($companys['agent_id'] > 0) {
                    $agent = model('admin')->where(array('userid'=>$companys['agent_id']))->field('fee_type,service_fee,roleid')->find();
                    if ($agent) {
                        if ($agent['fee_type'] == 1 && $agent['roleid'] == 6) {
                            $money =sprintf("%.2f",$rs['goods_deposit']*$agent['service_fee']/100);
                            $msg = "商家(id:".$rs['company_id']."),缴纳活动(id:".$rs['id'].")保证金".$rs['goods_deposit']."美元，提成".$money."美元";
                                $infos = array();
                                $infos['time'] = NOW_TIME;
                                $infos['type'] = 1;
                                $infos['money'] = $money;
                                $infos['agent_id']= $companys['agent_id'];
                                $infos['body'] = $msg;
                                model('company_log')->add($infos);
                            
                        }

                    }
                }

                /*招商专员--end*/
                runhook('product_check',array('userid' =>$rs['company_id'],'start_time' => $start_time,'title' => $rs['title'],'result' => 1));
                runhook('app_product_check',array('userid' =>$rs['company_id'],'start_time' => $start_time,'title' => $rs['title'],'result' => 1));

                $this->success('操作成功', 'javascript:close_dialog();');
            }
        } else {
            $form = new \Common\Library\form();
            $show_dialog = FALSE;
            include $this->admin_tpl('product_pass');
        }
    }
    
    /**
     * 审核拒绝
     * @param int $product_id 商品ID
     */
    public function refuse($product_id = 0) {
        $factory = new \Product\Factory\product($product_id);
        $result = $factory->refuse('审核拒绝并退还保证金');
        if(!$result) {
            $this->error($factory->getError());
        } else {
            runhook('product_check',array('userid' =>$factory->product_info['company_id'],'title' => $factory->product_info['title'],'result' => 0));
            runhook('app_product_check',array('userid' =>$factory->product_info['company_id'],'title' => $factory->product_info['title'],'result' => 0));

            $this->success('审核拒绝操作成功');
        }
    }
    
    /**
     * 确认付款
     * @param int $product_id  商品ID
     */
    public function pay($product_id = 0) {
        if ((int)$product_id < 1)   $this->error('该商品不存在！');
        $factory = new \Product\Factory\product($product_id);
        //将该产品的保证金及服务费存入数据库
        $rs = $factory->admin_pay();
        if(!$rs) $this->error($factory->getError());
        //设置订单状态
        $factory->set_status('-2', '后台管理员确认付款');
        $this->success('确认付款成功');
    }
    
    /**
     * 屏蔽商品
     * @param int $product_id 商品ID
     */
    public function blocked($product_id = 0) {
        $msg = I('msg');
        $msg = $msg ? $msg : '后台管理员屏蔽商品';
        $factory = new \Product\Factory\product($product_id);
        $result = $factory->set_status(5, $msg);
        if(!$result) $this->error('屏蔽商品失败');
        runhook('product_lock',array('userid' =>$factory->product_info['company_id'],'title' => $factory->product_info['title'],'cause' => $msg));
        runhook('app_product_lock',array('userid' =>$factory->product_info['company_id'],'title' => $factory->product_info['title'],'cause' => $msg));

        $this->success('屏蔽商品成功');
    }
    
    /**
     * 查看商品操作日志
     * @param int $product_id
     */
    public function log($product_id = 0) {
        $result = array('status' => 0);
        $factory = new \Product\Factory\product($product_id);
        $log = $factory->get_log();
        if($log) {
            foreach ($log as $k => $v) {
                $v['dateline'] = dgmdate($v['dateline'], 'Y/m/d H:i');
                if($v['is_sys'] == 1) {
                    $v['username'] = model('admin')->where(array('userid' => $v['uid']))->getField('username');
                } else {
                    $v['username'] = model('member')->where(array('userid' => $v['uid']))->getField('username');
                }
                $log[$k] = $v;
            }
            $result['status'] = 1;
            $result['data'] = $log;
        }
        echo json_encode($result);
    }
    
    /**
     * 发布商品
     */
    public function add(){
        $mod = I('mod', 'rebate');
        if(submitcheck('dosubmit')) {
            $info = $_POST['info']; 
            if ($info['mod'] == 'commission') {
               
               $info['goods_search_albums_url'] = $_POST['goods_search_albums_url'];
            }
         
            $result = $this->db->update($info);
            if(!$result) {
                $this->error($this->db->getError());
            } else {
                $this->success('产品发布成功', U('manage',array('mod'=>$info['mod'])));
            }
        } else {
            $show_header = $show_dialog = $show_validator = 1;
            $form = new \Common\Library\form();
            include $this->admin_tpl('product_add_'.$mod);
        }
    }


    /**
     * 佣金验证
     */
    public function reward(){
        //$company_id = $this->userid;
        $goods_price = I('goods_price');//下单价
        //$goods_price = 12;
        //判断该商家是那种类型的类型 
        $setting = model('activity_set')->where(array('activity_type' =>'commission'))->getField('key,value');
        //$price_type = $config['seller_price'];
        $bonus_price = string2array($setting['commission_price']);
        $bonus = $bonus_price['commission'];
        $price_type = $bonus['commission_price'];
        array_multisort($price_type,'min','SORT_ASC');
        if(!empty($price_type) && is_array($price_type)){
            $price = 0;//缴纳的佣金
            foreach ($price_type as $k=>$v){
                //判断下单在那个范围，应缴纳多少佣金  $max<$price_type<$min
                if( ($goods_price >= $v['min'] && $goods_price <= $v['max'])){
                     $price = sprintf('%.2f',$v['commission']);
                     break;
                    
                }
            }
        }
    
      echo $price;
        
    }
    
    /**
     * 编辑商品
     * @param int $id 商品ID
     */
    public function edit($id = 0){
        $rs = $this->db->detail($id);
        if(!$rs) $this->error($this->db->getError());
        if(submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['id'] = $id;

            if ($info['mod'] == 'commission') {
               
               $info['goods_search_albums_url'] = $_POST['goods_search_albums_url'];
               $info['allow_groupid'] = array2string($info['allow_groupid']);

            }


         
            $result = $this->db->update($info);
          
            if(!$result) {
                $this->error($this->db->getError());
            } else {
                $this->success('产品编辑成功', U('manage',array('mod'=>$info['mod'])));
            }
        } else {
            $show_header = $show_dialog = $show_validator = 1;
            $form = new \Common\Library\form();
            include $this->admin_tpl('product_edit_'.$rs['mod']);
        }
    }

    /* 删除商品 */
    public function delete(){
         
        if(submitcheck('dosubmit', 'GP')) {
           $ids = (array) I('ids');
            if(empty($ids)) $this->error('参数错误');
            foreach($ids as $id) {
               $factory = new \Product\Factory\product($id);  
                if($factory->product_info['status'] != 0){

                    $this->error($id.'当前商品状态不允许删除！');
                }

                $sqlmap =array();
                $sqlmap['goods_id'] = $id;
                $sqlmap['status'] = array('GT',0);

                if(model('order')->where($sqlmap)->limit(1)->select()){
                    $this->error('当前商品有进行中的订单，无法删除！');

                }; 

                $this->db->pro_delete($id);
            }

             $this->success('产品删除成功');
        } else {
            $this->error('请勿非法访问');
        }
    }


    /* 活动商品结算 [kza] */
    public function activity_over(){
    	$id = (int)I('id');
    	if ($id < 1) $this->error('该商品不存在！'); 
    	$factory = new \Product\Factory\product($id);
        if (IS_POST) {
            $factory->action_over_info();
            if(!$factory) {
                $this->error($factory->getError());
            }else{
                runhook('product_balance',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title']));
                runhook('app_product_balance',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title']));

                $this->success('活动商品结算完成', 'javascript:close_dialog();');
            }
        }else{
            $info = $factory->get_over_info();
            if(!$info) $this->error($factory->getError(), 'javascript:close_dialog();');
            include $this->admin_tpl('activity_over');
        }        
    }

      /* 活动商品结算 [kza] */
    public function add_time(){
        $id = (int)I('id');
        if ($id < 1) $this->error('该商品不存在！'); 
        $factory = new \Product\Factory\product($id);
        if (IS_POST) {
            $day = 7;
            $factorys = new \Product\Factory\product($id);
            $proInfos = $factorys->product_info;
            if ($proInfos['status'] != 2) $this->error('必须是正在结算中的商品才能追加时间！');
            if (!is_numeric($day)) {
                $this->error('请输入追加天数');
            }

            $new_day = NOW_TIME + ($day*86400);
            if (NOW_TIME > $new_day) {
                $this->error('追加时间已过，温馨提示只能在活动结束7天内进行延期！');
            }
            $result = model('product')->where(array('id' => $id))->setField('end_time',$new_day);
            $now_day = $proInfos['activity_days']+$day;
            $results = model('product_'.$proInfos['mod'])->where(array('id' => $id))->save(array('activity_days'=>$now_day));
            if ($result && $results) {
                
                model('product')->where(array('id' => $id))->setField('status',1);
                
            }



            $this->success('追加成功','javascript:close_dialog();');


           /* if(!$factory) {
                $this->error($factory->getError());
            }else{


              
                $this->success('追加时间完成', 'javascript:close_dialog();');
            }*/
        }else{
            $info = $factory->product_info;
            include $this->admin_tpl('add_time');
        }        
    }

    /*审核追加商品*/
    public function push_product(){
        
        $sqlMap = array();
        if (!empty($_GET['status'])) {
             $sqlMap['status'] = array('EQ','2');
        }

        if (IS_GET) {
             
             $sqlMap =array();
            //如果当前是招商专员
            if(session('roleid') ==6){
                $ids = model('member')->where(array('agent_id'=>$this->userid))->getField('userid',true);
                $sqlMap['company_id'] = array('in',$ids);
                $attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>6,'userid' =>cookie('userid')))->select();
            }else{
                $attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>6))->select();
                $attract = (isset($search['attract'])) ? $search['attract'] : -99;
                //所属专员查询
                if(isset($search['attract']) && $search['attract'] > -99){
                    //查出该专员下的商家id
                    $ids = model('member')->where(array('agent_id'=>$search['attract']))->getField('userid',true);
                    $sqlMap['company_id'] = array('in',$ids);
                }
            }
            
            $sqlMap['status'] = 2;
            $info = I('search');
            $info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
            $info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
            /* 下单时间 */
            if ($info['start_time'] && $info['end_time']){
                $sqlMap['dateline'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
            }else{
                if ($info['start_time'] > 0) {
                $sqlMap['dateline'] = array("EGT", $info['start_time']);
                }
                if ($info['end_time'] > 0) {
                    $sqlMap['dateline'] = array("ELT", $info['end_time']);
                }
            }

            if ($info['mod']) {
                $gids = model('product')->where(array('mod'=>array("EQ",$info['mod'])))->getfield('id',true);
                $sqlMap['product_id'] = array("IN", $gids);
            }

            $info['type'] = (int) $info['type'];
            $info['keyword'] = trim($info['keyword']);
            if ($info['keyword']) {
                switch ($info['type']) {
                    
                    case '2': //商家店铺
                        $uids = model('member_merchant')->where(array('store_name'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
                        $sqlMap['company_id'] = array("IN", $uids);
                        continue;
                    case '1': //商品标题
                        $gids = model('product')->where(array('title'=>array("LIKE", "%".$info['keyword']."%")))->getfield('id',true);
                        $sqlMap['product_id'] = array("IN", $gids);
                        continue;
                }
            }
            
          
        }
        $count = model('product_complement')->where($sqlMap)->count();
        $lists = model('product_complement')->where($sqlMap)->page(PAGE, 10)->order('id DESC')->select();
        foreach ($lists as $k=>$v){
            $factory = new \Product\Factory\product($v['product_id']);
            $v['product_info'] = $factory->product_info;
            $v['thumb'] = $factory->product_info['thumb'];
            $v['title'] = $factory->product_info['title'];
            $v['goods_price'] = $factory->product_info['goods_price'];
            $v['url'] = $factory->product_info['url'];
            $v['goods_service'] = $factory->product_info['goods_service'];
            $v['goods_id'] = $factory->product_info['id'];
            $v['store_name'] = model('merchant_store')->where(array('userid' => $v['company_id']))->getField('store_name');
            //所属专员
            $agent_id = model('member')->getFieldByUserid($v['company_id'],'agent_id');
            $v['attract'] = model('admin')->getFieldByUserid($agent_id,'username');
            $lists[$k] = $v;
        }
        $pages = page($count, 10);
        $show_header = TRUE;
        $show_dialog = TRUE;
        $form = new \Common\Library\form();
        include $this->admin_tpl('push_product_check'); 

    }

   public function  push_product_check(){
        $id = I('id');
        if ($id < 1)  $this->error('参数错误！');
        $result = model('product_complement')->find($id);
        if (!$result)  $this->error('参数错误！');
        $factory = new \Product\Factory\product($result['product_id']);
        $product_info =  $factory->product_info;
        if ($product_info['status'] != 1) {
            $this->error('须活动正在进行中的商品才能追加库存！');
        }

        if ($product_info['mod'] == 'rebate'){

                if($product_info['service_type'] == 1){
                   $total = sprintf("%.2f",($product_info['goods_price'] + ($product_info['goods_price'] * $product_info['goods_service'] / 100)) * $result['com_number']);
                }
                // 如果为部分缴纳方式
                if($product_info['service_type'] == 2){
                    $total = sprintf("%.2f",(($product_info['goods_price']-($product_info['goods_price'] / 10 *  $product_info['goods_discount'] ) )+ ($product_info['goods_price'] * $product_info['goods_service'] / 100)) * $result['com_number']);
                } 
            }else{//免费试用
                if ($product_info['goods_charge_way'] == 0) {
                    //总价 = (下单价 + 平台推广费[单份] + 红包[单份]) * 数量   
                    $total = sprintf('%.2f',($product_info['goods_price'] + $product_info['goods_service'] + $product_info['goods_bonus']) * $result['com_number']);
                }else{
                    //总价 = (下单价 + 红包[单份]) * 数量 + 收费价格 [单场] 
                    $total = sprintf('%.2f',($product_info['goods_price'] + $product_info['goods_bonus']) * $result['com_number'] + $product_info['goods_service']);
                }
            }
    if ($total != $result['com_total_fee']) $this->error('价格有误，请重新追加库存或联系管理员!');

        if ($result['status'] == 1) {
           $this->error('该追加的商品已操作过，请勿重新审核！');
        }

        $rs = model('product_complement')->where(array('id' => $id))->setField('status',1);

        $end_time = $this->db->where(array('id'=>$result['product_id']))->setInc('end_time',($result['com_day']*86400));
            // 增加追加的库存
        if ($end_time) {
                $goods_number = model('product_'.$product_info['mod'])->where(array('id'=>$result['product_id']))->setInc('goods_number',$result['com_number']);
                if ($goods_number) {

                    $notify = model('member_notify')->where(array('goods_id'=>$result['product_id']))->select();
                    foreach ($notify as $k => $v) {
                        runhook('goods_number_notify',array('userid' =>$v['userid'],'phone'=>$v['phone'],'email'=>$v['email'],'title' => $factory->product_info['title']));
                         model('member_notify')->where(array('id' => $v['id']))->setField('status',1);
                         model('member_notify')->where(array('id' => $v['id']))->save(array('updatetime'=>NOW_TIME));
                    }


                     /*招商专员--start*/
                    $companys = model('member')->field('agent_id')->find($product_info['company_id']);
                     if ($companys['agent_id'] > 0) {
                    $agent = model('admin')->where(array('userid'=>$companys['agent_id']))->field('fee_type,service_fee,roleid')->find();
                    if ($agent) {
                        if ($agent['fee_type'] == 1 && $agent['roleid'] == 6) {
                            $money =sprintf("%.2f",$result['com_total_fee']*$agent['service_fee']/100);
                            $msg = "商家(id:".$product_info['company_id']."),追加活动(id:".$product_info['id'].")保证金".$result['com_total_fee']."美元，提成".$money."美元";
                                $infos = array();
                                $infos['time'] = NOW_TIME;
                                $infos['type'] = 1;
                                $infos['money'] = $money;
                                $infos['agent_id']= $companys['agent_id'];
                                $infos['body'] = $msg;
                                model('company_log')->add($infos);
                            
                              }

                        }
                     }

                /*招商专员--end*/



                    $this->success('操作成功！');
                }

            }else{
                $this->error('操作失误，请稍后重试！');

        }

   }

   public function push_refuse(){
        $id = I('id');
        if ($id < 1)  $this->error('参数错误！');
        $result = model('product_complement')->find($id);
        if (!$result)  $this->error('参数错误！');
        $factory = new \Product\Factory\product($result['product_id']);
        $proInfo =  $factory->product_info;
        model('product_'.$proInfo['mod'])->where(array('id'=>$proInfo['id']))->setDec('goods_deposit',$result['com_total_fee']);
            // 将保证金写入商家保证金字段
            model('member_merchant')->where(array('userid'=>$result['company_id']))->setDec('frozen_deposit',$result['com_total_fee']);
            // 将追加表的状态更新为已支付
            model('product_complement')->where(array('id' => $result['id']))->setField('status',3);
            //将此信息写入记录表中
            $msg = 'id:'.$proInfo['id'].','.$proInfo['title'].'退还追加库存担保金';
            $sign = '2-'.$proInfo['mod'].'-'.$result['company_id'].'-'.$proInfo['id'].'-4'.$result['id'];
            $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
            if(!$rs){
                action_finance_log($result['company_id'],$result['com_total_fee'],'money',$msg,$sign,array('goods_id'=>$proInfo['id']));
                //加入产品日志
                product_log($proInfo['id'],'1','0',$result['company_id'],'退还追加库存担保金');
                $this->success('交付成功,请等待管理员审核');
            }else{
                $this->error('交付失败，一天只能追加一次');
            }


   }

   /*撤销屏蔽商品*/

     /* 删除商品 */
    public function cancel(){
        if(submitcheck('dosubmit', 'GP')) {
            $ids = (array) I('ids');
            if(empty($ids)) $this->error('参数错误');
            foreach($ids as $id) {
                    $msg =  '后台管理员撤销屏蔽商品';
                    $factory = new \Product\Factory\product($id);
                    $proInfo =  $factory->product_info;
                    if($proInfo['status'] !=5){
                        $this->error('该商品状态不是屏蔽状态');
                        return false;
                    }

                    $result = $factory->set_status(2, $msg);   
                }

                     $this->success('操作成功');
        } else {
            $this->error('请勿非法访问');
        }
    }


}