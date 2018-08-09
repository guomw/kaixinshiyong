<?php
namespace Install\Controller;
use Think\Controller;
Class IndexController extends Controller {
    public function _initialize() {
        $this->lockfile = RUNTIME_PATH.'install.lock';
        if(phpversion() < '5.3.0') set_magic_quotes_runtime(0);
        if(phpversion() < '5.2.0') die('您的php版本过低，不能安装本软件，请升级到5.2.0或更高版本再安装，谢谢！');
        if (file_exists($this->lockfile)) die("您已经安装过本系统,如果需要重新安装，请删除 ".$this->lockfile." 文件！");
        $this->steps = array('database', 'admin', 'cache');
        $this->caches = array(
            'module'     => '模块缓存',
            'model'      => '模型缓存',
            'admin_role' => '管理员角色缓存',
            'setting'    => '站点设置缓存'
        );
    }

    /* 第一步：授权协议 */
    public function index() {
        $license = @file_get_contents(MODULE_PATH.'Data/license.txt');
    	include template('index');
    }

    /* 第二步：环境检测 */
    public function step_2() {
        //错误
        $err = 0;
        //mysql检测
        if (function_exists('mysql_connect')) {
            $mysql = '<span class="correct_span">&radic;</span> 已安装';
        } else {
            $mysql = '<span class="correct_span error_span">&radic;</span> 出现错误';
            $err++;
        }
        //上传检测
        if (ini_get('file_uploads')) {
            $uploadSize = '<span class="correct_span">&radic;</span> ' . ini_get('upload_max_filesize');
        } else {
            $uploadSize = '<span class="correct_span error_span">&radic;</span>禁止上传';
            $err++;
        }
        //session检测
        if (function_exists('session_start')) {
            $session = '<span class="correct_span">&radic;</span> 支持';
        } else {
            $session = '<span class="correct_span error_span">&radic;</span> 不支持';
            $err++;
        }

        //PHP内置函数检测
        $function = array(
            'mb_strlen' => function_exists('mb_strlen'),
            'curl_init' => function_exists('curl_init'),
            'GD' => extension_loaded('gd'),
            'pdo_mysql' => extension_loaded('pdo_mysql')
        );
        foreach ($function as $rs) {
            if ($rs == false) {
                $err++;
            }
        }

        //目录权限检测
        $folder = array(
            '/',
            '/~runtime/',
            str_replace(SITE_PATH, '', CONF_PATH),
            '/uploadfile/'
        );
        $dir = new \Common\Library\dir();
        $folderInfo = array();
        foreach ($folder as $dir) {
            $result = array(
                'dir' => $dir,
            );
            $path = SITE_PATH . $dir;
            //是否可读
            if (is_readable($path)) {
                $result['is_readable'] = '<span class="correct_span">&radic;</span>可读';
            } else {
                $result['is_readable'] = '<span class="correct_span error_span">&radic;</span>不可读';
                $err++;
            }
            //是否可写
            if (is_writable($path)) {
                $result['is_writable'] = '<span class="correct_span">&radic;</span>可写';
            } else {
                $result['is_writable'] = '<span class="correct_span error_span">&radic;</span>不可写';
                $err++;
            }
            $folderInfo[] = $result;
        }

    	include template('step_2');
    }

    /* 第三步：写入数据 */
    public function step_3() {
        $scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER["PHP_SELF"];
        $rootpath = @preg_replace("/\/(I|i)nstall\/index\.php(.*)$/", "/", $scriptName);
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $domain = empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        if ((int) $_SERVER['SERVER_PORT'] != 80) {
            $domain .= ":" . $_SERVER['SERVER_PORT'];
        }
        $domain = $sys_protocal . $domain . $rootpath;
        $parse_url = parse_url($domain);
        $parse_url['path'] = str_replace('install.php', '', $parse_url['path']);    	
    	include template('step_3');
    }

    /* 第四步：安装进度 */
    public function step_4() {
    	$data = json_encode($_POST);
    	include template('step_4');
    }

    /* 第五步：安装成功 */
    public function step_5() {
    	@file_put_contents($this->lockfile, 'ok');
        // 统计代码
        helpers('authorization');
        sendAuthorization('install');
    	include template('step_5');
    }

    /* 写入数据库 */
    public function install() {
        $n = I('n', 0);
        $step = I('step', 'database');
        $result = array('status' => 0, 'step' => $step, 'message' => '', 'n' => $n);
        if (!in_array($step, $this->steps) || $step == 'finish') {
            $result['status'] = 1;
            $result['message'] = '<li><span class="correct_span">&radic;</span>安装成功</li>';
            exit(json_encode($result));
        }
        if (!method_exists($this, $step)) {
            $result['status'] = -1;
            $result['message'] = '<li><span class="correct_span error_span">&radic;</span>安装过程出现致命错误，程序被迫中止。请重新<a href="http://git.oschina.net/ /cn. .framework.tpcms.git" target="_blank">获取最新版</a>后重试！</li>';
            exit(json_encode($result));
        }
        $return = $this->$step();
        $result = array_merge($result, $return);
        exit(json_encode($result));        
    }

    private function database() {
        $result = array();
        $n = I('n');
        $result['n'] = $n;   
        $dbHost = trim($_POST['dbhost']);
        $dbPort = trim($_POST['dbport']);
        $dbName = trim($_POST['dbname']);
        $dbHost = empty($dbPort) || $dbPort == 3306 ? $dbHost : $dbHost . ':' . $dbPort;
        $dbUser = trim($_POST['dbuser']);
        $dbPwd = trim($_POST['dbpw']);
        $dbPrefix = empty($_POST['dbprefix']) ? 'tpcms_' : trim($_POST['dbprefix']);

        $username = trim($_POST['manager']);
        $password = trim($_POST['manager_pwd']);
        //网站名称
        $site_name = addslashes(trim($_POST['sitename']));
        //描述
        $seo_description = trim($_POST['siteinfo']);
        //关键词
        $seo_keywords = trim($_POST['sitekeywords']);
        //测试数据
        $testdata = (int) $_POST['testdata'];
        //邮箱地址
        $siteemail = trim($_POST['manager_email']);

        /* 一、创建数据库 */
        $conn = @ mysql_connect($dbHost, $dbUser, $dbPwd);
        if (!$conn) {
            $result['status'] = -1;
            $result['message'] = "连接数据库失败!";
            return $result;
        }
        mysql_query("SET NAMES 'utf8'");
        $version = mysql_get_server_info($conn);
        if ($version < 5.0) {
            $result['status'] = -1;
            $result['message'] = '数据库版本太低!';
            return $result;
        }
        if (!mysql_select_db($dbName, $conn)) {
            //创建数据时同时设置编码
            if (!mysql_query("CREATE DATABASE IF NOT EXISTS `" . $dbName . "` DEFAULT CHARACTER SET utf8;", $conn)) {
                $result['message'] = '数据库 ' . $dbName . ' 不存在，也没权限创建新的数据库！';
                return $result;
            }
            if (empty($n)) {
                $result['n'] = 1;
                $result['message'] = "成功创建数据库:{$dbName}<br>";
                return $result;
            }
            mysql_select_db($dbName, $conn);
        }
        /* End */
        /* 二、创建数据表 */
        $sqldata = file_get_contents(MODULE_PATH . 'Data/table.sql');
        $sqlFormat = sql_split($sqldata, $dbPrefix);
        
        $counts = count($sqlFormat);
        for ($i = $n; $i < $counts; $i++) {
            $sql = trim($sqlFormat[$i]);
            if (strstr($sql, 'CREATE TABLE')) {
                preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
                mysql_query("DROP TABLE IF EXISTS `$matches[1]");
                $ret = mysql_query($sql);
                if ($ret) {
                    $message = '<li><span class="correct_span">&radic;</span>创建数据表 ' . $matches[1] . '，完成</li> ';
                } else {
                    $message = '<li><span class="correct_span error_span">&radic;</span>创建数据表 ' . $matches[1] . '，失败</li>';
                }
                $i++;
                $result['n'] = $i;
                $result['message'] = $message;
                return $result;
            } else {
                $ret = mysql_query($sql);
            }
        }
        /* 四、生成数据库配置文件 */
        $strConfig = file_get_contents(MODULE_PATH . 'Data/~database.inc');
        $strConfig = str_replace('#DB_HOST#', $dbHost, $strConfig);
        $strConfig = str_replace('#DB_NAME#', $dbName, $strConfig);
        $strConfig = str_replace('#DB_USER#', $dbUser, $strConfig);
        $strConfig = str_replace('#DB_PWD#', $dbPwd, $strConfig);
        $strConfig = str_replace('#DB_PORT#', $dbPort, $strConfig);
        $strConfig = str_replace('#DB_PREFIX#', $dbPrefix, $strConfig);
        @file_put_contents(CONF_PATH . 'database.php', $strConfig);

        mysql_query("UPDATE `{$dbPrefix}setting` SET  `value` = '$site_name' WHERE `key` ='webname'");
        mysql_query("UPDATE `{$dbPrefix}setting` SET  `value` = '$seo_description' WHERE `key` ='description'");
        mysql_query("UPDATE `{$dbPrefix}setting` SET  `value` = '$seo_keywords' WHERE `key` ='description'");
        mysql_query("UPDATE `{$dbPrefix}setting` SET  `value` = '$siteemail' WHERE `key` = 'admin_email'");
        mysql_query("UPDATE `{$dbPrefix}setting` SET  `value` = '".random(24)."' WHERE `key` = 'authkey'");
        mysql_query("UPDATE `{$dbPrefix}setting` SET  `value` = '".random(3)."' WHERE `key` ='cookie_prefix'");
        mysql_query("UPDATE `{$dbPrefix}setting` SET  `value` = '".random(3)."' WHERE `key` ='session_prefix'");

        $result['step'] = 'admin';
        $result['message'] = '<li><span class="correct_span">&radic;</span>数据库写入完成...</li>';
        return $result;
    }

    /* 创建管理员 */
    private function admin() {
        $result = array();
        $encrypt = random(6);
        $username = trim($_POST['manager']);
        $password = trim($_POST['manager_pwd']);        
        $password = md5(md5($password.$encrypt));
        $siteemail = trim($_POST['manager_email']);
        $data = array(
            'username' => $username,
            'password' => $password,
            'realname' => '',
            'encrypt'  => $encrypt,
            'roleid'   => 1,
            'email'    => $siteemail
        );
        if (D('Admin')->add($data)) {
            $result['status'] = $result['n'] = 0;    
            $result['message'] = '<li><span class="correct_span">&radic;</span>创建管理员账号成功...</li>';            
        } else {
            $result['status'] = -1;
            $result['message'] = '<li><span class="correct_span error_span">&radic;</span>创建管理员账号失败</li>';
        }
        $result['step'] = 'cache';
        return $result;
    }

    /* 更新各种缓存 */
    private function cache() {
        $result = array();
        $cacheid = I('n', 0);
        $cache_keys = array_keys($this->caches);
       
        if ($cacheid >= count($cache_keys)) {
            $result['step'] = 'finish';
            $result['message'] = '<li><span class="correct_span">&radic;</span>所有缓存更新完毕...</li>';  
            return $result;
        }
        if (isset($cache_keys[$cacheid]) && $this->caches[$cache_keys[$cacheid]]) {
            api('Cache/'.$cache_keys[$cacheid]);
            $cacheid++;
            $result['n'] = $cacheid;
            $result['message'] = '<li><span class="correct_span">&radic;</span>更新缓存 '.$this->caches[$cache_keys[$cacheid]].' 成功...</li>';
        }
        return $result;
    }

    public function public_check_authorization() {
        $authorization = new \Common\Library\authorization();
        if(!$authorization->check()) {
            $this->error($authorization->getError());
        } else {
            $this->success('授权域名检测成功');
        }
    }


    /* 检测数据库连接 */
    public function public_check_database() {
    	$info = I('post.');
        $dbHost = $info['dbHost'] . ':' . $info['dbPort'];
        $conn = @mysql_connect($dbHost, $info['dbUser'], $info['dbPwd']);
        if ($conn) {
            exit("1");
        } else {
            exit("0");
        }
    }

}