<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
$(function(){
    $.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
    $("#name").formValidator({onshow:"<?php echo L('input').L('posid_name')?>",onfocus:"<?php echo L('posid_name').L('not_empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('posid_name').L('not_empty')?>"});
    $("#maxnum").formValidator({onshow:"<?php echo L('input').L('maxnum')?>",onfocus:"<?php echo L('maxnum').L('not_empty')?>"}).inputValidator({min:1,onerror:"<?php echo L('maxnum').L('not_empty')?>"}).regexValidator({datatype:'enum',regexp:'intege1',onerror:'<?php echo L('maxnum').L('not_empty')?>'}).defaultPassed();;       
})
//-->
</script>
<div class="pad_10">
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME) ?>" method="post" id="myform">
<input type="hidden" name="id" value="<?php echo $order['id']?>"></input>
<table width="100%" class="table_form">
<tr>
<td  width="80"><?php echo L('下单方式')?></td> 
<td><input type="text" name="order_way" class="input-text" value="<?php echo $order['order_way']?>" id="name"></input></td>
</tr>


<tr>
<td><?php echo L('下单方式图章')?></td> 
<td><?php echo $form::images('ico', 'image2', $order['ico'],'document');?></td>
</tr> 
<tr>
<td><?php echo L('下单方式描述')?></td> 
<td><textarea><?php echo $order['description']?></textarea>
</td>
</tr> 

</table>

    <div class="bk15"></div>
    <input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog" id="dosubmit">
</form>

</div>
</div>
</body>
</html>
<script type="text/javascript">
function category_load(obj) {
    var modelid = $(obj).attr('value');
    $.get('<?php echo U('Category/public_category_load') ?>', {modelid:modelid}, function(data){
            $('#load_catid').html(data);
        }
    );
}
</script>


