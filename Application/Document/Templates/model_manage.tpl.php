<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','Admin');?>
<div class="pad-lr-10">
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
            <tr>
			 <th width="100">modelid</th>
            <th width="100"><?php echo L('model_name');?></th>
			<th width="100"><?php echo L('tablename');?></th>
            <th ><?php echo L('description');?></th>
            <th width="100"><?php echo L('status');?></th>
            <th width="100"><?php echo L('items');?></th>
			<th width="230"><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
	<?php
	foreach($datas as $r) {
		$tablename = $r['name'];
	?>
    <tr>
		<td align='center'><?php echo $r['modelid']?></td>
		<td align='center'><?php echo $tablename?></td>
		<td align='center'><?php echo $r['tablename']?></td>
		<td align='center'>&nbsp;<?php echo $r['description']?></td>
		<td align='center'><?php echo $r['disabled'] ? L('icon_locked') : L('icon_unlock')?></td>
		<td align='center'><?php echo $r['items']?></td>
		<td align='center'><a href="<?php echo U('ModelField/init', array('modelid' => $r['modelid'])); ?>"><?php echo L('field_manage');?></a> | <a href="<?php echo U('edit', array('modelid' => $r['modelid'])) ?>" onclick="javascript:edit(this,'<?php echo addslashes($tablename);?>'); return false;"><?php echo L('edit');?></a> | <a href="<?php echo U('disabled', array('modelid' => $r['modelid'])) ?>"><?php echo $r['disabled'] ? L('field_enabled') : L('field_disabled');?></a> | <a href="<?php echo U('delete', array('modelid' => $r['modelid'])) ?>" onclick="model_delete(this,'<?php echo $r['modelid']?>','<?php echo str_replace('{message}', addslashes($tablename), L('confirm_delete_model'));?>',<?php echo $r['items']?>); return false;"><?php echo L('delete')?></a> | <a href="<?php echo U('export', array('modelid' => $r['modelid'])) ?>"><?php echo L('export');?></a></td>
	</tr>
	<?php } ?>
    </tbody>
    </table>
   <div id="pages"><?php echo $pages;?>
  </div>
</div>
<script type="text/javascript"> 
<!--
window.top.$('#display_center_id').css('display','none');
function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({
		title:'<?php echo L('edit_model');?>《'+name+'》',
		id:'edit',
		iframe:obj.href,
		width:'580',
		height:'420'
	}, function(){
		var d = window.top.art.dialog({id:'edit'}).data.iframe;
		d.document.getElementById('dosubmit').click();
		return false;
	}, function(){
		window.top.art.dialog({id:'edit'}).close()
	});
}
function model_delete(obj,id,name,items){
	if(items) {
		alert('<?php echo L('model_does_not_allow_delete');?>');
		return false;
	}
	window.top.art.dialog({content:name, fixed:true, style:'confirm', id:'model_delete'}, 
	function(){
		$.get(obj.href,function(data){
			if(data) {
				$(obj).parent().parent().fadeOut("slow");
			}
		})
	}, 
	function(){});
};

//-->
</script>
</body>
</html>
