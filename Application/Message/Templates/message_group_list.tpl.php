<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div class="pad-lr-10">

<form name="myform" action="<?php echo U('delete_group') ?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('message_group_id[]');"></th>
			<th><?php echo L('subject')?></th>
			<th width="35%" align="center"><?php echo L('content')?></th>
			<th width="15%" align="center"><?php echo L('sendtime')?></th>
			<th width='10%' align="center"><?php echo L('status')?></th>
			<th width="10%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox"
			name="message_group_id[]" value="<?php echo $info['id']?>"></td>
		<td><?php echo $info['subject']?></td>
		<td align="" widht="35%"><?php echo $info['content'];?></td>
		<td align="center" width="15%"><?php echo date('Y-m-d H:i:s',$info['inputtime']);?></td>
		<td align="center" width="10%"><?php if($info['status']==1){echo L('show_m');}else {echo '<font color=red>'.L('close').'</font>';}?></td>
		<td align="center" width="10%"> <a
			href='<?php echo U('delete_group', array('message_group_id' => $info['id'])) ?>'
			onClick="return confirm('<?php echo L('confirm', array('message' => daddslashes($info['subject'])))?>')"><?php echo L('delete')?></a>
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
	onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a>
<input name="submit" type="submit" class="button"
	value="<?php echo L('remove_all_selected')?>"
	onClick="return confirm('<?php echo L('confirm', array('message' => L('selected')))?>')">&nbsp;&nbsp;</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
</body>
</html>
