<?php
namespace Product\Controller;
class ApiController extends \Common\Controller\BaseController
{
    public function _initialize() {
        parent::_initialize();
    }
    
    /**
     * 用户抢购
     * @param int $goods_id 商品ID
     * @return bool
     */
    public function pay_submit($goods_id = 0) {
        $goods_id = (int) $goods_id;
        $talk_content = trim($_POST['talk_content']);
        $data_type = $_POST['data_type'];
        if($goods_id < 1) $this->error('参数错误');
        $Factory = new \Product\Factory\product($goods_id);
        // 读取后台活动设置：参与条件
        $bind_set = string2array(C_READ('buyer_join_condition','trial'));
        $com_set = string2array(C_READ('buyer_join_condition','commission'));

        if ($Factory->product_info['mod'] == 'trial' && $bind_set['bind_taobao'] == 4) {
            $bind_taobao = (int)trim($_POST['bind_taobao']);
            if ($bind_taobao < 1) $this->error('请选择您要购买的亚马逊帐号');
            $result = $Factory->pay_submit($talk_content,$bind_taobao,$data_type);
        }elseif($Factory->product_info['mod'] == 'commission' && $com_set['bind_taobao'] == 4){
            $bind_taobao = (int)trim($_POST['bind_taobao']);
            if ($bind_taobao < 1) $this->error('请选择您要购买的亚马逊帐号');
            $result = $Factory->pay_submit($bind_taobao);
        }else{
          
            $result = $Factory->pay_submit($talk_content,'',$data_type);
        }
        if(!$result) $this->error($Factory->getError());
          
        if ($Factory->product_info['mod'] == 'trial'){
            $content = nickname($Factory->product_info['company_id'])."您好 您的商品：".$Factory->product_info['title']." ,新增1名试客申请 请及时审核他的试用资格！ 谢谢";
            $message = array();
            $message['send_from_id'] = 1;
            $message['send_to_id'] = $Factory->product_info['company_id'];
            $message['subject'] = '新增1名试客申请';
            $message['content'] = $content;
            $api = new \Message\Library\api();
            $api->send_mess($message);
          }
        $is_vip_shi = model('order')->getFieldById($result,'is_vip_shi');
        $this->success(array('info' => '抢购成功','oid'=>$result,'is_vip_shi'=>$is_vip_shi));
    }   

    /* 用户抢购列表 */
    public function buyer_list() {
        $param = I('param.');
        extract($param);
        $limit = (isset($param['limit'])) ? (int) $param['limit'] : 10;
        
        $sqlmap = array();
        if(isset($goods_id) && !empty($goods_id)) {
            $sqlmap['goods_id'] = $goods_id;
        }
        if(isset($mod) && !empty($mod)) {
            $sqlmap['act_mod'] = $mod;
        }
        $sqlmap['status'] = array("GT", 1);
        $count = model('order')->where($sqlmap)->count();
        $uids = model('order')->where($sqlmap)->order('id DESC')->page(PAGE, $limit)->getField('buyer_id', TRUE);
        $lists = array();
        foreach ($uids as $k => $uid) {
           $uinfo = model('member')->getByUserid($uid);
           $uinfo['avatar'] = getavatar($uid);
           $uinfo['nickname'] = nickname($uid);
           $lists[] = $uinfo;
        }
        $pages = pages($count, $limit);
        echo json_encode(array('count' => $count, 'lists' => $lists, 'pages' => $pages));
    }

    /**
     * 买家晒单
     * @param int $goods_id 商品ID
     * @param int $limit    显示数量
     * @param int $page     当前分页
     * @author 老孔
     */
    public function report_list() {
        $attr = I('param.');
        $sqlmap = array();
        if(isset($attr['userid']) && is_numeric($attr['userid']) && $attr['userid'] > 0) {
            $sqlmap['userid'] = $attr['userid'];
        }
        if(isset($attr['goods_id']) && is_numeric($attr['goods_id']) && $attr['goods_id'] > 0) {
            $sqlmap['goods_id'] = $attr['goods_id'];
        }
        if(isset($attr['order_id']) && is_numeric($attr['order_id']) && $attr['order_id'] > 0) {
            $sqlmap['order_id'] = $attr['order_id'];
        }
        $sqlmap['status'] = 1;
        if (isset($attr['where'])) {
            $sqlmap['_string'] = $attr['where'];
        }
        $count = model('report')->where($sqlmap)->count();
        $result = model('report')->where($sqlmap)->page(PAGE, $attr['limit'])->select();
        $lists = array();
        if($result) {
            foreach ($result as $k => $v) {
                $v['nickname'] = getUserInfo($v['userid'], 'nickname');
                $v['avatar'] = getavatar($v['userid']);
                $v['dateline'] = dgmdate($v['reporttime'], 'Y年m月d日H时');
                $v['content'] = str_cut(strip_tags($v['content']), 70);
                $factory = new \Product\Factory\product($v['goods_id']);
                $v['product'] = $factory->product_info;
                $lists[$k] = $v;
            }
        }
        $pages = pages($count, $attr['limit']);
        echo json_encode(array('count' => $count, 'lists' => $lists, 'pages' => $pages));
    }

    /* 试用报告列表 */
    public function trail_report() {
        $attr = I('param.');
        $sqlmap = array();
        if(isset($attr['userid']) && !empty($attr['userid'])) {
            $sqlmap['userid'] = $attr['userid'];
        }
        if(isset($attr['goods_id']) && !empty($attr['goods_id'])) {
            $sqlmap['goods_id'] = $attr['goods_id'];
        }
        if(isset($attr['order_id']) && !empty($attr['order_id'])) {
            $sqlmap['order_id'] = $attr['order_id'];
        }        
        $sqlmap['status'] = 1;
        $count = model('trial_report')->where($sqlmap)->count();
        $result = model('trial_report')->where($sqlmap)->page(PAGE, $attr['limit'])->order("id desc")->select();
        $lists = array();
        $image =new \Think\Image();
        foreach ($result as $key => $val) {

             if(!file_exists(SITE_PATH.$val['thumb'])){
                unset($result[$key]);
                unset($val);
                continue;
            }

            $val['content'] = dstripslashes($val['content']);
            preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $val['content'], $matches);
            $val['nickname'] = nickname($val['userid']);
            $val['url'] = U('product/index/report_show',array('id'=>$val['id']));
            $val['inputtime'] = dgmdate($val['inputtime'],'Y年m月d日H时');
            $val['albums'] = (array) $matches[3];
            $val['base_info'] = string2array($val['base_info']);
            $val['content'] = str_cut(strip_tags($val['content']), 70);
            $val['avatar'] = getavatar($val['userid']);
            $factory = new \Product\Factory\product($val['goods_id']);
            $val['product'] = $factory->product_info;
            $image = getimagesize(SITE_PATH.$val['thumb']);
            $val['width']  = $image[0];
            $val['height'] = floor($image[1] * (260 / $val['width']));
            $lists[$key] = $val;

        }
        $pages = pages($count, $attr['limit']);
        echo json_encode(array('count' => $count, 'lists' => $lists, 'pages' => $pages));
    }

    /**
     * 获取内容
     */
    public function getlists() {
        $param = I('param.');
        extract($param);
        $catid = max(0, (int) $catid);
        $page = max(1, (int) $page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 20;
        $keyword = remove_xss($keyword);
        $sqlmap = array();
        if($mod == 'trial'){
            $protype = isset($protype) ? (int) $protype : -1;
            if($protype > -1){
                if($protype == 0){
                    //$sqlmap['t.goods_tryproduct'] = array('EQ','0');
                   $sqlmap['t.goods_tryproduct'] =  array(array('EQ','0'),array('EQ',''), 'or');  
                   $sqlmap['t.goods_bonus'] = array('EQ','0.00');

                }else if($protype==1){
                    $sqlmap['t.goods_tryproduct'] =array(array('NEQ','0'),array('NEQ',''), 'and');
                } else {
                    $sqlmap['t.goods_bonus'] = array('NEQ','0');
                }
             }else{
                //$sqlmap['t.goods_bonus'] = array('NEQ','0');
             }
        }
       
        if ($type == 1) {
            $sqlmap['t.goods_price'] = array(array('EGT',0),array('ELT',3)) ;
           
        }
       

        if ($type == 2) {
            $sqlmap['t.source'] = array('EQ',2);
        }

        if(isset($status)){
            $sqlmap['p.status'] = $status;
        }else{
            $sqlmap['p.status'] = 1;
            $sqlmap['p.start_time'] = array("LT", NOW_TIME);
            $sqlmap['p.end_time'] = array("GT", NOW_TIME);
        }
        if($mod && in_array($mod, array('rebate', 'postal', 'trial'))) {
            $sqlmap['p.mod'] = $mod;
        }
        
        if($keyword) {
            if($type == 'c') {
                $com_map = array();
                $com_map['store_name'] = array("LIKE", "%".$keyword."%");
                $company_ids = model('member_merchant')->where($com_map)->getField('userid', TRUE);                
                if(!$company_ids) {
                    $this->error('没有任何内容');
                }
                $sqlmap['p.company_id'] = array("IN", $company_ids);
            } else {
                $sqlmap['p.title|p.keyword'] = array("LIKE", "%".$keyword."%");
            }   
        }
        
        $company_id = (int) $company_id;
        if($company_id > 0) {
            $sqlmap['p.company_id'] = $company_id;
        }
       
        if($catid > 0) {
            $categorys = getcache('product_category', 'commons');
            $category = $categorys[$catid];
            $catids = $category['arrchildid'];
            if($catids) {
                $sqlmap['p.catid'] = array("IN", $catids);
            } else {
                $this->error('没有内容');
            }
        }   
        if ($orderby == 'null' || $orderway == 'null' || !$orderby || !$orderway){
            $orderby = 'id';
            $orderway = 'desc';
        }

      /* if ($orderby == 'already_num' ) {
                $orderby  = 'p.'.$orderby;
            }else{
                $orderby  = 't.'.$orderby;
        }*/

        if($mod){
            $count = model('product')->alias('p')->join(C('DB_PREFIX').'product_'.$mod.' AS t ON p.id = t.id')->where($sqlmap)->count();
            $ids = model('product')->alias('p')->join(C('DB_PREFIX').'product_'.$mod.' AS t ON p.id = t.id')->where($sqlmap)->page($page, $num)->order($orderby.' '.$orderway)->field('p.id')->select();
       
        }else{
            $sqlmap['p.mod'] = array('NEQ','trial');
            $count = model('product')->alias('p')->where($sqlmap)->count();
            $ids = model('product')->alias('p')->where($sqlmap)->page($page, $num)->order($orderby.' '.$orderway)->field('p.id')->select();

        }
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
            $r['url'] = $r['url'];
            $r['goods_number'] = $r['goods_number'];
            $r['goods_tryproduct'] = $r['goods_tryproduct'];

            $r['goods_bonus'] = $r['goods_bonus'];

            $lists[$k] = $r;
        }

        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['data'] = array(
            'count' => $count,
            'lists' => $lists,
            'pages' => $pages,

        );
        echo json_encode($result);
    }

    /*一键获取亚马逊商品信息*/
    public function go_link(){
        $go_link = I('go_link');

        $go_link = htmlspecialchars_decode($go_link);

        $is_tao = strstr($go_link,'taobao.com');
        $is_tmall = strstr($go_link,'tmall.com');
        $is_jd = strstr($go_link,'jd.com');
        if($is_tao){
            $return = go_taobao($go_link);

        }else if($is_tmall){
            $return = go_tmall($go_link);
        }else if($is_jd){
            $return = go_jd($go_link);
        }
        $url = $return['img'];
        if ($url) {
            $img = file_get_contents($url); 
            $date=date('Y',time());
            $m_d=date('md',time());
            $img_name=date('YmdHis',time());

           $root_url =  $_SERVER['DOCUMENT_ROOT'];
           /*判断是否存在此目录*/
            if(!is_dir($root_url.'/uploadfile/'.$date.'/'.$m_d)){
                    mkdir($root_url.'/uploadfile/'.$date.'/'.$m_d);
                    chmod($root_url.'/uploadfile/'.$date.'/'.$m_d,0777);
                    
            }

           $path_name = $root_url. '/uploadfile/'.$date.'/'.$m_d.'/'.$img_name.'.jpg';
           $path_name1 = '/uploadfile/'.$date.'/'.$m_d.'/'.$img_name.'.jpg';
         
            /*缩略图150要保存的位置*/
           $t_path= $root_url.'/uploadfile/'.$date.'/'.$m_d.'/'.$img_name.'_150.jpg';
             /*缩略图250要保存的位置*/
           $t_path2= $root_url.'/uploadfile/'.$date.'/'.$m_d.'/'.$img_name.'_250.jpg';
            file_put_contents($path_name,$img,FILE_APPEND); 
            get_thumb($path_name,$t_path,150,150);
            get_thumb($path_name,$t_path2,250,250);


            $return['img'] = $path_name1;
        }
       
        echo json_encode($return);
    }
     

    public function rand_lists(){
        $total = 100;//要产生多少个随机数;
        $min = 1;//随机数起始位置,
        $max = 999;//随机数结束位置
        
        $rand = array();
        while (count($rand) < $total ) {
        $r = mt_rand($min,$max);
            if ( !in_array($r,$rand) ) {
            $rand[] = $r;
            }
         }
        $sqlmap = array();
        $sqlmap['id'] = array('IN',$rand);
        $sqlmap['mod'] = array('EQ','rebate');

        $count = model('product')->where($sqlmap)->count();
        $ids = model('product')->where($sqlmap)->limit(3)->select();
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

    public function v2_getlists($title_siez = 36) {
        $param = I('param.');
        extract($param);
        $catid = max(0, (int) $catid);
        $page = max(1, (int) $page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 20;
        $keyword = remove_xss($keyword);
        $sqlmap = array();
        if($mod == 'trial'){
            $protype = isset($protype) ? (int) $protype : -1;
            if(isset($protype) ){
                if($protype ==1){
                    $sqlmap['t.goods_tryproduct'] =array(array('EQ',0),array('EQ',''),'or');
                    $sqlmap['t.goods_bonus'] =array(array('EQ',0));

                }

                if($protype ==2){
                    $sqlmap['t.goods_tryproduct'] =array(array('NEQ','0'),array('NEQ',''), 'and');
                } 

                if($protype ==3) {
                    $sqlmap['t.goods_bonus'] = array('NEQ','0');
                }

             }
        }

       
        if ($type == 1) {
            $sqlmap['t.goods_price'] = array(array('EGT',0),array('ELT',3)) ;
           
        }
       

        if ($type == 2) {
            $sqlmap['t.source'] = array('EQ',2);
        }

        if ($title != '') {
            $sqlmap['p.title'] = array('LIKE',"%".$title."%");

        }

        if(isset($status)){
            $sqlmap['p.status'] = $status;
        }else{
            $sqlmap['p.status'] = 1;
            $sqlmap['p.start_time'] = array("LT", NOW_TIME);
            $sqlmap['p.end_time'] = array("GT", NOW_TIME);
        }
        if($mod && in_array($mod, array('rebate', 'postal', 'trial','commission'))) {
            $sqlmap['p.mod'] = $mod;
        }
        
        if($keyword) {
            if($type == 'c') {
                $com_map = array();
                $com_map['store_name'] = array("LIKE", "%".$keyword."%");
                $company_ids = model('member_merchant')->where($com_map)->getField('userid', TRUE);                
                if(!$company_ids) {
                    $this->error('没有任何内容');
                }
                $sqlmap['p.company_id'] = array("IN", $company_ids);
            } else {
                $sqlmap['p.title|p.keyword'] = array("LIKE", "%".$keyword."%");
            }   
        }
        
        $company_id = (int) $company_id;
        if($company_id > 0) {
            $sqlmap['p.company_id'] = $company_id;
        }
       
        if($catid > 0) {
            $categorys = getcache('product_category', 'commons');
            $category = $categorys[$catid];
            $catids = $category['arrchildid'];
            if($catids) {
                $sqlmap['p.catid'] = array("IN", $catids);
            } else {
                $this->error('没有内容');
            }
        }   
        if ($orderby == 'null' || $orderway == 'null' || !$orderby || !$orderway){
            $orderby = 'id';
            $orderway = 'desc';
        }

        if($mod){
             if ($orderby == 'already_num' ) {
                $orderby  = 'p.'.$orderby;
            }else{
                $orderby  = 't.'.$orderby;
            }
            $count = model('product')->alias('p')->join(C('DB_PREFIX').'product_'.$mod.' AS t ON p.id = t.id')->where($sqlmap)->count();
            $ids = model('product')->alias('p')->join(C('DB_PREFIX').'product_'.$mod.' AS t ON p.id = t.id')->where($sqlmap)->page($page, $num)->order($orderby.' '.$orderway)->field('p.id')->select();
        }else{
            $sqlmap['p.mod'] = array(array('NEQ','rebate'),array('NEQ','postal'));
            $count = model('product')->alias('p')->where($sqlmap)->count();
            $ids = model('product')->alias('p')->where($sqlmap)->page($page, $num)->order($orderby.' '.$orderway)->field('p.id')->select();

        }
        if(!$ids) $this->error('没有找到商品');
        $lists = array();
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        foreach($ids as $k=>$v) {
            $factory = new \Product\Factory\product($v['id']);
            $r = $factory->product_info;
            $r['number'] = $r['goods_number'] - buyer_count_by_gid($r['id']);//剩余份数
            //$r['shop_source'] = get_shop_set($r['source'],'name');
            $r['img_source'] = get_shop_set($r['source'], 'small_logo');
            $r['start_time2'] = date("Y-m-d",$r['start_time']);
            $r['get_trial'] = get_trial_by_gid($r['id']); //已申请人数
            $r['title'] = str_cut($r['title'],$title_siez);
            $r['url'] = $r['url'];
            $r['goods_number'] = $r['goods_number'];
            $r['goods_tryproduct'] = $r['goods_tryproduct'];
            $r['goods_bonus'] = $r['goods_bonus'];
            $r['money'] = sprintf('%.2f',$r['goods_bonus']+$r['goods_price']);
            //$r['thumb'] = img2thumb($r['thumb'],'t',1);
            if ($r['start_time']>=$beginToday && $r['start_time'] < $endToday ) {
                $r['time_status'] = 1;
            }
            $lists[$k] = $r;
        }

        $pages = v2_page($count, $num);
        $pages2 = v2_page_2($count, $num);

        $result = array();
        $result['status'] = 1;
        $result['data'] = array(
            'count' => $count,
            'lists' => $lists,
            'pages' => $pages,
            'pages2' => $pages2

        );
        echo json_encode($result);
    }




     /* 用户抢购列表 */
    public function v2_buyer_list() {
        $param = I('param.');
        extract($param);
        $limit = (isset($param['num'])) ? (int) $param['num'] : 10;
        
        $sqlmap = array();
        if(isset($goods_id) && !empty($goods_id)) {
            $sqlmap['goods_id'] = $goods_id;
        }
        if(isset($mod) && !empty($mod)) {
            $sqlmap['act_mod'] = $mod;
        }
        $sqlmap['status'] = array("EGT", 0);
        $count = model('order')->where($sqlmap)->count();
        $uids = model('order')->where($sqlmap)->order('id DESC')->page(PAGE, $limit)->getField('buyer_id', TRUE);
        $lists = array();
        foreach ($uids as $k => $uid) {
           $uinfo =getUserInfo($uid,'',1);
           $uinfo['avatar'] = getavatar($uid);
           $uinfo['nickname'] = nickname($uid);
           $lists[] = $uinfo;
        }
        $pages = v2_page_3($count, $limit);
         $result = array();
        $result['status'] = 1;
        $result['data'] = array(
            'count' => $count,
            'lists' => $lists,
            'pages' => $pages,

        );
        echo json_encode($result);
    }

    /*商品提醒事项 用于后台商品提醒*/
    public function callback(){
       $result =  model('admin_news')->where('state=0')->limit(1)->find(); 
       if(!$result) return false;
       $data['id'] = $result[id];
       model('admin_news')->where($data)->setField('state',1);
       return exit(json_encode($result));
    }

    /*获取所属商家不同活动 各种订单状态下的活动商品*/
    public function status_goods(){

        $userid = (int) cookie('_userid');
        $info = I('get.');
        $status = isset($info['status']) && is_numeric($info['status']) ? (int) $info['status'] : $this->error('不是合法的活动状态');
        $mod = in_array($info['mod'],array('trial','rebate','commission')) ? $info['mod'] : $this->error('该活动类型不存在');

        if($mod == 'trial' ||  $mod == 'rebate' || $mod == 'commission' ){

            if($mod == 'trial' && $status ==2){
                //'order_sn' =>array('GT',0)
               $z_bgs = model('order')->field('goods_id')->where(array('status' =>$status,'seller_id' =>$userid,'act_mod' =>$mod))->distinct(true)->getField('goods_id',true);

            }else{
               $z_bgs = model('order')->field('goods_id')->where(array('status' =>$status,'seller_id' =>$userid,'act_mod' =>$mod))->distinct(true)->getField('goods_id',true);

            }
          

           if(!$z_bgs) $this->error('暂无对应的活动商品数据');
           foreach ($z_bgs as $k => $v) {

             /*统计单个商品的订单数据*/
             $rs[$k]['order_num'] = model('order')->where(array('status' =>$status,'seller_id' =>$userid,'goods_id' =>$v,'act_mod' =>$mod))->count();

              /*获取单个商品的名称和状态*/
             $rs[$k]['goods'] = model('product')->where(array('id' =>$v))->field('title,status,id')->select();

             $rs[$k]['status_name'] = goods_status_name($rs[$k]['goods'][0]['status']);

             if($mod == 'commission'){

                if($status == 3) $rs[$k]['url'] =U('Member/Order/v2_manage',array('goods_id'=>$v,'mod' =>$mod,'state' =>1,'status' =>2));
                if($status == 5) $rs[$k]['url'] =U('Member/Order/v2_manage',array('goods_id'=>$v,'mod' =>$mod,'state' =>1,'status' =>4));


             }else{
               $rs[$k]['url'] =U('Member/Order/v2_manage',array('goods_id'=>$v,'mod' =>$mod,'state' =>1,'status' =>$status));

             }
             
           }
           $this->success($rs);

        }


    }

          
           
        
           
}
