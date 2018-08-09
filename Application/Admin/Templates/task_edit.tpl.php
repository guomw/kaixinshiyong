<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
$(function(){
    $.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
    $("#name").formValidator({onshow:"<?php echo L('input').L('posid_name')?>",onfocus:"<?php echo L('posid_name').L('not_empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('posid_name').L('not_empty')?>"});
    $("#maxnum").formValidator({onshow:"<?php echo L('input').L('maxnum')?>",onfocus:"<?php echo L('maxnum').L('not_empty')?>"}).inputValidator({min:1,onerror:"<?php echo L('maxnum').L('not_empty')?>"}).regexValidator({datatype:'enum',regexp:'intege1',onerror:'<?php echo L('maxnum').L('not_empty')?>'}).defaultPassed();    
})
//-->
</script>
<div class="pad_10">
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME) ?>" method="post" id="myform">
<input type="hidden" name="id" value="<?php echo $id ;?>"></input>
<table width="100%" class="table_form">
        <tr>
            <td width="80"><?php echo L('任务名称')?></td> 
            <td><input type="text" name="task_name"  value="<?php echo $task['task_name'] ?>" class="input-text"/>
                &nbsp;
            </td>
        </tr>
        <tr>
            <td><?php echo L('奖励类型')?></td> 
            <td>
                <select name="task_type">
                    <option value='point' <?php if(isset($task['task_type']) && $task['task_type']=='point'){?>selected<?php }?>><?php echo L('积分')?></option>
                    <option value='exp' <?php if(isset($task['task_type']) && $task['task_type']=='exp'){?>selected<?php }?>><?php echo L('经验值')?></option>
                    <option value='money' <?php if(isset($task['task_type']) && $task['task_type']=='money'){?>selected<?php }?>><?php echo L('金额')?></option>
                </select>

            </td>
        </tr>
        <tr>
            <td><?php echo L('奖励')?></td>
            <td><input type="text" name="task_reward" value="<?php echo $task['task_reward'] ?>" class="input-text" size="6"/></td>
        </tr>
        <tr>
            <td><?php echo L('任务地址')?></td>
            <td><input type="text" name="url"  class="input-text" value="<?php echo $task['url'] ?>" size="50"/></td>
        </tr>
        <tr>
            <td><?php echo L('任务介绍')?></td>
            <td><textarea cols="49" rows="4" name="task_desc"><?php echo $task['task_desc'] ?></textarea></td>
        </tr>

            <tr>
            <td><?php echo L('任务状态')?></td>
            <td>
                <label><input type="radio" name="task_status" <?php  if($task['task_status'] == 1){ ?> checked <?php }?> value="1" >启用</label>
                <label><input type="radio" <?php  if($task['task_status'] == 0){ ?> checked <?php }?> name="task_status" value="0" >禁用</label>

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


