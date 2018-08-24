function dt(ts) { 
	var dt = new Date(ts * 1000);
	return dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate() + " " + dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
}
//错误提示
function error(msg) {	
	art.dialog({
		lock: true,
		fixed: true,
		icon: 'face-sad',
		title: '错误提示',
		content: msg,
		ok: true
	});
}
//错误提示，不刷新
function error_no(msg) {	
	art.dialog({
		lock: true,
		fixed: true,
		icon: 'face-sad',
		title: '错误提示',
		content: msg,
		ok: true
	});
}

function succes(msg) {
	art.dialog({
		lock: true,
		fixed: true,
		icon: 'face-smile',
		title: '提示',
		content: msg,
		ok: function(){
   			location.reload();
		}
	});
}
/* 确定弹窗 */
function enter(content,url) {
	art.dialog.confirm(content,function(){
		// art.dialog.tips('执行确定操作');
		location.href = url;
	}, function () {
	    // art.dialog.tips('执行取消操作');
	})
}

/* 倒计时 */
function order_time(sec,oid) {
	var oTime = $("#remaining_time_"+oid);
	var timer = null;
	timer = setInterval(function() {
		sec -= 1;
		if (sec <= 0) {
			oTime.html('-');
			clearInterval(timer);
			location.reload();
			return;
		}
		oTime.html(count_down(sec));
	}, 1000);
	oTime.html(count_down(sec));
}
function count_down(sec) {
	var s = sec;
	var left_s = s % 60;
	var m = Math.floor(s / 60);
	var left_m = m % 60;
	var h = Math.floor(m / 60);
	var left_h = h % 24;
	var d = Math.floor(h / 24);
	var ret = [];
	d && ret.push('<em class="d">', d, '</em>天');
	left_h && ret.push('<em class="h">', time_pad(left_h), '</em>时');
	left_m && ret.push('<em class="m">', time_pad(left_m), '</em>分');
	left_s && ret.push('<em class="s">', time_pad(left_s), '</em>秒');
	return ret.join('');
}

function time_pad(s) {
	return Number(s) > 9 ? String(s) : "0" + String(s);
}

var order = (function() {
	function get_order(oid) {
		return order_data[oid];
	}

	function set_btn_state(win, can_use) {
		if (can_use) {
			win.DOM.buttons.find('button:first').html('确定').removeAttr('disabled').addClass('aui_state_highlight');
		} else {
			win.DOM.buttons.find('button:first').html('操作中...').attr('disabled', 'disabled').removeClass('aui_state_highlight');
		}
	}

	var domain = location.host.replace(/(\w+\.)*(\w+\.com)/, '.$2');
	// function site(site){return'http://'+site+domain+'/';}

	// 计时器，用于判断是否显示验证码
	var cur_time = (function(){
		var node = parseInt(new Date().getTime()/1000);
		return function(){
			return parseInt(new Date().getTime()/1000)-node;
		}
	})();

	return {
		/* 倒计时 */
		timer:function(oid){
			var order = get_order(oid);
			var timestamp = parseInt($.now() / 1000);
			if(timestamp < order.end_time){
				order_time(order.end_time - timestamp,oid);
			}
		},
		/* 校验答案 */
		answer: function (oid){
			var _order = get_order(oid);
			var ask_html = '<div class="CPM_style CPM_style_2 border_radius_5" style="position: static;">';
				ask_html += '<p class="issue"><a href="'+site.site_root+'/help/?catid=81" target="_blank">答案下单帮助</a></p>';
				ask_html += '<font class="font f_bg_yes font_2">您需要回答问题才能完成抢购哦！</font>';
				ask_html += '<ul class="set_iss"><li>问题：<em>'+_order.pro.goods_rule.ask.question+'？</em></li>';
				if (_order.pro.goods_rule.ask.tips != null) {
					ask_html += '<li>提示：<em>'+_order.pro.goods_rule.ask.tips+'</em></li>';
				}
				ask_html += '<li><font>答案：</font><input type="text" name="answer" id="answer"/><img src="'+site.template_img+'/verify_btn1.png" alt="" id="right" style="display:none;"/>';
				ask_html += '<span id="error" style="display:none;"><img src="'+site.template_img+'/verify_btn2.png" alt=""/>回答问题错误！</span></li></ul><p class="iss_hint">提示：您可以在商家宝贝详情页查看宝贝介绍可以找到相关答案。<a href="'+site.site_root+_order.pro.goods_url+'" target="_blank">去找找看</a></p></div>';	
			art.dialog({
				title:'校验答案',
				fixed:true,
				width:500,
				lock:true,
				content:ask_html,
				okVal:'验证答案',
				ok:function(){
					if (_order.pro.goods_rule.ask.answer == $.trim($("#answer").val())) {
						$('#right').attr('style','display:');
						$('#error').attr('style','display:none');
						if (_order.pro.mod == 'rebate') {
							order.rebate_url(oid);
						}else if(_order.pro.mod == 'trial') {
							order.trial_url(oid);
						}
						return true;
					}else{
						$('#right').attr('style','display:none');
						$('#error').attr('style','display:');
						return false;
					}
																	
				},
				cancelVal:'取消',
				cancel:function() {return true;}
			});
			return false;
		},
		/* 抢购入口 */
		buy: function(oid) {
			var _order = get_order(oid);
			if (site.user.length < 1) {	// 未登录
				member.login();
				return false;
			}
			// 答案下单先校验答案
			// if (_order.type == 'ask') {
			// 	order.answer(oid);
			// 	return false;
			// }
			if (_order.pro.mod == 'rebate') {
				order.rebate_url(oid);
			}else if(_order.pro.mod == 'trial') {
				order.trial_url(oid);
			}
		},
		/* 现在去下单 [购物返利] */
		rebate_url : function(oid) {
			var _order = get_order(oid);
			// 抢购成功后的模版选择
			var buy_succes = '<div class="CPM_style CPM_style_2 border_radius_5" style="position: static;">';
			// 下单方式			
			switch(_order.type) {
				case 'general': //普通下单
					buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=79" target="_blank">普通下单常见问题</a></p><font class="font f_bg_yes font_2">抢购成功，请确认以下优惠哦！</font><ul class="hint_text_2"><p>请注意以下事项：</p><li>1、下单价：<em>'+_order.goods_price+'</em>，请在下单页面核对下单价是否一致。</li><li>2、返还划算金：<em>'+(_order.pro.goods_price-_order.pro.goods_price*_order.pro.goods_discount/10).toFixed(2)+'</em> 美元，交易完成后，将返还给您金额。</li><li>注意：报名抢购后<em>'+ act_config.buyer_write_order_time+'分钟内</em>不下单付款并返回填写订单号，本次订单将自动关闭。</li></ul></div>';
					break;
				case 'qrcode': //二维码下单
				  	buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=82" target="_blank">二维码下单帮助</a></p><font class="font f_bg_yes font_2">抢购成功，请扫描以下二维码</font>';
				  	buy_succes += '<img src="'+_order.pro.goods_rule.qrcode+'" alt="" class="QR_code" /><ul class="hint_text_2"><p>请注意以下事项：</p><li>1、下单价：<em>'+_order.goods_price+'</em> 美元，请在下单页面核对下单信息是否一致。</li><li>2、返还划算金：<em>'+(_order.pro.goods_price-_order.pro.goods_price*_order.pro.goods_discount/10).toFixed(2)+'</em> 美元，交易完成后，将返还给您的划算金额。</li>';
				  	buy_succes += '<li>注意：报名抢购后<em>'+ act_config.buyer_write_order_time+'分钟内</em>不下单付款并返回填写订单号，本次订单将自动关闭。</li></ul></div>';
					break;
				case 'search': //搜索下单
					buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=80" target="_blank">搜索下单常见问题</a></p>';
					buy_succes += '<font class="font f_bg_yes font_2">抢购成功，请使用搜索方式下单</font>';
					buy_succes += '<ul class="hint_text_2 margin_b_10"><p>搜索下单提示：</p>';
					buy_succes += '<li>1、进入<em><a href="'+_order.shop_source.url+'" target="_blank">'+_order.shop_source.name+'网首页</a></em>，搜索关键词：<b style="color:red;">'+_order.pro.goods_rule.keyword+'</b>。</li>';
					buy_succes += '<li>2、请使用<em>Ctrl+F 查找</em>旺旺名称:<span style="color:red;">'+_order.contact_want+'</span>;进入店铺找到活动宝贝，然后进行下单。</li>';
					if (_order.pro.goods_rule.keyword2.length > 0 ) {
						buy_succes += '<li>3、搜索提示：<span style="color:red;">'+ _order.pro.goods_rule.keyword2 +'</span></li>';
					}
					buy_succes += '</ul><ul class="hint_text_2"><p>请注意以下事项：</p>';
					buy_succes += '<li>1、下单价：<em>'+_order.goods_price+'</em> 美元，请在下单页面核对下单价是否一致。</li>';
					buy_succes += '<li>2、返还划算金：<em>'+(_order.pro.goods_price-_order.pro.goods_price*_order.pro.goods_discount/10).toFixed(2)+'</em> 美元，交易完成后，将返还给您的划算金额。</li>';
					buy_succes += '<li>注意：报名抢购后<em>'+ act_config.buyer_write_order_time+'分钟内</em>不下单付款并返回填写订单号，本次订单将自动关闭。</li></ul></div>';
					break;
				case 'ask' : //问答下单
					buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=81" target="_blank">答案下单常见问题</a></p><font class="font f_bg_yes font_2">抢购成功，请确认以下优惠哦！</font><ul class="hint_text_2"><p>请注意以下事项：</p><li>1、下单价：<em>'+_order.goods_price+'</em>，请在下单页面核对下单价是否一致。</li><li>2、返还划算金：<em>'+(_order.pro.goods_price-_order.pro.goods_price*_order.pro.goods_discount/10).toFixed(2)+'</em> 美元，交易完成后，将返还给您金额。</li><li>注意：报名抢购后<em>'+ act_config.buyer_write_order_time+'分钟内</em>不下单付款并返回填写订单号，本次订单将自动关闭。</li></ul></div>';
					break;
			}
			if (_order.type == 'search') {
				okval = '去搜索下单';
			}else if (_order.type == 'qrcode') {
				okval = '已下单去填写订单号';
			}else{
				okval = '抢购成功，现在马上去购买';
			}
			art.dialog({
				lock : true,
				fixed : true,
				id:'go_url',
				title : '商品下单提示',
				content : buy_succes,
				okVal : okval,
				ok : function() {
					if (_order.type == 'search'){
						window.open(_order.shop_source.url);
					}else if(_order.type != 'qrcode'){
						window.open(_order.pro.goods_url);
					}
					var diy_form='<form><table>'
								 + 		'<colgroup><col style="width:60px"><col style="width:350px"></colgroup>'
								 + 		'<tbody style="color:#797979;">'
								 + 			'<tr>'
								 + 				'<td>商品名称：</td>'
								 + 				'<td style="color: #06f"><a href="'+_order.pro.url+'" target="_blank" style="color:#09f;">'+ _order.title +'</a></td>'
								 + 			'</tr>'
								 + 			'<tr>'
								 + 				'<td>下单价：</td>'
								 + 				'<td style="font-weight:bold;color:#000;">$'+ _order.pro.goods_price +'</td>'
								 + 			'</tr>'
								 + 			'<tr>'
								 + 				'<td>订单编号：</td>'
								 + 				'<td><input type="text" name="order_sn" class="ui-form-text ui-form-textRed"/><span class="trade-no-msg" style="color: #cc0000;"></span></td>'
								 + 			'</tr>'
								 + 			'<tr>'
								 + 				'<td style="vertical-align: top;">温馨提示：</td>'
								 +				'<td style="color:#999;">'
								 +					'<p>1、请填写已付款的订单编号，若填入未付款单号，属于违规行为且将无法获得返款；<a style="color:#09f;" href="'+site.site_root+'/help/show/?catid=59&id=143" target="_blank">填写的订单号规则？</a></p>'
								 +					'<p>2、若单号被审核有误，请在 小时内进行申诉或修改，逾期将无法领回划算金（建议平时经常登录网站查看站内信提醒哦！）<a style="color:#09f;display:block;" href="'+site.site_root+'/help/show/?catid=6&id=43" target="_blank">如何获取订单编号？</a></p>'
								 +				'</td>'
								 + 			'</tr>'
								 + 		'</tbody>'
								 + '</table></form>';
					art.dialog({
						lock	: true,
						fixed	: true,
						title	: '填写亚马逊订单号',
						content	: diy_form,
						init	: function(){
							var dialog = this;
							var form  = this.DOM.content.find('form');	
							var trade = form.find('input[name=order_sn]');
							var noMsg = form.find('.trade-no-msg').html('');
							form.submit(function(){
								var order_sn_val   = $.trim( trade.val() );
								var data = {
									order_sn:order_sn_val,
									order_id:_order.oid
								};
								noMsg.css('color', '#c00').html('');
								var re = new RegExp("^[0-9]*$");
								if ( order_sn_val=='' ) {
									noMsg.html(' 请输入订单号');
									trade.val('').focus();
									return false;
								}else if ( re.test( order_sn_val ) === false ) {
									noMsg.html(' 订单号为15位纯数字');
									trade.val('').focus();
									return false;
								}else {
									noMsg.html('');
								}
								set_btn_state(dialog, false); 
								noMsg.css('color', '#666').html(" 正在发送...");
								$.post(site.site_root + '/index.php?m=order&c=api&a=fill_sn', data, function(ret) {
									set_btn_state(dialog, true);
									if (ret.status==1) {
										dialog.close();
										art.dialog({
											lock : true,
											fixed : true,
											title : '填写单号成功',
											content : '商家将在交易完成后审核返款。',
											ok : function() {
												location.reload();
											}
										});
									} else {
										noMsg.html(ret.info).css('color', '#c00');
									}
								});
								return false;
							});
							trade.focus();
						},
						ok		: function(){
							var form = this.DOM.content.find('form').submit();
							return false;
						},
						cancel : true
					});
					this.close();
				},
				cancelVal:'继续逛逛',
				cancel:function() {return true;}
			});			
		},
		/* 现在去下单 [免费试用] */
		trial_url : function(oid) {
			var _order = get_order(oid);
			// 抢购成功后的模版选择
			var buy_succes = '<div class="CPM_style CPM_style_2 border_radius_5" style="position: static;">';
			// 下单方式
			switch(_order.type) {
				case 'general': //普通下单
					buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=79" target="_blank">普通下单常见问题</a></p><font class="font f_bg_yes font_2">恭喜您，已获得试用资格，请确认以下优惠哦！</font><ul class="hint_text_2"><p>请注意以下事项：</p><li>1、下单价：<em>'+_order.goods_price+'</em>，请在下单页面核对下单价是否一致。</li><li>2、试用完成后将返还试用金：<em>'+_order.goods_price+'</em> 美元。</li>';
					if (_order.goods_tryproduct > 0 ) {
					buy_succes += '<li>3、最终试用商品：<em>'+_order.goods_tryproduct+'</em></li>';
					};
					buy_succes += '<li>注意：获得资格后<em>'+ act_config.buyer_write_order_time+'小时内</em>不下单付款并返回填写订单号，本次订单将自动关闭。</li></ul></div>';
					break;
				case 'qrcode': //二维码下单
				  	buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=82" target="_blank">二维码下单帮助</a></p><font class="font f_bg_yes font_2">恭喜您，已获得试用资格！请扫描以下二维码</font>';
				  	buy_succes += '<img src="'+_order.pro.goods_rule.qrcode+'" alt="" class="QR_code" /><ul class="hint_text_2"><p>请注意以下事项：</p><li>1、下单价：<em>'+_order.goods_price+'</em> 美元，请在下单页面核对下单信息是否一致。</li><li>2、试用完成后将返还试用金：<em>'+_order.goods_price+'</em> 美元。</li>';
				  	if (_order.goods_tryproduct > 0 ) {
					buy_succes += '<li>3、最终试用商品：<em>'+_order.goods_tryproduct+'</em></li>';
					};
				  	buy_succes += '<li>注意：获得资格后<em>'+ act_config.buyer_write_order_time+'小时内</em>不下单付款并返回填写订单号，本次订单将自动关闭。</li></ul></div>';
					break;
				case 'search': //搜索下单
					buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=80" target="_blank">搜索下单常见问题</a></p>';
					buy_succes += '<font class="font f_bg_yes font_2">恭喜您，已获得试用资格！请使用搜索方式下单</font>';
					buy_succes += '<ul class="hint_text_2 margin_b_10"><p>搜索下单提示：</p>';
					
					buy_succes += '<li>1、进入<em><a href="'+_order.shop_source.url+'" target="_blank">'+_order.shop_source.name+'网首页</a></em>，搜索关键词：<b style="color:red;">'+_order.pro.goods_rule.keyword+'</b>。</li>';
				//	buy_succes += '<li>2、请使用<em>Ctrl+F 查找</em>旺旺名称:'+_order.contact_want+';进入店铺找到活动宝贝，然后进行下单。</li>';
				
					buy_succes += '<li>2、搜索提示：<span style="color:red;">'+ _order.pro.goods_rule.keyword2 +'</li>';
					if (_order.goods_tryproduct > 0 ) {
					buy_succes += '<li>3、最终试用商品：<em>'+_order.goods_tryproduct+'</em></li></ul>';
					};
					buy_succes += '<ul class="hint_text_2"><p>请注意以下事项：</p>';
					buy_succes += '<li>1、下单价：<em>'+_order.goods_price+'</em> 美元，请在下单页面核对下单价是否一致。</li>';
					buy_succes += '<li>2、试用完成后将返还试用金：<em>'+_order.goods_price+'</em> 美元。</li>';
					buy_succes += '<li>注意：获得资格后<em>'+ act_config.buyer_write_order_time +'小时内</em>不下单付款并返回填写订单号，本次订单将自动关闭。</li></ul></div>';
					break;
				case 'ask' : //问答下单
					// buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=81" target="_blank">答案下单常见问题</a></p><font class="font f_bg_yes font_2">抢购成功，请确认以下优惠哦！</font><ul class="hint_text_2"><p>请注意以下事项：</p><li>1、下单价：<em>'+_order.goods_price+'</em>，请在下单页面核对下单价是否一致。</li><li>2、返还划算金：<em>'+(_order.pro.goods_price-_order.pro.goods_price*_order.pro.goods_discount/10).toFixed(2)+'</em> 美元，交易完成后，将返还给您金额。</li><li>注意：报名抢购后<em>'+ act_config.buyer_write_order_time+'分钟内</em>不下单付款并返回填写订单号，本次订单将自动关闭。</li></ul></div>';
					buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=81" target="_blank">答案下单常见问题</a></p><font class="font f_bg_yes font_2">恭喜您，已获得试用资格，请确认以下优惠哦！</font><ul class="hint_text_2"><p>请注意以下事项：</p><li>1、下单价：<em>'+_order.goods_price+'</em>，请在下单页面核对下单价是否一致。</li><li>2、返还划算金：<em>'+_order.goods_price+'</em> 美元，交易完成后，将返还给您金额。</li>';
					if (_order.goods_tryproduct > 0 ) {
					buy_succes += '<li>3、最终试用商品：<em>'+_order.goods_tryproduct+'</em></li>';
					};
					buy_succes += '<li>注意：报名抢购后<em>'+ act_config.buyer_write_order_time+'分钟内</em>不下单付款并返回填写订单号，本次订单将自动关闭。</li></ul></div>';
					break;
			};
			if (_order.type == 'search') {
				okval = '去搜索下单';
			}else if (_order.type == 'qrcode') {
				okval = '已下单去填写订单号';
			}else{
				okval = '抢购成功，现在马上去购买';
			}
			art.dialog({
				lock : true,
				fixed : true,
				id:'go_url',
				title : '商品下单提示',
				content : buy_succes,
				okVal : okval,
				ok : function() {
					if (_order.type != 'qrcode' && _order.type != 'search'){
						window.open(_order.pro.goods_url);
					}
					var diy_form='<form><table>'
								 + 		'<colgroup><col style="width:60px"><col style="width:350px"></colgroup>'
								 + 		'<tbody style="color:#797979;">'
								 + 			'<tr>'
								 + 				'<td>商品名称：</td>'
								 + 				'<td style="color: #06f"><a href="'+_order.pro.url+'" target="_blank" style="color:#09f;">'+ _order.title +'</a></td>'
								 + 			'</tr>'
								 + 			'<tr>'
								 + 				'<td>下单价：</td>'
								 + 				'<td style="font-weight:bold;color:#000;">$'+ _order.pro.goods_price +'</td>'
								 + 			'</tr>'
								 + 			'<tr>'
								 + 				'<td>订单编号：</td>'
								 + 				'<td><input type="text" name="order_sn" class="ui-form-text ui-form-textRed"/><span class="trade-no-msg" style="color: #cc0000;"></span></td>'
								 + 			'</tr>'
								 + 			'<tr>'
								 + 				'<td style="vertical-align: top;">温馨提示：</td>'
								 +				'<td style="color:#999;">'
								 +					'<p>1、请填写已付款的订单编号，若填入未付款单号，属于违规行为且将无法获得返款；<a style="color:#09f;" href="'+site.site_root+'/help/show/?catid=59&id=143" target="_blank">填写的订单号规则？</a></p>'
								 +					'<p>2、若单号被审核有误，请在 小时内进行申诉或修改，逾期将无法领回划算金（建议平时经常登录网站查看站内信提醒哦！）<a style="color:#09f;display:block;" href="'+site.site_root+'/help/show/?catid=6&id=43" target="_blank">如何获取订单编号？</a></p>'
								 +				'</td>'
								 + 			'</tr>'
								 + 		'</tbody>'
								 + '</table></form>';
					art.dialog({
						lock	: true,
						fixed	: true,
						title	: '填写亚马逊订单号',
						content	: diy_form,
						init	: function(){
							var dialog = this;
							var form  = this.DOM.content.find('form');	
							var trade = form.find('input[name=order_sn]');
							var noMsg = form.find('.trade-no-msg').html('');
							form.submit(function(){
								var order_sn_val   = $.trim( trade.val() );
								var data = {
									order_sn:order_sn_val,
									order_id:_order.oid
								};
								noMsg.css('color', '#c00').html('');
								var re = new RegExp("^[0-9]*$");
								if ( order_sn_val=='' ) {
									noMsg.html(' 请输入订单号');
									trade.val('').focus();
									return false;
								}else if ( re.test( order_sn_val ) === false ) {
									noMsg.html(' 订单号为纯数字');
									trade.val('').focus();
									return false;
								}else {
									noMsg.html('');
								}
								set_btn_state(dialog, false); 
								noMsg.css('color', '#666').html(" 正在发送...");
								$.post(site.site_root + '/index.php?m=order&c=api&a=fill_sn', data, function(ret) {
									set_btn_state(dialog, true);
									if (ret.status==1) {
										dialog.close();
										art.dialog({
											lock : true,
											fixed : true,
											title : '填写单号成功',
											content : '商家将在交易完成后审核返款。',
											ok : function() {
												location.reload();
											}
										});
									} else {
										noMsg.html(ret.info).css('color', '#c00');
									}
								});
								return false;
							});
							trade.focus();
						},
						ok		: function(){
							var form = this.DOM.content.find('form').submit();
							return false;
						},
						cancel : true
					});
					this.close();
				},
				cancelVal:'继续逛逛',
				cancel:function() {return true;}
			});			
		},
		error:function (msg) {
			art.dialog({
				lock: true,
				fixed: true,
				icon: 'face-sad',
				title: '错误提示',
				content: msg,
				ok: true
			});
		},
		succes:function (msg) {
			art.dialog({
				lock: true,
				fixed: true,
				icon: 'face-smile',
				title: '提示',
				content: msg,
				ok: true
			});
		},
		/* 填写订单号 [Adalways试用系统] */
		fill_trade_no : function(oid, cause, in_time, order_fill_interval, get_order_number_help) {
			var order = get_order(oid);
			// 根据控制显示验证码的参数判断表单是否需要验证码
			var is_captcha = 0;
			var diy_form = '<form><table>'
						 + 		'<colgroup><col style="width:60px"><col style="width:350px"></colgroup>'
						 + 		'<tbody style="color:#797979;">'
						 + 			'<tr>'
						 + 				'<td>商品名称：</td>'
						 + 				'<td style="color: #06f">'+ order.title +'</td>'
						 + 			'</tr>'
						 + 			'<tr>'
						 + 				'<td>下单价：</td>'
						 + 				'<td style="font-weight:bold;color:#000;">$'+ order.price +'</td>'
						 + 			'</tr>'
						 + 			'<tr>'
						 + 				'<td>订单编号：</td>'
						 + 				'<td><input type="text" name="order_sn" class="ui-form-text ui-form-textRed" /><span class="trade-no-msg" style="color: #cc0000;"></span></td>'
						 + 			'</tr>'
						 + 			'<tr>'
						 + 				'<td style="vertical-align: top;">温馨提示：</td>'
						 +				'<td style="color:#999;">'
						 +					'<p>1、请填写已付款的订单编号，若填入未付款单号，属于违规行为且将无法获得返款；<a style="color:#09f;" href="'+site.site_root+'/help/show/?catid=59&id=143 " target="_blank">填写的订单号规则？</a></p>'
						 +					'<p>2、若单号被审核有误，请在 小时内进行申诉或修改，逾期将无法领回划算金（建议平时经常登录网站查看站内信提醒哦！）<a style="color:#09f;display:block;" href="'+site.site_root+'/help/show/?catid=6&id=43" target="_blank">如何获取订单编号？</a></p>'
						 +				'</td>'
						 + 			'</tr>'
						 + 		'</tbody>'
						 + '</table></form>';

			art.dialog({
				lock	: true,
				fixed	: true,
				id		: 'fill_trade_no',
				title	: '填写亚马逊订单号',
				content	: diy_form,
				init	: function(){
					var dialog = this;
					var form  = this.DOM.content.find('form');	
					var trade = form.find('input[name=order_sn]');
					var noMsg = form.find('.trade-no-msg').html('');
					form.submit(function(){
						var order_sn_val   = $.trim( trade.val() );
						var data = {
							order_sn:order_sn_val,
							order_id:order.oid
						};
						noMsg.css('color', '#c00').html('');
						var re = new RegExp("^[0-9]*$");
						if ( order_sn_val=='' ) {
							noMsg.html(' 请输入订单号');
							trade.val('').focus();
							return false;
						}else if ( re.test( order_sn_val ) === false ) {
							noMsg.html(' 请输入订单号为纯数字');
							trade.val('').focus();
							return false;
						}else {
							noMsg.html('');
						}
						set_btn_state(dialog, false); 
						noMsg.css('color', '#666').html(" 正在发送...");
						$.post('index.php?m=order&c=api&a=fill_sn', data, function(ret) {
							set_btn_state(dialog, true);
							if (ret.status==1) {
								dialog.close();
								art.dialog({
									lock : true,
									fixed : true,
									title : '填写单号成功',
									content : '商家将在交易完成后审核返款。',
									ok : function() {
										location.reload();
									}
								});
							} else {
								noMsg.html(ret.info).css('color', '#c00');
							}
						});
						return false;
					});
					trade.focus();
				},
				ok		: function(){
					var form = this.DOM.content.find('form').submit();
					return false;
				},
				cancel : true
			});
		},
		appeal_reply : function(id){
			$.get('/order_appeal/reply/' + id, function(ret) {
				art.dialog({
					fixed : true,
					title : '回应申诉',
					drag : true,
					content : '弹窗内容',
					lock : true,
					id:'appeal_reply',
					padding : 0,
					content : ret,
					okVal:'提交',
					cancel:true,
					cancelVal:'关闭',
					init : function() {
						var self = this;
						this.DOM.content.find('form').submit(function(){self._click('提交');return false;});
					},
					ok : function() {
						var win = this;
						var appeal_id = $('#appeal_id').val();
						var form = this.DOM.content.find('form');
						var text = form.find('textarea').val();
						if ($.trim(text) == '') {
							alert('回应内容不能为空！');
							form.find('textarea').val('').focus();
							return false;
						}
						if ( text.length > 500){
							alert('回应内容不能超过500个字！');
							form.find('textarea').focus();
							return false;
						}
						
						if($("input[name^='img_']").length == 0 || $("input[name='img_1']").val() == false ){
							alert('请提交凭证图片！');
							return false;
						}

						set_btn_state(win, false);
						form.ajaxSubmit({
							type : "post",
							url : "/order_appeal/reply_post/" + appeal_id,
							dataType : 'json',
							success : function(ret) {
								set_btn_state(win, true);
								if (ret.success) {
									alert('回应申诉成功，请等待管理员处理。');
									win.close();
									location.reload();
									return;
								}
								alert(ret.data);
							}
						});
						return false;
					}
				});
			});
		},
		view_appeal : function(appeal_id,url,modelid,status) {
			$.post(url,{
				appeal_id : appeal_id
			}, function(ret) {
				if (modelid == 2) {
					if (status > 1) {
						art.dialog({
							title : '查看申诉',
							drag : true,
							lock : true,
							id:'view_appeal',
							padding : 0,
							content : ret,
							cancel : true
						});
					}else{
						art.dialog({
							title : '查看申诉',
							drag : true,
							lock : true,
							id:'view_appeal',
							padding : 0,
							content : ret,
							okVal : '点此去处理',
							ok : function(){
								window.open(site.site_root+'index.php?m=Member&c=Appeal&a=appeal_seller&appeal_id='+appeal_id);
							},
							cancel : true
						});
					}					
				}else{
					art.dialog({
						title : '查看申诉',
						drag : true,
						lock : true,
						id:'view_appeal',
						padding : 0,
						content : ret,
						cancel : true
					});
				}				
			});
		},
		/* 我要晒单 */
		add_show : function(oid) {
			var order = get_order(oid);
			var html = [];
			html.push('<form class="woyao-show" method="post" enctype="multipart/form-data"><table>');
			html.push('<tr><th>标题:</th><td>', order.title, '</td></tr>');
			html.push('<input id="file_upload" name="Filedata" type="file" style="display:none;" onchange = "return ajaxFileUploadaa()"/>');
			html.push('<tr><th>照片:</th><td><input type="button" id="image" name="report_imgs" value="上 传 图 片"/></td></tr>');
			html.push('<tr style="display:none;" id="url"><th>提示:</th><td><span id="notice"></span><span id="file_url" style="display:none;"></span></td></tr>');
			html.push('<tr><th>评价:</td><td><textarea name="content" rows="5" cols="30"></textarea></td></tr>');
			html.push('</table></form>');
			art.dialog({
				lock : true,
				fixed : true,
				title : '我要晒单',
				id:'add_show',
				content : html.join(''),
				init : function() {
					var win = this;
					var form = this.DOM.content.find('form');
					$("#image").click(function (){
						$("#file_upload").click();
					});
					form.submit(function() {
						var imgs = $('#file_url').html();
						var file = form.find('input[name=report_imgs]').val();
						if (!imgs || !imgs.match(/.jpg|.gif|.png|.bmp/i)) {
							error_no('请上传商品实拍照片！');
							return false;
						}
						var text = form.find('textarea').val();
						if ($.trim(text) == '') {
							error_no('评价内容不能为空！');
							form.find('textarea').val('').focus();
							return false;
						}
						set_btn_state(win, false);
						$.ajax({
							type : "POST",
							url  : site.site_root+"index.php?m=order&c=api&a=add_show",
							data : {oid:oid,report_imgs:imgs,content:text},
							dataType : 'JSON',
							error:function(ret){
								set_btn_state(win, true);
								error_no('系统错误，请稍后重试。');
							},
							success : function(ret) {
								set_btn_state(win, true);
								if (ret.status==1) {
									succes(ret.info);
									win.close();
									return;
								}
								succes(ret.info);
							}
						});
						return false;
					});
				},
				ok : function() {
					this.DOM.content.find('form').submit();
					return false;
				}
			});
		},
		/* 抢购记录 */
		view_log : function(oid) {
			var order = get_order(oid);
			$.post('?m=Member&c=Order&a=view_log', {
				oid:oid,
				userid:order.userid,
				modelid:order.modelid
			},function(ret) {
					var html = [];
					if (ret.status == 1) {
						for (var i = 0, l = ret.info.length; i < l; i++) {
							html.push('<tr><td>' + ret.info[i].inputtime + '</td><td>' + ret.info[i].cause + '</td></tr>');
						}
						html = '<div class="operate-log"><table class="ui-table"><col style="width:160px;" /><col style="width:30em;" /><tr><th>日期</th><th>内容</th></tr>' + html.join('') + '</table></div>';
						art.dialog({
							lock : true,
							fixed : true,
							title : '操作记录',
							id:'view_log',
							content : html,
							ok : true
						});
					} else {
						html = error(ret.info);
					}
				},'JSON'
			);
		},

		/* 黑名单 */
		blacklist : function(oid) {
			var order = get_order(oid);
			$.post('?m=Member&c=Order&a=blacklist', {
				order_id:oid,
			},function(ret) {
					var html = [];
					if (ret.status == 1) {
						art.dialog({
							lock : true,
							fixed : true,
							title : '黑名单',
							content : ret.info,
							ok : true
						});
					} else {
						html = error(ret.info);
					}
				},'JSON'
			);
		},
		/* 关闭订单 */
		close : function(oid){
			//订单在待填写单号情况下关闭订单
			var order = get_order(oid);
			art.dialog({
				lock : true,
				fixed : true,
				id : 'close',
				icon  : 'question',
				title : '关闭订单提示',
				content : '关闭后将不能填写订单号，确定要关闭么？',
				cancel:true,
				ok : function(){
					var dialog = this;
					set_btn_state(dialog, false);
					$.ajax({
						url		: site.site_root+'?m=member&c=order&a=close',
						data    : {oid:oid,userid:order.userid},
						type	: 'post',
						dataType: 'json',
						error	: function(){
							dialog.close();
							art.dialog({
								lock : true,
								fixed : true,
								icon  : 'error',
								title : '错误提示',
								content : '网络连接失败！',
								ok : true
							});
						},
						success	: function(ret){
							dialog.close();
							isSuccess = (ret.status==1) ? 1 : 0;
							art.dialog({
								lock : true,
								fixed : true,
								icon  : ['error','succeed'][isSuccess],
								title : '订单关闭'+['失败','成功'][isSuccess],
								content : ret.info,
								ok : true,
								close:function(){
									if(isSuccess){
										location.reload();
									}
								}
							});
						}
					});
					return false;
				}
			});
		},
		/* 关闭申诉 */
		close_appeal : function(aid,oid){
			var order = get_order(oid);
			art.dialog({
				lock : true,
				fixed : true,
				icon  : 'question',
				title : '关闭申诉提示',
				content : '关闭后该申诉自动结束且订单为之前的状态(审核失败)，确定要关闭么？',
				cancel:true,
				ok : function(){
					var dialog = this;
					set_btn_state(dialog, false);
					$.ajax({
						url		: site.site_root+'?m=member&c=appeal&a=close',
						data    : {aid:aid,userid:order.userid,oid:oid},
						type	: 'post',
						dataType: 'json',
						error	: function(){
							dialog.close();
							art.dialog({
								lock : true,
								fixed : true,
								icon  : 'error',
								title : '错误提示',
								content : '网络连接失败！',
								ok : true
							});
						},
						success	: function(ret){
							dialog.close();
							isSuccess = (ret.status==1) ? 1 : 0;
							art.dialog({
								lock : true,
								fixed : true,
								icon  : ['error','succeed'][isSuccess],
								title : '申诉关闭'+['失败','成功'][isSuccess],
								content : ret.info,
								ok : true,
								close:function(){
									if(isSuccess){
										location.reload();
									}
								}
							});
						}
					});
					return false;
				}
			});
		},
		user_edit_trade_no :function(oid){
			// 修改单号
			var order = get_order(oid);
			var diy_form = '<form><table>'
						 + 		'<colgroup><col style="width:60px"><col style="width:350px"></colgroup>'
						 + 		'<tbody style="color:#797979;">'
						 + 			'<tr>'
						 + 				'<td>商品名称：</td>'
						 + 				'<td style="color: #06f">'+ order.title +'</td>'
						 + 			'</tr>'
						 + 			'<tr>'
						 + 				'<td>下单价：</td>'
						 + 				'<td style="font-weight:bold;color:#000;">$'+ order.price +'</td>'
						 + 			'</tr>'
						 + 			'<tr>'
						 + 				'<td>订单编号：</td>'
						 + 				'<td><input type="text" name="trade_no" class="ui-form-text ui-form-textRed" /><span class="trade-no-msg" style="color: #cc0000;"></span></td>'
						 + 			'</tr>'
						 + 			'<tr>'
						 + 				'<td style="vertical-align: top;">温馨提示：</td>'
						 +				'<td style="color:#999;">'
						 +					'<p>1、请填写已付款的订单编号，若填入未付款单号，属于违规行为且将无法获得返款；<a style="color:#09f;" href="http://help.zhonghuasuan.com/buyer/category/66/125/16" target="_blank">填写的订单号规则？</a></p>'
						 +					'<p>2、若单号被审核有误，请在 '+order_auto_close_time_hour+' 小时内进行申诉或修改，逾期将无法领回划算金（建议平时经常登录网站查看站内信提醒哦！）<a style="color:#09f;display:block;" href="http://help.zhonghuasuan.com/buyer/category/63/81/124" target="_blank">如何获取订单编号？</a></p>'
						 +				'</td>'
						 + 			'</tr>'
						 + 		'</tbody>'
						 + '</table></form>';
			art.dialog({
				lock : true,
				fixed : true,
				title : '修改订单号',
				id:'user_edit_trade_no',
				content : diy_form,
				init : function() {
					var win = this;
					var form = this.DOM.content.find('form');
					var trade_no = form.find(':text');
					var span = form.find('.trade-no-msg').hide();

					trade_no.focusin(function(){span.html('').hide();});
					form.submit(function() {
						var no = $.trim(trade_no.val());
						if (!no) {
							trade_no.val('').focus();
							return false;
						}
						if (/[^\-0-9a-zA-Z]/.test( no )){
							span.show().html('订单号有误');
							return false;
						}

						set_btn_state(win, false);
						$.getJSON('/order/user_edit_no/' + oid + '/' + no, function(ret) {
							set_btn_state(win, true);
							if (ret.success) {
								win.close();
								art.dialog({
									lock : true,
									fixed : true,
									icon  : 'succeed',
									title : '修改单号成功',
									content : '商家将在交易完成后审核返款。',
									ok : function() {
										location.reload();
									}
								});
							} else {
								switch(ret.data){
									case 'TRADE_NO_ERROR':
										span.show().html('订单号格式有误！');
										break;
									case 'NO_BIND_MOBILE':
										span.show().html('未认证手机号码，<a href="'+site('buyer')+'bind/mobile" target="_blank" style="color:#0066FF">现在去认证</a>');
										break;
									default:
										span.html(ret.data).show();
								}
							}
						});

						return false;
					});
					trade_no.focus();
				},
				ok : function() {
					var form = this.DOM.content.find('form').submit();
					return false;
				},
				cancel : true
			});
		},
		/* 查看买家信息  */
		userInfo : function(userid,id) {
			$.get(site.site_root+'index.php?m=member&c=order&a=userInfo',{
				userid : userid,
				id : id
			}, function(ret) {
				if (ret.status == 0 ) {
					order.error(ret.info);
				}else{
					art.dialog({
						title : '查看买家信息',
						drag : true,
						lock : true,
						id:'userInfo',
						padding : 0,
						content : ret,
						ok : true
					});
				}				
			});
		},
		/* 查看试用报告 */
		view_report : function(order_id,url) {
			$.post(url,{
				order_id : order_id
			}, function(ret) {
				art.dialog({
					title : '查看试用报告',
					drag : true,
					lock : true,
					id:'view_report',
					padding : 0,
					content : ret,
					ok : true
				});
			});
		},
		/* 试用报告评优 */
		pay_report : function(order_id,url) {
			art.dialog({
				id:'goods_log',
				lock : true,
				fixed : true,
				title : '操作记录',
				drag : false,
				init : function() {
					var win = this;
					$.get(url + '&order_id=' + order_id, function(ret) {
						if (typeof ret.status != 'underfind') {
							win.content( ret );
						} 
						if(ret.status == 0){
							win.close();
							art.dialog({
								title : '试用报告操作',
								drag : true,
								lock : true,
								content : ret.info,
								ok : function(){
									location.href=ret.url;
								}
							});
						}
					});
				},
				okVal:'确认(并付款)',
			    ok: function(topWin){
			    	//改变按钮的状态 disabled
			    	$(".aui_state_highlight").attr('disabled',true);
			    	var inputs = this.DOM.content.find('input');
					var pay_appraised = inputs[1].checked;
					var val           = inputs[2].value;
					var order_id      = inputs[3].value;
			    	$.post(site.site_root+'?m=member&c=order&a=pay_report',{
			    		pay_appraised : pay_appraised,
			    		val : val,
			    		order_id : order_id
			    	},function(ret){
			    		if (ret.status==1){
			    			succes(ret.info);
			    		}else{
			    			error(ret.info);
			    		}
			    	},'JSON');
			    	return false;
			    },
			    cancel:function(){return true;}
			});
//			art.dialog.load(url + '&order_id=' + order_id, {
//			    title: '试用报告操作',
//			    padding:0,
//			    lock:true,
//			    fixed:true,
//                okVal:'确认(并付款)',
//			    ok: function(topWin){
//			    	//改变按钮的状态 disabled
//			    	$(".aui_state_highlight").attr('disabled',true);
//			    	var inputs = this.DOM.content.find('input');
//					var pay_appraised = inputs[1].checked;
//					var val           = inputs[2].value;
//					var order_id      = inputs[3].value;
//			    	$.post(site.site_root+'?m=member&c=order&a=pay_report',{
//			    		pay_appraised : pay_appraised,
//			    		val : val,
//			    		order_id : order_id
//			    	},function(ret){
//			    		if (ret.status==1){
//			    			succes(ret.info);
//			    		}else{
//			    			error(ret.info);
//			    		}
//			    	},'JSON');
//			    	return false;
//			    },
//			    cancel:function(){return true;}			    
//			}, false);
		},
		appeal_cancel : function(oid,appeal_id){
			//撤销申诉
			art.dialog({
				lock : true,
				fixed : true,
				icon  : 'question',
				title : '撤销申诉提示',
				content : '撤销审核后抢购状将恢复到申诉前的状态；确定要撤销申诉？',
				cancel:true,
				ok : function(){
					var dialog = this;
					set_btn_state(dialog, false);
					$.ajax({
						url		: '/order_appeal/cancel/'+oid+'/'+appeal_id,
						type	: 'GET',
						dataType: 'json',
						error	: function(){
							dialog.close();
							art.dialog({
								lock  : true,
								fixed : true,
								icon  : 'error',
								title : '错误提示',
								content : '网络连接失败！',
								ok : true
							});
						},
						success	: function(ret){
							dialog.close();
							isSuccess = ret.success ? 1 : 0;
							art.dialog({
								lock  : true,
								fixed : true,
								icon  : ['error','succeed'][isSuccess],
								title : '申诉撤销'+['失败','成功'][isSuccess],
								content : ret.data,
								ok : true,
								close:function(){
									if(isSuccess){
										location.reload();
									}
								}
							});
						}
					});
					return false;
				}
			});
		},
		/* 商家审核失败操作 */
		refuse : function(oid) {
			var order = get_order(oid);
			var html = [];
			html.push('<form class="woyao-show" method="post" enctype="multipart/form-data"><table>');
			html.push('<tr><th>标题:</th><td>', order.title, '</td></tr><br/><br/>');
			html.push('<tr><th>原因:</td><td><br/><textarea name="content" rows="6" cols="45"></textarea></td></tr>');
			html.push('</table></form>');
			art.dialog({
				lock : true,
				fixed : true,
				title : '审核失败理由',
				id:'refuse',
				content : html.join(''),
				init : function() {
					var win = this;
					var form = this.DOM.content.find('form');
					$("#image").click(function (){
						$("#file_upload").click();
					});
					form.submit(function() {
						var text = form.find('textarea').val();
						if ($.trim(text) == '') {
							error_no('原因不能为空！');
							form.find('textarea').val('').focus();
							return false;
						}
						set_btn_state(win, false);
						$.ajax({
							type : "POST",
							url  : site.site_root+"index.php?m=member&c=order&a=refuse",
							data : {oid:oid,content:text},
							dataType : 'JSON',
							error:function(ret){
								set_btn_state(win, true);
								error_no('系统错误，请稍后重试。');
							},
							success : function(ret) {
								set_btn_state(win, true);
								if (ret.status==1) {
									succes(ret.info);
									win.close();
									return;
								}else{
									error(ret.info);
								}
							}
						});
						return false;
					});
				},
				ok : function() {
					this.DOM.content.find('form').submit();
					return false;
				},
				cancel : '取消'
			});
		},
		/* 追加商品库存 [kza] */
		push_number : function(pid,url) {
			art.dialog.load(url + '&pid=' + pid, {
			    title: '追加商品库存',
			    padding:0,
			    lock:true,
			    id:'push_number',
			    fixed:true,
                okVal:'确认(去付款)',
			    ok: function(topWin){
			    	var inputs = this.DOM.content.find('input');
					var com_day 	= parseInt(inputs[0].value,10);
					var com_number  = parseInt(inputs[1].value,10);
			    	$.post(site.site_root+'?m=member&c=merchantProduct&a=push_number',{
			    		pid : pid,
			    		com_day : com_day,
			    		com_number : com_number
			    	},function(ret){
			    		if (ret.status == 1){
			    			window.location = ret.url;
			    		}else{
			    			error(ret.info);
			    		}
			    	},'JSON');
			    	return false;
			    },
			    cancel:function(){return true;}			    
			}, false);
		}
	};
})();

// 订单倒计时
$(function() {
	function count_down(sec) {
		if(sec<=0) return '-';
		var s = sec;
		var left_s = s % 60;
		var m = Math.floor(s / 60);
		var left_m = m % 60;
		var h = Math.floor(m / 60);
		var left_h = h % 24;
		var d = Math.floor(h / 24);

		var ret = [];
		d && ret.push('<span class="d">', d, '</span>天');
		left_h && ret.push('<span class="h">', left_h, '</span>时');
		left_m && ret.push('<span class="m">', left_m, '</span>分');
		left_s && ret.push('<span class="s">', left_s, '</span>秒');

		return ret.join('');
	}
});
