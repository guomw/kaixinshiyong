<?php 
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<div class="table-list">
<form name="searchform" action="<?php echo U('init',array('menuid' => MENUID));?>" method="post" >
<input type="hidden" name="menuid" value="<?php echo MENUID;?>">
<div class="explain-col search-form">
<?php echo L('card_title')?>  <input type="text" value="" class="input-text" name="info[card_title]"> 
<?php echo L('card_money')?>  <input type="text" value="" class="input-text" name="info[money]"> 
<?php echo L('inputtime')?>  <?php echo $form::date('info[start_addtime]',$start_addtime)?><?php echo L('to')?>   <?php echo $form::date('info[end_addtime]',$end_addtime)?> 
<?php echo $form::select($trade_status,$status,'name="info[status]"', L('all_status'))?>  
<input type="submit" value="<?php echo L('search')?>" class="button" name="searchsubmit">
</div>
</form>
<form name="myform" action="<?php echo U('init',array('menuid' => MENUID));?>" method="post" >
<table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('cardid[]');"></th>
			<th align="center"><?php echo L('card_title')?></th>
			<th width="68" align="center"><?php echo L('card_money')?></th>
			<th width='68' align="center"><?php echo L('card_point')?></th>
			<th width='68' align="center"><?php echo L('card_discount')?></th>
			<!--<th width="50" align="center"><?php echo L('addtime')?></th>  -->
			<th width="120" align="center"><?php echo L('inputtime')?></th>
			<th width="69" align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($card_list)){
	foreach($card_list as $info){
?>   
	<tr>
	<td align="center">
	<input type="checkbox" name="cardid[]" value="<?php echo $info['cardid']?>">
	</td>
	<td><?php echo $info['name']?></td>
	<td align="center"><?php echo $info['money']?></td>
	<td align="center"><?php echo $info['point']?></td>
	<td align="center"><?php echo $info['discount']?></td>
	<td align="center"><?php echo date('Y-m-d H:i:s', $info['addtime'])?></td>
	<td align="center">
	<a href="javascript:edit('<?php echo $info['cardid']?>', '<?php echo safe_replace($info['name'])?>');void(0);"><?php echo L('edit')?></a>|
	<a href="javascript:confirmurl('<?php echo U('delete', array('cardid[]' => $info['cardid']));?>','<?php echo L('sure_delete')?>');"><?php echo L('delete')?></a> 
	</td>
	</tr>
<?php 
	}
}
?>
</tbody>
    </table>
  
    <div class="btn1"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label>
        <?php if($_GET['s']==1) {?><input name='submit' type='submit' class="button" value='<?php echo L('cancel_all_selected')?>' onClick="document.myform.action='?m=announce&c=admin_announce&a=public_approval&passed=0'"><?php } elseif($_GET['s']==2) {?><input name='submit' type='submit' class="button" value='<?php echo L('pass_all_selected')?>' onClick="document.myform.action='?m=announce&c=admin_announce&a=public_approval&passed=1'"><?php }?>&nbsp;&nbsp;
		<input name="submit" type="submit" class="button" value="<?php echo L('remove_all_selected')?>" onClick="document.myform.action='?m=Member&c=MemberCard&a=delete';return confirm('<?php echo L('affirm_delete')?>')">&nbsp;&nbsp;</div>  </div>
 <div id="pages"><?php echo $this->db->pages;?></div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, title) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit_announce')?>--'+title, id:'edit', iframe:'?m=Member&c=MemberCard&a=edit&cardid='+id ,width:'700px',height:'500px'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
</script>