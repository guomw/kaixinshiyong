<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="table-list pad-lr-10">
<form name="myform" action="<?php echo U('public_listorder') ?>" method="post">
    <table width="100%" cellspacing="0">
        <thead>
		<tr>
		<th width="10%"><?php echo L('listorder');?></th>
		<th width="10%">ID</th>
		<th width="15%"  align="left" ><?php echo L('role_name');?></th>
		<th width="265"  align="left" ><?php echo L('role_desc');?></th>
		<th width="5%"  align="left" ><?php echo L('role_status');?></th>
		<th class="text-c"><?php echo L('role_operation');?></th>
		</tr>
        </thead>
<tbody>
<?php 
if(is_array($infos)){
	foreach($infos as $info){
?>
<tr>
<td width="10%" align="center"><input name='listorders[<?php echo $info['roleid']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input-text-c"></td>
<td width="10%" align="center"><?php echo $info['roleid']?></td>
<td width="15%"  ><?php echo $info['rolename']?></td>
<td width="265" ><?php echo $info['description']?></td>
<td width="5%"><a href="?m=admin&c=role&a=change_status&roleid=<?php echo $info['roleid']?>&disabled=<?php echo ($info['disabled']==1 ? 0 : 1)?>"><?php echo $info['disabled']? L('icon_locked'):L('icon_unlock')?></a></td>
<td  class="text-c">
<?php if($info['roleid'] > 1 || $info['rolename'] == '招商专员') {?>
<a href="<?php echo U('priv_setting', array('roleid' => $info['roleid'])) ?>" onclick="javascript:setting_role(this, '<?php echo daddslashes($info['rolename'])?>');return false;"><?php echo L('role_setting');?></a> | 
<?php } else {?>
<font color="#cccccc"><?php echo L('role_setting');?></font> |
<?php }?>
<a href="<?php echo U('Admin/init', array('roleid' => $info['roleid'])) ?>"><?php echo L('role_member_manage');?></a> | 
<?php if($info['roleid'] > 1  && $info['rolename'] != '招商专员') {?>
<a href="<?php echo U('edit', array('roleid' => $info['roleid'])) ?>"><?php echo L('edit')?></a> | 
<a href="javascript:confirmurl('<?php echo U('delete', array('roleid' => $info['roleid'])) ?>', '<?php echo L('posid_del_cofirm')?>')"><?php echo L('delete')?></a>
<?php } else {?>
<font color="#cccccc"><?php echo L('edit')?></font> | <font color="#cccccc"><?php echo L('delete')?></font>
<?php }?>
</td>
</tr>
<?php 
	}
}
?>
</tbody>
</table>
<div class="btn"><input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder')?>" /></div>
</form>
</div>

<script type="text/javascript">
<!--
function setting_role(obj, name) {
	window.top.art.dialog({
		title:'<?php echo L('sys_setting')?>《'+name+'》',
		id:'edit',
		iframe:obj.href,
		width:'700',
		height:'500'
	});
}

function setting_cat_priv(obj, name) {
	window.top.art.dialog({
		title:'<?php echo L('usersandmenus')?>《'+name+'》',
		id:'edit',
		iframe:obj.href,
		width:'700',
		height:'500'
	});
}
//-->
</script>
</body>
</html>
