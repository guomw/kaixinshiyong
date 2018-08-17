<?php defined('IN_ADMIN') or exit('No permission resources.');
$show_header = true;
?>
<?php include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript" src="<?php echo JS_PATH?>member_common.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>calendar/calendar.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>calendar/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>calendar/jscal2.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>calendar/border-radius.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>calendar/win2k.css">
<script type="text/javascript">
  $(document).ready(function() {
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#password").formValidator({empty:true,onshow:"<?php echo L('not_change_the_password_please_leave_a_blank')?>",onfocus:"<?php echo L('password').L('between_6_to_20')?>"}).inputValidator({min:6,max:20,onerror:"<?php echo L('password').L('between_6_to_20')?>"});
	$("#pwdconfirm").formValidator({empty:true,onshow:"<?php echo L('not_change_the_password_please_leave_a_blank')?>",onfocus:"<?php echo L('passwords_not_match')?>",oncorrect:"<?php echo L('passwords_match')?>"}).compareValidator({desid:"password",operateor:"=",onerror:"<?php echo L('passwords_not_match')?>"});
	$("#email").formValidator({onshow:"<?php echo L('input').L('email')?>",onfocus:"<?php echo L('email').L('format_incorrect')?>",oncorrect:"<?php echo L('email').L('format_right')?>"}).regexValidator({regexp:"email",datatype:"enum",onerror:"<?php echo L('email').L('format_incorrect')?>"}).ajaxValidator({
	    type : "get",
		url : "",
		data :"m=member&c=member&a=public_checkemail_ajax&phpssouid=<?php echo $memberinfo['phpssouid']?>&userid=<?php echo $memberinfo['userid'];?>",
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
		onerror : "<?php echo L('email_already_exist')?>",
		onwait : "<?php echo L('connecting_please_wait')?>"
	}).defaultPassed();
  });
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
<form name="myform" action="<?php echo U('Member/Member/edit');?>" method="post" id="myform" enctype="multipart/form-data">
	<input type="hidden" name="userid" id="userid" value="<?php echo $memberinfo['userid']?>" />
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form" id="goods_albums">
		<?php if ($memberinfo['modelid'] == 1){ ?>
		<tr>
			<td>头像</td> 
			<td> 
				<div id="img1">
					<img src="<?php echo getavatar($memberinfo['userid']);?>" alt="" height=90 width=90 onerror="javascript:this.src='<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg';"/>
					<input type="hidden" name="info[avatar]" value="<?php echo getavatar($memberinfo['userid']);?>" />
				</div>
			</td>
		</tr>
		<?php }else{ ?>
		<tr>
			<td>商家logo</td> 
			<td> 
				<div id="img2">
					<?php if(empty($memberinfo['store_logo'])){?>
					<img src="<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg" alt="" height=90 width=90 />
					<?php }else{?>
					<img src="<?php echo $memberinfo['store_logo'];?>" alt="" height=90 width=90 onerror="javascript:this.src='<?php echo THEME_STYLE_PATH;?>style/images/signIn_14.jpg';" />
					<?php }?>
					<input type="hidden" name="info[store_logo]" value="<?php echo $memberinfo['store_logo'];?>" />
				</div>
			</td>
		</tr>
		<?php } ?>

		<tr>
			<td><?php echo L('password')?></td> 
			<td><input type="password" name="info[password]" id="password" class="input-text"></input></td>
		</tr>
		<tr>
			<td><?php echo L('cofirmpwd')?></td> 
			<td><input type="password" name="info[pwdconfirm]" id="pwdconfirm" class="input-text"></input></td>
		</tr>
		<tr>
			<td><?php echo "昵称"; ?></td> 
			<td><input type="text" name="info[nickname]" id="contact_name" value="<?php echo $memberinfo['nickname']?>" class="input-text"></input></td>
		</tr>
		<tr>
			<td><?php echo L('email')?></td>
			<td>
			<input type="text" name="info[email]" value="<?php echo $memberinfo['email']?>" class="input-text" id="email" size="30"></input>
			</td>
		</tr>
		<tr>
			<td><?php echo L('mp')?></td>
			<td>
			<input type="text" name="info[phone]" value="<?php echo $memberinfo['phone']?>" class="input-text" id="mobile" size="15"></input>
			</td>
		</tr>
		<?php if ($memberinfo['modelid'] == 1): ?>
		<tr>
			<td><?php echo L('point')?></td>
			<td>
			<input type="text" name="info[point]" value="<?php echo $memberinfo['point']?>" class="input-text" id="point" size="10"></input>
			</td>
		</tr>
		<?php endif ?>

		<?php if ($moreinfo['name']): ?>
		<tr>
			<td>真实姓名</td>
			<td>
			<input type="text" name="infos[name]" value="<?php echo $moreinfo['name']?>" class="input-text" id="point" size="30"></input>
			</td>
		</tr>
		<?php endif ?>

		<?php if ($moreinfo['id_number']): ?>
		<tr>
			<td>身份证号</td>
			<td>
			<input type="text" name="infos[id_number]" value="<?php echo $moreinfo['id_number']?>" class="input-text" id="point" size="30"></input>
			</td>
		</tr>
		<?php endif ?>

		<?php if ($moreinfo['bank_account']): ?>
			<tr>
				<td>银行账号</td>
				<td>
				<input type="text" name="infos[bank_account]" value="<?php echo $moreinfo['bank_account']?>" class="input-text" id="point" size="30"></input>
				</td>
			</tr>
		<?php endif ?>


		<?php if ($moreinfo['alipay_account']): ?>
		<tr>
			<td>支付宝账号</td>
			<td>
			<input type="text" name="infos[alipay_account]" value="<?php echo $moreinfo['alipay_account']?>" class="input-text" id="point" size="30"></input>
			</td>
		</tr>
		<?php endif ?>

		<?php if ($memberinfo['modelid'] == 2): ?>

			<tr>
			<td><?php echo L('member_group')?></td>
			<td>
			<?php echo $form::select($grouplist, $memberinfo['groupid'], 'name="info[groupid]"', '');?> <div class="onShow"></div>
			</td>
		</tr>
		<tr>
		<td>到期时间</td>
			<td>
				<?php echo $form::date("info[group_endtime]", dgmdate($memberinfo['group_endtime']),1)?>


			</td>
		</tr>

		<?php endif ?>

		<?php if ($memberinfo['modelid'] == 1): ?>

			<tr>
		<td>推荐人id</td>
			<td>
				<input type="text" name="info[agent_id]" value="<?php echo $moreinfo['agent_id']?>" class="input-text" size="6">

			</td>
		</tr>
		<?php endif ?>


        
        <?php if($memberinfo['modelid'] == 2){?>
        <tr>
			<td>所属专员</td>
			<td>
			 <select name="info[agent_id]" <?php if($rolename != '超级管理员'){?>disabled="false"<?php }?>>
			     <option value="-99">全部</option>
			     <?php foreach ($attract_lists as $l) {?>
			     <option value="<?php echo $l['userid'];?>" <?php if($memberinfo['agent_id'] == $l['userid']){?>selected<?php }?>><?php echo $l['username'];?></option>
			     <?php }?>
			 </select>
			</td>
		</tr>
        <?php }?>
	</table>
</fieldset>
<div class="bk15"></div>
    <input name="dosubmit" id="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
<script language="JavaScript">
<!--
	function changemodel(modelid) {
		redirect('?m=member&c=member&a=edit&userid=<?php echo $memberinfo[userid]?>&modelid='+modelid+'&pc_hash=<?php echo $_SESSION['pc_hash']?>');
	}
//-->
</script>
</html>