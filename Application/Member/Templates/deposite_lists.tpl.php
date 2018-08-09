<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<!-- 后台待审核提现模板 -->
<form action="<?php echo __APP__;?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
        <tr>
        <td>
        <div class="explain-col">  
        	<input type="hidden" name="status" value="<?php echo $status;?>" />
        		提现方式：
        		<select name="type">
        			<option value="-99" <?php if ($type == -99){?>selected<?php }?>>全部</option>
        			<option value="1"  <?php if ($type == 1){?>selected<?php }?>>提现到银行</option>
        			<option value="2"  <?php if ($type == 2){?>selected<?php }?>>提现到支付宝</option>
        		</select>             
                申请时间：
                <?php echo $form::date('start_time', $start_time)?>-
                <?php echo $form::date('end_time', $end_time)?> 
                <?php echo $form::select($grouplist, $groupid, 'name="groupid"', L('member_group'))?>               
                <select name="p_type">
                	<option value='0' <?php if(isset($_GET['p_type']) && $_GET['p_type']==1){?>selected<?php }?>><?php echo '提现姓名';?></option>
                    <option value='1' <?php if(isset($_GET['p_type']) && $_GET['p_type']==1){?>selected<?php }?>><?php echo '昵称';?></option>
                    <option value='2' <?php if(isset($_GET['p_type']) && $_GET['p_type']==2){?>selected<?php }?>><?php echo "会员Id"?></option>
                </select>               
                <input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
                <input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
                <div style="color:red;">
                    <p> 注意事项</p>
                    <p>1.审核提现之前 请检查会员账户明细 是否有异常。</p>
                    <p>2.提现审核通过之后，请记录需要收款人的信息 再次检查是否有异常。</p>
                    <p>3.提现审核要仔细对照，仔细检查，避免平台资金损失。</p>
                    <p>4.建议设立专门的财务人员，负责此项提现审核工作。</p>
                    <p>5.最终支付给会员的金额是扣除手续费的实际应付金额。</p>
                </div>
    </div>
        </td>
        </tr>
    </tbody>
</table>
</form>

<form name="myform" action="<?php echo U('delete');?>" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th align="left" width="3%">ID</th>
            <th align="left" width="3%">userid</th>
            <th align="left" width="4%">会员类型</th>
            <th align="left" width="5%">提现类型</th>
            <th align="left" width="5%">提现人姓名</th>
            <th align="left" width="4%">提现总额</th>
            <th align="left" width="4%">手续费</th>
            <th align="left" width="5%">实际应支付</th>
            <th align="left" width="7%">提现方式</th>
            <th align="left" width="9%">提现账号</th>
            <th align="left" width="8%">申请时间</th>
            <th align="left" width="5%">状态</th>
            <th align="left" width="8%">审核时间</th>
            <th align="left" width="5%">交易成功单号</th>
            <th align="left" width="10%">审核失败原因</th>
            <th align="left">操作</th>
        </tr>
    </thead>
<tbody id="test">
<?php
    if(is_array($lists)){
    foreach($lists as $k=>$v) {
?>
    <tr>
       <td align="left"><?php echo $v['cashid']?></td>
        <td align="left"><a href="<?php echo U('Member/Member/memberinfo',array('userid'=>$v['userid']));?>"><?php echo $v['userid']?></a></td>
        <td align="left"><?php if($v['modelid'] == 1) {?>会员<?php }else{?>商家<?php }?></td>
        <td align="left">
        <?php if($v['paypal'] == 1):?>
        普通提现
        <?php elseif($v['paypal'] == 2): ?>
         <img src="/static/images/kuai.png" />快速提现
        <?php elseif($v['paypal'] == 2): ?>
         <img width="16px" src="/static/images/weixin.png" />微信实时提现
        <?php endif ?>    
        </td>
        <td align="left"><?php echo $v['name'];?></td>
        <td align="left"><?php echo $v['totalmoney'] + $v['fee'];?></td>
        <td align="left"><?php echo $v['fee'];?></td>
        <td align="left" style="color:green"><?php echo $v['totalmoney'];?></td>
        <td align="left">
        <?php if ($v['type'] == 1): ?>
              <img src="/static/images/bank.png" /><?php echo $v['bank'];?>
        <?php elseif($v['type'] == 2): ?>
              <img src="/static/images/alipay.png" /> 支付宝 
        <?php elseif($v['type'] == 3): ?>
              <img src="/static/images/weixin.png" /> 微信      
        <?php endif ?>
        </td>
        <td align="left"><?php echo $v['cash_alipay_username'];?></td>
        <td align="left"><?php echo dgmdate($v['inputtime'], 'Y/m/d H:i');?></td>
        <td align="left"><?php if($v['status'] == 0) {echo "<font color='red'>未审核</font>";}elseif ($v['status'] == 1) {echo "审核成功";}else{echo "审核失败";}?></td>
        <td align="left"><?php echo dgmdate($v['check_time'], 'Y/m/d H:i');?></td>
        <td align="left"><?php echo $v['success_order'];?></td>
        <td align="left">
        <?php echo $v['cause'];?>
        <?php if($v['status'] == -2): ?>
         <?php echo $v['err_cause'];?>
        <?php endif; ?>
        </td>
        <td align="left">
         	<?php if($v['status'] == 0): ?>
                <a href="<?php echo U('check', array('id' => $v['cashid'])) ?>" onclick="javascript:check(this,'<?php echo $v['nickname']?>');return false;">[通过]</a> |
                <a href="<?php echo U('uncheck', array('id' => $v['cashid'])) ?>" onclick="javascript:uncheck(this,'<?php echo $v['nickname']?>');return false;">[不通过]</a>
            <?php elseif($v['status'] == 1): ?>
            <?php elseif($v['status'] == -2): ?>
                <a href="<?php echo U('check_weixin_deposite',array('id'=>$v['cashid'])) ?>"><img width="16px" src="/static/images/weixin.png" />[重新微信支付]</a> |
            <?php endif; ?>
                <a href="<?php echo U('Member/Member/detail', array('userid' => $v['userid'],'modelid'=>2)) ?>">[<?php echo '账户明细';?>]</a>|
                <a href="<?php echo U('deposite_info',array('id'=>$v['cashid']));?>">[详情]</a> 
        </td>
    </tr>
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
$(function(){
	$("#test>tr").each(function(){
		$(this).children('td').eq(-1).find('#msg').blur(function(){
			var _this = $(this).val();
			var id = $(this).parents().find('#cashid').val();
			if(_this != ''){
				$.ajax({
					url:'<?php echo U('Member/Deposite/message');?>',
					type:'post',
					dataType:'json',
					data:{'msg':_this,'id':id},
					success:function(data){
						if(data.status == 1){
							alert(data.info);
						}
					}
				});
			}else{
				alert('请填写备注信息');
			}
		});
	});
});
function edit(obj, name) {
    window.top.art.dialog({id:'edit'}).close();
    window.top.art.dialog({title:'<?php echo L('edit').L('member')?>《'+name+'》',id:'edit',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function member_infomation(userid) {
    window.top.art.dialog({id:'modelinfo'}).close();
    window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=Member&c=Member&a=memberinfo&userid='+userid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}

function uncheck(obj, name){
	window.top.art.dialog({
        id:'uncheck'
    }).close();
	window.top.art.dialog({
        title:'审核提现',
        id:'uncheck',
        iframe:obj.href,
        width:'400',
        height:'150'}, 
        function(){
            var d = window.top.art.dialog({id:'uncheck'}).data.iframe;
            d.document.getElementById('dosubmit').click();
            return false;
        }, function(){
            window.top.art.dialog({
                id:'uncheck'
            }).close()
        });
}

function check(obj, name){
    window.top.art.dialog({
        id:'check'
    }).close();
    window.top.art.dialog({
        title:'审核提现',
        id:'check',
        iframe:obj.href,
        width:'400',
        height:'150'
       },
        function(){
            var d = window.top.art.dialog({id:'check'}).data.iframe;
            d.document.getElementById('dosubmit').click();
            return false;
        },function(){
            window.top.art.dialog({
                id:'check'
            }).close()
        });
}
</script>
</body>
</html>