	function picfile($field, $value, $fieldinfo) {
		$config = getcache('setting', 'document');
		$list_str = $str = '';
		extract(string2array($fieldinfo['setting']));
		if($value){
			$value = dhtml_entity_decode($value);
			$value = htmlspecialchars_decode($value);
			$value = mb_unserialize($value);
			$edit = 1;
		} else {
			$value = array();
			$edit = 0;
			$value = array();
		}
		if(is_array($config)) {
			foreach($config as $_k => $_v) {
				$list_str .= '标识：<input type="text" name="info[_'.$field.']['.$_k.'][name]" id="'.$field.'_'.$_k.'" value="'.$_v['name'].'" class="input-text" size="4">地址：<input type="text" name="info[_'.$field.']['.$_k.'][url]" id="'.$field.'_'.$_k.'" value="'.$value[$_k]['url'].'" class="input-text" style="width:60%;" onclick="this.select()">扣点：<input type="text" name="info[_'.$field.']['.$_k.'][point]" id="'.$field.'_'.$_k.'" value="'.$value[$_k]['point'].'" class="input-text" style="width:10%;"><div class="bk3"></div>';
			}
		}
		$string .= '<fieldset class="blue pad-10"><legend>普通授权</legend>';
		$string .= $list_str;
		$string .= '</fieldset><div class="bk10"></div>';

		$string .= '<fieldset class="blue pad-10 yellow"><legend>高级授权</legend>';
		$string .= '地址：<input type="text" name="info[_'.$field.'][self][url]" id="'.$field.'_self" value="'.$value['self']['url'].'" class="input-text" style="width:74%;" onclick="this.select()">扣点：<input type="text" name="info[_'.$field.'][self][point]" id="'.$picfile.'_'.$_k.'" value="'.$value['self']['point'].'" class="input-text" style="width:10%;">';
		$string .= '</fieldset><div class="bk10"></div>';


		if(!defined('IMAGES_INIT')) {
			$str = '<script type="text/javascript" src="'.JS_PATH.'swfupload/swf2ckeditor.js"></script>';
			define('IMAGES_INIT', 1);
		}
		$makepic = U('public_get_piclist');
		$authkey = upload_key("$upload_number,$upload_allowext,$isselectimage");	
		$string .= $str."<input type='text' name='info[$field]' id='$field' value='".$value['self']['url']."' class='input-text' style='width:80%'/>  <input type='button' class='button' onclick=\"javascript:flashupload('{$field}_downfield', '".L('attachment_upload')."','{$field}',submit_picfile,'{$upload_number},{$upload_allowext},{$isselectimage}','document','$this->catid','{$authkey}')\"/ value='".L('upload_soft')."'>";
		$string .= <<<EOF
<script text="text/javascript">
function submit_picfile (uploadid,returnid) {
	submit_files(uploadid,returnid);
	var d = window.top.art.dialog({id:uploadid}).data.iframe;
	var in_content = d.$("#att-status").html().substring(1);
	var in_content = in_content.split('|');
	var new_filepath = in_content[0].replace(uploadurl,'/');
	$.getJSON("{$makepic}", {picfile : new_filepath, ismake : 0}, function(result) {
		$.each(result,function(k,v){
			$("input[name='info[_picfile]["+k+"][url]']").val(v);
		});
	});
}
</script>
EOF;
		return $string;
	}