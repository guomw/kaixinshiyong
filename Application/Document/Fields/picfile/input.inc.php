	function picfile($field, $value) {
		//取得镜像站点列表
		$config = getcache('setting', 'document');
		$_value = $_POST['info']['_'.$field];
		$_result = getpicfile($value, 1);
		$_result = json_decode($_result, TRUE);
		$result = array();
		if ($_result) {
			foreach ($_result as $_k => $_r) {
				$dpi = ($_k == 'self') ? '300' : $config[$_k]['dpi'];
				$result[$_k] = getpicinfo($_r);
				$result[$_k]['name'] = $config[$_k]['name'];
				$result[$_k]['url'] = $_r;
				$result[$_k]['dpi'] = $dpi;
				$result[$_k]['dpi_w'] = dpi2cm($result[$_k]['width'], $dpi);
				$result[$_k]['dpi_h'] = dpi2cm($result[$_k]['height'], $dpi);
				$result[$_k]['point'] = (int) $_value[$_k]['point'];
			}
		}
		return serialize($result);
	}