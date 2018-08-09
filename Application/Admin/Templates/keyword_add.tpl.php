<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = 1;
include $this->admin_tpl('header','Admin');?>
<script type="text/javascript">
$(document).ready(function(){
	$("#dosubmit").click(function(){
		var _this = $("#keyword").val();
		$.ajax({
			url:'<?php echo U('Admin/Setting/keyword_add')?>',
			type:'post',
			dataType:'json',
			data:{'content':_this},
			success:function(data){
				if(data == 1){
					location.reload();
					//close_dialog();
				}else{
					alert('添加失败');
				}
			}
		});
	});
});
</script>
	<div class="pad-10">
		<div class="explain-col">
					添加说明<br />
					1.多个关键词 一行一个 可批量添加多个<br />
					2.重复的关键词会被系统排除。<br />
					3.建议定期更新关键词库，<a href="#" class="color1">点此去查看</a>。<br />
					4.如果网站当中包含有已经添加的关键词，会被过滤成 * 号
		</div>
		<div class="bk10"></div>
		<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
			<tr>
				<th><?php echo L('keyword')?>：</th>
				<td><textarea name="info[content]" id="keyword" cols="50"
					rows="6"></textarea></td>
			</tr> 
			<input type="submit" name="dosubmit" id="dosubmit" class="dialog" value=" <?php echo L('submit')?> " />
		</table>
		
	</div>
	</body>
</html>