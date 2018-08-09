	function groupid($field, $value, $fieldinfo) {
		$fieldinfo['setting'] = string2array($fieldinfo['setting']);
		$grouplist = getcache('member_group', 'member');
		foreach($grouplist as $_key=>$_value) {
			$data[$_key] = $_value['name'];
		}
		$defaultvalue = ($value) ? $value : str_replace('|', ',', $fieldinfo['setting']['groupids']);
		$form = new \Common\Library\form();
		return '<input type="hidden" name="info['.$field.']" value="1">'.$form::checkbox($data,$defaultvalue,'name="'.$field.'[]" id="'.$field.'"','','120');
	}
