<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad_10">
<form action="<?php echo __APP__;?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">	
				<?php echo L('keyword')?>：			
				<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
				<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<form action="<?php echo U('Admin/Setting/delete');?>" method="post" name="myform">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
		<tr>
		<th width="10%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
		<th width="10%">ID</th>
		<th width="10%" align="left" ><?php echo L('keyword_name')?></th>
		<th width="10%" align="left" ><?php echo L('add_time')?></th>
		<th width="15%" ><?php echo L('operations_manage')?></th>
		</tr>
        </thead>
        <tbody>
<?php 
if(is_array($keyword_lists)){
	foreach($keyword_lists as $info){
?>
<tr>
<td align="center"><input type="checkbox" value="<?php echo $info['keywordid']?>" name="ids[]"></td>
<td width="10%" align="center"><?php echo $info['keywordid']?></td>
<td width="10%" align="center"><?php echo $info['keyword']?></td>
<td width="10%" ><?php echo date('Y-m-d H:i',$info['inputtime'])?></td>
<td width="15%"  align="center">
<a href="javascript:confirmurl('<?php echo U('Admin/Setting/delete', array('ids[]'=> $info['keywordid'])) ?>', '<?php echo L('keyword_del_cofirm')?>')"><?php echo L('delete')?></a>
</td>
</tr>
<?php 
	}
}
?>
</tbody>
</table>
<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
</div>
</form>
 <div id="pages"><?php echo $pages?></div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
<!--
function edit(obj, name) {
	window.top.art.dialog({
		title:'<?php echo L('edit')?>--'+name, 
		id:'edit', 
		iframe: obj.href,
		width:'500px',
		height:'400px'
	}, 	function(){
		var d = window.top.art.dialog({id:'edit'}).data.iframe;
		var form = d.document.getElementById('dosubmit');form.click();
		return false;
	}, function(){
		window.top.art.dialog({id:'edit'}).close()
	});
}
//-->
</script>