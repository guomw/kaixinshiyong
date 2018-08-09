<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<form name="myform" action="<?php echo U('public_listorder') ?>" method="post">
<div class="pad-lr-10">
  <div class="table-list">
    <table width="100%" cellspacing="0">
      <thead>
        <tr>
          <th width="80"><?php echo L('listorder');?></th>
          <th width="100">id</th>
          <th><?php echo L('menu_name');?></th>
          <th><?php echo L('operations_manage');?></th>
        </tr>
      </thead>
      <tbody><?php echo $menus;?></tbody>
    </table>
    <div class="btn"><input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder')?>" /></div>
  </div>
</div>
</div>
</form>
</body>
</html>