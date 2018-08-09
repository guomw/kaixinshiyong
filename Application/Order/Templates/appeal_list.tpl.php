<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<style type="text/css">
    .explain-col{
     margin-top: 20px;
    }

</style>
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
                <?php echo "申诉时间"?>：
                <?php echo $form::date('start_time', $start_time)?>-
                <?php echo $form::date('end_time', $end_time)?>
                <?php //echo// $form::select($grouplist, $groupid, 'name="groupid"', L('member_group'))?>
               申诉状态： <select name="appeal_status">
                     <?php foreach ($appealstatus as $k => $v): ?>
                          <option <?php if($_GET['appeal_status'] == $k){ ?> selected <?php } ?>value="<?php echo $k; ?>"><?php echo $v ;?></option>
                     <?php endforeach ?>
                </select>

                 申诉类型：<select name="appeal_type">
                     <option value="-1">全部</option>
                     <?php foreach ($appealtype as $k => $v): ?>
                          <option <?php if($_GET['appeal_type'] == $k){ ?> selected <?php } ?> value="<?php echo $k; ?>"><?php echo $v ;?></option>
                     <?php endforeach ?>
                </select>

                <select name="type">
                    <option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>><?php echo "订单号"?></option>
                    <option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>><?php echo "申诉人"?></option>
                     <option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>><?php echo "商品名称"?></option>
                </select>               
                <input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
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
            <th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
            <th align="left">id</th>
            <th align="left">商品名称</th>
            <th align="left">申诉人</th>
            <th align="left">申诉商家</th>
            <th align="left">订单号</th>
            <th align="left">申诉类型</th>         
            <th align="left">申诉状态</th>
            <th align="left">申诉时间</th>
            <th align="left">操作</th>
        </tr>
    </thead>
<tbody>
<?php
    if(is_array($appeals)){
    foreach($appeals as $k=>$v) {
?>
    <tr>
        <td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="ids[]"></td>
        <td align="left"><?php echo $v['id']?></td>
        <td align="left"><?php echo $v['goods']['title']?></td>
        <td align="left"><?php echo $v['buyer']['nickname']?></td>
        <td align="left"><?php echo $v['seller']['nickname']?></td>
        <td align="left"><?php echo $v['order_sn'] ?></td>
        <td align="left"><?php echo $appealtype[$v['appeal_type']]?></td>
        <td align="left"><?php echo $appealstatus[$v['appeal_status']];?></td>
        <td align="left"><?php echo dgmdate($v['buyer_time']);?></td>
        <td align="left">
            <?php if($v['appeal_status']=='1'){ ?>
            <a href="javascript:member_infomation(<?php echo $v['id']?>)">[处理]</a>
            <?php } ?>
            <a href="<?php echo U('delete', array('ids[]' => $v['id'])) ?>" onclick="return confirm('删除后数据将无法恢复！你确定要删除吗？')">[删除]</a>
        </td>
    </tr>
<?php
    }
}
?>
</tbody>
</table>
<div class="btn">
<label for="check_box">全选/取消</label> <input type="submit" class="button" name="dosubmit" value="删除" onclick="return confirm('删除后数据将无法恢复！你确定要删除吗？')"/>
</div>
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