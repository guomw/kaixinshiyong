<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<form action="<?php echo U(ACTION_NAME) ?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_base">绑定亚马逊账号设置</li>
        </ul>
        <div id="div_setting_base" class="contentList pad-10">
            <table width="100%" class="table_form" id="goods_albums">
                <tr>
                    <th>绑定亚马逊帐号数量</th>
                    <td class="y-bg">
                        <input type="text" class="input-text" name="setting[bind_tb_nums]" value="<?php if($setting['bind_tb_nums']){echo $setting['bind_tb_nums'];}else{echo 10;} ?>" size="3" maxlength="3"/>个  
                        &nbsp;&nbsp;提示：绑定亚马逊帐号的数量最多限制个数(默认：10个)
                    </td>
                </tr>
                <tr>
                    <th>是否需要上传亚马逊截图</th>
                    <td class="y-bg">
                        <label><input type="radio" name="setting[bind_tb_img]" class="input-radio" <?php echo $setting[bind_tb_img] ==1 ? 'checked' : '' ?> value='1'>是</label>&nbsp;
                        <label><input type="radio" name="setting[bind_tb_img]" class="input-radio" <?php echo $setting[bind_tb_img] ==0 ? 'checked' : '' ?> value='0'>否</label>&nbsp;
                        &nbsp;&nbsp;提示：绑定亚马逊帐号的数量最多限制个数(默认：10个)
                    </td>
                </tr>
            </table>
        </div>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
</div>
</div>
</form>
</body>
</html>