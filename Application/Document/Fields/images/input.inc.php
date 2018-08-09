
	function images($field, $value) {
		//取得图片列表
		$pictures_url = $_POST[$field.'_url'];
		//取得图片说明
		$pictures_alt = isset($_POST[$field.'_alt']) ? $_POST[$field.'_alt'] : array();
		$array = $temp = array();
		if(!empty($pictures_url)) {
			foreach($pictures_url as $key=>$pic) {
				if(!$pic) continue;
				$temp['url'] = $pic;
				$temp['alt'] = dhtmlspecialchars($pictures_alt[$key]);
				$array[] = $temp;
			}
		}
		//$array = array2string($array);
		return serialize($array);
	}
