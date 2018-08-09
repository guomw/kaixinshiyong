<?php defined('IN_ADMIN') or exit('No permission resources.');$show_header = TRUE;?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
    <form name="myform" id="myform" method="post" action="<?php echo U(ACTION_NAME) ?>">
	<fieldset>
		<legend>QQ互联配置</legend>
		<table width="100%" class="table_form">
			<tr>
				<td width="80">APP ID：</td> 
                <td><input type="text" name="setting[qqconnect][appid]" id="appid" value="<?php echo $setting['qqconnect']['appid']?>"></td>
            </tr>
            <tr>
                <td>APP KEY：</td> 
                <td><input type="text" name="setting[qqconnect][appkey]" id="appkey" value="<?php echo $setting['qqconnect']['appkey']?>"/></td>
            </tr>
            <tr class="border-none-td">
                <td colspan="2">请在配置之前确认您是否已在QQ互联管理中心创建应用，若您还没有创建应用，<a href="http://connect.qq.com/manage/index" target="_blank" rel="nofollow" class="red">点击免费创建</a></td>
            </tr>
        </table>
    </fieldset>
    <div class="bk15"></div>
    <input name="dosubmit" id="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
    </form>
</div>
</body> 
</html>
<script type="text/javascript"> 
$.formValidator.initConfig({
	formid:"myform",
	autotip:true,
	onerror:function(msg,obj){
		$(obj).focus();
        return false;
	}
});
$("#appid").formValidator({
    empty:false,
	onshow:"请输入应用APP ID",
	onfocus:"请输入应用APP ID"
}).inputValidator({
    min:6,
    onerror:'APP ID 至少为6个字符'
}).defaultPassed();

$("#appkey").formValidator({
    empty:false,
	onshow:"请输入应用APP KEY",
	onfocus:"请输入应用APP KEY"
}).inputValidator({
    min:32,
    max:32,
    onerror:'APP KEY 为32个字符'
}).defaultPassed();
</script>