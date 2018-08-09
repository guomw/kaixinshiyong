<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div>
<form name="myform" id="myform" action="<?php echo U('delete');?>" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
<thead>
<tr>
<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
<th width="5%"><?php echo L('listorder')?></th>
<th width="5%">ID</th>
<th width="10%"><?php echo L('brand_title')?></th>
<th ><?php echo L('brand_image');?></th>
<th ><?php echo L('brand_url');?></th>
<th ><?php echo L('brand_date');?></th>
<th width="120"><?php echo L('operations_manage')?></th>
</tr>
</thead>
<tbody>
<?php
if(is_array($brand_lists)){
foreach($brand_lists as $info){
?>
	<tr>
		<td align="center"><input type="checkbox" name="id[]" value="<?php echo $info['id']?>"></td>
		<td align="center"><input type='text' name='listorder[<?php echo $info['id']?>]' value="<?php echo $info['listorder']?>" class="input-text-c" size="4"></td>
		<td align="center"><?php echo $info['id']?> </td>
		<td align="center"><?php echo $info['name']?> </td>
		<td align="center"><img src="<?php echo $info['image'];?>" width="100" height="50" /></td>
		<td align="center"><?php echo $info['url']?> </td>
		<td align="center"><?php echo dgmdate($info['dateline'],'Y-m-d')?> </td>
		<td align="center">
		<a href="<?php echo U('edit',array('id'=>$info['id']));?>" onclick="javascript:edit(this, '<?php echo $info['name']?>');return false;"	title="<?php echo L('edit')?>"><?php echo L('edit')?></a> | 
		<a href="javascript:confirmurl('<?php echo U('delete',array('id[]'=>$info['id']));?>', '<?php echo L('search_word_confirm_del')?>')"><?php echo L('delete')?></a> </td>
	</tr>
<?php
}
}
?></tbody>
</table>
<div  class="btn">
<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('selected_all')?></a>/<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a>
<input type="submit" name="submit"  class="button" value="<?php echo L('remove_all_selected')?>"  onClick="return confirm('<?php echo L('badword_confom_del')?>')" />&nbsp;
<input name="dosubmit" type="submit" class="button" value="<?php echo L('listorder')?>" onclick = "document.myform.action='<?php echo U('listorder');?>'">
</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
	function edit(id, name) {
		window.top.art.dialog({id:'edit'}).close();
		window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=Document&c=Image&a=edit&id='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
	}
	
	function checkuid() {
		var ids = '';
		$("input[name='keywordid[]']:checked").each(function(i, n){
			ids += $(n).val() + ',';
		});
		if (ids == '' ) {
			window.top.art.dialog({content:'<?php echo L('badword_pleasechose')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
			return false;
		} else {
			myform.submit();
		}
	}
</script>