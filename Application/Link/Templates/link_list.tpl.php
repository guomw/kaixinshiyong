<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'Admin');
?>
<div class="pad-lr-10">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"> 
		<?php echo L('all_linktype')?>: &nbsp;&nbsp; <a href="<?php echo U('init', array('menuid' => MENUID));?>"><?php echo L('all')?></a> &nbsp;&nbsp;
		<?php
		if(is_array($type_arr)){
			foreach($type_arr as $type){
		?>
		<a href="<?php echo U('init',array('typeid' => $type['typeid'], 'menuid' => MENUID));?>"><?php echo $type['typename'];?></a>&nbsp;
		<?php }}?>
		</div>
		</td>
		</tr>
    </tbody>
</table>
<form name="myform" id="myform" action="<?php echo U('all_delete');?>" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('linkid[]');"></th>
			<th width="35" align="center"><?php echo L('listorder')?></th>
			<th width="center" align="center">ID</th>
			<th align="center"><?php echo L('link_name')?></th>
			<th width="12%" align="center"><?php echo L('logo')?></th>
			<th width="15%" align="center"><?php echo L('url')?></th>
			<th width="10%" align="center"><?php echo L('typeid')?></th><!-- 所属分类 -->
			<th width='10%' align="center"><?php echo L('link_type')?></th>
			<th width="8%" align="center"><?php echo L('display')?></th>
			<th width="8%" align="center"><?php echo L('status')?></th>
			<th width="12%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($link_list)){
	foreach($link_list as $info){
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox" name="linkid[]" value="<?php echo $info['linkid']?>"></td>
		<td align="center" width="35"><input name='listorders[<?php echo $info['linkid']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input-text-c"></td>
		<td align="center" ><?php echo $info['linkid'];?></td>

		<td  align="center"><a href="<?php echo $info['url'];?>" title="<?php echo L('go_website')?>" target="_blank"><?php echo $info['webname']?></a> </td>
		<td align="center" width="12%"><?php if($info['linktype']==1){?>
			<?php if (!empty($info['image'])): ?>
				<img src="<?php echo $info['image'];?>" width=83 height=31>
			<?php endif ?>
			
			<?php }?></td>

		<td align="center" width="15%"><?php echo $info['url'];?></td>

		<td align="center" width="10%"><?php echo $info['typename'];?></td>
		
		<td align="center" width="10%"><?php if($info['linktype']==0){echo L('word_link');}else{echo L('logo_link');}?></td>

		<td width="8%" align="center"><?php if($info['display'] == '1'){?><?php echo L('yes');?><?php }else{?><?php echo L('no');?><?php } ?></td>	
		
		<td width="8%" align="center"><?php if($info['passed']=='0'){?><a
			href='<?php echo U('check',array('linkid'=>$info['linkid']));?>'
			onClick="return confirm('<?php echo L('pass_or_not')?>')"><font color=red><?php echo L('audit')?></font></a><?php }else{echo L('passed');}?></td>
			
		<td align="center" width="12%"><a href="<?php echo U('edit',array('linkid'=>$info['linkid']));?>"
			onclick="javascript:edit(this, '<?php echo $info['name']?>');return false;"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href="javascript:confirmurl('<?php echo U('delete', array('linkid' => $info['linkid']));?>','<?php echo L('sure_delete');?>');"
			><?php echo L('delete')?></a> 
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn"> 
<input name="dosubmit" type="submit" class="button"
	value="<?php echo L('listorder')?>" onclick = "document.myform.action='<?php echo U('sort');?>'">&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onClick="return confirm('<?php echo L('sure_delete')?>" value="<?php echo L('delete')?>"/></div>
<div id="pages"><?php echo $pages;?></div>
</form>
</div>
<script type="text/javascript">

function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:obj.href,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='linkid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:"<?php echo L('before_select_operations')?>",lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}
//向下移动
function listorder_up(id) {
	$.get('?m=link&c=link&a=listorder_up&linkid='+id,null,function (msg) { 
	if (msg==1) { 
	//$("div [id=\'option"+id+"\']").remove(); 
		alert('<?php echo L('move_success')?>');
	} else {
	alert(msg); 
	} 
	}); 
} 
</script>
</body>
</html>
