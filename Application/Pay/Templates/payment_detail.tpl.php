<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript">
    <!--
    $(function () {
        $.formValidator.initConfig({
            autotip: true, formid: "myform", onerror: function (msg) {
            }
        });
        $("#name").formValidator({
            onshow: "<?php echo L('input') . L('payment_mode') . L('name')?>",
            onfocus: "<?php echo L('payment_mode') . L('name') . L('empty')?>"
        }).inputValidator({min: 1, max: 999, onerror: "<?php echo L('payment_mode') . L('name') . L('empty')?>"});
    })
    //-->
</script>
<div class="pad-10">
    <div class="common-form">
        <form name="myform" action="<?php echo U(ACTION_NAME) ?>" method="post" id="myform">
            <fieldset>
                <legend>说明</legend>
                <?php if ($pay_name == '支付宝') { ?>
                    <table width="100%" class="table_form" id="taobao">
                        <tr>
                            <td>
                                <img src="<?php echo IMG_PATH; ?>taobao_log.png">支付宝为国内领先的支付平台!<br>
                                <a href="https://www.alipay.com/" style="color:red;" target="_blank">了解详情</a> <a
                                        href="http://help.alipay.com/support/help_detail.htm?help_id=241435"
                                        style="color:red;" target="_blank">如何签约</a><br><br>
                                单笔费率0.6% 已经签约的用户，可直接在下方填写相关账号信息即可。
                            </td>
                        </tr>
                    </table>
                <?php } ?>

                <?php if ($pay_name == '银行转账'): ?>
                    用户通过线下汇款或者网银转账汇款形式，将款项汇款到指定银行帐号,
                    然后联系平台客服手动后台充值。
                <?php endif; ?>

                <?php if ($pay_name == '环迅支付'): ?>
                    <img src="http://www.ips.com.cn/u/cms/www/201312/262058360eqa.gif">上海环迅支付平台<br>
                    <a href="http://www.ips.com.cn/" style="color:red;" target="_blank">了解详情</a><br><br>
                    单笔费率0.45%起 开通帐号可联系QQ:231227528 魏先生 18680739025 环迅重庆分公司开通 享受官方合作优惠。
                <?php endif; ?>


            </fieldset>
            <div class="bk15"></div>
            <fieldset>
                <legend><?php echo L('parameter_config') ?></legend>
                <table width="100%" class="table_form">
                    <tr>
                        <td width="120"><?php echo L('payment_mode') ?></td>
                        <td><?php echo $pay_name ?></td>
                    </tr>
                    <tr>
                        <td width="120"><?php echo L('payment_mode') . L('name') ?></td>
                        <td><input type="text" name="name" value="<?php echo $name ? $name : $pay_name ?>"
                                   class="input-text" id="name"></input></td>
                    </tr>
                    <?php foreach ($config as $conf => $name) { ?>
                        <tr>
                            <td><?php echo $name['name'] ?></td>
                            <td>
                                <?php if ($name['type'] == 'text') { ?>
                                    <input type="text" class="input-text" name="config_value[<?php echo $conf ?>]"
                                           id="<?php echo $conf ?>" value="<?php echo $name['value'] ?>"
                                           size="40"></input>
                                <?php } elseif ($name['type'] == 'select') { ?>
                                    <select name="config_value[<?php echo $conf ?>]" value="0">
                                        <?php foreach ($name['range'] as $key => $v) { ?>
                                            <option value="<?php echo $key ?>" <?php if ($key == $name['value']) { ?> selected="" <?php } ?> ><?php echo $v ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                                <input type="hidden" value="<?php echo $conf ?>" name="config_name[]"/>
                            </td>
                        </tr>
                    <?php } ?>

                    <tr>
                        <td><?php echo L('payment_mode') . L('desc') ?></td>
                        <td>
                            <textarea name="description" rows="2" cols="10" id="description" class="inputtext"
                                      style="height:100px;width:300px;"><?php echo $pay_desc ?></textarea>
                            <?php echo $form::editor('description', 'desc'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120"><?php echo L('listorder') ?></td>
                        <td><input type="text" name="pay_order" value="<?php echo $pay_order ?>" class="input-text"
                                   id="pay_order" size="3"/></td>
                    </tr>
                    <tr>
                        <td width="120"><?php echo L('online') ?>?</td>
                        <td><?php echo $is_online ? L('yes') : L('no') ?></td>
                    </tr>
                    <tr>
                        <td width="120">是否启用？</td>
                        <td id="is_open"><input name="is_open" value="1"
                                                type="radio" <?php echo ($enabled == 0) ? '' : 'checked' ?>> <?php echo '启用' ?>
                            &nbsp;&nbsp;&nbsp;<input name="is_open" value="0"
                                                     type="radio" <?php echo ($enabled == 1) ? '' : 'checked' ?>> <?php echo '停用' ?>
                            &nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td width="120"><?php echo L('pay_factorage') ?>：</td>
                        <td id="paymethod"><input name="pay_method" value="0"
                                                  type="radio" <?php echo ($pay_method == 1) ? '' : 'checked' ?>> <?php echo L('pay_method_rate') ?>
                            &nbsp;&nbsp;&nbsp;<input name="pay_method" value="1"
                                                     type="radio" <?php echo ($pay_method == 0) ? '' : 'checked' ?>> <?php echo L('pay_method_fix') ?>
                            &nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <div id="rate" <?php echo ($pay_method == 0) ? '' : 'class="hidden"' ?>>
                                <?php echo L('pay_rate') ?><input type="text" size="3" value="<?php echo $pay_fee ?>"
                                                                  name="pay_rate">&nbsp;%&nbsp;&nbsp;&nbsp;&nbsp;<?php echo L('pay_method_rate_desc') ?>
                            </div>
                            <div id="fix" <?php echo ($pay_method == 1) ? '' : 'class="hidden"' ?>>
                                <?php echo L('pay_fix') ?><input type="text" name="pay_fix" size="3"
                                                                 value="<?php echo $pay_fee ?>">&nbsp;&nbsp;&nbsp;&nbsp; <?php echo L('pay_method_fix_desc') ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </fieldset>

            <div class="bk15"></div>
            <input type="hidden" name="logo" value="<?php echo $logo ?>"/>
            <input type="hidden" name="pay_name" value="<?php echo $pay_name ?>"/>
            <input type="hidden" name="pay_id" value='<?php echo $pay_id ?>'/>
            <input type="hidden" name="pay_code" value='<?php echo $pay_code ?>'/>
            <input type="hidden" name="is_cod" value='<?php echo $is_cod ?>'/>
            <input type="hidden" name="is_online" value='<?php echo $is_online ?>'/>
            <input name="dosubmit" type="submit" value="<?php echo L('submit') ?>" class="dialog" id="dosubmit">
        </form>
    </div>
</div>
</body>
</html>
<script type="text/javascript">
    $(document).ready(function () {
        $("#paymethod input[type='radio']").click(function () {
            if ($(this).val() == 0) {
                $("#rate").removeClass('hidden');
                $("#fix").addClass('hidden');
                $("#rate input").val('0');
            } else {
                $("#fix").removeClass('hidden');
                $("#rate").addClass('hidden');
                $("#fix input").val('0');
            }
        });
    });
</script>


