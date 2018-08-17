<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_validator = true;include $this->admin_tpl('header');?>
<script type="text/javascript">
  $(document).ready(function() {
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#password").formValidator({empty:true,onshow:"<?php echo L('not_change_the_password_please_leave_a_blank')?>",onfocus:"<?php echo L('password').L('between_6_to_20')?>"}).inputValidator({min:6,max:20,onerror:"<?php echo L('password').L('between_6_to_20')?>"});
	$("#pwdconfirm").formValidator({empty:true,onshow:"<?php echo L('not_change_the_password_please_leave_a_blank')?>",onfocus:"<?php echo L('input').L('passwords_not_match')?>",oncorrect:"<?php echo L('passwords_match')?>"}).compareValidator({desid:"password",operateor:"=",onerror:"<?php echo L('input').L('passwords_not_match')?>"});
	$("#email").formValidator({onshow:"<?php echo L('input').L('email')?>",onfocus:"<?php echo L('email').L('format_incorrect')?>",oncorrect:"<?php echo L('email').L('format_right')?>"}).regexValidator({regexp:"email",datatype:"enum",onerror:"<?php echo L('email').L('format_incorrect')?>"});
  })
</script>
<div class="pad_10">
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME) ?>" method="post" id="myform">
<input type="hidden" name="info[userid]" value="<?php echo $userid?>">
<input type="hidden" name="userid" value="<?php echo $userid?>">
<input type="hidden" name="info[username]" value="<?php echo $username?>"></input>
<table width="100%" class="table_form contentWrap">
<tr>
<td width="80"><?php echo L('username')?></td> 
<td><?php echo $username?></td>
</tr>
<tr>
<td><?php echo L('password')?></td> 
<td><input type="password" name="info[password]" id="password" class="input-text"></input></td>
</tr>
<tr>
<td><?php echo L('cofirmpwd')?></td> 
<td><input type="password" name="info[pwdconfirm]" id="pwdconfirm" class="input-text"></input></td>
</tr>

<tr>
<td>联系电话</td>
<td>
<input type="text" name="info[phone]" value="<?php echo $phone?>" class="input-text" id="phone" size="11"></input>
</td>
</tr>
<tr>
<td><?php echo L('email')?></td>
<td>
<input type="text" name="info[email]" value="<?php echo $email?>" class="input-text" id="email" size="30"></input>
</td>
</tr>

<tr>
<td><?php echo L('realname')?></td>
<td>
<input type="text" name="info[realname]" value="<?php echo $realname?>" class="input-text" id="realname"></input>
</td>
</tr>

<tr>
<td><?php echo L('QQ')?></td>
<td>
<input type="text" name="info[qq]" value="<?php echo $qq;?>" class="input-text" id="qq"></input>
</td>
</tr>

<?php if (session('roleid')==1) {?>
<tr>
<td><?php echo L('userinrole')?></td>
<td>
<select name="info[roleid]">
<?php 
foreach($roles as $role) {
?>
<option value="<?php echo $role['roleid']?>" <?php echo (($role['roleid']==$roleid) ? 'selected' : '')?>><?php echo $role['rolename']?></option>
<?php 
}	
?>
</select>
</td>
</tr>
<?php }?>

<?php if ($roleid == 6){ ?>
<tr>
<td>基本工资</td>
<td>
<input type="text" name="info[fee_money]" value="<?php echo $fee_money ?>" class="input-text"></input>美元/月
</td>
</tr>
<tr>
<td>提成方式</td>
<td>
	<input type="radio" name="info[fee_type]" value="1" <?php if ($fee_type == 1): ?>
		checked
	<?php endif ?> class="js_check">按缴纳保证金提成
	<input type="radio" name="info[fee_type]"   <?php if ($fee_type == 2): ?>
		checked
	<?php endif ?> value="2" class="js_check">按单笔成交金额提成

	<input type="radio" name="info[fee_type]"  <?php if ($fee_type ==3): ?>
		checked
	<?php endif ?> value="3" class="js_check">按单笔成交服务费提成

</td>
</tr>
<tr class="js_much_show">
<td class="js_change">
<?php if ($fee_type == 2): ?>
	单笔成交金额提成
	<?php elseif($fee_type == 1): ?>
	保证金提成
   <?php elseif($fee_type == 3): ?>
    单笔成交服务费提成
	<?php endif ?> </td>
<td>
<input type="text" name="info[service_fee]" value="<?php echo $service_fee ?>" size="5">%
</td>
</tr>

<tr>
<td>钻石商家</td>
<td>
<input type="text" name="info[config][service_zuan_fee]" value="<?php echo $fee['service_zuan_fee'] ?>" size="10">/美元
（该招商专员下的商家 每次充值/续费 钻石vip会员 招商专员的一次性提成。）
</td>
</tr>

<tr>
<td>皇冠商家</td>
<td>
<input type="text" name="info[config][service_huang_fee]" value="<?php echo $fee['service_huang_fee'] ?>" size="10">/美元
</td>
</tr>

<script type="text/javascript">
	$('.js_check').click(function(){
				var a = $('input[name="info[fee_type]"]:checked').val();
				if (a == 2) {
					$('.js_change').html('单笔成交金额提成');
					$('.js_much_show').show();
				}else if(a == 1){
					$('.js_change').html('保证金提成');

					$('.js_much_show').show();

				}else if(a == 3){
					$('.js_change').html('单笔成交服务费提成');
					$('.js_much_show').show();
				};
			});

</script>

<?php }?>
</table>

    <div class="bk15"></div>
    <input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog" id="dosubmit">
</form>
</div>
</div>
</body>
</html>
