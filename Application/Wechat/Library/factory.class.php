<?php
namespace Wechat\Library;
class factory {
    private $userid = 0;
    private $params = '';
    private $web_url = '';

    public function __get($name) {
        if(isset($this->$name)) {
            return $this->$name;
        } else {
            return 0;
        }
    }


    public function __construct(){

        $this->web_url = 'http://'.$_SERVER['HTTP_HOST'].'/';
        $wap_config = getcache('setting', 'wap');
        $this->wap_url =  ($wap_config['wap_enable'] == 1 && $wap_config['wap_domain']) ? $wap_config['wap_domain'] : (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__;
        
    }

    
    public function __set($name, $value) {
        $this->$name = $value;
    }
    
    /* 绑定帐号 */
    public function wechat_account_bind() {
        return ($this->userid > 0) ? C('WECHAT_ACCOUNT_YBIND_TPL') : C('WECHAT_ACCOUNT_NBIND_TPL');
    }
    
    /* 账户余额 */ 
    public function wechat_account_balance() {
        $balance = model('member')->where(array('userid' => $this->userid))->getField('money');
        $replace['{balance}'] = sprintf('%.2f', $balance);
        $replace['{url}'] = str_replace('wechat.php', 'index.php',U('Pay/Index/deposite', $this->params, '', TRUE));
        return str_replace(array_keys($replace), $replace, C('WECHAT_ACCOUNT_BALANCE')); 
    }
    
    /* 申请提现 */
    public function wechat_account_cash() {
        $balance = model('member')->where(array('userid' => $this->userid))->getField('money');
        $replace['{balance}'] = sprintf('%.2f', $balance);
        $replace['{url}'] =  str_replace('wechat.php', 'index.php',U('Pay/Index/deposite', $this->params, '', TRUE));
        return str_replace(array_keys($replace), $replace, C('WECHAT_ACCOUNT_CASH'));
    }
    
    /* 我的积分 */
    public function wechat_account_point() {
        $point = model('member')->where(array('userid' => $this->userid))->getField('point');
        $replace['{point}'] = (int) $point;
        $replace['{url}'] = str_replace('wechat.php', 'index.php',U('Shop/Index/index', $this->params, '', TRUE));
        return str_replace(array_keys($replace), $replace, C('WECHAT_ACCOUNT_POINT'));
    }
    
    /* 账户信息 */
    public function wechat_account_info() {
        $user_info = model('member')->where(array('userid' => $this->userid))->find();        
        $replace['{nickname_status}'] = ($user_info['nickname']) ? '已完善' : '去完善';
        $replace['{phone_status}'] = ($user_info['phone_status'] == 1) ? '已认证' : '去认证';
        $replace['{phone_url}'] = str_replace('wechat.php', 'index.php',U('Member/Attesta/phone_attesta', $this->params, '', TRUE));
        $replace['{email_status}'] = ($user_info['email_status'] == 1) ? '已认证' : '去认证'; 
        $replace['{email_url}'] = str_replace('wechat.php', 'index.php',U('Member/Attesta/email_attesta', $this->params, '', TRUE));
        $att_map = array();
        $att_map['userid'] = $this->userid;
        $att_map['type'] = 'identity';
        //-----------实名认证状态-----------
        $identity = model('member_attesta')->where($att_map)->find();
        if(!$identity) {
            $replace['{identity_status}'] = '未认证';
        } else {
            if($identity['status'] == 1) {
                $replace['{identity_status}'] = '已认证';
            } elseif($identity['status'] == 0) {
                $replace['{identity_status}'] = '认证中';
            } else {
                $replace['{identity_status}'] = '未认证';
            }
        }
        $replace['{identity_url}'] = str_replace('wechat.php', 'index.php',U('Member/Attesta/name_attesta', $this->params, '', TRUE));
        //-----------支付宝认证状态-----------
        $att_map['type'] = 'alipay';
        $alipay = model('member_attesta')->where($att_map)->find();
        if(!$alipay) {
            $replace['{alipay_status}'] = '未认证';
        } else {
            if($alipay['status'] == 1) {
                $replace['{alipay_status}'] = '已认证';
            } elseif($alipay['status'] == 0) {
                $replace['{alipay_status}'] = '认证中';
            } else {
                $replace['{alipay_status}'] = '未认证';
            }
        }
        $replace['{alipay_url}'] = str_replace('wechat.php', 'index.php',U('Member/Attesta/alipay_attesta', $this->params, '', TRUE));
        //-----------银行卡认证状态-----------
        $att_map['type'] = 'bank';
        $bank = model('member_attesta')->where($att_map)->find();
        if(!$bank) {
            $replace['{bank_status}'] = '未认证';
        } else {
            if($bank['status'] == 1) {
                $replace['{bank_status}'] = '已认证';
            } elseif($bank['status'] == 0) {
                $replace['{bank_status}'] = '认证中';
            } else {
                $replace['{bank_status}'] = '未认证';
            }
        }
        return str_replace(array_keys($replace), $replace, C('WECHAT_ACCOUNT_INFO'));
    }
    

    /* 待审核订单 */
    public function wechat_order_wait_check() {
        $sqlmap = array();
        $sqlmap['buyer_id'] = $this->userid;
        //-----------购物返利待审核-----------
        $sqlmap['act_mod'] = 'rebate';
        $sqlmap['status'] = 3;
        $replace['{rebate_num}'] = (int) model('order')->where($sqlmap)->count();
        //-----------免费试用待审核-----------
        $sqlmap['act_mod'] = 'trial';
        $replace['{trial_num}'] = (int) model('order')->where($sqlmap)->count();
        //-----------免费试用待确认-----------
        $sqlmap['status'] = 1;
        $replace['{trial_confirm}'] = (int) model('order')->where($sqlmap)->count();
        
        $replace['{rebate_alias}'] = model('activity_set')->where(array('key' => 'rebate_name'))->getField('value');
        $replace['{trial_alias}'] = model('activity_set')->where(array('key' => 'trial_name'))->getField('value');
        $replace['{url}'] = str_replace('wechat.php', 'index.php',U('member/profile/index', $this->params, '', TRUE));
        return str_replace(array_keys($replace), $replace, C('WECHAT_ORDER_WAIT_CHECK'));
    }
    
    
    
    /* 待填写订单号 */
    public function wechat_order_wait_fill() {
        $replace['{rebate_alias}'] = model('activity_set')->where(array('key' => 'rebate_name'))->getField('value');
        $replace['{trial_alias}'] = model('activity_set')->where(array('key' => 'trial_name'))->getField('value');
        
        $sqlmap = array();
        $sqlmap['buyer_id'] = $this->userid;
        $sqlmap['status'] = 2;
        //-----------购物返利-----------
        $sqlmap['act_mod'] = 'rebate';
        $replace['{rebate_num}'] = (int) model('order')->where($sqlmap)->count();
        //-----------免费试用-----------
        $sqlmap['act_mod'] = 'trial';
        $replace['{trial_num}'] = (int) model('order')->where($sqlmap)->count();
        $replace['{url}'] = str_replace('wechat.php', 'index.php',U('member/profile/index', $this->params, '', TRUE));
        return str_replace(array_keys($replace), $replace, C('WECHAT_ORDER_WAIT_FILL'));
    }
    
    /* 待填写试用报告 */
    public function wechat_order_wait_trial_report() {
        $replace['{trial_alias}'] = model('activity_set')->where(array('key' => 'trial_name'))->getField('value');
        $sqlmap = array();
        $sqlmap['buyer_id'] = $this->userid;
        $sqlmap['act_mod'] = 'trial';
        $sqlmap['status'] = 8;
        $replace['{num}'] = (int) model('order')->where($sqlmap)->count();
        $replace['{url}'] = str_replace('wechat.php', 'index.php',U('member/profile/index', $this->params, '', TRUE));
        return str_replace(array_keys($replace), $replace, C('WECHAT_ORDER_WAIT_TRIAL_REPORT'));
    }
    
    /* 待评价订单 */
    public function wechat_order_wait_report() {
        $replace['{rebate_alias}'] = model('activity_set')->where(array('key' => 'rebate_name'))->getField('value');
        //-----------已完成总数-----------
        $sqlmap = array();
        $sqlmap['buyer_id'] = $this->userid;
        $sqlmap['act_mod'] = 'rebate';
        $sqlmap['status'] = 7;
        $count = (int) model('order')->where($sqlmap)->count();
        //-----------已晒单总数-----------
        $report = 0;
        if($count > 0) {
            $sqlmap = array();
            $sqlmap['userid'] = $this->userid;
            $sqlmap['order_id'] = array("IN", $order_ids);
            $report = model('report')->where(array('userid' =>$this->userid))->count();
        }

        $replace['{num}'] = (int)$count - $report;
        $replace['{url}'] = str_replace('wechat.php', 'index.php',U('member/profile/index', $this->params, '', TRUE));
        return str_replace(array_keys($replace), $replace, C('WECHAT_ORDER_WAIT_REPORT'));
    }
    
    /* 申诉中订单 */
    public function wechat_order_wait_appeal() {
        $sqlmap = array();
        $sqlmap['buyer_id'] = $this->userid;
        $sqlmap['appeal_status'] = array("LT", 2);
        $num = model('appeal')->where($sqlmap)->count();
        $replace = array(
            '{num}' => $num
        );
        $replace['{url}'] = str_replace('wechat.php', 'index.php',U('member/profile/index', $this->params, '', TRUE));
        return str_replace(array_keys($replace), $replace, C('WECHAT_ORDER_WAIT_APPEAL'));
    }
    
    /* 返利新品 */
    public function wechat_product_rebate() {
        $sqlmap = array();
        $sqlmap['mod'] = 'rebate';
        $sqlmap['status'] = 1;
        $sqlmap['start_time'] = array("LT", NOW_TIME);
        $ids = model('product')->where($sqlmap)->limit(7)->order("id DESC")->getField('id', TRUE);
        $result = array();
        if($ids) {
            foreach($ids as $id) {
                $product = new \Product\Factory\product($id);
                $item['title'] = $product->product_info['title'];
                $item['picurl'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['thumb'];
                $item['Description'] = '';
               // $item['url'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['url'];
               // $item['url'] = str_replace("wechat.php","index.php",$item['url']);
               // $item['url'] = ((stripos($item['url'], '?') === FALSE) ? $item['url'].'?' : $item['url']).http_build_query($this->params);
                $item['url'] = $this->wap_url.'/#/tab/rebate/'.$id;
                $result[] = $item;
            }
        }
        return $result;
    }
    
    /* 试用新品 */
    public function wechat_product_trial() {
        $sqlmap = array();
        $sqlmap['mod'] = 'trial';
        $sqlmap['status'] = 1;
        $sqlmap['start_time'] = array("LT", NOW_TIME);
        $ids = model('product')->where($sqlmap)->limit(7)->order("id DESC")->getField('id', TRUE);
        $result = array();
        if($ids) {
            foreach($ids as $id) {
                $product = new \Product\Factory\product($id);
                $item['title'] = $product->product_info['title'];
                $item['picurl'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['thumb'];
                $item['Description'] = '';
                //$item['url'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['url'];
                //$item['url'] = str_replace("wechat.php","index.php",$item['url']);
                //$item['url'] = ((stripos($item['url'], '?') === FALSE) ? $item['url'].'?' : $item['url']).http_build_query($this->params);
                //移动端地址
                $item['url'] = $this->wap_url.'/#/tab/trial/'.$id;
                $result[] = $item;
            }
        }
        return $result;
    }
    
    public function wechat_product_postal() {
        $sqlmap = array();
        $sqlmap['mod'] = 'postal';
        $sqlmap['status'] = 1;
        $sqlmap['start_time'] = array("LT", NOW_TIME);
        $ids = model('product')->where($sqlmap)->limit(7)->order("start_time ASC")->getField('id', TRUE);
        $result = array();
        if($ids) {
            foreach($ids as $id) {
                $product = new \Product\Factory\product($id);
                $item['title'] = $product->product_info['title'];
                $item['picurl'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['thumb'];
                $item['Description'] = '';
                $item['url'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['url'];
                $item['url'] = str_replace("wechat.php","index.php",$item['url']);

                $item['url'] = ((stripos($item['url'], '?') === FALSE) ? $item['url'].'?' : $item['url']).http_build_query($this->params);
                $result[] = $item;
            }
        }
        return $result;         
    }
    
    /* 推荐商品 */
    public function wechat_product_commend() {
        $sqlmap = array();
        $sqlmap['isrecommend'] = 1;
        $sqlmap['status'] = 1;
        $sqlmap['start_time'] = array("LT", NOW_TIME);
        $ids = model('product')->where($sqlmap)->limit(7)->order("id DESC")->getField('id', TRUE);
        $result = array();
        if($ids) {
            foreach($ids as $id) {
                $product = new \Product\Factory\product($id);
                $item['title'] = $product->product_info['title'];
                $item['picurl'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['thumb'];
                $item['Description'] = '';
                //$item['url'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['url'];
                //$item['url'] = str_replace("wechat.php","index.php",$item['url']);
                //$item['url'] = ((stripos($item['url'], '?') === FALSE) ? $item['url'].'?' : $item['url']).http_build_query($this->params);
                if($product->product_info['mod'] == 'rebate'){
                    $item['url'] = $this->wap_url.'/#/tab/rebate/'.$id;
                }elseif($product->product_info['mod'] == 'trial'){
                    $item['url'] = $this->wap_url.'/#/tab/trial/'.$id;
                }elseif($product->product_info['mod'] == 'commission'){
                    $item['url'] = $this->wap_url.'/#/tab/commission/'.$id;
                }else{
                    $item['url'] = $this->web_url.$product->product_info['url'];
                }

                $result[] = $item;
            }
        }
        return $result;        
    }
    
    /* 最新商品 */
    public function wechat_product_news() {
        $sqlmap = array();
        $sqlmap['status'] = 1;
        $sqlmap['start_time'] = array("LT", NOW_TIME);
        $ids = model('product')->where($sqlmap)->limit(7)->order("id DESC")->getField('id', TRUE);
        $result = array();
        if($ids) {
            foreach($ids as $id) {
                $product = new \Product\Factory\product($id);
                $item['title'] = $product->product_info['title'];
                $item['picurl'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['thumb'];
                $item['Description'] = '';
             //   $item['url'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['url'];
              //  $item['url'] = str_replace("wechat.php","index.php",$item['url']);

               // $item['url'] = ((stripos($item['url'], '?') === FALSE) ? $item['url'].'?' : $item['url']).http_build_query($this->params);
                if($product->product_info['mod'] == 'rebate'){
                    $item['url'] = $this->wap_url.'/#/tab/rebate/'.$id;
                }elseif($product->product_info['mod'] == 'trial'){
                    $item['url'] = $this->wap_url.'/#/tab/trial/'.$id;
                }elseif($product->product_info['mod'] == 'commission'){
                    $item['url'] = $this->wap_url.'/#/tab/commission/'.$id;
                }else{
                    $item['url'] = $this->web_url.$product->product_info['url'];
                }
                $result[] = $item;
            }
        }
        return $result;          
    }
    
    /* 搜索产品 */
    public function wechat_product_search($keyword = '') {
        if(!$keyword) return FALSE;
        $sqlmap = array();
        $sqlmap['status'] = 1;
        $sqlmap['title|keyword'] = array("LIKE", "%".$keyword."%");
        $sqlmap['start_time'] = array("LT", NOW_TIME);
        $ids = model('product')->where($sqlmap)->limit(7)->order("start_time ASC")->getField('id', TRUE);
        $result = array();
        if($ids) {
            foreach($ids as $id) {
                $product = new \Product\Factory\product($id);
                $item['title'] = $product->product_info['title'];
                $item['picurl'] = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__.$product->product_info['thumb'];
                $item['Description'] = '';
                if($product->product_info['mod'] == 'rebate'){
                    $item['url'] = $this->wap_url.'/#/tab/rebate/'.$id;
                }elseif($product->product_info['mod'] == 'trial'){
                    $item['url'] = $this->wap_url.'/#/tab/trial/'.$id;
                }elseif($product->product_info['mod'] == 'commission'){
                    $item['url'] = $this->wap_url.'/#/tab/commission/'.$id;
                }else{
                    $item['url'] = $this->web_url.$product->product_info['url'];
                }

                $result[] = $item;
            }
        }
        return $result;         
    }
}