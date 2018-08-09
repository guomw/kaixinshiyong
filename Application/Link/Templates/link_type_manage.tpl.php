<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'Admin');
?>
<div class="pad-lr-10">
<form name="myform" action="<?php echo U('delete');?>" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('typeid[]');"></th>
			<th width="80"><?php echo L('link_type_listorder')?></th> 
			<th align="center"><?php echo L('type_name')?></th>
			<th width="12%" align="center"><?php echo L('type_id')?></th> 
			<th width="25%" align="center"><?php echo L('type_description')?></th> 
			<th width="20%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($link_list)){
	foreach($link_list as $k=>$info){
?>
	<tr>
		<td align="center" width="35"><input type="checkbox"
			name="typeid[]" value="<?php echo $info['typeid']?>"></td>
		<td align="center"><input name='listorders[<?php echo $info['typeid']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input_center"></td> 
		<td align="center"><?php echo $info['typename']?></td>
		<td align="center" width="12%"> <?php echo $info['typeid'];?></td>
		<td align="center" width="25%"> <?php echo $info['describe'];?></td>
		<td align="center" width="20%"><a href="<?php echo U('edit',array('typeid'=>$info['typeid']));?>"
			onclick="javascript:edit(this,'<?php echo $info['typename'];?>'); return false;"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href="javascript:confirmurl('<?php echo U('delete', array('typeid[]' => $info['typeid']));?>','<?php echo L('sure_delete')?>');"
			><?php echo L('delete')?></a>
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
<div class="btn"><a href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('selected_all')?></a>/<a
	href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a><!--全选/取消-->
<input name="dosubmit" type="submit" class="button"
	value="<?php echo L('remove_all_selected')?>"
	onClick="return confirm('<?php echo L('delete', array('message' => L('sure_delete')))?>')">&nbsp;&nbsp;</div>
</form>
<div id="pages" class="text-c"><?php echo $pages;?></div>
</div>
</body>
</html>
<script type="text/javascript">
function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:obj.href,width:'550',height:'300'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='typeid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:"<?php echo L('before_select_operations')?>",lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}
</script>
