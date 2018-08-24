<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Task\Controller;
class IndexController extends \Common\Controller\BaseController {
    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        $userinfo = is_login();
        $map['task_status'] = 1;
        $map['type'] = array('NEQ','sign');
        $tasks = model('task')->where($map)->select();
        foreach($tasks as $k => $v) {
            switch ($v['type']) {
                // 注册
                case 'reg':
                    $v['is_complete'] = ($userinfo) ? 1 : 0;
                    break;
                /* 手机认证 */
                case 'phone':
                    $v['is_complete'] = (int) $userinfo['phone_status'];
                    break;
                /* 邮件认证 */
                case 'email':
                    $v['is_complete'] = (int) $userinfo['email_status'];
                    break;
                /* 身份认证 */
                case 'name':
                    if($userinfo && model('member_attesta')->where(array('userid' => $userinfo['userid'], 'type' => 'identity', 'status' => 1))->count()) {
                        $v['is_complete'] = 1;
                    } else {
                        $v['is_complete'] = 0;
                    }
                    break;
                default:
                    $v['is_complete'] = 0;
                    break;
				
            }
			if( $v['task_type'] == 'point'){
				$v['record'] = $v['task_reward'].'积分';
			}elseif($v['task_type'] == 'exp'){
				$v['record'] = $v['task_reward'].'经验';
			}else{
				$v['record'] = $v['task_reward'].'美元';
			}
            $tasks[$k] = $v;
        }
        $SEO = seo(0, '会员任务');
        $sign = model('task')->where(array('type'=>'sign'))->find();
        include template('index');
    }
    
    /**
     * 日赚任务
     */
    public function broke(){
    	$page = max(1, (int) I('page'));
    	$userinfo = is_login();
    	//if(!$userinfo) $this->error('您还没有登录，请先登录',U('Member/Index/login'));
    	$SEO = seo(0,'日赚任务');
    	//任务达人榜
    	$result = model('task_records')->field('id,userid,count(userid) AS num,tid,sum(price) AS total')->group('userid')->order('total DESC')->limit(9)->select();
    	foreach ($result as $k=>$v){
    		$result[$k]['nickname'] = model('member')->getFieldByUserid($v['userid'],'nickname');
    		$result[$k]['avatar'] = getavatar($v['userid']);
    	}
    	include template('broke');
    }
    
    public function iswork(){
    	$id = (int) I('id');
		if($id < 1) $this->error('参数错误');
		$userid = cookie('_userid');
		$info = model('task_records')->where(array('userid'=>$userid,'tid'=>$id))->find();
		if($info){
			$this->error('任务已经完成');
		}else{
			$this->success('ok');
		}
    }

    /**
     * 日赚任务详情
     */
    
    public function broke_show($id = 0){
    	$id = (int) $id;
    	if($id < 1) $this->error('没有该任务');
    	$userinfo = is_login();
    	if(!$userinfo) $this->error('您还没有登录，请先登录',U('Member/Index/login'));
    	$info = model('task_records')->where(array('userid'=>$userinfo['userid'],'tid'=>$id,'status'=>1))->find();
    	if($info) $this->error('您已经完成该任务了');
    	$SEO = seo(0,'日赚任务详情页');
    	$info = model('task_day')->getById($id);
    	extract($info);
    	$goods_albums = string2array($goods_albums);
		/* 浏览量 */
        model('task_day')->where(array('id' => $id))->setInc('hits', 1);

         /*ip地址*/
         $ip = get_client_ip();

         /*获取商家的基本信息*/
         $conmpany_info = model('merchant_store')->where(array('userid' =>$info['company_id'],'contact_want' =>$info['goods_wangwang']))->field('store_type,industry')->find();

         /*获取商家店铺分类*/
         $category   = model('product_category')->where(array('catid' =>$conmpany_info['industry']))->getField('catname');

         /*统计已发布的活动份数*/
         $goods_num = model('product')->where(array('company_id' =>$info['company_id']))->count();

         /*获取日赚任务已参与列表*/
         $rs = model('task_records')->where(array('tid' => $id))->getField('userid',true);

        foreach ($rs as $k => $v) {
              $rs1[$k]['nickname'] =  nickname($v);
              $rs1[$k]['avatar']   =  getavatar($v);
        }


        /*总参与人数*/
        $rs_num = count($rs);

    	include template('broke_show');
    }


    /**
     * 答案提交
     */
    public function answer(){
    	$content = I('content');
    	$id = (int) I('id');
    	if($id < 1) $this->error('参数错误');
    	$userinfo = is_login();
    	if(!$userinfo) $this->error('您还没有登录，请先登录',U('Member/Index/login'));
    	//判断用户是否完成过
    	$modelid = model('member')->getFieldByUserid($userinfo['userid'],'modelid');
    	if($modelid != 1) $this->error('只限于买家参与',U('broke'));
    	//查看该用户是否绑定手机、亚马逊账号
      
        if(DEFAULT_THEME != 'wap'){ //手机没有绑定亚马逊号
            $taobao = model('member_bind')->where(array('userid'=>$userinfo['userid']))->find();
            if(!$taobao){
                $this->error('您还没有绑定亚马逊，去绑定',U('Member/Attesta/bindtaobao'));
            }
        }

    	$phone = model('member')->getFieldByUserid($userinfo['userid'],'phone_status');
    	if($phone != 1){
    		$this->error('您还没有绑定手机，去绑定',U('Member/Attesta/phone_attesta'));
    	}
    	$r = model('task_records')->where(array('tid'=>$id,'userid'=>$userinfo['userid'],'status'=>1))->find();
		
    	if($r){
    		$this->error('您已经完成了该任务',U('broke'));
    	}

    	//查出该任务的答案
    	$info = model('task_day')->getById($id);
		//判断该任务是否存在
		if($info['goods_number'] == $info['already_num']){
			$this->error('该任务已结束');
		}    
    	$answer = $info['answer'];
    	if($answer == $content){
    		
    		//给用户增加佣金 
    		$sign = '1-3-'.$userinfo['userid'].'-'.$info['id'].'-'.$info['goods_price'];
    		$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
    		if(!$rs){

                if (C('sso_is_open') == 1) {
                    $_infos = array();
                    $_infos['userid'] = $userInfo['userid'];
                    $_infos['type'] = 'money';
                    $_infos['type_id'] = 2501;
                    $_infos['num'] = $info['goods_price'];
                    $_infos['seller_id'] = $info['company_id'];
                    $ret = _ps_send('money',$_infos);
                    $data = php_data($ret);
                    if ($data['status'] == 1) {
                        //加入任务记录表
                        $sqlMap = array();
                        $sqlMap['tid'] = $id;
                        $sqlMap['userid'] = $userinfo['userid'];
                        $sqlMap['start_time'] = NOW_TIME;
                        $sqlMap['status'] = 1;
                        $sqlMap['answer'] = $content;
                        $sqlMap['clientip'] = get_client_ip();
                        $sqlMap['price'] =$info['goods_price'];
                        model('task_records')->add($sqlMap);
                        action_finance_log($userinfo['userid'], $info['goods_price'], 'money', $info['title'].'任务完成，获得佣金', $sign,array('goods_id' => $info['id']));
                            }

                    # code...
                }else{

                        //加入任务记录表
                        $sqlMap = array();
                        $sqlMap['tid'] = $id;
                        $sqlMap['userid'] = $userinfo['userid'];
                        $sqlMap['start_time'] = NOW_TIME;
                        $sqlMap['status'] = 1;
                        $sqlMap['answer'] = $content;
                        $sqlMap['clientip'] = get_client_ip();
                        $sqlMap['price'] =$info['goods_price'];
                        model('task_records')->add($sqlMap);
            		    action_finance_log($userinfo['userid'], $info['goods_price'], 'money', $info['title'].'任务完成，获得佣金', $sign,array('goods_id' => $info['id']));
                }
    		}else{
    		    $this->error('会员增加佣金，重复操作');
    		}
    		$sign1 = '1-3-'.$info['company_id'].'-'.$info['id'].'-'.$info['goods_price'].'-'.NOW_TIME;
    		$rs1 = model('member_finance_log')->where(array('only'=>$sign1))->find();
    		if(!$rs1){
    		    action_finance_log($info['company_id'], -$info['goods_price'], 'deposit', $userinfo['userid'].'任务完成，扣除佣金', $sign1,array('goods_id' => $info['id']));
    		}else{
    		    $this->error('商家扣除佣金，重复操作');
    		}
    		//商家减去佣金（冻结中保证金减去）
    		model('member_merchant')->where(array('userid'=>$info['company_id']))->setDec('frozen_deposit',$info['goods_price']);
    		//任务完成数量 + 1
    		model('task_day')->where(array('id'=>$id))->setInc('already_num',1);
    		model('task_records')->where(array('tid'=>$id,'userid'=>$userinfo['userid']))->save(array('status'=>1));
    		$this->success('回答正确',U('broke'));
    	}else{
    		$this->error('答案错误,请仔细寻找',U('broke_show',array('id'=>$id)));
    	}
    }

    public function getlists(){
        $param = I('param.');
        extract($param);
        $catid = max(0, (int) $catid);
        $page = max(1, (int) $page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 20;
        $keyword = remove_xss($keyword);
        $sqlmap = array();

        $sqlmap['status'] = $status;
        $sqlmap['_string'] = '`goods_number` != `already_num`';

        $count = model('task_day')->where($sqlmap)->count();
        $lists = model('task_day')->where($sqlmap)->page($page, $num)->order($orderby.' '.$orderway)->select();

        foreach($lists as $k=>$v){
            $lists[$k]['sheng_num'] = $v['goods_number']-$v['already_num'];
        }

        if(!$lists){
            $result['status'] = 0;
            $result['data'] = array(
                'count' => $count
            );
            echo json_encode($result);
            exit;
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

}
