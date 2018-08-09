<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'Admin');
?>
<div class="pad-10">
<form action="<?php echo U(ACTION_NAME);?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('module_configuration')?></legend>
	<table width="100%"  class="table_form">
    <tr>
    <th width="150"><?php echo L('gbook_enable')?>：</th>
    <td class="y-bg"><input type="checkbox" name="info[gbook_enable]" value="1" <?php if ($setting['gbook_enable']){echo 'checked';}?> /></td>
  </tr>
    <tr>
    <th><?php echo L("gbook_allow_visitors")?>：</th>
    <td class="y-bg"><input type="checkbox" name="info[gbook_allow_visitors]" value="1" <?php if ($setting['gbook_allow_visitors']){echo 'checked';}?> /></td>
  </tr>
    <tr>
    <th><?php echo L('gbook_need_to_validate')?>：</th>
    <td class="y-bg"><input type="checkbox" name="info[gbook_need_to_validate]" value="1" <?php if ($setting['gbook_need_to_validate']){echo 'checked';}?> /></td>
  </tr>
  <tr>
    <th><?php echo L('gbook_notify_email')?>：</th>
    <td class="y-bg"><input type="input" class="input-text" name="info[gbook_notify_email]" value="<?php echo $setting['gbook_notify_email']?>" /> <?php echo L('gbook_notify_email_tips')?></td>
  </tr>
  <tr>
    <th><?php echo L('gbook_notify_sms')?>：</th>
    <td class="y-bg"><input type="input" class="input-text" name="info[gbook_notify_sms]" value="<?php echo $setting['gbook_notify_sms']?>" /> <?php echo L('gbook_notify_sms_tips')?></td>
  </tr>
  <tr>
    <th><?php echo L('gbook_submit_interval')?>：</th>
    <td class="y-bg"><input type="input" class="input-text" name="info[gbook_submit_interval]" value="<?php echo $setting['gbook_submit_interval']?>" /> <?php echo L('gbook_submit_interval_tips')?></td>
  </tr>
  <tr>
    <th><?php echo L('gbook_submit_total_day')?>：</th>
    <td class="y-bg"><input type="input" class="input-text" name="info[gbook_submit_total_day]" value="<?php echo $setting['gbook_submit_total_day']?>" /> <?php echo L('gbook_submit_total_day_tips')?></td>
  </tr>  
</table>

<div class="bk15"></div>
<input type="submit" id="dosubmit" name="dosubmit" class="button" value="<?php echo L('submit')?>" />
</fieldset>
</form>
</div>
</body>
</html>