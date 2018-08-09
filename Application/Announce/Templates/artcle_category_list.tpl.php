<?php 
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" action="#" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0" class="tablelist">
        <thead>
            <tr>
            <th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('aid[]');"></th>
            <th width="37"><?php echo L('listorder');?></th>
			<th align="center"><?php echo L('title')?></th>
			<th width="68" align="center"><?php echo L('startdate')?></th>
			<th width='68' align="center"><?php echo L('enddate')?></th>
			<th width='68' align="center"><?php echo L('inputer')?></th>
			<th width="68" align="center"><?php echo L('hits')?></th>
			<th width="120" align="center"><?php echo L('inputtime')?></th>
			<th width="69" align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($announce_lists)){
	foreach($announce_lists as $announce){
?>   
	<tr>
	<td align="center">
	<input type="checkbox" name="aid[]" value="<?php echo $announce['announceid']?>">
	</td>
	 <td align='center'><input name='listorders[<?php echo $announce['announceid'];?>]' type='text' size='3' value='<?php echo $announce['listorder'];?>' class='input-text-c'></td>
	<td><?php echo $announce['title']?></td>
	<td align="center"><?php echo date('Y-m-d',$announce['starttime']);?></td>
	<td align="center"><?php echo date('Y-m-d',$announce['endtime']);?></td>
	<td align="center"><?php echo $announce['username']?></td>
	<td align="center"><?php echo $announce['hits']?></td>
	<td align="center"><?php echo date('Y-m-d H:i:s', $announce['addtime'])?></td>
	<td align="center">
	<a href="javascript:edit('<?php echo $announce['announceid']?>', '<?php echo safe_replace($announce['title'])?>');void(0);"><?php echo L('edit')?></a>
	</td>
	</tr>
<?php 
	}
}
?>
</tbody>
    </table>
  
    <div class="btn"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label>
    <input type='submit'  name='submit'  class="button" value="<?php echo L('listorder');?>" onclick="document.myform.action='<?php echo U('listorder', array( 'dosubmit' => 1)) ?>'"/>
    <?php if($type == 1){?>
    	<input name='submit' type='submit' class="button" value='<?php echo L('pass_all_selected')?>' onClick="document.myform.action='?m=Announce&c=Announce&a=public_approval&passed=1'" />
		<?php }else{?>
		<input name="submit" type="submit" class="button" value="<?php echo L('remove_all_selected')?>" onClick="document.myform.action='?m=Announce&c=Announce&a=delete';return confirm('<?php echo L('affirm_delete')?>')">&nbsp;&nbsp;</div>  </div>
 	<?php }?>
 <div id="pages"><?php echo $this->db->pages;?></div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, title) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit_announce')?>--'+title, id:'edit', iframe:'?m=Announce&c=Announce&a=edit&aid='+id ,width:'700px',height:'500px'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
</script>