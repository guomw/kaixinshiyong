<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<form action="<?php echo U('update');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_upload"><?php echo L('图片设置')?></li>
        </ul>
        <div id="div_setting_upload" class="contentList pad-10">
            <table width="100%"  class="table_form">
                <tr>
                    <th width="130" valign="top"><?php echo L('upload_maxsize')?></th>
                    <td class="y-bg"><input type="text" class="input-text" name="setting[upload_maxsize]" id="upload_maxsize"  value="<?php echo $setting['upload_maxsize'] ? $setting['upload_maxsize'] : '2000' ?>"/> KB </td>
                </tr>
                <tr>
                    <th width="130" valign="top"><?php echo L('att_allow_ext')?></th>
                    <td class="y-bg"><input type="text" class="input-text" name="setting[upload_allowext]" id="upload_allowext"  value="<?php echo $setting['upload_allowext']?>"/></td>
                </tr>  
                <tr>
                    <th><?php echo L('att_gb_check')?></th>
                    <td class="y-bg"><?php echo $this->check_gd()?></td>
                <tr>
                    <th><?php echo L('att_watermark_enable')?></th>
                    <td class="y-bg"><input class="radio_style" name="setting[watermark_enable]" value="1" <?php echo $setting['watermark_enable']==1 ? 'checked="checked"' : ''?> type="radio"> <?php echo L('att_watermark_open')?>&nbsp;&nbsp;&nbsp;&nbsp;<input class="radio_style" name="setting[watermark_enable]" value="0" <?php echo $setting['watermark_enable']==0 ? 'checked="checked"' : ''?> type="radio"> <?php echo L('att_watermark_close')?>
                    </td>
                </tr>

                <tr>
                    <th><?php echo L('att_watermark_condition')?></th>
                    <td class="y-bg"><?php echo L('att_watermark_minwidth');?><input type="text" class="input-text" name="setting[watermark_minwidth]" id="watermark_minwidth"  value="<?php echo $setting['watermark_minwidth'] ? $setting['watermark_minwidth'] : '300' ?>" /> X <?php echo L('att_watermark_minheight')?><input type="text" class="input-text" name="setting[watermark_minheight]" id="watermark_minheight"  value="<?php echo $setting['watermark_minheight'] ? $setting['watermark_minheight'] : '300' ?>" /> PX</td>
                </tr>
                <tr>
                    <th width="130" valign="top"><?php echo L('att_watermark_img')?></th>
                    <td class="y-bg"><input type="text" name="setting[watermark_img]" id="watermark_img" value="<?php echo $setting['watermark_img'] ? $setting['watermark_img'] : 'mark.gif' ?>"/><?php echo L('att_watermark_img_desc')?></td>
                </tr>

                <tr>
                    <th width="130" valign="top"><?php echo L('att_watermark_pct')?></th>
                    <td class="y-bg"><input type="text" class="input-text" name="setting[watermark_pct]" id="watermark_pct"  value="<?php echo $setting['watermark_pct'] ? $setting['watermark_pct'] : '100' ?>" />  <?php echo L('att_watermark_pct_desc')?></td>
                </tr>
                <tr>
                    <th width="130" valign="top"><?php echo L('att_watermark_quality')?></th>
                    <td class="y-bg"><input type="text" class="input-text" name="setting[watermark_quality]" id="watermark_quality"  value="<?php echo $setting['watermark_quality'] ? $setting['watermark_quality'] : '80' ?>" /> <?php echo L('att_watermark_quality_desc')?></td>
                </tr>
                <tr>
                    <th width="130" valign="top"><?php echo L('att_watermark_pos')?></th>
                    <td>
                    <table width="100%" class="radio-label">
                        <tr>
                            <td rowspan="3"><input class="radio_style" name="setting[watermark_pos]" value="10" type="radio" <?php echo ($setting['watermark_pos']==10) ? 'checked':''?>> <?php echo L('att_watermark_pos_10')?></td>
                            <td><input class="radio_style" name="setting[watermark_pos]" value="1" type="radio" <?php echo ($setting['watermark_pos']==1) ? 'checked':''?>> <?php echo L('att_watermark_pos_1')?></td>
                            <td><input class="radio_style" name="setting[watermark_pos]" value="2" type="radio" <?php echo ($setting['watermark_pos']==2) ? 'checked':'' ?>> <?php echo L('att_watermark_pos_2')?></td>
                            <td><input class="radio_style" name="setting[watermark_pos]" value="3" type="radio" <?php echo ($setting['watermark_pos']==3) ? 'checked':''?>> <?php echo L('att_watermark_pos_3')?></td>
                        </tr>
                        <tr>
                            <td><input class="radio_style" name="setting[watermark_pos]" value="4" type="radio" <?php echo ($setting['watermark_pos']==4) ? 'checked':''?>> <?php echo L('att_watermark_pos_4')?></td>
                            <td><input class="radio_style" name="setting[watermark_pos]" value="5" type="radio" <?php echo ($setting['watermark_pos']==5) ? 'checked':''?>> <?php echo L('att_watermark_pos_5')?></td>
                            <td><input class="radio_style" name="setting[watermark_pos]" value="6" type="radio" <?php echo ($setting['watermark_pos']==6) ? 'checked':''?>> <?php echo L('att_watermark_pos_6')?></td>
                        </tr>
                        <tr>
                            <td><input class="radio_style" name="setting[watermark_pos]" value="7" type="radio" <?php echo ($setting['watermark_pos']==7) ? 'checked':''?>> <?php echo L('att_watermark_pos_7')?></td>
                            <td><input class="radio_style" name="setting[watermark_pos]" value="8" type="radio" <?php echo ($setting['watermark_pos']==8) ? 'checked':''?>> <?php echo L('att_watermark_pos_8')?></td>
                            <td><input class="radio_style" name="setting[watermark_pos]" value="9" type="radio" <?php echo ($setting['watermark_pos']==9) ? 'checked':''?>> <?php echo L('att_watermark_pos_9')?></td>
                        </tr>
                    </table>
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