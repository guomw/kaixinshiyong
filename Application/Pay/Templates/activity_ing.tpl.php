<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'Admin');
?>
<!--今日进行中的活动 -->
<div class="pad-lr-10">
<form name="myform" id="myform" action="" method="post" >
<div class="table-list">
    <table width="100%">
		<thead>
		<tr>
            <!-- <th width="37">排序</th> -->
            <th width="40">ID</th>
			<th  width="100">返利进行中</th>
			<th width="100">试用进行中</th>
 			<th width="100">日赚任务进行中</th>
			<th width="100">闪电佣金进行中</th>
            <th width="118">统计时间</th>
		</tr>
        </thead>
		<tbody>

		<?php foreach($lists as $r) : ; ?>
        <tr>
			
			<td align='center' ><?php echo $r['id'];?></td>
			<td align='center' ><?php echo $r['day_rebate_Start_mum'];?></td>
			<td align='center'><?php echo $r['day_trial_Start_mum'] ?></td>
 			<td align='center'><?php echo $r['day_task_Start_mum'] ?></td>
 			<td align='center'><?php echo $r['day_commission_Start_mum'] ?></td>
			<td align='center'><?php echo dgmdate($r['time'], 'Y-m-d')?></td>
		</tr>
		</tbody>
		<?php endforeach ?>
	</table>
   
	</div>
</div>
</form>
</body>
</html>