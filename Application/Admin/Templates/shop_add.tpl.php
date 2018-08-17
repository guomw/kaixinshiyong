<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('add');?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form" id="goods_albums">
		<tr>
			<td width="80">来源名称</td> 
			<td><input type="text" name="name"  class="input-text"/>&nbsp;例如 淘宝、天猫、京东等</td>
		</tr>
		<tr>
			<td>小LOGO</td> 
			<td>
				<div id="img1">
					<img src="<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg" width="100px" height="100px" alt="" onerror="javascript:this.src='<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg'"/>
                	<input type="hidden" name="small_logo" value="<?php echo $setting['small_logo'];?>" />
                </div>
			</td>
		</tr>
		<tr>
			<td>大LOGO</td>
			<td>
				<div id="img2">
					<img src="<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg" width="100px" height="100px" alt="" onerror="javascript:this.src='<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg'"/>
            		<input type="hidden" name="big_logo" value="<?php echo $setting['big_logo'];?>" />
            	</div>
			</td>
		</tr>
        <tr>
            <td>官方网址：</td>
            <td>
                <input type="text" name="url" value="" style="width:300px;"/>
            </td>
        </tr>
		<tr>
			<td>简要描述</td>
			<td><textarea cols="49" rows="4" name="description"></textarea></td>
		</tr>
	</table>
</fieldset>

    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
<link  rel="stylesheet"  href="<?php echo JS_PATH;?>webuploader/webuploader.css"/> 
<script type="text/javascript" src="<?php echo JS_PATH;?>webuploader/webuploader.js"></script>
<script type="text/javascript">
<!--
//图片上传功能
$(document).ready(function(){
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
//-->
</script>
</html>