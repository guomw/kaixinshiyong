<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Shop\Controller;
class ApiController extends \Common\Controller\BaseController
{
    public function _initialize(){
        parent::_initialize();
        $this->shop_db = model('shop');
        $this->log_db = model('shop_log');
        $this->uinfo = is_login();
    }

    /* 兑换商品 */
    public function submit($shop_id = 0){
    	/*$this->success('商品兑换成功');die;*/
    	$shop_id = (int) $shop_id;
    	$spec = I('spec', '', 'remove_xss');
    	if($shop_id < 1) {
    		$this->error('参数错误');
    	}
    	// 是否登录
    	if(!$this->uinfo) {
    		$this->error('您尚未登录');
    	}
    	if($this->uinfo['modelid'] != 1) {
    		$this->error('积分兑换目前只对普通用户开放');
    	}
    	// 商品信息
    	$rs = $this->shop_db->find($shop_id);
    	$rs['spec'] = explode(",", $rs['spec']);
    	if(!$rs) {
    		$this->error('商品不存在');
    	}
    	if($rs['end_time'] < NOW_TIME) {
    		$this->error('本活动已结束');
    	}
    	if ($rs['total_num'] <= $rs['sale_num']) {
    		$this->error('本商品已兑换完毕，请尝试兑换其它商品');
    	}

    	if($rs['spec'] && !in_array($spec, $rs['spec'])) {
    		$this->error('商品属性不正确');
    	}

    	// 积分信息
    	if($this->uinfo['point'] < $rs['point']) {
    		$this->error('您的积分不足');
    	}
    	// 是否重复兑换
    	$sqlmap = array();
    	$sqlmap['userid'] = $this->uinfo['userid'];
    	$sqlmap['shop_id'] = $rs['id'];
    	if($this->log_db->where($sqlmap)->count()) {
    		$this->error('您已兑换本商品，请勿重复申请');
    	}

    	/*------ 执行兑换 -------*/
    	// 写入日志
    	$log = array(
    		'userid' => $this->uinfo['userid'],
    		'shop_id' => $rs['id'],
    		'spec' => $spec,
    		'point' => $rs['point'],
    		'apply_time' => NOW_TIME,
    		'ip' => get_client_ip(),
    		'status' => 0
    	);
    	$result = $this->log_db->update($log);
    	if($result) {
    		// 扣除积分
    		$sign = '3-12-'.$this->uinfo['userid'].'-'.$shop_id.'-'.$rs['point'].'-'.date('Y-m-d H');
    		$rss = model('member_finance_log')->where(array('only'=>$sign))->find();
    		if(!$rss){
                if (C('sso_is_open') == 1) {
                    $infos = array();
                    $infos['userid'] = $this->uinfo['userid'];
                    $infos['type'] = 'point';
                    $infos['type_id'] = 1501;
                    $infos['num'] = $rs['point'];
                    $ret = _ps_send('point',$infos);
                    $data = php_data($ret);
                    if ($data['status'] == 1) {
                        action_finance_log($this->uinfo['userid'], '-'.$rs['point'] , 'point', '积分兑换商品',$sign);
                        $this->shop_db->where(array('id' => $shop_id))->setInc('sale_num');
                        $this->success('商品兑换成功');
                    }

                }else{

                    action_finance_log($this->uinfo['userid'], '-'.$rs['point'] , 'point', '积分兑换商品',$sign);
                    $this->shop_db->where(array('id' => $shop_id))->setInc('sale_num');
                    $this->success('商品兑换成功');
    		    
                 }
    		}else{
    		    $this->error('商品兑换失败，一个小时只限兑换一次');
    		}
    	} else {
    		$this->error('商品兑换失败');
    	}
    }

    public function check(){
        $address = model('member')->where(array('userid'=>$this->uinfo['userid']))->getField('address');
        if ($address) {
           $this->success('通过');
        }else{ 
            $this->error('您尚未完善收货地址，请先完善',U('member/profile/infomation'));
        }
    }
}