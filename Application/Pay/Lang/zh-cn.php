<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
 **/
return array(
		'trade_sn' => '支付单号',
		'addtime' => '订单时间',
		'to' => '至',
		'confirm_pay' => '确认并支付',
		'usernote' => '备注',
		'adminnote' => '管理员操作',
		'user_balance' => '用户余额：',
		'yuan' => '&nbsp元',
		'dian' => '&nbsp点',
		'trade_succ' => '成功',
		'checking' => '验证中..',
		'user_not_exist' => '该用户不存在',
		
		'input_price_to_change' => '输入修改数量（资金或者点数）',
		'number' => '数量 ',
		'must_be_price' => '必须为金额，最多保留两位小数',
		'reason_of_modify' => '要修改的理由',
		
		//modify_deposit.php
		'recharge_type' => '充值类型',
		'capital' => '资金',
		'point' => '点数',
		'recharge_quota' => '充值额度',
		'increase' => '增加',
		'reduce' => '减少',
		'trading' => '交易',
		'op_notice' => '提醒操作',
		'op_sendsms' => '发送短消息通知会员',
		'op_sendemail' => '发送e-mail通知会员',
		'send_account_changes_notice' => '账户变更通知',
		'background_operation' => '后台操作',
		'account_changes_notice_tips' => '尊敬的{username},您好！<br/>您的账户于{time}发生变动,操作：{op},理由:{note},当前余额：{amount}元，{point}积分。',
		
		//payment.php
		'basic_config' => '基本设置',
		'contact_email' => '联系邮箱',
		'contact_phone' => '联系电话',
		'order_info' => '订单信息',
		'order_sn' => '支付单号',
		'order_name' => '名称',
		'order_price' => '订单价格',
		'order_discount' => '交易加价/涨价',
		'order_addtime' => '订单生成时间',
		'order_ip' => '订单生成IP',
		'payment_type' => '支付类型',
		'order' => '订单',
		'disount_notice' => '要给顾客便宜10元,降价请输入“-10”',
		
		'discount' => '订单改价',
		'recharge' => '在线充值',
		'offline' => '线下支付',
		'online' => '在线支付',
		'selfincome' => '自助获取',
		
		'order_time' => '支付时间',
		'business_mode' => '业务方式',
		'payment_mode' => '支付方式',
		'deposit_amount' => '存入金额',
		'pay_status' => '付款状态',
		'pay_btn' => '付款',
		
		'name' => '名称',
		'desc' => '描述',
		'pay_factorage' => '支付手续费',
		'pay_method_rate' => '按比例收费',
		'pay_method_fix' => '固定费用',
		'pay_rate' => '费率',
		'pay_fix' => '金额',
		'pay_method_rate_desc' => '说明：顾客将支付订单总金额乘以此费率作为手续费；',
		'pay_method_fix_desc' => '说明：顾客每笔订单需要支付的手续费；',
		
		'parameter_config' => '参数设置',
		'plus_version' => '插件版本',
		'plus_author' => '插件作者',
		'plus_site' => '插件网址',
		
		'plus_install' => '安装',
		'plus_uninstall' => '卸载',
		
		'check_confirm' => '确认要通过订单  {sn} 审核？',
		'check_passed' => '审核通过',
		
		'change_price' => '改价',
		'check' => '审核',
		'closed' => '关闭',
		
		'thispage' => '本页',
		'finance' => '财务',
		'totalize' => '总计',
		'amount' => '金额',
		'total' => '总',
		'bi' => '笔',
		'trade_succ' => '成功',
		'transactions' => '交易量',
		'trade' => '交易',
		'trade_record_del' => '确认删除该记录？',
		
		/******************error & notice********************/
		
		'illegal_sign' => '签名错误',
		'illegal_notice' => '通知错误',
		'illegal_return' => '信息返回错误',
		'illegal_pay_method' => '支付方式错误',
		'illegal_creat_sn' => '订单号生成错误',
		
		
		'pay_success' => '恭喜您，支付成功',
		'pay_failed' => '支付失败，请联系管理员',
		'payment_failed' => '支付方式发生错误',
		'order_closed_or_finish' => '订单已完成或该已经关闭',
		'state_change_succ' => '状态修改完成',
		
		'delete_succ' => '删除成功',
		'public_discount_succ' => '操作成功',
		'admin_recharge' => '后台充值',
		
		/******************pay status********************/
		'all_status' => '全部状态',
		
		'unpay' => '<font color=>"red" class=>"onError">交易未支付</font>',
		'succ' => '<font color=>"green" class=>"onCorrect">交易成功</font>',
		'failed' => '交易失败',
		'error' => '交易错误',
		'progress' => '<font color=>"orange" class=>"onTime">交易处理中</font>',
		'timeout' => '交易超时',
		'cancel' => '交易取消',
		'waitting' => '<font color=>"orange" class=>"onTime">等待付款</font>',
		
		'unpay' => '交易未支付',
		'succ' => '交易成功',
		'progress' => '交易处理中',
		'cancel' => '交易取消',
		
		/*************pay plus language***************/
		
		'alipay' => '支付宝',
		'alipay_account' => '支付宝帐户',
		'alipay_tip' => '支付宝是国内领先的独立第三方支付平台，由阿里巴巴集团创办。致力于为中国电子商务提供“简单、安全、快速”的在线支付解决方案。',
		'alipay_key' => '交易安全校验码(key)',
		'alipay_partner' => '合作者身份(parterID)',
		'service_type' => '选择接口类型',
		
		'tenpay_account' => '财付通客户号',
		'tenpay_privateKey' => '财付通私钥',
		'tenpay_authtype' => '选择接口类型',
		
		'chinabank' => '网银在线',
		'chinabank_tip' => '网银在线与中国银行、中国工商银行、中国农业银行、中国建设银行、招商银行等国内各大银行，以及VISA、MasterCard、JCB等国际信用卡组织保持了长期、紧密、良好的合作关系。<a href=>"http://www.chinabank.com.cn" target=>"_blank"><font color=>"red">立即在线申请</font>',
		'chinabank_account' => '网银在线商户号',
		'chinabank_key' => '网银在线MD5私钥',
		
		'sndapay' => '盛付通',
		'sndapay_tip' => '盛付通是盛大网络创办的中国领先的在线支付平台，致力于为互联网用户和企业提供便捷、安全的支付服务。通过与各大银行、通信服务商等签约合作，提供具备相当实力和信誉保障的支付服务。<a href=>"http://www.shengpay.com/HomePage.aspx?tag=>phpcms" target=>"_blank"><font color=>"red">立即在线申请</font>',
		'sndapay_account' => '盛大支付商户号',
		'sndapay_key' => '盛大支付密钥',
		
		
		'service_type_range'   => '使用即时到账交易接口',
		'service_type_ranges'=>array(
					'0' =>'使用担保交易接口',
					'1' => '使用标准双接口',
					'2'=> '使用即时到账交易接口',
		),
		
		'userid' => '用户ID',
		'op' => '操作人',
		'expenditure_patterns' => '消费类型',
		'money' => '金钱',
		'point' => '下载点数',
		'from' => '从',
		'content_of_consumption' => '消费内容',
		'empdisposetime' => '消费时间',
		'consumption_quantity' => '消费数量',
		'self' => '自身',
		'wrong_time_over_time_to_time_less_than' => '错误的时间格式，结束时间小于开始时间！',
		
		'spend_msg_1' => '请对消费内容进行描述。',
		'spend_msg_2' => '请输入消费金额。',
		'spend_msg_3' => '用户不能为空。',
		'spend_msg_6' => '账户余额不足。',
		'spend_msg_7' => '消费类型为空。',
		'spend_msg_8' => '数据存入数据库时出错。',
		'bank_transfer' => '银行转账',
		'transfer' => '银行汇款/转账',
		'dsa' => 'DSA 签名方法待后续开发，请先使用MD5签名方式',
		'alipay_error' => '支付宝暂不支持{sign_type}类型的签名方式',
		'execute_date' => '执行日期',
		'query_stat' => '查询统计',
		'total_transactions' => '总交易数',
		'transactions_success' => '成功交易',
		'pay_tip' => '我们目前支持的汇款方式，请根据您选择的支付方式来选择银行汇款。汇款以后，请立即通知我们。',
		'configure' => '配置',
		'empty'      => '不能为空。',
		'points' => '点',
		/*审核充值*/
		'order_check_no'      =>'交易号',
		'check_status'            =>'审核状态',
		'passed'                     =>'通过',
		'unpassed'                 =>'不通过',
		'check_cause'            =>'审核原因',
		'email'                            => '邮箱',
		'email_already_exist'     =>'邮箱已占用',
);