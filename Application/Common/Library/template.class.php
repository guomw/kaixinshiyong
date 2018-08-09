<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Library;
use \Think\Storage; //加载分布式文件系统
class template {
	public $config       =   array();
	
	public function __construct() {
		$this->config['cache_path']      = C('CACHE_PATH');
		$this->config['template_suffix'] = C('TMPL_TEMPLATE_SUFFIX');
		$this->config['cache_suffix']    = C('TMPL_CACHFILE_SUFFIX');
		$this->config['tmpl_cache']      = C('TMPL_CACHE_ON');
		$this->config['cache_time']      = C('TMPL_CACHE_TIME');
		$this->config['taglib_begin']    = C('TAGLIB_BEGIN');
		$this->config['taglib_end']      = C('TAGLIB_END');
		$this->config['taglib_name']     = C('TAGLIB_NAME');
	}

	/**
	 * 获取模板内容
	 * @author   <jason>
	 */
	public function fetch($template, &$var, $prefix = '') {
        $templateCacheFile  =   $this->loadTemplate($template, $prefix);
        Storage::load($templateCacheFile, $var, null, 'tpl');
	}

	/**
	 * 加载模板并缓存
	 * @author   <jason>
	 */
	public function loadTemplate($template='', $prefix='') {
		/* 模板临时内容 */
		$tmplContent = file_exists($template) ? @file_get_contents($template) : $template;
		/* 缓存文件名 */
		$tmplCacheFile = $this->config['cache_path'].$prefix.md5($template).$this->config['cache_suffix'];
		/* 编译模板 */
		$tmplContent =  $this->compileTemplate($tmplContent);
		/* 保存缓存 */
		Storage::put($tmplCacheFile,trim($tmplContent),'tpl');
		/* 返回缓存文件名 */
		return $tmplCacheFile;
	}

	/**
	 * 解析模板
	 * @author   <jason>
	 */
	public function compileTemplate($content) {
		$content = preg_replace ( "/\{template\s+(.+)\}/", "<?php include template(\\1); ?>", $content );
		$content = preg_replace ( "/\{include\s+(.+)\}/", "<?php include \\1; ?>", $content );
		$content = preg_replace ( "/\{php\s+(.+)\}/", "<?php \\1?>", $content );
		$content = preg_replace ( "/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $content );
		$content = preg_replace ( "/\{else\}/", "<?php } else { ?>", $content );
		$content = preg_replace ( "/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $content );
		$content = preg_replace ( "/\{\/if\}/", "<?php } ?>", $content );
		//for 循环
		$content = preg_replace("/\{for\s+(.+?)\}/","<?php for(\\1) { ?>",$content);
		$content = preg_replace("/\{\/for\}/","<?php } ?>",$content);
		//++ --
		$content = preg_replace("/\{\+\+(.+?)\}/","<?php ++\\1; ?>",$content);
		$content = preg_replace("/\{\-\-(.+?)\}/","<?php ++\\1; ?>",$content);
		$content = preg_replace("/\{(.+?)\+\+\}/","<?php \\1++; ?>",$content);
		$content = preg_replace("/\{(.+?)\-\-\}/","<?php \\1--; ?>",$content);
		$content = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\}/", "<?php \$n=1;if(is_array(\\1)) foreach(\\1 AS \\2) { ?>", $content );
		$content = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/", "<?php \$n=1; if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>", $content );
		$content = preg_replace ( "/\{\/loop\}/", "<?php \$n++;}unset(\$n); ?>", $content );
		$content = preg_replace ( "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $content );
		$content = preg_replace ( "/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $content );
		$content = preg_replace ( "/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $content );
		$content = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "\$this->addquote('<?php echo \\1;?>')",$content);
		$content = preg_replace ( "/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $content );

		$content = preg_replace("/\{".$this->config['taglib_name'].":(\w+)\s+([^}]+)\}/ie", "self::begin_tag('$1','$2', '$0')", $content);

		$content = preg_replace("/\{\/".$this->config['taglib_name']."\}/ie", "self::end_tag()", $content);
		
		$content = "<?php defined('IN_TPCMS') or exit('No permission resources.'); ?>" . $content;
		return $content;
	}

	

	/**
	 * 字符转义
	 * @author   <jason>
	 */
	public function addquote($var) {
		return str_replace ( "\\\"", "\"", preg_replace ( "/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var ) );
	}

	static public function begin_tag($op, $data, $html) {
		preg_match_all("/([\w]+)\=[\"|']?([^\"|']+)[\"|']?/i", stripslashes($data), $matches, PREG_SET_ORDER);
		$arr = array('action','num','cache','page', 'pagesize', 'urlrule', 'return', 'start');
		$tools = array('json', 'xml', 'block', 'get');
		$datas = array();
		$tag_id = md5(stripslashes($html));
		$str_datas = 'op='.$op.'&tag_md5='.$tag_id;
		foreach ($matches as $v) {
			$str_datas .= $str_datas ? "&$v[1]=".($op == 'block' && strpos($v[2], '$') === 0 ? $v[2] : urlencode($v[2])) : "$v[1]=".(strpos($v[2], '$') === 0 ? $v[2] : urlencode($v[2]));
			if(in_array($v[1], $arr)) {
				$$v[1] = $v[2];
				continue;
			}
			$datas[$v[1]] = $v[2];
		}

		if (strpos($num, '$') === 0) {
			$num = $num;
		} else {
			$num = isset($num) && intval($num) ? intval($num) : 20;
		}

		$cache = isset($cache) && intval($cache) ? intval($cache) : 0;
		$return = isset($return) && trim($return) ? trim($return) : 'data';
		
		if (in_array($op,$tools)) {
			switch ($op) {
				case 'get':
					if (isset($start) && intval($start)) {
						$limit = intval($start).','.$num;
					} else {
						$limit = $num;
					}
					$sql = str_replace("{prefix}", C('DB_PREFIX'), $datas['sql']);
					$string .= '$'.$return.' = M()->query('.$sql.');';
					break;			
				default:
					# code...
					break;
			}
		} else {
			if (!isset($action) || empty($action)) return false;
			$string = '';
			$taglib_file = APP_PATH.ucwords($op).DIRECTORY_SEPARATOR.'Taglib'.DIRECTORY_SEPARATOR.$op.'.class.php';
			if (file_exists($taglib_file)) {
				if (!defined($op.'_tag')) {					
					$string .= "require_once('".$taglib_file."');";
					$op = (!empty($taglib)) ? trim($taglib) : $op;
					$string .= '$'.$op.'_tag = new '.$op.'();';
				}
				// define($op.'_tag', TRUE);			
				$string .= 'if(method_exists($'.$op.'_tag, \''.$action.'\')) {';
				if (isset($start) && intval($start)) {
					$datas['limit'] = intval($start).','.$num;
				} else {
					$datas['limit'] = $num;
				}
				if (isset($page)) {
					$datas['page'] = $page;
					$string .= '$count = $'.$op.'_tag->count('.self::array2html($datas).');';
					$string .= '$pages = pages($count, '.$num.', $page);';
				}
				$string .= '$'.$return.' = $'.$op.'_tag->'.$action.'('.self::array2html($datas).');';	
				$string .= '}';
			}
		}
		return '<?php '.$string.' ?>';
	}

	static private function end_tag() {
		return '';
		$string = <<<EOT
<?php
if(defined('IN_ADMIN') && !defined('HTML')) {
	echo '</div>';
}
?>
EOT;
		return $string;
	}

	/**
	 * 转换数据为HTML代码
	 * @param array $data 数组
	 */
	private static function array2html($data) {
		if (is_array($data)) {
			$str = 'array(';
			foreach ($data as $key=>$val) {
				if (is_array($val)) {
					$str .= "'$key'=>".self::array2html($val).",";
				} else {
					if (strpos($val, '$')===0) {
						$str .= "'$key'=>$val,";
					} else {
						$str .= "'$key'=>'".daddslashes($val)."',";
					}
				}
			}
			return $str.')';
		}
		return false;
	}
}