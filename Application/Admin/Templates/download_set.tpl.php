<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
$(function(){
	SwapTab('setting','on','',4, '<?php echo $this->groupid; ?>');
})
//-->
</script>
<form action="<?php echo U('download_set');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_base"><?php echo L('app下载页面设置')?></li>
        </ul>
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form"   id="goods_albums">
        <tr>
                <th> Android下载地址：</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[download_android]" value="<?php echo C('download_android') ?>"/>
                    &nbsp;&nbsp;提示：填写Android下载地址：
                </td>
            </tr>
            <tr>
                <th>iPhone下载地址</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[download_iphone]" value="<?php echo C('download_iphone') ?>"/>
                    &nbsp;&nbsp;提示：填写iPhone下载地址
                </td>
            <tr>
                <th>二维码：</th>
                <td class="y-bg">
					<div class="sub_btn" name="uploadify" id="img1">
                            <img src="<?php echo C('download_qrcode');?>" width="100px" height="100px" alt="" onerror="javascript:this.src='<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg'"/>
                            <input type="hidden" name="setting[download_qrcode]" value="<?php echo  C('download_qrcode');?>" />
                   </div>
                    &nbsp;&nbsp;提示：请上传微信公众号二维码
                </td>
            </tr>

              <tr>
                <th>页面简介：</th>
                <td class="y-bg">
                   <textarea name="setting[download_desc]" style="margin: 0px; height: 167px; width: 473px;"><?php echo stripslashes(C('download_desc')); ?></textarea>
                </td>
            <tr>
            
         
          
           
        </table>
        </div>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
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

</script>

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