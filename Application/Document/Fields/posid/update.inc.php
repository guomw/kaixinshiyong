	function posid($field, $value) {
        return TRUE;
		$position_data_db = M('PositionData');
		$thumb = $this->data['thumb'] ? 1 : 0;
		if(ACTION_NAME == 'add') {
			$textcontent = array();
			foreach($value as $r) {
				if($r!='-1') {
					if(empty($textcontent)) {
						foreach($this->fields as $_key=>$_value) {
							if($_value['isposition']) {
								$textcontent[$_key] = $this->data[$_key];
							}
						}
						$textcontent = array2string($textcontent);
					}
					
					$position_data_db->add(array('id'=>$this->id,'catid'=>$this->data['catid'],'posid'=>$r,'thumb'=>$thumb,'modelid'=>$this->modelid,'data'=>$textcontent,'listorder'=>$this->id));
				}
			}
		} else {
			$posids = array();
			$catid = $this->data['catid'];
			foreach($value as $r) {
				if($r!='-1') $posids[] = $r;
			}
			$textcontent = array();
			foreach($this->fields AS $_key=>$_value) {
				if($_value['isposition']) {
					$textcontent[$_key] = $this->data[$_key];
				}
			}
			//颜色选择为隐藏域 在这里进行取值
			$textcontent['style'] = $_POST['style_color'] ? strip_tags($_POST['style_color']) : '';
			$textcontent['inputtime'] = strtotime($textcontent['inputtime']);
			if($_POST['style_font_weight']) $textcontent['style'] = $textcontent['style'].';'.strip_tags($_POST['style_font_weight']);
			
			/* 添加新数据 */
			$PositionApi = new \Admin\Api\PositionApi(); 	
			/* 添加新数据 */
			foreach ($posids as $posid) {
				$PositionApi->position_update($this->id, $this->modelid, $catid, $posid, $textcontent);
			}
			return TRUE;
		}
	}
