<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="myform" action="<?php echo U('delete');?>" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="center" width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('typeid[]');"></th>
			<th align="center" width="5%"><?php echo L('sort')?></th>
			<th align="center" width="10%"><?php echo L('uid')?></th>
			<th align="center" width="25%"><?php echo '商家类型名称';?></th>
			<th align="center" width="25%"><?php echo '收费标准';?></th>
			<th align="center" width="20%"><?php echo '商家标示';?></th>
			<th align="center"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($merchant_lists)){
	foreach($merchant_lists as $k=>$v) {
		$pricetype = explode(',',$v['pricetype']);
?>
    <tr>
		<td align="center" ><input type="checkbox" value="<?php echo $v['groupid']?>" name="groupid[]"></td>
		<td align="center"><input type="text" name="listorders[<?php echo $v['groupid']?>]" class="input-text" size="1" value="<?php echo $v['listorder'];?>"></td>
		<td align="center"><?php echo $v['groupid']?></td>
		<td align="center"><?php echo $v['name'];?></td>
		<!-- 收费标准 -->
		<td align="center"><?php if($pricetype[0] == 1){echo '￥'.$pricetype[1].'元/月';}elseif ($pricetype[0] == 2) {echo '￥'.$pricetype[1].'元/季';}else{echo '￥'.$pricetype[1].'元/年';}?></td>
		<td align="center"><img src="<?php echo $v['image'];?>" width="20" height="20"/></td>
		<td align="center">
			<a href="<?php echo U('edit', array('groupid' => $v['groupid'])) ?>">[<?php echo L('edit')?>]</a> 
		
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
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo U('public_sort', array('dosubmit'=>1)) ?>'" value="<?php echo L('sort')?>"/>
</div>

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit').L('member')?>《'+name+'》',id:'edit',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='groupid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

/*function member_infomation(obj, name) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}*/
function member_infomation(userid) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=Member&c=Member&a=memberinfo&userid='+userid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}

function aword(obj ,name){
	window.top.art.dialog({id:'aword'}).close();
	window.top.art.dialog({title:'<?php echo L('aword').L('member')?>《'+name+'》',id:'aword',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'aword'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'aword'}).close()});
}
//-->
</script>
</body>
</html>