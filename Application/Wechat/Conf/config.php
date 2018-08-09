<?php
return array(
    'WECHAT_ACCOUNT_NBIND_TPL' => "您好，您还未绑定{site_name}网站帐号，绑定之后可获以下服务\r\n\r\n1.可使用微信快捷服务：\r\n2.可收取实时订单通知\r\n3.可获取优惠活动信息\r\n\r\n还没帐号<a href=\"{site_url}/#/tab/register\">去注册</a>\r\n   已有帐号<a href=\"{web_url}/?m=wechat&c=index&a=bind&openid={openid}\">现在绑定</a>",
    'WECHAT_ACCOUNT_YBIND_TPL' => "{nickname}您好，您已绑定云划算帐号。\r\n现在可使用以下功能：\r\n\r\n1.可使用微信快捷服务\r\n2.可收取实时订单通知\r\n3.可获取优惠活动信息",
    
    //我的余额
    'WECHAT_ACCOUNT_BALANCE' => "{nickname}您好，您账户账户余额\r\n可用余额：￥ {balance}\r\n可提现金额：￥ {balance}\r\n\r\n<a href=\"{url}\">申请提现</a>",
    
    //申请提现
    'WECHAT_ACCOUNT_CASH' => "{nickname}您好，您目前\r\n可提现金额为 ￥ {balance}\r\n<a href=\"{url}\">申请提现</a>\r\n\r\n温馨提示：\r\n1.普通提现到账时间为3-7天。\r\n2.快速提现到账时间为24小时以内",
    
    // 我的积分
    'WECHAT_ACCOUNT_POINT' => "{nickname}您好，您目前\r\n可用积分：{point}\r\n\r\n <a href=\"{url}\">去积分商城兑换</a>\r\n\r\n温馨提示：\r\n1.积分可到积分商城兑换礼物\r\n2.每日可参与任务赚取积分\r\n<a href=\"{url}\">去做任务</a>",
    
    // 账户信息
    'WECHAT_ACCOUNT_INFO' => "{nickname}您好，您目前账户情况\r\n1.完善资料 <a href=\"{site_url}\">{nickname_status}</a>\r\n2.手机认证 <a href=\"{phone_url}\">{phone_status}</a>\r\n3.邮箱认证 <a href=\"{email_url}\">{email_status}</a>\r\n4.实名认证 <a href=\"{identity_url}\">{identity_status}</a>\r\n5.绑定支付宝 <a href=\"{alipay_url}\">{alipay_status}</a>\r\n6.绑定银行卡 <a href=\"{site_url}\">{bank_status}</a>\r\n--------------------\r\n完善以上认证信息 可以使用丰富的功能。",
    
    //待审核订单
    'WECHAT_ORDER_WAIT_CHECK' => "{nickname}您好，您目前\r\n\r\n{rebate_alias}\r\n有 {rebate_num} 笔待审核订单号。 <a href=\"{url}\">去看看</a>\r\n--------------------\r\n{trial_alias}\r\n有 {trial_confirm} 笔待确认试用资格。 <a href=\"{url}\">去看看</a>\r\n有 {trial_num} 笔待审核试用报告。 <a href=\"{url}\">去看看</a>",
    //待填写订单号
    'WECHAT_ORDER_WAIT_FILL' => "{nickname}您好，您目前\r\n\r\n{rebate_alias}\r\n有 {rebate_num} 笔已抢购待填写订单号。<a href=\"{url}\">去填写</a>\r\n--------------------\r\n{trial_alias}\r\n有 {trial_num} 笔已获得试用资格待填写订单号。<a href=\"{url}\">去填写</a>\r\n\r\n温馨提示：请即时填写订单号，避免过期自动关闭订单",
    
    //待填写试用报告
    'WECHAT_ORDER_WAIT_TRIAL_REPORT' => "{nickname}您好，您目前\r\n\r\n有 {num} 笔{trial_alias}现在需要填写试用报告。<a href=\"{url}\">去填写报告</a>\r\n\r\n温馨提示：到期未填写试用报告，试用资格将到期自动取消，优秀的试用报告可获得商家额外奖励。",
    
    //待评价订单
    'WECHAT_ORDER_WAIT_REPORT' => "{nickname}您好，您目前\r\n\r\n有 {num} 笔{rebate_alias}待晒单评价。<a href=\"{url}\">去评价晒单</a>\r\n\r\n温馨提示：分享您的评价 助他人了解商品",
    
    /* 申诉中订单 */
    'WECHAT_ORDER_WAIT_APPEAL' => "{nickname}您好，您目前\r\n有{num}笔订单处于申诉中，<a href=\"{url}\">去看看结果</a>\r\n\r\n温馨提示：\r\n平台在收到用户申诉之后在48小时内会处理。",
    
);
