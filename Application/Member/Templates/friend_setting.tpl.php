<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php $show_header = TRUE; ?>
<?php include $this->admin_tpl('header', 'Admin');?>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('setting'); ?>" method="post" id="myform">
<fieldset>
	<legend>邀请好友设置（固定任务）</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"></td> 
			<td>
				当邀请的好友完成手机认证成功，邀请人获得奖励： <input type="text" class="input-text" name="fix[cost]" value="<?php if($setting['fix']['cost'] >= 0 ){echo $setting['fix']['cost'];}else{echo 100;}?>" size="5"/> 
                   <select name="fix[type]">
                   		<option value="money" <?php if($setting['fix']['type'] == 'money'){?>selected<?php }?>>现金/美元</option>
                   		<option value="point" <?php if($setting['fix']['type'] == 'point'){?>selected<?php }?>>积分</option>
                   </select>
            				
            	 被邀请者可获得奖励： <input type="text" class="input-text" name="fix[cost2]" value="<?php if($setting['fix']['cost2'] >= 0 ){echo $setting['fix']['cost2'];}else{echo 100;}?>" size="5"/> 
                               <select name="fix[type2]">
                               		<option value="money" <?php if($setting['fix']['type2'] == 'money'){?>selected<?php }?>>现金/美元</option>
                               		<option value="point" <?php if($setting['fix']['type2'] == 'point'){?>selected<?php }?>>积分</option>
                               </select>
                        </td>
		</tr>

<!-- 		<tr>
	<td width="80"></td> 
	<td>
		被邀请者奖励： <input type="text" class="input-text" name="fix[cost2]" value="<?php if($setting['fix']['cost2'] >= 0 ){echo $setting['fix']['cost2'];}else{echo 100;}?>" size="5"/> 
                   <select name="fix[type2]">
                   		<option value="money" <?php if($setting['fix']['type2'] == 'money'){?>selected<?php }?>>现金/美元</option>
                   		<option value="point" <?php if($setting['fix']['type2'] == 'point'){?>selected<?php }?>>积分</option>
                   </select>
            </td>
</tr> -->

		<tr>
			<td width="80"></td> 
			<td>
				  被邀请者完成三笔(试用订单或者赚佣金活动) 被邀请者可获得一次性奖励： <input type="text" class="input-text" name="fix[cost3]" value="<?php if($setting['fix']['cost3'] >= 0 ){echo $setting['fix']['cost3'];}else{echo 100;}?>" size="5"/> 
                   <select name="fix[type3]">
                   		<option value="money" <?php if($setting['fix']['type3'] == 'money'){?>selected<?php }?>>现金/美元</option>
                   		<option value="point" <?php if($setting['fix']['type3'] == 'point'){?>selected<?php }?>>积分</option>
                   </select>
                   (用于鼓励被邀请人参与网站活动)
            </td>
		</tr>
		
		<!-- <tr>
			<td></td>
			<td>
				当邀请的好友注册成功，奖励 <input type="text" class="input-text" name="fix[r_cost]" value="<?php //if($setting['fix']['r_cost'] >= 0){echo $setting['fix']['r_cost'];}else{echo 100;}?>" size="5"/> 
		                   <select name="fix[r_type]">
		                   		<option value="money" <?php //if($setting['fix']['r_type'] == 'money'){?>selected<?php //}?>>现金/美元</option>
		                   		<option value="point" <?php //if($setting['fix']['r_type'] == 'point'){?>selected<?php //}?>>积分</option>
		                   </select>
			</td>
		</tr> -->
	</table>
</fieldset>
 <div class="bk15"></div>


<fieldset>
	<legend>邀请好友等级设置</legend>
	(目前系统最多只支持 4级好友，奖励规则是以下设置奖励)<br/>
	<br/><br/>
	<table width="100%" class="table_form" id="accumulates">
		<tr>
			<td width="80" colspan="2">
				<div class="content-menu ib-a blue">
					<a class="add fb" href="javascript:;" id="add_friend"><em>添加推荐等级</em></a>
				</div>	
			</td>
		</tr>
		<?php if(empty($setting['friend'])){?>
		<tr>
			<td width="80"></td>
			<td>
				1级推荐好友奖励<input type="text" class="input-text" name="friend[1][cost]" value="5" size="5"/>&nbsp;&nbsp;
				<select name="friend[1][type]">
                   		<option value="money" <?php if($setting['friend']['type'] == 'money'){?>selected<?php }?>>现金</option>
                   		<option value="point" <?php if($setting['friend']['type'] == 'point'){?>selected<?php }?>>积分</option>
                 </select>
				
			</td>
		</tr>
		<?php }else{?>
		<?php foreach ($setting['friend'] as $k=>$v) {?>
		<tr>
			<td width="80"></td>
			<td>
				<?php echo $k ?>级推荐好友,奖励
				<input type="text" class="input-text" name="friend[<?php echo $k;?>][cost]" value="<?php echo $v['cost'];?>" size="5"/>&nbsp;&nbsp;
				<select name="friend[<?php echo $k;?>][type]">
                   		<option value="money"  <?php if($v['type'] == 'money'){?>selected<?php }?>>现金</option>
                   		<option value="point" <?php if($v['type'] == 'point'){?>selected<?php }?>>积分</option>
               </select>

				<?php if ($k > 1){?>
               <span style="cursor:pointer" class="deletes">删除</span>
               <?php } ?>

			</td>
		</tr>
		<?php }}?>


	</table> 
			<font color="red">(备注：该奖励是直接奖励给邀请人 获得奖励前提条件是被邀请人需完成一笔试用或者返利订单)</font> <br/>
			<font >(1级推荐好友 指的是 A邀请B  A可以获得的奖励，2级推荐好友是指B邀请C a可以获得的奖励)</font><br/>
			<font >(示例：A邀请了B B邀请了C  C就是B的一级好友，是A的二级好友)</font><br/>

</fieldset>
 <div class="bk15"></div>






 <fieldset>
	<legend>邀请好友设置（累积任务）</legend>
	<table width="100%" class="table_form" id="accumulate">
		<tr>
			<td width="80" colspan="2">
				<div class="content-menu ib-a blue">
					<a class="add fb" href="javascript:;" id="add_task"><em>添加累积任务</em></a>
				</div>	
			</td>
		</tr>
		<?php if(empty($setting['setting'])){?>
		<tr>
			<td width="80"></td>
			<td>
				当用户累积完成	<input type="text" class="input-text" name="setting[0][num]" value="1" size="5"/> 笔订单（含试用和返利），奖励
				<input type="text" class="input-text" name="setting[0][cost]" value="5" size="5"/>&nbsp;&nbsp;
				<select name="setting[0][type]">
                   		<option value="money" >现金/美元</option>
                   		<option value="point" selected>积分</option>
               </select>
			</td>
		</tr>
		<?php }else{?>
		<?php foreach ($setting['setting'] as $k=>$v) {?>
		<tr>
			<td width="80"></td>
			<td>
				当用户累积完成	<input type="text" class="input-text" name="setting[<?php echo $k;?>][num]" value="<?php echo $v['num'];?>" size="5"/> 笔订单（含试用和返利），奖励
				<input type="text" class="input-text" name="setting[<?php echo $k;?>][cost]" value="<?php echo $v['cost'];?>" size="5"/>&nbsp;&nbsp;
				<select name="setting[<?php echo $k;?>][type]">
                   		<option value="money"  <?php if($v['type'] == 'money'){?>selected<?php }?>>现金/美元</option>
                   		<option value="point" <?php if($v['type'] == 'point'){?>selected<?php }?>>积分</option>
               </select>
               
               <span style="cursor:pointer" class="delete">删除</span>
			</td>
		</tr>
		<?php }}?>
	</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend>邀请好友规则设置</legend>
	<table width="100%" class="table_form">
		<tr>
            <td width="80"></td>
			<td>
				<textarea style="margin: 0px; width: 652px;" name="info[friend_detail_desc]" id="info[friend_detail_desc]"><?php echo stripslashes($setting['info']['friend_detail_desc']); ?></textarea>
                 <?php echo $form::editor("info[friend_detail_desc]", "full");?>
			</td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="button">
</form>
</div>
</div>
<script type="text/javascript">
<!--
$(document).ready(function(){
	var num = '<?php echo $count - 1;?>';
	$("#add_task").click(function(){
		num++;
		var html = '';
		html += '<tr>';
		html += '<td width="80"></td>';
		html += '<td>';
		html += '当用户累积完成	<input type="text" class="input-text" name="setting['+num+'][num]" value="" size="5"/> 笔订单（含试用和返利），奖励';
		html += '<input type="text" class="input-text" name="setting['+num+'][cost]" value="" size="5"/>&nbsp;&nbsp;';
		html += '<select name="setting['+num+'][type]">';
		html += '<option value="money" selected>现金/美元</option>';
		html += '<option value="point">积分</option>';
		html += '</select>    <span style="cursor:pointer" class="delete">删除</span>';
		html += '</td>';
		html += '</tr>';
		if(num > 3){
			alert('建议：累积任务设置不操过4条，操过4条后前台将无法正常展示！');
		}
		$("#accumulate").append(html);
	});
    
    $(".delete").live('click',function(){
        $(this).parents('tr').remove();
    });

    var nums = '<?php echo $counts - 1;?>';
    $("#add_friend").click(function(){
		nums++;
		var html = '';
		html += '<tr>';
		html += '<td width="80"></td>';
		html += '<td>';
		html += ''+$("#accumulates").find('tr').size()+'级推荐好友,奖励	';
		html += '<input type="text" class="input-text" name="friend['+$("#accumulates").find('tr').size()+'][cost]" value="" size="5"/>&nbsp;&nbsp;';
		html += '<select name="friend['+$("#accumulates").find('tr').size()+'][type]">';
		html += '<option value="money" selected>现金</option>';
		html += '<option value="point">积分</option>';
		html += '</select>  ';
		html +='  <span style="cursor:pointer" class="deletes">删除</span>';
		html += '</td>';
		html += '</tr>';
		if($("#accumulates").find('tr').size() > 4){
			alert('推荐好友设置级别不能超过4级');
			return false;
		}
		$("#accumulates").append(html);
	});
    
    $(".deletes").live('click',function(){
        
        $(".deletes").parents('tr:last').remove();

    });
});
//-->
</script>
</body>
</html>