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
<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('keywordid[]');"></th>
<th width="5%"><?php echo L('listorder')?></th>
<th width="5%">ID</th>

<th width="30%"><?php echo L('nav_name')?></th>
<th ><?php echo L('status');?></th>
<th ><?php echo L('nav_new');?></th>
<th ><?php echo L('nav_url');?></th>
<th width="120"><?php echo L('operations_manage')?></th>
</tr>
</thead>
<tbody>
<?php
if(is_array($nav_lists)){
foreach($nav_lists as $info){
?>
	<tr>
		<td align="center"><input type="checkbox" name="navid[]" value="<?php echo $info['navid']?>"></td>
		<td align="center"><input type='text' name='listorder[<?php echo $info['navid']?>]' value="<?php echo $info['listorder']?>" class="input-text-c" size="4"></td>
		<td align="center"><?php echo $info['navid']?> </td>
		<td align="center"><?php echo $info['name']?> </td>
		<td align="center"><?php if($info['status'] == 1){?>有效<?php }else{?>无效<?php }?></td>
		<td align="center"><?php if($info['isblank'] == 1){?>是<?php }else{?>否<?php }?></td>
		<td align="center"><?php echo $info['url']?> </td>
		<td align="center">
		<a href="<?php echo U('edit',array('navid'=>$info['navid']));?>" onclick="javascript:edit(this, '<?php echo $info['name']?>');return false;"	title="<?php echo L('edit')?>"><?php echo L('edit')?></a> | 
		<a href="javascript:confirmurl('<?php echo U('delete',array('navid[]'=>$info['navid']));?>', '<?php echo L('search_word_confirm_del')?>')"><?php echo L('delete')?></a> </td>
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
		window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=Document&c=Navigate&a=edit&navid='+id,width:'450',height:'200'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
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