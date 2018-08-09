<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<!-- <div class="pad-lr-10">
    <div class="explain-col">
温馨提示：自定义任务需人工审核
</div> -->
<div class="pad_10">
<form name="myform" action="<?php echo U('delete');?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
         
            <th align="left"><?php echo L('任务名称')?></th>
            <th align="left"><?php echo L('任务奖励');?></th>
            <th align="left"><?php echo L('奖励类型');?></th>
            <th align="left"><?php echo L('任务地址');?></th>
            <th align="left" style="width:300px;"><?php echo L('任务介绍');?></th>
            <th align="left"><?php echo L('是否启用');?></th>

            <th align="left"><?php echo L('操作')?></th>
        </tr>
    </thead>
<tbody>
<?php
    if(is_array($task)){
    foreach($task as $k=>$v) {
?>
    <tr style="height:60px;">
        <?php if($v['id']>2){ ?>
            <td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="id[]"></td>
        <?php }else{ ?>
            <td align="left"><input type="checkbox" disabled="true"></td>
        <?php } ?>
       
        <td align="left"><?php echo $v['task_name']?></td>
        <td align="left"><?php echo $v['task_reward'] ?></td>
       <td align="left"><?php 
       if($v['task_type'] == 'point'){
        echo "积分";
       }elseif($v['task_type']== 'exp'){
        echo "经验值";
       }elseif($v['task_type'] == 'money'){
        echo "金额";
       }
       ?></td>
        <td align="left"><?php echo $v['url'];?></td>
        <td align="left"><?php echo str_cut($v['task_desc'],50);?></td>
        <td align="left"><?php if($v['task_status'] == 1){ echo "启用";}else{
            echo "禁用";
        }?></td>
       
        <td align="left">
            <a href="<?php echo U('task_edit', array('id' => $v['id'])) ?>" onclick="javascript:edit(this, '<?php echo $v['task_name']?>');return false;">[<?php echo L('修改')?>]</a>
           
        </td>
    </tr>
<?php
    }
}
?>
</tbody>
</table>
<!-- <div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
</div> -->

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(obj, name) {
    window.top.art.dialog({id:'edit'}).close();
    window.top.art.dialog({
        title:'<?php echo L('修改任务设置')?>【'+name+'】',
        id:'edit',
        iframe:obj.href,
        width:'700',
        height:'500'
        }, 
        function(){
            var d = window.top.art.dialog({id:'edit'}).data.iframe;
            d.document.getElementById('dosubmit').click();
            return false;
        }, 
        function(){
            window.top.art.dialog({id:'edit'}).close()}
        );
}

function checkuid() {
    var ids='';
    $("input[name='id[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        window.top.art.dialog({content:'<?php echo L('请选择要删除的记录');?>',lock:true,width:'200',height:'50',time:1.5},function(){});
        return false;
    } else {
        myform.submit();
    }
}

/*function member_infomation(obj, name) {
    window.top.art.dialog({id:'modelinfo'}).close();
    window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}*/
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