	function datetime($field, $value) {
		$setting = $this->fields[$field]['setting'];
		if($setting['fieldtype']=='int') {
			$value = strtotime($value);
		}
		return $value;
	}
