<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
/* 根据模型ID和字段名称获取字段信息 */
function get_model_field($modelid=0, $field = '') {
	$map = array();
	$map['modelid'] = $modelid;
	$map['field'] = $field;
	$result = D('ModelField')->where($map)->find();
	if ($result) {
		$result['setting'] = string2array($result['setting']);
	}
	return $result;
}

/* 转换字段配置 */
function get_formtype_box($string) {
	$result = array();
	if ($string) {
		$arrs = explode("\r\n", $string);
		if (is_array($arrs) && !empty($arrs)) {
			foreach ($arrs as $arr) {
				$v = explode("|", $arr);
				$result[$v[1]] = $v[0];
			}
		}
	}
	return $result;
}

function getpicfile($pic='', $ismake = 0) {
	$Image = new \Common\Library\image(1); 
	$result = array();
	$server_list = getcache('setting', 'document');

	$pic_file = str_replace('./uploadfile', SITE_PATH.'/uploadfile', $pic);
	$pic_dir = dirname($pic_file);
	$pic_filename = basename($pic_file);

	$imginfo = $Image->info($pic_file);
	if ($imginfo['width'] > $imginfo['height']) {
		$imgthumb = $imginfo['width'];
		$imgthumbtype = 'width';
	} else {
		$imgthumb = $imginfo['height'];
		$imgthumbtype = 'height';
	}
	$result['self'] = dir2url($pic_file);
	foreach ($server_list as $key => $v) {
		$_size = (int) $v['size'];
		if ($_size > 0) {
			if ($v['size'] > $imgthumb) continue;
			$thumbwidth = $thumbheight = '';
			if ($imgthumbtype == 'width') {
				$thumbwidth = $v['size'];
			} else {
				$thumbheight = $v['size'];
			}
			$new_filename = $key.'_'.$pic_filename;
			$pic = $pic_dir.'/'.$new_filename;
			if ($ismake == 1) {
				$Image->thumb($pic_file, $pic_dir.'/'.$new_filename, $thumbwidth, $thumbheight);
			}
		} else {
			$pic = $pic_file;
		}
		$result[$key] = dir2url($pic);
	}
	return json_encode($result);
}

function dir2url($pic) {
	return ((strripos($pic, 'http') === FALSE) ? 'http://'.$_SERVER['HTTP_HOST'] : '').str_replace(SITE_PATH, __ROOT__, $pic);
}

function getpicinfo($pic) {
	$pic = str_replace('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/uploadfile', SITE_PATH.'/uploadfile', $pic);
	$result = array();
	$Image = new \Common\Library\image();
	return $Image->info($pic);
}

function dpi2cm($px, $dpi = 72) {
	$dpis = array(
		'300' 	=> '0.003333',
		'72'	=> '0.013889',
	);
	if(!isset($dpis[$dpi])) return '0';
	return sprintf("%.2f", ($px * $dpis[$dpi] * 2.54));
}
