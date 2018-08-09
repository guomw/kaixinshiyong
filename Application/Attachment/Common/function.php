<?php 
/**
 * flash上传初始化
 * 初始化swfupload上传中需要的参数
 * @param $module 模块名称
 * @param $catid 栏目id
 * @param $args 传递参数
 * @param $userid 用户id
 * @param $groupid 用户组id
 * @param $isadmin 是否为管理员模式
 */
function initupload($module, $catid,$args, $userid, $groupid = '-1', $isadmin = '0'){
	$grouplist = getcache('member_group', 'member');
	// if($isadmin==0 && !$grouplist[$groupid]['allowattachment']) return $grouplist[$groupid]['allowattachment'];
	extract(getswfinit($args));
	$file_size_limit = C('upload_maxsize');
	$sess_id = NOW_TIME;
	$upload_path = __APP__;
	$upload_url = U('Attachment/Attachment/swfupload', array('dosubmit' => 1));
	$swf_auth_key = md5(C('AUTH_KEY').$sess_id);
	$init =  'var swfu = \'\';
	$(document).ready(function(){
	swfu = new SWFUpload({
		flash_url:"'.JS_PATH.'swfupload/swfupload.swf?"+Math.random(),
		upload_url:"'.$upload_url.'",
		file_post_name : "Filedata",
		post_params:{"SWFUPLOADSESSID":"'.$sess_id.'","module":"'.$module.'","catid":"'.$_GET['catid'].'","userid":"'.$userid.'","dosubmit":"1","thumb_width":"'.$thumb_width.'","thumb_height":"'.$thumb_height.'","watermark_enable":"'.$watermark_enable.'","filetype_post":"'.$file_types_post.'","swf_auth_key":"'.$swf_auth_key.'","isadmin":"'.$isadmin.'","groupid":"'.$groupid.'"},
		file_size_limit:"'.$file_size_limit.'",
		file_types:"'.$file_types.'",
		file_types_description:"All Files",
		file_upload_limit:"'.$file_upload_limit.'",
		custom_settings : {progressTarget : "fsUploadProgress",cancelButtonId : "btnCancel"},
 
		button_image_url: "",
		button_width: 75,
		button_height: 28,
		button_placeholder_id: "buttonPlaceHolder",
		button_text_style: "",
		button_text_top_padding: 3,
		button_text_left_padding: 12,
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor: SWFUpload.CURSOR.HAND,

		file_dialog_start_handler : fileDialogStart,
		file_queued_handler : fileQueued,
		file_queue_error_handler:fileQueueError,
		file_dialog_complete_handler:fileDialogComplete,
		upload_progress_handler:uploadProgress,
		upload_error_handler:uploadError,
		upload_success_handler:uploadSuccess,
		upload_complete_handler:uploadComplete
		});
	})';
	return $init;
}

/**
 * 读取swfupload配置类型
 * @param array $args flash上传配置信息
 */
function getswfinit($args) {
	$allowext = 'jpg|jpeg|gif|bmp|png|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|swf';
	$args = explode(',',$args);
	$arr['file_upload_limit'] = intval($args[0]) ? intval($args[0]) : '8';
	$args['1'] = ($args[1]!='') ? $args[1] : $allowext;
	$arr_allowext = explode('|', $args[1]);
	foreach($arr_allowext as $k=>$v) {
		$v = '*.'.$v;
		$array[$k] = $v;
	}
	$upload_allowext = implode(';', $array);
	$arr['file_types'] = $upload_allowext;
	$arr['file_types_post'] = $args[1];
	$arr['allowupload'] = intval($args[2]);
	$arr['thumb_width'] = intval($args[3]);
	$arr['thumb_height'] = intval($args[4]);
	$arr['watermark_enable'] = ($args[5]=='') ? 1 : intval($args[5]);
	return $arr;
}

/**
 * 判断是否为图片
 */
function is_image($file) {
	$ext_arr = array('jpg','gif','png','bmp','jpeg','tiff');
	$ext = fileext($file);
	return in_array($ext,$ext_arr) ? $ext_arr :false;
}
?>