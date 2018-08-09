<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php  $show_header = true;?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="120"><?php echo '店铺名称';?></td> 
			<td><?php echo $info['store_name'];?></td>
		</tr>
		<tr>
			<td width="120"><?php echo '商家logo';?></td> 
			<td><img src="<?php echo $info['store_logo'];?>" width="50" height="50"/></td>
		</tr>
		<tr>
			<td width="120"><?php echo '店铺网址';?></td> 
			<td><?php echo $info['store_address'];?></td>
		</tr>
		<tr>
			<td width="120"><?php echo '联系人';?></td> 
			<td><?php echo $info['contact_name'];?></td>
		</tr>
		<tr>
			<td width="120"><?php echo '联系旺旺';?></td> 
			<td><?php echo $info['contact_want'];?></td>
		</tr>
		<tr>
			<td width="120"><?php echo '联系QQ';?></td> 
			<td><?php echo $info['store_qq'];?></td>
		</tr>
	</table>
</fieldset>
</div>
</body>
</html>