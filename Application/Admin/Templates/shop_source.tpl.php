<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<style type="text/css">
tr{
    display: table-row;
    vertical-align: inherit;
    border-color: inherit;
}
td, th {
    display: table-cell;
    vertical-align: inherit;
    height: 30px;
}

.table-list thead th {
    height: 20px;
    background: #eef3f7;
    border-bottom: 1px solid #d5dfe8;
    font-weight: normal;
    color:#444;
}

.table-list  tr td {
    border:1px solid #eee;
}

</style>
<div class="pad-lr-10">
<form name="myform" action="<?php echo U('delete');?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th  align="center" width="20px"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
            <th width="30px"><?php echo L('id')?></th>
            <th width="50"><?php echo L('来源名称')?></th>
            <th align="center" width="60"><?php echo L('小LOGO');?></th>
            <th align="center" width="60"><?php echo L('大LOGO');?></th>
            <th align="center" style="width:100px;"><?php echo L('介绍');?></th>
            <th align="center" width="80"><?php echo L('添加时间')?></th>
            <th align="center" width="80"><?php echo L('修改时间')?></th>
            <th align="center" width="70"><?php echo L('操作')?></th>
        </tr>
    </thead>
<tbody>
<?php
    if(is_array($shoplist)){
    foreach($shoplist as $k=>$v) {
?>
    <tr style="height:30px;">
        <?php if($v['id']>0){ ?>
            <td align="center" width="20px"><input type="checkbox" value="<?php echo $v['id']?>" name="id[]"></td>
        <?php }else{ ?>
            <td align="center"><input type="checkbox" disabled="true"></td>
        <?php } ?>
        <td align="center"><?php echo $v['id']?></td>
        <td align="center"><?php echo $v['name']?></td>
        
        <td align="center"><?php if($v['small_logo']){ ?> <img src="<?php echo $v['small_logo']?>" width="40" height="40"/><?php }else{echo '暂无';} ?></td>
        <td align="center"><?php if($v['big_logo']){ ?> <img src="<?php echo $v['big_logo']?>" width="44" height="44"/><?php }else{echo '暂无';} ?></td>
        
        <td align="center"><?php echo str_cut($v['description'],150);?></td>
        <td align="center"><?php echo dgmdate($v['inputtime'])?></td>
        <td align="center"><?php if($v['updatetime']!='0'){echo dgmdate($v['updatetime']);}else{echo '--';}?></td>
        <td align="center">
            <a href="<?php echo U('edit', array('id' => $v['id'])) ?>" onclick="javascript:edit(this, '<?php echo $v['name']?>');return false;">[<?php echo L('修改')?>]</a>
            <?php if($v['id'] > 0){ ?>
            <a href="<?php echo U('delete', array('id[]' => $v['id'])) ?>" onclick="return confirm('<?php echo L('sure_delete')?>')">[<?php echo '删除';?>]</a>
            <?php } ?>
        </td>
    </tr>
<?php
    }
}
?>
</tbody>
</table>
<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
</div>

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(obj, name) {
    window.top.art.dialog({id:'edit'}).close();
    window.top.art.dialog({
        title:'<?php echo L('修改店铺来源设置')?>【'+name+'】',
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