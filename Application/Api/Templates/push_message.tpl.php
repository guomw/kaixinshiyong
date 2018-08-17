<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<form action="<?php echo U('push_message');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form">
        <tr>
                <th>推送对象</th>
                <td class="y-bg">
                    <label><input type="radio" id="id_recipient_type_0" class="input-text" name="push[recipient_type]" value="1" checked="checked"/> 广播(所有人)&nbsp;</label>
                    <label><input type="radio" id="id_recipient_type_1" class="input-text" name="push[recipient_type]" value="2"/>设备标签(Tag)&nbsp;</label>&nbsp;
                     <label><input type="radio" id="id_recipient_type_2" class="input-text" name="push[recipient_type]" value="3"/>设备别名(Alias)&nbsp;</label>&nbsp;
                      <label><input type="radio" id="id_recipient_type_3" class="input-text" name="push[recipient_type]" value="4"/>Registration ID&nbsp;</label>&nbsp;<br>
                </td>
            </tr>
            
            <tr class="activity_mold_hint1" style="display:none;">
                <th></th>
                <td class="y-bg">
                    <input type="text" class="input-text " name="push[tag_search]" value="" index="1" placeholder="输入设备标签" />&nbsp;
                    
                </td>
            </tr>

             <tr  class="activity_mold_hint2" style="display:none;">
                <th></th>
                <td class="y-bg">
                   
                     <input type="text" class="input-text " name="push[device_alias]" value="" index="2" placeholder="输入设备别名" />&nbsp;
                    
                </td>
            </tr>

                <tr class="activity_mold_hint3" style="display:none;">
                <th></th>
                <td class="y-bg">
                   
                      <input type="text" class="input-text " name="push[device_registrationid]" value="" index="3" placeholder="输入Registration ID" />&nbsp;
                       
                </td>
            </tr>

           <!--   <tr class="js_check">
                <th>发送时间</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="push[time_check]" value="1" checked="checked" /> 立即&nbsp;</label>
                    <label><input type="radio" class="input-text" name="push[time_check]" value="2" />定时(Tag)&nbsp;</label>&nbsp;
                     
                </td>
            </tr>

              <tr class="activity_mold_hint4" style="display:none;">
                <th></th>
                <td class="y-bg">
				<?php echo $form::date('push[start_time]', $start_time)?>
				
                </td>
            </tr> -->

               <tr>
                <th>推送标题</th>
                <td class="y-bg">
                   
                     <input type="text" class="input-text " name="push[title]" value="" index="2" placeholder="输入推送标题" />&nbsp;
                    
                </td>
            </tr>
         
           
           <tr>
                <th><?php echo L('推送内容')?></th>
                <td class="y-bg">
                    <textarea name="push[content]" cols="40" rows="5"></textarea>
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
		// 内部根据当前运行是创建，可能是input美元素，也可能是flash.
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


    $('input[type="radio"][name="push[recipient_type]"]').click(function(){
    	
    	var s = $(":radio:checked").val();
    	if(s == 2) {
    		$(".activity_mold_hint1").css('display','');
    		$(".activity_mold_hint3").css('display','none');
    		$(".activity_mold_hint2").css('display','none');

    	}else if(s == 3){
    		$(".activity_mold_hint2").css('display','');
    		$(".activity_mold_hint1").css('display','none');
    		$(".activity_mold_hint3").css('display','none');
    	}else if(s == 4){
    		$(".activity_mold_hint3").css('display','');
    		$(".activity_mold_hint2").css('display','none');
    		$(".activity_mold_hint1").css('display','none');

    	}else if(s == 1){
    		$(".activity_mold_hint3").css('display','none');
    		$(".activity_mold_hint2").css('display','none');
    		$(".activity_mold_hint1").css('display','none');
    	};

		});



    $('input[type="radio"][name="push[time_check]"]').click(function(){
    	
    	var s =  $('input[type="radio"][name="push[time_check]"]:checked').val();
    	alert(s);
    	if(s == 2) {
    		$(".activity_mold_hint4").css('display','');
    	}else if(s == 1){
    		$(".activity_mold_hint4").css('display','none');
    		
    	};

		});
})
</script>
</body>
</html>