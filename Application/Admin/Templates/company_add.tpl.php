<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_validator = true;
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#username").formValidator({onshow:"<?php echo L('input').L('username')?>",onfocus:"<?php echo L('username').L('between_2_to_20')?>"}).inputValidator({min:2,max:20,onerror:"<?php echo L('username').L('between_2_to_20')?>"}).ajaxValidator({
	    type : "get",
		url : "<?php echo U('ajax_checkusername') ?>",
		data :"",
		datatype : "html",
		async:'false',
		success : function(data){	
            if(Number(data) == "1" ) {
                return true;
			} else {
                return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "<?php echo L('user_already_exist')?>",
		onwait : "<?php echo L('connecting_please_wait')?>"
	});
	$("#password").formValidator({onshow:"<?php echo L('input').L('password')?>",onfocus:"<?php echo L('password').L('between_6_to_20')?>"}).inputValidator({min:6,max:20,onerror:"<?php echo L('password').L('between_6_to_20')?>"});
	$("#pwdconfirm").formValidator({onshow:"<?php echo L('input').L('cofirmpwd')?>",onfocus:"<?php echo L('input').L('passwords_not_match')?>",oncorrect:"<?php echo L('passwords_match')?>"}).compareValidator({desid:"password",operateor:"=",onerror:"<?php echo L('input').L('passwords_not_match')?>"});
	$("#email").formValidator({onshow:"<?php echo L('input').L('email')?>",onfocus:"<?php echo L('email').L('format_incorrect')?>",oncorrect:"<?php echo L('email').L('format_right')?>"}).regexValidator({regexp:"email",datatype:"enum",onerror:"<?php echo L('email').L('format_incorrect')?>"});
})
//-->
</script>
<div class="pad_10">
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME) ?>" method="post" id="myform">
<table width="100%" class="table_form contentWrap">
<tr>
<td width="80"><?php echo L('username')?></td> 
<td><input type="test" name="info[username]"  class="input-text" id="username"></input></td>
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
<td>联系电话</td>
<td>
<input type="text" name="info[phone]" value="" class="input-text" id="phone" size="11"></input>
</td>
</tr>
<tr>
<td><?php echo L('email')?></td>
<td>
<input type="text" name="info[email]" value="" class="input-text" id="email" size="30"></input>
</td>
</tr>
<tr>
<td><?php echo L('realname')?></td>
<td>
<input type="text" name="info[realname]" value="" class="input-text" id="realname"></input>
</td>
</tr>

<tr>
<td><?php echo L('QQ')?></td>
<td>
<input type="text" name="info[qq]" value="" class="input-text" id="qq"></input>
</td>
</tr>

<tr>
<td><?php echo L('userinrole')?></td>
<td>
<select name="info[roleid]">
<option value="6" selected="">招商专员</option>

</select>
</td>
</tr>
<tr>
<td>基本工资</td>
<td>
<input type="text" name="info[fee_money]" value="" class="input-text"></input>美元/月
</td>
</tr>
<tr>
<td>提成方式</td>
<td>
	<input type="radio" name="info[fee_type]" value="1" class="js_check">按缴纳保证金提成
	<input type="radio" name="info[fee_type]" value="2" class="js_check">按单笔成交金额提成
	<input type="radio" name="info[fee_type]" value="3" class="js_check">按单笔成交服务费提成
</td>
</tr>
<tr style="display:none;" class="js_much_show">
<td class="js_change">保证金提成</td>
<td>
<input type="text" name="info[service_fee]" value="" size="5">%</input>
（注：1.保证金提成是按照商家 成功发布活动和追加活动 缴纳的保证金提成 2. 单笔成交金额, 按照活动下单价来提成,3.单笔成交服务费是按照单笔成交的服务费进行提成）
</td>
</tr>
<tr>
<td>钻石商家每次升级提成</td>
<td>
<input type="text" name="info[config][service_zuan_fee]" value="" size="10">/美元</input>
（该招商专员下的商家 每次充值/续费 钻石vip会员 招商专员的一次性提成。）
</td>
</tr>

<tr>
<td>皇冠商家每次升级提成</td>
<td>
<input type="text" name="info[config][service_huang_fee]" value="" size="10">/美元</input>
（该招商专员下的商家 每次充值/续费 皇冠vip会员 招商专员的一次性提成。）
</td>
</tr>
</table>                       
    <div class="bk15"></div>
    <input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
</form>
</div>
</div>
</body>
</html>
<script type="text/javascript">
	$('.js_check').click(function(){
				var a = $('input[name="info[fee_type]"]:checked').val();
				if (a == 2) {
					$('.js_change').html('单笔成交金额提成');
					$('.js_much_show').show();
				}else if(a == 3){
					$('.js_change').html('单笔成交服务费提成');
					$('.js_much_show').show();
				}else if(a == 1){
					$('.js_change').html('缴纳保证金');
					$('.js_much_show').show();
				};
			});

</script>

