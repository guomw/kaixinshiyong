<?php
namespace Common\Library;
class form {
	/**
	 * 编辑器
	 * @param int $textareaid
	 * @param int $toolbar 
	 * @param string $module 模块名称
	 * @param int $catid 栏目id
	 * @param int $color 编辑器颜色
	 * @param boole $allowupload  是否允许上传
	 * @param boole $allowbrowser 是否允许浏览文件
	 * @param string $alowuploadexts 允许上传类型
	 * @param string $height 编辑器高度
	 * @param string $disabled_page 是否禁用分页和子标题
	 */
	public static function editor($textareaid = 'content', $toolbar = 'basic', $module = '', $catid = '', $color = '', $allowupload = 1, $allowbrowser = 1,$alowuploadexts = '',$height = 200,$disabled_page = 0, $allowuploadnum = '10') {
        $string = '';
        
        if(!defined('EDITOR_INIT')) {
            $string .= '<link href="'. JS_PATH .'ueditor/themes/default/css/ueditor.css" type="text/css" rel="stylesheet">';
            //$string .= '<script type="text/javascript" src="'.JS_PATH.'ueditor/third-party/jquery.min.js"></script>';
            $string .= '<script type="text/javascript" src="'.JS_PATH.'ueditor/ueditor.config.js"></script>';
            $string .= '<script type="text/javascript" src="'.JS_PATH.'ueditor/ueditor.all.js"></script>';
            $string .= '<script type="text/javascript" src="'.JS_PATH.'ueditor/lang/zh-cn/zh-cn.js"></script>';
        }
        /* 工具条 */
        if($toolbar == 'basic') {
            $toolbar = "['fullscreen', 'bold', 'italic', '|', 'forecolor', 'backcolor', 'fontfamily','fontsize', 'insertunorderedlist', 'insertorderedlist', '|', 'link', 'unlink', '|', 'simpleupload', 'insertimage']";
        } elseif($toolbar == 'full') {
            $toolbar = (defined('IN_ADMIN')) ? "['source'," : "[";
            $toolbar .= "'fullscreen','undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'scrawl', 'insertvideo', 'music', 'attachment', '|', 'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|', 'preview', 'searchreplace', 'help', 'drafts']";
        } elseif ($toolbar == 'desc') {
            $toolbar = "['fullscreen', 'bold', 'italic', '|', 'forecolor', 'backcolor', 'fontfamily','fontsize', 'insertunorderedlist', 'insertorderedlist', '|', 'link', 'unlink', '|', 'simpleupload', 'insertimage']";
        } else {
            $toolbar = '';
        }
        if($toolbar) {
            $toolbar = ',toolbars:['.$toolbar.']';
        }
        $string .= '<script type="text/javascript">var ue = UE.getEditor(\''.$textareaid.'\', {lang:"zh-cn", serverUrl:"'.U('Attachment/Attachment/editor').'", initialFrameWidth:"98%", initialFrameHeight:"'.$height.'"'.$toolbar.'})</script>';
        return $string;
	}
	
	/**
	 * 
	 * @param string $name 表单名称
	 * @param int $id 表单id
	 * @param string $value 表单默认值
	 * @param string $moudle 模块名称
	 * @param int $catid 栏目id
	 * @param int $size 表单大小
	 * @param string $class 表单风格
	 * @param string $ext 表单扩展属性 如果 js事件等
	 * @param string $alowexts 允许图片格式
	 * @param array $thumb_setting 
	 * @param int $watermark_setting  0或1
	 */
	public static function images($name, $id = '', $value = '', $moudle='', $catid='', $size = 50, $class = '', $ext = '', $alowexts = '',$thumb_setting = array(),$watermark_setting = 0, $button_text = '上传图片') {
		if(!$id) $id = $name;
		if(!$size) $size= 50;
		if(!empty($thumb_setting) && count($thumb_setting)) $thumb_ext = $thumb_setting[0].','.$thumb_setting[1];
		else $thumb_ext = ',';
		if(!$alowexts) $alowexts = 'jpg|jpeg|gif|bmp|png';
		if(!defined('IMAGES_INIT')) {
			$str = '<script type="text/javascript" src="'.JS_PATH.'swfupload/swf2ckeditor.js"></script>';
			define('IMAGES_INIT', 1);
		}
		$authkey = upload_key("1,$alowexts,1,$thumb_ext,$watermark_setting");
		return $str."<input type=\"text\" name=\"$name\" id=\"$id\" value=\"$value\" size=\"$size\" class=\"$class\" $ext/>  <input type=\"button\" class=\"button\" onclick=\"javascript:flashupload('{$id}_images', '{$button_text}','{$id}',submit_images,'1,{$alowexts},1,{$thumb_ext},{$watermark_setting}','{$moudle}','{$catid}','{$authkey}')\"/ value=\"{$button_text}\">";
	}

	/**
	 * 
	 * @param string $name 表单名称
	 * @param int $id 表单id
	 * @param string $value 表单默认值
	 * @param string $moudle 模块名称
	 * @param int $catid 栏目id
	 * @param int $size 表单大小
	 * @param string $class 表单风格
	 * @param string $ext 表单扩展属性 如果 js事件等
	 * @param string $alowexts 允许上传的文件格式
	 * @param array $file_setting 
	 */
	public static function upfiles($name, $id = '', $value = '', $moudle='', $catid='', $size = 50, $class = '', $ext = '', $alowexts = '',$file_setting = array() ) {
		if(!$id) $id = $name;
		if(!$size) $size= 50;
		if(!empty($file_setting) && count($file_setting)) $file_ext = $file_setting[0].','.$file_setting[1];
		else $file_ext = ',';
		if(!$alowexts) $alowexts = 'rar|zip';
		if(!defined('IMAGES_INIT')) {
			$str = '<script type="text/javascript" src="'.JS_PATH.'swfupload/swf2ckeditor.js"></script>';
			define('IMAGES_INIT', 1);
		}
		$authkey = upload_key("1,$alowexts,1,$file_ext");
		return $str."<input type=\"text\" name=\"$name\" id=\"$id\" value=\"$value\" size=\"$size\" class=\"$class\" $ext/>  <input type=\"button\" class=\"button\" onclick=\"javascript:flashupload('{$id}_files', '".L('attachmentupload')."','{$id}',submit_attachment,'1,{$alowexts},1,{$file_ext}','{$moudle}','{$catid}','{$authkey}')\"/ value=\"".L('filesupload')."\">";
	}
	
	/**
	 * 日期时间控件
	 * 
	 * @param $name 控件name，id
	 * @param $value 选中值
	 * @param $isdatetime 是否显示时间
	 * @param $loadjs 是否重复加载js，防止页面程序加载不规则导致的控件无法显示
	 * @param $showweek 是否显示周，使用，true | false
	 */
	public static function date($name, $value = '', $isdatetime = 0, $loadjs = 0, $showweek = 'true', $timesystem = 1) {
		if($value == '0000-00-00 00:00:00') $value = '';
		$id = preg_match("/\[(.*)\]/", $name, $m) ? $m[1] : $name;
		if($isdatetime) {
			$size = 21;
			$format = '%Y-%m-%d %H:%M:%S';
			if($timesystem){
				$showsTime = 'true';
			} else {
				$showsTime = '12';
			}
			
		} else {
			$size = 10;
			$format = '%Y-%m-%d';
			$showsTime = 'false';
		}
		$str = '';
		if($loadjs || !defined('CALENDAR_INIT')) {
			define('CALENDAR_INIT', 1);
			$str .= '<link rel="stylesheet" type="text/css" href="'.JS_PATH.'calendar/jscal2.css"/>
			<link rel="stylesheet" type="text/css" href="'.JS_PATH.'calendar/border-radius.css"/>
			<link rel="stylesheet" type="text/css" href="'.JS_PATH.'calendar/win2k.css"/>
			<script type="text/javascript" src="'.JS_PATH.'calendar/calendar.js"></script>
			<script type="text/javascript" src="'.JS_PATH.'calendar/lang/en.js"></script>';
		}
		$str .= '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" size="'.$size.'" class="date" readonly>&nbsp;';
		$str .= '<script type="text/javascript">
			Calendar.setup({
			weekNumbers: '.$showweek.',
		    inputField : "'.$id.'",
		    trigger    : "'.$id.'",
		    dateFormat: "'.$format.'",
		    showTime: '.$showsTime.',
		    minuteStep: 1,
		    onSelect   : function() {this.hide();}
			});
        </script>';
		return $str;
	}

	/**
	 * @param string $name 		表单名
	 * @param string $value		表单值
	 * @param string $id		表单ID
	 * @param string $showtime	是否显示时间
	 * @param string $options	配置项
	 */
	public static function datepicker($name = '', $value = '', $id = '', $showtime = 0,  $options = '') {
		$string = '';
		$size = ($showtime == 0) ? 10 : 21;
		if(!defined('DATAPICKER')) {
			$string .= '<script type="text/javascript" src="' . JS_PATH . 'datepicker/WdatePicker.js"></script>';
		}
		$string .= '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" size="'.$size.'" class="date" onclick="WdatePicker('.$options.')" readonly>&nbsp;';
		return $string;
	}

	/**
	 * 栏目选择
	 * @param intval/array $catid 选中的ID，多选是可以是数组
	 * @param string $str 属性
	 * @param string $default_option 默认选项
	 * @param intval $modelid 按所属模型筛选
	 * @param intval $type 栏目类型
	 * @param intval $onlysub 只可选择子栏目
	 */
	public static function select_category($catid = 0, $str = '', $default_option = '', $modelid = 0, $type = -1, $onlysub = 0) {
		$tree = new \Common\Library\tree;
		if ($type == -1) {
			$result = getcache('category', 'commons');
		}elseif($type == 0){
			$result = getcache('product_category', 'commons');
		}
		
		$string = '<select '.$str.'>';
		if($default_option) $string .= "<option value='0'>$default_option</option>";
		if (is_array($result)) {
			foreach($result as $r) {
				$r['selected'] = '';
				if(is_array($catid)) {
					$r['selected'] = in_array($r['catid'], $catid) ? 'selected' : '';
				} elseif(is_numeric($catid)) {
					$r['selected'] = $catid==$r['catid'] ? 'selected' : '';
				}
				$r['html_disabled'] = "0";
				if (!empty($onlysub) && $r['child'] != 0) {
					$r['html_disabled'] = "1";
				}
				$categorys[$r['catid']] = $r;
				if($modelid && $r['modelid']!= $modelid ) unset($categorys[$r['catid']]);
			}
		}
		$str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>;";
		$str2 = "<optgroup label='\$spacer \$catname'></optgroup>";
		$tree->init($categorys);
		$string .= $tree->get_tree_category(0, $str, $str2);			
		$string .= '</select>';
		return $string;
	}
    
    /**
     * 栏目选择
     */
    public static function select_product_category($field = '', $value = 0) {
        $value = (int) $value;
		$publish_str = '<input type="hidden" name="info['.$field.']" id="linkage_input_'.$field.'" value="'.$value.'" />';		
        $categorys = getcache('product_category', 'commons');
        $category = array();
        foreach ($categorys as $k => $cat) {
                $v['id'] = $cat['catid'];
                $v['name'] = $cat['catname'];
                $v['parentid'] = $cat['parentid'];
                $category[] = $v;
        }
        $defVal = '';
        if($value > 0) {
            $parentid_str = $categorys[$value]['arrparentid'].','.$value;
            $parentid_arr = explode(',', $parentid_str);
            array_shift($parentid_arr);
            $defVal = implode(',', $parentid_arr);
            $defVal = ',defVal: ['.$defVal.']';
        }

        $tree_list = list_to_tree($category, 'id', 'parentid', 'cell');
        if (!defined('LINKAGE_JS_INIT')) {
            $publish_str .= '<script type="text/javascript" src="'.JS_PATH.'linkage/js/linkagesel.min.js"></script>';
        }
        $publish_str .= '<select id="linkage_'.$field.'"></select>';	
        $publish_str .= '<script type="text/javascript">
var '.$field.'_opts = {
        data: '.json_encode($tree_list).'
        ,selStyle: "margin-left: 3px;"
        ,select:  "#linkage_'.$field.'"
        ,loaderImg:"'.IMG_PATH.'msg_img/loading.gif"
        ,autoLink:false
        ,head:"请选择"'.$defVal.'
};
        var linkage_'.$field.' = new LinkageSel('.$field.'_opts);
        
        linkage_'.$field.'.onChange(function() {
            $("#linkage_input_'.$field.'").attr("value", this.getSelectedValue());
        });
      </script>';
		return $publish_str;      
    }

    public static function select_linkage($keyid = 0, $parentid = 0, $name = 'parentid', $id ='', $alt = '', $linkageid = 0, $property = '') {
		$tree = new \Common\Library\tree();
		$result = getcache('linkage_'.$keyid, 'linkage');
		$id = $id ? $id : $name;
		$string = "<select name='$name' id='$id' $property>\n<option value='0'>$alt</option>\n";
		if($result['data']) {
			foreach($result['data'] as $area) {	
				$categorys[$area['linkageid']] = array('id'=>$area['linkageid'], 'parentid'=> $area['parentid'], 'name'=>$area['name']);	
			}
		}
		$str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
		$tree->init($categorys);
		$string .= $tree->get_tree($parentid, $str, $linkageid);			
		$string .= '</select>';
		return $string;
	}
    
	/**
	 * 下拉选择框
	 */
	/* public static function select($name, $value, $array = array(), $default = '', $class='', $width = 0) {
		if(!is_array($array) || count($array)== 0) return false;
		$str = '';
		if($class) $str .= 'class="'.$class.'"';
		if($width) $str .= 'style="width:'.$width.'"';
		$string = '<select name="'.$name.'" '.$str.'>';
		if($default) $string.= '<option>'.$default.'</option>';	
		foreach($array as $k=>$v) {
			$selected = ($k == $value) ? 'selected' : '';
			$string .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
		}
		$string .= '</select>';
		return $string;
	} */
	/**
	 * 下拉选择框
	 */
	public static function select($array = array(), $id = 0, $str = '', $default_option = '') {
		$string = '<select '.$str.'>';
		$default_selected = (empty($id) && $default_option) ? 'selected' : '';
		if($default_option) $string .= "<option value='' $default_selected>$default_option</option>";
		if(!is_array($array) || count($array)== 0) return false;
		$ids = array();
		if(isset($id)) $ids = explode(',', $id);
		foreach($array as $key=>$value) {
			$selected = in_array($key, $ids) ? 'selected' : '';
			$string .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
		}
		$string .= '</select>';
		return $string;
	}

	/**
	 * 复选框
	 *
	 * @param $array 选项 二维数组
	 * @param $id 默认选中值，多个用 '逗号'分割
	 * @param $str 属性
	 * @param $defaultvalue 是否增加默认值 默认值为 -99
	 * @param $width 宽度
	 */
	public static function checkbox($name, $value, $array = array(), $class='', $width = 0) {
		$string = '';
		if($value != '') $value = strpos($value, ',') ? explode(',', trim($value)) : (array)$value;
		foreach($array as $k => $v) {
			$k = trim($k);
			$checked = ($value && in_array($k, $value)) ? 'checked' : '';
			$width = ($width) ? 'width:'.$width.'px;' : '';
			$string .= '<label class="ib" style="width:'.$width.'px">';
			$string .= '<input type="checkbox" id="'.$name.'_'.$i.'" value="'.htmlspecialchars($k).'" '.$checked.' name="'.$name.'[]"/> '.htmlspecialchars($v).'&nbsp;';
			$string .= '</label>';
			unset($checked);
			$i++;
		}
		return $string;
	}


	/**
	 * 单选框
	 * 
	 * @param $array 选项 二维数组
	 * @param $id 默认选中值
	 * @param $str 属性
	 */
	public static function radio($name, $value, $array = array(), $class='', $width = 0) {
		$string = '';
		foreach($array as $k => $v) {
			$checked = trim($value)==trim($k) ? 'checked' : '';
			$style = '';
			if($width > 0) $style .= "width:".$width."px";
			$string .= '<label class="ib '.$class.'" style="'.$style.'">';
			$string .= '<input type="radio" id="'.$name.'_'.htmlspecialchars($k).'" '.$checked.' value="'.$k.'" name="'.$name.'">'.$v.'&nbsp;';
			$string .= '</label>';
		}
		return $string;
	}
	/**
	 * 模板选择
	 * 
	 * @param $style  风格
	 * @param $module 模块
	 * @param $id 默认选中值
	 * @param $str 属性
	 * @param $pre 模板前缀
	 */
	public static function select_template($style, $module, $id = '', $str = '', $pre = '') {
		// $tpl_root = TPADMIN_TMPL;
		$templatedir = TPL_PATH.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR;
		$confing_path = $TPL_PATH.$style.DIRECTORY_SEPARATOR.'config.php';
		$localdir = str_replace(array('/', '\\'), '', $TPL_PATH).'|'.$style.'|'.$module;
		$templates = glob($templatedir.$pre.'*.html');
		if(empty($templates)) {
			$style = 'default';
			$templatedir = $tpl_root.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR;
			$confing_path = $tpl_root.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.'config.php';
			$localdir = str_replace(array('/', '\\'), '', $tpl_root).'|'.$style.'|'.$module;
			$templates = glob($templatedir.$pre.'*.html');
		}
		if(empty($templates)) return false;
		$files = @array_map('basename', $templates);
		$names = array();
		if(file_exists($confing_path)) {
			$names = include $confing_path;
		}
		$templates = array();
		if(is_array($files)) {
			foreach($files as $file) {
				$key = substr($file, 0, -5);
				$templates[$key] = isset($names['file_explan'][$module][$file]) && !empty($names['file_explan'][$module][$file]) ? $names['file_explan'][$module][$file].'('.$file.')' : $file;
			}
		}
		ksort($templates);
		return self::select($templates, $id, $str, '请选择');
	}
	
	/**
	 * 验证码
	 * @param string $id            生成的验证码ID
	 * @param integer $code_len     生成多少位验证码
	 * @param integer $font_size    验证码字体大小
	 * @param integer $width        验证图片的宽
	 * @param integer $height       验证码图片的高
	 * @param string $font          使用什么字体，设置字体的URL
	 * @param string $font_color    字体使用什么颜色
	 * @param string $background    背景使用什么颜色
	 */
	public static function checkcode($id = 'checkcode',$code_len = 4, $font_size = 20, $width = 130, $height = 50, $font = '', $font_color = '', $background = '') {
		return "<img id='$id' onclick='this.src=this.src+\"&\"+Math.random()' src='".SITE_PROTOCOL.SITE_URL.WEB_PATH."api.php?op=checkcode&code_len=$code_len&font_size=$font_size&width=$width&height=$height&font_color=".urlencode($font_color)."&background=".urlencode($background)."'>";
	}
	/**
	 * url  规则调用
	 * 
	 * @param $module 模块
	 * @param $name 文件名
	 * @param $ishtml 是否为静态规则
	 * @param $id 选中值
	 * @param $str 表单属性
	 * @param $default_option 默认选项
	 */
	public static function urlrule($module, $name, $ishtml, $id, $str = '', $default_option = '') {
		if(!$module) $module = 'Document';
		$urlrules = getcache('urlrule', 'commons');
		$array = array();
		foreach($urlrules as $roleid => $rules) {
			if($rules['module'] == $module && $rules['action']==$name && $rules['ishtml'] == $ishtml) $array[$roleid] = $rules['example'];
		}		
		return form::select($array, $id,$str,$default_option);
	}
}

?>