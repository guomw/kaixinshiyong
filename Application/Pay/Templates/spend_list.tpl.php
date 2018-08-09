<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
	$show_dialog = 1;
	include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<div class="table-list">
<form name="searchform" action="<?php echo U('init',array('menuid' => MENUID));?>" method="post" >
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
		
		消费时间  <?php echo $form::date('starttime',$format::date($starttime))?> <?php echo L('to')?>   <?php echo $form::date('endtime',$format::date($endtime))?> 
		<?php echo  $form::select(array('1'=>L('昵称'), '2'=>L('userid')), $user_type, 'name="user_type"')?>： <input type="text" value="<?php echo $username?>" class="input-text" name="username"> 
		<!--<?php echo $form::select(array(''=>L('op'), '1'=>L('username'), '2'=>L('userid')), $op_type, 'name="op_type"')?>：
		 <input type="text" value="<?php echo $op?>" class="input-text" name="op">  -->
		<input type="hidden" value="<?php echo MENUID?>" name="menuid">
		<input type="submit" value="<?php echo L('search')?>" class="button" name="dosubmit">
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="2%"><?php echo L('ID')?></th>
            <th width="5%"><?php echo '会员类型';?></th>
            <th width="10%"><?php echo L('username')?></th>
            <th width="20%"><?php echo L('content_of_consumption')?></th>
            <th width="15%"><?php echo L('empdisposetime')?> </th>
            <th width="9%"><?php echo L('op')?></th>
            <th width="8%"><?php echo '消费金额'?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($lists)){
	$amount = $point = 0;
	foreach($lists as $info){
?>   
	<tr>
	<td align="center"><?php echo $info['id']?></td>
	<td align="center"><?php if($info['modelid'] == 1){?>会员<?php }else{?>商家<?php }?></td>
	<td width="10%" align="center"><?php echo $info['username']?></td>
	<td width="20%" align="center"><?php echo $info['cause']?></td>
	<td  width="15%" align="center"><?php echo dgmdate($info['dateline'],'Y-m-d')?></td>
	<td width="9%" align="center"><?php if (!empty($info['op_userid'])) {echo $info['op_username'];} else {echo L('self');}?></td>
	<td width="8%" align="center"><?php echo $info['num']?></td>
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