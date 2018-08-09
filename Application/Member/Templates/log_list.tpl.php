<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','Admin');
?>
<div class="pad-lr-10">
<form name="searchform" action="<?php echo U('init') ?>" method="post" >
<input type="hidden" value="<?php echo MENUID;?>" name="menuid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
			<div class="explain-col">
				<?php echo L('username')?>:  <input type="text" value="<?php echo $username; ?>" class="input-text" name="search[username]">  
				<?php echo L('type');?>：<input type="text" value="<?php echo $type; ?>" class="input-text" name="search[type]">
				<?php echo L('time')?>:  <?php echo $form::date('search[start_time]',$start_time,'')?> <?php echo L('to')?>   <?php echo $form::date('search[end_time]', $end_time,'')?> 
				<input type="submit" value="<?php echo L('search')?>" class="button" name="searchsubmit">
		</div>
		</td>
		</tr>
    </tbody>
</table>

</form>
<form name="myform" action="<?php //echo U('delete') ?>" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('messageid[]');"></th>
			<th><?php echo L('subject')?></th>
			<th width="35%" align="center"><?php echo L('type')?></th>
			<th width="10%" align="center"><?php echo L('addtime')?></th>
			<th width='10%' align="center"><?php echo L('username')?></th>
			<th width='10%' align="center"><?php echo L('status')?></th>
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
			name="logid[]" value="<?php echo $info['logid']?>"></td>
		<td><?php echo $info['title']?><a href="javascript:member_infomation(<?php echo $info['logid']?>, '')"><img src="<?php echo IMG_PATH;?>admin_img/detail.png"></a></td>
		<td align="center" width="35%"><?php if($info['type'] == 1){?><?php echo L('image');?><?php }elseif($info['type'] == 2){?><?php echo L('suffer')?><?php }else{?><?php echo L('pretty');?><?php }?></td>
		<td align="center" width="10%"><?php echo date('Y-m-d',$info['inputtime']);?></td>
		<td align="center" width="10%"><?php echo $info['username'];?></td>
		<td align="center" width="10%"><?php if($info['status'] == 0){?><font color="red"><?php echo L('audit')?></font><?php }else{?><?php echo  L('passed')?><?php }?></td>
		<td align="center" width="15%"> 
		<a href='<?php echo U('delete', array('logid[]' => $info['logid'])) ?>'
			onClick="return confirm('<?php echo L('confirm', array('logid[]' => $info['logid']))?>')"><?php echo L('delete')?></a>
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
	<input name="dosubmit" type="submit" class="button" value="<?php echo L('check');?>" onClick="document.myform.action='?m=Log&c=Log&a=check';">&nbsp;&nbsp;
<input name="submit" type="submit" class="button"
	value="<?php echo L('remove_all_selected')?>"
	onClick="document.myform.action='?m=Log&c=Log&a=delete';return confirm('<?php echo L('affirm_delete')?>')">&nbsp;&nbsp;</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">

function see_all(id, name) {
	window.top.art.dialog({id:'sell_all'}).close();
	window.top.art.dialog({title:'<?php echo L('details');//echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=message&c=message&a=see_all&messageid='+id,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'see_all'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'see_all'}).close()});
}
function member_infomation(logid, name) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('loginfo')?>',id:'modelinfo',iframe:'?m=Log&c=Log&a=loginfo&logid='+logid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}
</script>
</body>
</html>