<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
  $(document).ready(function() {
	$.formValidator.initConfig({autotip:falser,formid:"myform",onerror:function(msg){alert(msg);return false;}});
	$("#name").formValidator({onshow:"<?php echo L('input').L('linkage_name').L('linkage_name_desc')?>",onfocus:"<?php echo L('linkage_name').L('not_empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('linkage_name').L('not_empty')?>"});
  })
</script>
<div class="pad_10">
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME) ?>" method="post" id="myform">
<table width="100%" class="table_form contentWrap">
<tr>
<td width="60"><?php echo L('level_menu')?></td>
<td>
<?php echo $list?>
</td>
</tr>

<tr>
<td><?php echo L('linkage_name')?></td>
<td>
<textarea name="info[name]" rows="2" cols="20" id="name" class="inputtext" style="height:90px;width:95%;"><?php echo $name?></textarea>
</td>
</tr>

<tr>
<td><?php echo L('menu_description')?></td>
<td>
<textarea name="info[description]" rows="2" cols="20" id="description" class="inputtext" style="height:45px;width:95%;"><?php echo $description?></textarea>
</td>
</tr>
</table>
 <div class="bk15"></div>
<input type="hidden" name="keyid" value="<?php echo $keyid?>">
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog" id="dosubmit">
</form>
<div class="explain-col">
<strong>温馨提示：</strong>可以在[ 菜单名称 ] 处通过一行一个进行批量添加，别忘记更新缓存哦:-)</div>
</div>
</div>
</body>
</html>
