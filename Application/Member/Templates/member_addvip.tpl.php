<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = TRUE;
include $this->admin_tpl('header','Admin');
?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
    $(function(){
        $.formValidator.initConfig({
            formid:"myform",
            autotip:true,
            onerror:function(msg,obj){
                window.top.art.dialog({
                    content:msg,lock:true,width:'200',height:'50'
                }, function(){
                    this.close();$(obj).focus();
                })}
        });
        $("#groupday").formValidator({
            onshow:"请输入赠送时间",
            onfocus:"请输入赠送时间"
        }).inputValidator({
            min:1,
            max:5,
            onerror:"请不要输入大于5位数"
        }).regexValidator({
            datatype:'enum',
            regexp:'num1',
            onerror:'请输入数字'
        })
    })

</script>

<div class="pad_10">
<form action="<?php echo U('addvip'); ?>" method="post" name="myform" id="myform" enctype="multipart/form-data">
<input type="hidden" name="userid" value="<?php echo $userid;?>" />                                                  
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="100">赠送VIP类型</th>
		<td>
            <select id="groupid" name="info[groupid]">
                <?php foreach($merchant_group as $k=>$v){?>
                    <option value="<?php echo $v['groupid'];?>"><?php echo $v['name'];?></option>
                <?php }?>
            </select>
        </td>
	</tr>
	
	<tr>
		<th>赠送时间</th>
		<td>
            <input type="text" value="" id="groupday" name="info[groupday]" class="input-text">天
        </td>
	</tr>

    <tr>
        <th></th>
        <td><input type="hidden" name="forward" value="<?php echo U('addvip');?>">
            <input type="submit" name="dosubmit" id="dosubmit" class="dialog" value=" <?php echo L('submit')?> ">
        </td>
    </tr>

</table>
</form>
</div>
</body>
</html> 