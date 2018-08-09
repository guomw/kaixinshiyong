<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#defualtpoint").formValidator({tipid:"pointtip",onshow:"<?php echo L('input').L('defualtpoint')?>",onfocus:"<?php echo L('defualtpoint').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('defualtpoint').L('between_1_to_8_num')?>"});
	$("#defualtamount").formValidator({tipid:"starnumtip",onshow:"<?php echo L('input').L('defualtamount')?>",onfocus:"<?php echo L('defualtamount').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('defualtamount').L('between_1_to_8_num')?>"});
	$("#rmb_point_rate").formValidator({tipid:"rmb_point_rateid",onshow:"<?php echo L('input').L('rmb_point_rate')?>",onfocus:"<?php echo L('rmb_point_rate').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('rmb_point_rate').L('between_1_to_8_num')?>"});

});
//-->
</script>
<div class="pad-lr-10">
<div class="common-form">
<form name="myform" action="<?php echo U('setting'); ?>" method="post" id="myform">
	<table width="100%" class="table_form">
		<tr>
			<td width="200"><?php echo L('allow_register')?></td> 
			<td>
				<label><input type="radio" name="info[allowregister]"  class="input-radio" <?php if($member_setting['allowregister']) {?>checked<?php }?> value='1'><?php echo L('yes')?> </label>&nbsp;<label><input type="radio" name="info[allowregister]"  class="input-radio" <?php if(!$member_setting['allowregister']) {?>checked<?php }?> value='0'><?php echo L('no')?></label>
			</td>
		</tr>
		
		<tr>
			<td width="200"><?php echo L('register_model')?></td> 
			<td>
				<label><input type="radio" name="info[choosemodel]"  class="input-radio"<?php if($member_setting['choosemodel']) {?>checked<?php }?> value='1'><?php echo L('yes')?></label>&nbsp;
				<label><input type="radio" name="info[choosemodel]"  class="input-radio"<?php if(!$member_setting['choosemodel']) {?>checked<?php }?> value='0'><?php echo L('no')?></label>
			</td>
		</tr>
		
		<tr>
			<td width="200">需要邮件审核会员</td> 
			<td>
				<?php foreach ($models as $key => $v): ?>
					<label><input type="checkbox" name="info[emailcheck_model][]" value="<?php echo $key ?>" <?php echo (in_array($key, $member_setting['emailcheck_model'])) ? 'checked' : ''; ?>> <?php echo $v['name'] ?></label>&nbsp;
				<?php endforeach; ?>
				&nbsp;&nbsp;<font color=red><?php echo L('enablemailcheck_notice')?></font>
			</td>
		</tr>

		<tr>
			<td width="200">需要管理员审核会员</td> 
			<td>
				<?php foreach ($models as $key => $v): ?>
					<label><input type="checkbox" name="info[admincheck_model][]" value="<?php echo $key ?>"> <?php echo $v['name'] ?></label>&nbsp;
				<?php endforeach; ?>
			</td>
		</tr>

		<tr>
			<td width="200">注册默认会员组</td> 
			<td>
			<?php echo $form::select($grouplist, $member_setting['register_groupid'], 'name="info[register_groupid]"', L('member_group'))?>
			</td>
		</tr>

		<tr>
			<td width="200"><?php echo L('enablcodecheck')?></td> 
			<td>
				<label><input type="radio" name="info[enablcodecheck]"  class="input-radio"<?php if($member_setting['enablcodecheck']) {?>checked<?php }?> value='1'><?php echo L('yes')?></label>&nbsp;
				<label><input type="radio" name="info[enablcodecheck]"  class="input-radio"<?php if(!$member_setting['enablcodecheck']) {?>checked<?php }?> value='0'><?php echo L('no')?></label>
			</td>
		</tr>
		
		<tr>
			<td width="200"><?php echo L('rmb_point_rate')?></td> 
			<td>
				<input type="text" name="info[rmb_point_rate]" id="rmb_point_rate" class="input-text" size="4" value="<?php echo $member_setting['rmb_point_rate'];?>">
			</td>
		</tr>
				
		<tr>
			<td width="200"><?php echo L('defualtpoint')?></td> 
			<td>
				<input type="text" name="info[defualtpoint]" id="defualtpoint" class="input-text" size="4" value="<?php echo $member_setting['defualtpoint'];?>">
			</td>
		</tr>

		<tr>
			<td width="200"><?php echo L('show_register_protocol')?></td> 
			<td>
				<?php echo L('yes')?><input type="radio" name="info[showregprotocol]"  class="input-radio" <?php if($member_setting['showregprotocol']) {?>checked<?php }?> value='1'>
				<?php echo L('no')?><input type="radio" name="info[showregprotocol]"  class="input-radio" <?php if(!$member_setting['showregprotocol']) {?>checked<?php }?> value='0'>
			</td>
		</tr>
		
		<tr>
			<td width="200"><?php echo L('register_verify_message')?></td> 
			<td>
				<textarea name="info[registerverifymessage]" id="registerverifymessage" style="width:80%;height:120px;"><?php echo $member_setting['registerverifymessage']?></textarea>
				<BR><?php echo L('register_func_tips');?>

			</td>
		</tr>

		<tr>
			<td width="200"><?php echo L('forgetpasswordmessage')?></td> 
			<td>
				<textarea name="info[forgetpassword]" id="forgetpassword" style="width:80%;height:120px;"><?php echo $member_setting['forgetpassword']?></textarea>
			</td>
		</tr>

	</table>
    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="button">
</form>
</div>
</div>
</body>
</html>