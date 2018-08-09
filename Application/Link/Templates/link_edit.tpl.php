<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = TRUE;
include $this->admin_tpl('header','Admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}}); 

	$("#link_name").formValidator({onshow:"<?php echo L("input").L('link_name')?>",onfocus:"<?php echo L("input").L('link_name')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('link_name')?>"}).ajaxValidator({
		type : "get",
		url : "<?php echo U('public_check_webname');?>",
		datatype : "html",
		async:'false',
		success : function(data){	
			if( data == "1" ){
				return true;
			}else{
				return false;
			}},
			buttons: $("#dosubmit"),
			onerror : "<?php echo L('link_name').L('exists')?>",
			onwait : "<?php echo L('connecting')?>"}).defaultPassed(); 

	$("#link_url").formValidator({onshow:"<?php echo L("input").L('url')?>",onfocus:"<?php echo L("input").L('url')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('url')?>"}).regexValidator({regexp:"^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&]*([^<>])*$",onerror:"<?php echo L('link_onerror')?>"})
	
	})
//-->
</script>
<div class="pad_10">
<form action="<?php echo U('edit');?>" method="post" name="myform" id="myform">
<input type="hidden" name="linkid" value="<?php echo $_GET['linkid']?>">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="20%"><?php echo L('typeid')?>：</th>
		<td><select name="link[typeid]" id="typeid">
		<option value="0" <?php if($typeid=='0'){echo "selected";}?>>默认分类</option>
		<?php
		  $i=0;
		  foreach($types as $type_key=>$type){
		  $i++;
		?>
		<option value="<?php echo $type['typeid'];?>" <?php if($type['typeid']==$linkinfo['typeid']){echo "selected";}?>><?php echo $type['typename'];?></option>
		<?php }?>
			 
		</select></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('link_type')?>：</th>
		<td>
		<?php if($linkinfo['linktype'] == 1){
		?>
		<input name="link[linktype]" type="radio" value="1" checked="checked" style="border:0" onclick="$('#logolink').show()" class="radio_style">
	<?php echo L('logo_link')?>&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="link[linktype]" value="0" style="border:0" onclick="$('#logolink').hide()" class="radio_style">
	<?php echo L('word_link')?>
		<?php }else{?>
		<input name="link[linktype]" type="radio" value="1" style="border:0" onclick="$('#logolink').show()" class="radio_style">
	<?php echo L('logo_link')?>&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="link[linktype]" value="0" checked="checked" style="border:0" onclick="$('#logolink').hide()" class="radio_style">
	<?php echo L('word_link')?>
		<?php }	?>
		
		</td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('link_name')?>：</th>
		<td><input type="text" name="link[webname]" id="link_name"
			size="30" class="input-text" value="<?php echo $linkinfo['webname'];?>"></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('url')?>：</th>
		<td><input type="text" name="link[url]" id="link_url"
			size="30" class="input-text" value="<?php echo $linkinfo['url'];?>"></td>
	</tr>

	<?php if($linkinfo['linktype']==1){?>
	<tr id="logolink">
		<th width="100"><?php echo L('logo')?>：</th>
		<td><?php echo $form::images('link[image]', 'image', $linkinfo['image'], 'document');?></td>
	</tr>
	<?php }else{?>
	<tr id="logolink" style="display: none;">
		<th width="100"><?php echo L('logo')?>：</th>
		<td><?php echo $form::images('link[image]', 'image', $linkinfo['image'], 'document');?></td>
	</tr>
	<?php }?>
	
	<tr>
		<th width="100"><?php echo L('username')?>：</th>
		<td><input type="text" name="link[userid]" id="link_username"
			size="30" class="input-text" value="<?php echo $linkinfo['userid'];?>"></td>
	</tr>

 
	<tr>
		<th><?php echo L('web_description')?>：</th>
		<td><textarea name="link[description]" id="introduce" cols="50"
			rows="6"><?php echo $linkinfo['description'];?></textarea></td>
	</tr>
	<tr>
		<th><?php echo L('display')?>：</th>
		<td><input name="link[display]" type="radio" value="1" <?php if($linkinfo['display']==1){echo "checked";}?>>&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;<input
			name="link[display]" type="radio" value="0" <?php if($linkinfo['display']==0){echo "checked";}?>>&nbsp;<?php echo L('no')?></td>
	</tr>
	<tr>
		<th><?php echo L('passed')?>：</th>
		<td><input name="link[passed]" type="radio" value="1" <?php if($linkinfo['passed']==1){echo "checked";}?>>&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;<input
			name="link[passed]" type="radio" value="0" <?php if($linkinfo['passed']==0){echo "checked";}?>>&nbsp;<?php echo L('no')?></td>
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

