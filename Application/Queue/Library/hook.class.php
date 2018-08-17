 <?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/

/*
namespace Queue\Library;
class hook {
    
    //群发队列

    public function system_inits() {

      $SmsApi = new \Sms\Api\SmsApi();

        while(true){
            $item = model('queue')->where(array('status' => 0))->order('id asc')->find(); //获取数据表第一条记录
            if(!$item){
                //如果队列中没有数据，则结束定时器
                break;
            }

            if($item['type'] == 2) {

               //发送邮件  

               if(sendmail($item['email'], $item['title'], $item['body'])){

                 model('queue')->where(array('id' => $item['id']))->delete();

               }else{
                   model('queue')->where(array('id' => $item['id']))->setField(array('status' => 2));
               };

            }


           if($item['type'] == 1) {

            $order = model('order')->where(array('id'=>$item['order_id']))->field('status,goods_id,buyer_id,check_time,inputtime,create_time')->find();
            $goods = model('product')->where(array('id' =>$order['goods_id'] ))->field('title,mod,goods_price')->find();
            $userinfo= model('member')->where(array('userid' =>$order['buyer_id']))->field('phone,email,nickname')->find();
            if($goods['mod'] == 'trial') $goods_price = model('product_trial')->where(array('id' =>$order['goods_id'] ))->getfield('goods_price');
            if($goods['mod'] == 'rebate') $goods_discount =model('product_rebate')->where(array('id' =>$order['goods_id'] ))->getfield('goods_discount');



            if ($order['status'] == 2 && $goods['mod'] == 'trial') {
                 $html = "亲爱的{nickname} 您已获得了（id：{goods_id}）商品的试用资格，但您还未下单填写订单号,请您在到期时间之前完成下单并返回平台填写订单号，逾期将失去试用资格！\r\n<br/><br/> 试用品名称：{goods_name}<br/>试用品价值：{goods_price}美元<br/>申请时间：{apply_time}<br/>获得资格时间：{check_time}<br/><br/>当前状态： 请及时按照活动要求下单，并返回平台填写订单号。<br/>到期时间：{end_time}<br/><br/>温馨提示：<br/>1.未及时填写订单号，将视为您主动放弃试用资格！<br/>2.在下单过程中，如有任何疑问请及时联系在线客服！<br/><br/>&nbsp;&nbsp;&nbsp;{webname}品质试用，贴心试用每一天，祝您每天生活愉快";
                  $arr = array();
                  $apply_time = dgmdate($order['create_time'], 'Y-m-d');
                  $check_time = dgmdate($order['check_time'], 'Y-m-d');
                  $end_time = dgmdate($order['check_time'] + (C_READ('buyer_write_order_time')*3600));
                  $webname = C('WEBNAME');
                  $nickname = $userinfo['nickname']?$userinfo['nickname']:C('WEBNAME').'会员';
                  $goods_id = $order['goods_id'];
                  $title = $goods['title'];
                  $goods_price = $goods['goods_price'];
                 
                  $arr['param'] = "{'nickname':'$nickname','goods_id':'$goods_id','goods_name':'$title','goods_price':'$goods_price','apply_time':'$apply_time','check_time':'$check_time','end_time':'$end_time','webname':'$webname'}";
                  $arr['template_id'] = C('template_id_17');
                  $result = $SmsApi->send($item['phone'], $html,$arr);
            }elseif($order['status'] == 8){

               $html2 = "亲爱的{nickname}！ 您已获得了（id：{goods_id}）商品的试用资格，但您还未填写试用报告，请在到期时间之前填写试用报告<br/><br/> 试用品名称：{goods_name}<br/>试用品价值：{goods_price}美元<br/>申请时间：{apply_time}<br/>获得资格时间：{check_time}<br/><br/>当前状态： 请及时填写试用报告。<br/>到期时间：{end_time}<br/><br/>温馨提示：<br/>1.未及时填写试用报告，将视为您主动放弃试用资格！<br/>2.如因为快递等非人为原因，导致还未收到商品，请主动联系平台客服，延长时间！<br/><br/>3.如有任何疑问，请及时联系平台客服！<br/><br/>&nbsp;&nbsp;&nbsp;{webname}品质试用，贴心试用每一天，祝您每天生活愉快";

                  $arr = array();
                  $apply_time = dgmdate($order['create_time'], 'Y-m-d');
                  $check_time = dgmdate($order['check_time'], 'Y-m-d');
                  $end_time = dgmdate($order['check_time'] + (C_READ('buyer_write_talk_time')*86400));
                  $webname = C('WEBNAME');
                  $nickname = $userinfo['nickname']?$userinfo['nickname']:C('WEBNAME').'会员';
                  $goods_id = $order['goods_id'];
                  $title = $goods['title'];
                  $goods_price = $goods['goods_price'];
                  $arr['param'] = "{'nickname':'$nickname','goods_id':'$goods_id','goods_name':'$title','goods_price':'$goods_price','apply_time':'$apply_time','check_time':'$check_time','end_time':'$end_time','webname':'$webname'}";
                  $arr['template_id'] = C('template_id_18');
                  $result = $SmsApi->send($item['phone'], $html2,$arr);


            }elseif ($order['status'] == 2 && $goods['mod'] == 'rebate') {

               $html3 = "亲爱的{nickname}！ 您抢购了活动（id：{goods_id}）商品，您还未填写订单号，请在到期时间之前填写订单号！<br/><br/>活动品名称：{goods_name}<br/>原价: {goods_price}美元<br/>活动价: {money} 美元<br/>折扣: {discount}折<br/>抢购时间：{create_time}<br/><br/>当前状态： 请及时下单并返回平台填写订单号。<br/>到期时间：{end_time}<br/><br/>温馨提示：<br/>1.未及时填写订单号，将视为您主动放弃抢购资格！<br/>2.在下单过程中，如有任何疑问请及时联系在线客服！<br/><br/>&nbsp;&nbsp;&nbsp;{webname}品质试用，贴心试用每一天，祝您每天生活愉快";

                  $arr = array();
                  $apply_time = dgmdate($order['create_time'], 'Y-m-d');
                  $check_time = dgmdate($order['check_time'], 'Y-m-d');
                  $money = $goods['goods_price'] / 10 * $goods_discount;
                  $end_time = dgmdate($order['check_time'] + (C_READ('buyer_write_order_time','rebate')*3600));
                  $webname = C('WEBNAME');
                  $nickname = $userinfo['nickname']?$userinfo['nickname']:C('WEBNAME').'会员';
                  $goods_id = $order['goods_id'];
                  $title = $goods['title'];
                  $goods_price = $goods['goods_price'];
                  $arr['param'] = "{'nickname':'$nickname','goods_id':'$goods_id','goods_name':'$title','goods_price':'$goods_price','create_time':'$apply_time','money':'$money','discount':'$goods_discount','end_time':'$end_time','webname':'$webname'}";
                  $arr['template_id'] = C('template_id_19');
                  $result = $SmsApi->send($item['phone'], $html3,$arr);
                
              
            }
           


                   //发送短信    
             if($result){

                model('queue')->where(array('id' => $item['id']))->delete();
             }else{

                model('queue')->where(array('id' => $item['id']))->setField(array('status' => 2));
             }


           }
        }

    }
}
*/

?>