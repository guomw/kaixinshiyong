<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php $show_header = TRUE; ?>
<?php include $this->admin_tpl('header', 'Admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>

<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('score_set'); ?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('模板设置')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td><?php echo "发送邮件模板"?></td> 
			<td>
				<textarea name="info[registerverifymessage]" style="margin: 0px; width: 720px; height: 320px;"><?php echo $member_setting['registerverifymessage']; ?>" </textarea>
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

