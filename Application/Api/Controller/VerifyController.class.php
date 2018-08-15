<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Api\Controller;
use Think\Controller;
/* 验证码相关 */
class VerifyController extends Controller
{
	/* 创建验证码 */
	public function create() {
		$param = I('param.');
		$checkcode = new \Common\Library\checkcode();
		ob_end_clean();
		if (isset($param['code_len']) && intval($param['code_len'])) {
			$checkcode->code_len = intval($param['code_len']);
		}
		if ($checkcode->code_len > 8 || $checkcode->code_len < 2) {
			$checkcode->code_len = 4;
		}
		if (isset($param['font_size']) && intval($param['font_size'])) $checkcode->font_size = intval($param['font_size']);
		if (isset($param['width']) && intval($param['width'])) $checkcode->width = intval($param['width']);
		if ($checkcode->width <= 0) {
			$checkcode->width = 100;
		}
		if (isset($param['height']) && intval($param['height'])) $checkcode->height = intval($param['height']);
		if ($checkcode->height <= 0) {
			$checkcode->height = 50;
		}
		$max_width = $checkcode->code_len * 28;
		$max_height = $checkcode->font_size * 2;
		if($checkcode->width > $max_width) $checkcode->width = $max_width;
		if($checkcode->height > $max_height) $checkcode->height = $max_height;
		if (isset($param['font_color']) && trim(urldecode($param['font_color'])) && preg_match('/(^#[a-z0-9]{6}$)/im', trim(urldecode($param['font_color'])))) $checkcode->font_color = trim(urldecode($param['font_color']));
		if (isset($param['background']) && trim(urldecode($param['background'])) && preg_match('/(^#[a-z0-9]{6}$)/im', trim(urldecode($param['background'])))) $checkcode->background = trim(urldecode($param['background']));
		$checkcode->doimage();
		session('verify', $checkcode->get_code());
		setcache('app_verify', $checkcode->get_code());
	}	
}