<?php
class content_input {
	var $modelid;
	var $fields;
	var $data;

    function __construct($modelid) {
		$this->db_pre = C('DB_PREFIX');
		$this->modelid = $modelid;
		$this->fields = getcache('model_field_'.$modelid, 'model');
		$this->error = '';
		//初始化附件类
		// pc_base::load_sys_class('attachment','',0);
		// $this->siteid = param::get_cookie('siteid');
		// $this->attachment = new attachment('content','0',$this->siteid);
    }

	function get($data,$isimport = 0) {
		$this->data = $data = trim_script($data);
		$info = array();
		foreach($data as $field=>$value) {
			if(!isset($this->fields[$field]) && !check_in($field,'paytype,paginationtype,maxcharperpage,id')) continue;
			if(defined('IN_ADMIN')) {
				if(check_in(session('roleid'), $this->fields[$field]['unsetroleids'])) continue;
			} else {
				$_groupid = cookie('_groupid');
				if(check_in($_groupid, $this->fields[$field]['unsetgroupids'])) continue;
			}
			$name = $this->fields[$field]['name'];
			$minlength = $this->fields[$field]['minlength'];
			$maxlength = $this->fields[$field]['maxlength'];
			$pattern = $this->fields[$field]['pattern'];
			$errortips = $this->fields[$field]['errortips'];
			if(empty($errortips)) $errortips = $name.' '.L('not_meet_the_conditions');
			$length = empty($value) ? 0 : (is_string($value) ? strlen($value) : count($value));

			if($minlength && $length < $minlength) {
				if(!$isimport) {					
					$this->error = $name.' '.L('not_less_than').' '.$minlength.L('characters');
				} 
				return false;
			}
			if($maxlength && $length > $maxlength) {
				if($isimport) {
					$value = str_cut($value,$maxlength,'');
				} else {
					$this->error = $name.' '.L('not_more_than').' '.$maxlength.L('characters');
					return false;
				}
			} elseif($maxlength) {
				$value = str_cut($value,$maxlength,'');
			}
			if($pattern && $length && !preg_match($pattern, $value) && !$isimport) {
				$this->error = $errortips;
				return false;
			}
			$models = getcache('model','commons');         
            if($this->fields[$field]['isunique'] && ACTION_NAME != 'edit') {
            	$this->db->table_name = $this->fields[$field]['issystem'] ? $this->db_pre.$models[$this->modelid]['tablename'] : $this->db_pre.$models[$this->modelid]['tablename'].'_data';
            	if(M()->query("SELECT {$field} FROM `{$this->db->table_name}` WHERE `{$field}` = '{$value}' LIMIT 0,1")) {
	            	$this->error = $name.L('the_value_must_not_repeat');
	            	return false;
            	}
            }
			$func = $this->fields[$field]['formtype'];
			if(method_exists($this, $func)) $value = $this->$func($field, $value);
			if($this->fields[$field]['issystem']) {
				$info['system'][$field] = $value;
			} else {
				$info['model'][$field] = $value;
			}
			//颜色选择为隐藏域 在这里进行取值
			$info['system']['style'] = $_POST['style_color'] && preg_match('/^#([0-9a-z]+)/i', $_POST['style_color']) ? $_POST['style_color'] : '';
			if($_POST['style_font_weight']=='bold') $info['system']['style'] = $info['system']['style'].';'.strip_tags($_POST['style_font_weight']);
		}
		return $info;
	}

	public function getError() {
		return $this->error;
	}
}?>