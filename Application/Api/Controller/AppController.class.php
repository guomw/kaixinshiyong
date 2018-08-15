<?php

namespace Api\Controller;

use \Common\Controller\BaseController;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Max-Age:60');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header('Content-Type:application/json;charset=utf-8');

/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
 **/
class AppController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $this->message_db = model('message');
        $this->mgroup_db = model('message_group');
    }

    /**
     * api获取商品列表信息
     * @author   <jason>
     */
    public function goodslists()
    {
        $param = I('param.');
        extract($param);
        $catid = max(0, (int)$catid);
        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        $keyword = remove_xss($keyword);
        $sqlmap = array();
        if ($mod == 'trial') {
            $protype = isset($protype) ? (int)$protype : -1;
            /*protype: 0 :[实物专区] 1 :[拍a发b] 2 :[红包专区]*/
            if ($protype > -1) {
                if ($protype == 0) {
                    //$sqlmap['t.goods_tryproduct'] = array('EQ','0');
                    $sqlmap['t.goods_tryproduct'] = array(array('EQ', '0'), array('EQ', ''), 'or');
                    $sqlmap['t.goods_bonus'] = array('EQ', '0.00');

                } else if ($protype == 1) {
                    $sqlmap['t.goods_tryproduct'] = array(array('NEQ', '0'), array('NEQ', ''), 'and');
                    $sqlmap['t.goods_bonus'] = array('EQ', '0.00');

                } else if ($protype == 2) {
                    $sqlmap['t.goods_bonus'] = array('NEQ', '0.00');
                    $sqlmap['t.goods_tryproduct'] = array(array('EQ', '0'), array('EQ', ''), 'or');


                }
            } else {
                //$sqlmap['t.goods_bonus'] = array('NEQ','0');
            }
        }

        if ($type == 1) {
            $sqlmap['t.goods_price'] = array(array('EGT', 0), array('ELT', 3));

        }


        if ($source) {
            $sqlmap['t.source'] = array('EQ', $source);
        }
        if ($isrecommend) {
            $sqlmap['p.isrecommend'] = array('EQ', $isrecommend);
        }

        if (isset($status)) {
            $sqlmap['p.status'] = $status;
        } else {
            $sqlmap['p.status'] = 1;
            $sqlmap['p.start_time'] = array("LT", NOW_TIME);
            $sqlmap['p.end_time'] = array("GT", NOW_TIME);
        }
        if ($mod && in_array($mod, array('rebate', 'postal', 'trial', 'commission'))) {
            $sqlmap['p.mod'] = $mod;
        }

        if ($keyword) {
            if ($type == 'c') {
                $com_map = array();
                $com_map['store_name'] = array("LIKE", "%" . $keyword . "%");
                $company_ids = model('member_merchant')->where($com_map)->getField('userid', TRUE);
                if (!$company_ids) {
                    $this->json_function(0, '没有任何内容');
                    exit();

                }
                $sqlmap['p.company_id'] = array("IN", $company_ids);
            } else {
                $sqlmap['p.title|p.keyword'] = array("LIKE", "%" . $keyword . "%");
            }
        }

        $company_id = (int)$company_id;
        if ($company_id > 0) {
            $sqlmap['p.company_id'] = $company_id;
        }

        if ($catid > 0) {
            $categorys = getcache('product_category', 'commons');
            $category = $categorys[$catid];
            $catids = $category['arrchildid'];
            if ($catids) {
                $sqlmap['p.catid'] = array("IN", $catids);
            } else {
                $this->json_function(0, '没有内容');
                exit();

            }
        }

        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'desc';
        } else {
            if ($orderby == 'id' || $orderby == 'start_time' || $orderby == 'hits') {
                $orderby = 'p.' . $orderby;
            } else {
                $orderby = 't.' . $orderby;
            }
        }

        if ($mod) {
            $count = model('product')->alias('p')->join(C('DB_PREFIX') . 'product_' . $mod . ' AS t ON p.id = t.id')->where($sqlmap)->count();
            $ids = model('product')->alias('p')->join(C('DB_PREFIX') . 'product_' . $mod . ' AS t ON p.id = t.id')->where($sqlmap)->page($page, $num)->field('p.id')->order($orderby . ' ' . $orderway)->select();

            // echo model('product')->getLastSql();
        } else {
            $sqlmap['p.mod'] = array('NEQ', 'trial');
            $count = model('product')->alias('p')->where($sqlmap)->count();
            $ids = model('product')->alias('p')->where($sqlmap)->field('p.id')->page($page, $num)->order($orderby . ' ' . $orderway)->select();

        }

        if (!$ids) {
            $this->json_function(0, '没有找到商品');
            exit();
        }
        $lists = array();
        foreach ($ids as $k => $v) {
            $factory = new \Product\Factory\product($v['id']);
            $rs = $factory->product_info;
            $r['mod_name'] = model('activity_set')->where(array('key' => $rs['mod'] . '_name'))->getField('value');
            $r['price_name'] = activitiy_price_name($rs['mod']);
            $r['mod_price'] = price($rs['id']);
            $r['number'] = $rs['goods_number'] - buyer_count_by_gid($rs['id']);//剩余份数
            $r['start_time'] = $rs['start_time'];
            $r['title'] = $rs['title'];
            $r['get_trial'] = get_trial_by_gid($rs['id']); //已申请人数
            $r['catid'] = $rs['catid'];
            $r['id'] = $rs['id'];
            $r['goods_number'] = $rs['goods_number'];
            $r['source'] = $rs['source'];
            /*列表页获取250*250的图片*/
            $r['thumb'] = img2thumb($rs['thumb'], 't', 1);
            if ($rs['mod'] == 'rebate') {
                $r['price'] = $rs['goods_price'] * $rs['goods_discount'];
                $r['goods_discount'] = $rs['goods_discount'];

            }
            $r['goods_price'] = $rs['goods_price'];
            $r['goods_bonus'] = $rs['goods_bonus'];

            $r['hits'] = $rs['hits'];
            $r['goods_tryproduct'] = $rs['goods_tryproduct'];
            if ($rs['mod'] == 'trial') {
                $r['goods_vipfree'] = $rs['goods_vipfree'];
                if ($rs['goods_bonus'] > '0' || $rs['goods_bonus'] != '0.00') {
                    /*3:红包类型*/
                    $r['protype'] = 3;
                } elseif ($rs['goods_tryproduct'] > 0 || $rs['goods_tryproduct'] > '0' && $rs['goods_tryproduct'] != '') {
                    /*w:拍a发b*/
                    $r['protype'] = 2;
                } else {
                    /*1:实物专区*/
                    $r['protype'] = 1;

                }


                if ($rs['goods_point'] == 1) {
                    $r['point_num'] = Integral_quantity($rs['goods_price']);

                } else {
                    unset($r['point_num']);
                }

            }

            $r['subsidy_type'] = $rs['subsidy_type'];
            $r['subsidy'] = $rs['subsidy'];
            $r['goods_point'] = $rs['goods_point'];


            if ($rs['mod'] == 'commission') {
                $r['bonus_price'] = $rs['bonus_price'];
                $r['fan_price'] = $rs['bonus_price'] + $rs['goods_price'];
                if ($rs['allow_groupid'] != '') {
                    $r['allow_groupid'] = string2array($rs['allow_groupid']);

                }


            }

            $lists[$k] = $r;
        }

        //  print_r($lists);

        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['count'] = $count;
        $result['lists'] = $lists ? $lists : '';
        $result['pages'] = $pages;
        echo json_encode($result);
    }

    /*产品分类表*/
    public function goods_categorylists()
    {
        $param = I('param.');
        extract($param);
        $sqlmap = array();
        if (!empty($where)) {
            $sqlmap['_string'] = $where;
        } else {
            $catid = (empty($catid)) ? 0 : $catid;
            if (strpos($catid, ',') === FALSE) {
                $catid = (int)$catid;
                $catid = ($catid < 1) ? 0 : $catid;
                $sqlmap['parentid'] = $catid;
            } else {
                $sqlmap['catid'] = array("IN", $catid);
            }
        }
        $listss = model('product_category')->where($sqlmap)->limit($limit)->order("listorder ASC")->select();
        $result = array();
        $rewrite = new \Common\Library\rewrite();
        $lists = array();
        foreach ($listss as $k => $v) {
            $r['url'] = $rewrite->category($v['catid']);
            $r['catname'] = $v['catname'];
            $r['catid'] = $v['catid'];
            $r['parentid'] = $v['parentid'];
            $r['arrparentid'] = $v['arrparentid'];
            $r['arrchildid'] = $v['arrchildid'];
            $r['child'] = $v['child'];/*是否存在子栏目，1存在*/
            $lists[$k] = $r;
        }
        $result = array();
        $result['status'] = 1;
        $result['lists'] = $lists;
        echo json_encode($result);

    }

    /*获取商品详情*/
    public function goods_show()
    {
        $param = I('param.');
        extract($param);
        if (!$goods_id) $this->error('参数错误！');
        $factory = new \Product\Factory\product($goods_id);
        $rs = $factory->product_info;
        $lists = array();
        $lists['mod'] = $rs['mod'];
        $lists['status'] = $rs['status'];
        $lists['catid'] = $rs['catid'];
        $lists['start_time'] = $rs['start_time'];
        $lists['end_time'] = $rs['end_time'];
        $lists['title'] = $rs['title'];
        $lists['id'] = $rs['id'];
        $lists['source'] = $rs['source'];/*店铺来源*/
        $lists['type'] = $rs['type'];/*下单方式*/
        $lists['thumb'] = $rs['thumb'];
        $lists['url'] = $rs['url'];
        $lists['goods_url'] = $rs['goods_url'];
        $lists['goods_rule'] = $rs['goods_rule'];
        $lists['goods_vipfree'] = $rs['goods_vipfree'];
        $lists['goods_point'] = $rs['goods_point'];
        $lists['goods_search_albums'] = $rs['goods_search_albums'];
        $lists['goods_content'] = $rs['goods_content'];
        $lists['goods_tips'] = $rs['goods_tips'];
        $lists['source'] = $rs['source'];
        $lists['type'] = $rs['type'];
        $lists['price_name'] = activitiy_price_name($rs['mod']);
        if ($rs['mod'] == 'rebate') {
            $lists['price'] = $rs['goods_price'] * $rs['goods_discount'];
            $lists['goods_discount'] = $rs['goods_discount'];

        }

        if ($rs['mod'] == 'commission') {
            $lists['bonus_price'] = $rs['bonus_price'];                     //闪电红包
            $lists['fan_price'] = $rs['bonus_price'] + $rs['goods_price'];  //返还金额
            //获取搜索关键词 排序 旺旺
            //$data['id'] = $lists['goods_content']
            $lists['keyword'] = $rs['keyword'];
            $lists['sort'] = $rs['sort'];
            $lists['goods_address'] = $rs['goods_address'];
            $lists['goods_wangwang'] = $rs['goods_wangwang'];
            //获取商家店铺
            if (!empty($lists['goods_wangwang'])) {
                $lists['goods_wangwang'] = get_store_value($lists['company_id'], $lists['goods_wangwang']);
            }
            $lists['goods_search_albums_url'] = $rs['goods_search_albums_url'];

            if ($rs['allow_groupid'] != '') {
                $allow_groupid = string2array($rs['allow_groupid']);
                if (in_array(1, $allow_groupid)) {
                    $lists['allow_groupid'] = 1;
                } elseif (in_array(2, $allow_groupid)) {
                    $lists['allow_groupid'] = 2;
                } else {
                    $lists['allow_groupid'] = 0;
                }


            } else {
                $lists['allow_groupid'] = 0;

            }

            $lists['subsidy_type'] = $rs['subsidy_type'];
            $lists['subsidy'] = $rs['subsidy'];


        }

        $lists['goods_price'] = $rs['goods_price'];
        $lists['goods_tryproduct'] = $rs['goods_tryproduct'];
        $lists['goods_bonus'] = $rs['goods_bonus'];
        $lists['goods_number'] = $rs['goods_number'];
        $lists['number'] = $rs['goods_number'] - buyer_count_by_gid($rs['id']);//剩余份数
        $lists['hits'] = $rs['hits'];
        if ($rs['mod'] == 'trial') {

            if ($rs['goods_bonus'] > '0' || $rs['goods_bonus'] != '0.00') {
                /*3:红包类型*/
                $lists['protype'] = 3;
            } elseif ($rs['goods_tryproduct'] > 0 || $rs['goods_tryproduct'] > '0' && $rs['goods_tryproduct'] != '') {
                /*w:拍a发b*/
                $lists['protype'] = 2;
            } else {
                /*1:实物专区*/
                $lists['protype'] = 1;

            }

            if ($rs['goods_point'] > 0) {
                $lists['point_num'] = Integral_quantity($rs['goods_price']);
                # code...
            } else {
                unset($lists['point_num']);
            }

            $lists['subsidy_type'] = $rs['subsidy_type'];
            $lists['subsidy'] = $rs['subsidy'];
            $lists['goods_ww'] = $rs['goods_ww'];
            if (!empty($lists['goods_ww'])) {
                $lists['goods_ww'] = get_store_value($lists['company_id'], $lists['goods_ww']);
            }
        }
        $lists['apply_people'] = get_trial_by_gid($rs['id']);/*申请人数*/
        $lists['passed_people'] = get_trial_pass_by_gid($rs['id']);/*已通过数量*/
        $lists['finish_people'] = get_over_trial_by_gid($rs['id']);/*已完成人数*/
        $lists['goods_deposit'] = $rs['goods_deposit'];
        $result = array();
        $result['lists'] = $lists;
        echo json_encode($result);
    }

    public function good_user_list()
    {
        $param = I('param.');
        extract($param);
        $state = $state ? $state : 1;
        /*state     （1为已申请 2.为已通过 3.为已完成）*/
        if (!$goods_id || !$state) $this->error('参数错误！');
        $sqlmap = array();
        $sqlmap['goods_id'] = $goods_id;
        if ($state == 2) {
            $sqlmap['status'] = array('EQ', 2);
        } else if ($state == 3) {
            $sqlmap['status'] = array('EQ', 7);

        }
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }
        $factory = new \Product\Factory\product($goods_id);
        $order = model('order')->where($sqlmap)->order($orderby . ' ' . $orderway)->select();
        $lists = array();
        foreach ($order as $k => $v) {
            $r['avatar'] = getavatar($v['buyer_id'], 1);
            $r['nickname'] = nickname($v['buyer_id']);
            $r['apply_time'] = $v['create_time'];
            $lists[$k] = $r;
        }
        $result = array();
        $result['lists'] = $lists;
        //var_dump($lists);
        echo json_encode($result);
    }

    /*获取单个商品的晒单*/
    public function goods_report_lists()
    {
        $param = I('param.');
        extract($param);
        if (!$goods_id) {
            $this->json_function(0, '参数错误！');
            exit();
        }
        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        if (!$goods_id) {
            $this->json_function(0, '参数错误！');
            exit();
        }
        $sqlmap = array();
        $sqlmap['goods_id'] = $goods_id;
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }
        $count = model('report')->where($sqlmap)->order($orderby . ' ' . $orderway)->count();
        $report_lists = model('report')->where($sqlmap)->order($orderby . ' ' . $orderway)->page($page, $num)->select();
        $lists = array();
        foreach ($report_lists as $k => $v) {
            $r['avatar'] = getavatar($v['userid'], 1);
            $r['nickname'] = nickname($v['userid']);
            $r['report_imgs'] = $v['report_imgs'];
            $r['content'] = $v['content'];
            $r['reporttime'] = $v['reporttime'];
            $lists[$k] = $r;

        }

        $pages = page($count, $num);
        $result = array();
        $result['lists'] = $lists;
        $result['pages'] = $pages;

        //var_dump($lists);
        echo json_encode($result);


    }

    /*获取单个商品的试用报告*/
    public function trial_report_lists()
    {
        $param = I('param.');
        extract($param);
        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        if (!$goods_id) {
            $this->json_function(0, '参数错误！');
            exit();
        }
        $sqlmap = array();
        $sqlmap['goods_id'] = (int)$goods_id;
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }
        $count = model('trial_report')->where($sqlmap)->count();
        $trial_lists = model('trial_report')->where($sqlmap)->order($orderby . ' ' . $orderway)->page($page, $num)->select();
        $pages = page($count, $num);
        $lists = array();
        foreach ($trial_lists as $k => $v) {
            $r['avatar'] = getavatar($v['userid'], 1);
            $r['nickname'] = nickname($v['userid']);
            $r['thumb'] = $v['thumb'];
            $r['content'] = $v['content'];
            $r['inputtime'] = $v['inputtime'];
            $lists[$k] = $r;

        }
        $result = array();
        $result['lists'] = $lists;
        $result['pages'] = $pages;
        echo json_encode($result);
    }

    /*日赚任务列表*/
    public function broke_list()
    {
        $param = I('param.');
        extract($param);
        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        $state = $state ? $state : 1;
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }

        $sqlmap['status'] = $state;
        $lists = array();
        $count = model('task_day')->where($sqlmap)->count();
        $task = model('task_day')->where($sqlmap)->page($page, $num)->order($orderby . ' ' . $orderway)->select();
        foreach ($task as $k => $v) {
            $lists[$k]['status'] = $v['status'];
            $lists[$k]['id'] = $v['id'];
            $lists[$k]['goods_number'] = $v['goods_number'];
            $lists[$k]['thumb'] = img2thumb($v['thumb'], 's', 1);
            $lists[$k]['title'] = $v['title'];
            $lists[$k]['goods_price'] = $v['goods_price'];
            $lists[$k]['number'] = $v['goods_number'] - $v['already_num'];
            $lists[$k]['already_num'] = $v['already_num'];
            $lists[$k]['source'] = $v['source'];
        }
        $pages = page($count, $num);
        $result = array();
        $result['lists'] = $lists;
        $result['count'] = $count;
        $result['pages'] = $pages;
        echo json_encode($result);
    }

    /*日赚任务详情*/
    public function broke_show()
    {
        $param = I('param.');
        extract($param);
        if (!$id) {
            $this->json_function(0, '参数错误！');
            exit();
        }
        $lists = model('task_day')->where(array('id' => $id))->find();
        $lists['thumb'] = img2thumb($lists['thumb'], 't', 1);
        $lists['goods_albums'] = string2array($lists['goods_albums']);
        //获取商家店铺
        if (!empty($lists['goods_wangwang'])) {
            $lists['goods_wangwang'] = get_store_value($lists['company_id'], $lists['goods_wangwang']);
        }
        $result = array();
        $result['lists'] = $lists;
        echo json_encode($result);

    }

    /*购物返利参与条件(试客)（1为需要 0为不需要）*/
    public function check_authority()
    {
        $param = I('param.');
        extract($param);
        $lists = array();
        //$conditions = C_READ('buyer_good_buy_times','trial');
        $conditions = string2array(model('activity_set')->where(array('key' => 'buyer_join_condition', 'activity_type' => $mod))->getField('value'));
        if ((int)$conditions['information'] == 6) {
            $lists['information'] = 1;
        } else {
            $lists['information'] = 0;
        }

        if ((int)$conditions['phone'] == 1) {
            $lists['phone'] = 1;
        } else {
            $lists['phone'] = 0;

        }

        if ((int)$conditions['email'] == 2) {
            $lists['email'] = 1;
        } else {
            $lists['email'] = 0;

        }

        if ((int)$conditions['realname'] == 3) {
            $lists['realname'] = 1;
        } else {
            $lists['realname'] = 0;

        }

        if ((int)$conditions['bind_taobao'] == 4) {
            $lists['bind_taobao'] = 1;
        } else {
            $lists['bind_taobao'] = 0;

        }

        if ((int)$conditions['bind_alipay'] == 5) {
            $lists['bind_alipay'] = 1;
        } else {
            $lists['bind_alipay'] = 0;

        }

        $lists['goods_count'] = C_READ('buyer_good_buy_times', $mod);

        if (C_READ('buyer_day_buy_times', $mod) == 0) {
            $lists['buy_count'] = '不限';
        } else {
            $lists['buy_count'] = C_READ('buyer_day_buy_times', $mod);

        }
        if (C_READ('buyer_buy_time_limit', $mod) == 0) {
            $lists['time_limit'] = '不限';
        } else {
            $lists['time_limit'] = C_READ('buyer_buy_time_limit', $mod);

        }

        $result = array();
        $result['lists'] = $lists;
        echo json_decode($result);

    }

    /*10.获取网站幻灯片*/
    public function focus()
    {
        $param = I('param.');
        extract($param);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 3;
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }
        $sqlmap = array();
        $sqlmap['type'] = $type ? $type : 1;
        $focus = model('focus')->where($sqlmap)->order($orderby . ' ' . $orderway)->limit($num)->select();
        $lists = array();
        foreach ($focus as $k => $v) {
            if ($v['endtime'] == 0 || NOW_TIME < $v['endtime']) {
                $r['title'] = $v['title'];
                $r['image'] = img2thumb($v['image'], 'b');
                $r['url'] = $v['url'];
                $r['endtime'] = date('Y-m-d', $v['endtime']);
                $lists[$k] = $r;

            }
        }
        echo json_encode($lists);

    }


    /*11.获取用户信息*/
    public function get_userinfo()
    {
        $param = I('param.');
        extract($param);
        if (!$userid || !$random) {
            $this->json_function(0, '参数错误');
            exit();
        }

        $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $random))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }

        $userinfos = member_info($userid);
        $lists = array();
        $lists['userid'] = $userinfos['userid'];
        $lists['status'] = $userinfos['status'];
        $lists['nickname'] = $userinfos['nickname'];
        $lists['groupid'] = $userinfos['groupid'];
        $lists['avatar'] = getavatar($userinfos['userid'], 1);
        $lists['phone'] = $userinfos['phone'];
        $lists['email'] = $userinfos['email'];
        $lists['money'] = $userinfos['money'];
        $lists['point'] = $userinfos['point'];
        $lists['group_name'] = member_group_name($userinfos['userid']);
        $lists['phone_status'] = $userinfos['phone_status'];
        $lists['email_status'] = $userinfos['email_status'];
        $lists['alipay_status'] = $userinfos['alipay_status'];
        $lists['name'] = $userinfos['name'];
        $lists['bank_status'] = $userinfos['bank_status'];
        $lists['qq'] = $userinfos['qq'];
        $lists['name_status'] = $userinfos['id_number_status'];
        echo json_encode($lists);
    }

    /*获取用户绑定淘宝帐号*/
    public function get_tbaccount()
    {
        $param = I('param.');
        $sqlmap = array();
        extract($param);
        if (!$userid || !$random) {
            $this->json_function(0, '参数错误');
            exit();
        }

        $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $random))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }

        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;

        if ($userid) {
            $sqlmap['userid'] = $userid;
        }
        if ($id) {
            $sqlmap['id'] = $id;
        }
        $count = model('member_bind')->where($sqlmap)->count();
        $account = model('member_bind')->where($sqlmap)->page($page, $num)->order($orderby . ' ' . $orderway)->select();
        $lists = array();
        foreach ($account as $k => $v) {
            $r['id'] = $v['id'];
            $r['account'] = $v['account'];
            $r['inputtime'] = $v['inputtime'];
            $r['updatetime'] = $v['updatetime'];
            $r['safe_grade'] = $v['safe_grade'];
            $r['account_level'] = $v['account_level'];
            $r['bscore'] = $v['bscore'];
            $r['is_real_name'] = $v['is_real_name'];
            $r['is_default'] = $v['is_default'];
            $r['status'] = $v['status'];
            $lists[$k] = $r;

        }
        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['count'] = $count;
        $result['lists'] = $lists;
        $result['pages'] = $pages;
        echo json_encode($result);

    }

    public function get_useraccount()
    {
        $param = I('param.');
        $sqlmap = array();
        extract($param);
        if (!$userid || !$random) {
            $this->json_function(0, '参数错误');
            exit();
        }

        $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $random))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }

        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        if (!$userid) {
            $this->json_function(0, '请勿非法访问');
            exit();
        }

        if ($userid) {
            $sqlmap['userid'] = $userid;
        }
        if ($type) {
            $sqlmap['type'] = $type ? $type : 'alipay';
        }
        $count = model('member_attesta')->where($sqlmap)->count();
        $account = model('member_attesta')->where($sqlmap)->page($page, $num)->order($orderby . ' ' . $orderway)->select();
        $lists = array();
        foreach ($account as $k => $v) {
            $infos = string2array($v['infos']);
            $r['userid'] = $v['userid'];
            $r['id'] = $v['id'];
            $r['type'] = $v['type'];
            if ($v['type'] == 'alipay') {
                $r['account'] = $infos['alipay_account'];
                $r['username'] = $infos['username'];

            } elseif ($v['type'] == 'bank') {
                $r['account'] = $infos['account'];
                $r['province'] = $infos['province'];
                $r['city'] = $infos['city'];
                $r['bank_name'] = $infos['bank_name'];
                $r['area'] = $infos['area'];
                $r['sub_branch'] = $infos['sub_branch'];
                //根据linkid查地址
                $province = model('linkage')->getFieldByLinkageid($infos['province'], 'name');
                $city = model('linkage')->getFieldByLinkageid($infos['city'], 'name');
                $r['band_address'] = $province . $city . $infos['sub_branch'];

            }

            $lists[$k] = $r;

        }
        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['count'] = $count;
        $result['lists'] = $lists;
        $result['pages'] = $pages;
        echo json_encode($result);


    }

    /*获取用户订单信息*/
    public function getorderlists()
    {
        $param = I('param.');
        $sqlmap = array();
        extract($param);
        if (!$userid || !$random) {
            $this->json_function(0, '参数错误');
            exit();
        }

        $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $random))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }

        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        if (!$userid) {
            $this->json_function(0, '请问非法访问');
            exit();
        }

        if ($userid) {
            $sqlmap['buyer_id'] = $userid;
        }
        if ($mod) {
            $sqlmap['act_mod'] = $mod ? $mod : 'trial';
        }
        if ($status) {
            /*待审核*/
            if ((int)$status == -1) {
                $sqlmap['status'] = array('EQ', 1);
            } elseif ($status == 1) {
                $sqlmap['status'] = array('IN', '2,3,4,5,6,8');
            } elseif ($status == 2) {
                $sqlmap['status'] = array('EQ', 0);

            } elseif ($status == 3) {
                $sqlmap['status'] = array('EQ', 7);
            }
        }
        $count = model('order')->where($sqlmap)->count();
        $orders = model('order')->where($sqlmap)->page($page, $num)->order($orderby . ' ' . $orderway)->select();
        $lists = array();
        foreach ($orders as $k => $v) {
            $r['id'] = $v['id'];
            $r['buyer_id'] = $v['buyer_id'];
            $r['goods_id'] = $v['goods_id'];
            $r['seller_id'] = $v['seller_id'];
            $r['inputtime'] = $v['inputtime'];
            $r['status'] = $v['status'];
            $r['check_time'] = $v['check_time'];
            if ($r['status'] == 0) {
                $data1['order_id'] = $v['id'];
                $order_list = model('order_log')->where($data1)->order('id DESC')->limit(1)->find();
                $r['cause'] = $order_list['cause'];
            }


            if ($v['act_mod'] == 'trial') {
                $r['trial_report'] = model('trial_report')->where(array('order_id' => $v['id']))->find();
            }

            if ($v['act_mod'] == 'rebate') {
                $r['rebate_report'] = model('report')->where(array('order_id' => $v['id']))->find();
            }

            $r['act_mod'] = $v['act_mod'];
            $r['order_sn'] = $v['order_sn'];
            $r['create_time'] = $v['create_time'];
            $r['complete_time'] = $v['complete_time'];
            $r['bind_id'] = !$v['bind_id'] ? '' : $v['bind_id'];
            if ($r['bind_id']) {
                $taobao_account = model('member_bind')->where('id=' . $r['bind_id'] . '')->getField('account');
                $r['taobao_account'] = !$taobao_account ? "" : $taobao_account;
            }

            $r['is_vip_shi'] = $v['is_vip_shi'];
            $factory = new \Product\Factory\product($v['goods_id']);
            $r['title'] = $factory->product_info['title'];
            // $r['thumb'] = $factory->product_info['thumb'];
            $r['thumb'] = img2thumb($factory->product_info['thumb'], 's', 1);
            $r['goods_price'] = $factory->product_info['goods_price'];
            $r['goods_bonus'] = $factory->product_info['goods_bonus'];
            $r['goods_discount'] = $factory->product_info['goods_discount'];

            if ($v['act_mod'] == 'commission') {
                $data2['id'] = $v['goods_id'];
                $r['bonus_price'] = model('product_commission')->where($data2)->getField('bonus_price');
                $r['fan_price'] = $r['bonus_price'] + $r['goods_price'];
            }

            $lists[$k] = $r;
        }

        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['count'] = $count;
        $result['lists'] = $lists;
        $result['pages'] = $pages;
        echo json_encode($result);

    }

    /*15.获取用户提现记录*/
    public function getusercashlog()
    {
        $param = I('param.');
        $sqlmap = array();
        extract($param);
        if (!$userid || !$random) {
            $this->json_function(0, '参数错误');
            exit();
        }

        $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $random))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }
        if ($orderby == '' || $orderway == '') {
            $orderby = 'cashid';
            $orderway = 'DESC';
        }

        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        if (!$userid) {
            $this->json_function(0, '请问非法访问');
            exit();
        }

        if ($userid) {
            $sqlmap['userid'] = $userid;
        }
        if ($paypal) {
            $sqlmap['paypal'] = $paypal;
        }

        $count = model('cash_records')->where($sqlmap)->count();
        $cashs = model('cash_records')->where($sqlmap)->page($page, $num)->order($orderby . ' ' . $orderway)->select();
        $lists = array();
        foreach ($cashs as $k => $v) {
            $r['userid'] = $v['userid'];
            $r['id'] = $v['cashid'];
            $r['inputtime'] = $v['inputtime'];
            if ($v['paypal'] == 1) {
                $r['paypal'] = '普通';
            } else {
                $r['paypal'] = '快速';
            }
            $r['status'] = $v['status'];
            $r['money'] = $v['money'];
            $r['cause'] = $v['cause'];
            $r['fee'] = $v['fee'];
            $r['totalmoney'] = $v['totalmoney'];
            $r['account'] = $v['cash_alipay_username'];
            if ($v['type'] == 1) {
                $r['type'] = '银行卡';
            } elseif ($v['type'] == 2) {
                $r['type'] = '支付宝';
            }
            $r['check_time'] = $v['check_time'];
            $lists[$k] = $r;

        }

        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['count'] = $count;
        $result['lists'] = $lists;
        $result['pages'] = $pages;
        echo json_encode($result);
    }

    /*获取用户账户明细*/
    public function getfinancelog()
    {
        $param = I('param.');
        $sqlmap = array();
        extract($param);
        if (!$userid || !$random) {
            $this->json_function(0, '参数错误');
            exit();
        }

        $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $random))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }

        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        if (!$userid) {
            $this->json_function(0, '请问非法访问');
            exit();
        }

        if ($userid) {
            $sqlmap['userid'] = $userid;
        }
        if ($type) {
            if ($type == 1) {
                $sqlmap['type'] = 'money';
            }

            if ($type == 2) {
                $sqlmap['type'] = 'point';
            }

        }

        $count = model('member_finance_log')->where($sqlmap)->count();
        $finances = model('member_finance_log')->where($sqlmap)->page($page, $num)->order($orderby . ' ' . $orderway)->select();
        $lists = array();
        foreach ($finances as $k => $v) {
            $r['userid'] = $v['userid'];
            $r['id'] = $v['id'];
            $r['type'] = $v['type'];
            $r['num'] = $v['num'];
            //  $r['total_num'] = model('member_finance_log')->where($info)->sum('num');

            //   $r['total_desc'] = model('member_finance_log')->where($infos)->sum('num');
            $r['dateline'] = $v['dateline'];
            $r['cause'] = $v['cause'];
            $lists[$k] = $r;
        }

        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['count'] = $count;
        $result['lists'] = $lists;
        $result['pages'] = $pages;
        echo json_encode($result);


    }

    /*获取站内信*/
    public function getmessage()
    {
        $param = I('param.');
        $sqlmap = array();
        extract($param);
        $type = (isset($type) && is_numeric($type)) ? $type : 1;
        if ($type == 1) {
            if ($orderby == '' || $orderway == '') {
                $orderby = 'messageid';
                $orderway = 'DESC';
            }
        } else {
            if ($orderby == '' || $orderway == '') {
                $orderby = 'id';
                $orderway = 'DESC';
            }
        }


        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;

        if (!$userid) {
            $this->json_function(0, '请问非法访问');
            exit();
        }

        /*  if ($userid) {
            $sqlmap['send_to_id'] = $userid;
        }*/

        if ($type == 1) {//私信
            $sqlMap = array();
            $sqlMap['send_to_id'] = $userid;
            $count = $this->message_db->where(array('send_to_id' => $userid, 'status' => 0))->count();
            $announce_count = $this->message_db->where($sqlMap)->count();
            $announce_lists = $this->message_db->where($sqlMap)->page(PAGE, 10)->order($orderby . ' ' . $orderway)->select();
            // echo $this->message_db->getLastSql();
            $lists = array();
            foreach ($announce_lists as $k => $v) {
                $rs = model('message_data')->where(array('message_id' => $v['messageid'], 'userid' => $userid))->find();

                $r['message_id'] = $v['messageid'];
                $r['isdelete'] = $rs['isdelete'];
                $r['send_to_id'] = $v['send_to_id'];
                $r['subject'] = $v['subject'];
                $r['content'] = $v['content'];
                $r['inputtime'] = $v['message_time'];
                $r['new_type'] = 1;
                $r['status'] = $v['status'];


                $lists[$k] = $r;
            }

            //  var_dump($announce_lists);
            $pages = showPage($announce_count, PAGE, 10);
        } elseif ($type == 2) {//系统消息
            //查出当前会员的会员组
            $sqlMap = array();
            $count = $this->mgroup_db->where(array('status' => 0))->count();
            $r = model('member')->where(array('userid' => $userid, 'modelid' => 1))->field('groupid')->find();
            $sqlMap['groupid'] = $r['groupid'];
            $announce_count = $this->mgroup_db->where($sqlMap)->count();
            $announce_lists = $this->mgroup_db->where($sqlMap)->page(PAGE, 10)->order($orderby . ' ' . $orderway)->select();
            $lists = array();
            //  echo $this->mgroup_db->getLastSql();

            foreach ($announce_lists as $k => $v) {
                $rs = model('message_data')->where(array('group_message_id' => $v['id'], 'userid' => $userid))->find();
                // var_dump($rs['userid']);
                $announce_lists[$k]['group_id'] = $rs['group_message_id'];
                $announce_lists[$k]['isdelete'] = $rs['isdelete'];

                $r['message_id'] = $v['id'];
                $r['isdelete'] = $rs['isdelete'];
                $r['send_to_id'] = $rs['userid'];
                $r['subject'] = $v['subject'];
                $r['content'] = $v['content'];
                $r['inputtime'] = $v['inputtime'];
                $r['status'] = $v['status'];


                $lists[$k] = $r;
            }
            $pages = showPage($announce_count, PAGE, 10);
        } elseif ($type == 3) {
            $lists = array();
            $announce_count = model('push_log')->where(array('type' => 'all'))->count();
            $announce_lists = model('push_log')->where(array('type' => 'all'))->page(PAGE, 10)->order($orderby . ' ' . $orderway)->select();
            foreach ($announce_lists as $k => $v) {
                $lists[$k]['subject'] = $v['title'];
                $lists[$k]['content'] = $v['content'];
                $lists[$k]['message_id'] = $v['id'];
                $lists[$k]['inputtime'] = $v['send_time'];
                $lists[$k]['new_type'] = 3;

            }
        }

        $pages = showPage($announce_count, PAGE, 10);
        $result = array();
        $result['status'] = 1;
        $result['count'] = $announce_count;
        $result['count_status'] = $count;
        $result['lists'] = $lists;
        $result['pages'] = $pages;
        echo json_encode($result);


    }


    //获取单个站内信详情
    public function getmessage_show()
    {

        extract($param);

        //    var_dump(I('param.'));

        if (I('param.type') == 1) {

            $param = I('param.');
            $data = array();
            $data['messageid'] = $param['id'];
            $rs = model('message')->where($data)->find();
            if ($rs) {
                model('message')->where($data)->setField('status', 1);
            }


            $r['message_id'] = $rs['id'];
            $r['isdelete'] = $rs['isdelete'];
            $r['send_to_id'] = $rs['userid'];
            $r['subject'] = $rs['subject'];
            $r['content'] = $rs['content'];
            $r['inputtime'] = $rs['message_time'];
            $r['status'] = $rs['status'];

            echo json_encode($r);
        }


        if (I('param.type') == 3) {

            $param = I('param.');
            $data = array();
            $data['id'] = $param['id'];
            $rs = model('push_log')->where($data)->find();
            if ($rs) {
                model('push_log')->where($data)->setField('status', 1);
            }

            $r['subject'] = $rs['title'];
            $r['content'] = $rs['content'];
            $r['message_id'] = $rs['id'];
            $r['inputtime'] = $rs['send_time'];
            $r['new_type'] = 3;
            echo json_encode($r);
        }
    }


    /**
     * 标注已读
     */
    public function read()
    {
        if (IS_POST) {
            $ids = (array)$_POST['id'];
            $userid = I('userid');
            if (!$ids) exit();
            $sqlMap = array();
            $type = I('type', 1);
            if ($type == 1) {   //站内信
                $sqlMap['messageid'] = array('EQ', $ids);
                $sqlMap['send_to_id'] = $userid;
                $result = $this->message_db->where($sqlMap)->save(array('status' => 1));
                if (!$result) {
                    $this->json_function(0, '标记失败');
                    exit();
                }
                $this->json_function(1, '标记成功');
            } else {  //  群发短消息

                $sqlMap['userid'] = $userid;
                $sqlMap['group_message_id'] = $ids;
                $count = model('message_data')->where($sqlMap)->count();
                if (!$count) {
                    $result = model('message_data')->add($sqlMap);
                }

                if (!$result) {
                    $this->json_function(0, '标记失败');
                    exit();
                }
                $this->json_function(1, '标记成功');
            }
        }

    }


    /*获取帮助中心*/
    public function gethelplist()
    {
        $param = I('param.');
        extract($param);
        if ($orderby == '' || $orderway == '') {
            $orderby = 'catid';
            $orderway = 'DESC';
        }

        if ($catid) {
            $sqlmap['parentid'] = array('EQ', $catid);

        } else {
            $sqlmap['parentid'] = array('EQ', 1);
        }

        $category = model('category')->where($sqlmap)->order($orderby . ' ' . $orderway)->select();
        $lists = array();
        foreach ($category as $k => $v) {
            $r['catname'] = $v['catname'];
            $r['parentid'] = $v['parentid'];
            $r['catid'] = $v['catid'];
            $r['parentid'] = $v['parentid'];
            $r['arrchildid'] = $v['arrchildid'];
            $lists[$k] = $r;
        }
        //var_dump($lists);
        echo json_encode($lists);


    }

    /*获取文章列表*/
    public function getcatidlist()
    {
        $param = I('param.');
        extract($param);
        if ($orderby == '' || $orderway == '') {
            $orderby = 'catid';
            $orderway = 'DESC';
        }
        if (!$catid) {
            $this->json_function(0, '参数错误');
            exit();
        }
        $sqlmap = array();
        $sqlmap['catid'] = $catid;
        $lists = model('article')->where($sqlmap)->order($orderby . ' ' . $orderway)->select();
        foreach ($lists as $k => $v) {
            $lists[$k]['title'] = $v['title'];
            $lists[$k]['id'] = $v['id'];
            $lists[$k]['description'] = $v['description'];
            $hit = model('category')->find($v['catid']);
            $lists[$k]['hits'] = $hit['hits'];

        }

        //  var_dump($lists);

        echo json_encode($lists);

    }

    public function helpshow()
    {
        $param = I('param.');
        extract($param);
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'DESC';
        }
        if (!$id) {
            $this->json_function(0, '参数错误！');
            exit();
        }
        $sqlmap = array();
        $sqlmap['id'] = $id;
        $lists = model('article_data')->where($sqlmap)->order($orderby . ' ' . $orderway)->field('id,content')->find();

        $article = model('article')->find($lists['id']);
        $lists['description'] = $article['description'];
        $lists['title'] = $article['title'];
        $hit = model('category')->find($article['catid']);
        $lists['hits'] = $hit['hits'];
        echo json_encode($lists);

    }

    public function check_phone_exsit()
    {
        $phone = I('phone');
        $count = model('member')->where(array('phone' => $phone))->count();
        if ($count > 0) {
            $this->json_function(0, '手机号已被占用');
            return FALSE;

        } else {
            $this->json_function(1, '手机号可用');
        }

    }

    public function send_phone_code()
    {
        $result = array();
        $enum = I('enum');
        $mobile = I('phone');
        if (is_mobile(trim($mobile)) != TRUE) {
            $this->json_function(0, '手机号格式错误！');
            return FALSE;

        }

        /* 手机号码已被注册的不能发送短信 */
        if (model('member')->where(array('phone' => $mobile))->count() > 0) {
            $this->json_function(0, '该手机号已被占用');
            return FALSE;

        }

        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $sqlmap = array();
        $sqlmap['posttime'] = array('between', array($beginToday, $endToday));
        $sqlmap['mobile'] = $mobile;
        $sqlmap['enum'] = $enum ? $enum : 'register';
        $count = model('sms_report')->where($sqlmap)->count();

        $conditions = array();
        $conditions['posttime'] = array('between', array($beginToday, $endToday));
        $conditions['ip'] = get_client_ip();
        $ip_count = model('sms_report')->where($conditions)->count();

        if (intval($count) > 3) {
            $this->json_function(0, '同一号码，每天只能发送3次，请明日再尝试');
            return FALSE;

        }

        /* 检测当前手机的发送日期 */
        $_vcode = random(6, 1);
        $msg = '您的验证码为' . $_vcode . ',请勿向任何人提供那您接收到的验证码信息';
        $SmsApi = new \Sms\Api\SmsApi();
        $arr = array();
        $arr['param'] = "{'code':'$_vcode'}";
        $arr['template_id'] = C('template_id_2');
        $result = $SmsApi->send($mobile, $msg, $arr);
        if (!$result) {
            $this->json_function(0, '手机短信发送失败，请重试。');
            return FALSE;
        } else {
            $info = array();
            $info['mobile'] = $mobile;
            $info['posttime'] = NOW_TIME;
            $info['id_code'] = $_vcode;
            $info['msg'] = $msg;
            $info['ip'] = get_client_ip();
            $info['enum'] = $enum ? $enum : 'register';
            model('sms_report')->update($info);
            $this->json_function(1, '发送成功');
        }

    }


    public function register_check_sms($isyes = '')
    {
        /* $mobile = I('phone');
        $sms = I('sms');*/

        if (empty($mobile) || !is_mobile($mobile)) {
            $this->json_function(0, '手机号码为空或格式错误');
            return FALSE;
        }
        if (empty($sms)) {
            $this->json_function(0, '手机短信验证码不能为空');
            return FALSE;
        }
        $sqlmap = array();
        $sqlmap['mobile'] = $mobile;
        $sqlmap['id_code'] = $sms;
        $sqlmap['status'] = 0;
        if (model('sms_report')->where($sqlmap)->count() < 1) {
            $this->json_function(0, '验证码输入错误');
            return FALSE;
        }
        if ($isyes === TRUE) {
            model('sms_report')->where($sqlmap)->setField('status', 1);
        }

        $this->json_function(1, '验证码输入正确');


    }


    /* 检测验证码是否正确 */
    public function public_checkverify_ajax()
    {
        $verify = I('verify');
        $verify = strtolower($verify);
        if (checkVerify($verify, FALSE) == TRUE) {
            $this->json_function(1, '验证码输入正确');
            return true;
        } else {
            $this->json_function(0, '验证码输入错误');
            return FALSE;

        }
    }


    /*获取验证码*/
    public function get_verify()
    {
        $verify = A('Api/Verify');
        $results = $verify->create();
        $this->json_function(1, '获取成功', $results);
    }


    public function register()
    {

        if (IS_POST) {
            $models = getcache('model', 'commons');
            $settings = getcache('setting', 'member');
            $param = I('param.');
            extract($param);
            $info = array();
            if (is_mobile($user_phone)) {
                $info['phone'] = $user_phone;
            } else {
                $this->json_function(0, '格式错误');
                return FALSE;
            }
            $phone_count = model('member')->where(array('phone' => $user_phone))->count();
            if ($phone_count > 0) {
                $this->json_function(0, '该用户已存在');
                return FALSE;
            }
            $info['password'] = $user_password;
            // $info['sms'] = '709390';
            $info['sms'] = $sms;
            $info['modelid'] = I('modelid', '1', 'intval');
            // $MemberLogic = D('Member', 'Logic');
            $MemberLogic = D('Member/Member', 'Logic');
            if (!$MemberLogic->register_check_sms($info['phone'], $sms, TRUE)) {
                $this->json_function(0, '验证码输入错误！');
                return FALSE;
            } else {
                /* 定义手机为已验证 */
                $info['phone_status'] = 1;

            }

            /* 注册默认值 */
            $info['encrypt'] = random(6);
            $info['point'] = (int)0;
            $info['groupid'] = (isset($info['groupid']) && is_numeric($info['groupid']) && $info['groupid'] > 1) ? $info['groupid'] : 1;
            $User = D('Member/Member', 'Model');
            $userids = $User->update($info);
            $result = $MemberLogic->publogin($userids);
            if (!$result) {
                $this->json_function(0, '注册失败，请稍后重试');
                return FALSE;
            } else {
                $userid = $userids;
                $data = array();
                $data['alias'] = $userid;
                $data['userid'] = $userid;
                $data['platform'] = $platform;
                $data['version'] = $version;
                $data['reg_time'] = NOW_TIME;
                $data['name'] = $platform_name;
                $count = model('mebmer_app')->where(array('userid' => $userid))->count();
                if ((int)$count > 0) {
                    model('member_app')->where(array('userid' => $userid))->save($data);
                } else {
                    model('member_app')->add($data);
                }
                $userinfo = getUserInfo($userid);
                if ($info['agent_id'] > 0) {
                    runhook('member_attesta_phone', array('userid' => $userid));
                }
                $this->json_function(1, '注册成功', $userinfo);
            }

        } else {
            $this->json_function(0, '请勿非法访问！');
            return FALSE;
        }

    }

    /** 邮件注册
     * @return bool
     */
    public function registerToEmail()
    {
        if (IS_POST) {
            $param = I('param.');
            extract($param);
            $info = array();
            if (isemail($user_email)) {
                $info['email'] = $user_email;
            } else {
                $this->json_function(0, '格式错误');
                return FALSE;
            }
            $email_count = model('member')->where(array('email' => $user_email))->count();
            if ($email_count > 0) {
                $this->json_function(0, '该用户已存在');
                return FALSE;
            }
            if(!checkAppVerify(strtolower($verifyCode)) == TRUE){
                $this->json_function(0, '验证码输入错误'.$verifyCode);
                return false;
            }
            $info['password'] = $user_password;
            $info['modelid'] = I('modelid', '1', 'intval');
            /* 注册默认值 */
            $info['encrypt'] = random(6);
            $info['point'] = (int)0;
            $info['groupid'] = (isset($info['groupid']) && is_numeric($info['groupid']) && $info['groupid'] > 1) ? $info['groupid'] : 1;
            $MemberLogic = D('Member/Member', 'Logic');
            $User = D('Member/Member', 'Model');
            $userids = $User->update($info);
            $result = $MemberLogic->publogin($userids);
            if (!$result) {
                $this->json_function(0, '注册失败，请稍后重试');
                return FALSE;
            } else {
                $userid = $userids;
                $data = array();
                $data['alias'] = $userid;
                $data['userid'] = $userid;
                $data['platform'] = $platform;
                $data['version'] = $version;
                $data['reg_time'] = NOW_TIME;
                $data['name'] = $platform_name;
                $count = model('mebmer_app')->where(array('userid' => $userid))->count();
                if ((int)$count > 0) {
                    model('member_app')->where(array('userid' => $userid))->save($data);
                } else {
                    model('member_app')->add($data);
                }
                $userinfo = getUserInfo($userid);
                if ($info['agent_id'] > 0) {
                    runhook('member_attesta_email', array('userid' => $userid));
                }
                $this->json_function(1, '注册成功', $userinfo);
            }
        } else {
            $this->json_function(0, '请勿非法访问！');
            return FALSE;
        }
    }


    public function login()
    {
        if (IS_POST) {
            /*  $postdata = file_get_contents('php://input',true);
            $data = json_decode($postdata);*/
            $info = I('post.');
            $username = htmlspecialchars(trim($info['username']));
            $password = htmlspecialchars(trim($info['password']));
            $sqlMap = array();
            $sqlMap['email|phone'] = $username;
            $userinfo = model('member')->where($sqlMap)->find();
            if (!$userinfo) {
                $this->json_function(0, '用户名或密码错误，请检查');
                exit();
            }

            if ($userinfo) {
                if ($userinfo['status'] != 1) {
                    $this->json_function(0, '您的账户尚未通过审核');
                    exit();
                } elseif ($userinfo['islock'] == 1) {
                    $this->json_function(0, '您的账户被系统锁定，禁止登录');
                    exit();

                } elseif ($userinfo['password'] != md5(md5($password . $userinfo['encrypt']))) {
                    $this->json_function(0, '用户名或密码错误！');
                    exit();
                } elseif ($userinfo['modelid'] != 1) {
                    $this->json_function(0, '商家会员不能在手机端登录，请在电脑上操作');
                    exit();

                } else {

                    cookie('_userid', $userinfo['userid'], 86400);
                    cookie('_groupid', $userinfo['groupid'], 86400);
                    cookie('_modelid', $userinfo['modelid'], 86400);
                    model('member')->update(array('userid' => $userinfo['userid'], 'lastdate' => NOW_TIME, 'lastip' => get_client_ip(), 'loginnum' => $userinfo['loginnum'] + 1), false);
                    $app = model('member_app')->where(array('userid' => $userinfo['userid']))->find();
                    $random = random(15);
                    if ($app) {
                        $result = model('member_app')->where(array('userid' => $userinfo['userid']))->save(array('target' => $random));
                    } else {

                        $data = array();
                        $data['alias'] = $userinfo['userid'];
                        $data['userid'] = $userinfo['userid'];
                        $data['platform'] = $info['platform'];
                        $data['reg_time'] = NOW_TIME;
                        $data['verison'] = $info['version'];
                        $data['name'] = $info['platform_name'];
                        $data['target'] = $random;
                        $result = model('member_app')->add($data);
                        // echo model('member_app')->getLastSql();
                    }

                    $return = array();
                    $return['userid'] = $userinfo['userid'];
                    $return['nickname'] = nickname($userinfo['userid']);
                    $return['random'] = $random;
                    if ($result) {
                        $this->json_function(1, '登录成功！', $return);
                        // push($userinfo['userid'],'恭喜你登录成功！','','',$m_time='0');
                    } else {
                        $this->json_function(0, '登录失败');
                        exit();

                    }

                }
            } else {
                $this->json_function(0, '该用户不存在！');
                exit();
            }
        }
    }


    /*发送验证码*/
    public function send_code()
    {
        if (IS_POST) {
            $info = I('post.');
            $_account = htmlspecialchars(trim($info['username']));
            $sqlmap = array();
            $sqlmap['phone|email'] = array('EQ', $_account);
            $userinfo = model('member')->where($sqlmap)->find();
            if (!$userinfo) {
                $this->json_function(0, '该账号不存在！');
                exit();
            }
            extract($userinfo);
            $code = random(6, 1);
            $message = '您的验证码为' . $code . "。该验证码有效期为5分钟，请勿向任何人提供那您接收到的验证码信息";
            if (is_mobile($_account) != FALSE) {
                $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
                $sqlmap = array();
                $sqlmap['posttime'] = array('between', array($beginToday, $endToday));
                $sqlmap['ip'] = get_client_ip();
                $sqlmap['userid'] = $userid;
                $sqlmap['enum'] = 'password';
                $lastSms = model('sms_report')->where($sqlmap)->order('id DESC')->find();
                if (($lastSms['posttime'] + 60) > NOW_TIME) {
                    $this->json_function(0, '请等待60秒后再获取');
                    exit();
                }
                $count = model('sms_report')->where($sqlmap)->count();
                if ($count > 3) {
                    $this->json_function(0, '今日发送短信条数已用完');
                    exit();
                }

                $SmsApi = new \Sms\Api\SmsApi();
                $result = $SmsApi->send($_account, $message);
                if (!$result) {

                    $this->json_function(0, '手机短信发送失败，请重试。');
                    exit();
                } else {
                    $info = array();
                    $info['mobile'] = $_account;
                    $info['posttime'] = NOW_TIME;
                    $info['id_code'] = $code;
                    $info['msg'] = $message;
                    $info['status'] = 1;
                    $info['userid'] = $userid;
                    $info['ip'] = get_client_ip();
                    $info['enum'] = 'password';
                    model('sms_report')->update($info);
                    $this->json_function(1, '发送成功');
                }

            } elseif (isemail($_account) != FALSE) {
                $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
                $sqlmap = array();
                $sqlmap['posttime'] = array('between', array($beginToday, $endToday));
                $sqlmap['userid'] = $userid;
                $lastSms = model('email_log')->where($sqlmap)->order('id DESC')->find();
                if (($lastSms['posttime'] + 60) > NOW_TIME) {
                    $this->json_function(0, '请等待60秒后再获取');
                    exit();
                }

                $arr = array();
                $arr['param'] = "{'code':'$code'}";
                $arr['template_id'] = C('template_id_2');
                $result = sendmail($_account, "找回密码", $message, $arr);

                if ($result) {
                    $info = array();
                    $info['email'] = $_account;
                    $info['posttime'] = NOW_TIME;
                    $info['code'] = $code;
                    $info['msg'] = $message;
                    $info['status'] = 1;
                    $info['userid'] = $userid;
                    model('email_log')->add($info);
                    $this->json_function(1, '发送成功');
                }
            } else {
                $this->json_function(0, '请输入邮箱或电话号码');
                exit();
            }
        }
    }

    /*发送邮箱验证码*/
    public function send_email_code()
    {
        if (IS_POST) {
            $info = I('post.');
            $_account = htmlspecialchars(trim($info['account']));
            if (!isemail($_account)) {
                $this->json_function(0, '请输入正确的邮箱格式');
                exit();
            }

            $userid = $info['userid'];
            $target = $info['random'];
            $title = $info['title'];
            if (!$userid || !$target) {
                $this->json_function(0, '参数错误！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $target))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $code = random(6, 1);
            $message = '您的验证码为' . $code . "。该验证码有效期为5分钟，请勿向任何人提供那您接收到的验证码信息";
            $userinfo = model('member')->where(array('userid' => $userid))->find();
            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
            $sqlmap = array();
            $sqlmap['posttime'] = array('between', array($beginToday, $endToday));
            $sqlmap['userid'] = $userid;
            $lastSms = model('email_log')->where($sqlmap)->order('id DESC')->find();
            if (($lastSms['posttime'] + 60) > NOW_TIME) {
                $this->json_function(0, '请等待60秒后再获取');
                exit();
            }
            if ($_account == $userinfo['email'] && $userinfo['email_status'] == 0) {
                $result = sendmail($_account, $title, $message);
                if ($result) {
                    $info = array();
                    $info['email'] = $_account;
                    $info['posttime'] = NOW_TIME;
                    $info['code'] = $code;
                    $info['msg'] = $message;
                    $info['status'] = 1;
                    $info['userid'] = $userid;
                    model('email_log')->add($info);
                    $this->json_function(1, '发送成功');
                }

            } elseif ($_account != $userinfo['email']) {
                $count = model('member')->where(array('email' => $_account))->count();
                if ($count > 0) {
                    $this->json_function(0, '邮箱已经被占用');
                    exit();
                } else {
                    $result = sendmail($_account, $title, $message);
                    if ($result) {
                        $info = array();
                        $info['email'] = $_account;
                        $info['posttime'] = NOW_TIME;
                        $info['code'] = $code;
                        $info['msg'] = $message;
                        $info['status'] = 1;
                        $info['userid'] = $userid;
                        model('email_log')->add($info);
                        $this->json_function(1, '发送成功');
                    }

                }


            } elseif ($_account == $userinfo['email'] && $userinfo['email_status'] == 1) {
                $this->json_function(0, '您已经认证过该邮箱了');
                exit();
            }

        }
    }


    /*验证找回密码验证码是否通过*/
    public function check_find_pwd()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '请输入账号或验证码');
                exit();
            }

            $_account = htmlspecialchars(trim($info['username']));
            $_code = htmlspecialchars(trim($info['code']));
            $sqlmap = array();
            $sqlmap['phone|email'] = array('EQ', $_account);
            $userinfo = model('member')->where($sqlmap)->find();
            if (!$userinfo) {
                $this->json_function(0, '该账号不存在！');
                exit();
            }
            if (isemail($_account) != FALSE) {
                $email_log = model('email_log')->where(array('email' => $_account, 'code' => $_code, 'userid' => $userinfo['userid']))->order('id DESC')->find();
                if (!$email_log) {
                    $this->json_function(0, '该验证码不存在，请重新获取');
                    exit();
                }
                if (NOW_TIME > ($email_log['posttime'] + 5 * 60)) {
                    $this->json_function(0, '验证码已失效，请重新获取过');
                    exit();
                }
                $app = model('member_app')->where(array('userid' => $userinfo['userid']))->find();
                $random = random(15);
                if ($app) {
                    $result = model('member_app')->where(array('userid' => $userinfo['userid']))->save(array('target' => $random));
                } else {

                    $data = array();
                    $data['alias'] = $userinfo['userid'];
                    $data['userid'] = $userinfo['userid'];
                    $data['platform'] = 1;
                    $data['reg_time'] = NOW_TIME;
                    $data['name'] = 1;
                    $data['target'] = $random;
                    $result = model('member_app')->add($data);
                }

                if ($result) {
                    $return = array();
                    $return['userid'] = $userinfo['userid'];
                    $return['random'] = $random;
                    $this->json_function(1, '验证通过', $return);

                }


            } elseif (is_mobile($_account) != FALSE) {
                $sms = model('sms_report')->where(array('mobile' => $_account, 'id_code' => $_code, 'enum' => 'password', 'userid' => $userinfo['userid']))->order('id DESC')->find();
                if (!$sms) {
                    $this->json_function(0, '该验证码不存在，请重新获取');
                    exit();
                }
                if (NOW_TIME > ($sms['posttime'] + 5 * 60)) {
                    $this->json_function(0, '验证码已失效，请重新获取过');
                    exit();
                }
                $app = model('member_app')->where(array('userid' => $userinfo['userid']))->find();
                $random = random(15);
                if ($app) {
                    $result = model('member_app')->where(array('userid' => $userinfo['userid']))->save(array('target' => $random));
                } else {

                    $data = array();
                    $data['alias'] = $userinfo['userid'];
                    $data['userid'] = $userinfo['userid'];
                    $data['platform'] = 1;
                    $data['reg_time'] = NOW_TIME;
                    $data['name'] = 1;
                    $data['target'] = $random;
                    $result = model('member_app')->add($data);
                }

                if ($result) {
                    $return = array();
                    $return['userid'] = $userinfo['userid'];
                    $return['random'] = $random;
                    $this->json_function(1, '验证通过', $return);

                }


            } else {
                $this->json_function(0, '账号错误！');
                exit();
            }

        }
    }


    /*重置密码*/
    public function reset_pwd()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '请输入账号或验证码');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $_account = htmlspecialchars(trim($info['username']));
            $_password = htmlspecialchars(trim($info['password']));
            $_userid = htmlspecialchars(trim($info['userid']));
            $_target = htmlspecialchars(trim($info['random']));
            $sqlmap = array();
            $sqlmap['email|phone'] = $_account;
            $sqlmap['userid'] = $_userid;
            $userinfo = model('member')->where($sqlmap)->find();


            if (!$userinfo) {
                $this->json_function(0, '该用户信息不存在');
                exit();
            }

            $password = md5(md5($_password . $userinfo['encrypt']));
            $result = model('member')->where(array('userid' => $userinfo['userid']))->setField('password', $password);
            if (!$result) {
                $this->json_function(0, '密码修改失败');
                exit();
            } else {
                $this->json_function(1, '密码已成功重置');
            }

        }
    }

    /*修改密码*/
    public function update_pwd()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '请输入账号或验证码');
                exit();
            }
            $userid = $info['userid'];
            $target = $info['random'];
            if (!$userid || !$target) {
                $this->json_function(0, '参数错误！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $target))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $userinfo = model('member')->find($userid);
            $oldpass = md5(md5($info['oldpass'] . $userinfo['encrypt']));
            $rs = model('member')->where(array('userid' => $userid))->find();
            if ($oldpass != $rs['password']) {
                $this->json_function(0, '原密码错误');
                exit();
            }
            if ($rs) {

                $info['password'] = md5(md5($info['password'] . $userinfo['encrypt']));
                $result = model('member')->where(array('userid' => $userid))->save($info);
                if (!$result) {
                    $this->json_function(0, '修改密码失败');
                    exit();
                }
                $this->json_function(1, '修改密码成功');

            } else {
                $this->json_function(0, '该用户不存在');
                exit();
            }
        }
    }

    /*修改头像*/
    public function update_avatar()
    {
        if (IS_POST) {
            if (!$info) {
                $this->json_function(0, '请输入账号或验证码');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            extract($info);
            $target = $random;
            if (!$userid || !$target) {
                $this->json_function(0, '参数错误！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $target))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            if ($info['avatar']) {
                //把上传的文件移入文件
                $avatar = $info['avatar'];
                $avatar_info = A('Member/Profile');
                $suid = sprintf("%09d", $userid);
                $dir1 = substr($suid, 0, 3);
                $dir2 = substr($suid, 3, 2);
                $dir3 = substr($suid, 5, 2);
                $rootDir = SITE_PATH . '/uploadfile/avatar/';
                $userDir = $dir1 . '/' . $dir2 . '/' . $dir3 . '/';

                //头像新文件名
                $filename = $rootDir . $userDir . $userid . '_avatar.jpg';
                // 调取150*150的缩略图
                $list = explode('.', $avatar);
                $avatar = $list['0'] . '_150.' . $list['1'];
                if (file_exists(SITE_PATH . $avatar)) {
                    $result = moveFile(SITE_PATH . $avatar, $filename);
                }
            }

            model('member')->where(array('userid' => $userid))->save(array('nickname' => $nickname));
            $this->json_function(1, '修改用户信息成功');

        }

    }

    /*修改绑定用户电话*/
    public function update_phone()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '请输入账号或验证码');
                exit();
            }
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            extract($info);
            $target = $random;

            if (!$userid || !$target || !$phone || !$code) {
                $this->json_function(0, '参数错误！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $target))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            if (!is_mobile($phone)) {
                $this->json_function(0, '账户信息格式错误');
                exit();
            }

            $sqlmap = array();
            $sqlmap['userid'] = array('NEQ', $userid);
            $sqlmap['phone'] = array('EQ', $phone);
            $count = model('member')->where($sqlmap)->count();
            if ($count > 0) {
                $this->json_function(0, '该手机已存在！');
                exit();
            }

            $sms = model('sms_report')->where(array('mobile' => $phone, 'id_code' => $code, 'enum' => 'phone'))->order('id DESC')->find();
            if (!$sms) {
                $this->json_function(0, '该验证码不存在，请重新获取');
                exit();
            }
            if (NOW_TIME > ($sms['posttime'] + 5 * 60)) {
                $this->json_function(0, '验证码已失效，请重新获取过');
                exit();
            }


            $user_info = model('member')->where(array('userid' => $userid))->save(array('phone' => $phone, 'phone_status' => 1));
            if ($user_info) {
                $this->json_function(1, '修改电话成功！');
            }

        }

    }

    /*修改用户qq*/
    public function update_qq()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '请输入qq账号');
                exit();
            }
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            extract($info);
            $target = $random;

            if (!$userid || !$target || !$qq) {
                $this->json_function(0, '参数错误！');
                exit();
            }


            $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $target))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $sqlmap = array();
            $sqlmap['userid'] = array('NEQ', $userid);
            $sqlmap['qq'] = array('EQ', $qq);
            $count = model('member')->where($sqlmap)->count();
            if ($count > 0) {
                $this->json_function(0, '该qq已被绑定！');
                exit();
            }

            $user_info = model('member')->where(array('userid' => $userid))->save(array('qq' => $qq));
            if ($user_info) {
                $this->json_function(1, '修改qq信息成功');
            }

        }

    }

    /*修改邮箱*/
    public function update_email()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '请输入账号信息');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            extract($info);
            $target = $random;

            if (!$userid || !$target || !$email) {
                $this->json_function(0, '参数错误！');
                exit();
            }


            if (!isemail($email)) {
                $this->json_function(0, '账户信息格式错误');
                exit();
            }


            $sqlmap = array();
            $sqlmap['userid'] = array('NEQ', $userid);
            $sqlmap['email'] = array('EQ', $email);
            $count = model('member')->where($sqlmap)->count();
            if ($count > 0) {
                $this->json_function(0, '该邮箱已存在！');
                exit();
            }


            $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $target))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }


            $email_log = model('email_log')->where(array('email' => $email, 'code' => $code, 'userid' => $userid))->order('id DESC')->find();
            if (!$email_log) {
                $this->json_function(0, '该验证码不存在，请重新获取');
                exit();
            }
            if (NOW_TIME > ($email_log['posttime'] + 5 * 60)) {
                $this->json_function(0, '验证码已失效，请重新获取过');
                exit();
            }


            $user_info = model('member')->where(array('userid' => $userid))->save(array('email' => $email, 'email_status' => 1));

            if ($user_info) {
                $this->json_function(1, '修改email信息成功');
            } else {
                $this->json_function(0, '修改email信息失败，请稍后再试');
                exit();

            }

        }
    }

    /*联动地区*/
    public function get_area()
    {
        $id = I('id', 0, 'intval');
        $area = model('linkage')->where(array('parentid' => $id, 'keyid' => 1))->select();
        $count = model('linkage')->where(array('parentid' => $id, 'keyid' => 1))->count();
        $this->json_function(1, $count, $area);
    }

    public function get_address()
    {
        $info = I('post.');
        if (!$info) {
            $this->json_function(0, '参数错误！');
            exit();
        }

        if (!$info['userid']) {
            $this->json_function(0, '请先登录！');
            exit();
        }
        extract($info);
        $target = $random;
        $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $target))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }

        $userinfo = model('member')->where(array('userid' => $userid))->find();
        //查询所在地
        if ($userinfo['address']) {
            $address = string2array($userinfo['address']);
        }
        extract($address);
        $provice_name = model('linkage')->where(array('linkageid' => $provice))->getField('name');
        $city_name = model('linkage')->where(array('linkageid' => $city))->getField('name');
        $area_name = model('linkage')->where(array('linkageid' => $area))->getField('name');


        //收货地址
        if ($userinfo['receives']) {
            $receives = string2array($userinfo['receives']);
        }
        extract($receives);
        $return = array();
        $return['provice'] = $provice;
        $return['city'] = $city;
        $return['area'] = $area;
        $return['provice_name'] = $provice_name;
        $return['city_name'] = $city_name;
        $return['area_name'] = $area_name;
        $return['r_address'] = $r_address;
        $return['r_name'] = $r_name;
        $return['r_phone'] = $r_phone;
        $this->json_function(1, '', $return);

    }

    /*修改收货地址*/
    public function update_address()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '参数错误！');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            $addr = array();
            $addr['provice'] = $info['province'];
            $addr['city'] = $info['city'];
            $addr['area'] = $info['area'];
            $map = array();
            $map['r_address'] = $info['r_address'];
            $map['r_name'] = $info['r_name'];
            $map['r_phone'] = $info['r_phone'];
            $sqlmap = array();
            $sqlmap['address'] = array2string($addr);//所在地
            $sqlmap['receives'] = array2string($map);
            $userinfo = model('member')->where(array('userid' => $info['userid']))->save($sqlmap);
            if ($userinfo) {
                $this->json_function(1, '更新成功');

            }

        }


    }

    /*修改身份认证*/
    public function update_identity()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '参数错误！');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            if (strlen($info['id_number']) < 15 || strlen($info['id_number']) > 18) {
                $this->error('请输入正确的身份证号码');
                return FALSE;
            }

            $rs = model('member_attesta')->where(array('userid' => $info['userid'], 'type' => 'identity'))->find();
            if ($rs['status'] == 1) {
                $this->json_function(0, '亲，您的信息已审核通过，请不要重复提交');
                exit();
            }


            $infos = array();
            $infos['name'] = $info['name'];
            $infos['id_number'] = $info['id_number'];
            $infos['person_img'] = $info['person_img'];
            $infos['face_img'] = $info['face_img'];
            $infos['back_img'] = $info['back_img'];
            $sqlmap = array();
            $sqlmap['infos'] = array2string($infos);
            $sqlmap['userid'] = $info['userid'];
            $sqlmap['dateline'] = NOW_TIME;
            $sqlmap['type'] = 'identity';
            $sqlmap['status'] = 0;

            $sqlMap = array();
            $sqlMap['year'] = $info['year'];
            $sqlMap['month'] = $info['month'];
            $sqlMap['day'] = $info['day'];
            $sqlMap['age'] = $info['age'];

            $conditions = array();
            $conditions['sex'] = $info['sex'];
            $conditions['birthday'] = array2string($sqlMap);


            if ($rs) {
                $sqlmap['updatetime'] = NOW_TIME;
                $result = model('member_attesta')->where(array('id' => $rs['id']))->save($sqlmap);

            } else {
                $result = model('member_attesta')->add($sqlmap);

            }


            $counts = model('member_detail')->where(array('userid' => $info['userid']))->count();
            if ($counts > 0) {
                $r_detail = model('member_detail')->where(array('userid' => $info['userid']))->save($conditions);
            } else {
                $r_detail = model('member_detail')->add($conditions);
            }

            if ($result || $r_detail) {
                $this->json_function(1, '已成功提交身份信息，请等待审核');

            }


        }
    }

    public function bank_api()
    {
        if (IS_GET) {
            $ch = curl_init();
            $account = I('account');
            $url = 'http://apis.baidu.com/datatiny/cardinfo/cardinfo?cardnum=' . $account;
            $header = array(
                'apikey:  c929640261374c68da4396fd138faa8f',
            );
            // 添加apikey到header
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // 执行HTTP请求
            curl_setopt($ch, CURLOPT_URL, $url);
            $res = curl_exec($ch);
            $result = json_decode($res);

            /* echo $result->data->{'bankname'};
                echo $result->status;*/

            if ($result->status == 1) {
                $this->json_function(1, '获取成功', $result->data->{'bankname'});
            } else {
                $this->json_function(0, '获取信息失败，请稍后再试');
                exit();

            }

        }

    }

    /*绑定淘宝信息    */
    public function bind_tb_info()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '参数错误！');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            extract($info);

            /* 统计该会员已绑定数量 */
            $bind_tb_nums = C('bind_tb_nums');  //  绑定个数
            $count = model('member_bind')->where(array('userid' => $userid, 'status' => array('NEQ', 2)))->count();
            if ($count >= $bind_tb_nums) {
                $this->json_function(0, '您已绑定了' . $count . '个淘宝帐号，已达到最高绑定数量了！');
                exit();
            }

            /* 该账号是否已经被绑定 */
            $account_count = model('member_bind')->where(array('account' => $account, 'userid' => array('NEQ', $userid)))->count();
            if ($account_count >= 1) {
                $this->json_function(0, '该淘宝账号已经被绑定过，请更换淘宝账号');
                exit();
            }

            /* 查看是否本人已绑定该账号 */
            $update_info = model('member_bind')->where(array('userid' => $userid, 'account' => $account))->find();
            if ($update_info['status'] && $update_info['status'] != 2) {
                $this->json_function(0, '该账号您已绑定，请勿重复绑定');
                exit();
            }

            /* 当重新进行绑定的时候 */
            if ($update_info) {
                $update = array();
                $update['status'] = 1;
                $update['updatetime'] = NOW_TIME;
                $result = model('member_bind')->where(array('id' => $update_info['id']))->save($update);
                if (!$result) {
                    $this->json_function(0, '绑定淘宝账号失败');
                    exit();
                } else {
                    $this->json_function(1, '绑定淘宝账号成功');
                }
            }
            $data = array();
            $data['userid'] = $userid;
            $data['account'] = $account;
            if ($get_info['status'] == 1) {
                $data['reg_time'] = $get_info['info']['regTime'];
                $data['safe_grade'] = $get_info['info']['safeType'];
                $data['is_real_name'] = $get_info['info']['utype'];
                $data['mid_comment'] = $get_info['info']['brate1'];
                $data['cha_comment'] = $get_info['info']['brate2'];
                $data['favorable_rate'] = $get_info['info']['bok_p'] ? $get_info['info']['bok_p'] : '未知';
                $data['account_level'] = $get_info['info']['bLevelIco'];
                $data['bLevel'] = $get_info['info']['bLevel'];
                $data['bscore'] = $get_info['info']['bscore'];
            }
            $data['status'] = 1;
            $data['is_default'] = 0;
            $data['updatetime'] = $data['inputtime'] = NOW_TIME;
            $result = model('member_bind')->update($data);
            if (!$result) {
                $this->json_function(0, '绑定淘宝账号失败');
                exit();
            } else {
                $this->json_function(1, '绑定淘宝账号成功');
            }

        }

    }


    /*删除淘宝账号绑定*/
    public function bind_del_tb()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '参数错误！');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            extract($info);
            if (!$id) {
                $this->json_function(0, '参数错误！');
                exit();
            }
            $result = model('member_bind')->where(array('id' => $id, 'userid' => $userid))->delete();
            if (!$result) {
                $this->json_function(0, '该账号删除失败，请稍后再尝试');
                exit();
            } else {
                $this->json_function(1, '该账号已删除');

            }
        }
    }


    /*设置为默认账号*/
    public function setdefault()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '参数错误！');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            extract($info);
            $rs = model('member_bind')->find($id);
            if (!$rs) {
                $this->json_function(0, '该账号已删除');
                exit();
            }
            if ($rs['userid'] != $userid) {
                $this->json_function(0, '请登录您的会员帐号！', U('member/index/login/'));
                exit();
            }


            /* 把该会员的所有绑定信息设置为不是默认 */
            model('member_bind')->where(array('userid' => $userid))->setField('is_default', 0);

            /* 设置当前账号为默认账号 */
            $result = model('member_bind')->where(array('id' => $id))->setField('is_default', 1);
            if (!$result) {
                $this->json_function(0, '设置默认账号失败');
            }
            $this->json_function(1, '默认账号设置成功！');
        }

    }


    /*获取支行信息*/
    public function get_bank_info()
    {
        $banks = model('linkage')->where(array('parentid' => 0, 'keyid' => 3360))->select();
        if ($banks) {
            $this->json_function(1, '获取信息成功', $banks);
        }

    }

    /*绑定银行信息*/
    public function bind_bank_info()
    {

        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '参数错误！');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $map = array();
            $map['infos'] = array2string($info);
            $map['userid'] = $info['userid'];
            $map['dateline'] = NOW_TIME;
            $map['status'] = 1;
            $map['type'] = 'bank';
            $infos = model('member_attesta')->where(array('userid' => $info['userid'], 'type' => 'identity'))->find();
            if (empty($infos) || $infos['status'] == 0) {
                $this->json_function(0, '您实名认证正在审核中，实名认证通过后才可绑定');
                exit();
            } elseif (empty($infos) || $infos['status'] == -1) {
                $this->json_function(0, '您实名认证未通过，通过后才可绑定');
                exit();
            }


            if (!is_numeric($info['account']) || strlen($info['account']) < 16 || strlen($info['account']) > 19) {
                $this->json_function(0, '请输入正确的银行卡号');
                exit();
            }


            $accounts = model('member')->where(array('userid' => $info['userid']))->find();
            if ($accounts['bank_status'] == 1) {
                $this->json_function(0, '已绑定，请勿重新绑定');
                exit();
            }
            //判断该银行账号是否绑定
            /*$attesta_infos =  model('member_attesta')->where(array('type'=>'bank','userid'=>array('NEQ',$info['userid']),'infos'=>array('LIKE','%'.$info['account'].'%')))->count();

            if($attesta_infos >0){
                $this->json_function(0,'银行账号已存在，请勿重复绑定');
                exit();
            }*/
            //记录该用户绑定的信息
            $result = model('member_attesta')->add($map);
            if ($result) {
                $_data = model('member')->where(array('userid' => $info['userid']))->setField('bank_status', 1);
                //$url = ($_data['modelid'] == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
                //runhook('member_attesta_bank');
                $this->json_function(1, '银行绑定成功');
            } else {
                $this->json_function(0, '操作失误，请重试！');
                exit();
            }
        }

    }


    public function bind_alipay_info()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            if (empty($info['account'])) {
                $this->json_function(0, '请输入支付宝账号');
                exit();
            }
            $email = isemail($info['account']);
            $phone = is_mobile($info['account']);
            if (!$email && !$phone) {
                $this->json_function(0, '格式错误');
                exit();
            }

            $infos = model('member_attesta')->where(array('userid' => $info['userid'], 'type' => 'identity'))->find();
            if (empty($infos) || $infos['status'] == 0) {
                $this->json_function(0, '您实名认证正在审核中，实名认证通过后才可绑定');
                exit();
            } elseif (empty($infos) || $infos['status'] == -1) {
                $this->json_function(0, '您实名认证未通过，通过后才可绑定');
                exit();
            }


            $exit = model('member_attesta')->where(array('userid' => $info['userid'], 'type' => 'alipay'))->find();
            if ($exit && $exit['status'] == 1) {
                $this->json_function(0, '支付宝账号已认证过，请勿重复认证');
                exit();
            }


            //判断该支付宝账号是否绑定
            $count = model('member_attesta')->where(array('type' => 'alipay', 'userid' => array('NEQ', $info['userid']), 'infos' => array('LIKE', '%' . $info['account'] . '%')))->count();
            if ($count > 0) {
                $this->json_function(0, '支付宝账号已存在！');
                exit();
            }
            //将认证信息存入数据库
            $sqlmap = array();
            $arr = array('username' => $name, 'alipay_account' => $info['account'], 'alipay_code' => $info['alipay_code']);
            $sqlmap['infos'] = array2string($arr);
            $sqlmap['userid'] = $info['userid'];
            $sqlmap['dateline'] = NOW_TIME;
            $sqlmap['status'] = 1;
            $sqlmap['type'] = 'alipay';
            $result = model('member_attesta')->add($sqlmap);
            if ($result) {
                model('member')->where(array('userid' => $info['userid']))->setField('alipay_status', 1);
                //runhook('member_attesta_alipay');
                $this->json_function(1, '支付宝绑定成功');
            } else {
                $this->json_function(0, '操作失误，请重试！');
                exit();
            }

        }

    }


    public function get_person_like()
    {
        $like = model('linkage')->where(array('parentid' => 0, 'keyid' => 3372))->select();
        //var_dump($like);
        if ($like) {
            $this->json_function(1, '获取信息成功', $like);
        }
    }


    /*提现后台配置信息*/
    public function cash_config_info()
    {
        $pay_setting = getcache('deposite_setting', 'pay');
        extract($pay_setting);
        $lists = array();
        /*快速提现最快到账时间*/
        $lists['quick_time'] = $quick['time'];
        /*普通提现到账时间*/
        $lists['common_time'] = $common['time'];
        /*普通会员。商家手续费*/
        $lists['service_fee'] = $quick['service']['common'];
        /*vip 会员，商家手续费*/
        $lists['vip_service_fee'] = $quick['service']['vip'];
        /*提现方式*/
        $lists['cash_type'] = $type;
        /*最少提现金额*/
        $lists['min_money'] = $pay_setting['min_money'];
        /*提现金额的倍数*/
        $lists['multiple_money'] = $pay_setting['multiple_money'];
        if ($lists) {
            $this->json_function(1, '获取信息成功', $lists);
        }
    }


    public function person_cash()
    {

        if (IS_POST) {

            $info = I('post.');
            $info['inputtime'] = NOW_TIME;
            $info['status'] = 0;
            $info['userid'] = $info['userid'];
            $info['source'] = 1;
            $info['ip'] = get_client_ip();
            //判断是否有申请提现
            $sign = '4-1-' . $info['userid'] . '-' . $info['money'] . '-' . dgmdate($info['inputtime'], 'Y-m-d H:i');
            $posttime = model('cash_records')->where(array('userid' => $info['userid']))->order('cashid DESC')->find();
            if ($posttime['inputtime'] + 60 > NOW_TIME) {
                $this->json_function(0, '请一分钟后再申请提现');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }


            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            if (empty($info['money'])) {
                $this->json_function(0, '请输入提现金额');
                exit();
            }

            $userid = $info['userid'];
            $pay_setting = getcache('deposite_setting', 'pay');
            extract($pay_setting);
            if (empty($type)) {
                $this->json_function(0, '管理员没有设置提现方式，请联系管理员');
                exit();
            }

            //提现手续费
            $modelid = model('member')->getFieldByUserid($userid, 'modelid');
            if ($modelid == 1) {
                $fee = $quick['service']['common'];
            } else {
                //判断该商家是否为普通商家
                $groupid = model('member')->getFieldByUserid($userid, 'groupid');
                if ($groupid == 1) {//普通商家
                    $fee = $quick['service']['common'];
                } else {
                    $fee = $quick['service']['vip'];
                }
            }

            $_identity = model('member_attesta')->where(array('userid' => $userid, 'type' => 'identity', 'status' => 1))->find();
            if (!$_identity) {
                $this->json_function(0, '请先认证实名认证');
                exit();
            }

            $identify = string2array($_identity['infos']);
            $name = $identify['name'];
            //查出该用户提交绑定的账号
            if ($info['type'] == 1) {//提到银行卡
                $bank = model('member_attesta')->where(array('userid' => $userid, 'type' => 'bank', 'status' => 1))->find();
                if (!$bank) {
                    $this->json_function(0, '您还没有绑定银行卡账号，请先绑定');
                    exit();
                }
                $bankinfos = string2array($bank['infos']);
                $info['bank'] = model('linkage')->getFieldByLinkageid($bankinfos['bank_name'], 'name');
                $info['name'] = $name;
                $info['cash_alipay_username'] = $bankinfos['account'];
            } else {//提现到支付宝
                $alipay = model('member_attesta')->where(array('userid' => $userid, 'type' => 'alipay', 'status' => 1))->find();
                if (!$alipay) {
                    $this->json_function(0, '你还没有绑定支付宝账号，请先绑定');
                    exit();
                }
                $alipayinfos = string2array($alipay['infos']);
                $info['name'] = $name;
                // $info['bank'] = '';
                $info['cash_alipay_username'] = $alipayinfos['alipay_account'];
            }


            //判断当前金额是否符合条件
            $min_money = $pay_setting['min_money'];
            if ($info['money'] < 0) {
                $this->json_function(0, '金额不能小于0 ');
                exit();
            }
            if ($info['money'] < $min_money && $info['money'] > 0) {
                $this->json_function('金额不能小于' . $min_money);
                exit();
            }
            if ($info['money'] % $pay_setting['multiple_money'] != 0) {
                $this->json_function(0, '金额必须为' . $pay_setting['multiple_money'] . '倍数');
                exit();
            }
            if ($info['paypal'] == 1) {
                $info['fee'] = 0;
                $info['totalmoney'] = $info['money'];
            } else {
                $info['fee'] = $info['money'] * $fee / 100;
                $info['totalmoney'] = $info['money'] - $info['money'] * $fee / 100;
            }

            $money = model('member')->getFieldByUserid($userid, 'money');

            if ($money < $info['money']) {
                $this->json_function(0, '您的账户余额不足');
                exit();
            }


            $result = model('cash_records')->add($info);
            if ($result) {
                action_finance_log($userid, -$info['money'], 'money', 'userid' . $userid . ':申请提现', $sign, array());
                $this->json_function(1, '申请成功，请耐心等待');
            } else {
                $this->json_function(0, '申请失败');
                exit();
            }

        }

    }

    /*获取用户支付宝以及银行卡*/
    public function cash_account_info()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            $alipay = model('member_attesta')->where(array('userid' => $info['userid'], 'type' => 'alipay'))->find();
            $bank = model('member_attesta')->where(array('userid' => $info['userid'], 'type' => 'bank'))->find();

            if (!$alipay) {
                $this->json_function(0, '未绑定支付宝账号，请先绑定');
                exit();
            }

            $list = array();

            $alipay_info = string2array($alipay['infos']);
            $list['alipay_account'] = $alipay_info['alipay_account'];

            if (!$bank) {
                $this->json_function(0, '未绑定银行账号，请先绑定');
                exit();
            }

            $bankinfos = string2array($bank['infos']);
            $list['bank_name'] = model('linkage')->getFieldByLinkageid($bankinfos['bank_name'], 'name');
            $list['bank_account'] = $bankinfos['account'];
            $this->json_function(1, '获取信息成功', $list);
        }
    }

    /*提现手续费*/
    public function cash_fee_money()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            extract($info);
            $pay_setting = getcache('deposite_setting', 'pay');
            $quick = $pay_setting['quick'];
            //判断该会员是商家还是用户
            $modelid = model('member')->getFieldByUserid($userid, 'modelid');
            //提现手续费  判断是否为商家
            //$fee = ($modelid == 1) ? $quick['service']['common'] : $quick['service']['vip'];
            if ($modelid == 1) {
                $fee = $quick['service']['common'];
            } else {
                //判断该商家是否为普通商家
                $groupid = model('member')->getFieldByUserid($userid, 'groupid');
                if ($groupid == 1) {//普通商家
                    $fee = $quick['service']['common'];
                } else {
                    $fee = $quick['service']['vip'];
                }
            }
            $total_money = $money - $money * $fee / 100;
            $lists = array();
            $lists['total_money'] = $total_money;
            $lists['fee'] = $fee;
            $this->json_function(1, '获取信息成功', $lists);


        }
    }

    /*获取活动配置*/
    public function get_activity_config()
    {
        $activity_type = 'trial';
        $setting = model('activity_set')->where(array('activity_type' => $activity_type))->getField('key,value');
        // 公用
        if ($setting['single_mode']) $setting['single_mode'] = string2array($setting['single_mode']);
        if ($setting['seller_join_condition']) $setting['seller_join_condition'] = string2array($setting['seller_join_condition']);
        if ($setting['buyer_join_condition']) $setting['buyer_join_condition'] = string2array($setting['buyer_join_condition']);
        if ($setting['seller_discount_range']) $setting['seller_discount_range'] = string2array($setting['seller_discount_range']);
        if ($setting['seller_get_appeal']) $setting['seller_get_appeal'] = string2array($setting['seller_get_appeal']);
        // 免费试用
        if ($setting['seller_trialtalk_check']) $setting['seller_trialtalk_check'] = string2array($setting['seller_trialtalk_check']);
        if ($setting) {
            /*$lists = array();
            $lists['buyer_join_condition'] = $setting['buyer_join_condition'];
            $lists['buyer_good_buy_times'] = $setting['buyer_good_buy_times'];
            $lists['buyer_day_buy_times'] = $setting['buyer_day_buy_times'];
            $lists['buyer_write_order_time'] = $setting['buyer_write_order_time'];
            $lists['buyer_write_talk_time'] = $setting['buyer_write_talk_time'];
            $lists['buyer_check_update_order_sn'] = $setting['buyer_check_update_order_sn'];*/
            $this->json_function(1, '获取信息成功', $setting);

        }


    }

    /*申请订单*/
    public function apply_order()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            extract($info);
            $data_type = $data_type ? $data_type : 0;
            if ($goods_id < 1) {
                $this->json_function(0, '参数错误');
                exit();
            }
            $Factory = new \Product\Factory\product($goods_id);

            if ($Factory->product_info['mod'] != 'trial') {
                $this->json_function(0, '商品不存在');
                exit();
            }
            // 读取后台活动设置：参与条件
            $bind_set = string2array(C_READ('buyer_join_condition', 'trial'));
            if ($Factory->product_info['mod'] == 'trial' && $bind_set['bind_taobao'] == 4) {
                $bind_taobao = (int)trim($bind_taobao);
                if ($bind_taobao < 1) {
                    $this->json_function(0, '请选择您要购买的淘宝帐号');
                    exit();
                }
                $result = $this->pay_submit($goods_id, $talk_content, $bind_taobao, $userid);
            } else {
                $result = $this->pay_submit($goods_id, $talk_content, '', $userid);
            }

            if (!$result) {
                $this->json_function(0, $result);
            } else {
                $this->json_function(1, '试用申请成功，请等待审核', $result);
            }

        }


    }


    /*申请订单*/
    public function vip_apply_order()
    {
        $info = I('post.');
        if (!$info['userid']) {
            $this->json_function(0, '请先登录！');
            exit();
        }
        $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }
        extract($info);
        if ($goods_id < 1) {
            $this->json_function(0, '参数错误');
            exit();
        }
        $Factory = new \Product\Factory\product($goods_id);
        // 读取后台活动设置：参与条件
        $bind_set = string2array(C_READ('buyer_join_condition', 'trial'));
        if ($Factory->product_info['mod'] == 'trial' && $bind_set['bind_taobao'] == 4) {
            $bind_taobao = (int)trim($bind_taobao);
            if ($bind_taobao < 1) {
                $this->json_function(0, '请选择您要购买的淘宝帐号');
                exit();
            }
            $result = $this->vip_pay_submit($goods_id, $talk_content, $bind_taobao, $userid);
        } else {
            $result = $this->vip_pay_submit($goods_id, $talk_content, '', $userid);
        }

        if (!$result) {
            $this->json_function(0, $result);
        } else {
            $is_vip_shi = model('order')->getFieldById($result, 'is_vip_shi');
            $this->json_function(1, '试用申请成功，可直接下单填写订单号', $result);
        }


    }

    /**
     * vip试客用户抢购
     * $talk : 对商家说点什么
     * $bind_id : 选择购买的淘宝帐号
     */
    public function vip_pay_submit($goods_id, $talk = '', $bind_id = 0, $userid, $data_type = 1)
    {
        if ($goods_id < 1) {
            $this->json_function(0, '参数错误');
        }
        $factory = new \Product\Factory\product($goods_id);
        if ($factory->product_info['goods_vipfree'] != 1) {
            $this->json_function(0, '该商品不是没有vip免审权限');
            exit();
        }
        // 检测用户权限
        $ischk = $this->pay_check($goods_id, $userid);
        if ($ischk === TRUE) { // 权限通过时
            $info = array();
            $info['buyer_id'] = $userid;
            $info['seller_id'] = $factory->product_info['company_id'];
            $info['goods_id'] = $factory->product_info['id'];
            $info['act_mod'] = $factory->product_info['mod'];
            $info['source'] = 1;
            $info['trade_sn'] = date('YmdHis') . random(6, 1);
            $info['inputtime'] = $info['create_time'] = NOW_TIME;
            $groupid = model('member')->where(array('userid' => $userid))->getField('groupid');

            $info['status'] = 1;
            if ($data_type == 1) {
                if ((int)$groupid != 2) {
                    $this->json_function(0, '当前用户不是vip会员，请先申请vip会员');
                    exit();
                }
                if ($factory->product_info['goods_vipfree'] == 1) { //这个商品没有VIP免审
                    $groups = getcache('member_group', 'member');
                    $level = $groups[2];

                    $day_count = $level['day_count'];
                    $month_count = $level['month_count'];

                    if (!empty($day_count) || !empty($month_count)) { //没有缓存
                        //查询今日这个用户试用次数
                        $sql_where = array();
                        $sql_where['is_vip_shi'] = 1;
                        $time = strtotime(date("Y-m-d"));
                        $sql_where['buyer_id'] = $userid;
                        $sql_where['create_time'] = array('gt', $time);
                        $day_count_mysql = model('order')->where($sql_where)->count();

                        //查询这月这个用户试用次数
                        $sql_where2 = array();
                        $sql_where2['is_vip_shi'] = 1;
                        $sql_where2['buyer_id'] = $userid;

                        $time2 = strtotime(date("Y-m"));
                        $sql_where2['create_time'] = array('gt', $time2);
                        $month_count_mysql = model('order')->where($sql_where2)->count();
                        if ($day_count > $day_count_mysql && $month_count > $month_count_mysql) { //每日 和 每月比较
                            $info['status'] = 2;
                            $info['is_vip_shi'] = 1;
                            $info['check_time'] = NOW_TIME;
                        } else {
                            $this->json_function(0, '免审次数已完');
                            exit();
                        }
                    }
                }
            }

            $info['talk'] = trim($talk);
            $info['bind_id'] = $bind_id;
            $order_id = model('order')->update($info);

            //减少库存
            model('product')->where(array('id' => $this->product_info['id']))->setDec('already_num');
            if ($order_id) {
                $Factory = new \Product\Factory\order($order_id);
                $Factory->write_log('使用vip特权兑换试用资格', $userid);
                return $order_id;
            } else {
                return $error = '用户抢购失败';
            }
        } else {
            return $ischk;
        }
    }


    /*申请订单*/
    public function point_apply_order()
    {
        $info = I('post.');
        if (!$info['userid']) {
            $this->json_function(0, '请先登录！');
            exit();
        }
        $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }
        extract($info);
        if ($goods_id < 1) {
            $this->json_function(0, '参数错误');
            exit();
        }
        $Factory = new \Product\Factory\product($goods_id);
        // 读取后台活动设置：参与条件
        $bind_set = string2array(C_READ('buyer_join_condition', 'trial'));
        if ($Factory->product_info['mod'] == 'trial' && $bind_set['bind_taobao'] == 4) {
            $bind_taobao = (int)trim($bind_taobao);
            if ($bind_taobao < 1) {
                $this->json_function(0, '请选择您要购买的淘宝帐号');
                exit();
            }
            $result = $this->point_pay_submit($goods_id, $talk_content, $bind_taobao, $userid);
        } else {
            $result = $this->point_pay_submit($goods_id, $talk_content, '', $userid);
        }

        if (!$result) {
            $this->json_function(0, $result);
        } else {
            $is_vip_shi = model('order')->getFieldById($result, 'is_vip_shi');
            $this->json_function(1, '试用申请成功，可直接下单填写订单号', $result);
        }


    }


    /**
     * 积分免审
     * $talk : 对商家说点什么
     * $bind_id : 选择购买的淘宝帐号
     */
    private function point_pay_submit($goods_id, $talk = '', $bind_id = 0, $userid)
    {
        if ($goods_id < 1) {
            $this->json_function(0, '参数错误');
        }
        $factory = new \Product\Factory\product($goods_id);
        if ($factory->product_info['goods_point'] < 0) {
            $this->json_function(0, '该商品没有积分抵消vip免审权限');
            exit();
        }
        // 检测用户权限
        $ischk = $this->pay_check($goods_id, $userid);
        if ($ischk === TRUE) { // 权限通过时
            $info = array();
            $info['buyer_id'] = $userid;
            $info['seller_id'] = $factory->product_info['company_id'];
            $info['goods_id'] = $factory->product_info['id'];
            $info['act_mod'] = $factory->product_info['mod'];
            $info['source'] = 1;
            $info['trade_sn'] = date('YmdHis') . random(6, 1);
            $info['inputtime'] = $info['create_time'] = NOW_TIME;
            $point = model('member')->where(array('userid' => $userid))->getField('point');
            $set_point = Integral_quantity($factory->product_info['goods_price']);

            if ($factory->product_info['goods_point'] > 0 && (int)$point >= (int)$set_point) {
                $info['status'] = 2;
                $info['is_vip_shi'] = 2;
                $info['check_time'] = NOW_TIME;
            } else {
                $this->error = '当前积分不足';
                return FALSE;
            }

            //判断商品库存是否充足
            if ($factory->product_info['already_num'] >= $factory->product_info['goods_number']) {
                $this->error = '亲 来晚了当前没有库存了';
                return FALSE;
            }
            $info['talk'] = trim($talk);
            $info['bind_id'] = $bind_id;
            $order_id = model('order')->update($info);

            //减少商品库存
            model('product')->where(array('id' => $factory->product_info['id']))->setInc('already_num');


            if ($order_id) {
                $Factory = new \Product\Factory\order($order_id);
                $Factory->write_log('积分兑换试用资格', $userid);
                $sign = '9-' . $userid . $goods_id . NOW_TIME . '-1';
                action_finance_log($userid, -$set_point, 'point', '商品id:' . $factory->product_info['id'] . ',获取免审试用资格,花费' . $set_point . '积分', $sign, array('goods_id' => $factory->product_info['id']), TRUE);
                return $order_id;
            } else {
                return $error = '用户抢购失败';
            }
        }
    }


    /**
     * 用户抢购
     * $talk : 对商家说点什么
     * $bind_id : 选择购买的淘宝帐号
     */
    public function pay_submit($goods_id, $talk = '', $bind_id = 0, $userid)
    {
        if ($goods_id < 1) {
            $this->json_function(0, '参数错误');
        }
        $factory = new \Product\Factory\product($goods_id);
        // 检测用户权限
        $ischk = $this->pay_check($goods_id, $userid);
        if ($ischk === TRUE) { // 权限通过时
            $info = array();
            $info['buyer_id'] = $userid;
            $info['seller_id'] = $factory->product_info['company_id'];
            $info['goods_id'] = $factory->product_info['id'];
            $info['act_mod'] = $factory->product_info['mod'];
            $info['source'] = 1;
            $info['trade_sn'] = date('YmdHis') . random(6, 1);
            $info['inputtime'] = $info['create_time'] = NOW_TIME;
            $info['status'] = 1;
            $info['talk'] = trim($talk);
            $info['bind_id'] = $bind_id;
            $order_id = model('order')->update($info);
            if ($order_id) {
                $Factory = new \Product\Factory\order($order_id);
                $Factory->write_log('用户抢购资格', $userid);
                return $order_id;
            } else {
                return $error = '用户抢购失败';
            }
        } else {
            return $ischk;
        }
    }


    public function pay_check($goods_id, $userid)
    {
        $Factory = new \Product\Factory\product($goods_id);
        $config = $Factory->getConfig();
        /*---- 限制买家参与 ----*/
        if (empty($userid)) {
            $this->json_function(0, '尚未登录');
            exit();
        }

        $user_info = model('member')->find($userid);
        if ($user_info['modelid'] != 1) {
            $this->json_function(0, '暂时只限买家参与');
            exit();
        }
        /*---- 判断后台活动设置 ----*/
        /* 手机认证 */
        if ($config['buyer_join_condition']['phone'] && !$user_info['phone_status']) {
            $this->json_function(0, '请先进行手机认证');
            exit();

        }
        /* 邮箱认证 */
        if ($config['buyer_join_condition']['email'] && !$user_info['email_status']) {
            $this->json_function(0, '请先进行邮箱认证');
            exit();
        }
        /* 实名认证 */
        $identity_count = model('member_attesta')->where(array('userid' => $user_info['userid'], 'type' => 'identity'))->count();
        if ($config['buyer_join_condition']['realname'] && $identity_count != 1) {
            $this->json_function(0, '请先进行实名认证');
            exit();

        }

        /* 绑定淘宝账号 */
        $tb_count = model('member_bind')->where(array('userid' => $user_info['userid'], 'status' => array('NEQ', 2)))->count();
        if ($config['buyer_join_condition']['bind_taobao'] && $tb_count < 1) {
            $this->json_function(0, '请先绑定淘宝账号');
            exit();
        }

        /* 是否绑定支付宝 */
        $account = model('member_attesta')->where(array('userid' => $user_info['userid'], 'type' => 'alipay'))->count();
        if ($config['buyer_join_condition']['bind_alipay'] && $account != 1) {
            $this->json_function(0, '请先绑定支付宝账号');
            exit();
        }

        /*---- 检测商品状态 ----*/
        /* 上架状态 */
        if ($Factory->product_info['status'] != 1) {
            $this->json_function(0, '该活动不在进行中');
            exit();
        }
        if ($Factory->product_info['start_time'] > NOW_TIME) {
            $this->json_function(0, '该活动尚未开始');
            exit();

        }
        if ($Factory->product_info['end_time'] < NOW_TIME) {
            $this->json_function(0, '该活动已经结束');
            exit();

        }
        /* 检测商品库存 */
        if ($Factory->product_info['goods_number'] - $Factory->product_info['already_num'] < 1) {
            $this->json_function(0, '该商品已售罄');
            exit();
        }
        /*---- 检测活动设置 ----*/
        //1.是否有抢购未下单的
        $o_map = array();
        $o_map['buyer_id'] = $user_info['userid'];
        $o_map['goods_id'] = $Factory->product_info['id'];
        $o_map['status'] = array('NEQ', 0);
        $wait_fill_num = model('order')->where($o_map)->count();
        /*购物返利限定抢购次数*/
        $count = C_READ('buyer_good_buy_times', 'trial');
        if ($wait_fill_num >= $count) {
            $this->json_function(0, '您已抢购了该订单' . $count . '次，请勿重复抢购。');
            exit();

        }
        // 若设置为可以抢购多次，若有订单未完成的则不允许下单
        if ($count > 1) {
            $o_map['status'] = array('NOT IN', array('0', '7'));
            $is_over = model('order')->where($o_map)->count();
            if ($is_over > 0) {
                $this->json_function(0, '当前还有未完成的订单，请订单完成后再继续下单');
                exit();
            }
        }
        //2.每天参与总次数
        if ($config['buyer_day_buy_times'] > 0) {
            $o_map = array();
            $o_map['buyer_id'] = $user_info['userid'];
            $o_map['_string'] = "DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
            $buyer_day_buy_times = model('order')->where($o_map)->count();
            if ($buyer_day_buy_times >= $config['buyer_day_buy_times']) {
                $this->json_function(0, '每天只能参与' . $buyer_day_buy_times . '次抢购哦~,超出每日活动次数限制');
                exit();
            }
        }

        //3.单品抢购时间间隔
        if ($config['buyer_buy_time_limit'] > 0) {
            $o_map = array();
            $o_map['goods_id'] = $Factory->product_info['id'];
            $last_create_time = model('order')->where($o_map)->getField('create_time');
            if ($last_create_time > 0 && $last_create_time < $config['buyer_buy_time_limit']) {
                $this->json_function(0, '同时间段内抢购人数过多');
                exit();
            }
        }

        //4.单商品会员抢购次数
        if ($config['buyer_good_buy_times'] > 0) {
            $o_map = array();
            $o_map['buyer_id'] = $user_info['userid'];
            $o_map['goods_id'] = $Factory->product_info['id'];
            $o_map['status'] = array('GT', 1);
            $buyer_good_buy_times = model('order')->where($o_map)->count();
            if ($buyer_good_buy_times >= $config['buyer_good_buy_times']) {
                $this->json_function(0, '您已参与该商品' . $config['buyer_good_buy_times'] . '次抢购,请抢购其它商品吧');
                exit();
            }
        }

        /*仿重复申请同一店铺*/
        if ($Factory->product_info['goods_tips']['goods_order']['is_join'] == 1) {
            $o_map = array();
            $o_map['buyer_id'] = $user_info['userid'];
            $o_map['seller_id'] = $Factory->product_info['company_id'];
            $o_map['status'] = array('GT', 0);
            $buyer_continuity_apply = model('order')->where($o_map)->count();
            if ($buyer_continuity_apply > 0) {
                $this->json_function(0, '您已参与过该商家的商品,请抢购其它商家的商品吧');
                exit();
            }
        }

        $black_count = model('member_blacklist')->where(array('seller_id' => $Factory->product_info['company_id'], 'buyer_id' => $user_info['userid']))->count();
        if ($black_count > 0) {
            $this->json_function(0, '您已被该商家移入黑名单,请抢购其它商家的商品吧');
            exit();
        }
        return TRUE;
    }

    /*填写订单号*/
    public function fill_order_sn()
    {


        $info = I('post.');
        if (!$info['userid']) {
            $this->json_function(0, '请先登录！');
            exit();
        }
        $userid = $info['userid'];
        $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }


        $info['order_sn'] = trim($info['order_sn']);

        $factory = new \Product\Factory\order($info['order_id']);
        if (!$factory->order_info['id']) {
            $this->json_function('该订单不存在，请重新下单');
            exit();
        }
        $order_sn_count = model('order')->where(array('order_sn' => $info['order_sn']))->count();
        if ($order_sn_count > 0) {
            $this->json_function(0, '该订单号重复！请检查!');
            exit();
        }
        if ($factory->order_info['order_sn']) {
            $cause = '修改订单号,' . '订单号为：' . $info['order_sn'];
            if ($factory->order_info['act_mod'] == 'commission') {
                $arr = array();
                $arr['order_sn'] = $info['order_sn'];
                $arr['order_img'] = $info['order_img'];
                $result = $factory->fill_trade_no($arr, $cause, $userid);
            } else {
                $result = $factory->fill_trade_no($info['order_sn'], $cause, $userid);

            }
            // 免费试用且已发布试用报告的则状态变为待商家审核
            $report_count = model('trial_report')->where(array('order_id' => $factory->order_info['id']))->count();
            if ($factory->order_info['act_mod'] == 'trial' && $report_count > 0) {
                $factory->set_status(3);
            }
        } else {

            if ($factory->order_info['act_mod'] == 'commission') {
                $arr = array();
                $arr['order_sn'] = $info['order_sn'];
                $arr['order_img'] = $info['order_img'];
                $result = $factory->fill_trade_no($arr, '填写订单号,' . '订单号为：' . $info['order_sn'], $userid);
            } else {
                $result = $factory->fill_trade_no($info['order_sn'], '填写订单号,' . '订单号为：' . $info['order_sn'], $userid);
            }
        }
        if (!$result) {
            $this->json_function(0, $factory->getError());
            exit();
        }
        /*  runhook('order_fill_trade_no',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title'],'order_sn' => $info['order_sn'],'mod' => $factory->product_info['mod']));*/
        $this->json_function(1, '填写订单号成功');

    }

    /*填写试用报告*/
    public function fill_trial_report()
    {
        if (IS_POST) {
            $info = I('post.');
            $info = array_filter($info);
            if (!$info) {
                $this->json_function(0, '该信息不完整，请重新填写试用报告！');
                exit();
            }
            if ((int)$info['order_id'] < 1) {
                $this->json_function(0, '该订单不存在');
                exit();
            }
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            if ((int)$info['star'] == 0) {
                $this->json_function(0, '请您为本次试用选择星星打分');
                exit();
            }

            $infos = array();
            $infos['background'] = $info['background'];
            $infos['height'] = $info['height'];
            $infos['weight'] = $info['weight'];
            $infos['age'] = $info['age'];
            $infos['job'] = $info['job'];
            $infos['star'] = $info['star'];
            if ($info['content'] == '') {
                $this->json_function(0, '请填写试用过程及体验');
                exit();
            }
            $buyer_id = model('order')->getFieldById($info['order_id'], 'buyer_id');

            if ($buyer_id != $info['userid']) {
                $this->json_function(0, '您没有权限进行此操作');
                exit();
            }
            $factory = new \Product\Factory\order($info['order_id']);
            /*if ($factory->order_info['trial_report'])   $this->error('您已填写试用报告，请等待商家审核哦~');*/
            $data = array();
            $trial_report_info = model('trial_report')->where(array('order_id' => $info['order_id']))->order('id DESC')->find();
            if ($trial_report_info) {
                $data['id'] = $trial_report_info['id'];
            }


            $data['content'] = $info['content'];
            unset($info['content']);
            $report = array2string($infos);
            $data['base_info'] = $report;
            // 提取内容首图
            $data['thumb'] = $info['thumb'];
            $data['inputtime'] = NOW_TIME;
            $data['goods_id'] = $factory->order_info['goods_id'];
            $data['order_id'] = $info['order_id'];
            $data['userid'] = $factory->order_info['buyer_id'];
            $data['status'] = '0';
            $data['ip'] = get_client_ip();
            if (!$data) {
                $this->json_function(0, '该信息不完整，请重新填写试用报告！');
                exit();
            }
            $result = $factory->fill_trial_report($data, $info['userid']);
            if (!$result) {
                $this->json_function(0, $factory->getError());
                exit();
            }
            //  runhook('order_fill_report',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title']));
            $this->json_function(1, '填写试用报告成功，请等待商家审核哦~');
        }
    }

    /*关闭订单*/
    public function close_order_sn()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['order_id']) {
                $this->json_function(0, '该订单不存在');
                exit();
            }
            if (!$info['userid']) {
                $this->json_function(0, '请登录后再关闭订单');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $factory = new \Product\Factory\order($info['order_id']);
            if ($info['userid'] != $factory->order_info['buyer_id']) {
                $this->json_function(0, '您没有权限进行此操作');
                exit();
            }
            $result = $factory->close();
            if (!$result) {
                $this->json_function(0, '订单关闭失败，请稍后再试！');
                exit();
            }

            $this->json_function(1, '订单关闭成功');

        }
    }

    /*48.获取该会员是否参与过本次活动*/
    public function is_join()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            if (!$info['goods_id']) {
                $this->json_function(0, '参数错误！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            $count = model('order')->where(array('goods_id' => $info['goods_id'], 'buyer_id' => $info['userid']))->count();
            if ($count > 0) {
                $status = 0;
            } else {
                $status = 1;
            }

            $this->json_function(1, '获取信息成功', $status);


        }
    }


    /*购物返利配置*/
    public function get_rebate_config()
    {
        $activity_type = 'rebate';
        $setting = model('activity_set')->where(array('activity_type' => $activity_type))->getField('key,value');
        // 公用
        if ($setting['single_mode']) $setting['single_mode'] = string2array($setting['single_mode']);
        if ($setting['seller_join_condition']) $setting['seller_join_condition'] = string2array($setting['seller_join_condition']);
        if ($setting['buyer_join_condition']) $setting['buyer_join_condition'] = string2array($setting['buyer_join_condition']);
        if ($setting['seller_discount_range']) $setting['seller_discount_range'] = string2array($setting['seller_discount_range']);
        if ($setting['seller_get_appeal']) $setting['seller_get_appeal'] = string2array($setting['seller_get_appeal']);
        // 免费试用
        //   if($setting['seller_trialtalk_check']) $setting['seller_trialtalk_check'] = string2array($setting['seller_trialtalk_check']);
        if ($setting) {
            /*$lists = array();
            $lists['buyer_artificial_check'] = $setting['buyer_artificial_check'];
            $lists['buyer_good_buy_times'] = $setting['buyer_good_buy_times'];
            $lists['buyer_join_condition'] = $setting['buyer_join_condition'];
            $lists['buyer_day_buy_times'] = $setting['buyer_day_buy_times'];
            $lists['buyer_buy_time_limit'] = $setting['buyer_buy_time_limit'];
            $lists['buyer_write_order_time'] = $setting['buyer_write_order_time'];
            $lists['buyer_write_order_time'] = $setting['buyer_write_order_time'];
            $lists['buyer_update_order_type'] = $setting['buyer_update_order_type'];
            $lists['buyer_check_update_order_sn'] = $setting['buyer_check_update_order_sn'];*/
            $this->json_function(1, '获取信息成功', $setting);

        }


    }


    /*申请购物返利活动*/
    public function apply_rebate_order()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            extract($info);
            if ($goods_id < 1) {
                $this->json_function(0, '参数错误');
                exit();
            }
            $Factory = new \Product\Factory\product($goods_id);

            if (!$Factory) {
                $this->json_function(0, '商品信息错误！');
                exit();
            }

            if ($Factory->product_info['mod'] != 'rebate') {
                $this->json_function(0, '商品活动类型不存在');
                exit();
            }
            $result = $this->rebate_pay_submit($goods_id, $userid);
            if (!$result) {
                $this->json_function(0, $result);
            } else {
                $this->json_function(1, '抢购成功', $result);
            }

        }

    }


    /*抢购购物返利商品*/
    public function rebate_pay_submit($goods_id, $userid)
    {
        $user_info = model('member')->find($userid);
        $factory = new \Product\Factory\product($goods_id);

        if (!$user_info) {
            $this->json_function(0, '试客数据不存在');
        }
        $ischk = $this->rebate_pay_check($goods_id, $userid);
        if ($ischk === TRUE) {
            $info = array();
            $info['buyer_id'] = $user_info['userid'];
            $info['seller_id'] = $factory->product_info['company_id'];
            $info['goods_id'] = $factory->product_info['id'];
            $info['act_mod'] = $factory->product_info['mod'];
            $info['source'] = 1;
            $info['trade_sn'] = date('YmdHis') . random(6, 1);
            $info['inputtime'] = $info['create_time'] = NOW_TIME;
            $info['status'] = 2;
            $order_id = model('order')->update($info);
            if ($order_id) {
                $Factory = new \Product\Factory\order($order_id);
                $Factory->write_log('用户抢购资格', $userid);
                model('product')->where(array('id' => $factory->product_info['id']))->setInc('already_num');
                return $order_id;
            } else {
                $this->json_function(0, '用户抢购失败');
                exit();
            }
        } else {
            return FALSE;
        }
    }


    /**
     * 抢购检测
     */
    public function rebate_pay_check($goods_id, $userid)
    {
        $user_info = model('member')->find($userid);
        $factory = new \Product\Factory\product($goods_id);
        $config = $factory->getConfig();
        /*---- 限制买家参与 ----*/
        if (empty($user_info['userid'])) {
            $this->json_function(0, '尚未登录');
            exit();
        }
        if ($user_info['modelid'] != 1) {
            $this->json_function(0, '暂时只限买家参与');
            exit();
        }
        /*---- 判断认证设置 ----*/
        /* 手机认证 */
        if ($config['buyer_join_condition']['phone'] && !$user_info['phone_status']) {
            $this->json_function(0, '请先进行手机认证');
            exit();
        }

        /* 邮箱认证 */
        if ($config['buyer_join_condition']['email'] && !$user_info['email_status']) {
            $this->json_function(0, '请先进行邮箱认证');
            exit();
        }
        // 统计实名认证
        $identity_count = model('member_attesta')->where(array('userid' => $user_info['userid'], 'type' => 'identity'))->count();
        /* 实名认证 */
        if ($config['buyer_join_condition']['realname'] && $identity_count != 1) {
            $this->json_function(0, '请先进行实名认证');
            exit();
        }

        /* 绑定淘宝账号 */
        $tb_count = model('member_bind')->where(array('userid' => $user_info['userid'], 'status' => array('NEQ', 2)))->count();
        if ($config['buyer_join_condition']['bind_taobao'] && $tb_count < 1) {
            $this->json_function(0, '请先绑定淘宝账号');
            exit();
        }

        /* 是否绑定支付宝 */
        $account = model('member_attesta')->where(array('userid' => $user_info['userid'], 'type' => 'alipay'))->count();
        if ($config['buyer_join_condition']['bind_alipay'] && $account != 1) {
            $this->json_function(0, '请先绑定支付宝账号');
            exit();
        }


        /*---- 检测商品状态 ----*/
        /* 上架状态 */
        if ($factory->product_info['status'] != 1) {
            $this->json_function(0, '该商品尚未上架');
            exit();
        }
        if ($factory->product_info['start_time'] > NOW_TIME) {
            $this->json_function(0, '该活动尚未开始');
            exit();

        }
        if ($factory->product_info['end_time'] < NOW_TIME) {
            $this->json_function(0, '该活动已经结束');
            exit();
        }
        /* 检测商品库存 */
        if ($factory->product_info['goods_number'] - $factory->product_info['already_num'] < 1) {
            $this->json_function(0, '该商品已售罄');
            exit();

        }
        /*---- 检测活动设置 ----*/
        //1.是否有抢购未下单的
        $o_map = array();
        $o_map['buyer_id'] = $user_info['userid'];
        $o_map['goods_id'] = $factory->product_info['id'];
        $o_map['status'] = array('NEQ', 0);
        $wait_fill_num = model('order')->where($o_map)->count();
        /*购物返利限定抢购次数*/
        $count = C_READ('buyer_good_buy_times', 'rebate');
        if ($wait_fill_num >= $count) {
            $this->json_function(0, '您已抢购了该订单' . $count . '次，请勿重复抢购。');
            exit();
        }
        // 若设置为可以抢购多次，若有订单未完成的则不允许下单
        if ($count > 1) {
            $o_map['status'] = array('BETWEEN', array('1', '6'));
            $is_over = model('order')->where($o_map)->count();
            if ($is_over > 0) {
                $this->json_function(0, '当前还有未完成的订单，请订单完成后再继续下单');
                exit();

            }
        }
        //2.每天参与总次数
        if ($config['buyer_day_buy_times'] > 0) {
            $o_map = array();
            $o_map['buyer_id'] = $user_info['userid'];
            $o_map['_string'] = "DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
            $buyer_day_buy_times = model('order')->where($o_map)->count();
            if ($buyer_day_buy_times >= $config['buyer_day_buy_times']) {
                $this->json_function(0, '超出每日活动次数限制');
                exit();
            }
        }
        //3.商品抢购时间间隔
        if ($config['buyer_buy_time_limit'] > 0) {
            $o_map = array();
            $o_map['goods_id'] = $factory->product_info['id'];
            $last_create_time = model('order')->where($o_map)->getField('create_time');
            if ($last_create_time > 0 && $last_create_time < $config['buyer_buy_time_limit']) {
                $this->json_function(0, '同时间段内抢购人数过多');
                exit();
            }
        }
        //4.商品会员抢购次数
        //@todu:需求无法确认是只记录已成功的还是所有的抢购都计算
        if ($config['buyer_good_buy_times'] > 0) {
            $o_map = array();
            $o_map['buyer_id'] = $user_info['userid'];
            $o_map['goods_id'] = $factory->product_info['id'];
            $o_map['status'] = 7;
            $buyer_good_buy_times = model('order')->where($o_map)->count();
            if ($buyer_good_buy_times >= $config['buyer_good_buy_times']) {
                $this->json_function(0, '您参与该商品抢购次数过多');
                exit();
            }
        }
        return TRUE;
    }


    /*试客晒单*/
    public function shai_report()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info) {
                $this->json_function(0, '访问有误！请稍后再试');
                exit();
            }

            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            if (!$info['order_id']) {
                $this->json_function(0, '请输入订单号ID！');
                exit();
            }

            $exists = model('report')->where(array('order_id' => $info['order_id']))->find();
            if ($exists) {
                $this->json_function(0, '请勿重复提交');
                exit();

            }

            if (!$info['report_imgs']) {
                $this->json_function(0, '请上传图片！');
                exit();
            }

            if (!$info['content']) {
                $this->json_function(0, '请输入评价内容！');
                exit();
            }

            // 获取订单信息
            $factory = new \Product\Factory\order($info['order_id']);
            if (!$factory) {
                $this->json_function(0, '信息不完善');
                exit();

            }
            $order_info = $factory->order_info;
            $data = array();
            $data['userid'] = $order_info['buyer_id'];
            $data['goods_id'] = $order_info['goods_id'];
            $data['order_id'] = $order_info['id'];
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
            $data['reporttime'] = NOW_TIME;
            $data['report_imgs'] = $info['report_imgs'];
            $data['content'] = $info['content'];
            if (C('buyer_artificial_check') == 0) { //  获取后台开关
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }
            $result = model('report')->add($data);
            if (!$result) {
                // 发布晒单失败后删除之前上传的图片
                if ($info['report_imgs']) unlink($info['report_imgs']);
                $this->json_function(0, '晒单失败，请稍后再试！');
                exit();

            }
            $factory->write_log('发布晒单成功', $order_info['buyer_id']);
            $this->json_function(1, '晒单成功！');
        } else {
            $this->json_function(0, '请勿非法请求！');
            exit();
        }
    }


    public function appeal_order()
    {
        if (IS_POST) {
            $data = I('post.');
            if (!$data['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $data['userid'], 'target' => $data['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $factory = new \Product\Factory\order($data['order_id']);

            if ($factory->order_info['status'] != 4) {
                $this->json_function(0, '请勿非法操作');
                exit();
            }

            // 写入操作日志、并设置该订单状态为申述中(6)
            if ($factory->order_info['buyer_id'] != $data['userid']) {
                $this->json_function(0, '请勿非法访问！');
                exit();
            }

            if ($data['appeal_type'] < 1) {
                $this->json_function(0, '请选择申诉类型');
                exit();
            }
            if ($data['buyer_cause'] == null) {
                $this->json_function(0, '申诉理由不能为空');
                exit();
            }
            if ($data['buyer_phone'] == null) {
                $this->json_function(0, '手机号码不能为空');
                exit();
            }
            if ($data['buyer_qq'] == null) {
                $this->json_function(0, 'QQ号码不能为空');
                exit();
            }

            $conditions = array();
            $conditions[] = $data['buyer_imgs_url_img1'];
            $conditions[] = $data['buyer_imgs_url_img2'];
            $conditions[] = $data['buyer_imgs_url_img3'];


            /* if (!$data['buyer_imgs_url']) {
                $this->json_function(0,'请上传申诉的图片');exit();
            }  */
            $data["order_sn"] = $factory->order_info["order_sn"];
            $data["order_id"] = $data["order_id"];
            $data["goods_id"] = $factory->order_info["goods_id"];
            $data["seller_id"] = $factory->order_info["seller_id"];

            $data["appeal_status"] = '0';
            $data["buyer_id"] = $data['userid'];
            $data["buyer_time"] = NOW_TIME;
            $data['buyer_imgs_url'] = array2string($conditions);
            $result = model('appeal')->add($data);
            if (!$result) {
                $this->json_function(0, "申诉失败");
                exit();
            }

            $result = $factory->set_status(6, '会员(本人)申诉已成功');
            if (!$result) {
                $this->json_function(0, '申诉提交失败，请稍后再试！');
                exit();
            }
            $this->json_function(1, '申诉成功');
            // runhook('app_order_appeal',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title']));
            // $this->success('申诉成功', U("Order/manage",array('state'=>'6','mod'=>$factory->product_info['mod'])),1);


        }
    }


    /*是否参与过该活动*/
    public function is_join_borke()
    {
        if (IS_POST) {
            $data = I('post.');
            if (!$data['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $data['userid'], 'target' => $data['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $task = model('task_records')->where(array('tid' => $data['taskid'], 'userid' => $data['userid']))->count();
            if ($task > 0) {
                $status = 1;
            } else {
                $status = 0;
            }

            $this->json_function(1, '审核通过', $status);
        }
    }

    /*获取日赚任务配置*/
    public function get_borke_config()
    {
        if (IS_GET) {
            $activity_type = 'task';
            $setting = model('activity_set')->where(array('activity_type' => $activity_type))->getField('key,value');
            // 公用
            if ($setting) {
                $this->json_function(1, '获取信息成功', $setting);
            }
        }
    }


    /*日赚任务提交*/
    public function answer_task()
    {
        if (IS_POST) {
            $content = I('content');
            $userid = I('userid');
            $id = (int)I('id');
            $random = I('random');

            if ($id < 1) {
                $this->json_function(0, '参数错误');
                exit();
            }

            if (!$userid) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $userid, 'target' => $random))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $userinfo = model('member')->find($userid);
            if (!$userinfo) {
                $this->json_function(0, '您还没有登录，请先登录');
                exit();
            }
            //判断用户是否完成过
            $modelid = model('member')->getFieldByUserid($userinfo['userid'], 'modelid');
            if ($modelid != 1) {
                $this->json_function(0, '只限于买家参与');
                exit();
            }
            //查看该用户是否绑定手机、淘宝账号
            if (DEFAULT_THEME != 'wap') { //手机没有绑定淘宝号
                $taobao = model('member_bind')->where(array('userid' => $userinfo['userid']))->find();

                if (!$taobao) {
                    $this->json_function(0, '您还没有绑定淘宝');
                    exit();
                }
            }

            $phone = model('member')->getFieldByUserid($userinfo['userid'], 'phone_status');
            if ($phone != 1) {
                $this->json_function(0, '您还没有绑定手机');
                exit();
            }
            $r = model('task_records')->where(array('tid' => $id, 'userid' => $userinfo['userid'], 'status' => 1))->find();

            if ($r) {
                $this->json_function(0, '您已参数与过该任务');
                exit();
            }
            //查出该任务的答案
            $info = model('task_day')->getById($id);
            //判断该任务是否存在
            if ($info['goods_number'] == $info['already_num']) {
                $this->json_function(0, '该任务已结束');
                exit();
            }
            $answer = $info['answer'];
            if ($answer == $content) {

                //给用户增加佣金
                $sign = '1-3-' . $userinfo['userid'] . '-' . $info['id'] . '-' . $info['goods_price'];
                $rs = model('member_finance_log')->where(array('only' => $sign))->find();
                if (!$rs) {
                    //加入任务记录表
                    $sqlMap = array();
                    $sqlMap['tid'] = $id;
                    $sqlMap['userid'] = $userinfo['userid'];
                    $sqlMap['start_time'] = NOW_TIME;
                    $sqlMap['status'] = 1;
                    $sqlMap['answer'] = $content;
                    $sqlMap['clientip'] = get_client_ip();
                    $sqlMap['price'] = $info['goods_price'];
                    model('task_records')->add($sqlMap);
                    action_finance_log($userinfo['userid'], $info['goods_price'], 'money', $info['title'] . '任务完成，获得佣金', $sign, array('goods_id' => $info['id']));
                } else {
                    $this->json_function(0, '会员增加佣金，重复操作');
                    exit();
                }
                $sign1 = '1-3-' . $info['company_id'] . '-' . $info['id'] . '-' . $info['goods_price'] . '-' . NOW_TIME;
                $rs1 = model('member_finance_log')->where(array('only' => $sign1))->find();
                if (!$rs1) {
                    action_finance_log($info['company_id'], -$info['goods_price'], 'deposit', $userinfo['userid'] . '任务完成，扣除佣金', $sign1, array('goods_id' => $info['id']));
                } else {
                    $this->json_function(0, '商家扣除佣金，重复操作');
                    exit();
                }
                //商家减去佣金（冻结中保证金减去）
                model('member_merchant')->where(array('userid' => $info['company_id']))->setDec('frozen_deposit', $info['goods_price']);
                //任务完成数量 + 1
                model('task_day')->where(array('id' => $id))->setInc('already_num', 1);
                model('task_records')->where(array('tid' => $id, 'userid' => $userinfo['userid']))->save(array('status' => 1));
                $this->json_function(1, '回答正确');

            } else {
                $this->json_function(0, '答案错误,请仔细寻找');
                exit();
            }

        }

    }


    /*获取订单日志*/
    public function order_log()
    {
        if (IS_POST) {
            $data = I('post.');
            if (!$data['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $data['userid'], 'target' => $data['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }


            if (!$data['order_id']) {
                $this->json_function(0, '请输入订单号ID！');
                exit();
            }
            $sqlMap = array();

            $modelid = model('member')->where(array('userid' => $data['userid']))->getFieldByUserid($data['userid'], 'modelid');
            if ($modelid == 1) {
                $sqlMap['buyer_id'] = $data['userid'];
            } else {
                $sqlMap['seller_id'] = $data['userid'];

            }
            $sqlMap['order_id'] = $data['order_id'];
            // $count = model('order_log')->where($sqlMap)->count();
            $order_list = model('order_log')->where($sqlMap)->select();
            //  echo model('order_log')->getLastSql();
            if ($order_list) {
                $this->json_function(1, '获取信息成功', $order_list);
            } else {
                $this->json_function(0, '没有获取到相关信息');
                exit();

            }


        }
    }

    public function order_log_one()
    {
        if (IS_POST) {
            $data = I('post.');
            if (!$data['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $data['userid'], 'target' => $data['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }


            if (!$data['order_id']) {
                $this->json_function(0, '请输入订单号ID！');
                exit();
            }
            $sqlMap = array();

            $modelid = model('member')->where(array('userid' => $data['userid']))->getFieldByUserid($data['userid'], 'modelid');
            if ($modelid == 1) {
                $sqlMap['buyer_id'] = $data['userid'];
            } else {
                $sqlMap['seller_id'] = $data['userid'];

            }
            $sqlMap['order_id'] = $data['order_id'];
            // $count = model('order_log')->where($sqlMap)->count();
            $order_list = model('order_log')->where($sqlMap)->order('id DESC')->limit(1)->find();
            //  echo model('order_log')->getLastSql();
            if ($order_list) {

                $this->json_function(1, '获取信息成功', $order_list);
            } else {
                $this->json_function(0, '没有获取到相关信息');
                exit();

            }
        }
    }


    public function order_info_one()
    {
        if (IS_POST) {
            $data = I('post.');
            if (!$data['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $data['userid'], 'target' => $data['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }


            if (!$data['order_id']) {
                $this->json_function(0, '请输入订单号ID！');
                exit();
            }
            $sqlMap = array();
            $sqlMap['buyer_id'] = $data['userid'];
            $sqlMap['id'] = $data['order_id'];
            // $count = model('order_log')->where($sqlMap)->count();
            $order_info = model('order')->where($sqlMap)->find();

            $order_list = model('order_log')->where($sqlmap)->order('id DESC')->limit(1)->find();

            $order_info['cause'] = $order_list['cause'];

            //获得绑定淘宝id
            if ($order_info['bind_id'] > 0) {
                $data['id'] = $order_info['bind_id'];
                $order_info['taobao'] = model('member_bind')->where($data)->getField('account');
            }

            if ($order_info['act_mod'] == 'trial') {
                $order_info['trial_report'] = model('trial_report')->where(array('order_id' => $data['order_id']))->find();
            }
            if ($order_info) {
                $this->json_function(1, '获取信息成功', $order_info);
            } else {
                $this->json_function(0, '没有获取到相关信息');
                exit();

            }
        }

    }


    public function get_user_borke()
    {
        if (IS_POST) {
            $data = I('post.');
            if (!$data['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $data['userid'], 'target' => $data['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }


            $lists = model('task_records')->where(array('userid' => $data['userid']))->order('id desc')->select();
            foreach ($lists as $k => $v) {
                $borke_info = model('task_day')->find($v['tid']);
                $lists[$k]['title'] = $borke_info['title'];
                $lists[$k]['goods_price'] = $borke_info['goods_price'];

            }

            $this->json_function(1, '获取信息成功', $lists);
        }

    }

    /*    public function test(){
        $conditions = array();
        $data['buyer_imgs_url_img1'] = 'images/1.jpg';
        $data['buyer_imgs_url_img2'] = 'images/2.jpg';
        $data['buyer_imgs_url_img3'] = 'images/3.jpg';
        $conditions[] = $data['buyer_imgs_url_img1'];
        $conditions[] = $data['buyer_imgs_url_img2'];
        $conditions[] = $data['buyer_imgs_url_img3'];
        var_dump($conditions);
    }*/


//     public function upload_img_demo(){
//        if(!empty($_FILES)){
//            $upload = new \Think\Upload();// 实例化上传类
//            $upload->config->maxSize  =     3145728 ;
//            $upload->exts     =     array('jpg', 'gif', 'png');
//            $date=date('Y',time());
//            $m_d=date('md',time());
//            $upload->rootPath = './uploadfile/app/'.$date.'/'.$m_d;
//            if(!file_exists($upload->rootPath)){//不存在，则创建
//               mkdir($upload->rootPath, 0777);
//            }
//            $upload->savePath = '';
//            $upload->replace  = TRUE;
//            $upload->saveName = NOW_TIME.random(5,1);
//            $upload->autoSub = FALSE;
//            $upload->saveExt  = 'jpg';
//            $result = $upload->upload($_FILES);
//            $name = __ROOT__.'/uploadfile/app/'.$result['Filedata']['savename'];
//            if($result){
//                exit($name);
//            }else{
//                exit('error');
//            }
//        }
//    }


    public function isrecommend()
    {
        $param = I('param.');
        extract($param);
        $sqlmap = array();
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'desc';
        } else {
            if ($orderby == 'id' || $orderby == 'start_time' || $orderby == 'hits') {
                $orderby = 'p.' . $orderby;
            } else {
                $orderby = 't.' . $orderby;
            }
        }
        $mod = $mod ? $mod : 'trial';
        $sqlmap['p.status'] = 1;
        $sqlmap['p.start_time'] = array("LT", NOW_TIME);
        $sqlmap['p.end_time'] = array("GT", NOW_TIME);
        $sqlmap['p.mod'] = $mod;
        $sqlmap['p.isrecommend'] = 1;

        $ids = model('product')->alias('p')->join(C('DB_PREFIX') . 'product_' . $mod . ' AS t ON p.id = t.id')->where($sqlmap)->field('p.id')->order($orderby . ' ' . $orderway)->select();
        // echo model('product')->getLastSql();

        if (!$ids) {
            $this->json_function(0, '没有找到商品');
            exit();
        }
        $lists = array();
        foreach ($ids as $k => $v) {
            $factory = new \Product\Factory\product($v['id']);
            $rs = $factory->product_info;
            $r['mod_name'] = model('activity_set')->where(array('key' => $rs['mod'] . '_name'))->getField('value');
            $r['price_name'] = activitiy_price_name($rs['mod']);
            $r['mod_price'] = price($rs['id']);
            $r['number'] = $rs['goods_number'] - buyer_count_by_gid($rs['id']);//剩余份数
            $r['start_time'] = $rs['start_time'];
            $r['title'] = $rs['title'];
            $r['get_trial'] = get_trial_by_gid($rs['id']); //已申请人数
            $r['catid'] = $rs['catid'];
            $r['id'] = $rs['id'];
            //$r['thumb'] = img2thumb($rs['thumb'],'s',1);
            $r['thumb'] = $rs['thumb'];

            if ($rs['mod'] == 'rebate') {
                $r['price'] = $rs['goods_price'] * $rs['goods_discount'];
            }
            $r['goods_price'] = $rs['goods_price'];
            $r['goods_bonus'] = $rs['goods_bonus'];
            $r['hits'] = $rs['hits'];
            $r['goods_tryproduct'] = $rs['goods_tryproduct'];
            if ($rs['mod'] == 'trial') {

                if ($rs['goods_bonus'] > '0' || $rs['goods_bonus'] != '0.00') {
                    /*3:红包类型*/
                    $r['protype'] = 3;
                } elseif ($rs['goods_tryproduct'] > 0 || $rs['goods_tryproduct'] > '0' && $rs['goods_tryproduct'] != '') {
                    /*w:拍a发b*/
                    $r['protype'] = 2;
                } else {
                    /*1:实物专区*/
                    $r['protype'] = 1;

                }

            }

            $lists[$k] = $r;
        }

        $this->json_function(1, '获取信息成功', $lists);

    }

    /*积分商城列表页*/
    public function shop_lists()
    {
        $param = I('param.');
        extract($param);
        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'desc';
        }
        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        $map = array();
        $map['end_time'] = array('GT', NOW_TIME);
        $count = model('shop')->where($map)->count();
        $lists = model('shop')->where($map)->page($page, $num)->order($orderby . ' ' . $orderway)->select();
        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['count'] = $count;
        $result['lists'] = $lists ? $lists : '';
        $result['pages'] = $pages;
        echo json_encode($result);

    }

    public function shop_show()
    {
        $id = I('id', 0, 'intval');
        if ($id < 1) {
            $this->json_function(0, '参数错误');
            exit();
        }
        $shop = model('shop')->find($id);
        $shop['desc'] = htmlspecialchars_decode(stripslashes($shop['desc']));
        $this->json_function(1, '获取信息成功', $shop);

    }


    public function exchange()
    {
        if (IS_POST) {

            $data = I('post.');
            if (!$data['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $data['userid'], 'target' => $data['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            extract($data);

            $shop_id = (int)$id;
            $spec = I('spec', '', 'remove_xss');
            if ($shop_id < 1) {
                $this->json_function(0, '参数错误');
                exit();
            }

            $uinfo = model('member')->find($userid);

            if ($uinfo['modelid'] != 1) {
                $this->json_function(0, '积分兑换目前只对普通用户开放');
                exit();
            }
            // 商品信息
            $rs = model('shop')->find($shop_id);
            $rs['spec'] = explode(",", $rs['spec']);
            if (!$rs) {
                $this->json_function(0, '商品不存在');
                exit();
            }
            if ($rs['end_time'] < NOW_TIME) {
                $this->json_function(0, '本活动已结束');
                exit();
            }
            if ($rs['total_num'] <= $rs['sale_num']) {
                $this->json_function(0, '本商品已兑换完毕，请尝试兑换其它商品');
                exit();
            }

            if ($rs['spec'] && !in_array($spec, $rs['spec'])) {
                $this->json_function(0, '商品属性不正确');
                exit();
            }

            // 积分信息
            if ($uinfo['point'] < $rs['point']) {
                $this->json_function(0, '您的积分不足');
                exit();
            }
            // 是否重复兑换
            $sqlmap = array();
            $sqlmap['userid'] = $uinfo['userid'];
            $sqlmap['shop_id'] = $rs['id'];
            if (model('shop_log')->where($sqlmap)->count()) {
                $this->json_function(0, '您已兑换本商品，请勿重复申请');
                exit();
            }

            /*------ 执行兑换 -------*/
            // 写入日志
            $log = array(
                'userid' => $uinfo['userid'],
                'shop_id' => $rs['id'],
                'spec' => $spec,
                'point' => $rs['point'],
                'apply_time' => NOW_TIME,
                'ip' => get_client_ip(),
                'status' => 0
            );
            $result = model('shop_log')->update($log);
            if ($result) {
                // 扣除积分
                $sign = '3-12-' . $uinfo['userid'] . '-' . $shop_id . '-' . $rs['point'] . '-' . date('Y-m-d H');
                $rss = model('member_finance_log')->where(array('only' => $sign))->find();
                if (!$rss) {
                    action_finance_log($uinfo['userid'], '-' . $rs['point'], 'point', '积分兑换商品', $sign);
                    model('shop')->where(array('id' => $shop_id))->setInc('sale_num');
                    $this->json_function(1, '商品兑换成功');
                } else {
                    $this->json_function(0, '商品兑换失败，一个小时只限兑换一次');
                    exit();
                }
            } else {
                $this->json_function(0, '商品兑换失败');
                exit();
            }
        }


    }

    public function is_join_exchange()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            if (!$info['id']) {
                $this->json_function(0, '参数错误！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            $count = model('shop_log')->where(array('shop_id' => $info['id'], 'userid' => $info['userid']))->count();
            if ($count > 0) {
                $status = 1;
            } else {
                $status = 0;
            }

            $this->json_function(1, '获取信息成功', $status);


        }
    }

    /*兑换记录*/
    public function exchange_log()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }


            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            extract($info);

            if ($orderby == '' || $orderway == '') {
                $orderby = 'id';
                $orderway = 'desc';
            }
            $page = max(1, (int)$page);
            $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
            $sqlmap = array();
            $status = $status ? $status : 1;
            $sqlmap['status'] = array('EQ', $status);
            $count = model('shop_log')->where($sqlmap)->count();
            $lists = model('shop_log')->where($sqlmap)->page($page, $num)->select();
            $pages = page($count, $num);
            $result = array();
            $result['status'] = 1;
            $result['count'] = $count;
            $result['lists'] = $lists ? $lists : '';
            $result['pages'] = $pages;
            echo json_encode($result);


        }

    }

    /*推荐好友记录*/
    public function recommend_friend()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }


            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            extract($info);

            if ($orderby == '' || $orderway == '') {
                $orderby = 'id';
                $orderway = 'desc';
            }
            $page = max(1, (int)$page);
            $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
            $sqlMap = array();
            $sqlMap['userid'] = $userid;
            $sqlMap['recommend_status'] = 1;
            $count = model('member_finance_log')->where($sqlMap)->count();
            $reword_list = model('member_finance_log')->where($sqlMap)->page($page, $num)->order('id DESC')->select();
            $pages = page($count, $num);
            $result = array();
            $result['status'] = 1;
            $result['count'] = $count;
            $result['lists'] = $reword_list ? $reword_list : '';
            $result['pages'] = $pages;
            echo json_encode($result);

        }


    }

    /*获取获得邀请好友的总金额
*/
    public function recommend_total_moeny()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            extract($info);
            $map = array();
            $map['userid'] = array('EQ', $userid);
            $map['type'] = array('EQ', 'money');
            $map['recommend_status'] = array('EQ', '1');
            $reward = model('member_finance_log')->field("SUM(`num`) AS num")->where($map)->order('num DESC')->select();

            /* 获取已推荐的好友数量 */
            $reward['us_num'] = model('member')->where(array('agent_id' => $userid))->count();

            $this->json_function(1, '获取信息成功', $reward);

        }

    }

    /*获取邀请好友配置*/
    public function get_friends_config()
    {
        $invite = getcache('friend_setting', 'member');
        $this->json_function(1, '获取信息成功', $invite);
    }

    /*排行榜*/
    public function order_by_friend()
    {
        $by_friend = model('member_finance_log')->where(array('type' => 'money', 'recommend_status' => 1))->field('id,userid,sum(num) AS total')->group('userid')->order('total DESC')->limit(10)->select();
        foreach ($by_friend as $key => $value) {
            $by_friend[$key]['nickname'] = nickname($value['userid']);

        }
        $this->json_function(1, '获取信息成功', $by_friend);

    }

    public function get_reward_config()
    {
        $lists = model('lottery_draw_set')->select();
        $lottery_draw_set2 = model('lottery_draw_set2')->find();
        $result = array();
        $result['status'] = 1;
        $result['lists'] = $lists ? $lists : '';
        $result['data'] = $lottery_draw_set2;
        echo json_encode($result);
    }

    public function reward_list()
    {
        $lists = model('lottery_draw_list')->limit(10)->order('lottery_draw_list_id DESC')->select();
        $this->json_function(1, '获取信息成功', $lists);
    }

    public function lottery()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            $lottery = A('Task/LotteryDraw');
            $reward = $lottery->LotteryDrawIndexFunAjax($info['userid']);
            $this->json_function(1, '获取成功', $reward);

        }

    }

    //获取当前会员的中奖记录
    public function user_reward_list()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $data[userid] = $info['userid'];
            $lists = model('lottery_draw_list')->where($data)->limit(10)->order('lottery_draw_list_id DESC')->select();
            $this->json_function(1, '获取信息成功', $lists);

        }

    }

    //获取当前会员剩余抽奖次数
    public function user_reward_cishu()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            //统计使用次数
            $today = strtotime(date('Y-m-d 00:00:00'));
            $data['userid'] = $info['userid'];
            $data['time'] = array('egt', $today);
            $lists = model('lottery_draw_list')->where($data)->limit(10)->order('lottery_draw_list_id DESC')->count();
            $lottery_draw_set2 = model('lottery_draw_set2')->find();
            $lists = $lottery_draw_set2['lottery_draw_num_after_share'] - $lists;
            $this->json_function(1, '获取信息成功', $lists);

        }

    }


    //获取当前会员的积分兑换记录
    public function get_shop_log()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $data['userid'] = $info['userid'];
            $data['status'] = $info['status'];
            $lists = model('shop_log')->where($data)->limit(10)->select();
            foreach ($lists as $k => $v) {

                $lists1 = model('shop')->where(array('id' => $v['shop_id']))->find();
                $lists[$k]['title'] = $lists1["title"];
                $lists[$k]['img'] = $lists1["images"];

            }

            $this->json_function(1, '获取信息成功', $lists);

        }

    }


    //修改会员昵称
    public function set_user_nickname()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $data['userid'] = $info['userid'];
            $lists = model('member')->where($data)->setField('nickname', $info['nickname']);


            $avatar = $info['user_avatar'];

            F('data', $info, CONF_PATH);

            if ($avatar) {

                //需移入的文件夹目录
                $suid = sprintf("%09d", $info['userid']);
                $dir1 = substr($suid, 0, 3);
                $dir2 = substr($suid, 3, 2);
                $dir3 = substr($suid, 5, 2);

                $root_url = $_SERVER['DOCUMENT_ROOT'];

                $rootDir = $root_url . '/uploadfile/avatar/';
                $userDir = $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
                //头像新文件名
                $filename = $rootDir . $userDir . $info['userid'] . '_avatar.jpg';

                if (!is_dir($rootDir . $userDir)) {
                    mkdir($rootDir . $userDir,0777,true);
                    chmod($rootDir . $userDir, 0777);
                }
                $upload = copy($root_url . $avatar, $filename);

                if ($upload) {
                    $this->json_function(1, '头像修改成功', $lists);
                }
                else{
                    $this->json_function(1, $filename, $lists);
                }


            }
            else{
                $this->json_function(1, '昵称修改成功', $lists);
            }



        }

    }


    public function get_commission_config()
    {
        $activity_type = 'commission';
        $setting = model('activity_set')->where(array('activity_type' => $activity_type))->getField('key,value');
        // 公用
        if ($setting['single_mode']) $setting['single_mode'] = string2array($setting['single_mode']);
        if ($setting['seller_join_condition']) $setting['seller_join_condition'] = string2array($setting['seller_join_condition']);
        if ($setting['buyer_join_condition']) $setting['buyer_join_condition'] = string2array($setting['buyer_join_condition']);
        if ($setting) {
            $this->json_function(1, '获取信息成功', $setting);

        }

    }

    public function complete_order()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }

            $sqlmap = array();
            $sqlmap['buyer_id'] = $info['userid'];
            $sqlmap['act_mod'] = 'trial';
            $sqlmap['status'] = 7;
            $count = model('order')->where($sqlmap)->count();
            $this->json_function(1, '获取成功', $count);

        }

    }

    /*闪电试用参与条件(试客)（1为需要 0为不需要）*/
    public function check_commission_authority()
    {
        $param = I('param.');
        extract($param);
        $lists = array();
        //$conditions = C_READ('buyer_good_buy_times','trial');
        $conditions = string2array(model('activity_set')->where(array('key' => 'buyer_join_condition', 'activity_type' => $mod))->getField('value'));
        if ((int)$conditions['information'] == 6) {
            $lists['information'] = 1;
        } else {
            $lists['information'] = 0;
        }

        if ((int)$conditions['phone'] == 1) {
            $lists['phone'] = 1;
        } else {
            $lists['phone'] = 0;

        }

        if ((int)$conditions['email'] == 2) {
            $lists['email'] = 1;
        } else {
            $lists['email'] = 0;

        }

        if ((int)$conditions['realname'] == 3) {
            $lists['realname'] = 1;
        } else {
            $lists['realname'] = 0;

        }

        if ((int)$conditions['bind_taobao'] == 4) {
            $lists['bind_taobao'] = 1;
        } else {
            $lists['bind_taobao'] = 0;

        }

        if ((int)$conditions['bind_alipay'] == 5) {
            $lists['bind_alipay'] = 1;
        } else {
            $lists['bind_alipay'] = 0;

        }

        if ((int)$conditions['num_trial'] == 7) {
            $lists['order_num'] = 1;
        } else {
            $lists['order_num'] = 0;

        }

        $result = array();
        $result['lists'] = $lists;
        echo json_decode($result);

    }

    /*申请闪电试用*/
    public function commission_pay_submit()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }
            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            extract($info);
            if ($goods_id < 1) {
                $this->json_function(0, '参数错误');
                exit();
            }
            $Factory = new \Product\Factory\product($goods_id);

            if (!$Factory) {
                $this->json_function(0, '商品信息错误！');
                exit();
            }

            if ($Factory->product_info['mod'] != 'commission') {
                $this->json_function(0, '商品活动类型不存在');
                exit();
            }
            // 读取后台活动设置：参与条件
            $bind_set = string2array(C_READ('buyer_join_condition', 'commission'));
            if ($Factory->product_info['mod'] == 'commission' && $bind_set['bind_taobao'] == 4) {
                $bind_taobao = (int)trim($taobao);
                if ($bind_taobao < 1) {
                    $this->json_function(0, '请选择您要购买的淘宝帐号');
                    exit();
                }
                $result = $this->apply_commission_order($goods_id, $userid, $bind_taobao);
            } else {
                $result = $this->apply_commission_order($goods_id, $userid, '');
            }

            if (!$result) {
                $this->json_function(0, $result);
            } else {
                $this->json_function(1, '抢购成功', $result);
            }

        }

    }


    /*抢购闪电试用商品  1.增加签名检测 2.增加淘宝账号*/
    public function apply_commission_order($goods_id, $userid, $bind_id = 0)
    {

        $user_info = model('member')->find($userid);
        $factory = new \Product\Factory\product($goods_id);

        if (!$user_info) {
            $this->json_function(0, '试客数据不存在');
        }
        $ischk = $this->commission_pay_check($goods_id, $userid);

        if ($ischk === TRUE) {
            $info = array();
            $info['buyer_id'] = $user_info['userid'];
            $info['seller_id'] = $factory->product_info['company_id'];
            $info['goods_id'] = $factory->product_info['id'];
            $info['act_mod'] = $factory->product_info['mod'];
            $info['source'] = 1;
            $info['trade_sn'] = date('YmdHis') . random(6, 1);
            $info['inputtime'] = $info['create_time'] = NOW_TIME;
            $info['status'] = 2;
            $info['bind_id'] = $bind_id;
            $order_id = model('order')->update($info);
            if ($order_id) {
                $Factory = new \Product\Factory\order($order_id);
                $Factory->write_log('用户抢购资格', $userid);
                model('product')->where(array('id' => $factory->product_info['id']))->setInc('already_num');
                return $order_id;
            } else {
                $this->json_function(0, '用户抢购失败');
                exit();
            }
        } else {
            return FALSE;
        }
    }


    /**
     * 抢购闪电试用检测
     */
    public function commission_pay_check($goods_id, $userid)
    {
        $user_info = model('member')->find($userid);
        $factory = new \Product\Factory\product($goods_id);
        $config = $factory->getConfig();
        /*---- 限制买家参与 ----*/
        if (empty($user_info['userid'])) {
            $this->json_function(0, '尚未登录');
            exit();
        }
        if ($user_info['modelid'] != 1) {
            $this->json_function(0, '暂时只限买家参与');
            exit();
        }
        /*---- 判断认证设置 ----*/
        /* 手机认证 */
        if ($config['buyer_join_condition']['phone'] && !$user_info['phone_status']) {
            $this->json_function(0, '请先进行手机认证');
            exit();
        }

        /* 邮箱认证 */
        if ($config['buyer_join_condition']['email'] && !$user_info['email_status']) {
            $this->json_function(0, '请先进行邮箱认证');
            exit();
        }
        // 统计实名认证
        $identity_count = model('member_attesta')->where(array('userid' => $user_info['userid'], 'type' => 'identity'))->count();
        /* 实名认证 */
        if ($config['buyer_join_condition']['realname'] && $identity_count != 1) {
            $this->json_function(0, '请先进行实名认证');
            exit();
        }

        /* 绑定淘宝账号 */
        $tb_count = model('member_bind')->where(array('userid' => $user_info['userid'], 'status' => array('NEQ', 2)))->count();
        if ($config['buyer_join_condition']['bind_taobao'] && $tb_count < 1) {
            $this->json_function(0, '请先绑定淘宝账号');
            exit();
        }

        /* 是否绑定支付宝 */
        $account = model('member_attesta')->where(array('userid' => $user_info['userid'], 'type' => 'alipay'))->count();
        if ($config['buyer_join_condition']['bind_alipay'] && $account != 1) {
            $this->json_function(0, '请先绑定支付宝账号');
            exit();
        }


        /*---- 检测商品状态 ----*/
        /* 上架状态 */
        if ($factory->product_info['status'] != 1) {
            $this->json_function(0, '该商品尚未上架');
            exit();
        }
        if ($factory->product_info['start_time'] > NOW_TIME) {
            $this->json_function(0, '该活动尚未开始');
            exit();

        }
        if ($factory->product_info['end_time'] < NOW_TIME) {
            $this->json_function(0, '该活动已经结束');
            exit();
        }
        /* 检测商品库存 */
        if ($factory->product_info['goods_number'] - $factory->product_info['already_num'] < 1) {
            $this->json_function(0, '该商品已售罄');
            exit();

        }
        /*---- 检测活动设置 ----*/
        if ($factory->product_info['allow_groupid'] != '') {
            $allow_groupid = string2array($factory->product_info['allow_groupid']);
            $member_name = model('member_group')->where(array('groupid' => $user_info['groupid']))->getField('name');
            if (!in_array($user_info['groupid'], $allow_groupid)) {
                $this->json_function(0, '您目前是' . $member_name . '没有权限参与该活动');
                exit();
            }

        }


        $order_count = model('order')->where(array('buyer_id' => $user_info['userid'], 'goods_id' => $factory->product_info['id'], 'act_mod' => 'commission'))->count();

        if ($order_count > 0) {

            $this->json_function(0, '您已参与过该活动');
            exit();
        }

        //获得用户已完成订单数量
        $tiral_num = model('order')->where(array('buyer_id' => $user_info['userid'], 'act_mod' => 'trial', 'status' => 7))->count();

        if ((int)$trial_num - (int)$config['buyer_join_condition']['num_trial_art'] < 0) {
            $this->json_function(0, '您还要完成' . $config['buyer_join_condition']['num_trial_art'] - $trial_num . '笔订单才能参与该活动');
            exit();
        }


        return TRUE;
    }


    public function sign()
    {
        if (IS_POST) {
            $info = I('post.');
            if (!$info['userid']) {
                $this->json_function(0, '请先登录！');
                exit();
            }

            $target_info = model('member_app')->where(array('userid' => $info['userid'], 'target' => $info['random']))->find();
            if (!$target_info) {
                $this->json_function(0, '账户信息错误');
                exit();
            }
            $uid = $info['userid'];
            $sqlmap = array();
            $sqlmap['uid'] = $uid;
            $sqlmap['_string'] = "DATE_FORMAT(FROM_UNIXTIME(dateline),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
            $count = model('member_sign')->where($sqlmap)->count();
            if ($count) {
                $this->json_function(0, '您今日已签到');
                exit();
            } else {
                $sign_info = array('uid' => $uid);
                model('member_sign')->update($sign_info);
                $sign = model('task')->where(array('type' => 'sign'))->find();
                $only = '3-5-' . $uid . '-' . date('Y-m-d');
                $rs = model('member_finance_log')->where(array('only' => $only))->find();
                $num = $sign['task_reward'] * 2; //双倍签到强烈
                if (!$rs) {
                    $result = action_finance_log($uid, $num, $sign['task_type'], 'app每日签到奖励', $only);
                    $this->json_function(1, '签到成功,您本次APP签到，获得双倍奖励 ' . $num . ' 积分');
                } else {
                    $this->json_function(0, '签到失败，您今日已经签到过了！');
                    exit();

                }
            }
        }
    }

    public function get_app_config()
    {
        $arr = array();
        $arr['version'] = C('serverAppVersion');
        $arr['time'] = C('time');
        $arr['content'] = C('content');
        $arr['url'] = C('url');
        $this->json_function(1, '获取信息成功', $arr);

    }

    //网站基本信息
    public function get_webinfo()
    {
        $arr = array();
        $arr['webname'] = C('webname');
        $arr['logo'] = C('SITE_LOGO_ZHU');
        $this->json_function(1, '获取信息成功', $arr);
    }


    //上传单个图片
    public function upload_img()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 31457280;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = $_SERVER['DOCUMENT_ROOT'] . '/uploadfile/app/'; // 设置附件上传根目录
        // 上传单个文件
        $info = $upload->uploadone($_FILES["Filedata"]);
        if (!$info) {// 上传错误提示错误信息
            echo($upload->getError());
        } else {// 上传成功 获取上传文件信息
            echo '/uploadfile/app/' . $info['savepath'] . $info['savename'];
        }
    }

    //单个图片进行转码
    public function upload_img2()
    {
        $param = I('param.');
        $str = $param['file'];
        $type = $param['type'];
        switch ($type) {
            case 'image/png':
                $ext = '.png';
                break;
            case 'image/jpeg':
                $ext = '.jpg';
                break;
            case 'image/bmp':
                $ext = '.bmp';
                break;
            default:
                $ext = '.jpg';
        }
        $file_url = '/uploadfile/app/app_img/' . time() . $ext;
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        if (!file_exists(dirname($file_path))) {
            mkdir(dirname($file_path), 0777, true);
        }
        $img_content = str_replace('data:' . $type . ';base64,', '', $str);
        $str2 = base64_decode(str_replace(" ", "+", $img_content));
        $result = file_put_contents($file_path, $str2);
        if ($result) {
            $ret['status'] = 1;
            $ret['msg'] = '成功上传';
            $ret['url'] = $file_url;
        } else {
            $ret['status'] = 0;
            $ret['msg'] = '上传失败！';
            $ret['url'] = $file_url;

        }

        echo json_encode($ret);

    }

    //用户头像转码

    public function upload_avatar_img2()
    {
        $param = I('param.');
        $target_info = model('member_app')->where(array('userid' => $param['userid'], 'target' => $param['random']))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }
        $suid = $param['userid'];
        $suid = sprintf("%09d", $suid);
        $dir1 = substr($suid, 0, 3);
        $dir2 = substr($suid, 3, 2);
        $dir3 = substr($suid, 5, 2);
        $str = $param['file'];
        $type = $param['type'];
        switch ($type) {
            case 'image/png':
                $ext = '.png';
                break;
            case 'image/jpeg':
                $ext = '.jpg';
                break;
            case 'image/bmp':
                $ext = '.bmp';
                break;
            default:
                $ext = '.jpg';
        }
        $file_url = '/uploadfile/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $param['userid'] . '_avatar.jpg';
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        if (!file_exists(dirname($file_path))) {
            mkdir(dirname($file_path), 0777, true);
        }
        $img_content = str_replace('data:' . $type . ';base64,', '', $str);
        $str2 = base64_decode(str_replace(" ", "+", $img_content));
        $result = file_put_contents($file_path, $str2);
        if ($result) {
            $ret['status'] = 1;
            $ret['msg'] = '成功上传';
            $ret['url'] = $file_url;
            $image = new \Think\Image();
            $image->open($file_path);
            // 生成一个缩放后填充大小150*150的缩略图并保存为thumb.jpg
            $image->thumb(80, 80, \Think\Image::IMAGE_THUMB_FILLED)->save($file_path);

        } else {
            $ret['status'] = 0;
            $ret['msg'] = '上传失败！';
            $ret['url'] = $file_url;

        }

        echo json_encode($ret);
    }


    //上传用户头像
    public function upload_avatar_img($suid = 0, $random = '')
    {

        $param = I('param.');

        $target_info = model('member_app')->where(array('userid' => $param['userid'], 'target' => $param['random']))->find();
        if (!$target_info) {
            $this->json_function(0, '账户信息错误');
            exit();
        }

        $suid = $param['userid'];

        if ($suid < 1) {

            exit('useid不存在');

        }
        $suid = sprintf("%09d", $suid);
        $dir1 = substr($suid, 0, 3);
        $dir2 = substr($suid, 3, 2);
        $dir3 = substr($suid, 5, 2);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 31457280;
        $upload->exts = array('jpg', 'gif', 'png');
        $upload->rootPath = $_SERVER['DOCUMENT_ROOT'] . '/uploadfile/';
        $upload->savePath = 'avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
        $upload->replace = TRUE;
        $upload->saveName = $param['userid'] . '_avatar';
        $upload->autoSub = FALSE;
        $upload->saveExt = 'jpg';
        $upload->thumb = true;
        $upload->thumbMaxWidth = '50,200';
        $upload->thumbMaxHeight = '50,200';
        $upload->thumbFile = $param['userid'] . '_avatar';
        $upload->thumbRemoveOrigin = true;
        $info = $upload->uploadone($_FILES["file"]);
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功 获取上传文件信息
            echo '/uploadfile/' . $info['savepath'] . $info['savename'];
        }


    }

    /*根据商品信息搜索商品信息*/

    public function search_goods()
    {
        $param = I('param.');
        extract($param);
        $catid = max(0, (int)$catid);
        $page = max(1, (int)$page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 10;
        $keyword = remove_xss($keyword);
        $sqlmap = array();
        if ($keyword) {
            if ($type == 'c') {
                $com_map = array();
                $com_map['store_name'] = array("LIKE", "%" . $keyword . "%");
                $company_ids = model('member_merchant')->where($com_map)->getField('userid', TRUE);
                if (!$company_ids) {
                    $this->json_function(0, '没有任何内容');
                    exit();

                }
                $sqlmap['p.company_id'] = array("IN", $company_ids);
            } else {
                $sqlmap['p.title|p.keyword'] = array("LIKE", "%" . $keyword . "%");
            }
        }

        $company_id = (int)$company_id;
        if ($company_id > 0) {
            $sqlmap['p.company_id'] = $company_id;
        }

        if ($catid > 0) {
            $categorys = getcache('product_category', 'commons');
            $category = $categorys[$catid];
            $catids = $category['arrchildid'];
            if ($catids) {
                $sqlmap['p.catid'] = array("IN", $catids);
            } else {
                $this->json_function(0, '没有内容');
                exit();

            }
        }

        if ($orderby == '' || $orderway == '') {
            $orderby = 'id';
            $orderway = 'desc';
        }

        if ($mod) {
            $count = model('product')->alias('p')->join(C('DB_PREFIX') . 'product_' . $mod . ' AS t ON p.id = t.id')->where($sqlmap)->count();
            $ids = model('product')->alias('p')->join(C('DB_PREFIX') . 'product_' . $mod . ' AS t ON p.id = t.id')->where($sqlmap)->page($page, $num)->field('p.id')->order($orderby . ' ' . $orderway)->select();

            // echo model('product')->getLastSql();
        } else {
            $sqlmap['p.mod'] = array('NEQ', 'postal');
            $count = model('product')->alias('p')->where($sqlmap)->count();
            $ids = model('product')->alias('p')->where($sqlmap)->field('p.id')->page($page, $num)->order($orderby . ' ' . $orderway)->select();

        }

        if (!$ids) {
            $this->json_function(0, '没有找到商品');
            exit();
        }
        $lists = array();
        foreach ($ids as $k => $v) {
            $factory = new \Product\Factory\product($v['id']);
            $rs = $factory->product_info;
            $r['mod_name'] = model('activity_set')->where(array('key' => $rs['mod'] . '_name'))->getField('value');
            $r['price_name'] = activitiy_price_name($rs['mod']);
            $r['mod_price'] = price($rs['id']);
            $r['number'] = $rs['goods_number'] - buyer_count_by_gid($rs['id']);//剩余份数
            $r['start_time'] = $rs['start_time'];
            $r['title'] = $rs['title'];
            $r['get_trial'] = get_trial_by_gid($rs['id']); //已申请人数
            $r['catid'] = $rs['catid'];
            $r['id'] = $rs['id'];
            $r['goods_number'] = $rs['goods_number'];
            $r['source'] = $rs['source'];
            $r['mod'] = $rs['mod'];
            $r['url'] = $rs['url'];
            $r['goods_url'] = $rs['goods_url'];
            //$r['thumb'] = img2thumb($rs['thumb'],'s',1);
            $r['thumb'] = $rs['thumb'];

            if ($rs['mod'] == 'rebate') {
                $r['price'] = $rs['goods_price'] * $rs['goods_discount'];
                $r['goods_discount'] = $rs['goods_discount'];

            }
            $r['goods_price'] = $rs['goods_price'];
            $r['goods_bonus'] = $rs['goods_bonus'];

            $r['hits'] = $rs['hits'];
            $r['goods_tryproduct'] = $rs['goods_tryproduct'];
            if ($rs['mod'] == 'trial') {
                $r['goods_vipfree'] = $rs['goods_vipfree'];
                if ($rs['goods_bonus'] > '0' || $rs['goods_bonus'] != '0.00') {
                    /*3:红包类型*/
                    $r['protype'] = 3;
                } elseif ($rs['goods_tryproduct'] > 0 || $rs['goods_tryproduct'] > '0' && $rs['goods_tryproduct'] != '') {
                    /*w:拍a发b*/
                    $r['protype'] = 2;
                } else {
                    /*1:实物专区*/
                    $r['protype'] = 1;

                }


                if ($rs['goods_point'] == 1) {
                    $r['point_num'] = Integral_quantity($rs['goods_price']);

                } else {
                    unset($r['point_num']);
                }

            }

            $r['subsidy_type'] = $rs['subsidy_type'];
            $r['subsidy'] = $rs['subsidy'];
            $r['goods_point'] = $rs['goods_point'];


            if ($rs['mod'] == 'commission') {
                $r['bonus_price'] = $rs['bonus_price'];
                $r['fan_price'] = $rs['bonus_price'] + $rs['goods_price'];
                if ($rs['allow_groupid'] != '') {
                    $r['allow_groupid'] = string2array($rs['allow_groupid']);

                }


            }

            $lists[$k] = $r;
        }
        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['count'] = $count;
        $result['lists'] = $lists ? $lists : '';
        $result['pages'] = $pages;
        echo json_encode($result);

    }


    public function json_function($status, $msg, $data = '')
    {
        $result = array();
        $result['status'] = $status;
        $result['msg'] = $msg;
        $result['data'] = $data;
        echo json_encode($result);

    }


    //获取服务器的热门搜索关键词

    public function keyword_hot()
    {

        $result = model('keyword_hot')->select();

        echo json_encode($result);
    }


    /*第三方登录*/
    public function qqlogin()
    {
        if (IS_POST) {
            $param = I('post.');
            if (!$param) {
                $this->json_function(0, '参数错误');
            }

            $MemberLogic = D('Member/Member', 'Logic');
            $openid = $param['userinfo_social_uid'];
            /* 判断是否已绑定 QZONE    QQWEIBO*/
            $type = $param['userinfo_media_type'];
            $oauth = model('member_oauth')->where(array('openid' => $openid, 'type' => $type))->find();

            if ($oauth && $oauth['uid'] > 0) {
                $userinfo = model('member')->find($oauth['uid']);
                cookie('_userid', $oauth['uid'], 86400);
                $app = model('member_app')->where(array('userid' => $userinfo['userid']))->find();
                $random = random(15);
                $result = model('member_app')->where(array('userid' => $userinfo['userid']))->save(array('target' => $random));
                $return = array();
                $return['userid'] = $userinfo['userid'];
                $return['nickname'] = nickname($userinfo['userid']);
                $return['random'] = $random;
                $this->json_function(1, '登录成功', $return);
            } else {

                $info['modelid'] = 1;
                $info['email'] = random(8) . '@qq.com';
                $info['password'] = random(6);
                $info['encrypt'] = random(6);
                $info['nickname'] = $param['userinfo_username'];
                $userid = model('member')->update($info);

                if (!$userid) {
                    $this->json_function(0, model('member')->getError());
                } else {
                    $token = array();
                    $token['uid'] = $userid;
                    $token['openid'] = $openid;
                    $token['type'] = $type;
                    $token['access_token'] = random(32);
                    $token['access_time'] = NOW_TIME;
                    $token['refresh_token'] = random(32);
                    model('member_oauth')->add($token);
                    $data = array();
                    $data['alias'] = $userid;
                    $data['userid'] = $userid;
                    $data['platform'] = $param['platform'];
                    $data['version'] = $param['version'];
                    $data['reg_time'] = NOW_TIME;
                    $data['name'] = $param['platform_name'];
                    $random = random(15);
                    $data['target'] = $random;

                    $count = model('mebmer_app')->where(array('userid' => $userid))->count();
                    if ((int)$count > 0) {
                        model('member_app')->where(array('userid' => $userid))->save($data);
                    } else {
                        model('member_app')->add($data);
                    }
                    $userinfo = model('member')->find($userid);
                    cookie('_userid', $userid, 86400);
                    $return = array();
                    $return['userid'] = $userinfo['userid'];
                    $return['nickname'] = nickname($userinfo['userid']);
                    $return['random'] = $random;
                    model('member')->update(array('userid' => $userid, 'lastdate' => NOW_TIME, 'lastip' => get_client_ip(), 'loginnum' => $userinfo['loginnum'] + 1), false);
                    $this->json_function(1, '登录成功', $return);

                }


            }
        }
    }

    /**
     * 创建图形验证码
     */
    public function createVerifyCode()
    {
        $this->get_verify();
    }


}                