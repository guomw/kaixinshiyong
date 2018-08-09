<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
$(function(){
	SwapTab('setting','on','',4, 'base');

    $('#button').click(function(){
          $.get('<?php echo U('rand') ?>',function(ret){
            $('#sso_key').val(ret.url);
         });
    });
    

 
});
//--> 
</script>
<form action="<?php echo U('update');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
     <li id="tab_setting_base" <?php if ($this->groupid == 'base'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',4, 'base');">应用管理</li>       </ul>
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form" id="goods_albums">
        <p> 1.接入云划算整合支付之前，请确保已经购买云划算整合支付系统</p>
        <p> 2.整合支付系统一旦正式接入则无法关闭，否则会造成数据无法同步</p>
        <p> 3.配置成功之后，切勿修改应用通信信息，否则会造成无法通信。</p>
         <br/>
            <tr>
                <th>当前应用名</th>
                <td class="y-bg"><input type="text" class="input-text" id='sso_name' name="setting[sso_name]"  readonly="readonly"  value="<?php echo C('webname');?>"/></td>
            </tr>
            <tr>
                <th>应用通信地址</th>
                <td class="y-bg"><input type="text" class="input-text"  id='sso_address' name="setting[sso_address]"   value="<?php echo C('sso_address');?>"/>（整合支付平台接口地址）</td>

            </tr> 

            <tr>
                <th>云平台appid</th>
                <td class="y-bg"><input type="text" class="input-text"  id='appid' name="setting[appid]"   value="<?php echo C('appid');?>"/>（整合支付平台appid 从整合支付平台获取）</td>
                
            </tr> 
            <tr>
                <th>通信秘钥</th>
                <td class="y-bg"><input type="text" class="input-text" id='sso_key' name="setting[sso_key]" id="sso_key"  value="<?php if(C('sso_key') == ''){
                    echo $rand;
                }else{
                    echo C('sso_key');
                };?>" size="50"/>
                <input type="button" class="button" id="button" value="自动生成">
                </td>
            </tr> 
            <tr>
                <th>系统</th>
                <td class="y-bg">
                    <select name="setting[sso_system]">
                        <option value="1"><?php echo C('WEBNAME'); ?>整合支付系统</option>
                    </select>
                    

                </td>
            </tr>

            <tr>
                <th>是否启用</th>
                <td class="y-bg">
                    <input type="radio" name="setting[sso_is_open]" value="1" <?php if(C('sso_is_open') =='1'){echo 'checked';} ?>/>是
                    <input type="radio" id='open0' name="setting[sso_is_open]" value="0" <?php if(C('sso_is_open') =='0'){echo 'checked';} ?>/>否

                    <span id='tongxin'></span>            
           </td>
            </tr>

            <script type="text/javascript">
            $("input[name='setting[sso_is_open]']").click(function() {

            	if($(this).val() == 1) {

            		if($('#sso_address').val() == ''){
            			$(this).removeAttr("checked");
                        $('#open0').attr('checked','checked');
            			alert('请填写应用通信地址'); 

            			return false;
            		} 

            		if($('#appid').val() == ''){
            			$(this).removeAttr("checked");
                        $('#open0').attr('checked','checked');
            			alert('请填写从云平台获取的应用id'); 
            			return false;
            		} 

            		if($('#sso_key').val() == ''){
            			$(this).removeAttr("checked");
            			$('#open0').attr('checked','checked');
            			alert('请填写从云平台获取的key'); 
            			return false;
            		} 



            		var data={
            			'sso_address' : $('#sso_address').val(),
            			'appid' : $('#appid').val(),
            			'sso_key' : $('#sso_key').val()
            		}

                     
                    $.getJSON('/index.php?m=admin&c=sso&a=sso_chek',data,function(s){

                    	if(s['status'] == 1){

                    	   $('#tongxin').html('<font color="green">整合支付平台通信成功</font>');
                    		$('#sso_address,#sso_key,#appid').attr("readOnly",true);

                    	}else{
                    		$(this).removeAttr("checked");
                    		$('#open0').attr('checked','checked');
                    		$('#sso_address,#sso_key,#appid').attr("readOnly",false);
                           	alert(s.info);
                           	return false;
                    	}


                    })
            		
            	}else{

            		$('#sso_address,#sso_key,#appid').attr("readOnly",false);
            	}
            })
            </script>
        </table>
        </div>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button" onclick='dosub()'/>
</div>
</div>
</form>
</body>



<script type="text/javascript">

<?php

if(c('sso_is_open') == 1){

	echo "$('#sso_address,#sso_key,#appid').attr('readOnly',true);";
}

?>



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


/* 当站点关闭时未填写关闭原因则禁止提交 */
function dosub(){


       $("#myform").attr('onsubmit','return true');
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
</html>