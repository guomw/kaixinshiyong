<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
$(function(){
	SwapTab('setting','on','',4, '<?php echo $this->groupid; ?>');
});
//--> 
</script>
<form action="<?php echo U('update');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_base" <?php if ($this->groupid == 'base'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',4, 'base');"><?php echo L('setting_basic_cfg')?></li>
        </ul>
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form" id="goods_albums">
            <tr>
                <th>网站主LOGO</th>
                <td class="y-bg">
                	<div class="sub_btn" name="uploadify" id="img1">
						<img src="<?php if (file_exists('.'.$setting['site_logo_zhu'])){echo $setting['site_logo_zhu'];}else{echo THEME_STYLE_PATH.'style/images/signIn_14.jpg';};?>"/>
						<input type="hidden" name="setting[site_logo_zhu]" value="<?php echo $setting['site_logo_zhu'];?>" />
					</div>&nbsp;请上传网站主logo，建议尺寸165*55像素，用于网站主要页面显示
                </td>
            </tr>
            
            <tr>
                <th>网站副LOGO</th>
                <td class="y-bg">
                	<div class="sub_btn" name="uploadify" id="img2">
						<img src="<?php if (file_exists('.'.$setting['site_logo_fu'])){echo $setting['site_logo_fu'];}else{echo THEME_STYLE_PATH.'style/images/signIn_14.jpg';};?>"/>
						<input type="hidden" name="setting[site_logo_fu]" value="<?php echo $setting['site_logo_fu'];?>" />
						</div>&nbsp;请上传网站副logo，建议尺寸165*55像素，用于网站会员中心等页面显示
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('webname')?></th>
                <td class="y-bg"><input type="text" class="input-text" name="setting[webname]" id="webname"  value="<?php echo $setting['webname'];?>"/></td>
            </tr>
            <tr>
                <th><?php echo L('网站标题')?></th>
                <td class="y-bg"><input type="text" class="input-text" name="setting[site_web_title]" id="site_web_title"  value="<?php echo $setting['site_web_title'];?>"/></td>
            </tr> 
            <tr>
                <th><?php echo L('网站关键词')?></th>
                <td class="y-bg"><input type="text" class="input-text" name="setting[keyword]" id="keyword"  value="<?php echo $setting['keyword'];?>"/></td>
            </tr> 
            <tr>
                <th><?php echo L('网站描述')?></th>
                <td class="y-bg"><textarea name="setting[description]" id="description" cols="47" rows="6"><?php echo $setting['description'];?></textarea></td>
            </tr>
            <tr>
                <th><?php echo L('网站版权')?></th>
                <td class="y-bg"><input type="text" class="input-text" name="setting[site_web_copyright]" id="site_web_copyright"  value="<?php echo $setting['site_web_copyright'];?>"/>
                </td>
            </tr>
            <!--
            <tr>
                <th>开启伪静态</th>
                <td class="y-bg">
                    <label><input type="radio" name="setting[rewrite_enabled]" value="1" <?php if ($setting['rewrite_enabled'] == 1): ?>checked<?php endif ?>>开启</label>
                    <label><input type="radio" name="setting[rewrite_enabled]" value="0" <?php if ($setting['rewrite_enabled'] == 0): ?>checked<?php endif ?>>关闭</label>
                </td>
            </tr>-->

            <tr>
                <th><?php echo L('统计代码')?></th>
                <td class="y-bg"><textarea name="setting[site_statistical_code]" id="site_statistical_code" cols="47" rows="5"><?php echo stripslashes ($setting['site_statistical_code']);?></textarea></td>
            </tr>
            <tr>
                <th><?php echo L('ICP备案号')?></th>
                <td class="y-bg"><input type="text" class="input-text" name="setting[site_web_icp]" id="site_web_icp"  value="<?php echo $setting['site_web_icp'];?>"/>
                </td>
            </tr>
            <!-- 
            <tr>
                <th><?php echo L('setting_default_theme');?></th>
             
                <td class="y-bg"><?php echo $form::select($style_list, $setting['default_style'], 'name="setting[default_style]" id="default_theme"', L('please_select'))?> &nbsp; <?php echo L('setting_default_theme_tips');?></td>
            </tr> -->

            <tr>
                <th><?php echo L('站点提示状态');?></th>
                <td class="y-bg">
                    <input type="radio" name="setting[site_web_notice]" value="1" <?php if($setting['site_web_notice']=='1'){echo 'checked';} ?>/>打开
                    <input type="radio" name="setting[site_web_notice]" value="0" <?php if($setting['site_web_notice']=='0'){echo 'checked';} ?>/>关闭
                </td>
            </tr>

            <tr>
                <th><?php echo L('站点状态');?></th>
                <td class="y-bg" id='radio'>
                    <input type="radio" name="setting[site_web_close]" value="1" <?php if($setting['site_web_close']=='1'){echo 'checked';} ?>/>打开
                    <input type="radio" name="setting[site_web_close]" value="0" <?php if($setting['site_web_close']=='0'){echo 'checked';} ?>/>关闭
                </td>
            </tr>

            <tr id="cause" <?php if($setting['site_web_close']=='1'){echo 'style="display:none;"';} ?>>
                <th><?php echo L('关闭原因');?></th>
                <td class="y-bg">
                    <textarea name="setting[site_web_close_cause]" cols="47" rows="3" id="cont"><?php echo $setting['site_web_close_cause'];?></textarea>
                </td>
            </tr>
        </table>
        </div>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button" onclick='dosub()'/>
</div>
</div>
</form>
</body>
<script type="text/javascript">
function SwapTab(name,cls_show,cls_hide,cnt,cur) {
    $('div.contentList').hide();
    $('ul.tabBut > li').attr('class', cls_hide);
    $('#div_'+name+'_'+cur).show();
    $('#tab_'+name+'_'+cur).attr('class',cls_show);
}
function showsmtp(obj,hiddenid){
	hiddenid = hiddenid ? hiddenid : 'smtpcfg';
	var status = $(obj).val();
	if(status == 1) $("#"+hiddenid).show();
	else  $("#"+hiddenid).hide();
}
/* test_mail */
function test_mail() {
	var mail_type = $('input[checkbox=mail_type]:checked').val();
	var mail_auth = $('input[checkbox=mail_auth]:checked').val();
   	$.post('<?php echo U("Admin/Setting/public_test_mail");?>',{mail_to: $('#mail_to').val(),mail_type:mail_type,mail_server:$('#mail_server').val(),mail_port:$('#mail_port').val(),mail_user:$('#mail_user').val(),mail_password:$('#mail_password').val(),mail_auth:mail_auth,mail_from:$('#mail_from').val()}, function(data){
		alert(data);
	});
}
/* 站点关闭原因 */
$(document).ready(function() {
    $("#radio input[type='radio']").click( function () {
        if($(this).val()== 0){
            $("#cause").attr('style','');
        } else {
            $("#cause").attr('style','display:none');
        }   
    });
});
/* 当站点关闭时未填写关闭原因则禁止提交 */
function dosub(){
    if ($("#radio input[type='radio']:checked").val()=='0' && $("#cont").val().length=='0'){
        alert('请填写站点关闭的原因');
        $("#myform").attr('onsubmit','return false');
    }else{
       $("#myform").attr('onsubmit','return true');
    }
}
</script>
<link rel="stylesheet"  href="<?php echo JS_PATH;?>webuploader/webuploader.css" /> 
<script  type="text/javascript" src="<?php echo JS_PATH;?>webuploader/webuploader.js"></script>
<script type="text/javascript">
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
</script>
</html>