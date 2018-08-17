<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});

	$("#nickname").formValidator({onshow:"<?php echo L('input').L('nickname')?>",onfocus:"<?php echo L('nickname').L('between_2_to_20')?>"}).inputValidator({min:2,max:20,onerror:"<?php echo L('nickname').L('between_2_to_20')?>"}).regexValidator({regexp:"ps_username",datatype:"enum",onerror:"<?php echo L('nickname').L('format_incorrect')?>"}).ajaxValidator({
	    type : "get",
		url : "",
		data :"m=member&c=unreal&a=public_checknickname_ajax",
		datatype : "html",
		async:'false',
		success : function(data){
            if( data == "1" ) {
                return true;
			} else {
                return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "<?php echo L('deny_register').L('or').L('user_already_exist')?>",
		onwait : "<?php echo L('connecting_please_wait')?>"
	});
	
	
});
//-->
</script>
<script type="text/javascript">
/** url跳转 */
function redirect(url) {
	if(url.indexOf('://') == -1 && url.substr(0, 1) != '/' && url.substr(0, 1) != '?') url = $('base').attr('href')+url;
	location.href = url;
}
</script>
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
		    	"filetype_post":"jpg|jpeg|gif|bmp|png",
		    	"swf_auth_key":"57a39f6f7415ec2cdd2b8afd77b57c3f",
		    	"isadmin":"1",
		    	"groupid":"2"
		    },
		    // 内部根据当前运行是创建，可能是input美元素，也可能是flash.
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
		    	width: '90',
		    	height: '90'
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
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('add');?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form" id="goods_albums">
		<tr>
			<td>昵称</td>
			<td>
			<input type="text" name="info[nickname]" value="" class="input-text" id="nickname" size="10"></input>
			</td>
		</tr>
		<tr>
			<td>头像</td> 
			<td> 
				<div id="img1">
					<img src="/uploadfile/avatar/avatar.jpg" alt="" height=90 width=90 onerror="javascript:this.src='/uploadfile/avatar/avatar.jpg"/>
					<input type="hidden" name="info[avatar]" value="/uploadfile/avatar/avatar.jpg" />
				</div>
			</td>
		</tr>
		
		
	</table>
</fieldset>

    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
</html>