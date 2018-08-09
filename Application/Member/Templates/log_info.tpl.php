<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = 1;
include $this->admin_tpl('header','Admin');
?>
<div class="pad-lr-10">
<div class="table-list">
<div class="common-form">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="10%"><?php echo L('username')?>：</td> 
			<td><?php echo $info['username']?></td>
		</tr>
		
		<tr>
			<td width="10%"><?php echo L('type')?>：</td> 
			<td><?php if($info['type'] == 0){?><?php echo L('image');?><?php }else if($info['type'] == 1){?><?php echo L('suffer');?><?php }else{?><?php echo L('pretty');?><?php }?></td>
		</tr>
		
		<tr>
			<td width="10%"><?php echo L('subject')?>：</td> 
			<td><?php echo $info['title'];?></td>
		</tr>
		
		<tr>
			<td width="10%"><?php echo L('introduce')?>：</td> 
			<td><?php echo $info['introduce'];?></td>
		</tr>
		
		<tr>
			<td width="10%"><?php echo L('content')?>：</td>
			<td><?php echo $info['content']?></td>
		</tr>
		
		<tr>
			<td width="10%"><?php echo L('hits')?>：</td>
			<td><?php echo $info['hits']?></td>
		</tr>
		
	</table>
</fieldset>
<div class="bk15"></div>
<input type="button" class="dialog" name="dosubmit" id="dosubmit" onclick="window.top.art.dialog({id:'modelinfo'}).close();"/>
</div>
</div>
</body>
</html>