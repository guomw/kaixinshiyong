<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form action="<?php echo __APP__;?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<input type="hidden" value="<?php echo $userid;?>" name="userid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
        <tr>
            <td>
                <div class="explain-col">
                               查询时间：
                <?php echo $form::date('start_time', $start_time);?>-
                <?php echo $form::date('end_time', $end_time);?>           
                <input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
                </div>
            </td>
        </tr>
    </tbody>
</table>
</form> 

<form name="myform" action="<?php echo U('delete');?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th align="left">日期</th>
            <th align="left">业务id</th>
            <th align="left">业务员</th>
            <th align="left">新增商家</th>
            <th align="left">新增返利活动</th>
            <th align="left">新增试用活动</th>
            <th align="left">试用活动担保金</th>
            <th align="left">返利活动担保金</th>
            <th align="left">试用活动退还</th>
            <th align="left">返利活动退还</th>
            <th align="left">商家升级vip数量</th>
            <th align="left">商家升级费用总计</th>
            <th align="left">操作</th>
        </tr>
    </thead>
<tbody>
<?php 
if($starttime && $endtime){
    foreach($history_lists as $k=>$v) {
?>
    <tr>
        <td align="left"><?php echo $k;?></td>
        <td align="left"><?php if(empty($v)){echo '-';}else{echo $v['userid'];}?></td>
        <td align="left"><?php if(empty($v)){echo '-';}else{echo $v['username'];}?></td>
        <td align="left"><?php if(empty($v)){echo '0';}else{echo $v['merchant_num'];}?>名商家</td>
        <td align="left"><?php if(empty($v)){echo '0';}else{echo $v['rebate_num'];}?>美元</td>
        <td align="left"><?php if(empty($v)){echo '-';}else{echo $v['trial_num'];}?></td>
        <td align="left"><?php if(empty($v)){echo '-';}else{echo $v['trial_deposite'];}?></td>
        <td align="left"><?php if(empty($v)){echo '-';}else{echo $v['rebate_deposite'];}?></td>
        <td align="left"><?php if(empty($v)){echo '-';}else{echo $v['trial_back'];}?></td>
        <td align="left"><?php if(empty($v)){echo '-';}else{echo $v['rebate_back'];}?></td>
        <td align="left"><?php if(empty($v)){echo '-';}else{echo $v['merchant_vipnum'];}?></td>
        <td align="left"><?php if(empty($v)){echo '-';}else{echo $v['merchant_money'];}?></td>
        <td align="left">
            <a href="<?php echo U('Admin/Statistics/search',array('time'=>$k,'userid'=>$userid));?>" >[查询]</a>
        </td>
    </tr>
    <?php 
    } }?>
    <tr style="color:green">
        <td align="left">总计</td>
        <td align="left"></td>
        <td align="left"></td>
        <td align="left"><?php echo $merchant_num;?>名商家</td>
        <td align="left"><?php echo $rebate_num;?>美元</td>
        <td align="left"><?php echo $trial_num;?></td>
        <td align="left"><?php echo $trial_deposite_num;?></td>
        <td align="left"><?php echo $rebate_deposite_num;?></td>
        <td align="left"><?php echo $trial_back_num;?></td>
        <td align="left"><?php echo $rebate_back_num;?></td>
        <td align="left"><?php echo $vip_num;?></td>
        <td align="left"><?php echo $money;?></td>
        <td align="left"></td>
    </tr>
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
        window.top.art.dialog({content:'请勾选你要删除的记录',lock:true,width:'200',height:'50',time:1.5},function(){});
        return false;
    } else {
        myform.submit();
    }
}
function member_infomation(userid) {
    window.top.art.dialog({id:'modelinfo'}).close();
    window.top.art.dialog({title:'处理申诉',id:'modelinfo',iframe:'?m=Order&c=Appeal&a=appeal_do&appeal_id='+userid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}
//-->
</script>
</body>
</html>