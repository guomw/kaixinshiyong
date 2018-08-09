<?php
/**
 * 队列消息发送控制器 专门用于发送队列消息 eamil 和 phone
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace   Queue\Controller;
class IndexController extends \Common\Controller\BaseController
{
    public function _initialize(){
        parent::_initialize();

       static $order_info; $goods_info; $user_info;
        
    }

    public function queue($order_id,$type){
          $order_id = (int) $order_id;
          $type = (int) $type;

          //获取单个订单信息
          $this->order_info = model('order')->field('id,buyer_id,goods_id,inputtime,status,check_time,create_time,complete_time')->where(array('id' =>$order_id))->find();

          //获取单个会员的信息
          $this->user_info  = model('member')->where(array('userid' =>$this->order_info['buyer_id']))->field('phone,email,nickname')->find();

          //获取单个商品的信息
          $this->goods_info = model('product')->where(array('id' =>$this->order_info['goods_id'] ))->field('title,mod,goods_price')->find();

         if($this->goods_info['mod'] == 'trial') $goods_price = model('product_trial')->where(array('id' =>$this->order_info['goods_id'] ))->getfield('goods_price');
         if($this->goods_info['mod'] == 'rebate') $goods_discount =model('product_rebate')->where(array('id' =>$this->order_info['goods_id'] ))->getfield('goods_discount');

          //会员昵称
          $nickname = $this->user_info['nickname'] ? $this->user_info['nickname'] : c('WEBNAME').'会员';

           /*发送类型1 获得试用资格未下单*/
          if($type == 1){
                //开启邮件发送
                if(C('two_trial_pass.email') == 1 || C('two_trial_pass.message')  == 1){
                    $data['title'] = '您参与的活动（id：'.$this->order_info['goods_id'].'）即将失去试用资格! ';
                    $data['body']  = '亲爱的'.$nickname.'！ 您已获得了（id：'.$this->order_info['goods_id']."）商品的试用资格，但您还未下单填写订单号,请您在到期时间之前完成下单并返回平台填写订单号，逾期将失去试用资格！\r\n<br/><br/> ";
                    $data['body']  .='试用品名称：'.$this->goods_info['title']."<br/> ";
                    $data['body']  .= '试用品价值：'.$goods_price." 元<br/>";
                    $data['body']  .='申请时间：'.dgmdate($this->order_info['create_time']).'<br/>';
                    $data['body']  .='获得资格时间：'.dgmdate($this->order_info['check_time'])."<br/><br/>";
                    $data['body']  .='当前状态： 请及时按照活动要求下单，并返回平台填写订单号。'."<br/>";
                    $data['body']  .='到期时间： '.dgmdate($this->order_info['check_time'] + (C_READ('buyer_write_order_time')*3600)).'<br/><br/>';
                    $data['body']  .='温馨提示：'."<br/>";
                    $data['body']  .='1.未及时填写订单号，将视为您主动放弃试用资格！'."<br/>";
                    $data['body']  .='2.在下单过程中，如有任何疑问请及时联系在线客服！'."<br/><br/>";
                    $data['body']  .= '                       '.C('WEBNAME').' 品质试用，贴心试用每一天，祝您每天生活愉快';

                   if(C('two_trial_pass.email') == 1) $this->add_email_queue($data);

                    //开启站内信发送
                    if(C('two_trial_pass.message')  == 1){
                        $message = array();
                        $message['send_from_id'] = 1;
                        $message['send_to_id'] = $this->order_info['buyer_id'];
                        $message['subject'] = $data['title'];
                        $message['content'] = $data['body'];
                        $api = new \Message\Library\api();
                        $api->send_mess($message);
                    }

                }
               
                //开启短信发送
                if(C('two_trial_pass.sms') == 1){

                 $data1['body'] = '亲爱的'.$nickname.',温馨提示：您获得了活动商品:'.$this->goods_info['title'].'的试用资格，请在'.dgmdate($this->order_info['check_time'] + (C_READ('buyer_write_order_time')*3600)).' 之前下单并填写订单号，逾期视为您主动放弃试用资格，谢谢合作！';
                    $this->add_phone_queue($data1);
                }


          }
          
          /*发送类型2 用户到期之前未填写试用报告*/
          if($type == 2){
              
                    //开启邮件发送
                   if(C('two_trial_report.email') == 1 || C('two_trial_report.message') == 1) {
                    $data['title'] = '请及时填写活动（id：'.$this->order_info['goods_id'].'）试用报告  ';
                    $data['body']  = '亲爱的'.$nickname.'！ 您已获得了（id：'.$this->order_info['goods_id']."）商品的试用资格，但您还未填写试用报告，请在到期时间之前填写试用报告<br/><br/> ";
                    $data['body']  .='试用品名称：'.$this->goods_info['title']."<br/> ";
                    $data['body']  .= '试用品价值：'.$goods_price." 元<br/>";
                    $data['body']  .='申请时间：'.dgmdate($this->order_info['create_time']).'<br/>';
                    $data['body']  .='获得资格时间：'.dgmdate($this->order_info['check_time'])."<br/><br/>";
                    $data['body']  .='当前状态： 请及时填写试用报告。'."<br/>";
                    $data['body']  .='到期时间： '.dgmdate($this->order_info['check_time'] + (C_READ('buyer_write_talk_time') * 86400)).'<br/><br/>';
                    $data['body']  .='温馨提示：'."<br/>";
                    $data['body']  .='1.未及时填写试用报告，将视为您主动放弃试用资格！<br/>';
                    $data['body']  .='2.如因为快递等非人为原因，导致还未收到商品，请主动联系平台客服，延长时间！<br/>';
                    $data['body']  .='3.如有任何疑问，请及时联系平台客服！<br/><br/>';
                    $data['body']  .= '                       '.C('WEBNAME').' 品质试用，贴心试用每一天，祝您每天生活愉快';

                   if(C('two_trial_report.email') == 1) $this->add_email_queue($data);

                    //开启站内信发送
                    if(C('two_trial_report.message') == 1){
                        $message = array();
                        $message['send_from_id'] = 1;
                        $message['send_to_id'] = $this->order_info['buyer_id'];
                        $message['subject'] = $data['title'];
                        $message['content'] = $data['body'];
                        $api = new \Message\Library\api();
                        $api->send_mess($message);

                    }

                   }

                    //开启短信发送
                   if(C('two_trial_report.sms') == 1){

                    $data1['body'] = '亲爱的'.$nickname.',温馨提示：您参与的活动'.$this->goods_info['title'].',但还未填写试用报告，请在'.dgmdate($this->order_info['check_time'] + (C_READ('buyer_write_talk_time') * 86400)).' 之前填写试用报告，逾期视为您主动放弃试用资格，谢谢合作！';
                    $this->add_phone_queue($data1);

                   }

          }

          /*发送类型3 用户抢购了返利商品未填写订单号*/
          if($type == 3){

               //发送邮件
              if(C('two_rebate_order_sn.email') == 1 || C('two_rebate_order_sn.message') == 1 ){
                $data['title'] = '请及时填写活动（id：'.$this->order_info['goods_id'].'）订单号!  ';
                $data['body']  = '亲爱的'.$nickname.'！ 您抢购了活动（id：'.$this->order_info['goods_id']."）商品，您还未填写订单号，请在到期时间之前填写订单号！<br/><br/> ";
                $data['body']  .='活动品名称：'.$this->goods_info['title']."<br/> ";
                $data['body']  .= '原价 '.$this->goods_info['goods_price']." 元<br/>";
                $data['body']  .= '活动价 '.$this->goods_info['goods_price'] / 10 * $goods_discount." 元<br/>";
                $data['body']  .= '折扣 '.$goods_discount." 折<br/>";
                $data['body']  .='抢购时间：'.dgmdate($this->order_info['create_time']).'<br/><br/>';
                $data['body']  .='当前状态： 请及时下单并返回平台填写订单号。'."<br/>";
                $data['body']  .='到期时间： '.dgmdate($this->order_info['create_time'] + (C_READ('buyer_write_order_time')*3600)).'<br/><br/>';
                $data['body']  .='温馨提示：'."<br/>";
                $data['body']  .='1.未及时填写订单号，将视为您主动放弃抢购资格！<br/>';
                $data['body']  .='2.在下单过程中，如有任何疑问请及时联系在线客服！<br/><br/>';
                $data['body']  .= '                       '.C('WEBNAME').' 品质试用，贴心试用每一天，祝您每天生活愉快';
                
                if(C('two_rebate_order_sn.email') == 1)  $this->add_email_queue($data);

                //开启站内信发送
                if(C('two_rebate_order_sn.message') == 1){
                    $message = array();
                    $message['send_from_id'] = 1;
                    $message['send_to_id'] = $this->order_info['buyer_id'];
                    $message['subject'] = $data['title'];
                    $message['content'] = $data['body'];
                    $api = new \Message\Library\api();
                    $api->send_mess($message);

                }

              }

             //发送短信
             if(C('two_rebate_order_sn.sms') == 1){
                $data1['body'] = '亲爱的'.$nickname.',温馨提示：您抢购的活动商品'.$this->goods_info['title'].',还未填写订单号，请在'.dgmdate($this->order_info['create_time'] + (C_READ('buyer_write_order_time')*3600)).' 之前填写订单号，逾期视为您主动放弃活动资格，自动关闭订单！';
                $this->add_phone_queue($data1);

                       
             }
           

          }

    }


       /* 添加邮件发送队列*/
    private function add_email_queue($data){

        if(!$this->user_info['email']) return false;
        $data['email'] = $this->user_info['email'];
        $data['time1'] = NOW_TIME;
        $data['type'] = 2;
        $data['status'] = 0;
        $data['order_id'] =$this->order_info['id'];
        $data['userid'] = $this->order_info['buyer_id'];
        $ret =  model('queue')->add($data);
        if($ret){
            return true;
        }else{
            return false;
        }
           
     }

    /* 添加手机短信发送队列*/
      private function add_phone_queue($data){
        if(!$this->user_info['phone']) return false;
        $data['phone'] = $this->user_info['phone'];
        $data['time1'] = NOW_TIME;
        $data['type'] = 1;
        $data['status'] = 0;
        $data['order_id'] =$this->order_info['id'];
        $data['userid'] = $this->order_info['buyer_id'];
        $ret =  model('queue')->add($data);
        if($ret){
            return true;
        }else{
            return false;
        }

        
     }
        


}