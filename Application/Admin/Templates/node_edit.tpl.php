<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
$(function(){
  $.formValidator.initConfig({
      formid:"myform",
      autotip:true,
      onerror:function(msg,obj){}
    });

		$("#name").formValidator({
      onshow:"请输入菜单名称",
      onfocus:"请输入菜单名称"
    }).inputValidator({
      min:1,
      onerror:"请输入菜单名称"
    }).defaultPassed();

		$("#m").formValidator({
      onshow:"请输入模块名",
      onfocus:"请输入模块名"
    }).inputValidator({
      min:1,
      onerror:"请输入模块名"
    }).defaultPassed();
    
		$("#c").formValidator({
      onshow:"请输入控制器名",
      onfocus:"请输入控制器名"
    }).inputValidator({
      min:1,
      onerror:"请输入控制器名"
    }).defaultPassed();
		
    $("#a").formValidator({
      onshow:"请输入方法名",
      onfocus:"请输入方法名"
    }).inputValidator({
      min:1,
      onerror:"请输入方法名"
    }).defaultPassed();
	})
//-->
</script>
<div class="common-form">
<form name="myform" id="myform" action="<?php echo U(ACTION_NAME) ?>" method="post">
<table width="100%" class="table_form contentWrap">
      <tr>
        <th width="200"><?php echo L('menu_parentid')?>：</th>
        <td>
<select name="info[parentid]" style="width:200px;">
 <option value="0"><?php echo L('no_parent_menu')?></option>
<?php echo $select_categorys;?>
</select>
        </td>
      </tr>
      <tr>
        <th>菜单名称：</th>
        <td><input type="text" name="info[name]" id="name" value="<?php echo $name ?>" class="input-text" ></td>
      </tr>
	<tr>
        <th>模块名（Module）：</th>
        <td><input type="text" name="info[m]" id="m" value="<?php echo $m ?>" class="input-text" ></td>
      </tr>
	<tr>
        <th>控制器（Controller）：</th>
        <td><input type="text" name="info[c]" id="c" value="<?php echo $c ?>" class="input-text" ></td>
      </tr>
	<tr>
        <th>方法名（Action）：</th>
        <td><input type="text" name="info[a]" id="a" value="<?php echo $a ?>" class="input-text" ></td>
      </tr>
	<tr>
        <th><?php echo L('att_data')?>：</th>
        <td><input type="text" name="info[data]" value="<?php echo $data ?>" class="input-text" ></td>
      </tr>
	<tr>
        <th>显示状态：</th>
        <td>
          <label><input type="radio" name="info[display]" value="1" <?php if($display) echo 'checked';?>> 显示</label>
          <label><input type="radio" name="info[display]" value="0" <?php if(!$display) echo 'checked';?>> 隐藏</label>
        </td>
      </tr>
</table>
</div>
    <div class="bk15"></div>
    <input name="id" type="hidden" value="<?php echo $id?>">
    <div class="btn"><input type="submit" id="dosubmit" class="button" name="dosubmit" value="<?php echo L('submit')?>"/></div>
</div>
</form>
</body>
</html>