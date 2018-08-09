<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad-10">
    <form name="search" action="<?php echo U('dialog'); ?>" method="get" >
        <input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
        <input type="hidden" value="<?php echo CONTROLLER_NAME ?>" name="c">
        <input type="hidden" value="<?php echo ACTION_NAME ?>" name="a">
        <input type="hidden" value="<?php echo $modelid;?>" name="modelid">
        <table width="100%" cellspacing="0" class="search-form">
            <tbody>
            <tr>
                <td align="center">
                    <div class="explain-col">
                        <select name="field">
                            <option value='1' <?php if($_GET['field']=='username') echo 'selected';?>>商家账号</option>
                            <option value='2' <?php if($_GET['field']=='userid') echo 'selected';?>>商家ID</option>
                        </select>
                        <?php $form::select_category($catid,'name="catid"', L('no_limit_category'), $modelid,0,1);?>
                        <input name="keywords" type="text" value="<?php echo stripslashes($_GET['keywords'])?>" style="width:330px;" class="input-text" />
                        <input type="submit" name="search" class="button" value="<?php echo L('search');?>" />
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
    <div class="table-list">
        <table width="100%" cellspacing="0">
            <thead>
            <tr>
                <th align="left">ID</th>
                <th align="left">店铺名称</th>
                <th align="left">店铺图标</th>
                <th align="left">所在地址</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(is_array($infos)){
                foreach($infos as $k=>$v) {
                    ?>
                    <tr onclick="select_list(this, '<?php echo $v['store_name'] ?>', '<?php echo $v['userid'] ?>');" class="item" style="cursor: pointer;">
                        <td align="left"><?php echo $v['userid'] ?></td>
                        <td align="left"><?php echo $v['store_name'] ?></td>
                        <td align="left"><img src="<?php echo $v['store_logo']?>" alt="<?php echo $v['store_name'] ?>" onerror="this.src='<?php echo __ROOT__?>/uploadfile/avatar/seller_logo.jpg'" width="100" height="100"/></td>
                        <td align="left"><?php echo $v['store_address'];?></td>
                    </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
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
</SCRIPT>
</body>
</html>