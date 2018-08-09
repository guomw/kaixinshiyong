<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = true;
include $this->admin_tpl('header','Admin');
?>
<div class="pad_10">
<div class="common-form">
<form action="<?php echo U('add'); ?>" method="post" name="myform" id="myform">     
<fieldset>                                             
<table cellpadding="2" cellspacing="1" class="table_form" width="100%" id="goods_albums"> 
	<tr>
		<th width="15%" align="right">类型 :</th>
		<td>
			<select name="image[type]">
				<option value="1">首页幻灯</option>
				<option value="2">试用报告幻灯</option>
			</select>
		</td>
	</tr>
	
	<tr>
		<th width="15%" align="right">幻灯标题 :</th>
		<td><input type="text" name="image[title]" id="title" size="20"></td>
	</tr>
	<tr>
		<th>幻灯大图：</th>
		<td>
			<div class="sub_btn" name="uploadify" id="img1">
				<img alt="" width="100" height="50" src="<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg"/>
				<input type="hidden" name="image[image]" value="<?php echo $image;?>" />
			</div>&nbsp;提示：建议上传图片大小710*300
		</td>
	</tr>
	<tr>
		<th width="15%" align="right">v2版幻灯背景 :</th>
		<td><input type="color" id="color" name="image[color]"  value="" size="20"></td>

	</tr>
	
	<tr>
		<th>开始时间：</th>
		<td>
			<?php echo $form::date('image[starttime]',date('Y-m-d H:i:s', NOW_TIME),1)?>
		</td>
	</tr>
	<tr>
		<th>结束时间：</th>
		<td>
			<?php echo $form::date('image[endtime]', '',1)?> &nbsp;
		</td>
	</tr>
		<tr>
		<th>链接地址：</th>
		<td>
			<input type="text" name="image[url]" id="url" size="20">&nbsp;
		</td>
	</tr>
	<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="<?php echo U('add');?>"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>
</table>
</fieldset>
</div>
</form>
</div>
<link href="<?php echo JS_PATH;?>webuploader/webuploader.css" rel="stylesheet" />
<script src="<?php echo JS_PATH;?>webuploader/webuploader.js" type="text/javascript"></script>
<script type="text/javascript">
//图片上传功能
$(document).ready(function() {
	var goods_album = $("#goods_albums").find('div');
	for(var i=0; i < goods_album.length; i++) {
		var uploader = WebUploader.create({
			auto:true,
			fileVal:'Filedata',
		    // swf文件路径
		    swf: '<?php echo JS_PATH;?>webuploader/webuploader.swf',
		    // 文件接收服务端。
		    server: "<?php echo U('Attachment/Attachment/swfupload');?>",
		    // 选择文件的按钮。可选
		    formData:{
		    	"module":"",
		    	"catid":"",
		    	"userid":"1",
		    	"dosubmit":"1",
		    	"thumb_width":"0",
		    	"thumb_height":"0",
		    	"watermark_enable":"1",
		    	"filetype_post":"jpg|jpeg|gif|bmp|png|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|swf",
		    	"swf_auth_key":"57a39f6f7415ec2cdd2b8afd77b57c3f",
		    	"isadmin":"1",
		    	"groupid":"2"
		    },
		    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
		    pick: {
		    	id: '#img' + (i+1),
		    	multiple:false
		    },
		    accept:{
				title: '图片文件',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
		    },
		    thumb:{
		    	width: '110',
		    	height: '110'
		    },
		    chunked: false,
		    chunkSize:1000000,
		    // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
		    resize: false
		});

		uploader.onUploadSuccess = function( file, response ) {
			var pickid = this.options.pick.id;
			var data = response._raw;
			var arr = data.split(',');
			if(arr[0] > 0) {
				$(pickid).children().find('img').attr('src', arr[1]);
				$(pickid).children().find('input[type=hidden]').eq(0).attr('value', arr[1]);
			}
		}

		uploader.onUploadError = function(file, reason) {
			alert('文件上传错误：' + reason);
		}
	}
})
</script>

<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript"> 
<!--
$.formValidator.initConfig({
	formid:"myform",
	autotip:true,
	onerror:function(msg,obj){
		$(obj).focus();
	}
});
$("#url").formValidator({
	empty:false,
	onempty:'链接地址不能为空',
	onshow:"请输入链接地址",
	onfocus:"请输入链接地址"
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:"链接地址输入错误"
});
$("#title").formValidator({
	empty:false,
	onempty:'图片标题不能为空',
	onshow:"请输入图片标题",
	onfocus:"请输入图片标题"
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:"图片标题输入错误"
});
//-->
</script>
</body>
</html> 