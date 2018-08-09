<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script  type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script  type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<style type="text/css">
.input-botton {
    border:none;
    border-bottom:1px dotted #E1A035;
    background:none;
}
</style>
<script type="text/javascript">
$(function(){
    $('.input-botton').click(function(){
        $(this).select();
    })
})
</script>
<form action="<?php echo U('sms');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <fieldset>
        <legend>短信模板配置</legend>    
        <div class="btn text-l">
        <span class="font-fixh green">提示：中国网建无需设置短信模板，以下短信模板设置 用于阿里大鱼</span>
        </div>
       <br/>
        <table width="100%"  class="table_form">
           
            <tr>
                <th>手机注册:</th>
                <td>
                    标识：1 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_1]" value="<?php echo C('template_id_1') ?>" >

                </td>
            </tr>

            <tr>
                <th>找回密码:</th>
                <td>
                    标识：2 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_2]" value="<?php echo C('template_id_2') ?>" >

                </td>
            </tr>
            

            <tr>
                <th>手机绑定:</th>
                <td>
                    标识：3 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_3]" value="<?php echo C('template_id_3') ?>" >

                </td>
            </tr>

              <tr>
                <th>充值审核通知:</th>
                <td>
                    标识：4 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_4]" value="<?php echo C('template_id_4') ?>" >

                </td>
            </tr>

             <tr>
                <th>提现审核通知:</th>
                <td>
                    标识：5&nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_5]" value="<?php echo C('template_id_5') ?>" >

                </td>
            </tr>
             <tr>
                <th>商品审核通知:</th>
                <td>
                    标识：6 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_6]" value="<?php echo C('template_id_6') ?>" >

                </td>
            </tr>

             <tr>
                <th>商品屏蔽通知:</th>
                <td>
                    标识：7 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_7]" value="<?php echo C('template_id_7') ?>" >

                </td>
            </tr>

             <tr>
                <th>商品结算通知:</th>
                <td>
                    标识：8 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_8]" value="<?php echo C('template_id_8') ?>" >

                </td>
            </tr>

              <tr>
                <th>填写订单号通知:</th>
                <td>
                    标识：9 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_9]" value="<?php echo C('template_id_9') ?>" >

                </td>
            </tr>

            <tr>
                <th>填写试用报告通知:</th>
                <td>
                    标识：10 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_10]" value="<?php echo C('template_id_10') ?>" >

                </td>
            </tr>
             <tr>
                <th>订单号审核通知:</th>
                <td>
                    标识：11 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_11]" value="<?php echo C('template_id_11') ?>" >

                </td>
            </tr>

             <tr>
                <th>订单结算通知:</th>
                <td>
                    标识：12 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_12]" value="<?php echo C('template_id_12') ?>" >

                </td>
            </tr>

            <tr>
                <th>用户发起申诉通知:</th>
                <td>
                    标识：13 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_13]" value="<?php echo C('template_id_13') ?>" >

                </td>
            </tr>

             <tr>
                <th>申诉仲裁结果通知:</th>
                <td>
                    标识：14 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_14]" value="<?php echo C('template_id_14') ?>" >

                </td>
            </tr>

             <tr>
                <th>补仓提醒通知:</th>
                <td>
                    标识：15 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_15]" value="<?php echo C('template_id_15') ?>" >

                </td>
            </tr>

             <tr>
                <th>赠送vip会员模板:</th>
                <td>
                    标识：16 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_16]" value="<?php echo C('template_id_16') ?>" >

                </td>
            </tr>

              <tr>
                <th>二次提醒（试用订单提醒）:</th>
                <td>
                    标识：17&nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_17]" value="<?php echo C('template_id_17') ?>" >

                </td>
            </tr>

              <tr>
                <th>二次提醒（填写试用报告）:</th>
                <td>
                    标识：18 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_18]" value="<?php echo C('template_id_18') ?>" >

                </td>
            </tr>

              <tr>
                <th>二次提醒（返利订单提醒）:</th>
                <td>
                    标识：19 &nbsp;&nbsp;&nbsp;
                    模板id: <input type="text" name="configs[template_id_19]" value="<?php echo C('template_id_19') ?>" >

                </td>
            </tr>
            
            
            
           
            


           
        </table>
        </fieldset>
        <div class="bk15"></div>
        <input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
    </div>
</div>
</form>
</body>
<script type="text/javascript">
function SwapTab(name,cls_show,cls_hide,cnt,cur) {
    $('div.contentList').hide();
    $('ul.tabBut > li').attr('class', cls_hide);
    $('#div_'+name+'_'+cur).show();
    $('#tab_'+name+'_'+cur).attr('class',cls_show);
}
</script>
</html>