<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = false;
?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>member_common.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH ?>dialog/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="<?php echo THEME_STYLE_PATH ?>style/js/taobao.js"></script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('add_time'); ?>" method="post" id="myform">
<input type="hidden" name="id" value="<?php echo $info['id'] ?>">
<fieldset>
	<legend>基本信息</legend>
	<table width="100%" class="table_form">
		<tr>
			<td>商品标题：</td> 
			<td><?php echo $info['title']; ?></td>
		</tr>
		<tr>
			<td>活动周期</td> 
			<td><?php echo dgmdate($info['start_time']) ?> - <?php echo dgmdate($info['end_time'])?></td>
		</tr>
		<tr>
			<td>当前状态</td> 
			<td><span style="color:red;"><?php echo $this->activity_status[$info['status']]; ?></span></td>
		</tr>
		<tr>
			<td>商品份数</td>
			<td><?php echo $info['goods_number'] ?> 份</td>
		</tr>
		<tr>
			<td>已售份数</td>
			<td><?php echo $info['already_num'] ?> 份</td>
		</tr>
		<tr>
			<td>商品下单价</td>
			<td><?php echo $info['goods_price'] ?>美元</td>
		</tr>
		
		<tr>
			<td colspan="2"><span style="color:red;">*温馨提示，追加商品时间默认为7天，点击即可重新自动上架 ~该操作不可逆</span></td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
    <input name="dosubmit" id="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
</html>