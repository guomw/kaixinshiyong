<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
	$show_dialog = 1;
	include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<form name="myform" action="<?php echo U('pay_deposit',array('menuid' => MENUID));?>" method="post" >
<div class="explain-col search-form">
<?php echo L('email')?>  <input type="text" value="" class="input-text" name="keyword">  
<input type="hidden" value="<?php echo MENUID?>" name="menuid">
<input type="submit" value="<?php echo L('search')?>" class="button" name="dosubmit">
</div>
</form>
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
	            <th width="10%">ID</th>
				<th width="9%">昵称</th>
	            <th width="20%">邮箱</th>
	            <th width="15%">充值金额</th>
	            <th width="15%">备注</th>
				<th width="15%">充值时间</th>
				<th width="15%">操作人</th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($lists)){
	$amount = $point = 0;
	foreach($lists as $info){
?>   
	<tr>
		<td width="10%" align="center"><?php echo $info['id']?></td>
		<td width="20%" align="center"><?php echo $info['nickname']?></td>
		<td  width="15%" align="center"><?php echo $info['email'];?></td>
		<td width="9%" align="center"><?php echo $info['money'];?></td>
		<td  width="15%" align="center"><?php echo $info['cause'];?></td>
		<td width="9%" align="center"><?php echo dgmdate($info['inputtime'],'Y-m-d H:i:s');?></td>
		<td width="9%" align="center"><?php echo $info['admin']; ?></td>
	</tr>
<?php 
	}
}
?>
    </tbody>
    </table>

 <div id="pages"> <?php echo $pages?></div>
</div>
</div>
</form>
</body>
</html>
<script type="text/javascript">
<!--
	function discount(id, name) {
	window.top.art.dialog({title:'<?php echo L('discount')?>--'+name, id:'discount', iframe:'?m=pay&c=payment&a=public_discount&id='+id ,width:'500px',height:'200px'}, 	function(){var d = window.top.art.dialog({id:'discount'}).data.iframe;
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'discount'}).close()});
}
function detail(id, name) {
	window.top.art.dialog({title:'<?php echo L('discount')?>--'+name, id:'discount', iframe:'?m=pay&c=payment&a=public_pay_detail&id='+id ,width:'500px',height:'550px'});
}
//-->
</script>