<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'Admin');
?>
<div class="pad-10">
<form action="<?php echo U('setting');?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('comments_module_configuration')?></legend>
	<table width="100%"  class="table_form">
    <tr>
    <th width="120"><?php echo L('comment_on_whether_to_allow_visitors')?>：</th>
    <td class="y-bg"><input type="checkbox" name="guest" value="1" <?php if ($setting['guest']){echo 'checked';}?> /></td>
  </tr>
    <tr>
    <th width="120"><?php echo L("check_comment")?>：</th>
    <td class="y-bg"><input type="checkbox" name="check" value="1" <?php if ($setting['check']){echo 'checked';}?> /></td>
  </tr>
    <tr>
    <th width="120"><?php echo L('whether_to_validate')?>：</th>
    <td class="y-bg"><input type="checkbox" name="code" value="1" <?php if ($setting['code']){echo 'checked';}?> /></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('comments_on_points_awards')?>：</th>
    <td class="y-bg"><input type="input" name="add_point" value="<?php echo  isset($setting['add_point']) ? $setting['add_point'] : '0'?>" /> <?php echo L('to_operate')?></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('be_deleted_from_the_review_points')?>：</th>
    <td class="y-bg"><input type="input" name="del_point" value="<?php echo  isset($setting['del_point']) ? $setting['del_point'] : '0'?>" /> <?php echo L('to_operate')?></td>
  </tr>
</table>

<div class="bk15"></div>
<input type="submit" id="dosubmit" name="dosubmit" class="button" value="<?php echo L('submit')?>" />
</fieldset>
</form>
</div>
</body>
</html>