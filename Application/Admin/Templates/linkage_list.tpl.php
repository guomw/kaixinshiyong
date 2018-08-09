<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad_10">
<div class="explain-col">
温馨提示：添加联动菜单后，请点击联动菜单后“更新缓存”按钮！<font color="red">若联动数据超大，强烈建议使用联动菜单的方式来调用。</font>
</div>
<div class="bk10"></div>
<form name="myform" action="?m=admin&c=role&a=listorder" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
		<tr>
		<th width="10%">ID</th>
		<th width="20%" align="left" ><?php echo L('linkage_name')?></th>
		<th width="30%" align="left" ><?php echo L('linkage_desc')?></th>
		<th width="20%" ><?php echo L('linkage_calling_code')?></th>
		<th width="20%" ><?php echo L('operations_manage')?></th>
		</tr>
        </thead>
        <tbody>
		<?php 
		if(is_array($infos)){
			foreach($infos as $info){
		?>
		<tr>
		<td width="10%" align="center"><?php echo $info['linkageid']?></td>
		<td width="20%" ><?php echo $info['name']?></td>
		<td width="30%" ><?php echo $info['description']?></td>
		<td width="20%"  class="text-c"><input type="text" value="{menu_linkage(<?php echo $info['linkageid']?>,'L_<?php echo $info['linkageid']?>')}" style="width:200px;"></td>
		<td width="20%" class="text-c"><a href="<?php echo U('public_manage_submenu', array('keyid' => $info['linkageid'])) ?>"><?php echo L('linkage_manage_submenu')?></a> | <a href="<?php echo U('edit', array('linkageid' => $info['linkageid'])) ?>" onclick="edit(this,'<?php echo daddslashes($info['name'])?>');return false;"><?php echo L('edit')?></a> | <a href="javascript:confirmurl('<?php echo U('delete', array('linkageid' => $info['linkageid'])) ?>', '<?php echo L('linkage_is_del')?>')"><?php echo L('delete')?></a> | <a href="<?php echo U('public_cache', array('linkageid' => $info['linkageid'])) ?>"><?php echo L('update_backup')?></a></td>
		</tr>
		<?php 
			}
		}
		?>
</tbody>
</table>
</div>
</div>
</form>
<script type="text/javascript">
<!--
function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:name,id:'edit',iframe:obj.href,width:'500',height:'200'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
//-->
</script>
</body>
</html>