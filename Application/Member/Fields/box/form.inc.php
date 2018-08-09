	function box($field, $value, $fieldinfo) {
		$setting = string2array($fieldinfo['setting']);
		if($value=='') $value = $setting['defaultvalue'];
		$options = explode("\r", $setting['options']);
		foreach($options as $_k) {
			$v = explode("|",$_k);
			$k = trim($v[1]) ? $v[1] : $v[0];
			$option[$k] = $v[0];
		}
		$values = explode(',',$value);
		$value = array();
		foreach($values as $_k) {
			if($_k != '') $value[] = $_k;
		}
		$value = implode(',',$value);
		$form = new \Common\Library\form();		
		switch($setting['boxtype']) {
			case 'radio':
				$string = $form::radio($option,$value,"name='info[$field]'",$setting['width'],$field);
			break;

			case 'checkbox':
				$string = $form::checkbox($option,$value,"name='info[$field][]'",1,$setting['width'],$field);
			break;

			case 'select':
				$string = $form::select($option,$value,"name='info[$field]' id='$field'");
			break;

			case 'multiple':
				$string = $form::select($option,$value,"name='info[$field][]' id='$field' size=2 multiple='multiple' style='height:60px;'");
			break;
		}
		return $string;
	}