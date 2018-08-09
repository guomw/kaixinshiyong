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
                <?php echo "任务时间"?>：
                <?php echo $form::date('start_time', $start_time)?>-
                <?php echo $form::date('end_time', $end_time)?> 
                
                <?php //echo $form::select($modellist, $modelid, 'name="modelid"', L('member_model'))?>
                <?php echo $form::select($grouplist, $groupid, 'name="groupid"', L('member_group'))?>               
                <select name="type">
                    <option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>><?php echo L('username')?></option>
                    <option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>><?php echo "会员Id"?></option>
                  
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
            <th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('userid[]');"></th>
            <th align="left"></th>
            <th align="left">会员Id</th>
            <th align="left"><?php echo L('username')?></th>
            <th align="left"><?php echo L('member_group')?></th>
            <th align="left"><?php echo "参与任务名称"?></th>
            <th align="left"><?php echo "任务开始时间"?></th>
            <th align="left"><?php echo "任务结束时间"?></th>           
            <th align="left"><?php echo L('operation')?></th>
        </tr>
    </thead>
<tbody>
<?php
    if(is_array($memberlist)){
    foreach($memberlist as $k=>$v) {
?>
    <tr>
        <td align="left"><input type="checkbox" value="<?php echo $v['userid']?>" name="userid[]"></td>
        <td align="left"><?php if($v['islock']) {?><img title="<?php echo L('lock')?>" src="<?php echo IMG_PATH?>icon_padlock.gif"><?php }?></td>
        <td align="left"><?php echo $v['userid']?></td>
        <td align="left"><?php echo $modellist[$v['modelid']]?></td>
        <td align="left"><img src="<?php echo getavatar($v['userid'])?>" height=18 width="18" onerror="this.src='<?php echo IMG_PATH?>member/nophoto.gif'"><?php if($v['vip']) {?><img title="<?php echo L('vip')?>" src="<?php echo IMG_PATH?>vip.gif"><?php }?><?php echo $v['username']?><a href="javascript:;" onclick="javascript:member_infomation(<?php echo $v['userid'];?>)"><?php echo $member_model[$v['modelid']]['name']?><img src="<?php echo IMG_PATH;?>detail.png"></a></td>
        <td align="left"><?php echo $grouplist[$v['groupid']] ?></td>
        <td align="left"><?php echo $v['regip']?></td>
        <td align="left"><?php echo dgmdate($v['lastdate'], 1);?></td>
        <td align="left">
            <a href="#">[查看]</a>
            <a href="<?php echo U('edit', array('userid' => $v['userid'])) ?>" onclick="javascript:edit(this, '<?php echo $v['username']?>');return false;">[<?php echo L('edit')?>]</a>
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
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo U('lock'); ?>'" value="<?php echo L('lock')?>"/>
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo U('unlock'); ?>'" value="<?php echo L('unlock')?>"/>
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
    $("input[name='userid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
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
//-->
</script>
</body>
</html>