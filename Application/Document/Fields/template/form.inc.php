	function template($field, $value, $fieldinfo) {
		$default_style = C('DEFAULT_THEME') ? C('DEFAULT_THEME') : 'default';
		$form = new \Common\Library\form();
		return $form::select_template($default_style,'document',$value,'name="info['.$field.']" id="'.$field.'"','show');
	}
