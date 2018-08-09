<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'Admin');
?>
<div class="pad_10">
<div class="bk15"></div>
<div class="explain-col">
<strong>批量导入提示</strong><br/>
<p>1、请在导入前通过FTP将需导入的图片上传到 ./uploadfile/tmp/ 目录</p>
<p>2、所有图片的正确命名格式为：地区-作者-标题-关键词.后缀 </p>
<p>3、程序在自动导入成功后会移动图片到正确的目录；</p>
</div>
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME) ?>" method="post" id="myform">
<input type="hidden" name="info[modelid]" value="2">
<table width="100%" class="table_form contentWrap">
<tr>
<td width="80">目标栏目</td> 
<td><?php echo $form::select_category(12,'name="info[catid]"', '选择栏目', 2);?></td>
</tr>
<?php foreach ($forminfos['base'] as $fieldid => $forminfo): ?>
	<?php if($fieldid != 'type') continue; ?>
<tr>
<td><?php echo $forminfo['name'] ?></td>
<td><?php echo $forminfo['form'] ?>&nsbp;<?php echo $forminfo['tips']; ?></td>
</tr>
<?php endforeach; ?>
<tr>
<td>每次导入</td> 
<td><input type="text" name="info[pagesize]" id="pagesize" class="input-text" size="5" value="10"></input>不建议单次导入的数据太大</td>
</tr> 

<tr>
<td>来源路径</td> 
<td>
	<input type="text" name="info[path]" id="path" value="" size="30" class="input-text">&nbsp;<em class='red'>请输入文件夹名称，以“/”结尾</em>（<strong>不包括./uploadfile/tmp/</strong>）
</td>
</tr> 
</table>
 	<div class="bk15"></div>
    <input name="dosubmit" type="submit" value="确定导入" class="button">
</form>
</div></div>
</body>
</html>
<script type="text/javascript">
function category_load(obj) {
	var modelid = $(obj).attr('value');
	$.get('<?php echo U('Admin/Category/public_category_load') ?>', {modelid:modelid}, function(data){
			$('#load_catid').html(data);
		  });
}
</script>


