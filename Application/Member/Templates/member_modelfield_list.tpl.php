<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'Admin');?>
<div class="pad-lr-10">
	<div class="table-list">
		<div class="bk10"></div>
		<form name="myform" id="myform" action="<?php echo U('sort');?>" method="post">
		<table width="100%" cellspacing="0" >
        <thead>
            <tr>
				<th width="70"><?php echo L('listorder')?></th>
	            <th width="90"><?php echo L('fieldname')?></th>
				<th width="100"><?php echo L('cnames');?></th>
				<th width="100"><?php echo L('type');?></th>
	            <th width="50"><?php echo L('must_input');?></th>
	            <th width="50"><?php echo L('search');?></th>
	            <th width="50"><?php echo L('listorder');?></th>
				<th width="50"><?php echo L('disabled');?></th>
				<th><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
        <tbody class="td-line">
        <?php
        foreach($datas as $r) {
        ?>
        <tr>
        	<td align='center' width='70'><input name='listorders[<?php echo $r['fieldid']?>]' type='text' size='3' value='<?php echo $r['listorder']?>' class='input-text-c'></td>
        	<td width='90'><?php echo $r['field']?></td>
        	<td width="100"><?php echo $r['name']?></td>
        	<td width="100" align='center'><?php echo $r['formtype']?></td>
        	<td width="50" align='center'><?php echo $r['isbase'] ? L('icon_unlock') : L('icon_locked')?></td>
        	<td width="50" align='center'><?php echo $r['issearch'] ? L('icon_unlock') : L('icon_locked')?></td>
        	<td width="50" align='center'><?php echo $r['isorder'] ? L('icon_unlock') : L('icon_locked')?></td>
        	<td width="50" align='center'><?php echo $r['disabled'] ? L('icon_unlock') : L('icon_locked')?></td>
        	<td align='center'><a href="<?php echo U('edit', array('fieldid' => $r['fieldid'])); ?>" onclick="javascript:edit(this, '<?php echo $r['name']?>');return false;"><?php echo L('modify')?></a> | 
        	<a href="<?php echo U('disabled', array('fieldid' => $r['fieldid'])) ?>"><?php if (!$r['disabled']): ?><?php echo L('disable')?><?php else: ?><?php echo L('enable')?><?php endif ?></a> | 
			<a href="javascript:confirmurl('<?php echo U('delete', array('fieldid' => $r['fieldid'])) ?>','<?php echo L('sure_delete')?>')"><?php echo L('delete')?></a>
		</td>
	</tr>
	<?php } ?>
    </tbody>
    </table>
<div class="btn"><input type="submit" class="button" name="dosubmit" value="<?php echo L('sort')?>"/>
</div> 
<div id="pages"><?php if(isset($pages)){echo $pages;}?></div>
</div>
</div>
</form>
<div id="PC__contentHeight" style="display:none">160</div>
<script language="JavaScript">
<!--
function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({
		title:'<?php echo L('edit').L('field')?>《'+name+'》',
		id:'edit',
		iframe:obj.href,
		width:'700',
		height:'500'
	}, function(){
		var d = window.top.art.dialog({id:'edit'}).data.iframe;
		d.document.getElementById('dosubmit').click();
		return false;
	}, function(){
		window.top.art.dialog({id:'edit'}).close()
	});
}

function move(id, name) {
	window.top.art.dialog({id:'move'}).close();
	window.top.art.dialog({title:'<?php echo L('move')?>《'+name+'》',id:'move',iframe:'?m=member&c=member_model&a=move&modelid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'move'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'move'}).close()});
}
//-->
</script>
</body>
</html>