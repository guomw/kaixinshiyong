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
<form action="<?php echo U('update');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_base"><?php echo L('联系设置')?></li>
        </ul>
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form"   id="goods_albums">
        <tr>
                <th>新浪微博</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[xinlang]" value="<?php echo $setting['xinlang'] ?>"/>
                    &nbsp;&nbsp;提示：填写新浪微博的链接地址
                </td>
            </tr>
            <tr>
                <th>腾讯微博</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[tengxun]" value="<?php echo $setting['tengxun'] ?>"/>
                    &nbsp;&nbsp;提示：填写腾讯微博的链接地址
                </td>
            <tr>
                <th>微信公众号</th>
                <td class="y-bg">
					<div class="sub_btn" name="uploadify" id="img1">
                            <img src="<?php echo $setting['weixin_logo'];?>" width="100px" height="100px" alt="" onerror="javascript:this.src='<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg'"/>
                            <input type="hidden" name="setting[weixin_logo]" value="<?php echo $setting['weixin_logo'];?>" />
                   </div>
                    &nbsp;&nbsp;提示：请上传微信公众号二维码
                </td>
            </tr>
            
            <tr>
                <th>买家QQ群</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[buyer_qq_group]" value="<?php echo $setting['buyer_qq_group'] ?>"/>
                    &nbsp;&nbsp;
                </td>
            </tr>
            
            <tr>
                <th>买家QQ群代码</th>
                <td class="y-bg">
                    <textarea name="setting[buyer_code]"><?php echo stripslashes($setting['buyer_code']);?></textarea>&nbsp;&nbsp;帮助链接：http://qun.qq.com/join.html
                </td>
            </tr>
            
             <tr>
                <th>商家QQ群</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[merchant_qq_group]" value="<?php echo $setting['merchant_qq_group'] ?>"/>
                    &nbsp;&nbsp;
                </td>
            </tr>
            
            <tr>
                <th>商家QQ群代码</th>
                <td class="y-bg">
                    <textarea name="setting[merchant_code]"><?php echo stripslashes($setting['merchant_code']);?></textarea>
                </td>
            </tr>
            
            <tr>
                <th>买家客服QQ</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[site_contact_qq1]" value="<?php echo $setting['site_contact_qq1'] ?>"/>
                    &nbsp;&nbsp;提示：多个QQ使用','分隔
                </td>
            </tr>

             <tr>
                <th>商家咨询QQ</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[site_complain_qq]" value="<?php echo $setting['site_complain_qq'] ?>"/>
                </td>
             </tr>

            <tr>
               <th>旺旺在线咨询</th>
               <td class="y-bg">
                   <input type="text" class="input-text" name="setting[site_complain_wangwang]" value="<?php echo $setting['site_complain_wangwang'] ?>"/>
               </td>
            </tr>


            <tr>
                <th>举报投诉QQ</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[site_consult_qq]" value="<?php echo $setting['site_consult_qq'] ?>"/>
                </td>
            </tr>

             <tr>
                <th>合作洽谈QQ</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[site_collaborate_qq]" value="<?php echo $setting['site_collaborate_qq'] ?>"/>
                </td>
            </tr>

            <tr>
                <th>售后咨询QQ</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[site_service_qq]" value="<?php echo $setting['site_service_qq'] ?>"/>
                </td>
            </tr>
            <tr>
                <th>联系电话</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[site_contact_tel]" value="<?php echo $setting['site_contact_tel'] ?>" maxlength='13'/>
                </td>
            </tr> 
            <tr>
                <th><?php echo L('联系邮箱')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[site_contact_email]" value="<?php echo $setting['site_contact_email'];?>"/>
                </td>
            </tr> 
             <tr>
                <th>邮编</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[site_contact_zipcode]" value="<?php echo $setting['site_contact_zipcode'] ?>" maxlength='6'/>
                </td>
            </tr> 
            <tr>
                <th><?php echo L('所在省市')?></th>
                <td class="y-bg">
                    <select name="setting[site_contact_city]" id="city" onclick="loadarea()" >
                            <?php foreach ($region as $k => $v): ?>
                               <option <?php if ($setting['site_contact_city'] == $v['linkageid']): ?>
                             selected <?php endif ?>value="<?php echo $v['linkageid'] ?>"><?php echo $v['name'] ?></option>
                            <?php endforeach ?>

                      </select>
                    <select name="setting[site_contact_area]" id="area">
                        <?php if ($setting['site_contact_area']): ?>
                            <?php foreach ($area as $k => $v): ?>
                            <option <?php if ($setting['site_contact_area'] == $v['linkageid']): ?>selected <?php endif ?>value="<?php echo $v['linkageid'] ?>">
                                <?php echo $v['name'] ?>
                            </option>
                        <?php endforeach ?>
                        <?php else: ?>  
                           <option>请选择</option>
                        <?php endif ?>
                   </select>
                </td>
            </tr>
            <tr>
                <th>工作日</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[site_work_day]" value="<?php echo $setting['site_work_day'] ?>" />
                </td>
            </tr> 
            <tr>
                <th><?php echo L('详细地址')?></th>
                <td class="y-bg"><input type="text" class="input-text" name="setting[site_contact_address]"  value="<?php echo $setting['site_contact_address'];?>"/>
                </td>
            </tr>
            <tr>
                <th>地图标识：</th>
                <td class="y-bg">
                   <div class="sub_btn" name="uploadify" id="img2">
                            <img src="<?php echo $setting['site_map_image'];?>" width="300px" height="100px" alt="" onerror="javascript:this.src='<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg'"/>
                            <input type="hidden" name="setting[site_map_image]" value="<?php echo $setting['site_map_image'];?>" />
                   </div>&nbsp;请上传地图，建议尺寸1000*405像素，用于活动页联系我们页面显示
                </td>
            </tr> 
        </table>
        </div>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
</div>
</div>
</form>
</body>
<script type="text/javascript">
function loadarea(){
    var Id = $("#city").val();
    $('#area option').remove();
    $.post("<?php echo U('Setting/get_area') ?>",{id:Id},function(data){
        var region = eval(data);
        if(region!=''){
            $.each(region,function(no,items){
                $('#area').append('<option value="'+items.linkageid+'">'+items.name+'</option>');
            });
        }
    });
}
$(".tab_p").hide();
$(".tab_tp").hover(function(){
    $(".tab_p").toggle();
});
</script>
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
//-->
</script>
</html>