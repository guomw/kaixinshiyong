<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = TRUE;
include $this->admin_tpl('header','Admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}}); 

	$("#link_name").formValidator({onshow:"<?php echo L("input").L('link_name')?>",onfocus:"<?php echo L("input").L('link_name')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('link_name')?>"}).ajaxValidator({type : "get",url : "",data :"m=link&c=link&a=public_name&linkid=<?php echo $linkid;?>",datatype : "html",async:'false',success : function(data){	if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('link_name').L('exists')?>",onwait : "<?php echo L('connecting')?>"}).defaultPassed(); 

	$("#link_url").formValidator({onshow:"<?php echo L("input").L('url')?>",onfocus:"<?php echo L("input").L('url')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('url')?>"}).regexValidator({regexp:"^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&]*([^<>])*$",onerror:"<?php echo L('link_onerror')?>"})
	
	})
//-->
</script>
<div class="pad_10">
<form action="<?php echo U('edit');?>" method="post" name="myform" id="myform">
<input type="hidden" name="cardid" value="<?php echo $_GET['cardid']?>">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="100"><?php echo L('card_title')?>：</th>
		<td><input type="text" name="card[name]" id="card_name"
			size="30" class="input-text" value="<?php echo $lists['name']?>"></td>
	</tr>
	
	<tr id="logolink">
		<th width="100"><?php echo L('logo')?>：</th>
		<td><?php echo $form::images('card[image]', 'image', $image, 'document');?></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('card_money');?>：</th>
		<td><input type="text" name="card[money]" id="link_url"
			size="30" class="input-text" value="<?php echo $lists['money']?>"></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('card_point')?>：</th>
		<td><input type="text" name="card[point]" id="link_username"
			size="30" class="input-text"  value="<?php echo $lists['point']?>"></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('card_points')?>：</th>
		<td><input type="text" name="card[addpoint]" id="link_username"
			size="30" class="input-text" value="<?php echo $lists['addpoint']?>"></td>
	</tr>
	
	<tr>
		<th><?php echo L('card_discount')?>：</th>
		<td><input name="card[discount]" type="text" value="<?php echo $lists['discount']?>" size="30" class="input-text">
	</tr>
	
	<tr>
		<th><?php echo L('card_msg')?>：</th>
		<td><textarea name="card[discription]" id="introduce" cols="50"
			rows="6"><?php echo $lists['discription']?></textarea></td>
	</tr>
	
	<tr>
		<th><?php echo L('is_hot')?>：</th>
		<td>
			<?php 
			if($lists['ishot'] == 1){
			?>
			<input name="card[ishot]" type="radio" value="1" checked>&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;
			<input name="card[ishot]" type="radio" value="0">&nbsp;<?php echo L('no')?>
			<?php 
			}else{
			?>
			<input name="card[ishot]" type="radio" value="1" >&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;
			<input name="card[ishot]" type="radio" value="0" checked>&nbsp;<?php echo L('no')?>
			<?php 
			}
			?>
			</td>
	</tr>
	<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="<?php U('edit');?>"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html>

