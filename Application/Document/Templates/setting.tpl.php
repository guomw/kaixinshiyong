<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'Admin');?>
<div class="pad_10">
<div class="bk15"></div>
<div class="explain-col">
<strong>！配置必读！</strong><br/>
<p class="red">1、非特殊情况，禁止修改此处的配置，否则可能会出现错乱！</p>
<p>2、DPI值不参与实际的裁剪算法，仅作为前台下载提示；</p>
<p>3、峰值是以图片的宽或高的最长边为准，若小于该尺寸，则不会生成和显示；</p>
<p class="blue">4、峰值若设置为0，则不生成缩略图而以原图尺寸；</p>
</div>
<div class="bk15"></div>
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME); ?>" method="post" id="myform">
	<table width="100%" class="table_form">			
		<tr>
			<td width="80" valign="top">下载配置</td> 
			<td>
				标识 <input type="text" name="setting[small][name]" class="input-text" size="2" value="<?php echo $setting['small']['name'] ?>">；峰值 <input type="text" name="setting[small][size]" size="4" value="<?php echo $setting['small']['size'] ?>">像素；印刷 <input type="text" name="setting[small][dpi]" size="4" value="<?php echo $setting['small']['dpi'] ?>">DPI
				<div class="bk3"></div>
				标识 <input type="text" name="setting[middle][name]" class="input-text" size="2" value="<?php echo $setting['middle']['name'] ?>">；峰值 <input type="text" name="setting[middle][size]" value="<?php echo $setting['middle']['size'] ?>" size="4">像素；印刷 <input type="text" name="setting[middle][dpi]" value="<?php echo $setting['middle']['dpi'] ?>" size="4">DPI
				<div class="bk3"></div>
				标识 <input type="text" name="setting[big][name]" class="input-text" size="2" value="<?php echo $setting['big']['name'] ?>">；峰值 <input type="text" name="setting[big][size]" size="4" value="<?php echo $setting['big']['size'] ?>">像素；印刷 <input type="text" name="setting[big][dpi]" size="4" value="<?php echo $setting['big']['dpi'] ?>">DPI
				<div class="bk3"></div>
				标识 <input type="text" name="setting[huge][name]" class="input-text" size="2" value="<?php echo $setting['huge']['name'] ?>">；峰值 <input type="text" name="setting[huge][size]" size="4" value="<?php echo $setting['huge']['size'] ?>">像素；印刷 <input type="text" name="setting[huge][dpi]" size="4" value="<?php echo $setting['huge']['dpi'] ?>">DPI
			</td>
		</tr>

	</table>
    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="button">
</form>
</div>
</div>
</body>
</html>