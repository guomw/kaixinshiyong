<?php
namespace Order\Controller;
class ApiController extends \Common\Controller\BaseController
{
    public function _initialize() {
        parent::_initialize();
    }
    
    /**
     * 填写订单号    [Adalways试用系统]
     * @author 华强 <masater@ .com>
     */
    public function fill_sn() {
        $info = I('post.');
        $info['order_sn'] = trim($info['order_sn']);
        if (!$info) $this->error('访问有误！请稍后再试');
        $factory = new \Product\Factory\order($info['order_id']);
        if (!$factory->order_info['id']) $this->error('该订单不存在，请重新下单');
        $order_sn_count = model('order')->where(array('order_sn' => $info['order_sn']))->count();
        if ($order_sn_count > 0) $this->error('该订单号重复！请检查!');

        if ($factory->order_info['order_sn']) {
            $cause = '修改商品订单号';
             if ($factory->order_info['act_mod'] == 'commission') {
                $arr = array();
                $arr['order_sn'] = $info['order_sn'];
                $arr['order_img'] = $info['order_img'];
                $result = $factory->fill_trade_no($arr,$cause);
            }else{
                $result = $factory->fill_trade_no($info['order_sn'],$cause);

            }

            // 免费试用且已发布试用报告的则状态变为待商家审核
            $report_count = model('trial_report')->where(array('order_id'=>$factory->order_info['id']))->count();
            if ($factory->order_info['act_mod'] == 'trial' && $report_count > 0){
                $factory->set_status(3);
            }
        }else{
            if ($factory->order_info['act_mod'] == 'commission') {
                $arr = array();
                $arr['order_sn'] = $info['order_sn'];
                $arr['order_img'] = $info['order_img'];
                $result = $factory->fill_trade_no($arr);
            }else{
               $result = $factory->fill_trade_no($info['order_sn']);
            }
        }


        if (!$result) $this->error($factory->getError());
        runhook('order_fill_trade_no',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title'],'order_sn' => $info['order_sn'],'mod' => $factory->product_info['mod']));
        $this->success('填写订单号成功');
    }

    /* 发布晒单->图片上传 [Adalways试用系统] */
    public function add_show_img(){
        if(!empty($_FILES)){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize  =     3145728 ;
            $upload->exts     =     array('jpg', 'gif', 'png');
            $upload->rootPath = './uploadfile/report/';
            if(!file_exists($upload->rootPath)){//不存在，则创建
               mkdir($upload->rootPath, 0777);
            }
            $upload->savePath = '';
            $upload->replace  = TRUE;
            $upload->saveName = NOW_TIME.random(5,1).'_report';
            $upload->autoSub = FALSE;
            $upload->saveExt  = 'jpg';
            $result = $upload->upload($_FILES);
            $name = __ROOT__.'/uploadfile/report/'.$result['Filedata']['savename'];
            if($result){
                exit($name);
            }else{
                exit('error');
            }
        }
    }

    /**
     * 发布晒单    [Adalways试用系统]
     * @author 华强 <masater@ .com>
     */
    public function add_show() {
        if (IS_POST){
            $info = I('post.');
            if (!$info) $this->error('访问有误！请稍后再试');
            if (!$info['oid']) $this->error('请输入订单号ID！');
            if (!$info['report_imgs']) $this->error('请上传图片！');
            if (!$info['content']) $this->error('请输入评价内容！');
            // 获取订单信息
            $factory = new \Product\Factory\order($info['oid']);
            $order_info = $factory->order_info;
            $data = array();
            $data['userid']      = $order_info['buyer_id'];
            $data['goods_id']    = $order_info['goods_id'];
            $data['order_id']    = $order_info['id'];
            $data['ip']          = $_SERVER['REMOTE_ADDR'];
            $data['reporttime']  = NOW_TIME;
            $data['report_imgs'] = $info['report_imgs'];
            $data['content']     = $info['content'];
            if (C('buyer_artificial_check') == 0) { //  获取后台开关
                $data['status'] = 1;
            }else {
                $data['status'] = 0;
            }
            $result = model('report')->add($data);
            if (!$result){
                // 发布晒单失败后删除之前上传的图片
                if ($info['report_imgs']) unlink($info['report_imgs']);
                $this->error('晒单失败，请稍后再试！');
            }
            $factory->write_log('发布晒单成功');
            $this->success('晒单成功！');
        }else{
            $this->error('请勿非法请求！');
        }
    }


    /*查看晒单*/
    public function show_order($id = 0){
        $id = (int)($id);
        if (!$id) return FALSE;
        $sqlmap = array();
        $sqlmap['userid'] = cookie('_userid');
        $sqlmap['order_id'] = $id;
        $count = model('report')->where($sqlmap)->count();
        $lists = model('report')->where($sqlmap)->order('reporttime DESC')->page(PAGE,10)->select();
        $pages = showPage($count,PAGE,10);
        include template('buyer/show_order','member');
    }

    /* 会员对商品订单详细信息 */
    public function order_infos($pid) {
        $this->db = model('order');
        $pid = (int) $pid;
        $userid = (int) cookie('_userid');
        if ($pid < 1) $this->error('该商品不存在');
        if ($userid < 1) $this->error('该会员不存在');
        $info = array();
        $sqlmap = array();
        $sqlmap['goods_id'] = $pid;
        $sqlmap['buyer_id'] = $userid;
        /* 总订单数 */
        $info['all_orders'] = $this->db->where($sqlmap)->count();

        /* 未完成的订单 */
        $sqlmap['status'] = array('BETWEEN','1,6') ;
        $info['not_over'] = $this->db->where($sqlmap)->count();

        $order_infos = array('close_order','trial_apply','buy_success','wait_examine','examine_refuse','examine_pass','appealing','over_order');
        for ($i=0; $i < 8; $i++) {
            $sqlmap['status'] = $i;
            $info[$order_infos[$i]] = $this->db->where($sqlmap)->count();
        }
        $this->success($info);
    }

    
        //桌面订单提醒通知
        public function order_message(){
            //获取点对点的商家信息
            $userid = (int) cookie('_userid');
            if ($userid < 1) $this->error('该会员不存在');
            $data = model('message')->where(array('send_to_id' =>$userid,'status' => 0))->find();
            if(!$data){
            $this->error('没有要通知的信息');
            }else{
               model('message')->where(array('messageid' =>$data['messageid']))->setField('status',1);
               $this->success($data);
            }
        }

}
