<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad-10">
    <form  name="myform" id="myform" action="<?php echo U('unreal_order'); ?>" method="post" >
        <input type="hidden" value="<?php echo $goods_id;?>" name="goods_id" id="goods_id">
        <input type="hidden" value="" name="ids" id="ids">
       

    <div class="table-list">
        <table width="100%" cellspacing="0">
            <thead>
            <tr>
                 <th align="left"></th>
                <th align="left">ID</th>
                <th align="left">昵称</th>
                <th align="left">头像</th>
                <th align="left">注册时间</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(is_array($infos)){
                foreach($infos as $k=>$v) {
                    ?>
                    <!-- <tr onclick="select_list(this, '<?php echo $v['store_name'] ?>', '<?php echo $v['userid'] ?>');" class="item" style="cursor: pointer;"> -->
                        <td align="left"><input type="checkbox" value="<?php echo $v['userid']; ?>" name="ids[]" class="uids js_uid" >
</td>
                        <td align="left"><?php echo $v['userid'] ?></td>
                        <td align="left"><?php echo $v['nickname'] ?></td>
                        <td align="left"><img src="<?php echo $v['avatar']?>" alt="<?php echo $v['avatar'] ?>" onerror="this.src='<?php echo __ROOT__?>/uploadfile/avatar/seller_logo.jpg'" width="50" height="50"/></td>
                        <td align="left"><?php echo dgmdate($v['dateline'], 'Y-m-d');?></td>
                    </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
     </form>
 <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
 <div class="btn">
<input type="checkbox" class="che change_all" onclick="selectall('ids[]');">


</div>
        <div id="pages"><?php echo $pages;?></div>
    </div>
</div>
<style type="text/css">
    .line_ff9966,.line_ff9966:hover td{
        background-color:#FF9966;
    }
    .line_fbffe4,.line_fbffe4:hover td {
        background-color:#fbffe4;
    }
</style>
<SCRIPT type="text/javascript">
var result_id = "<?php echo I('result_id', 'company_id') ?>";
function select_list(obj,title,id) {
    $(".item").removeClass('line_ff9966');
    $(obj).addClass("line_ff9966");
    window.parent.$('#' + result_id + '_text').attr('value', title);
    window.parent.$('#' + result_id).attr("value", id);
}
 
function selectall(name) {
  if ($(".change_all").prop("checked")) {
        $("input[name='"+name+"']").attr('checked', 'checked').prop('checked', 'checked');
        var ids='';
        $("input[name='ids[]']:checked").each(function(i, n){
                    ids += $(n).val() + ',';
         });

        $("#ids").val(ids);
  } else {
        $("input[name='"+name+"']").removeAttr('checked');
  }
}

$(".js_uid").click(function(){
     var ids='';
        $("input[name='ids[]']:checked").each(function(i, n){
                    ids += $(n).val() + ',';
         });

        $("#ids").val(ids);
});




/*$(".aui_state_highlight").click(function(){
     var ids='';
   $("input[name='ids[]']:checked").each(function(i, n){
            ids += $(n).val() + ',';
        });
   var goods_id = '';
   goods_id = $("#goods_id").val();

   $.post("<?php echo U('Member/Unreal/unreal_order') ?>",{ids:ids,goods_id:goods_id}, function(ret) {
        if (ret.status==1) {
          alert(ret.info);
        }else{
          alert(ret.info);

        }
     });

});
*/</SCRIPT>
</body>
</html>