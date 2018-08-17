<?php
// +----------------------------------------------------------------------
// | 云划算试客系统 公用方法 公共函数
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://www.dealswill.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
/**
 * 获取系统信息
 */
helpers('time');
helpers('extention');
function get_sysinfo() {
    $sys_info['os']             = PHP_OS;
    $sys_info['zlib']           = function_exists('gzclose');//zlib
    $sys_info['safe_mode']      = (boolean) ini_get('safe_mode');//safe_mode = Off
    $sys_info['safe_mode_gid']  = (boolean) ini_get('safe_mode_gid');//safe_mode_gid = Off
    $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : L('no_setting');
    $sys_info['socket']         = function_exists('fsockopen') ;
    $sys_info['web_server']     = strpos($_SERVER['SERVER_SOFTWARE'], 'PHP')===false ? $_SERVER['SERVER_SOFTWARE'].'PHP/'.phpversion() : $_SERVER['SERVER_SOFTWARE'];
    $sys_info['phpv']           = phpversion(); 
    $sys_info['mysqlv'] = mysql_get_server_info();
    $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
    $sys_info['mysqlsize']      = M()->query("select round(sum(DATA_LENGTH/1024/1024)+sum(DATA_LENGTH/1024/1024),2) as db_length from information_schema.tables 
where table_schema='".C('DB_NAME')."'");
    $sys_info['mysqlsize']      = $sys_info['mysqlsize'][0]['db_length'];
    return $sys_info;
}

/**
 * 获取缓存
 * @author   <jason>
 * @param  string $file 文件名
 * @param  string $dir  目录名
 * @return mixed
 */
function getcache($file, $dir = NULL) {
	$fileName = (!is_null($dir) ? 'caches_'.$dir.'/'.$file : $file);
	return F($fileName);
}

/**
 * 设置缓存
 * @author   <jason>
 * @param  string 	$file 	缓存名
 * @param  mixed 	$value 	缓存值
 * @param  string 	$dir 	目录名
 * @return mixed
 */
function setcache($file, $value = NULL, $dir = NULL) {
	$fileName = (!is_null($dir) ? 'caches_'.$dir.'/'.$file : $file);
	return F($fileName, $value);
}

/* 删除缓存文件 */
function delcache($file, $dir = NULL) {
	$fileName = (!is_null($dir) ? 'caches_'.$dir.'/'.$file : $file);
	return F($fileName, NULL);
}

/**
 * 实例化数据模型
 * @param $name string 模型名
 * @param $layer 模型层
 * @return obj
 */
function model($name, $layer='') {
	return D(parse_name($name, 1), $layer);
}

/* 字符集转换 */
function diconv($str, $in_charset, $out_charset = CHARSET, $ForceTable = FALSE) {
	$in_charset = strtoupper($in_charset);
	$out_charset = strtoupper($out_charset);

	if(empty($str) || $in_charset == $out_charset) {
		return $str;
	}
	$out = '';
	if(!$ForceTable) {
		if(function_exists('iconv')) {
			$out = iconv($in_charset, $out_charset.'//IGNORE', $str);
		} elseif(function_exists('mb_convert_encoding')) {
			$out = mb_convert_encoding($str, $out_charset, $in_charset);
		}
	}
	return $out;
}

/* 获取文件名（处理乱码） */
function _basename($filename){  
     return preg_replace('/^.+[\\\\\\/]/', '', $filename);  
}

function dimplode($array) {
	if(!empty($array)) {
		$array = array_map('addslashes', $array);
		return "'".implode("','", is_array($array) ? $array : array($array))."'";
	} else {
		return 0;
	}
}

function _dfsockopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE, $encodetype  = 'URLENCODE', $allowcurl = TRUE, $position = 0) {
	$return = '';
	$matches = parse_url($url);
	$scheme = $matches['scheme'];
	$host = $matches['host'];
	$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
	$port = !empty($matches['port']) ? $matches['port'] : 80;

	if(function_exists('curl_init') && function_exists('curl_exec') && $allowcurl) {
		$ch = curl_init();
		$ip && curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: ".$host));
		curl_setopt($ch, CURLOPT_URL, $scheme.'://'.($ip ? $ip : $host).':'.$port.$path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($post) {
			curl_setopt($ch, CURLOPT_POST, 1);
			if($encodetype == 'URLENCODE') {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			} else {
				parse_str($post, $postarray);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postarray);
			}
		}
		if($cookie) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$data = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		if($errno || $status['http_code'] != 200) {
			return;
		} else {
			return !$limit ? $data : substr($data, 0, $limit);
		}
	}
	if($post) {
		$out = "POST $path HTTP/1.0\r\n";
		$header = "Accept: */*\r\n";
		$header .= "Accept-Language: zh-cn\r\n";
		$boundary = $encodetype == 'URLENCODE' ? '' : '; boundary='.trim(substr(trim($post), 2, strpos(trim($post), "\n") - 2));
		$header .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data$boundary\r\n";
		$header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$header .= "Host: $host:$port\r\n";
		$header .= 'Content-Length: '.strlen($post)."\r\n";
		$header .= "Connection: Close\r\n";
		$header .= "Cache-Control: no-cache\r\n";
		$header .= "Cookie: $cookie\r\n\r\n";
		$out .= $header.$post;
	} else {
		$out = "GET $path HTTP/1.0\r\n";
		$header = "Accept: */*\r\n";
		$header .= "Accept-Language: zh-cn\r\n";
		$header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$header .= "Host: $host:$port\r\n";
		$header .= "Connection: Close\r\n";
		$header .= "Cookie: $cookie\r\n\r\n";
		$out .= $header;
	}

	$fpflag = 0;
	if(!$fp = @fsocketopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout)) {
		$context = array(
			'http' => array(
				'method' => $post ? 'POST' : 'GET',
				'header' => $header,
				'content' => $post,
				'timeout' => $timeout,
			),
		);
		$context = stream_context_create($context);
		$fp = @fopen($scheme.'://'.($ip ? $ip : $host).':'.$port.$path, 'b', false, $context);
		$fpflag = 1;
	}

	if(!$fp) {
		return '';
	} else {
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		@fwrite($fp, $out);
		$status = stream_get_meta_data($fp);
		if(!$status['timed_out']) {
			while (!feof($fp) && !$fpflag) {
				if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
					break;
				}
			}

			if($position) {
				for($i=0; $i<$position; $i++) {
					$char = fgetc($fp);
					if($char == "\n" && $oldchar != "\r") {
						$i++;
					}
					$oldchar = $char;
				}
			}

			if($limit) {
				$return = stream_get_contents($fp, $limit);
			} else {
				$return = stream_get_contents($fp);
			}
		}
		@fclose($fp);
		return $return;
	}
}



	function Get($url)
	{
	if(function_exists('file_get_contents'))
	{
	$file_contents = file_get_contents($url);
	}
	else
	{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	}
	return $file_contents;
	}


/**
* 判断字符类型
* @param $str  string 原始字符
* @return array {
    1 : 汉字数字英文的混合字符串
    2 : 汉字数字的混合字符串
    3 : 汉字英文的混合字符串
    4 : 数字英文的混合字符串
    5 : 纯汉字
    6 : 纯数字
    7 : 纯英文
    8 : 其它
}
*/
function checkstr($str){
    $output='';
    $a=ereg('['.chr(0xa1).'-'.chr(0xff).']', $str);
    $b=ereg('[0-9]', $str);
    $c=ereg('[a-zA-Z]', $str);
    if($a && $b && $c){ $output='1';}
    elseif($a && $b && !$c){ $output='2';}
    elseif($a && !$b && $c){ $output='3';}
    elseif(!$a && $b && $c){ $output='4';}
    elseif($a && !$b && !$c){ $output='5';}
    elseif(!$a && $b && !$c){ $output='6';}
    elseif(!$a && !$b && $c){ $output='7';}
    else{ $output = 8;}
    return $output;
}

/**
 * 电子邮箱格式判断
 * @param  string $email 字符串
 * @return bool
 */
function isemail($email) {
	if (!empty($email)) {
	    return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $email);
	}
	return FALSE;
}

function is_mobile($string){
	if (!empty($string)) {
		return preg_match('/^(1(([3578][0-9])|(47)|[8][0126789]))\d{8}$/', $string);
	}
	return FALSE;
} 

function genTree($rows, $id='id', $pid='parentid', $son='cell') {  
    $items = array();  
    foreach ($rows as $row) $items[$row[$id]] = $row;  
    foreach ($items as $item) $items[$item[$pid]][$son][$item[$id]] = &$items[$item[$id]];  
    return isset($items[0][$son]) ? $items[0][$son] : array();
}

/**
 * 时间格式化
 * @param  int $timestamp 时间戳
 * @param  string  $format    格式
 * @return string
 */
function dgmdate($timestamp=0, $format='Y-m-d H:i:s') {
	$times = intval($timestamp);
	if(!$times) return false;
	return date($format,$times);
}


/**
 * 移动文件
 *
 * @param string $fileUrl
 * @param string $aimUrl
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function moveFile($fileUrl, $aimUrl, $overWrite = false) {
    if (!file_exists($fileUrl)) {
        return false;
    }
    if (file_exists($aimUrl) && $overWrite = false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite = true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
     createDir($aimDir);
    copy($fileUrl, $aimUrl);
    return true;
}

/**
 * 删除文件
 *
 * @param string $aimUrl
 * @return boolean
 */
function unlinkFile($aimUrl) {
    if (file_exists($aimUrl)) {
        unlink($aimUrl);
        return true;
    } else {
        return false;
    }
}

/* 
* 功能：循环检测并创建文件夹 
* 参数：$aimUrl 文件夹路径 
* 返回： 
*/ 

function createDir($aimUrl) {
    $aimUrl = str_replace('', '/', $aimUrl);
    $aimDir = '';
    $arr = explode('/', $aimUrl);
    $result = true;
    foreach ($arr as $str) {
        $aimDir .= $str . '/';
        if (!file_exists($aimDir)) {
            $result = mkdir($aimDir);
        }
    }
    return $result;
}

/**
 * 随机字符串
 *
 * @return bool
 * @author master@xuew.com
 **/
function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	if($numeric) {
		$hash = '';
	} else {
		$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		$length--;
	}
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

// 获取超全局变量
function getgpc($k, $type='GP') {
	$type = strtoupper($type);
	switch($type) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		default:
			if(isset($_GET[$k])) {
				$var = &$_GET;
			} else {
				$var = &$_POST;
			}
			break;
	}
	return isset($var[$k]) ? $var[$k] : NULL;
}

// 检测表单
function submitcheck($name, $type = 'P') {
	if(!getgpc($name)) {
		return FALSE;
	} else {
		$type = strtoupper($type);
		$fromhash = getgpc('fromhash');
		switch ($type) {
			case 'G':
				return IS_GET;
				break;
			case 'GP':
				return IS_GET || IS_POST;
				break;
			default:
				return IS_POST;
				break;
		}
		if (getgpc('fromhash') == session('FROMHASH')) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

/**
 * DZ在线中文分词
 * @param $title string 进行分词的标题
 * @param $content string 进行分词的内容
 * @param $encode string API返回的数据编码
 * @return array 得到的关键词数组
 */
function dz_segment($title = '', $content = '', $encode = 'utf-8'){
    if($title == ''){
        return false;
    }
    $title = rawurlencode(strip_tags($title));
    $content = strip_tags($content);
    if(strlen($content)>2400){ //在线分词服务有长度限制
        $content =  mb_substr($content, 0, 800, $encode);
    }
    $content = rawurlencode($content);
    $url = 'http://keyword.discuz.com/related_kw.html?title='.$title.'&content='.$content.'&ics='.$encode.'&ocs='.$encode;
    $xml_array=simplexml_load_file($url);//将XML中的数据,读取到数组对象中
    $result = $xml_array->keyword->result;
    $data = array();
    foreach ($result->item as $key => $value) {
        array_push($data, (string)$value->kw);
    }
    if(count($data) > 0){
        return $data;
    }else{
        return false;
    }
}

/**
 * 字符串加解密
 * @param  string  $string    原始字符串
 * @param  string  $operation 加解密类型
 * @param  string  $key       密钥
 * @param  integer $expiry    有效期
 * @author   <jason>
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key != '' ? $key : C('AUTHKEY'));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
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
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/**
 * 字符串添加反斜杠
 * @param  string|array  $string 字符串或数组
 * @param  bool $force
 * @return string|array
 */
function daddslashes($string) {
	if(!is_array($string)) return addslashes($string);
	foreach($string as $key => $val) $string[$key] = daddslashes($val);
	return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function dstripslashes($string) {
	if(!is_array($string)) return stripslashes($string);
	foreach($string as $key => $val) $string[$key] = dstripslashes($val);
	return $string;
}


/**
 * 数据htmlspecialchars
 * @param  mixed $string 原始数据
 * @param  mixed $flags
 * @return mixed
 */
function new_html_special_chars($string, $flags = null) {
	return dhtmlspecialchars($string, $flags);
}
function dhtmlspecialchars($string, $flags = null) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val, $flags);
		}
	} else {
		if($flags === null) {
			$string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
			if(strpos($string, '&amp;#') !== false) {
				$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
			}
		} else {
			if(PHP_VERSION < '5.4.0') {
				$string = htmlspecialchars($string, $flags);
			} else {
				if(strtolower(CHARSET) == 'utf-8') {
					$charset = 'UTF-8';
				} else {
					$charset = 'ISO-8859-1';
				}
				$string = htmlspecialchars($string, $flags, $charset);
			}
		}
	}
	return $string;
}

/**
 * 分页函数
 * @author   <jason>
 * @param  int $totalRows 总数
 * @param  int $listRows  分页条数
 * @return string
 */
function page($totalRows = 0, $listRows = 20) {
	$Page = new \Common\Library\Page($totalRows, $listRows);
	$Page->setConfig('header', '<a class="a1">%TOTAL_ROW%条</a>');
	$Page->setConfig('prev', '上一页');
	$Page->setConfig('next', '下一页');
	$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
	return $Page->show();
}


function v2_page($totalRows = 0, $listRows = 20) {
	$Page = new \Common\Library\Page($totalRows, $listRows);
	$Page->setConfig('header', '<span class="all clear"><b>共</b><b>%TOTAL_ROW%条</b></span> <span class="all clear" ><b>到第</b><input class="i1 fl" type="text" id="js_page_num"/><b>页</b></span>
					<span class="all"><a href="javascript:;" id="js_page" class="b1" style="cursor:pointer;">确定</a></span>');
	$Page->setConfig('prev', '上一页');
	$Page->setConfig('next', '下一页');
	$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE%');
	return $Page->v2_show();
}

function v2_page_3($totalRows = 0, $listRows = 20) {
	$Page = new \Common\Library\Page($totalRows, $listRows);
	$Page->setConfig('header', '<span class="all clear"><b>共</b><b>%TOTAL_ROW%条</b></span>');
	$Page->setConfig('prev', '上一页');
	$Page->setConfig('next', '下一页');
	$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
	return $Page->v2_show();
}

function v2_page_2($totalRows = 0, $listRows = 20) {
	$Page = new \Common\Library\Page($totalRows, $listRows);
	$Page->setConfig('header', '<span class="fl"><b class="cc"> %NOW_PAGE%</b>/<b>%TOTAL_PAGE%</b></span>');
	$Page->setConfig('prev', '<');
	$Page->setConfig('next', '>');
	$Page->setConfig('theme','%HEADER%  %UP_PAGE%  %DOWN_PAGE%');
	return $Page->v2_show_2();
}
/**
 * 
 * @param  $total 总数
 * @param  $curr默认当前页
 * @param  $prePage 每页显示的条数
 */
function showPage($total,$curr,$prePage) {
	if ($total <=0) {
		return ''; //如果总条目<=0 直接返回空字符串
	}
	$cnt = ceil($total / $prePage); //算总页数，进一取整

	//最终生成的URL里边必然有page=N
	$url = $_SERVER['REQUEST_URI'];
	$parse = parse_url($url); //把URL分析结果放在数组里
	//print_r($parse);
	//保证参数里边有page
	if (!isset($parse['query'])) {
		$parse['query'] = 'page=' . $curr;
	}
	//把query字符串分析成数组，再次确保有page选项
	parse_str($parse['query'],$parms);
	if (!array_key_exists('page', $parms)) {
		$parms['page'] = $curr;
	}
	//判断除了page之外，还有没有其他参数
	if (count($parms) == 1) {
		$url = $parse['path'] . '?';
	} else {
		unset($parms['page']);
		$url = $parse['path'] . '?' . http_build_query($parms) . '&';
	}

	//echo $url
	$prev = $curr - 1;
	$next = $curr + 1;
	//首页
	//$indexLink = '<a href="' . $url .'page=' . 1 . '" class="prev textAlgin1 color3 border_ddd floatLeft" >首页</a>';
	//上一页
	if ($prev < 1) {
		$prevLink = '';
	}else {
		$prevLink = '<a href="' . $url .'page=' . $prev . '" class="prev textAlgin1 color3 border_ddd floatLeft" > < </a>';
	}
	//下一页
	if ($next > $cnt) {
		$nextLink = '';
	}else {
		$nextLink = '<a href="' . $url .'page=' . $next . '" class="next textAlgin1 color3 border_ddd floatLeft displayB" >  > </a>';
	}
	//尾页
	//$lastLink = '<a href="' . $url .'page=' . $cnt . '">尾页</a>';
	//echo $indexLink.'  '.$prevLink.'  '.$nextLink .'  '.$lastLink;
	//上一页，1 2 3 4 5 下一页

	$start = $curr - (5-1)/2; //计算左侧开始的页码
	$end = $curr + (5-1)/2;    //计算右侧开始的页码
	//如果左侧的页面，已经小于1，则把小于1 的部分补到右侧
	if ($start < 1) {
		$end += (1 - $start);
		$start = 1; //修改start = 1

		if ($end > $cnt) {
			$end  = $cnt;
		}
	}

	//把右侧超出的部分，补到左边
	if ($end > $cnt) {
		$start -= ($end - $cnt);
		$end = $cnt;

		if ($start < 1) {
			$start = 1;
		}
	}
	//循环出页码数
	$pageStr = '';
	for ($i=$start; $i <= $end ; $i++) {
		//当前页
		if ($i == $curr) {
			$pageStr .= '<a href="' . $url . 'page=' . $i . '" class="textAlgin1 color3 border_ddd floatLeft displayB border active">' . $i . '</a>';
			continue;
		}

		$pageStr .= '<a href="' . $url . 'page=' . $i . '" class="textAlgin1 color3 border_ddd floatLeft displayB border">' . $i . '</a>';
	}
	return $prevLink.$pageStr.$nextLink; 
}

function agent_pages($totalRows = 0, $listRows = 20) {
	$Page = new \Common\Library\Page($totalRows, $listRows);
	$Page->setConfig('header', '<p class="fl gong pd16">共<span>%TOTAL_ROW%</span>条</p>
	<div class="fl ">
		<span class="fl pd16">
		到第
		</span>
		<input class="fl pd16 line14 spanipt" type="text" name="js_page_num" id="" value="" />
		<span class="fl pd16">页</span>
		<div class="fl diviput">
			<input class="sbinput " type="button" name="" id="js_page"  value="确定" />
		</div><div class="fl numbermainz ">');
	$Page->setConfig('prev', '上一页');
	$Page->setConfig('next', '下一页');
	$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE%');
	return $Page->agent_show();
}



/**
 * 分页函数
 *
 * @param $num 信息总数
 * @param $perpage 每页显示数
 * @param $curr_page 当前分页
 * @param $urlrule URL规则
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 分页
 */
function pages($num, $perpage = 20, $curr_page, $urlrule = '', $array = array(),$setpages = 6) {
	if(defined('URLRULE') && $urlrule == '') {
		$urlrule = URLRULE;
		$array = $GLOBALS['URL_ARRAY'];
	} elseif($urlrule == '') {
		$urlrule = url_par('page={$page}');
	}
	$multipage = '';
	if($num > $perpage) {
		$page = $setpages+1;
		$offset = ceil($setpages/2-1);
		$pages = ceil($num / $perpage);
		if (defined('IN_ADMIN') && !defined('PAGES')) define('PAGES', $pages);
		$from = $curr_page - $offset;
		$to = $curr_page + $offset;
		$more = 0;
		if($page >= $pages) {
			$from = 2;
			$to = $pages-1;
		} else {
			if($from <= 1) {
				$to = $page-1;
				$from = 2;
			}  elseif($to >= $pages) {
				$from = $pages-($page-2);
				$to = $pages-1;
			}
			$more = 1;
		}
		$multipage .= '<a class="page-wa">'.$num.L('page_item').'</a>';
		
		if($curr_page>0) {
			$multipage .= '<a href="'.pageurl($urlrule, $curr_page-1, $array).'" class="page-wa">'.L('previous').'</a>';
			if($curr_page==1) {
				$multipage .= '<span>1</span>';
			} elseif($curr_page > 6 && $more) {
				$multipage .= '<a href="'.pageurl($urlrule, 1, $array).'">1</a>..';
			} else {
				$multipage .= '<a href="'.pageurl($urlrule, 1, $array).'">1</a>';
			}
		}
		for($i = $from; $i <= $to; $i++) {
			if($i != $curr_page) {
				$multipage .= '<a href="'.pageurl($urlrule, $i, $array).'">'.$i.'</a>';
			} else {
				$multipage .= '<span>'.$i.'</span>';
			}
		}


		if($curr_page<$pages) {
			if($curr_page<$pages-5 && $more) {
				$multipage .= '<em>...</em><a href="'.pageurl($urlrule, $pages, $array).'">'.$pages.'</a><a href="'.pageurl($urlrule, $curr_page+1, $array).'" class="page-wa">'.L('next').'</a>';
			} else {
				$multipage .= '<a href="'.pageurl($urlrule, $pages, $array).'">'.$pages.'</a><a href="'.pageurl($urlrule, $curr_page+1, $array).'" class="page-wa">'.L('next').'</a>';
			}
		} elseif($curr_page==$pages) {
			$multipage .= '<span>'.$pages.'</span><a href="'.pageurl($urlrule, $curr_page, $array).'" class="page-wa">'.L('next').'</a>';
		} else {
			$multipage .= '<a href="'.pageurl($urlrule, $pages, $array).'">'.$pages.'</a><a href="'.pageurl($urlrule, $curr_page+1, $array).'" class="page-wa">'.L('next').'</a>';
		}
	}
	return $multipage;
}
/**
 * 返回分页路径
 *
 * @param $urlrule 分页规则
 * @param $page 当前页
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 完整的URL路径
 */
function pageurl($urlrule, $page, $array = array()) {
	if(strpos($urlrule, '~')) {
		$urlrules = explode('~', $urlrule);
		$urlrule = $page < 2 ? $urlrules[0] : $urlrules[1];
	}
	$findme = array('{$page}');
	$replaceme = array($page);
	if (is_array($array)) foreach ($array as $k=>$v) {
		$findme[] = '{$'.$k.'}';
		$replaceme[] = $v;
	}
	$url = str_replace($findme, $replaceme, $urlrule);
	$url = str_replace(array('http://','//','~'), array('~','/','http://'), $url);
	return remove_xss($url);
}

/**
 * URL路径解析，pages 函数的辅助函数
 *
 * @param $par 传入需要解析的变量 默认为，page={$page}
 * @param $url URL地址
 * @return URL
 */
function url_par($par, $url = '') {
	if($url == '') $url = get_url();
	$pos = strpos($url, '?');
	if($pos === false) {
		$url .= '?'.$par;
	} else {
		$querystring = substr(strstr($url, '?'), 1);
		parse_str($querystring, $pars);
		$query_array = array();
		foreach($pars as $k=>$v) {
			if($k != 'page') $query_array[$k] = $v;
		}
		$querystring = http_build_query($query_array).'&'.$par;
		$url = substr($url, 0, $pos).'?'.$querystring;
	}
	return $url;
}

/**
 * 获取当前页面完整URL地址
 */
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
	$path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
* 将字符串转换为数组
*
* @param	string	$data	字符串
* @return	array	返回数组格式，如果，data为空，则返回空数组
*/
function string2array($data = '') {
	if($data == '' || !isset($data)) return array();
	@eval("\$array = $data;");
	return $array;
}
/**
* 将数组转换为字符串
*
* @param	array	$data		数组
* @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
* @return	string	返回字符串，如果，data为空，则返回空
*/
function array2string($data, $isformdata = 1) {
	if($data == '') return '';
	if($isformdata) $data = dstripslashes($data);
	return var_export($data, TRUE);
}

/**
 * 生成上传附件验证
 * @param $args   参数
 * @param $operation   操作类型(加密解密)
 */
function upload_key($args) {
	$pc_auth_key = md5(C('AUTH_KEY').$_SERVER['HTTP_USER_AGENT']);
	$authkey = md5($args.$pc_auth_key);
	return $authkey;
}

/**
 * 自动纠正URL地址格式
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function _froatURI ($url) {
	$url = str_replace("：", ":", $url);
	if(stripos($url,"https://") === 0) {
		return $url;
	} else {
		if(stripos($url,"http://http://") === 0) {
			$url = str_replace("http://http://", "", $url);
		}
		if(stripos($url,"http://") === false) {
			$url = 'http://'.$url;
		}		
	}
	return $url;
}

/**
 * 获取平阴
 * @param  string  $str  转换的字符串
 * @param  bool $ishead  是否为首字母
 * @param  bool $isclose 是否释放资源
 */
function getPinyin($str, $ishead = FALSE, $isclose = TRUE) {
    global $pinyins;
    $restr = '';
    $str = trim(iconv('UTF-8', 'GBK', $str));
    $slen = strlen($str);
    if($slen < 2) {
        return $str;
    }
    if(count($pinyins) == 0) {
        $fp = fopen(COMMON_PATH.'/Data/pinyin.dat', 'r');
        while(!feof($fp)) {
            $line = trim(fgets($fp));
            $pinyins[$line[0].$line[1]] = substr($line, 3, strlen($line)-3);
        }
        fclose($fp);
    }    

    for($i=0; $i<$slen; $i++) {
        if(ord($str[$i])>0x80) {
            $c = $str[$i].$str[$i+1];
            $i++;
            if(isset($pinyins[$c])) {
                if($ishead=== FALSE) {
                    $restr .= $pinyins[$c];
                } else {
                    $restr .= $pinyins[$c][0];
                }
            } else {
                $restr .= "_";
            }
        } else if( preg_match("/[a-z0-9]/i", $str[$i])) {
            $restr .= $str[$i];
        } else {
            $restr .= "_";
        }
    }
    if($isclose === TRUE) {
        unset($pinyins);
    }
    return $restr;
}

/**
 * IE浏览器判断
 * @author   <jason>
 */
function is_ie() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
	if(strpos($useragent, 'msie ') !== false) return true;
	return false;
}

/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return mixed
 */
 function trim_script($str) {
	if(is_array($str)){
		foreach ($str as $key => $val){
			$str[$key] = trim_script($val);
		}
 	}else{
 		$str = preg_replace ( '/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str );
		$str = str_replace ( 'javascript:', 'javascript：', $str );
 	}
	return $str;
}

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	$length = intval($length-strlen($dot)-$length/3);
	$n = $tn = $noc = 0;
	while($n < strlen($string)) {
		$t = ord($string[$n]);
		if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
			$tn = 1; $n++; $noc++;
		} elseif(194 <= $t && $t <= 223) {
			$tn = 2; $n += 2; $noc += 2;
		} elseif(224 <= $t && $t <= 239) {
			$tn = 3; $n += 3; $noc += 2;
		} elseif(240 <= $t && $t <= 247) {
			$tn = 4; $n += 4; $noc += 2;
		} elseif(248 <= $t && $t <= 251) {
			$tn = 5; $n += 5; $noc += 2;
		} elseif($t == 252 || $t == 253) {
			$tn = 6; $n += 6; $noc += 2;
		} else {
			$n++;
		}
		if($noc >= $length) {
			break;
		}
	}
	if($noc > $length) {
		$n -= $tn;
	}
	$strcut = substr($string, 0, $n);
	$strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
	return $strcut.$dot;
}

/**
 * 生成标题样式
 * @param $style   样式
 * @param $html    是否显示完整的STYLE
 */
function title_style($style, $html = 1) {
	$str = '';
	if ($html) $str = ' style="';
	$style_arr = explode(';',$style);
	if (!empty($style_arr[0])) $str .= 'color:'.$style_arr[0].';';
	if (!empty($style_arr[1])) $str .= 'font-weight:'.$style_arr[1].';';
	if ($html) $str .= '" ';
	return $str;
}

/**
 * 组装生成ID号
 * @param $modules 模块名
 * @param $contentid 内容ID
 */
function id_encode($modules,$contentid) {
	return urlencode($modules.'-'.$contentid);
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	return $string;
}

/**
 * 获取内容地址
 * @param $catid   栏目ID
 * @param $id      文章ID
 * @param $allurl  是否以绝对路径返回
 */
function go($catid, $id, $allurl = 0) {
	$category = getcache('category', 'commons');
	$models = getcache('model', 'commons');	
	$id = (int) $id;
	$catid = (int) $catid;
	if(!$id || !isset($category[$catid])) return '';
	$modelid = $category[$catid]['modelid'];
	if(!$modelid) return '';
	$db = M($models[$modelid]['tablename']);
	$r = $db->getById($id, 'url');
	if (!empty($allurl)) {
		if (strpos($r['url'], '://')===false) {
			if (strpos($category[$catid]['url'], '://') === FALSE) {
				$site = siteinfo($category[$catid]['siteid']);
				$r['url'] = substr($site['domain'], 0, -1).$r['url'];
			} else {
				$r['url'] = $category[$catid]['url'].$r['url'];
			}
		}
	}
	return $r['url'];
}

function dheader($string, $replace = true, $http_response_code = 0) {
	$islocation = substr(strtolower(trim($string)), 0, 8) == 'location';
	$string = str_replace(array("\r", "\n"), array('', ''), $string);
	if(empty($http_response_code) || PHP_VERSION < '4.3' ) {
		@header($string, $replace);
	} else {
		@header($string, $replace, $http_response_code);
	}
	if($islocation) {
		exit();
	}
}

function dhtml_entity_decode($string) {
	return html_entity_decode($string,ENT_QUOTES, 'utf-8');
}

function dhtmlentities($string) {
	return htmlentities($string,ENT_QUOTES, 'utf-8');
}

  /**
   * edit编辑器转义
   * @author   <master@ .cn>
   * @date：2016-07-12
   * @version：1.0
   */
function dhtmlreplae($html){
  
   $html = str_replace('\\', '', $html);
   $html = htmlspecialchars_decode($html);
  

   return $html;
}

/**
 * 当前路径
 * 返回指定栏目路径层级 
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function catpos($catid, $symbol='', $type = '',$mod=''){
	$symbol = $symbol ? $symbol : ' > ';
	$category_arr = array();
	$cache_name = ($type) ? $type.'_category':'category';
	$category_arr = getcache($cache_name, 'commons');
	if(!isset($category_arr[$catid])) return '';
	$pos = '';
	$siteurl = '';
	$arrparentid = array_filter(explode(',', $category_arr[$catid]['arrparentid'].','.$catid));
	foreach($arrparentid as $catid) {
		if (strtolower($type) == 'product') {
			$rewrite = new \Common\Library\rewrite();
			if ($mod != '') {
				$url = '/index.php?m=product&c=Index&a=lists&mod='.$mod.'&catid='.$catid;
			}else{
				$url = $rewrite->category($catid);
			}
		} else {
			$url = U('Document/Index/lists', array('catid' => $catid));
		}
		if(strpos($url, '://') === false) $url = $siteurl.$url;
		$pos .= '<a href="'.$url.'">'.$category_arr[$catid]['catname'].'</a>'.$symbol;
	}
	return $pos;
}

/**
 * 当前路径
 * 返回指定栏目路径搜索wap端
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function catpos2($catid, $symbol='', $type = ''){
    $symbol = $symbol ? $symbol : ' > ';
    $category_arr = array();
    $cache_name = ($type) ? $type.'_category':'category';
    $category_arr = getcache($cache_name, 'commons');
    if(!isset($category_arr[$catid])) return '';
    $pos = '';
    $siteurl = '';
    $arrparentid = array_filter(explode(',', $category_arr[$catid]['arrparentid'].','.$catid));
    foreach($arrparentid as $catid) {
        //$url = $category_arr[$catid]['url'];
        if (strtolower($type) == 'product') {
            $rewrite = new \Common\Library\rewrite();
//            $url = $rewrite->category($catid);
            $url = U('Search/Index/index', array('catid' => $catid));
        } else {
            $url = U('Search/Index/index', array('catid' => $catid));
        }
        if(strpos($url, '://') === false) $url = $siteurl.$url;
        $pos .= '<a href="'.$url.'">'.$category_arr[$catid]['catname'].'</a>'.$symbol;
    }
    return $pos;
}

/**
 * 获取子栏目
 * @param $parentid 父级id
 * @param $type 栏目类型
 * @param $self 是否包含本身 0为不包含
 * @param $siteid 站点Id
 */
function subcat($parentid = NULL, $type = NULL,$self = '0') {
	$category = getcache('category', 'commons');
	foreach($category as $id=>$cat) {
		$cat['url'] = U('Document/Index/lists', array('catid' => $cat['catid']));
		if(($parentid === NULL || $cat['parentid'] == $parentid) && ($type === NULL || $cat['type'] == $type)) $subcat[$id] = $cat;
		if($self == 1 && $cat['catid'] == $parentid && !$cat['child'])  $subcat[$id] = $cat;
	}
	return $subcat;
}

/**
 * 嵌入点
 * @param  string $hookid 嵌入点名称
 * @param  string $param  传递的参数
 */
function runhook($hookid, $param) {
	if (empty($hookid)) return FALSE;
	return A('Common/Hook', 'Api')->run($hookid,$param); 
}

/**
 * 生成指定尺寸缩略图
 * @author   <jason>
 */
function thumb($thumb, $width, $height) {
	return $thumb;
}

/**
 * 加载小助手
 * @author   <jason>
 */
function helpers($file = '', $module = '') {
	if (empty($file)) return FALSE;
	$dir = COMMON_PATH.'Helpers/';
	if (is_array($file)) {
		foreach ($file as $f) {
			require_cache($dir.$file.'.helper.php');
		}
	} else {
		require_cache($dir.$file.'.helper.php');
	}
}

/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * 返回附件类型图标
 * @param $file 附件名称
 * @param $type png为大图标，gif为小图标
 */
function file_icon($file,$type = 'png') {
	$ext_arr = array('doc','docx','ppt','xls','txt','pdf','mdb','jpg','gif','png','bmp','jpeg','rar','zip','swf','flv');
	$ext = fileext($file);
	if($type == 'png') {
		if($ext == 'zip' || $ext == 'rar') $ext = 'rar';
		elseif($ext == 'doc' || $ext == 'docx') $ext = 'doc';
		elseif($ext == 'xls' || $ext == 'xlsx') $ext = 'xls';
		elseif($ext == 'ppt' || $ext == 'pptx') $ext = 'ppt';
		elseif ($ext == 'flv' || $ext == 'swf' || $ext == 'rm' || $ext == 'rmvb') $ext = 'flv';
		else $ext = 'do';
	}
	if(in_array($ext,$ext_arr)) return 'static/images/ext/'.$ext.'.'.$type;
	else return 'static/images/ext/blank.'.$type;
}

/**
 * 执行SQL语句
 * @param  string $sqlquery SQL语句，支持多条
 * @return bool
 */
function sqlexecute($sqlquery = '') {
	if(empty($sqlquery)) return FALSE;
	$sqlquery = str_replace('prefix_', C('DB_PREFIX'), $sqlquery);
	$version = M()->query("select version() as v;");
	$version = intval($version[0]['v']);
	if($version > '4.1' && C('DEFAULT_CHARSET')) {
	    $sqlquery = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8;", $sqlquery);
	}            
	$sqlquery = str_replace("\r", "\n", $sqlquery);
	$queriesarray = explode(";\n", trim($sqlquery));
	foreach ($queriesarray as $query) {
		if (substr($query, 0, 1) != '#' && substr($query, 0, 1) != '-') {
			M()->execute($query);
		}
	}
	return TRUE;
}

/**
 * 获取数据库版本
 * @return
 */
function getDbVersion() {
	$version = M()->query("select version() as v;");
	return intval($version[0]['v']);
}

/**
 * 加载模板
 * @param  string $controller   模块名
 * @param  string $action 		方法名
 * @param  string $style    	风格名
 * @return string
 */
function template($file, $module = '', $style = '') {
	$module = empty($module) ? strtolower(MODULE_NAME) : trim($module);
	$file = empty($file) ? strtolower(ACTION_NAME) : trim($file);	
	$_default_style = DEFAULT_THEME;
	$_default_theme = DEFAULT_THEME;
	$_style = ($_default_style) ? $_default_style : $_default_theme;
	$style = (empty($style)) ? $_style : 'cloud';
	$template = template_file($module, $file, $style);
	$tpl = new \Common\Library\template();
	$tmplCacheFile = $tpl->loadTemplate($template);
	return $tmplCacheFile;
}

/**
 * 获取模板文件
 * @author   <jason>
 */
function template_file($module = '', $file = '', $style = 'default') {
	$dep = C('TMPL_FILE_DEPR');
	$suffix = C('TMPL_TEMPLATE_SUFFIX');
	$tmpdir = C('VIEW_PATH').$style;
	$tplfile = $tmpdir.$dep.$module.$dep.$file.$suffix;
	/* 模板不存在时取默认模板 */
	if ($style != 'default' && !file_exists ($tplfile)) {
		$tplfile = C('VIEW_PATH').'cloud'.$dep.$module.$dep.$file.$suffix;
	}
	$tplfile = (!file_exists($tplfile)) ? MODULE_PATH.'Templates/'.$file.$suffix : $tplfile;
	if (!file_exists ($tplfile)) {
		die($tplfile.' is not exists!');
	}
	return $tplfile;
}


function birthday($birthday){

	$age = date('Y', time()) - date('Y', strtotime($birthday)) - 1;  
	if (date('m', time()) == date('m', strtotime($birthday))){  
	  
	    if (date('d', time()) > date('d', strtotime($birthday))){  
	    $age++;  
	    }  
	}elseif (date('m', time()) > date('m', strtotime($birthday))){  
	    $age++;  
	}  
	return $age;  
}


/**
 * 分页函数
 * 
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $pageurls 链接地址
 * @return 分页
 */
function content_pages($num, $curr_page, $pageurls) {
	$multipage = '';
	$page = 11;
	$offset = 4;
	$pages = $num;
	$from = $curr_page - $offset;
	$to = $curr_page + $offset;
	$more = 0;
	if($page >= $pages) {
		$from = 2;
		$to = $pages-1;
	} else {
		if($from <= 1) {
			$to = $page-1;
			$from = 2;
		} elseif($to >= $pages) {
			$from = $pages-($page-2);
			$to = $pages-1;
		}
		$more = 1;
	}
	if($curr_page > 0) {
		$perpage = $curr_page == 1 ? 1 : $curr_page-1;
		$multipage .= '<a class="a1" href="'.$pageurls[$perpage][0].'">上一页</a>';
		if($curr_page==1) {
			$multipage .= ' <span>1</span>';
		} elseif($curr_page>6 && $more) {
			$multipage .= ' <a href="'.$pageurls[1][0].'">1</a>..';
		} else {
			$multipage .= ' <a href="'.$pageurls[1][0].'">1</a>';
		}
	}
	for($i = $from; $i <= $to; $i++) {
		if($i != $curr_page) {
			$multipage .= ' <a href="'.$pageurls[$i][0].'">'.$i.'</a>';
		} else {
			$multipage .= ' <span>'.$i.'</span>';
		}
	}
	if($curr_page<$pages) {
		if($curr_page<$pages-5 && $more) {
			$multipage .= ' ..<a href="'.$pageurls[$pages][0].'">'.$pages.'</a> <a class="a1" href="'.$pageurls[$curr_page+1][0].'">下一页</a>';
		} else {
			$multipage .= ' <a href="'.$pageurls[$pages][0].'">'.$pages.'</a> <a class="a1" href="'.$pageurls[$curr_page+1][0].'">下一页</a>';
		}
	} elseif($curr_page==$pages) {
		$multipage .= ' <span>'.$pages.'</span> <a class="a1" href="'.$pageurls[$curr_page][0].'">下一页</a>';
	}
	return $multipage;
}


/**
 * 生成SEO
 * @param $catid        栏目ID
 * @param $title        标题
 * @param $description  描述
 * @param $keyword      关键词
 */
function seo($catid = '', $title = '', $description = '', $keyword = '', $type = '') {
	if (!empty($title)) $title = strip_tags($title);
	if (!empty($description)) $description = strip_tags($description);
	if (!empty($keyword)) $keyword = str_replace(' ', ',', strip_tags($keyword));
	$cat = array();
	if (!empty($catid)) {
		$cache_name = ($type) ? $type.'_category':'category';
		$categorys = getcache($cache_name, 'commons');
		$cat = $categorys[$catid];
		$cat['setting'] = string2array($cat['setting']);
	}	
	$site_title = C('SITE_TITLE');
	$site_description = C('description');
	$seo['site_title'] = !empty($site_title) ? $site_title : C('WEBNAME');
	$seo['keyword'] = !empty($keyword) ? $keyword : C('keyword');
	$seo['description'] = isset($description) && !empty($description) ? $description : (isset($cat['setting']['meta_description']) && !empty($cat['setting']['meta_description']) ? $cat['setting']['meta_description'] : (isset($site_description) && !empty($site_description) ? $site_description : ''));
	$seo['title'] =  (isset($title) && !empty($title) ? $title.' - ' : '').(isset($cat['setting']['meta_title']) && !empty($cat['setting']['meta_title']) ? $cat['setting']['meta_title'].' - ' : (isset($cat['catname']) && !empty($cat['catname']) ? $cat['catname'].' - ' : ''));
	foreach ($seo as $k=>$v) {
		$seo[$k] = str_replace(array("\n","\r"),	'', $v);
	}	
	return $seo;
}
/**
 * 检测模型是否存在
 * @author   <jason>
 * @param  string $module 模型名称
 * @return bool
 */
function module_exists($module = '') {
	if (empty($module)) return FALSE;
	$modules = getcache('module', 'commons');
	$module = ucwords($module);
	if(in_array($module, array('Api', 'Common'))) return TRUE;
	return (is_array($modules) && isset($modules[$module]));
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.phpcms.cn'));
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '') {
	if(defined('IN_ADMIN')) {
		include parent::$admin->admin_tpl('showmessage', 'admin');
	} else {
		include template('showmessage', 'common');
	}
	exit;
}


/**
 * 获取可用模板列表
 * @author   <jason>
 */
function template_list($disable = 0) {
	$list = glob(TPL_PATH.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
	$arr = $template = array();
	foreach ($list as $key => $v) {
		$dirname = basename($v);
		if (file_exists($v.DIRECTORY_SEPARATOR.'config.php')) {
			$arr[$key] = include $v.DIRECTORY_SEPARATOR.'config.php';
			if (!$disable && isset($arr[$key]['disable']) && $arr[$key]['disable'] == 1) {
			}
		} else {
			$arr[$key]['name'] = $dirname;
		}
		$arr[$key]['dirname']=$dirname;
	}
	return $arr;
}

/**
 * 创建密码
 * @param  string $password 原始密码
 * @param  string $encrypt  加密因子
 * @return string
 */
function create_password($password ='', $encrypt = '') {
	return md5(md5($password.$encrypt));
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars = array()){
    $array     = explode('/',$name);
    $method    = array_pop($array);
    $classname = array_pop($array);
    $module    = $array? array_pop($array) : 'Common';
    $callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
    if(is_string($vars)) {
        $vars = explode(",", $vars);
    }
    return call_user_func_array($callback,$vars);
}

/**
 * 检查id是否存在于数组中
 *
 * @param $id
 * @param $ids
 * @param $s
 */
function check_in($id, $ids = '', $s = ',') {
	if(!$ids) return false;
	$ids = explode($s, $ids);
	return is_array($id) ? array_intersect($id, $ids) : in_array($id, $ids);
}

/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) { 
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

    $parm = array_merge($parm1, $parm2); 

	for ($i = 0; $i < sizeof($parm); $i++) { 
		$pattern = '/'; 
		for ($j = 0; $j < strlen($parm[$i]); $j++) { 
			if ($j > 0) { 
				$pattern .= '('; 
				$pattern .= '(&#[x|X]0([9][a][b]);?)?'; 
				$pattern .= '|(&#0([9][10][13]);?)?'; 
				$pattern .= ')?'; 
			}
			$pattern .= $parm[$i][$j]; 
		}
		$pattern .= '/i';
		$string = preg_replace($pattern, '', $string); 
	}
	return $string;
}

/**
 * 调用关联菜单
 * @param $linkageid 联动菜单id
 * @param $id 生成联动菜单的样式id
 * @param $defaultvalue 默认值
 */
function menu_linkage($linkageid = 0, $id = 'linkid', $defaultvalue = 0) {
	$linkageid = intval($linkageid);
	$datas = array();
	$datas = getcache('linkage_'.$linkageid, 'linkage');
	$infos = $datas['data'];
	if($datas['style']=='1') {
		$title = $datas['title'];
		$container = 'content'.random(3).date('is');
		if(!defined('DIALOG_INIT_1')) {
			define('DIALOG_INIT_1', 1);
			$string .= '<script type="text/javascript" src="'.JS_PATH.'dialog.js"></script>';
			$string .= '<link href="'.CSS_PATH.'dialog.css" rel="stylesheet" type="text/css" />';
		}
		if(!defined('LINKAGE_INIT_1')) {
			define('LINKAGE_INIT_1', 1);
			$string .= '<script type="text/javascript" src="'.JS_PATH.'linkage/js/pop.js"></script>';
		}
		$var_div = $defaultvalue && (ACTION_NAME=='edit' || ACTION_NAME=='account_manage_info'  || ACTION_NAME=='info_publish' || ACTION_NAME=='orderinfo') ? menu_linkage_level($defaultvalue,$linkageid,$infos) : $datas['title'];
		$var_input = $defaultvalue && (ACTION_NAME=='edit' || ACTION_NAME=='account_manage_info'  || ACTION_NAME=='info_publish') ? '<input type="hidden" name="info['.$id.']" value="'.$defaultvalue.'">' : '<input type="hidden" name="info['.$id.']" value="">';
		$string .= '<div name="'.$id.'" value="" id="'.$id.'" class="ib">'.$var_div.'</div>'.$var_input.' <input type="button" name="btn_'.$id.'" class="button" value="'.L('linkage_select').'" onclick="open_linkage(\''.$id.'\',\''.$title.'\','.$container.',\''.$linkageid.'\')">';
		$string .= '<script type="text/javascript">';
		$string .= 'var returnid_'.$id.'= \''.$id.'\';';
		$string .= 'var returnkeyid_'.$id.' = \''.$linkageid.'\';';
		$string .=  'var '.$container.' = new Array(';
		foreach($infos AS $k=>$v) {
			if($v['parentid'] == 0) {
				$s[]='new Array(\''.$v['linkageid'].'\',\''.$v['name'].'\',\''.$v['parentid'].'\')';
			} else {
				continue;
			}
		}
		$s = implode(',',$s);
		$string .=$s;
		$string .= ')';
		$string .= '</script>';
		
	} elseif($datas['style'] == '2') {
		$defVal = get_linkage($defaultvalue, $linkageid, ',', 4);
		if(!defined('LINKAGE_INIT_2')) {
			define('LINKAGE_INIT_2', 2);
			$string .= '<script type="text/javascript" src="'.JS_PATH.'linkage/js/linkagesel.min.js"></script>';
			$string .= '<select id="linkage_'.$id.'"></select>';
			$string .= '<input type="hidden" name="info['.$id.']" id="linkage_input_'.$id.'" value="'.$defaultvalue.'">';
			$string .= '<script type="text/javascript">
    var '.$id.'_opts = {
            ajax: "'.__APP__.'?m=Api&c=Linkage&a=index&act=ajax_linkagesel&keyid='.$linkageid.'"
            ,selStyle: "margin-left: 3px;"
            ,select:  "#linkage_'.$id.'"
            ,loaderImg:"'.IMG_PATH.'msg_img/loading.gif"
            ,autoLink:false
            ,head:"请选择"
            ,onChange:"get_linkage_val()"
            ,defVal: ['.$defVal.']
    };
			var linkage_'.$id.' = new LinkageSel('.$id.'_opts);

			linkage_'.$id.'.onChange(function() {
				$("#linkage_input_'.$id.'").val(this.getSelectedValue());
			});			
			</script>';
		}		
	}elseif($datas['style']=='3') {
		if(!defined('LINKAGE_INIT_1')) {
			define('LINKAGE_INIT_1', 1);
			$string .= '<script type="text/javascript" src="'.JS_PATH.'linkage/js/jquery.ld.js"></script>';
		}
		$default_txt = '';
		if($defaultvalue) {
				$default_txt = menu_linkage_level($defaultvalue,$linkageid,$infos);
				$default_txt = '["'.str_replace(' > ','","',$default_txt).'"]';
		}
		$string .= $defaultvalue && (ACTION_NAME=='edit' || ACTION_NAME=='account_manage_info'  || ACTION_NAME=='info_publish') ? '<input type="hidden" name="info['.$id.']"  id="'.$id.'" value="'.$defaultvalue.'">' : '<input type="hidden" name="info['.$id.']"  id="'.$id.'" value="">';

		for($i=1;$i<=$datas['setting']['level'];$i++) {
			$string .='<select class="pc-select-'.$id.'" name="'.$id.'-'.$i.'" id="'.$id.'-'.$i.'"><option value="">请选择菜单</option></select> ';
		}

		$string .= '<script type="text/javascript">
					$(function(){
						var $ld5 = $(".pc-select-'.$id.'");
						$ld5.ld({ajaxOptions : {"url" : "'.__APP__.'?m=Api&c=Linkage&a=index&act=ajax_select&keyid='.$linkageid.'"},defaultParentId : 0,style : {"width" : 120}})	 
						var ld5_api = $ld5.ld("api");
						ld5_api.selected('.$default_txt.');
						$ld5.bind("change",onchange);
						function onchange(e){
							var $target = $(e.target);
							var index = $ld5.index($target);
							$("#'.$id.'-'.$i.'").remove();
							$("#'.$id.'").val($ld5.eq(index).show().val());
							index ++;
							$ld5.eq(index).show();								}
					})
		</script>';
			
	} else {
		$title = $defaultvalue ? $infos[$defaultvalue]['name'] : $datas['title'];
		$colObj = random(3).date('is');
		$string = '';
		if(!defined('LINKAGE_INIT')) {
			define('LINKAGE_INIT', 1);
			$string .= '<script type="text/javascript" src="'.JS_PATH.'linkage/js/mln.colselect.js"></script>';
			if(defined('IN_ADMIN')) {
				$string .= '<link href="'.JS_PATH.'linkage/style/admin.css" rel="stylesheet" type="text/css">';
			} else {
				$string .= '<link href="'.JS_PATH.'linkage/style/css.css" rel="stylesheet" type="text/css">';
			}
		}
		$string .= '<input type="hidden" name="info['.$id.']" value="1"><div id="'.$id.'"></div>';
		$string .= '<script type="text/javascript">';
		$string .= 'var colObj'.$colObj.' = {"Items":[';

		foreach($infos AS $k=>$v) {
			$s .= '{"name":"'.$v['name'].'","topid":"'.$v['parentid'].'","colid":"'.$k.'","value":"'.$k.'","fun":function(){}},';
		}
		$string .= substr($s, 0, -1);
		$string .= ']};';
		$string .= '$("#'.$id.'").mlnColsel(colObj'.$colObj.',{';
		$string .= 'title:"'.$title.'",';
		$string .= 'value:"'.$defaultvalue.'",';
		$string .= 'width:100';
		$string .= '});';
		$string .= '</script>';
	}
	return $string;
}

/**
 * 联动菜单层级
 */

function menu_linkage_level($linkageid,$keyid,$infos,$result=array()) {
	if(array_key_exists($linkageid,$infos)) {
		$result[]=$infos[$linkageid]['name'];
		return menu_linkage_level($infos[$linkageid]['parentid'],$keyid,$infos,$result);
	}
	krsort($result);
	return implode(' > ',$result);
}
/**
 * 通过catid获取显示菜单完整结构
 * @param  $menuid 菜单ID
 * @param  $cache_file 菜单缓存文件名称
 * @param  $cache_path 缓存文件目录
 * @param  $key 取得缓存值的键值名称
 * @param  $parentkey 父级的ID
 * @param  $linkstring 链接字符
 */
function menu_level($menuid, $cache_file, $cache_path = 'commons', $key = 'catname', $parentkey = 'parentid', $linkstring = ' > ', $result=array()) {
	$menu_arr = getcache($cache_file, $cache_path);
	if (array_key_exists($menuid, $menu_arr)) {
		$result[] = $menu_arr[$menuid][$key];
		return menu_level($menu_arr[$menuid][$parentkey], $cache_file, $cache_path, $key, $parentkey, $linkstring, $result);
	}
	krsort($result);
	return implode($linkstring, $result);
}
/**
 * 通过id获取显示联动菜单
 * @param  $linkageid 联动菜单ID
 * @param  $keyid 菜单keyid
 * @param  $space 菜单间隔符
 * @param  $tyoe 1 返回间隔符链接，完整路径名称 3 返回完整路径数组，2返回当前联动菜单名称，4 直接返回ID
 * @param  $result 递归使用字段1
 * @param  $infos 递归使用字段2
 */
function get_linkage($linkageid, $keyid, $space = '>', $type = 1, $result = array(), $infos = array()) {
	$linkageid = (int) $linkageid;
	if($space=='' || !isset($space))$space = '>';
	if(!$infos) {
		$datas = getcache('linkage_'.$keyid, 'linkage');
		$infos = $datas['data'];
	}
	if($type == 1 || $type == 3 || $type == 4) {
		if(array_key_exists($linkageid,$infos)) {
			$result[]= ($type == 1) ? $infos[$linkageid]['name'] : (($type == 4) ? $linkageid :$infos[$linkageid]);
			return get_linkage($infos[$linkageid]['parentid'], $keyid, $space, $type, $result, $infos);
		} else {
			if(count($result)>0) {
				krsort($result);
				if($type == 1 || $type == 4) $result = implode($space,$result);
				return $result;
			} else {
				return $result;
			}
		}
	} else {
		return $infos[$linkageid]['name'];
	}
}

function mb_unserialize($serial_str) {
    $serial_str = str_replace ( "\r", "", $serial_str );
    $serial_str = preg_replace ( '!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
    return unserialize ( $serial_str );
}

function get_document_info($modelid, $id, $modelinfo = TRUE) {
	$models = getcache('model', 'commons');
	$model = $models[$modelid];
	$tablename = $model['tablename'];
	$info = M($tablename)->where(array('id' => $id))->find();
	if ($modelinfo == TRUE) {
		$modelinfo = M($tablename.'_data')->where(array('id' => $id))->find();
		$info = array_merge($info, $modelinfo);
	}
	require_once CACHE_MODEL_PATH.'content_output.class.php';
	$model_output = new \content_output($modelid, $info['catid']);
	$result = $model_output->get($info);
	return $result;
}

/**
 * 获取用户头像
 * @author   <jason>
 */
function getavatar($uid = 0,$modelid = 1, $type = 'url') {
    $suid = sprintf("%09d", $uid);
    $dir1 = substr($suid, 0, 3);
    $dir2 = substr($suid, 3, 2);
    $dir3 = substr($suid, 5, 2);
    $rootDir = SITE_PATH.'/uploadfile/avatar/';
    $userDir = $dir1.'/'.$dir2.'/'.$dir3.'/';
    if($modelid == 1){
    	$fileDir = $rootDir.$userDir.$uid.'_avatar.jpg';
    }else{
    	$fileDir = model('member_merchant')->getFieldByUserid($uid,'store_logo');
    	$fileDir = SITE_PATH.$fileDir;
    } 
    if (!file_exists($fileDir)) {
    	$result = ($modelid == 1) ? $rootDir.'avatar.jpg' : $rootDir.'seller_logo.jpg';
    } else {
    	$result = $fileDir;
    }
    return ($type == 'url') ? str_replace(SITE_PATH, __ROOT__, $result) : $result;
}

function getUserInfo($uid, $field = NULL,$type) {
	$userinfo = D('Member')->getByUserid($uid);
	if (!$userinfo) {
		return FALSE;
	}
	$userinfo['avatar'] = getavatar($uid);
	$sqlmap = array();
	$sqlmap['userid'] = $userinfo['userid'];
	$sqlmap['modelid'] = $userinfo['modelid'];
	$sqlmap['nickname'] = $userinfo['nickname'];
	$sqlmap['groupid'] = $userinfo['groupid'];
	$sqlmap['lastdate'] = $userinfo['lastdate'];
	$sqlmap['alipay_status'] = $userinfo['alipay_status'];
	$sqlmap['bank_status'] = $userinfo['bank_status'];
	$sqlmap['email_status'] = $userinfo['email_status'];
	$sqlmap['phone_status'] = $userinfo['phone_status'];
	$sqlmap['phone'] = $userinfo['phone'];
	$sqlmap['email'] = $userinfo['email'];
	$sqlmap['point'] = $userinfo['point'];

	$sqlmap['name_status'] = model('member_attesta')->where(array('userid'=>$uid,'type'=>'identity'))->getField('status');
	if ($field === NULL) {
		return $userinfo;
	}elseif ($type) {
		return $sqlmap;
	} else {
        return $userinfo[$field];
	}
}

/* 字符串隐藏 */
function get_encode($str) {
    return $str ? cutstr($str, 3, '***').substr($str, -1) : '';
}


/**
 *  字符串提取函数
 *
 * @access    global
 * @param     string  $string   待截取的原字符串
 * @param     number  $length   截取字符串的长度
 * @param     string  $dot      超过部分字符代替
 * @return    string
 */
function cutstr($string, $length, $dot = ' ...') {
    if(strlen($string) <= $length) {
        return $string;
    }
    $pre = chr(1);
    $end = chr(1);
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

    $strcut = '';
    if(strtolower(CHARSET) == 'utf-8') {
        $n = $tn = $noc = 0;
        while($n < strlen($string)) {
            $t = ord($string[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1; $n++; $noc++;
            } elseif(194 <= $t && $t <= 223) {
                $tn = 2; $n += 2; $noc += 2;
            } elseif(224 <= $t && $t <= 239) {
                $tn = 3; $n += 3; $noc += 2;
            } elseif(240 <= $t && $t <= 247) {
                $tn = 4; $n += 4; $noc += 2;
            } elseif(248 <= $t && $t <= 251) {
                $tn = 5; $n += 5; $noc += 2;
            } elseif($t == 252 || $t == 253) {
                $tn = 6; $n += 6; $noc += 2;
            } else {
                $n++;
            }

            if($noc >= $length) {
                break;
            }

        }
        if($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);

    } else {
        for($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
        }
    }

    $strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    $pos = strrpos($strcut, chr(1));
    if($pos !== false) {
        $strcut = substr($strcut,0,$pos);
    }
    return $strcut.$dot;
}

/**
 * 检查会员是否登录
 * @author   <jason>
 */
function is_login() {
	$userid = (int) cookie('_userid');
	if ($userid < 1) {
		return FALSE;
	}
	return getUserInfo($userid,'',1);
}

/**
 * 校验验证码
 * @author   <jason>
 */
function checkVerify($string, $reset = TRUE) {
	if (!empty($string) && $string == strtolower(session('verify'))) {
        if($reset === TRUE) session('verify', NULL);
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * 校验验证码(app端)
 * @param $string   验证码
 * @param bool $reset 是否重置
 * @return boolean
 */
function checkAppVerify($string,$reset=true){
    if (!empty($string)&&$string==getcache("app_verify")){
        if($reset === TRUE) delcache('app_verify', NULL);
        return TRUE;
    }
    else{
        return false;
    }
}


/* 生成文件 */
function dmkdir($dir, $mode = 0777, $makeindex = TRUE){
	if(!is_dir($dir)) {
		dmkdir(dirname($dir), $mode, $makeindex);
		@mkdir($dir, $mode);
		if(!empty($makeindex)) {
			@touch($dir.'/index.html'); @chmod($dir.'/index.html', 0777);
		}
	}
	return true;
}
/**
 * 转换字节数为其他单位
 *
 *
 * @param	string	$filesize	字节大小
 * @return	string	返回大小
 */
function sizecount($filesize) {
	if ($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 .' GB';
	} elseif ($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 .' MB';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize.' Bytes';
	}
	return $filesize;
}


function sql_split($sql, $tablepre) {
    if ($tablepre != "prefix_")
        $sql = str_replace("prefix_", $tablepre, $sql);
    $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    $queriesarray = explode(";\n", trim($sql));
    unset($sql);
    foreach ($queriesarray as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        $queries = array_filter($queries);
        foreach ($queries as $query) {
            $str1 = substr($query, 0, 1);
            if ($str1 != '#' && $str1 != '-')
                $ret[$num] .= $query;
        }
        $num++;
    }
    return $ret;
}

/**
 * 代码广告展示函数
 * @param intval $siteid 所属站点
 * @param intval $id 广告ID
 * @return 返回广告代码
 */
function show_ad($siteid, $id) {
	$siteid = intval($siteid);
	$id = intval($id);
	if(!$id || !$siteid) return false;
	$p = D('Poster');
	$r = $p->where(array('spaceid'=>$id, 'siteid'=>$siteid))->field( 'disabled, setting')->order('id ASC')->find();
	if ($r['setting']) {
		$c = string2array($r['setting']);
	} else {
		$r['code'] = '';
	}
	return $c['code'];
}

/**
 * 文件下载
 * @param $filepath 文件路径
 * @param $filename 文件名称
 */
function file_down($filepath, $filename = '') {
	if(!$filename) $filename = basename($filepath);
	if(is_ie()) $filename = rawurlencode($filename);
	$filetype = fileext($filename);
	$filesize = sprintf("%u", filesize($filepath));
	if(ob_get_length() !== false) @ob_end_clean();
	header('Pragma: public');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: pre-check=0, post-check=0, max-age=0');
	header('Content-Transfer-Encoding: binary');
	header('Content-Encoding: none');
	header('Content-type: '.$filetype);
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Content-length: '.$filesize);
	readfile($filepath);
	exit;
}

/* 
 * 统计订单条数 [Adalways试用系统] 普通
 * 
 * $userid: 会员userid
 * $modelid: 会员模型id
 * $status: 传入状态值
 * $mod: 商品类型
 * */
function order_count($userid, $modelid,$status=-2,$mod='',$goods_id = 0){
    $userid = (int) $userid;
    $status = (int) $status;
    if($userid < 1) return FALSE;
    if($modelid < 1) return FALSE;
	$sqlmap = array();
	($modelid == 1) ? $sqlmap['buyer_id'] = $userid : $sqlmap['seller_id'] = $userid;
	if (!empty($mod)) {
		  $sqlmap['act_mod'] = $mod;
	}
    if ($goods_id > 0)	$sqlmap['goods_id'] = $goods_id;
    if($status > -2) {
        $sqlmap['status'] = $status;
    }
	return (int) model('order')->where($sqlmap)->count();
}

/* 
 * 统计申诉条数 [Adalways试用系统]
 * 
 * $userid: 会员userid
 * $modelid: 会员模型id
 * $status: 传入状态值
 * 
 * */
function appeal_count($userid, $modelid,$status=-1){
    $userid = (int) $userid;
    if ($userid && $modelid) {
    	$sqlmap = array();
	($modelid == 1) ? $sqlmap['buyer_id'] = $userid : $sqlmap['seller_id'] = $userid;
    }
    $status = (int) $status;
    if($status > -1) {
        $sqlmap['appeal_status'] = $status;
    }
	return (int) model('appeal')->where($sqlmap)->count();
}
/**
 * 产品记录[Adalways试用系统]
 * @param unknown_type $p_id 产品id
 * @param unknown_type $p_state 状态标示
 * @param unknown_type $is_sys 是否平台
 * @param unknown_type $uid 操作人i的
 * @param unknown_type $msg 操作理由
 * @return unknown
 */
function product_log($p_id,$p_state,$is_sys,$uid,$msg){
	$model = M('product_log');
	$sqlMap = array();
	$sqlMap['p_id'] = $p_id;
	$sqlMap['p_state'] = $p_state;
	$sqlMap['is_sys'] = $is_sys;
	$sqlMap['uid'] = $uid;
	$sqlMap['msg'] = $msg;
	$sqlMap['dateline'] = NOW_TIME;
	$sqlMap['clientip'] = get_client_ip();
	$result = $model->add($sqlMap);
	return $result;
}

function commission_log($p_id,$p_state,$is_sys,$uid,$msg){
	$sqlMap = array();
	$sqlMap['p_id'] = $p_id;
	$sqlMap['p_state'] = $p_state;
	$sqlMap['is_sys'] = $is_sys;
	$sqlMap['uid'] = $uid;
	$sqlMap['msg'] = $msg;
	$sqlMap['dateline'] = NOW_TIME;
	$sqlMap['clientip'] = get_client_ip();
	$result = model('commission_log')->add($sqlMap);
	return $result;
}

/**
 * 日赚任务记录
 */
function task_log($t_id,$t_state,$is_sys,$uid,$msg){
	$model = M('task_log');
	$sqlMap = array();
	$sqlMap['t_id'] = $t_id;
	$sqlMap['t_status'] = $t_state;
	$sqlMap['is_sys'] = $is_sys;
	$sqlMap['userid'] = $uid;
	$sqlMap['msg'] = $msg;
	$sqlMap['dateline'] = NOW_TIME;
	$sqlMap['clientip'] = get_client_ip();
	$result = $model->add($sqlMap);
	return $result;
}

/*是否签到*/
function is_sign($uid) {
	$db = M('MemberSign');
	$sqlmap = array();
	$sqlmap['uid'] = $uid;
	$sqlmap['_string'] = "DATE_FORMAT(FROM_UNIXTIME(dateline),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
	return ($db->where($sqlmap)->count()) ? TRUE : FALSE;
}

/** 
 *	取商品信息 [Adalways试用系统]
 * 	$id    : 商品id
**/
function getGoodsInfo($id = '0') {
	if ($id < 1) return json_encode(array('info'=>'参数错误'));
    // $categorys = getcache('category', 'commons');
    // $category = $categorys[$catid];
    // if (!$category) return json_encode(array('info'=>'数据异常'));
    $rs = D('product')->where(array('id'=>$id))->find();
    $rs1 = D('product_'.$rs['mod'])->where(array('id'=>$id))->find();
    if (!$rs || !$rs1) return json_encode(array('info'=>'您查看的记录不存在'));
    return array_merge($rs, $rs1);
}


/**
 * 执行用户财务明细记录
 * @param int       $uid        用户ID
 * @param decimal   $num        变更数量
 * @param string    $type       变更类型
 * @param string    $cause      变更理由
 * @param int       $extra      附加数组（goods_id/order_id）
 * @param string    $only       标记
 * @return bool
 */
function action_finance_log($uid = 0, $num = 0, $type = 'money', $cause = '',$only='',$extra = array(),$isrun = TRUE) {
    $uid = (int) $uid;
    if($uid < 1 || !in_array($type, array('deposit','money','point','exp','service'))) return FALSE;
    $log = $extra;
    $log['userid'] = $uid;
    $log['num'] = $num;
    $log['type'] = $type;
    $log['cause'] = $cause;
    $log['dateline'] = NOW_TIME;
    $log['ip'] = get_client_ip();
    $log['only'] = $only;

    $r = model('member_finance_log')->add($log);
    if(!$r) return FALSE;
    if ($isrun === TRUE){
    	$result =  model('member')->where(array('userid' => $uid))->setInc($type, $num);
    	if ($type == 'money') {
    		$sqlmap = array();
    		$sqlmap['userid'] = $uid;
    		$sqlmap['id'] = $r;
    		model('member_finance_log')->where($sqlmap)->setField(array('total_money'=>getUserInfo($uid,'money')));
    	}

    	return $result;
    } 
    return TRUE;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = & $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[$data[$pk]] = & $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = & $refer[$parentId];
                    $parent[$child][$data[$pk]] = & $list[$key];
                }
            }
        }
    }
    return $tree;
}
/**
 * 查询用户组的名称
 */
function member_group_name($userid){
	$userinfo = model('member')->getByUserid($userid);
	$groups = ($userinfo['modelid'] == 1) ? getcache('member_group','member') : getcache('merchant_group','member');
	return $groups[$userinfo['groupid']]['name'];
}

/* 调用会员昵称 */
function nickname($userid,$type = ''){
	$userinfo = model('member')->where(array('userid' => $userid))->field('nickname,email,phone')->find();

	if (!$userinfo['nickname']) {
		if($userinfo['email']){
			$nickname = 'ml_'.$userinfo['email'];
		}else{		
			$nickname = substr_replace($userinfo['phone'],'***',5,6);
		}
	}else{
		    $nickname = $userinfo['nickname'];
	}

	return $nickname;
}

/**
 * 查找该栏目是属于买家还是商家
 * @param $catid 栏目id
 */
function id_in_arrchildid($catid, $id) {
	$categorys = getcache('category', 'commons');
	$category = $categorys[$catid];
	$arrchildid = $category['arrchildid'];
	return check_in($id, $arrchildid); 
}


function is_gb2312($str){
	for($i=0; $i<strlen($str); $i++) {
		$v = ord( $str[$i] );
		if( $v > 127) {
			if( ($v >= 228) && ($v <= 233) )
			{
				if( ($i+2) >= (strlen($str) - 1)) return true;  // not enough characters
				$v1 = ord( $str[$i+1] );
				$v2 = ord( $str[$i+2] );
				if( ($v1 >= 128) && ($v1 <=191) && ($v2 >=128) && ($v2 <= 191) ) // utf编码
					return false;
				else
					return true;
			}
		}
	}
	return true;
}

/*商家待审核，进行中，受到的申诉条数*/
 function total_count($userid){
    $where['seller_id'] = $userid;
    $result['appeal_count'] = model('appeal')->where($where)->count();

    $sqlMap['status'] = 1;
    $sqlMap['userid'] = $userid;
    $result['activity_count'] =  D('Product/Product')->where($sqlMap)->count();

    $result['checke_activity'] = D('Product/Product')->where(array('status'=>array('in','-3,-2'),'userid'=>$userid))->count(); 
    return $result;

}

/*调用活动价格单位*/
function activitiy_price_name($mod){
	$activity = model('activity_set')->where(array('activity_type'=>$mod))->getField('key,value');
	if($mod == 'rebate'){
		return $activity['rebate_price_name'];
	}else if ($mod == 'trial'){
		return $activity['trial_price_name'];
	}else{
		return $activity['postal_price_name'];
	}
}

/*调用活动状态*/
function goods_status_name($status){
	return	array_search($status,array(
			'待审核待付款' =>-3,
			'待审核已付款' => -2,
			'审核通过(待上线)'=>-1,
	        '活动进行中' =>1,
	        '活动结束(结算中)' => 2,
	        '活动结束(已结算)' => 3,
	        '已撤销' => 4,
	        '已屏蔽' => 5,
			));

}

/*产品价格*/
function price($id){
	$factory = new \Product\Factory\product($id);
	$mod = $factory->product_info['mod'];
	if($mod == 'rebate'){
		$price = sprintf('%.2f' , $factory->product_info['goods_price'] * $factory->product_info['goods_discount'] / 10);
	}else{
		$price = sprintf('%.2f' , $factory->product_info['goods_price']);
	}
	return $price;
}

//获取未读消息条数
function message_count($userid = 0) {
	/* 读取未读点对点短信 */
	$userid = (int) $userid;
	if($userid < 1) return 0;
	$sqlmap = array();
	$sqlmap['send_to_id'] = $userid;
	$sqlmap['status'] = 0;
	$message_count = model('message')->where($sqlmap)->count();
	/* 读取未读群发消息 */
	$message_group_count = 0;
	$group_map = array();
	$group_map['status'] = 0;
	$message_group = model('message_group')->where($group_map)->select();
	if($message_group) {
		foreach ($message_group as $key => $value) {
			if(model('message_data')->where(array('group_message_id' => $value['id']))->count() == 0) {
				$message_group_count++;
			}
		}
	}
	return (int) ($message_count + $message_group_count);
	
}

/* 获取获取来源指定信息 */
function get_shop_set($id = 0, $field = NULL) {
	$info = model('shop_set')->getById($id);
	if($field) return $info[$field];
	return $info;
}

/* C：获取全局配置文件 */
function C_READ($name = '', $mod = 'trial') {
	 $caches =  getcache($mod,'activity_set');
	 if(!$caches || empty($caches)){
	 	 $lists = model('activity_set')->distinct(true)->field('activity_type')->select();
         foreach ($lists as $k => $v) {
            //获取每个类型的所有值
            $activity_types = model('activity_set')->where(array('activity_type' => $v['activity_type']))->select();
            $types = array();
            foreach ($activity_types as $key => $value) {
            	$types[$value['key']] = $value['value'];
            }
            //缓存类型
            setcache($v['activity_type'],$types,'activity_set');
            unset($activity_types);
         }
         $caches =  getcache($mod,'activity_set');
	 }
	 return $caches[$name];
}

/* 获取会员信息 */
function member_info($userid){
	if ((int) $userid < 1) return FALSE;
	$rs1 = model('member')->where(array('userid'=>$userid))->find();
	if (!$rs1)	return FALSE;
	$modelid = $rs1['modelid'];
	$models = getcache('model','commons');
	$tablename = $models[$modelid]['tablename'];
	$rs2 = model($tablename)->where(array('userid'=>$userid))->find();
	if(empty($rs1) ){
		$data = $rs2;
	}else if(empty($rs2)){
		$data = $rs1;
	}else{
		$data = array_merge($rs1,$rs2);
	}
	if($modelid == 2){
		// 商家类型	(1:普通商家、2:钻石商家、3:皇冠商家)
		$groupid = model('member')->where(array('userid'=>$userid))->getField("groupid");
		$seller_type = member_group_name($userid);
		// 店铺认证
		$shop = model('member_attesta')->where(array('userid'=>$userid,'type'=>'shop','status'=>1))->count();
		// 品牌认证
		$brand = model('member_attesta')->where(array('userid'=>$userid,'type'=>'brand','status'=>1))->find();
		$data['groupid']       = $groupid;
		$data['seller_type']   = $seller_type;
		$data['shop_attesta']  = $shop;
		$data['brand_attesta'] = ($brand) ? 1: 0;
		$brandinfo = array2string($brand['infos']);
		$data['brand_name'] = $brandinfo['chinese'];
	}else{
		//会员生日
		$birthday = string2array($data['birthday']);
		$data['birth'] = $birthday;
		$data['birthday'] = $birthday['year'].'-'.$birthday['month'].'-'.$birthday['day'];
		//会员联系地址
		$address = string2array($data['receives']);
		$data['contact_address'] = $address['r_address'];
		$data['contact_name'] = $address['r_name'];
		$data['contact_tel'] = $address['r_phone'];
		$data['avatar'] = getavatar($userid);
		$data['agent_count'] = model('member')->where(array('agent_id'=>$userid))->count();
	}
	//身份信息
	$idetifyinfos = model('member_attesta')->where(array('userid'=>$userid))->select();
		foreach ($idetifyinfos as $k=>$v){
			$idetify = string2array($v['infos']);
			
			if($v['type'] == 'identity'){
				$data['id_number'] = $idetify['id_number'];
				$data['name'] = $idetify['name'];
				$status = model('member_attesta')->where(array('userid'=>$userid,'type'=>'identity'))->find();

				$data['id_number_status'] = $status['status'];
			}elseif ($v['type'] == 'bank'){
				$data['bank_account'] = $idetify['account'];
				$data['bank_name'] = $idetify['bank_name'];
				$data['province'] = $idetify['province'];
				$data['city'] = $idetify['city'];
				$data['area'] = $idetify['area'];
				$data['sub_branch'] = $idetify['sub_branch'];
				//根据linkid查地址
				$province = model('linkage')->getFieldByLinkageid($idetify['province'],'name');
				$city = model('linkage')->getFieldByLinkageid($idetify['city'],'name');
				$data['band_address'] = $province.$city.$idetify['sub_branch'];
			}else if ($v['type'] == 'alipay'){
				$data['alipay_account'] = $idetify['alipay_account'];
				$data['alipay_code'] = $idetify['alipay_code'];
				$data['a_username'] = $idetify['username'];
			}
		}
	return $data;
}
/* 商家活动条数 */
function activity_count($userid,$mod='',$status = -4){
	if($userid < 1) return false;
	$sqlmap = array();
	$sqlmap['company_id'] = $userid;
	if(!empty($mod)){
		$sqlmap['mod'] = $mod;
	}
	if($status > -4){
		$sqlmap['status'] = $status;
	}
	return model('product')->where($sqlmap)->count();
}
/**
 * 转换URL路由
 * @param type $rule
 * @param type $param
 */
function url_rewrite($rule = '', $param = array(), $domain = '') {

	if(isset($rule) || !empty($rule))  return false;
    $urlrules = C('URL_REWRITE');
    $urlrulek = parse_name($rule['m']).'/'.parse_name($rule['c']).'/'.$rule['a'];
    if(!$urlrules[$urlrulek]) return FALSE;
    return (($domain) ? ((is_ssl()?'https://':'http://')).$domain : '').$urlrules[$urlrulek].(($param) ? '?'.http_build_query($param) : '');
}
/* 读取店铺来源 */
function shop_set($id,$field = ''){
	if ($field!=''){
		$result = model('shop_set')->where(array('id'=>$id))->getfield($field);
	}else{
		$result = model('shop_set')->find($id);
	}
	return $result;
}
/*活动是否开启*/
function is_activity_open($mod){
	$info = model('activity_set')->where(array('activity_type'=>$mod))->getField('key,value');
	return $info[$mod.'_isopen'];
}
/* [统计] 根据商品ID统计试用报告总数 */
function report_count_by_gid($goods_id) {
	$sqlmap = array();
	$sqlmap['goods_id'] = $goods_id;
	$sqlmap['status'] = 1;
	return model('trial_report')->where($sqlmap)->count();
}
/* [统计] 详情页，已审核人数 */
function buyer_count_by_gid($goods_id = 0) {
	$sqlmap = array();
    $sqlmap['goods_id'] = $goods_id;  
    $sqlmap['status'] = array("GT", 1);
	return model('order')->where($sqlmap)->count();
}

/* [统计] 根据商品ID获取晒单总数 */
function report_buyer_by_gid($goods_id = 0) {
	$sqlmap = array();
	$sqlmap['goods_id'] = $goods_id;
	$sqlmap['status'] = 1;
	return model('report')->where($sqlmap)->count();
}
/* [统计：蒲哥说的] 根据商品ID获取已申请试用总人数 */
function get_trial_by_gid($goods_id = 0) {
	$sqlmap = array();
	$sqlmap['goods_id'] = $goods_id;
	return model('order')->where($sqlmap)->count();
}

/*根据商品ID获取已申请试用通过总人数*/
function get_trial_pass_by_gid($goods_id = 0){
	$sqlmap = array();
	$sqlmap['goods_id'] = $goods_id;
	$sqlmap['status'] = array("GT", 1);	return model('order')->where($sqlmap)->count();
}



/*根据商品ID获取已关闭的总人数*/
function get_Close_order($goods_id = 0){
	$sqlmap = array();
	$sqlmap['goods_id'] = $goods_id;
	$sqlmap['status'] = array("eq", 0);	return model('order')->where($sqlmap)->count();
}



/* [统计：蒲哥说的] 根据商品ID获取已完成试用总人数 */
function get_over_trial_by_gid($goods_id = 0) {
	$sqlmap = array();
	$sqlmap['goods_id'] = $goods_id;
	$sqlmap['status'] = 7;
	return model('order')->where($sqlmap)->count();
}

/*[统计]商品信息统计*/
function goods_info_count($status = 0){
	$sqlMap = array();
	$sqlMap['status'] = $status;
	return model('product')->where($sqlMap)->count();
}
/*[统计]订单信息统计*/
function order_info_count($status = 0,$istoday = false){
	$sqlMap = array();
	if($istoday != false){
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$sqlMap['inputtime'] = array('between',array($beginToday,$endToday));
		$count = model('order')->where($sqlMap)->count();
	}else{
		$sqlMap['status'] = $status;
		$count = model('order')->where($sqlMap)->count();
	}
	return $count;
}
/*【统计】今日新增商家会员
 * @param $modedid 1：会员  2：商家
 * return count
 * */
function  member_info_count($modelid){
	$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
	$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
	$sqlMap = array();
	$sqlMap['regdate'] = array('between',array($beginToday,$endToday));
	$sqlMap['modelid'] = $modelid;
	$count = model('member')->where($sqlMap)->count();
	return $count;
}

/**
 * 今日提现充值统计
 * @param $type  1:充值  2：提现
 */

function deposite_count($type = 1,$status = 0){
	$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
	$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
	if($type == 1){//今日在线充值总额
		$sqlMap['dateline'] = array('between',array($beginToday,$endToday));
		$sqlMap['status'] =$status;
		$count = model('pay_order')->where($sqlMap)->sum('total_fee');
	}elseif($type == 2){
		/*待审核充值*/
		unset($sqlMap);
		$sqlMap['status'] =0;
		$count =  model('pay_check')->where($sqlMap)->sum('money');
	}elseif($type == 3 ){
		/*待审核提现*/
		unset($sqlMap);
		$sqlMap['status'] =0;
		$count = model('cash_records')->where($sqlMap)->sum('totalmoney');
	}elseif($type == 4){
		$sqlMap['inputtime'] = array('between',array($beginToday,$endToday));
		$sqlMap['status'] = $status;
		$count = model('cash_records')->where($sqlMap)->sum('totalmoney');
	}
	return ($count == '') ? 0 : $count;
}

/**
 * 查看待审核品牌、实名认证总数
 * @param $type 1:品牌 2：实名认证
 */

function brand_count($type='identity',$status='0'){
	return model('member_attesta')->where(array('type' => $type,'status'=>0))->count();
}

/* 读取店铺名称 */
function store_name($userid) {
	$userid = (int)$userid;
	if ($userid < 0) return FALSE;
	return model('member_merchant')->getFieldByUserid($userid,'store_name');
}


/* 读取最新绑定店铺信息 */
function new_store_name($userid,$ww) {

  $info =	model('merchant_store')->where(Array('userid' =>$userid,'contact_want' =>$ww))->find();

  return $info;

}


/**
 * 获取用户的的个人认证信息
*/
function get_personal($userid,$type){
	$r = model('member_attesta')->where(array('userid'=>$userid,'type'=>$type,'status'=>1))->find();
	$real = string2array($r['infos']);
	return $real;
}


/*获取seo设置*/
function get_seo($type ='',$field ='',$title=''){
	$setting = model('Setting')->getField('key,value');
	$arr = array('{site_title}'  => C('webname'));
	$arrs = array('{title}'  => $title);

	
	if (isset($setting['score_seo'])) {
		$setting['score_seo'] = string2array($setting['score_seo']);
	}

	if (isset($setting['activity_seo'])) {
		$setting['activity_seo'] = string2array($setting['activity_seo']);
	}

	if (isset($setting['help_seo'])) {
		$setting['help_seo'] = string2array($setting['help_seo']);
	}

	if (isset($setting['rebate_seo'])) {
		$setting['rebate_seo'] = string2array($setting['rebate_seo']);
	}

	if (isset($setting['trial_seo'])) {
		$setting['trial_seo'] = string2array($setting['trial_seo']);
	}

	if (isset($setting['all_seo'])) {
		$setting['all_seo'] = string2array($setting['all_seo']);
	}

	if (isset($setting['red_seo'])) {
		$setting['red_seo'] = string2array($setting['red_seo']);
	}


	if (isset($setting['postal_seo'])) {
		 $setting['postal_seo'] = string2array($setting['postal_seo']);
	}
	if (isset($setting['shai_seo'])) {
		$setting['shai_seo'] = string2array($setting['shai_seo']);
	}

	if (isset($setting['report_seo'])) {
		$setting['report_seo'] = string2array($setting['report_seo']);
	}


	if (isset($setting['rebate_show'])) {
		$setting['rebate_show'] = string2array($setting['rebate_show']);
	}

	if (isset($setting['trial_show'])) {
		$setting['trial_show'] = string2array($setting['trial_show']);
	}

	if (isset($setting['postal_show'])) {
		$setting['postal_show'] = string2array($setting['postal_show']);
	}
	if (!empty($title)) {
		$setting[$type][$field] = str_replace(array_keys($arrs), $arrs, $setting[$type][$field]);

	}

	if (!empty($field)) {
		$setting[$type][$field] = str_replace(array_keys($arr), $arr, $setting[$type][$field]);
		return $setting[$type][$field];
	}else{

		$setting[$type] = str_replace(array_keys($arr), $arr, $setting[$type]);
		return $setting[$type];
	}
    
     
}

/* 通过旺旺获取商家店铺的联系qq*/
function get_bind_taobao_qq($company_id = 0,$goos_ww ='') {
	return model('merchant_store')->where(array('userid' =>$company_id,'contact_want' =>$goos_ww))->getField('store_qq');
}
/* 通过旺旺获取商家店铺的某一字段信息*/
function get_store_value($company_id = 0,$goos_ww ='',$value = 'store_name') {
	return model('merchant_store')->where(array('userid' =>$company_id,'contact_want' =>$goos_ww))->getField($value);
}


/* 获取该会员绑定淘宝信息 */
function get_bind_taobao($userid = '') {
	$userid = (int)$userid;
	if ($userid < 1) return FALSE;
	return model('member_bind')->where(array('userid'=>$userid,'status'=>1))->order('id DESC')->select();
}

/*统计会员绑定的淘宝帐号数量*/
function get_bind_taobao_num($userid = '') {

	if(!isset($userid)) echo $userid =cookie('_userid');
	if ($userid < 1) return FALSE;
	 model('member_bind')->where(array('userid'=>$userid,'status'=>1))->count();

	 echo model('member_bind')->getLastSql();

	 die();

	 //return 
}

/* 
 *	商品图片地址转换 
 *	$url  : 要转换的图片地址；
 *	$size : 要转换的图片尺寸(b:原图；m:250*250；s:150*150)
 *  $is_has : 已经转换 再转换一次
*/
function img2thumb($url='',$size='b',$is_has = 0) {
	$arr = array('b'=>'','m'=>'_300','s'=>'_150','t'=>'_250');
	if (!$url) return FALSE;
	$list = explode('.', $url);
    if($is_has == 1){
        $arrs = explode('_', $list[0]);
        $new_url = $arrs[0].$arr[$size].'.'.$list[1];
    }else{
        $new_url = $list[0].$arr[$size].'.'.$list[1];
    }
	// 当缩略图不存在时则调用原图
	if (!file_exists('.'.$new_url)) {
		$new_url = $url;
	}
	return $new_url;
}

function get_thumb($filename,$thumb_img = 'test.jpg',$width='150',$height='120'){
	$image = new \Think\Image(); 
	$image->open($filename);
	// 生成一个缩放后填充大小的图片并存为新图片
	$image->thumb($width,$width,\Think\Image::IMAGE_THUMB_FILLED)->save($thumb_img);
	//返回缩略图
	return $thumb_img;
}




/**
 * 获取用户邀请的奖励
 * @param $type 奖励类型
 */
 function get_reword($userid,$type){
    if($userid < 1) return FALSE;
    $sqlMap = array();
    $sqlMap['userid'] =  $userid;
    $sqlMap['recommend_status'] = array('EQ','1');
    $sqlMap['type'] = array('EQ',$type);
    return model('member_finance_log')->where($sqlMap)->sum('num');
 }
 
 /**
  * 邀请积分排行榜
  */
 function invite_among($type){
 	$friend_list = model('member_finance_log')->group('userid')->field('sum(num) AS num,userid')->where(array('type'=>$type,'recommend_status'=>1))->order('num DESC')->limit(10)->select();
 	foreach ($friend_list as $_k=>$_v){
 		$friend_list[$_k]['nickname'] = nickname($_v['userid']);
 		$friend_list[$_k]['avatar'] = getavatar($_v['userid']);
 	}
 	return $friend_list;
 }

function go_taobao($url){
	preg_match('/id=([^<>&]*)/', $url, $id); 
	$id = $id[1];
	require APP_PATH."taobao/TopSdk.php";
	$c = new TopClient;
	// 获取淘宝商品信息
    $c->appkey = C('API_KEY');
    $c->secretKey = C('API_SECRET');
	$req = new TbkItemInfoGetRequest;
	$req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,nick");
	$req->setPlatform("1");
	$req->setNumIids("$id");
	$resp = $c->execute($req);
    $result['status'] = 1;
	$result['url'] = (string)$resp->results->n_tbk_item->item_url;
	$result['img'] = $resp->results->n_tbk_item->pict_url;
	$result['title'] =(string)$resp->results->n_tbk_item->title;
	$result['keyword'] = $keywords[1];
	$result['goods_price'] =(string)$resp->results->n_tbk_item->reserve_price;
	$result['description'] = $description;
	$result['wangwang'] = (string)$resp->results->n_tbk_item->nick;
	 return $result;
	
}


function go_tmall($url){
	preg_match('/id=([^<>&]*)/', $url, $id); 
	$id = $id[1];
	require APP_PATH."taobao/TopSdk.php";
	$c = new TopClient;
	// 获取天猫商品信息
    $c->appkey = C('API_KEY');
    $c->secretKey = C('API_SECRET');
	$req = new TbkItemInfoGetRequest;
	$req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,nick");
	$req->setPlatform("1");
	$req->setNumIids("$id");
	$resp = $c->execute($req);

	$result['status'] = 1;
	$result['url'] = (string)$resp->results->n_tbk_item->item_url;
	$result['img'] = $resp->results->n_tbk_item->pict_url;
	$result['title'] =(string)$resp->results->n_tbk_item->title;
	$result['keyword'] = $keywords[1];
	$result['goods_price'] =(string)$resp->results->n_tbk_item->reserve_price;
	$result['description'] = $description;
	$result['wangwang'] = (string)$resp->results->n_tbk_item->nick;

	return $result;

}

function my_image_resize($src_file, $dst_file , $new_width , $new_height) { 
		if($new_width <1 || $new_height <1) { 
		echo "params width or height error !"; 
		exit(); 
		} 
		if(!file_exists($src_file)) { 
		echo $src_file . " is not exists !"; 
		exit(); 
		} 
		// 图像类型 
		$type=exif_imagetype($src_file); 
		$support_type=array(IMAGETYPE_JPEG , IMAGETYPE_PNG , IMAGETYPE_GIF); 
		if(!in_array($type, $support_type,true)) { 
		echo "this type of image does not support! only support jpg , gif or png"; 
		exit(); 
		} 
		//Load image 
		switch($type) { 
		case IMAGETYPE_JPEG : 
		$src_img=imagecreatefromjpeg($src_file); 
		break; 
		case IMAGETYPE_PNG : 
		$src_img=imagecreatefrompng($src_file); 
		break; 
		case IMAGETYPE_GIF : 
		$src_img=imagecreatefromgif($src_file); 
		break; 
		default: 
		echo "Load image error!"; 
		exit(); 
		} 
		$w=imagesx($src_img); 
		$h=imagesy($src_img); 
		$ratio_w=1.0 * $new_width / $w; 
		$ratio_h=1.0 * $new_height / $h; 
		$ratio=1.0; 
		// 生成的图像的高宽比原来的都小，或都大 ，原则是 取大比例放大，取大比例缩小（缩小的比例就比较小了） 
		if( ($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) { 
		if($ratio_w < $ratio_h) { 
		$ratio = $ratio_h ; // 情况一，宽度的比例比高度方向的小，按照高度的比例标准来裁剪或放大 
		}else { 
		$ratio = $ratio_w ; 
		} 
		// 定义一个中间的临时图像，该图像的宽高比 正好满足目标要求 
		$inter_w=(int)($new_width / $ratio); 
		$inter_h=(int) ($new_height / $ratio); 
		$inter_img=imagecreatetruecolor($inter_w , $inter_h); 
		imagecopy($inter_img, $src_img, 0,0,0,0,$inter_w,$inter_h); 
		// 生成一个以最大边长度为大小的是目标图像$ratio比例的临时图像 
		// 定义一个新的图像 
		$new_img=imagecreatetruecolor($new_width,$new_height); 
		imagecopyresampled($new_img,$inter_img,0,0,0,0,$new_width,$new_height,$inter_w,$inter_h); 
		switch($type) { 
		case IMAGETYPE_JPEG : 
		imagejpeg($new_img, $dst_file,100); // 存储图像 
		break; 
		case IMAGETYPE_PNG : 
		imagepng($new_img,$dst_file,100); 
		break; 
		case IMAGETYPE_GIF : 
		imagegif($new_img,$dst_file,100); 
		break; 
		default: 
		break; 
		} 
		} // end if 1 
		// 2 目标图像 的一个边大于原图，一个边小于原图 ，先放大平普图像，然后裁剪 
		// =if( ($ratio_w < 1 && $ratio_h > 1) || ($ratio_w >1 && $ratio_h <1) ) 
		else{ 
		$ratio=$ratio_h>$ratio_w? $ratio_h : $ratio_w; //取比例大的那个值 
		// 定义一个中间的大图像，该图像的高或宽和目标图像相等，然后对原图放大 
		$inter_w=(int)($w * $ratio); 
		$inter_h=(int) ($h * $ratio); 
		$inter_img=imagecreatetruecolor($inter_w , $inter_h); 
		//将原图缩放比例后裁剪 
		imagecopyresampled($inter_img,$src_img,0,0,0,0,$inter_w,$inter_h,$w,$h); 
		// 定义一个新的图像 
		$new_img=imagecreatetruecolor($new_width,$new_height); 
		imagecopy($new_img, $inter_img, 0,0,0,0,$new_width,$new_height); 
		switch($type) { 
		case IMAGETYPE_JPEG : 
		imagejpeg($new_img, $dst_file,100); // 存储图像 
		break; 
		case IMAGETYPE_PNG : 
		imagepng($new_img,$dst_file,100); 
		break; 
		case IMAGETYPE_GIF : 
		imagegif($new_img,$dst_file,100); 
		break; 
		default: 
		break; 
		} 
		}// if3 
}// end function 


function go_jd($url){
    $text = file_get_contents("$url");
    $result = array();
    // if(empty($text)){
    //     $result['status'] = 0;
    //     $result['info'] = '地址没有值';
    //     return $result;
    // }
    //将url地址上页面内容保存进$text
    

    $text = iconv('GBK','UTF-8',$text);

    $shopurl = $url;
    //获取商品链接url
    preg_match('/<img[^>]*data-img="1"[^r]*rc=\"([^"]*)\"[^>]*>/', $text, $img);
    //获取商品主图
    preg_match('/<title>([^<>]*)<\/title>/', $text, $title);
    //获取商品标题
    preg_match('/<meta name="keywords" .*?content="(.*?)".*? \/>/is', $text, $keywords);
    //获取商品关键字
    preg_match('/class="p-price">$([^<>]*)<\/strong>/', $text, $price);
    //获取价格
    preg_match('/<a class="slogo-shopname"[^>]*>([^<>]*)<\/a>/', $text, $wangwang);
    //获取旺旺 去掉空白
    $wangwang[1]=preg_replace("/\s+/",' ',$wangwang[1]);

//    preg_match_all('/<script[^>]*>[^<]*<\/script>/is', $text, $content);//页面js脚本
//    $content=$content[0];
//    $description='<div id="detail"> </div><div id="description"><div id="J_DivItemDesc">描述加载中</div></div>';
//    foreach ($content as &$v){
//        $description.= $v;
//    };
    $result['status'] = 1;
    $result['url'] = $shopurl;
    $result['img'] = $img[1];
    $result['title'] = $title[1];
    $result['title'] = str_replace("-京东","",$result['title']);
    $result['keyword'] = $keywords[1];
    $result['goods_price'] = $price[1];
//    $result['description'] = $description;
    $result['wangwang'] = $wangwang[1];
    return $result;
}


function get_order_status($goods_id,$userid){
	  $con = array();
	  $con['goods_id'] = array('EQ',$goods_id);
	  $con['buyer_id'] = array('EQ',$userid);
	  $status = model('order')->where($con)->order('id DESC')->getField('status');
	  return $status;
}


function push($receive,$title,$content,$m_type='',$m_txt='',$m_time='0',$userid){
            //推送消息异步执行处理机制
            $fp = fsockopen($_SERVER['HTTP_HOST'], 80, $errno, $errstr, 30);
            if (!$fp) {
                echo "$errstr ($errno)<br />\n";
            } else {
            	$content = preg_replace("@\s@is",'',$content);
                $out = "GET http://".$_SERVER['HTTP_HOST']."/index.php?m=api&c=PushSDK&a=new_push&receive=".$receive."&title=".$title."&content=".$content."&m_type=".$m_type."&m_txt=".$m_txt."&m_time=".$m_time."&userid=".$userid."  HTTP/1.1\r\n";
                $out .= "Host: ".$_SERVER['HTTP_HOST']."\r\n";
                $out .= "Connection: Close\r\n\r\n";
                fwrite($fp, $out);
                fclose($fp);
            }
            return;
}

/*function push($receive,$title,$content,$m_type='',$m_txt='',$m_time='0',$userid){
		 	$pushObj = A('Api/PushSDK');
		       // $result = $push->push($receive,$content,'',$m_txt,$m_time='0',$userid);
		      //$result = $push->send('测试','发呆发发发呆司法所');
		    $receive = array('alias'=>array($receive));    //别名

		      //离线保留时间
		 
		    //调用推送,并处理
		    $result = $pushObj->new_push($receive,$title,$content,$m_type,$m_txt,$m_time,$userid);
		    if($result){
		        $res_arr = json_decode($result, true);
		        if(isset($res_arr['error'])){                       //如果返回了error则证明失败
		            echo $res_arr['error']['message'];          //错误信息
		            echo $res_arr['error']['code'];             //错误码
		            return false;       
		        }else{
		            //处理成功的推送......
		           // echo $res_arr['msg_id'];
		            return true;
		        }
		    }else{      //接口调用失败或无响应
		        echo '接口调用失败或无响应';
		        return false;
		    }
}*/


//积分兑换 用于计算后台的积分兑换规则
//规则如下 1美元等于多少积分
//根据下单价来进行自动结算 
//传入下单价 后台设置的积分兑换值 
// 如果1美元价值需要1积分  后台自行设置1美元价值 需要多少积分 保留小数点2位  那么10美元价值就需要10积分。

function Integral_quantity($goods_jiage){
 $goods_jiage = floor($goods_jiage) * C_READ('trial_point');
 $goods_jiage = ceil($goods_jiage );
 return $goods_jiage;
}


//根据商品id查询商家绑定旺旺
function id_wangwang($id){
  $data['userid'] = $id;
  model('member_merchant')->where($data)->getField('store_name'); 
}

/*发送验证码*/
function sms($mobile,$extr){
	extract($extr);
	$setting = getcache('setting', 'sms');
	require APP_PATH."taobao/dayu/TopSdk.php";
	$c = new TopClient;
    $c->appkey = $setting['username'];
	$c->secretKey =$setting['password'];
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	
	$req->setExtend("123456");
	$req->setSmsType("normal");
	$req->setSmsFreeSignName($setting['target']);
	$req->setSmsParam($param);
	$req->setRecNum($mobile);
	$req->setSmsTemplateCode($template_id);
	$resp = $c->execute($req);
	$result['err_code'] = (string)$resp->result->err_code;
	$result['success'] = (string)$resp->result->success;
	$result['msg'] = (string)$resp->result->sub_msg ? (string)$resp->result->sub_msg :'';
	return $result;

}



function deldir($dir) {
	$dh=opendir($dir);
	while ($file=readdir($dh)) {
	if($file != "." && $file!="..") {
	$fullpath=$dir."/".$file;
	if(!is_dir($fullpath)) {
	unlink($fullpath);
	} else {
	deldir($fullpath);
	}
	}
	}
	closedir($dh);
	if(rmdir($dir)) {
	return true;
	} else {
	return false;
	}
}



/*整合平台一系列通用接口*/

/**
* 数据加密、解密函数
*
*
* @param	string	$txt		字符串
* @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
* @param	string	$key		密钥：数字、字母、下划线
* @param	string	$expiry		过期时间
* @return	string
*/
function sys_auth($string, $operation = 'ENCODE',$expiry = 0) {
	$ckey_length = 4;
     $key = C('sso_key');
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

function auth_data($data,$action = '') {
	$s = $sep = '';
	foreach($data as $k => $v) {
		if(is_array($v)) {
			$s2 = $sep2 = '';
			foreach($v as $k2 => $v2) {
					$s2 .= "$sep2{$k}[$k2]="._ps_stripslashes($v2);
				$sep2 = '&';
			}
			$s .= $sep.$s2;
		} else {
			$s .= "$sep$k="._ps_stripslashes($v);
		}
		$sep = '&';
	}

	$auth_s ='type='.$action.'&v=trial&appid='.c('appid').'&data='.urlencode(sys_auth($s));
	return $auth_s;
}

/**
 * 过滤字符串
 * @param $string
 */
 function _ps_stripslashes($string) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(MAGIC_QUOTES_GPC) {
		return stripslashes($string);
	} else {
		return $string;
	}
}


	/**
	 * 发送数据 get 形式
	 * @param $action 操作
	 * @param $data 数据
	 */
	 function _get_send($action,$data = null) {

 		return _ps_post($action,500000,auth_data($data,$action),'',false);


	}



	/**
	 * 发送数据 post 形式
	 * @param $action 操作
	 * @param $data 数据
	 */
	 function _ps_send($action,$data = null) {
        //检测是否开启整合平台
		//获取整合平台信息
		//写入本地请求日志
		$data1 =array();
		$data1['time'] = NOW_TIME;
		$data1['ip'] = get_client_ip();
		$data1['action'] = $action;
		$data1['data'] = array2string($data);
		$result = model('xwpay_log')->add($data1);

	    if($result)$log_id = $result;
 		return _ps_post($action,500000,auth_data($data,$action),$log_id);
	}



	/**
	 *  post数据
	 *  @param string $url		post的url
	 *  @param int $limit		返回的数据的长度
	 *  @param string $post		post数据，字符串形式username='dalarge'&password='123456'
	 *  @param string $cookie	模拟 cookie，字符串形式username='dalarge'&password='123456'
	 *  @param string $ip		ip地址
	 *  @param string $ip		ip地址
	 *  @param int $timeout		连接超时时间
	 *  @param bool $block		是否为阻塞模式
	 *  @return string			返回字符串
	 */
	
      

	 function _ps_post($action, $limit = 0, $post = '',$log_id='',$is_post = true,$cookie = '', $ip = '', $timeout = 15, $block = true) {		$return = '';

	    $url = c('sso_address');
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

   /*   转换整合支付平台 返回的数据*/
   function php_data($data = null) {

   	 	if(!isset($data) && $data = null ) exit('0');
   	 	parse_str($data,$data1);

        return $data1;
   	}
  //获取后台管理员名称
  function get_admin_name($id){
	  return model('admin')->where(array('userid' =>$id))->getField('username'); 
  }
