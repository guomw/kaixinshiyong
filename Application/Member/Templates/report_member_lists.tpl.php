<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col" style="color:red;"> 
			温馨提示：只支持使用风格，不支持默认模板
		</div>
		</td>
		</tr>
    </tbody>
</table>
<form name="myform" action="<?php echo U('delete');?>" method="post" onsubmit="checkuid();return false;">
<input type='hidden' name='type' value="delete"/>
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
			<th align="left">ID</th>
			<th align="left">达人名称</th>
			<th align="left">达人图片</th>
			<th align="left">达人人气</th>
			<th align="left">添加时间</th>
			<th align="left">操作</th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($report_lists)){
	foreach($report_lists as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="ids[]"></td>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><?php echo $v['name'] ?></td>
		<td align="left"><img src="<?php echo $v['image'];?>" width="50" height="80"/></td>		
		<td align="left"><?php echo $v['popularity'];?></td>
		<td align="left"><?php echo dgmdate($v['inputtime'],'Y-m-d')?></td>
		<td align="left">
			<a href="javascript:;" onclick="javascript:edit('<?php echo $v['id'];?>','<?php echo $v['name'];?>')">[修改]</a>				
			<a href="<?php echo U('delete', array('ids[]' => $v['id'])); ?>" onclick="return confirm('确定删除？该操作不可逆转')">[删除]</a>
		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>
<div class="btn">
<label for="check_box">全选/取消</label>
<input type="submit" class="button" name="dosubmit" value="删除" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function checkuid() {
	var ids='';
	$("input[name='ids[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'请勾选要删除的记录',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

function edit(id, name) {
    window.top.art.dialog({id:'edit'}).close();
    window.top.art.dialog({title:'<?php echo L('edit').L('member_group')?>《'+name+'》',id:'edit',iframe:'?m=member&c=TrialReport&a=edit&id='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}

//-->
</script>
</body>
</html>