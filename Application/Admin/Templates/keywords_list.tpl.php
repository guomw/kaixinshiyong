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

<th width="30%"><?php echo L('keyword_name')?></th>
<th ><?php echo '搜索地址';?></th>
<th width="120"><?php echo L('operations_manage')?></th>
</tr>
</thead>
<tbody>
<?php
if(is_array($infos)){
	$this->hits_db = D('Hits');
foreach($infos as $info){
	$hits_r = $this->hits_db->getByHitsid('c-'.$modelid.'-'.$info['id']);
?>
	<tr>
		<td align="center"><input type="checkbox" name="keywordid[]" value="<?php echo $info['keywordid']?>"></td>
		<td align="center"><input type='text' name='listorder[<?php echo $info['keywordid']?>]' value="<?php echo $info['listorder']?>" class="input-text-c" size="4"></td>

		<td align="center"><?php echo $info['keywordid']?></td>
		<td align="center"><span ><?php echo $info['name']?></span><?php if($info['status'] == 1){?><img src="<?php echo IMG_PATH;?>icon_a3a5899.png"><?php }else{?> <?php }?> </td>
		<td align="center"><?php echo $info['address']?></td>
		<td align="center">
		<a href="javascript:edit('<?php echo $info['keywordid'];?>','<?php echo $info['name'];?>')"><?php echo L('edit');?></a> | <a href="javascript:confirmurl('<?php echo U('delete',array('keywordid[]'=>$info['keywordid']));?>', '<?php echo L('search_word_confirm_del')?>')"><?php echo L('delete')?></a> </td>
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
		window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=Admin&c=Keywords&a=edit&keywordid='+id,width:'450',height:'200'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
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