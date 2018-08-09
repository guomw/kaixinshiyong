<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = $show_validator = $show_scroll = 1; 
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<h2 class="title-1 f14 lh28">(<?php echo $r['name'];?>)广告版位调用</h2>
<div class="bk10"></div>
<div class="explain-col">
	<strong>调用说明：</strong>将下面的调用代码复制到你需要调用的模板位置即可。
</div>
<div class="bk10"></div>
<fieldset>
	<legend>调用代码</legend>
    JS调用代码<br />
	<input name="jscode1" id="jscode1" value='<script language="javascript" src="<?php echo U('Poster/Api/show','id='.$id);?>"></script>' style="width:410px"> <input type="button" onclick="$('#jscode1').select();document.execCommand('Copy');" value="选中调用代码" class="button" style="width:114px">
</fieldset>
<div class="bk10"></div>
</div>
</body>
</html>