<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Controller;
use \Common\Controller\BaseController;
define('MODULE_CACHE', DATA_PATH.'caches_model/');
Class IndexController extends BaseController {
    public function _initialize() {
    	parent::_initialize();
        $this->categorys = getcache('product_category', 'commons');
        $this->models = getcache('model', 'commons');
        $this->db = model("product");
        $this->member = model("member");
        $this->user_info = is_login();

        /* 将页码变量赋值到模板 */
    }
    /**
     * 主页
     * @author   <jason>
     */
    public function index() {
        $setting2 = getcache('setting', 'wap');
        $wap_domain = ltrim($setting2['wap_domain'], "'http://'");
        $detect = new \Wap\Library\Mobile_Detect();
        if ($detect->isMobile() && C('system_auth_type') == 'professional' ) {
            redirect('http://'.$wap_domain);
            return false;
        }

        $SEO = seo();
        //晒单达人
        //查出晒单最多的人数
        $result = model('report')->field('id,userid,content,report_imgs,goods_id,count(userid) AS total')->group('userid')->order('reporttime DESC')->limit(10)->select();
        foreach ($result as $k=>$v){
        	//根据userid查用户信息
        	$userinfo = model('member')->getByUserid($v['userid']);
        	$result[$k]['nickname'] = $userinfo['nickname'];
        	$result[$k]['avatar'] = getavatar($v['userid']);
            $factory = new \Product\Factory\product($v['goods_id']);
            $result[$k]['url'] = $factory->product_info['url'];
        	//根据产品id查产品信息
        	$result[$k]['prosub'] = model('product')->getFieldById($v['goods_id'],'title');
        }
        $SEO['title'] = C('SITE_WEB_TITLE').'-'.C('WEBNAME');
        $SEO['keyword'] = C('SITE_WEB_TITLE').'-'.C('WEBNAME');
        $SEO['description'] = C('SITE_WEB_TITLE').'-'.C('WEBNAME');
        //试用报告
        $trialinfos = model('trial_report')->field('id,thumb,userid,content,goods_id,count(userid) AS total')->group('userid')->order('inputtime DESC')->limit(4)->select();
        
        foreach ($trialinfos as $_k=>$_v){
        	$trialinfos[$_k]['nickname'] = model('member')->getFieldByUserid($_v['userid'],'nickname');
        	$trialinfos[$_k]['avatar'] = getavatar($_v['userid']);
        	$factory = new \Product\Factory\product($_v['goods_id']);
        	$trialinfos[$_k]['url'] = $factory->product_info['url'];
        	$trialinfos[$_k]['title'] = $factory->product_info['title'];
        }

        include template('index');

    }

    /* 商品总汇 */
    public function all() {
        $param = I('param.');
        extract($param);
        $catid = (int) $catid;
        if($catid > 0) {
            $category = $this->categorys[$catid];
            $category['setting'] = string2array($category['setting']);
            $arrparentid = explode(',', $category['arrparentid']);
            $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
            extract($category);
            $setting = $category['setting'];               
            $arrchild_arr = $this->categorys[$parentid]['arrchildid'];
            if($arrchild_arr=='') $arrchild_arr = $this->categorys[$catid]['arrchildid'];
            $arrchild_arr = explode(',',$arrchild_arr);
            array_shift($arrchild_arr);
            $SEO = seo($catid, $setting['meta_title'],$setting['meta_description'],$setting['meta_keywords'], 'product');
        } else {
            $SEO = seo('','商品总汇');
        }    	
        include template('all');
    }

    /**
     * 列表页
     * @author   <jason>
     */
    public function lists($catid = 0, $tpl = '', $extra = '',$mod='rebate') {  
        $catid = (int) $catid;
        $page = max(1, (int) I('page'));
        $mod = I('mod');
		$protype = I('protype');
        //判断该活动是否开启
        $is_open = is_activity_open($mod);
        if($is_open != 1){
        	$this->error('该活动尚未开启，请耐心等待');
        }
        if($catid > 0) {
            $category = $this->categorys[$catid];
            $category['setting'] = string2array($category['setting']);
            $arrparentid = explode(',', $category['arrparentid']);
            $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
            extract($category);
            $setting = $category['setting'];
            $SEO = seo($catid, $setting['meta_title'],$setting['meta_description'],$setting['meta_keywords'], 'product');           
            $arrchild_arr = $this->categorys[$parentid]['arrchildid'];
            if($arrchild_arr=='') $arrchild_arr = $this->categorys[$catid]['arrchildid'];
            $arrchild_arr = explode(',',$arrchild_arr);
            array_shift($arrchild_arr);
        }
        if($mod == 'rebate'){
        	$msg = C('REBATE_NAME');
        }else if($mod == 'trial'){
        	$msg = C('TRIAL_NAME');
        }else{
        	$msg = C('POSTAL_NAME');
        }
        $SEO = seo('',$msg.'商品列表');
        if ($protype == 2) {
            include template('red_'.$mod);

        }else{
           include template('list_'.$mod);

        }
    }

     /**
     * 名品管列表页
     * @author   <jason>
     */
    public function famous() {  
        $param = I('param.');
        extract($param);
        $catid = (int) $catid;
        if($catid > 0) {
            $category = $this->categorys[$catid];
            $category['setting'] = string2array($category['setting']);
            $arrparentid = explode(',', $category['arrparentid']);
            $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
            extract($category);
            $setting = $category['setting'];               
            $arrchild_arr = $this->categorys[$parentid]['arrchildid'];
            if($arrchild_arr=='') $arrchild_arr = $this->categorys[$catid]['arrchildid'];
            $arrchild_arr = explode(',',$arrchild_arr);
            array_shift($arrchild_arr);
            $SEO = seo($catid, $setting['meta_title'],$setting['meta_description'],$setting['meta_keywords'], 'product');
        } else {
            $SEO = seo('','名品馆');
        }       
        include template('list_famous');
    }

    /*0元邮*/
    public function favourable($catid = 0, $tpl = '', $extra = '',$mod='postal'){
        $catid = (int) $catid;
        $page = max(1, (int) I('page'));
        $protype = I('protype');
        //判断该活动是否开启
        $is_open = is_activity_open($mod);
        
        if($is_open != 1){
            $this->error('该活动尚未开启，请耐心等待');
        }
        if($catid > 0) {
            $category = $this->categorys[$catid];
            $category['setting'] = string2array($category['setting']);
            $arrparentid = explode(',', $category['arrparentid']);
            $top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
            extract($category);
            $setting = $category['setting'];
            $SEO = seo($catid, $setting['meta_title'],$setting['meta_description'],$setting['meta_keywords'], 'product');           
            $arrchild_arr = $this->categorys[$parentid]['arrchildid'];
            if($arrchild_arr=='') $arrchild_arr = $this->categorys[$catid]['arrchildid'];
            $arrchild_arr = explode(',',$arrchild_arr);
            array_shift($arrchild_arr);
        }
        if($mod == 'rebate'){
            $msg = C('REBATE_NAME');
        }else if($mod == 'trial'){
            $msg = C('TRIAL_NAME');
        }else{
            $msg = C('POSTAL_NAME');
        }
        $SEO = seo('',$msg.'商品列表');
        include template('list_favourable');
    }


    /**
     * 内容页浏览    [Adalways试用系统]
     * @author   <jason>
     */
    public function show($id = 0) {
        $id = (int) $id;
        if ($id < 1) $this->error('参数错误');

        //手机端图片调用250的
        $setting2 = getcache('setting', 'wap');
        $http_host = $_SERVER['HTTP_HOST'];
        $wap_domain = ltrim($setting2['wap_domain'], "'http://'");
        $detect = new \Wap\Library\Mobile_Detect();
        if ($detect->isMobile() && C('system_auth_type') == 'professional' ) {
            redirect('http://'.$wap_domain.'/#/tab/trial/'.$id,2, '正在加载触屏版...');
            $rs['thumb'] = img2thumb($rs['thumb'],'m',1);
            return false;
        }
        
        $rs['thumb'] = img2thumb($rs['thumb'],'',1);
        $factory = new \Product\Factory\product($id);
        $rs = $factory->product_info;
        if($rs['status'] < 1 && $rs['status'] != -1) { 
            $this->error('暂时无法查看该信息',U('Product/Index/all'));
        }




        $rs['buyer_user_count'] = count($factory->buyer_list());
        $rs['report_user_count'] = count($factory->report_list());
        if($rs['company_id']) {
            $rs['__seller__'] = model('member_merchant')->detail($rs['company_id']);
            if (!$rs['__seller__']['contact_want']) $rs['__seller__']['contact_want'] = '';
        }
        $rs['act_config'] = $factory->getConfig();
        extract($rs);
        // 获取商家信息
        if ( (int)$rs['company_id'] >0 ){
            $seller = member_info($rs['company_id']);
        }

        if ($rs['mod'] == 'commission') {
            $allow = string2array($rs['allow_groupid']);
        }
        if (!$seller && $rs['mod'] != 'postal')   $this->error('数据异常，请浏览其它商品！');
        $SEO = seo($catid, $title, $description, $keyword, 'product');
        /* 浏览量 */
        $this->db->where(array('id' => $id))->setInc('hits', 1);
        /* 更新商品库存 */
        $count = model('order')->where(array('goods_id'=>$id,'status'=>array('GT',1)))->count();
        $this->db->where(array('id' => $id))->setField('already_num', $count);

        $groups = getcache('member_group','member');
        $level = $groups[2];
        $day_count = $level['day_count'];
        $month_count = $level['month_count'];

        //查询今日这个用户试用次数
        $sql_where = array();
        $sql_where['is_vip_shi'] = 1;
        $time = strtotime(date("Y-m-d"));
        $sql_where['creat_time'] = array('gt',$time);
        $day_count_mysql = model('order')->where($sql_where)->count();
        //查询这月这个用户试用次数
        $sql_where2 = array();
        $sql_where2['is_vip_shi'] = 1;
        $time2 = strtotime(date("Y-m"));
        $sql_where2['creat_time'] = array('gt',$time2);
        $month_count_mysql = model('order')->where($sql_where2)->count();
        $day_count = $day_count-$day_count_mysql;
        $month_count = $month_count-$month_count_mysql;

        /*验证*/
        $user_info = $this->user_info;
    
        if($user_info){
            $buyer_join_condition = model('activity_set')->where(array('key'=>'buyer_join_condition','activity_type'=>'commission'))->getField('value');
            $buyer_join_condition = ($buyer_join_condition) ? string2array($buyer_join_condition) : '';
             // 统计实名认证
             $identity_count = model('member_attesta')->where(array('userid'=>$this->user_info['userid'],'type'=>'identity'))->count();
             /* 绑定淘宝账号 */
             $tb_count = model('member_bind')->where(array('userid'=>$this->user_info['userid'],'status'=>1))->count();

            // 已绑定的淘宝账号
             $bind_tbs = get_bind_taobao($this->user_info['userid']);

             /* 是否绑定支付宝 */
            $account = model('member_attesta')->where(array('userid'=>$this->user_info['userid'],'type'=>'alipay'))->count(); 
            /*是否完善资料*/
            $information = model('member_detail')->where(array('userid'=>$this->user_info['userid']))->find();     
            //完成试用活动的次数
            $tiral_num= model('order')->where(array('buyer_id'=>$this->user_info['userid'],'act_mod'=>'trial','status'=>7))->count();
            
            //统计还有多少没有完成
            $num = 0;
            if ($tiral_num <  $buyer_join_condition['num_trial_art']){
                $num = $num + 1;
            }

            if($buyer_join_condition['phone'] && !$this->user_info['phone_status']){
                $num = $num + 1;
            }
            if($buyer_join_condition['email'] && !$this->user_info['email_status']){
                $num = $num + 1;
            }
            if($buyer_join_condition['realname'] && $identity_count != 1){
                $num = $num + 1;
            }
            if($buyer_join_condition['bind_taobao'] && $tb_count < 1){
                $num = $num + 1;
            }
            if($buyer_join_condition['bind_alipay'] && $account != 1){
                $num = $num + 1;
            }

        }

        
    	include template('show_'.$mod);
    }

    /* 用户晒单 */
    public function report() {
        include template('report');
    }

    /*试用报告详情页*/
    public function report_show($id = 0){
        $id = (int)$id;
        if ($id < 1) $this->error('参数错误'); 
		//试用报告信息
        $report = model('trial_report')->find($id);
        $factory = new \Product\Factory\product($report['goods_id']);
		//产品信息
        $rs = $factory->product_info;
        $baseinfo = string2array($report['base_info']);
        $good = model('order')->where(array('id'=>$report['order_id']))->find();
        $alipay_count = model('order')->where(array('act_mod'=>'trial','buyer_id'=>
        $report['userid']))->count();
        $success_count = model('order')->where(array('act_mod'=>'trial','buyer_id'=>
            $report['userid'],'status'=>'7'))->count();
        $lists = model('trial_report')->where(array('goods_id'=>$report['goods_id']))->select();
        foreach ($lists as $key => $value) {
            $num = string2array($value['base_info']);
            $star += $num['star'];
        }
        $count = model('trial_report')->where(array('goods_id'=>$report['goods_id']))->count();
        $score = round($star/$count);
		//上一篇
		$prev = model('trial_report')->where(array('id'=>array('LT',$id)))->order('id DESC')->find();
		$prevf = new \Product\Factory\product($prev['goods_id']);
		
		//下一篇
		$next = model('trial_report')->where(array('id'=>array('GT',$id)))->order('id ASC')->find();
		$nextf = new \Product\Factory\product($next['goods_id']);
		
        $SEO = seo(0,'试用报告详情');
        include template('show_report');
    }
	
	/*试用报告列表页*/
	public function report_list(){
		//试用报告评优
		$result = model('trial_report')->alias('t')
				->join('__ORDER__ o ON t.order_id = o.id')
				->field('t.*,count(o.appraised) AS count,count(o.buyer_id) AS buy_num')
				->where('o.appraised = 1 AND t.userid=o.buyer_id')
				->group('t.userid')->order('count DESC')
				->limit(5)->select();

        include template('report_list');
	}

    public function notify(){
        $phone = $_POST['phone'];
         if(is_mobile(trim($phone)) != TRUE) {
            $this->error('手机号格式不正确');
         }
        $email = $_POST['email'];
         if(!isemail($email)) {
            $this->error('Email格式错误');
        }
        $goods_id = (int)$_POST['goods_id'];
       $count = model('member_notify')->where(array('userid'=>cookie('_userid'),'goods_id'=>$goods_id))->count();

        if ($count > 0) {
           $this->error('已订阅过，不须重复订阅');
        }
        if (cookie('_userid') == NULL) {
            $this->error('请登录');
        }

        $data = array();
        $data['phone'] = $phone;
        $data['email'] = $email;
        $data['goods_id'] = $goods_id;
        $data['userid'] = cookie('_userid');
        $data['ip'] = get_client_ip();
        $data['status'] = 0;
        $data['inputtime'] = NOW_TIME;
        $result = model('member_notify')->add($data);
        if ($result > 0) {
            $this->success('订阅成功');
        }else{
            $this->error('请稍后重试!');
        }
    }


    public function rand_lists(){
        $idss = model('product')->where(array('mod'=>'trial','status'=>1,'isrecommend'=>1))->limit(20)->getField('id',TRUE);
        $rand = array();
        if (count($idss)>5) {
            $_count = 6;
        }else{
            $_count = count($idss)-1;
        }
        $r = array_rand($idss,$_count);
        foreach ($r as $key => $v) {
             if ( !in_array($idss[$v],$rand) ) {
                $rand[] = $idss[$v];
            }
        }
         
        $sqlmap = array();
        $sqlmap['id'] = array('IN',$rand);
        $sqlmap['mod'] = array('EQ','trial');
        $sqlmap['status'] = array('EQ',1);
        $sqlmap['isrecommend'] = array('EQ',1);



        $count = model('product')->where($sqlmap)->count();
        $ids = model('product')->where($sqlmap)->limit(12)->select();
            if(!$ids) $this->error('没有找到商品');
            $lists = array();
            foreach($ids as $k=>$v) {
                $factory = new \Product\Factory\product($v['id']);
                $r = $factory->product_info;
                $r['mod_name'] = model('activity_set')->where(array('key' => $r['mod'].'_name'))->getField('value');
                $r['mod_unit'] = activitiy_price_name($r['mod']);
                $r['mod_price'] = price($r['id']);
                $r['img_source'] = get_shop_set($r['source'], 'small_logo');
                $r['number'] = $r['goods_number'] - buyer_count_by_gid($r['id']);//剩余份数
                $r['shop_source'] = get_shop_set($r['source'],'name');
                $r['start_time2'] = date("Y-m-d",$r['start_time']);
                $r['get_trial'] = get_trial_by_gid($r['id']); //已申请人数
                $r['title'] = str_cut($r['title'],36);
                $lists[$k] = $r;
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

         /**
     * 抢购支付宝检测
     */
    public function alipay_check(){
        //判断该用户是否绑定淘宝账号
        $rs = model('member_bind')->where(array('userid'=>$this->user_info['userid']))->select();
        
        include template('check_alipay');
    }
    /**
     * 判断抢购资格
     */
    public function person_check(){
        $taobao = I('taobao');
        $id = I('id');
        $factory = new \Product\Factory\product($id);
        $rs = $factory->product_info;
        //查处用户抢购资格
        $buyer_join_condition = model('activity_set')->where(array('key'=>'buyer_join_condition','activity_type'=>'commission'))->getField('value');
        $buyer_join_condition = ($buyer_join_condition) ? string2array($buyer_join_condition) : '';
         // 统计实名认证
        $identity_count = model('member_attesta')->where(array('userid'=>$this->user_info['userid'],'type'=>'identity'))->count();
         /* 绑定淘宝账号 */
        $tb_count = model('member_bind')->where(array('userid'=>$this->user_info['userid'],'status'=>1))->count();
         /* 是否绑定支付宝 */
        $account = model('member_attesta')->where(array('userid'=>$this->user_info['userid'],'type'=>'alipay'))->count(); 
        /*是否完善资料*/
        $information = model('member_detail')->where(array('userid'=>$this->user_info['userid']))->find();     
        //完成试用活动的次数
        $tiral_num= model('order')->where(array('buyer_id'=>$this->userinfo['userid'],'act_mod'=>'trial','status'=>7))->count();
        
        //统计还有多少没有完成
        $num = 0;
        /*if ($tiral_num <  $buyer_join_condition['num_trial_art']){
            $num = $num + 1;
        }*/
        if(empty($information)){
            $num = $num + 1;
        }
        if($buyer_join_condition['phone'] && !$this->user_info['phone_status']){
            $num = $num + 1;
        }
        if($buyer_join_condition['email'] && !$this->user_info['email_status']){
            $num = $num + 1;
        }
        if($buyer_join_condition['realname'] && $identity_count != 1){
            $num = $num + 1;
        }
        if($buyer_join_condition['bind_taobao'] && $tb_count < 1){
            $num = $num + 1;
        }
        if($buyer_join_condition['bind_alipay'] && $account != 1){
            $num = $num + 1;
        }

        include template('person_check');
    }


    public function index_cache(){

          //读取缓存时间
          $Data = F('index_cache');
          if(NOW_TIME > $Data +300 || !$Data){
            $fh= file_get_contents("http://".$_SERVER['HTTP_HOST']."/index.php?m=Product&c=index");
             //生成静态文件
            $myfile = fopen("index.html", "w") or die("Unable to open file!");
            //写入缓存时间
            F('index_cache',NOW_TIME);
            fwrite($myfile,$fh);
            fclose($myfile);  
            return true;            
          } 

         return false;    
    }


}
?>
