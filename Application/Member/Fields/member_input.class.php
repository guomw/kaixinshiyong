<?php
class member_input {
	var $modelid;
	var $fields;
	var $data;

    function __construct($modelid) {
		// $this->db = pc_base::load_model('sitemodel_field_model');
		// $this->db_pre = $this->db->db_tablepre;
		$this->modelid = $modelid;
		$this->models = getcache('model', 'commons');
		$this->fields = getcache('model_field_'.$modelid, 'model');
		$this->db = M($this->models[$modelid]['tablename']);
		$this->error = '';
		//初始化附件类
    }

	function get($data) {
		$this->data = $data = trim_script($data);
		$info = array();
		$debar_filed = array('catid','title','style','thumb','status','islink','description');
		if(is_array($data)) {
			foreach($data as $field=>$value) {
				if($data['islink']==1 && !in_array($field,$debar_filed)) continue;
				$field = safe_replace($field);
				$name = $this->fields[$field]['name'];
				$minlength = $this->fields[$field]['minlength'];
				$maxlength = $this->fields[$field]['maxlength'];
				$pattern = $this->fields[$field]['pattern'];
				$errortips = $this->fields[$field]['errortips'];
				if(empty($errortips)) $errortips = "$name 不符合要求！";
				$length = empty($value) ? 0 : strlen($value);
				if($minlength && $length < $minlength && !$isimport) {
					$this->error = "$name 不得少于 $minlength 个字符！";
					return FALSE;
				}
				if (!array_key_exists($field, $this->fields)) {
					$this->error = '模型中不存在'.$field.'字段';
					return FALSE;
				}
				if($maxlength && $length > $maxlength && !$isimport) {
					$this->error = "$name 不得超过 $maxlength 个字符！";
					return FALSE;
				} else {
					str_cut($value, $maxlength);
				}
				if($pattern && $length && !preg_match($pattern, $value) && !$isimport) {
					$this->error = $errortips;
					return FALSE;
				}
	            if($this->fields[$field]['isunique'] && $this->db->where(array($field=>$value))->count() && ACTION_NAME != 'edit') {
	            	$this->error = "$name 的值不得重复！";
	            	return FALSE;
	            }
				$func = $this->fields[$field]['formtype'];
				if(method_exists($this, $func)) $value = $this->$func($field, $value);
	
				$info[$field] = $value;
			}
		}
		return $info;
	}

	public function getError() {
		return $this->error;
	}
}?>