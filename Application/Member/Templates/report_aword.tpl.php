<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php $show_header = TRUE; ?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#name").formValidator({onshow:"<?php echo L('input').L('groupname')?>",onfocus:"<?php echo L('groupname').L('between_2_to_8')?>"}).inputValidator({min:2,max:15,onerror:"<?php echo L('groupname').L('between_2_to_8')?>"}).regexValidator({regexp:"ps_username",datatype:"enum",onerror:"<?php echo L('groupname').L('format_incorrect')?>"}).ajaxValidator({
	    type : "get",
		url : "",
		data :"m=member&c=member_group&a=public_checkname_ajax",
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
		onerror : "<?php echo L('groupname_already_exist')?>",
		onwait : "<?php echo L('connecting_please_wait')?>"
	}).defaultPassed();
	$("#group_point").formValidator({tipid:"pointtip",onshow:"<?php echo '请输入奖励值';?>",onfocus:"<?php echo '请输入奖励值，只能为数字';?>"}).regexValidator({regexp:"num",datatype:"enum",onerror:"<?php echo '输入错误';?>"});
	$("#group_starnum").formValidator({tipid:"starnumtip",onshow:"<?php echo L('input').L('starnum')?>",onfocus:"<?php echo L('starnum').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('starnum').L('between_1_to_8_num')?>"});
	$("#maxmessagenum").formValidator({tipid:"maxmessagenumtip",onshow:"<?php echo L('input').L('maxmessagenum')?>",onfocus:"<?php echo L('maxmessagenum').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('maxmessagenum').L('between_1_to_8_num')?>"});
	$("#allowpostnum").formValidator({tipid:"allowpostnumip",onshow:"<?php echo L('input').L('allowpostnum')?>",onfocus:"<?php echo L('allowpostnum').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('allowpostnum').L('between_1_to_8_num')?>"});

});
//-->
</script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('aword') ?>" method="post" id="myform">
<input type="hidden" name="info[userid]" value="<?php echo $userid;?>">
<fieldset>
	<legend>晒单奖励</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="120">晒单奖励类型</td> 
			<td>
				<label><input type="radio" name="info[point]" checked="checked" value="1">积分</label>&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td><?php echo L('member_aword_value')?></td> 
			<td>
			<input type="text" name="info[value]" class="input-text" id="group_point" value="<?php echo $groupinfo['value']?>" size="6"></td>
		</tr>
		<tr>
			<td><?php echo L('member_aword_cause')?></td> 
			<td><textarea cols="50" rows="5" name="info[cause]"></textarea></td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
</html>