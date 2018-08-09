<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});

	$("#username").formValidator({onshow:"<?php echo L('input').L('username')?>",onfocus:"<?php echo L('username').L('between_2_to_20')?>"}).inputValidator({min:2,max:20,onerror:"<?php echo L('username').L('between_2_to_20')?>"}).regexValidator({regexp:"ps_username",datatype:"enum",onerror:"<?php echo L('username').L('format_incorrect')?>"}).ajaxValidator({
	    type : "get",
		url : "",
		data :"m=member&c=member&a=public_checkname_ajax",
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
	$("#password").formValidator({onshow:"<?php echo L('input').L('password')?>",onfocus:"<?php echo L('password').L('between_6_to_20')?>"}).inputValidator({min:6,max:20,onerror:"<?php echo L('password').L('between_6_to_20')?>"});
	$("#pwdconfirm").formValidator({onshow:"<?php echo L('input').L('cofirmpwd')?>",onfocus:"<?php echo L('input').L('passwords_not_match')?>",oncorrect:"<?php echo L('passwords_match')?>"}).compareValidator({desid:"password",operateor:"=",onerror:"<?php echo L('input').L('passwords_not_match')?>"});
	$("#point").formValidator({tipid:"pointtip",onshow:"<?php echo L('input').L('point').L('point_notice')?>",onfocus:"<?php echo L('point').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('point').L('between_1_to_8_num')?>"});
	$("#email").formValidator({onshow:"<?php echo L('input').L('email')?>",onfocus:"<?php echo L('email').L('format_incorrect')?>",oncorrect:"<?php echo L('email').L('format_right')?>"}).inputValidator({min:2,max:32,onerror:"<?php echo L('email').L('between_2_to_32')?>"}).regexValidator({regexp:"email",datatype:"enum",onerror:"<?php echo L('email').L('format_incorrect')?>"}).ajaxValidator({
	    type : "get",
		url : "",
		data :"m=member&c=member&a=public_checkemail_ajax",
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
		onerror : "<?php echo L('deny_register').L('or').L('email_already_exist')?>",
		onwait : "<?php echo L('connecting_please_wait')?>"
	});
	$("#nickname").formValidator({onshow:"<?php echo L('input').L('nickname')?>",onfocus:"<?php echo L('nickname').L('between_2_to_20')?>"}).inputValidator({min:2,max:20,onerror:"<?php echo L('nickname').L('between_2_to_20')?>"}).regexValidator({regexp:"ps_username",datatype:"enum",onerror:"<?php echo L('nickname').L('format_incorrect')?>"}).ajaxValidator({
	    type : "get",
		url : "",
		data :"m=Member&c=Member&a=public_checknickname_ajax",
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
		onerror : "<?php echo L('already_exist').L('already_exist')?>",
		onwait : "<?php echo L('connecting_please_wait')?>"
	}).defaultPassed();
	
	$("#phone").formValidator({
		empty:false,
		onempty:'手机号码不能为空',
		onshow:'请输入您的手机号码（大陆地区）',
		onfocus:'请输入您的手机号码（大陆地区）'
	}).inputValidator({
		min:11,
		max:11,
		onerror:'手机号码只能为11位'
	}).regexValidator({
		datatype:'enum',
		regexp:'mobile',
		onerror:'手机号码格式不正确'
	}).ajaxValidator({
	    url : "<?php echo U('Member/Member/public_checkphone_ajax');?>",
	    datatype:'JSON',
	    async:false,
	    success:function(ret) {
	        if(ret.status == 1) {
	            return true;
	        } else {
	            return false;
	        }
	    },
	    onerror:'该手机已被占用'
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
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('add');?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		<!-- <tr>
			<td width="80"><?php echo L('username')?></td> 
			<td><input type="text" name="username"  class="input-text" id="username"></input></td>
		</tr> -->
		<tr>
			<td><?php echo L('email')?></td>
			<td>
			<input type="text" name="info[email]" value="" class="input-text" id="email" size="30"></input>
			</td>
		</tr>
		<tr>
			<td><?php echo L('password')?></td> 
			<td><input type="password" name="info[password]" class="input-text" id="password" value=""></input></td>
		</tr>
		<tr>
			<td><?php echo L('cofirmpwd')?></td> 
			<td><input type="password" name="info[pwdconfirm]" class="input-text" id="pwdconfirm" value=""></input></td>
		</tr>
		<tr>
			<td><?php echo L('nickname')?></td> 
			<td><input type="text" name="info[nickname]" id="nickname" value="" class="input-text"></input></td>
		</tr>
		<tr>
			<td><?php echo L('mp')?></td>
			<td>
			<input type="text" name="info[phone]" value="<?php echo $memberinfo['phone']?>" class="input-text" id="phone" size="15"></input>
			</td>
		</tr>
		<tr>
			<td><?php echo L('member_group')?></td>
			<td>
					<?php foreach ($groups as $r){ ?>
						<label><input type="radio" name="info[groupid]" value="<?php echo $r[groupid]; ?>" <?php if ($groupid == $r[groupid]){?>checked<?php }?> /><?php echo $r[name];?></label>&nbsp;
					<?php } ?>
			</td>
		</tr>
		<!--<tr>
			<td><?php echo L('point')?></td>
			<td>
			<input type="text" name="point" value="" class="input-text" id="point" size="10"></input>
			</td>
		</tr>
		<tr>
			<td><?php echo L('member_model')?></td>
			<td>
					<label><input type="hidden" name="modelid" value="<?php echo $modelid; ?>"  checked />商家会员</label>&nbsp;
					<?php // foreach ($models as $r){ ?>
						<?php //if($r['module'] != 'member' || $r['disabled'] == 1) continue; ?>
						  <label><input type="radio" name="modelid" value="<?php echo $r[modelid]; ?>" <?php if ($modelid == $r[modelid]){?>checked<?php }?> onclick="redirect('<?php echo U('add', array('modelid' => $r[modelid]));?>')" /><?php echo $r[name];?></label>&nbsp;
					
--><?php //} ?>
			</td>
		</tr>
		<!-- <tr>
			<td><?php echo L('vip')?></td>
			<td>
			<?php echo L('isvip')?> <input type="checkbox" name="vip" value=1 />
			<?php echo L('overduedate')?> <?php echo $form::date('overduedate', '', 1)?>
			</td>
		</tr> -->
		<?php 
				if (is_array($forminfos) && !empty($forminfos)) {
					foreach($forminfos as $key => $info){?>	
				<tr>
					<td><?php echo $info['name'];?><?php if ($info['isbase'] == 1){ ?><font color="Red">*</font><?php }?></td>
					<td><?php echo $info['form'];?>&nbsp;&nbsp;<?php echo $info['tips'];?></td>
				</tr>
					<?php
						}}
				?>
	</table>
</fieldset>

    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
</html>