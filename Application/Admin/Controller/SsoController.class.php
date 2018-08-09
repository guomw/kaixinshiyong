<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class SsoController extends InitController {

    public $appid,$sso_address,$sso_key;
    public function _initialize() {
    	parent::_initialize();
        $this->db = D('Setting');
       
    }
    /* 1.1 SEO基本设置  [Adalways试用系统] */
    public function init() {


		include $this->admin_tpl('sso_list');
    }

    public function add(){
        $rand = random(40);
        include $this->admin_tpl('sso_add');
    }

    public function rand(){
        $rands = random(40);
        $this->success('获取成功',$rands);
    }



    public function update(){
        if (submitcheck('dosubmit')) {

            $info = $_POST['setting'];
            if (empty($info)) {
                $this->error('参数错误');
            }
            @file_put_contents(CONF_PATH.'sso.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>");
            $this->success('操作成功');
        } else {
            $this->error('请勿非法提交');
        }
    }


    /* 与支付平台进行通信链接*/
    public function tongxin(){
        
        if(c('sso_is_open') == 1){

          $ret= _get_send('tongxin',array('appid'=>1));
          if($ret == 1){
           $this->success('通信成功');
          }else{
            $this->error('通信失败！');

          }

        }else{

            $this->error('未开启通信');
        }

    }
    
    /*添加应用的时候 与云平台进行通信检测*/

    public function sso_chek($appid='',$sso_key='',$sso_address=''){

             $dada = I('param.');

              $this->appid = $appid;
              $this->sso_key =$sso_key;
              $this->sso_address = $sso_address;
              $ret = $this->sso_chek_ps_send('tongxin',$dada);
              
              if($ret == '1'){

                $this->success('通信检测成功！');
              }else{
                $this->error('与整合支付平台通信失败，请检查配置重新填写');
              }

    }



    /*只用于添加应用和修改应用的时候 与云平台进行通信检测*/
    public function sso_chek_ps_send($action,$data = null) {
        return $this->sso_chek_ps_post('tongxin',500000,$this->sso_chek_auth_data($data,$action));
    }

    /*只用于添加应用和修改应用的时候 与云平台进行通信检测*/
   public function sso_chek_auth_data($data,$action = '') {
        $s = $sep = '';
        foreach($data as $k => $v) {
            if(is_array($v)) {
                $s2 = $sep2 = '';
                foreach($v as $k2 => $v2) {
                        $s2 .= "$sep2{$k}[$k2]=".$this->sso_chek_ps_stripslashes($v2);
                    $sep2 = '&';
                }
                $s .= $sep.$s2;
            } else {
                $s .= "$sep$k=".$this->sso_chek_ps_stripslashes($v);
            }
            $sep = '&';
        }

        $auth_s ='type='.$action.'&v=trial&appid='.$this->appid.'&data='.urlencode($this->sso_chek_sys_auth($s));
        return $auth_s;
    }

    /*只用于添加应用和修改应用的时候 与云平台进行通信检测*/
     function sso_chek_ps_stripslashes($string) {
        !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
        if(MAGIC_QUOTES_GPC) {
            return stripslashes($string);
        } else {
            return $string;
        }
    }

        /*只用于添加应用和修改应用的时候 与云平台进行通信检测*/
    function sso_chek_sys_auth($string, $operation = 'ENCODE',$expiry = 0) {
        $ckey_length = 4;
        $key = $this->sso_key;
        $keya = md5(substr($key, 0, 20));
        $keyb = md5(substr($key, 20, 20));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(strtr(substr($string, $ckey_length), '-_', '+/')) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
        }
    }

    /*只用于添加应用和修改应用的时候 与云平台进行通信检测*/
     function sso_chek_ps_post($action, $limit = 0, $post = '',$log_id='',$is_post = true,$cookie = '', $ip = '', $timeout = 15, $block = true) {       $return = '';

        $url = $this->sso_address;
        $matches = parse_url($url);
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;
        $siteurl = $url; //第四处
        if($is_post) {
            $out = "POST $path HTTP/1.1\r\n";
            $out .= "Accept: */*\r\n";
            $out .= "Referer: ".$siteurl."\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n" ;
            $out .= 'Content-Length: '.strlen($post)."\r\n" ;
            $out .= "Connection: Close\r\n" ;
            $out .= "Cache-Control: no-cache\r\n" ;
            $out .= "Cookie: $cookie\r\n\r\n" ;
            $out .= $post ;
        } else {
            $out = "GET ".$path."&".$post." HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            $out .= "Referer: ".$siteurl."\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
        }
    
        $fp = @fsockopen($host,$port,$errno,$errstr,$timeout);
        if(!$fp){
            //标记当前记录需要二次请求
           if($is_post) model('xwpay_log')->where(array('id' =>$log_id))->setField(array('status' => 2));
            return '-1';
        } 

        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);

        if($status['timed_out']) return ''; 
        while (!feof($fp)) {
            if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n"))  break;              
        }
        
        $stop = false;
        while(!feof($fp) && !$stop) {
            $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
            $return .= $data;
            if($limit) {
                $limit -= strlen($data);
                $stop = $limit <= 0;
            }
        }
        @fclose($fp);

       

        //部分虚拟主机返回数值有误，暂不确定原因，过滤返回数据格式
        $return_arr = explode("\n", $return);

        //print_r($return_arr);

        if(isset($return_arr[1])) {
           if($is_post){
            $return = trim($return_arr[2]);
           }else{
            $return = trim($return_arr[1]);
           }

        }
        unset($return_arr);
        //请求成功更新数据库当前状态
        if($is_post) model('xwpay_log')->where(array('id' =>$log_id))->setField(array('status' => 1,'success'=>$return));

        return $return;
    }

}