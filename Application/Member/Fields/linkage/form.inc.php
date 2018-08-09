	function linkage($field, $value, $fieldinfo) {
		$setting = $fieldinfo['setting'];
		$linkageid = $setting['linkageid'];
		return menu_linkage($linkageid,$field,$value);
	}
