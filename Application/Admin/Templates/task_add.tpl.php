<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_validator = true;
include $this->admin_tpl('header');
?>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('add');?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"><?php echo L('任务名称')?></td> 
			<td><input type="text" name="task_name"  class="input-text"/>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td><?php echo L('奖励类型')?></td> 
			<td>
				<select name="type">
					<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>><?php echo L('积分')?></option>
					<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>><?php echo L('经验值')?></option>
					<option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>><?php echo L('金额')?></option>
				</select>

			</td>
		</tr>
		<tr>
			<td><?php echo L('奖励')?></td>
			<td><input type="text" name="task_reward"  class="input-text" size="6"/></td>
		</tr>
		<tr>
			<td><?php echo L('任务地址')?></td>
			<td><input type="text" name="url"  class="input-text" size="50"/></td>
		</tr>
		<tr>
			<td><?php echo L('任务介绍')?></td>
			<td><textarea cols="49" rows="4" name="task_desc"></textarea></td>
		</tr>

			<tr>
			<td><?php echo L('任务状态')?></td>
			<td>
				<label><input type="radio" name="task_task" value="1" >启用</label>
				<label><input type="radio" name="task_task" value="0" >禁用</label>

			</td>
		</tr>
	</table>
</fieldset>

    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
</html>