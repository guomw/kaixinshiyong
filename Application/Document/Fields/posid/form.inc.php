	function posid($field, $value, $fieldinfo) {
        return FALSE;
		$setting = string2array($fieldinfo['setting']);
		$position = getcache('position', 'commons');
		if(empty($position)) return '';
		$array = array();
		foreach($position as $_key=>$_value) {
			if($_value['modelid'] && ($_value['modelid'] !=  $this->modelid) || ($_value['catid'] && strpos(','.$this->categorys[$_value['catid']]['arrchildid'].',',','.$this->catid.',')===false)) continue;
			$array[$_key] = $_value['name'];
		}
		$posids = array();
		if(ACTION_NAME == 'edit') {
			$result = D('PositionData')->where(array('id'=>$this->id,'modelid'=>$this->modelid))->getField('posid',TRUE);
			$posids = implode(',', $result);
		} else {
			$posids = $setting['defaultvalue'];
		}
		$form = new \Common\Library\form();
		return $form::checkbox($array,$posids,"name='info[$field][]'",'',$setting['width']);
	}
