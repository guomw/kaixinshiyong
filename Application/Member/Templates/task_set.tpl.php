<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<form action="<?php echo U('update');?>" method="post" id="myform">
<input type='hidden' name='activity_type' value="task"/>
<div class="pad-10">
    <div class="col-tab">
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form">
        <tr>
                <th><?php echo L('活动状态')?></th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[task_isopen]" value="1" <?php if($setting['task_isopen'] == 1){?>checked<?php }?>/>开启&nbsp;</label>
                    <label><input type="radio" class="input-text" name="setting[task_isopen]" value="0" <?php if($setting['task_isopen'] == 0){?>checked<?php }?>/>关闭&nbsp;</label>&nbsp;<font color="red">关闭之后在前台将无法使用此功能</font>
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('每份最低佣金')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[task_price]" value="<?php echo $setting['task_price'];?>"/>&nbsp;例如：￥1.00元
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('最低发布份数')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[task_num]" value="<?php if(!$setting['task_num']){echo '1';}else{echo $setting['task_num'];}?>"/>&nbsp;
                </td>
            </tr>
           
           <tr>
                <th><?php echo L('活动介绍')?></th>
                <td class="y-bg">
                    <textarea name="setting[task_content]" cols="40" rows="5"><?php echo $setting['task_content'];?></textarea>
                </td>
            </tr>
            
			<tr>
                <th><?php echo L('活动图片')?></th>
                <td class="y-bg">
                    <span class="border_dddddd" id="imgupload">
						<img onerror="javascript:this.src='<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg';" src="<?php echo $setting['task_img'];?>"  alt="" />
						<input type="hidden" name="setting[task_img]" value="<?php echo $setting['task_img'];?>" />
					</span>
                </td>
            </tr>
        </table>
        </div>
        <div class="bk15"></div>
        <input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
    </div>
</div>
</form>
<link href="<?php echo JS_PATH;?>webuploader/webuploader.css" rel="stylesheet" />
<script src="<?php echo JS_PATH;?>webuploader/webuploader.js" type="text/javascript"></script>
<script type="text/javascript">
//图片上传功能
$(document).ready(function() {
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
			"filetype_post":"jpg|jpeg|gif|png",
			"swf_auth_key":"57a39f6f7415ec2cdd2b8afd77b57c3f",
			"isadmin":"1",
			"groupid":"2"
		},
		// 内部根据当前运行是创建，可能是input元素，也可能是flash.
		pick: {
			id: '#imgupload',
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
		console.log(arr);
		if(arr[0] > 0) {
			$(pickid).children().find('img').attr('src', arr[1]);
			$(pickid).children().find('input[type=hidden]').eq(0).attr('value', arr[1]);
		}
	}

	uploader.onUploadError = function(file, reason) {
		alert('文件上传错误：' + reason);
	}
})
</script>
							
<script type='text/javascript'>
$(function(){
    $.formValidator.initConfig({
        formid:"myform",
        autotip:true,
        onerror:function(msg,obj){
            $(obj).focus();
        }
    });
    $("#charge_money").formValidator({
        empty:false,
        onempty:'收费价格不能为空',
        onshow:'请输入收费价格(纯数字)' ,
        onfocus:"请输入收费价格(纯数字)" 
    }).regexValidator({
        regexp:'num',
        datatype:'enum',
        onerror:'收费价格只能为正数'
    });
})
</script>
</body>
</html>