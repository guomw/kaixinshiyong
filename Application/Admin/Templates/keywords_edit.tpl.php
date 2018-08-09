<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','Admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	})
//-->
</script>
<div class="pad_10">
<form action="<?php echo U('edit'); ?>" method="post" name="myform" id="myform">             
<input type="hidden" value="<?php echo $keywordid;?>" name="keywordid" />                                     
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="25%"><?php echo L('search_word_name');?> :</th>
		<td><input type="text" name="info[name]" id="word" size="20" value="<?php echo $name;?>"></td>
	</tr>
	<tr>
		<th><?php echo '搜素地址';?> :</th>
		<td>
			<select name="info[type]" id="urltype">
				<option value="0" <?php if($type == 0){?>selected<?php }?>>默认</option>
				<option value="1" <?php if($type == 1){?>selected<?php }?>>自定义</option>
			</select>
		</td>
	</tr>
	
	<tr <?php if($type == 0){?>style="display:none;"<?php }?> id="address">
		<th>地址：</th>
		<td>
			<input type="text" name="info[address]" value="<?php echo $address?>"  id="urladdress" />
		</td>
	</tr>
	<script type="text/javascript">
		$(function(){
			$("#urltype").change(function(){
				var _val = $(this).val();
				if(_val == 1){
					$("#address").show();
				}else{
					$("#address").hide();
				}
			});
		});
	</script>
	<tr>
		<th><?php echo L('seach_status');?> :</th>
		<td>
			<input type="radio" name="info[isrecommend]" value="1"  id="status" <?php if($isrecommend == 1){?>checked<?php }?>>是
			<input type="radio" name="info[isrecommend]" value="0"  id="status" <?php if($isrecommend == 0){?>checked<?php }?>>否
		</td>
	</tr>
	<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="<?php echo U('add');?>"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>
</table>
</form>
</div>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>


</body>
</html> 