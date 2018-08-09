<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','Admin');
?>
<div class="pad-lr-10">
<form action="<?php echo __APP__;?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col">  接收人:  <input type="text" value="<?php echo $_GET['nickname'] ?>" class="input-text" name="nickname"> 推送时间:  <?php echo $form::date('start_time',$info['start_time'],'')?>- &nbsp; <?php echo $form::date('end_time', $info['end_time'],'')?> <input type="submit" value="<?php echo L('search')?>" class="button" name="dosubmit">
		</div>
		</td>
		</tr>
    </tbody>
</table>
<input type="hidden" name="menuid" value="<?php echo MENUID; ?>">
</form>
<form name="myform" action="<?php echo U('push_del') ?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
			<th>ID</th>
			<th>类型</th>
			<th width="35%" align="center">推送内容</th>
			<th width="10%" align="center">接收人</th>
			<th width='10%' align="center">推送时间</th>
			<th width="15%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($lists)){
	foreach($lists as $info){
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox"
			name="ids[]" value="<?php echo $info['id']?>"></td>
		<td align="center"><?php echo $info['id']?></td>
		<td><?php echo $info['type']?></td>
		<td align="" widht="35%"><?php echo $info['content'];?></td>
	
		<td align="center" width="10%"><?php echo dhtmlspecialchars(nickname($info['userid']))?dhtmlspecialchars(nickname($info['userid'])):"无昵称";?></td>
		<td align="center"><?php echo dgmdate($info['send_time'],'Y-m-d');?> </td>
		<td align="center" width="15%"> <a
			href='<?php echo U('push_del', array('ids' => $info['id'])) ?>'
			onClick="return confirm('<?php echo L('confirm', array('message' => '确认删除该信息'))?>')"><?php echo L('delete')?></a>
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
<script type="text/javascript">

function see_all(id, name) {
	window.top.art.dialog({id:'sell_all'}).close();
	window.top.art.dialog({title:'<?php echo L('details');//echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=message&c=message&a=see_all&messageid='+id,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'see_all'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'see_all'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='ids[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:"<?php echo L('before_select_operation')?>",lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}
</script>
</body>
</html>
