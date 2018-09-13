<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header','admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
$(function(){
    SwapTab('setting','on','',2, 'quick');
})
function SwapTab(name,cls_show,cls_hide,cnt,cur) {
    $('div.contentList').hide();
    $('ul.tabBut > li').attr('class', cls_hide);
    $('#div_'+name+'_'+cur).show();
    $('#tab_'+name+'_'+cur).attr('class',cls_show);
}
</script>
<form action="<?php echo U('update');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_quick" <?php if ($this->type == 'quick'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',2, 'quick');"><?php echo L('提现设置')?></li>
        </ul>
        <!-- 商家设置 -->
        <div id="div_setting_quick" class="contentList pad-10">
        <table width="100%"  class="table_form">
        	<tr>
                <th><?php echo L('快速提现最快到账时间')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[quick][time]" value="<?php echo $quick['time'];?>" />&nbsp;例如：2小时
                </td>
            </tr>
            
              <tr>
                <th><?php echo L('普通提现到账时间')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[common][time]" value="<?php echo $common['time'];?>" />&nbsp;例如：2小时
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('普通会员/普通商家手续费')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[quick][service][common]" value="<?php echo $quick['service']['common'];?>" />%&nbsp;&nbsp;例如：1%
                </td>
            </tr>
            
             <tr>
                <th><?php echo L('vip会员/vip商家手续费')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[quick][service][vip]" value="<?php echo $quick['service']['vip'];?>" />%&nbsp;&nbsp;例如：1%
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('提现方式')?></th>
                <td class="y-bg">
                    <label><input type="checkbox" class="input-text" name="setting[type][]" value="alipay" <?php if(in_array('alipay',$type)){?>checked<?php }?>/>提现到支付宝</label>&nbsp;&nbsp;
                    <label><input type="checkbox" class="input-text" name="setting[type][]" value="bank"  <?php if(in_array('bank',$type)){?>checked<?php }?>/>提现到银行</label>
                    <label><input type="checkbox" class="input-text" name="setting[type][]" value="paypal"  <?php if(in_array('paypal',$type)){?>checked<?php }?>/>提现到paypay</label>
                    <label><input type="checkbox" class="input-text" name="setting[type][]" value="quickpay"  <?php if(in_array('quickpay',$type)){?>checked<?php }?>/>提现到quickpay</label>
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('提现金额')?></th>
                <td class="y-bg">
                    	最小<input type="text" class="input-text" name="setting[min_money]" value="<?php echo $pay_setting['min_money'];?>" size="6"/>/美元&nbsp;&nbsp;
                    	倍数<input type="text" class="input-text" name="setting[multiple_money]" value="<?php echo $pay_setting['multiple_money'];?>" size="6"/>提示：提现时按照当前的倍数提现
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
<script type="text/javascript">
// 表单校验
$(function(){
    $.formValidator.initConfig({
        formid:"myform",
        autotip:true,
        onerror:function(msg,obj){
            $(obj).focus();
        }
    });
    // 商家审核时间
    $("#check_time").formValidator({
        empty:false,
        onempty:'商家审核时间不能为空',
        onshow:'请输入商家审核时间(纯数字)' ,
        onfocus:"请输入商家审核时间(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'商家审核时间只能为正数'
    });
})
</script>
</html>