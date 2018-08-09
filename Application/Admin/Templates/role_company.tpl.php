
<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad_10">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
		<tr>
		<th width="10%">招商人员id</th>
		<th width="10%" align="left" > 招商专员姓名</th>
		<th width="10%" align="left" > 招商专员专属链接</th>
		<th width="10%" align="left" >基本工资</th>
		<th width="10%" align="left" >提成模式</th>
		<th width="10%" align="left" >提成比例</th>
		<th width="10%" align="left" >充值/续费钻石vip提成</th>
		<th width="10%" align="left" >充值/续费皇冠vip提成</th>
		<th width="10%" align="left" >本月获得的所有提成</th>
		<th width="15%" ><?php echo L('operations_manage')?></th>
		</tr>
        </thead>
        <tbody>
<?php $admin_founders = explode(',', C('ADMIN_FOUNDERS'));?>
<?php 
if(is_array($infos)){
	foreach($infos as $info){
?>
<tr>
<td align="center"><?php echo $info['userid']?></td>
<td><?php echo $info['username']?></td>
<td>

<?php if (C('DEFAULT_STYLE') == 'cloud3'): ?>
	<a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/zs/'.$info['userid']; ?>" ><?php echo 'http://'.$_SERVER['HTTP_HOST'].'/zs/'.$info['userid']; ?></a>
	<?php else: ?>
	<a href="javascript:;"><?php echo U('Member/Index/register',array('modelid'=>2,'agent_id'=>$info['userid']),'',TRUE,TRUE);?></a>
<?php endif ?>
</td>
<td width="10%" ><?php echo $info['fee_money']?$info['fee_money']:'0.00'?>  /月</td>
<td width="10%" >
	<?php if(!$info['fee_type']) {echo "暂无设置";} ?> 
	<?php if($info['fee_type'] == 1) { echo "按保证金提成";} ?>
	<?php if($info['fee_type'] == 2) {echo "按单笔成交下单价提成";} ?>
    <?php if($info['fee_type'] == 3) {echo "按单笔成交服务费提成";} ?>
</td>
<td ><?php echo $info['service_fee']?$info['service_fee']:0?>%</td>
<?php $info['company_config'] = string2array($info['company_config']); ?>
<td ><?php echo $info['company_config']['service_zuan_fee']?$info['company_config']['service_zuan_fee']:0; ?> 元</td>
<td ><?php echo $info['company_config']['service_huang_fee']?$info['company_config']['service_huang_fee']:0?>元</td>
<td >本月累计获得提成 
<?php 
	 $sqlmap = array();
	 $sqlmap['agent_id'] = $info['userid'];
	 $sqlmap['time'] = array('EGT',strtotime(date('Y-m')));
    echo model('company_log')->where($sqlmap)->sum('money'); 
 ?>元</td>
<td  align="center">
<a href="<?php echo U('admin/admin/company_log', array('userid' => $info['userid'])) ?>" >提成明细</a> |
<a href="<?php echo U('admin/edit', array('userid' => $info['userid'])) ?>" onclick="javascript:edit(this, '<?php echo dhtmlspecialchars($info['username'])?>'); return false;"><?php echo L('edit')?></a> | 
<?php if(!in_array($info['userid'],$admin_founders)) {?>
<a href="javascript:confirmurl('<?php echo U('admin/delete', array('userid'=> $info['userid'])) ?>', '<?php echo L('admin_del_cofirm')?>')"><?php echo L('delete')?></a>
<?php } else {?>
<font color="#cccccc"><?php echo L('delete')?></font>
<?php } ?>
</td>

</tr>
<?php 
	}
}
?>
</tbody>
</table>
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