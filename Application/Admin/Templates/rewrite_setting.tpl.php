<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php $show_header = TRUE; ?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>

<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME); ?>" method="post" id="myform">
<fieldset>
	<legend>性能优化</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="120">开启伪静态</td> 
            <td>
                <label><input type="radio" name="setting[rewrite_rule][enabled]" value="1" <?php if ($setting['enabled'] == 1): ?>checked<?php endif ?>/> 开启</label>
                <label><input type="radio" name="setting[rewrite_rule][enabled]" value="0" <?php if ($setting['enabled'] == 0): ?>checked<?php endif ?>/> 关闭</label>
            </td>
		</tr>
		<tr>
			<td>产品分类目录</td> 
            <td>
                <input type="text" name="setting[rewrite_rule][category]" value="<?php echo $setting['category'] ?>"/>
            </td>	
		</tr>
		<tr>
			<td>商品总汇页</td> 
            <td>
                <input type="text" name="setting[rewrite_rule][all]" value="<?php echo $setting['all'] ?>"/>
            </td>	
		</tr>
		<tr>
			<td>购物返利列表</td> 
            <td>
                <input type="text" name="setting[rewrite_rule][rebate]" value="<?php echo $setting['rebate'] ?>"/>
            </td>	
		</tr>
		<tr>
			<td>免费试用列表</td> 
            <td>
                <input type="text" name="setting[rewrite_rule][trial]" value="<?php echo $setting['trial'] ?>"/>
            </td>	
		</tr>
		<tr>
			<td>九块九包邮列表</td> 
            <td>
                <input type="text" name="setting[rewrite_rule][postal]" value="<?php echo $setting['postal'] ?>"/>
            </td>	
		</tr>
		<tr>
			<td>闪电试用列表</td> 
            <td>
                <input type="text" name="setting[rewrite_rule][commission]" value="<?php echo $setting['commission'] ?>"/>
            </td>	
		</tr>				
		<tr>
			<td>积分商城列表页</td> 
            <td>
                <input type="text" name="setting[rewrite_rule][shop/lists]" value="<?php echo $setting['shop/lists'] ?>"/>
            </td>	
		</tr>
		<tr>
			<td>买家晒单页</td> 
            <td>
                <input type="text" name="setting[rewrite_rule][shop/show]" value="<?php echo $setting['shop/show'] ?>"/>
            </td>	
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
<input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>">
</form>
</div>
</div>
</body>
</html>