<?php
return array(
    'URL_REWRITE' => array (
        'member/index/register' => '/register/',
        'member/index/login' => '/login/',

        'task/index/index' => '/task/',

        # 积分商城
        'shop/index/index' => '/shop/',
        'shop/index/show' => '/shop/show/',

        'document/index/lists' => '/help/',
        'document/index/show' => '/help/show/',


        'member/attesta/person' => '/user/seller/attestation/',//商家 - 个人认证
        'member/attesta/shop' => '/user/seller/shop/',//商家 -店铺认证
        'member/attesta/brand' => '/user/seller/brand/',//商家 -品牌认证
        'member/merchant/complete' => '/user/seller/profile/',//商家 -商家资料
        'member/enter_activity/index' => '/user/seller/enroll/',//商家 -活动报名

        'member/enter_activity/detail_activity' => '/user/seller/show/',//商家 - 活动报名详情页
        'member/merchant_product/activity' => '/user/seller/manager/',//商家 - 活动管理

        'member/appeal/appeal_manage' => '/user/seller/goods/appeal/',//商家 - 申诉管理
        'member/merchant_product/add' => '/user/seller/goods/',//商家 - 发布商品
        'member/merchant_product/edit' => '/user/seller/edit/',//商家 - 发布商品
        'member/merchant_product/bailbond' => '/user/seller/payment/',//商家 - 支付费用

        'member/financial/pay_log' => '/user/seller/recharge_log/',//商家 -充值明细
        'pay/index/pay'        => '/user/seller/pay/',//快速充值

        'member/merchant/becomevip' => '/user/seller/vip/',//商家 - vip商家
        'member/financial/cash_log' => '/user/seller/cash_log/',//商家 -提现明细
        'member/financial/index' => '/user/seller/financial/',//商家 -财务明细

        'member/profile/index'          => '/user/',
        'member/profile/infomation'     => '/user/profile/', //资料修改
        'member/attesta/index'          => '/user/attesta/',//用户 - 资料认证
        'member/attesta/name_attesta'   => '/user/name/', //用户 - 实名认证
        'member/attesta/email_attesta'  => '/user/email/', //用户 - 邮箱认证
        'member/attesta/phone_attesta'  => '/user/phone/', //用户 - 手机认证
        'member/attesta/alipay_attesta' => '/user/alipay/', //用户 - 支付宝绑定
        'member/attesta/bank_attesta'   => '/user/bank/', //用户 - 银行卡绑定
        
        /* 用户 - 订单管理 */
        'member/order/manage'       => '/user/order/',
        'member/recommend/index'    => '/user/recommend/',//用户 - 推荐
        'member/announce/announce'  => '/user/message/',//用户 - 站内信
        'pay/index/deposite'        => '/user/deposite/',//用户 - 提现
        'member/financial/cash_log' => '/user/deposite/log',//用户 - 提现记录
        'member/financial/point_log' => '/user/buyer/point',//用户 - 积分明细
        'member/financial/convert_log' => '/user/buyer/convert/',//用户 - 积分兑换记录


        'member/financial/index' => '/user/money/log',//用户 - 财务明细
        'member/member_friend/index'=>'/invitation/', //邀请好友

        'oauth/index/callback'=>'/qqlogin/', //邀请好友
        'product/index/report_list'=>'/report/', //试用报告列表页
        'product/index/report_show'=>'/try_report/show/', //试用报告详情页




    )
);
