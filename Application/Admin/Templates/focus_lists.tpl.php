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
		<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
		<th width="center" width="35px">排序</th>
		<th align="center">ID</th>
		<th width="10%">图片标题</th>
		<th>大图</th>
		<th>开始时间</th>
		<th>结束时间</th>
		<th>链接地址</th>
		<th width="120">管理操作</th>
		</tr>
		</thead>
<tbody>
<?php
if(is_array($lists)) {
foreach($lists as $info){
?>
	<tr>
		<td align="center"><input type="checkbox" name="id[]" value="<?php echo $info['id']?>"></td>
		<td align="center" width="25px" height="41px" ><input type='text' name='listorder[<?php echo $info['id']?>]' value="<?php echo $info['listorder']?>" class="input-text-c" size="4"></td>
		<td align="center" width="35px"><?php echo $info['id']?> </td>
		<td align="center"><?php echo $info['title']?> </td>
		<td align="center"><img src="<?php echo $info['image'];?>" width="100" height="50" /></td>
		<td align="center"><?php echo dgmdate($info['starttime'],'Y-m-d');?></td>
		<td align="center"><?php echo dgmdate($info['endtime'],'Y-m-d');?> </td>
		<td align="center"><?php echo $info['url']?> </td>
		<td align="center">
		<a href="<?php echo U('edit',array('id'=>$info['id']));?>" onclick="javascript:edit(this, '<?php echo $info['name']?>');return false;" title="<?php echo L('edit')?>"><?php echo L('edit')?></a> | 
		<a href="javascript:confirmurl('<?php echo U('delete',array('id[]'=> $info['id']));?>', '确定删除指定首页幻灯？')">删除</a></td>
	</tr>
<?php
}
}
?></tbody>
</table>
<div  class="btn">
<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('selected_all')?></a>/<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a>
<input type="submit" name="submit"  class="button" value="删除选中"  onClick="return confirm('确定删除已勾选的首页幻灯？')" />&nbsp;
<input name="dosubmit" type="submit" class="button" value="更新排序" onclick = "document.myform.action='<?php echo U('public_listorder');?>'">
</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
	function edit(o, t) {
		window.top.art.dialog({id:'edit'}).close();
		window.top.art.dialog({
			title:'<?php echo L('edit')?> '+t+' ',
			id:'edit',
			iframe:o.href,
			width:'500',
			height:'350'
		}, function(){
			var d = window.top.art.dialog({id:'edit'}).data.iframe;
			var form = d.document.getElementById('dosubmit');form.click();
			return false;
		}, function(){
			window.top.art.dialog({id:'edit'}).close()
		});
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