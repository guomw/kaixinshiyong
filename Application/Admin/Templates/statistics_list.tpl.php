<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
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
                                排序方式：
                <select name="sort">
                    <option value="add_merchant" <?php if($_GET['sort'] == 'add_merchant'){?>selected<?php }?>>新增商家</option> 
                    <option value="month_money" <?php if($_GET['sort'] == 'month_money'){?>selected<?php }?>>本月提成金额</option>
                </select>
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
            <th align="left">排名</th>
            <th align="left">业务id</th>
            <th align="left">业务员</th>
            <th align="left">今日新增商家</th>
            <th align="left">本月新增商家</th>
            <th align="left">今日提成金额</th>         
            <th align="left">本月提成金额</th>
            <th align="left">操作</th>
        </tr>
    </thead>
<tbody>
<?php 
if(is_array($attract_lists)){
    foreach ($attract_lists as $k=>$a){
?>
    <tr>
        <td align="left"><?php echo $k+1;?></td>
        <td align="left"><?php echo $a['userid'];?></td>
        <td align="left"><?php echo $a['username'];?></td>
        <td align="left"><?php echo $a['add_merchant'];?>名商家</td>
        <td align="left"><?php echo $a['month_seller'];?>名商家</td>
        <td align="left"><?php echo sprintf("%.2f",$a['today_money']);?>/元</td>
        <td align="left"><?php echo sprintf("%.2f",$a['month_money']);?>/元</td>
        <td>
    <a href="<?php echo U('admin/admin/company_log',array('userid'=>$a['userid'])) ?>">[历史记录]</a>          
  <a href="<?php echo U('Admin/Statistics/detail',array('userid'=>$a['userid']));?>" >[查看详细统计]</a>

        </td>
    </tr>
    <?php }}?>
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