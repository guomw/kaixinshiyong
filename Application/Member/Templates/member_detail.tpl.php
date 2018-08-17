<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>

<div class="pad-lr-10">
<form name="searchform" action="<?php echo __APP__;?>" method="get" >
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<input type="hidden" value="<?php echo $userid; ?>" name="userid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
    		<td>
    			<div class="explain-col">
    				<?php echo L('type');?>：
    				<select name="search[type]">
    				    <option value="-99" <?php if($type == -99){?>selected<?php }?>>全部</option>
    				    <option value="1" <?php if($type == 1){?>selected<?php }?>>提现</option>
    				    <option value="2" <?php if($type == 2){?>selected<?php }?>>充值</option>
    				    <option value="3" <?php if($type == 3){?>selected<?php }?>>订单结算</option>
    				    <option value="4" <?php if($type == 4){?>selected<?php }?>>活动结算</option>
    				    <option value="5" <?php if($type == 5){?>selected<?php }?>>vip费用</option>
    				</select>
    				<?php echo L('time')?>:  <?php echo $form::date('search[start_time]',$start_time,'')?> <?php echo L('to')?>   <?php echo $form::date('search[end_time]', $end_time,'')?> 
    				<input type="submit" value="<?php echo L('search')?>" class="button" name="searchsubmit">
    		  </div>
    		</td>
		</tr>
    </tbody>
</table>
</form>

<div class="btn text-l">
    <span class="font-fixh green">
            账户余额： <span class="font-fixh"><?php echo $money;?></span>&nbsp;&nbsp;
            提现中金额：<span class="font-fixh"><?php if($deposite){echo $deposite;}else{echo 0;}?></span>&nbsp;&nbsp;
            累积成功提现：<span class="font-fixh"><?php if($success_deposite){echo $success_deposite;}else{echo 0;}?></span>&nbsp;&nbsp;
            历史总收入：<span class="font-fixh"><?php if($money_total){echo $money_total;}else{echo 0;}?></span>&nbsp;&nbsp;
            历史总支出：<span class="font-fixh"><?php if($expend_money_total){echo $expend_money_total;}else{echo 0;}?></span>&nbsp;&nbsp;
            异常资金：<span class="font-fixh"><?php echo $anomaly;?></span>&nbsp;&nbsp;
    </span>
</div>
<br />
<div class="explain-col">  
    <p> 注意事项</p>
    <p>1.账户余额是会员目前的帐号余额（不包含提现中的）</p>
    <p>2.提现中的金额是会员正在申请提现中的金额</p>
    <p>3.历史总收入，包括用户充值，保证金退还，后台充值等，</p>
    <p>4.历史总支出 包括所有支出，如购买vip，缴纳保证金。</p>
    <p>5.异常资金是统计会员的（历史总收入 - 历史总支出 是否等于= 账户余额）。</p>
    <p>6.如果发现有异常资金请仔细检查账户明细，判断是否是误判。</p>
</div>
<br />
<form name="myform" action="<?php echo U('detail_delete');?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
			<th align="left"><?php echo 'Id';?></th>
			<th align="left"><?php echo '昵称';?></th>
			<th align="left"><?php echo '来源';?></th>
			<th align="left"><?php echo '收入';?></th>
			<th align="left"><?php echo '支出';?></th>
			<th align="left"><?php echo '摘要';?></th>
			<th align="left"><?php echo '余额';?></th>
			<th align="left"><?php echo '入库时间';?></th>

		</tr>
	</thead>
<tbody>
<?php
	if(is_array($lists)){
	foreach($lists as $k=>$v) {
?>
	<?php if(!empty($modelid)){?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="ids[]"></td>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><?php echo $v['username']?></td>
		<td align="left"><?php echo $v['cause'];?></td>
     <?php if($v['num'] >= 0){
          echo"<td align='left'><span class='font-fixh green'>{$v['num']}</span></td><td align='left'></td>";
        }
        else{
        	echo "<td align='left'></td> 
        	      <td align='left'><span class='font-fixh'>{$v['num']}</span></td>";
        }
        ?>	
       </td>
		<td align="left"><?php if($v['num'] >= "0"){echo "收入";}else{echo "支出";}?></td>	
        <td align="left"><?php echo $v['total_money'] ?></td>
		<td align="left"><?php echo dgmdate($v['dateline']);;?></td>
    </tr>
    <?php }else{?>
     <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="ids[]"></td>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><?php echo $v['username']?></td>
		<td align="left"><?php echo $v['cause'];?></td>
		  <?php if($v['num'] >= 0){
          echo"<td align='left'><span class='font-fixh green'>{$v['num']}</span></td><td align='left'></td>";
        }
        else{
        	echo "<td align='left'></td> 
        	      <td align='left'><span class='font-fixh'>{$v['num']}</span></td>";
        }
        ?>	
        <td align="left"><?php if($v['num'] >= "0"){echo "收入";}else{echo "支出";}?></td>	

		<!--<?php if($v['type'] == 'point'){?>
		<td align="left"><?php echo substr($v['num'],0,-3).'(积分)';?></td>
		<?php }else if($v['type'] == 'exp'){?>
		<td align="left"><?php echo substr($v['num'],0,-3).'(成长值)';?></td>
		<?php }else{?>
		<td align="left"><?php echo $v['num'].'(美元)';?></td>
		<?php }?>-->
		        <td align="left"><?php echo $v['total_money'] ?></td>
   

		<td align="left"><?php echo dgmdate($v['dateline']);;?></td>
    </tr>

    <?php }?>

<?php
	}
}
?>
</tbody>
</table>

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit').L('member')?>《'+name+'》',id:'edit',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='ids[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

function member_infomation(userid) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=Member&c=Member&a=memberinfo&userid='+userid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}

function aword(obj ,name){
	window.top.art.dialog({id:'aword'}).close();
	window.top.art.dialog({title:'<?php echo L('aword').L('member')?>《'+name+'》',id:'aword',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'aword'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'aword'}).close()});
}
//-->
</script>
</body>
</html>