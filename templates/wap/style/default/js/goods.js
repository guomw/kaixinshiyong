/* 商品相关 */
var page_detail = (function() {
	var oIntro = oBuyer = oBuyer_navs = oBuyer_conts = oTime = null;
	var buy_num = 0;
    var timestamp = parseInt($.now() / 1000);
    var isLoadReport = isLoadBuyer = true;
	var text_status = {	//	状态
		't-3': '待审核(待付款)',
		't-2': '待审核(已付款)',
		't-1': '审核通过(待上线)',
		't0': '审核失败(已退款)',
		't1': '我要抢购',
		't2': '活动结束(结算中)',
		't3': '活动结束(已结算)',
		't4': '已撤销',
		't5': '已屏蔽'
	};
	var _answer = 0;
    var pages = {
        buyer_pages:1,
        report_pages:1
    };

    function error(msg) {	//	错误提示
//		art.dialog({
//			lock: true,
//			fixed: true,
//			icon: 'error',
//			title: '错误提示',
//			content: msg,
//			ok: true
//		});
        $("#msg").html(msg);
    }

    /* 活动剩余时 */
    function show_remain_time(sec) {
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

    /* 开抢倒计时 */
    function show_online_time(start_sec, obj) {
        var timer = null;
        timer = setInterval(function() {
            var remain_sec = start_sec - timestamp;
            if (remain_sec <= 0) {
                obj.html('0');
                clearInterval(timer);
                oIntro.find('.timeRemaining-tit').html("剩余时间");
                btn_buy.removeClass('btn_no').addClass('btn_yes').text('我要抢购');
                show_remain_time(goods.first_days);
                return;
            }
            obj.html(count_down(remain_sec));
        }, 1000);
    }

    function time_pad(s) {
        return Number(s) > 9 ? String(s) : "0" + String(s);
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
        d && ret.push(d, '天');
        left_h && ret.push(time_pad(left_h), '时');
        left_m && ret.push(time_pad(left_m), '分');
        left_s && ret.push(time_pad(left_s), '秒');
        return ret.join('');
    }

    function format_date(ts) {
        function _pad(n, c) {
            n = n.toString();
            return n.length < c ? _pad('0' + n, c, '0') : n;
        }
        var dt = new Date(ts);
        return _pad(dt.getMonth() + 1, 2) + '月' + _pad(dt.getDate(), 2) + '日 ' + _pad(dt.getHours(), 2) + '时' + _pad(dt.getMinutes(), 2) + '分';
    }

    function set_btn_state(win, can_use) {
        if (can_use) {
            win.DOM.buttons.find('button:first').html('确定').removeAttr('disabled').addClass('aui_state_highlight');
        } else {
            win.DOM.buttons.find('button:first').html('操作中...').attr('disabled', 'disabled').removeClass('aui_state_highlight');
        }
    }

	return {
		/* 初始化 */
		init: function() {
			if (typeof(goods) != 'object') {
				alert('无法读取此商品信息！');
				location.href = '/';
				return;
			}
            var remain_time = goods.end_time - timestamp;
            oIntro = $('#shop_text');
            var btn_buy = oIntro.find('a#btn_buy');
			oTime = oIntro.find('.timeRemaining-cont > font');
			// 当前抢购按钮
			btn_text = text_status['t' + goods.status];
			if (goods.status != 1){
				btn_buy.text(btn_text).removeClass('shop_buy_btn_on').addClass('shop_buy_btn_off');
			} else {
                /* 判断库存 */
                if(goods.goods_stock < 1) {
                    btn_buy.text('已被售罄').removeClass('shop_buy_btn_on').addClass('shop_buy_btn_off');
                } else {
                    if(timestamp < goods.start_time) {
                        if(goods.start_time - timestamp >= 3600) {
                            oIntro.find('.timeRemaining-cont > span').text('开抢时间');
                            oTime.html(format_date(goods.start_time * 1000));
                        } else {
                            oIntro.find('.timeRemaining-cont > span').text("开抢倒计时");
                            show_online_time(goods.start_time, oTime);
                        }
                        btn_buy.text('尚未开始').removeClass('shop_buy_btn_on').addClass('shop_buy_btn_off');
                    } else if(timestamp > goods.end_time) {
                        btn_buy.text('已经结束').removeClass('shop_buy_btn_on').addClass('shop_buy_btn_off');
                    } else {
                    	/* 获取接口信息 */
						if (parseInt(site.user.userid) > 0 ){
							$.getJSON(site.site_root+'/index.php?m=order&c=api&a=order_infos&pid='+goods.id,{
								pid : goods.id
							},function(ret){
								if(ret.status == 1 && (parseInt(ret.info.not_over) > 0 || parseInt(goods.buyer_good_buy_times) <= parseInt(ret.info.over_order))) {
                                    btn_buy.text('已抢购').removeClass('btn_no').addClass('shop_buy_btn_off');
								}else{
									btn_buy.text('立即抢购').removeClass('shop_buy_btn_off').addClass('shop_buy_btn_on');
                        			show_remain_time(goods.end_time - timestamp);
								}
							});
						}else{
							btn_buy.text('立即抢购').removeClass('shop_buy_btn_off').addClass('shop_buy_btn_on');
                        	show_remain_time(goods.end_time - timestamp);
						}
                    }
                }
			}
            // 抢购列表
            $("#report_count").text(goods.report_count);
            $("#buyer_count").text(goods.buyer_count);
		},

		error:function (msg) {
//			art.dialog({
//				lock: true,
//				fixed: true,
//				icon: 'face-sad',
//				title: '错误提示',
//				content: msg,
//				ok: true
//			});
            $("#msg").html(msg)
		},
		succes:function (msg) {
//			art.dialog({
//				lock: true,
//				fixed: true,
//				icon: 'face-smile',
//				title: '提示',
//				content: msg,
//				ok: true
//			});
            $("#msg").html(msg)
		},
		/* 校验答案 */
		answer: function(){
            location.href = site.site_root+'/index.php?m=member&c=order&a=answer&goods_id='+goods.id
//			var ask_html = '<div class="CPM_style CPM_style_2 border_radius_5" style="position: static;">';
//				ask_html += '<p class="issue"><a href="'+site.site_root+'/help/?catid=81" target="_blank">答案下单帮助</a></p>';
//				ask_html += '<font class="font f_bg_yes font_2">您需要回答问题才能完成抢购</font>';
//				ask_html += '<ul class="set_iss"><li>问题：<em>'+goods.goods_rule.ask.question+'？</em></li>';
//				if (goods.goods_rule.ask.tips != null) {
//					ask_html += '<li>提示：<em>'+goods.goods_rule.ask.tips+'</em></li>';
//				}
//				ask_html += '<li><font>答案：</font><input type="text" name="answer" id="answer"/><img src="'+site.template_img+'/verify_btn1.png" alt="" id="right" style="display:none;"/>';
//				ask_html += '<span id="error" style="display:none;"><img src="'+site.template_img+'/verify_btn2.png" alt=""/>回答问题错误！</span></li></ul><p class="iss_hint">提示：您可以在商家宝贝详情页查看宝贝介绍可以找到相关答案。<a href="'+site.site_root+goods.goods_url+'" target="_blank">去找找看</a></p></div>';
//			art.dialog({
//				title:'校验答案',
//				fixed:true,
//				width:500,
//				lock:true,
//				content:ask_html,
//				okVal:'验证答案',
//				ok:function(){
//					if (goods.goods_rule.ask.answer == $.trim($("#answer").val())) {
//						$('#right').attr('style','display:');
//						$('#error').attr('style','display:none');
//						page_detail.do_buy();
//						_answer = 1;
//						return true;
//					}else{
//						$('#right').attr('style','display:none');
//						$('#error').attr('style','display:');
//						return false;
//					}
//
//				},
//				cancelVal:'取消',
//				cancel:function() {return true},
//			})
//			return _answer;
			return false;
		},
		/* 抢购入口 */
		buy: function() {
			if($('a#btn_buy').hasClass('shop_buy_btn_off')) return false;
			if (site.user.length < 1) {	// 未登录
//				member.login();
                location.href = login;
                return false;
			}
			// 答案下单先校验答案
			if (goods.type == 'ask') {
				page_detail.answer();
				return false;
			}
			page_detail.do_buy();
		},
		/* 执行抢购 */
		do_buy : function(){
			var okval =  '';
			if (goods.type == 'search') {
				okval = '去搜索下单';
			}else if (goods.type == 'qrcode') {
				okval = '已下单去填写订单号';
			}else{
				okval = '抢购成功，现在马上去购买';
			}

            //返回顶部
            $('.set-top').click();

			// 抢购成功
			$.post(site.site_root + '/index.php?m=product&c=api&a=pay_submit',{
				goods_id : goods.id
			},function(ret){
				if (ret.status==1){
//					art.dialog({
//						title:'抢购成功',
//						fixed:true,
//						lock:true,
//						content:buy_succes,
//						okVal:okval,
//						ok:function() {
//							if (goods.type != 'qrcode' && goods.type != 'search'){
//								window.open(goods.goods_url);
//							}
//							var diy_form='<form><table>'
//										 + 		'<colgroup><col style="width:60px"><col style="width:350px"></colgroup>'
//										 + 		'<tbody style="color:#797979;">'
//										 + 			'<tr>'
//										 + 				'<td>商品名称：</td>'
//										 + 				'<td style="color: #06f"><a href="'+goods.url+'" target="_blank" style="color:#09f;">'+ goods.title +'</a></td>'
//										 + 			'</tr>'
//										 + 			'<tr>'
//										 + 				'<td>下单价：</td>'
//										 + 				'<td style="font-weight:bold;color:#000;">$'+ goods.goods_price +'</td>'
//										 + 			'</tr>'
//										 + 			'<tr>'
//										 + 				'<td>订单编号：</td>'
//										 + 				'<td><input type="text" name="order_sn" class="ui-form-text ui-form-textRed" maxlength="15"/><span class="trade-no-msg" style="color: #cc0000;"></span></td>'
//										 + 			'</tr>'
//										 + 			'<tr>'
//										 + 				'<td style="vertical-align: top;">温馨提示：</td>'
//										 +				'<td style="color:#999;">'
//										 +					'<p>1、请填写已付款的订单编号，若填入未付款单号，属于违规行为且将无法获得返款；<a style="color:#09f;" href="'+site.site_root+'/help/show/?catid=59&id=143" target="_blank">填写的订单号规则？</a></p>'
//										 +					'<p>2、若单号被审核有误，请在 小时内进行申诉或修改，逾期将无法领回划算金（建议平时经常登录网站查看站内信提醒哦！）<a style="color:#09f;display:block;" href="'+site.site_root+'/help/show/?catid=6&id=43" target="_blank">如何获取订单编号？</a></p>'
//										 +				'</td>'
//										 + 			'</tr>'
//										 + 		'</tbody>'
//										 + '</table></form>';
//							art.dialog({
//								lock	: true,
//								fixed	: true,
//								title	: '填写亚马逊订单号',
//								content	: diy_form,
//								init	: function(){
//									var dialog = this;
//									var form  = this.DOM.content.find('form');
//									var trade = form.find('input[name=order_sn]');
//									var noMsg = form.find('.trade-no-msg').html('');
//									form.submit(function(){
//										var order_sn_val   = $.trim( trade.val() );
//										var data = {
//											order_sn:order_sn_val,
//											order_id:ret.info.oid
//										};
//										noMsg.css('color', '#c00').html('');
//										var re = new RegExp("^\\d{15}$");
//										if ( order_sn_val=='' ) {
//											noMsg.html(' 请输入订单号');
//											trade.val('').focus();
//											return false;
//										}else if ( re.test( order_sn_val ) === false ) {
//											noMsg.html(' 订单号为15位纯数字');
//											trade.val('').focus();
//											return false;
//										}else {
//											noMsg.html('');
//										}
//										set_btn_state(dialog, false);
//										noMsg.css('color', '#666').html(" 正在发送...");
//										$.post(site.site_root + '/index.php?m=order&c=api&a=fill_sn', data, function(ret) {
//											set_btn_state(dialog, true);
//											if (ret.status==1) {
//												dialog.close();
//												art.dialog({
//													lock : true,
//													fixed : true,
//													title : '填写单号成功',
//													content : '商家将在交易完成后审核返款。',
//													ok : function() {
//														location.reload();
//													}
//												});
//											} else {
//												noMsg.html(ret.info).css('color', '#c00');
//											}
//										});
//										return false;
//									});
//									trade.focus();
//								},
//								ok		: function(){
//									var form = this.DOM.content.find('form').submit();
//									return false;
//								},
//								cancel : true,
//							});
//							this.close();
//						},
//						cancelVal:'继续逛逛',
//						cancel:function() {return true}
//					})
                    $("#msg").html(buy_succes);
                    oIntro = $('#shop_text');
                    var btn_buy = oIntro.find('a#btn_buy');
                    btn_buy.text('已抢购').removeClass('shop_buy_btn_on').addClass('shop_buy_btn_off');

                    if(goods.type != 'ask'){
                        location.href = site.site_root + '/index.php?m=Member&c=order&a=edit_ordernum&orderid='+ret.info.oid;
                    }

                }else{
					error(ret.info);
					return false;
				}
			},'JSON');
			// 抢购成功后的模版选择
			var buy_succes = '<div class="CPM_style CPM_style_2 border_radius_5" style="position: static;color: #bb000d">';
            // 下单方式
            switch(goods.type) {
                case 'general': //普通下单
                    buy_succes += '恭喜您，已抢购成功！</div>';
                    break;
                case 'qrcode': //二维码下单
                    buy_succes += '恭喜您，抢购成功，请扫描以下二维码:</div>';
                    buy_succes += '<div><img src="'+goods.goods_rule.qrcode+'" alt="" class="QR_code" style="width:50%"/></div>';
                    break;
                case 'search': //搜索下单
                    buy_succes += '恭喜您，已抢购成功，请使用搜索方式下单</div>';
                    break;
                case 'ask' : //问答下单
                    buy_succes += '恭喜您，已成功提交申请，请等待商家审核资格哦！</div>';
                    break;
            };
		},
        /* 谁抢到了 */
        buyer_list:function() {
        	if(isLoadBuyer == false){
                $("#end-hint-buyer").html('没有更多了...');
                return false;
            }
            $.getJSON(site.site_root + '/index.php?m=product&c=api&a=buyer_list', {
                goods_id:goods.id,
                limit:12,
                page:pages.buyer_pages
            }, function(ret) {
                if(ret.lists.length > 0) {
                    var _html = '';
                    $.each(ret.lists, function(i, n) {
                        _html += '<li class="fl w25 mt10">';
                        _html += '<span class="d-block"><img class="avatar w50" src="'+ n.avatar +'" /></span>';
                        _html += '<span class="d-block nickname">'+ n.nickname +'</span>';
                        _html += '</li>';
                    })
                    $("#buyer_list").append(_html);
                    pages.buyer_pages++;
                } else {
                    isLoadBuyer = false;
                    $(".end-hint").html('下拉即可加载更多');
                    $("#end-hint-buyer").html('没有更多了...');
                    return false;
                }
            });
        },
        /* 买家晒单 */
        report_list:function() {
            if(isLoadReport == false){
                $("#end-hint-report").html('没有更多了...');
                return false;
            }
            $.getJSON(site.site_root + '/index.php?m=product&c=api&a=report_list', {
                goods_id:goods.id,
                limit:5,
                page:pages.report_pages
            }, function(ret) {
                if(ret.lists.length > 0) {
                    var _html = '';
                    $.each(ret.lists, function(i, n) {

                        _html += '<div id="trail_report" class="d-b-wrap">';
                        _html += '<div class="d-b-list clear">';
                        _html += '<span class="fl"><img src="'+ n.avatar +'" alt=""></span>';
                        _html += '<ul class="fl txt">';
                        _html += '<li class="name">'+ n.nickname +'</li>';
                        _html += '<li class="eva">'+ n.content +'</li>';
                        if(n.albums) {
                            _html += '<li style="width: 80%;text-indent:2%" class="eva2">';
                            $.each(n.albums, function(k, album) {
                                _html += '<a><img src="'+ album +'"></a>';
                            });
                            _html += '</li>';
                        }
                        _html += '</ul>';
                        _html += '</div>';
                        _html += '</div>';

                    });
                    $("#trail_report").before(_html);
                    pages.report_pages++;
                } else {
                    isLoadReport = false;
                    $(".end-hint").html('下拉即可加载更多');
                    $("#end-hint-report").html('没有更多了...');
                    return false;
                }
            });
        },
	};
})();