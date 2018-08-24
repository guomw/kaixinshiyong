/* 商品相关  */
var trial_detail = (function() {
	var oIntro = oBuyer = oBuyer_navs = oBuyer_conts = oTime = null;
	var buy_num = 0;
    var timestamp = parseInt($.now() / 1000);
	var text_status = {	//	状态
		't-3': '待审核(待付款)',
		't-2': '待审核(已付款)',
		't-1': '审核通过(待上线)',
		't0': '审核失败(已退款)',
		't1': '我要抢购',
		't2': '活动结束(结算中)',
		't3': '活动结束(已结算)',
		't4': '已撤销',
		't5': '已屏蔽',
	};

	var pages = {
		buyer_pages:2,
		report_pages:2
	};

	var isLoadReport = isLoadBuyer = true;

	function error(msg) {	//	错误提示
		art.dialog({
			lock: true,
			fixed: true,
			icon: 'error',
			title: '错误提示',
			content: msg,
			ok: true
		});
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
		var remain_sec = start_sec - timestamp;
		timer = setInterval(function() {
			remain_sec -= 1;
			if (remain_sec <= 0) {
				obj.html('0');
				clearInterval(timer);
				$('.a_yes').attr('style','display:');
				$('#start').attr('style','display:none');
                show_remain_time(goods.end_time - timestamp);
				return;
			}
			obj.html(count_down(remain_sec));
		}, 1000);
		obj.html(count_down(remain_sec));
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
            // 获取倒计时
			oTime = oIntro.find('.a_yes span');
			// 当前抢购按钮
			btn_text = text_status['t' + goods.status];
			if (goods.status != 1){
            	$('#notice').attr('style','display:');
            	$('#notice').html('该试用商品当前状态未上架：'+btn_text);
				btn_buy.text(btn_text).addClass('btn_no');
			} else {
                /* 判断库存 */
                if(goods.goods_stock < 1) {
                	$('#notice').attr('style','display:');
            		$('#notice').html('该试用商品当前库存已售罄');
                    btn_buy.text('已被售罄').addClass('btn_no');
                } else {
                    if(timestamp < goods.start_time) {
                		$('#start').attr('style','display:');
                		var sTime = $('#start > font > span');
                        show_online_time(goods.start_time, sTime);                   
                        btn_buy.text('尚未开始').removeClass('btn_yes').addClass('btn_no');
                    } else if(timestamp > goods.end_time) {
                        btn_buy.text('已经结束').removeClass('btn_yes').addClass('btn_no');
                    } else {
                    	/* 获取接口信息 */
						if (parseInt(site.user.userid) > 0 ){
							$.getJSON(site.site_root+'/index.php?m=order&c=api&a=order_infos&pid='+goods.id,{
								pid : goods.id
							},function(ret){
								if(ret.status == 1 && (parseInt(ret.info.not_over) > 0 || parseInt(goods.buyer_good_buy_times) <= parseInt(ret.info.over_order))) {
                                    btn_buy.text('已申请').removeClass('btn_yes').addClass('btn_no');
									$("#apply_pass").attr('style','display:');
									$("#apply_unpass").attr('style','display:none');
								}else{
	                    			$('.a_yes').attr('style','display:');
									$("#apply_unpass").attr('style','display:');
									$("#apply_pass").attr('style','display:none');
									btn_buy.text('申请试用').removeClass('btn_no').addClass('btn_yes');
                        			show_remain_time(goods.end_time - timestamp);
								}
							});
						}else{
	                    	$('.a_yes').attr('style','display:');
	                    	$("#apply_unpass").attr('style','display:');
							$("#apply_pass").attr('style','display:none');
	                        show_remain_time(goods.end_time - timestamp);
							btn_buy.text('申请试用').removeClass('btn_no').addClass('btn_yes');
						}
                    }
                }
			}
            // 抢购列表
            $("#report_count").text(goods.report_count);
            $("#buyer_count").text(goods.buyer_count);
            
		},
		error:function (msg) {
			art.dialog({
				lock: true,
				fixed: true,
				icon: 'error',
				title: '错误提示',
				content: msg,
				ok: true
			});
		},
		/* 抢购入口 */
		buy: function() {
			if (site.user.length < 1) {	// 未登录
				member.login();
				return false;
			}
			if($('a#btn_buy').hasClass('btn_no')) return false;
			// 答案下单先校验答案
			if (goods.type == 'ask') {
				trial_detail.answer();
				return false;
			}			
			trial_detail.talk2seller();
		},
		/* 校验答案 */
		answer: function (){
			var ask_html = '<div class="CPM_style CPM_style_2 border_radius_5" style="position: static;">';
				ask_html += '<p class="issue"><a href="'+site.site_root+'/help/?catid=81" target="_blank">答案下单帮助</a></p>';
				ask_html += '<font class="font f_bg_yes font_2">您需要回答问题才能完成抢购哦！</font>';
				ask_html += '<ul class="set_iss"><li>问题：<em>'+goods.goods_rule.ask.question+'？</em></li>';
				if (goods.goods_rule.ask.tips != null) {
					ask_html += '<li>提示：<em>'+goods.goods_rule.ask.tips+'</em></li>';
				}
				ask_html += '<li><font>答案：</font><input type="text" name="answer" id="answer"/><img src="'+site.template_img+'/verify_btn1.png" alt="" id="right" style="display:none;"/>';
				ask_html += '<span id="error" style="display:none;"><img src="'+site.template_img+'/verify_btn2.png" alt=""/>回答问题错误！</span></li></ul><p class="iss_hint">提示：您可以在商家宝贝详情页查看宝贝介绍可以找到相关答案。<a href="'+site.site_root+goods.goods_url+'" target="_blank">去找找看</a></p></div>';	
			art.dialog({
				title:'校验答案',
				fixed:true,
				width:500,
				lock:true,
				content:ask_html,
				okVal:'验证答案',
				ok:function(){
					if (goods.goods_rule.ask.answer == $.trim($("#answer").val())) {
						$('#right').attr('style','display:');
						$('#error').attr('style','display:none');
						trial_detail.talk2seller();
						return true;
					}else{
						$('#right').attr('style','display:none');
						$('#error').attr('style','display:');
						return false;
					}																	
				},
				cancelVal:'取消',
				cancel:function() {return true},
			})
			return false;
		},
		/* 对商家说点什么 */
		talk2seller: function (){
			var talk_html = '<div class="CPM_style CPM_style_2 border_radius_5" style="position: static;width:332px">';
				if (bind_set == 4) {	//	后台活动设置开启 需要亚马逊账号时启用
					if (bind_tbs != 0) {
						talk_html += '<font class="font font_2" style="padding-left: 20px;color:blue;">用此亚马逊账号购买：<select name="bind_taobao" id="bind_taobao">';
						$.each(bind_tbs,function(k,tb) {
							if (tb.is_default == 1) {
								talk_html += '<option value="'+ tb.id +'" selected>'+ tb.account +'</option>';
							}else{
								talk_html += '<option value="'+ tb.id +'">'+ tb.account +'</option>';
							}						
						});
						talk_html += '</select></font><br/>';
					}else{
						talk_html += '<font class="font font_2" style="padding-left: 20px;color:blue;">暂无绑定的亚马逊账号！<a href="/index.php?m=Member&c=Attesta&a=bindtaobao" target="_blank" style="color:red;">点此去绑定</a></font><br/>';
					}
				}
				talk_html += '<font class="font font_2" style="padding-left: 30px;">对商家说点什么，可以帮助您提高通过率</font>';
				talk_html += '<textarea cols="42" rows="6" placeholder="如：在线等，通过后立即下单(最多100个字符)" id="txtremark" style="color: rgb(108, 108, 108); margin: 0px 0px 0px 30px; width: 269px; height: 84px;" maxlength="100"></textarea>';
				talk_html += '</div>';

			art.dialog({
				title:'说点什么',
				fixed:true,
				lock:true,
				content:talk_html,
				okVal:'提交',
				ok:function(){
					var talk_content = $('#txtremark').val();
					var phone = /^(1)[0-9]{10}$/; 
					var url = /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&]*([^<>])*$/;
					var qq = /^\d{5,10}$/;
					// 若后台活动设置开启需要绑定亚马逊账号后则校验;
					var bind_taobao = $('#bind_taobao').val();
					if(phone.test(talk_content) || url.test(talk_content) || qq.test(talk_content) || (bind_set==4&&isNaN(bind_taobao)==true)){
						var str = '';
						if (phone.test(talk_content)) {
							str = '禁止输入联系方式';
						}else if (url.test(talk_content)){
							str = '禁止输入网站域名地址';
						}else if(qq.test(talk_content)){
							str = '禁止输入数字等联系方式';
						}else if(isNaN(bind_taobao) == true){
							str = '请选择要绑定的亚马逊帐号';
						}
						art.dialog({
							lock: true,
							fixed: true,
							icon: 'face-sad',
							title: '温馨提示',
							content: str,
							ok: true
						});
						return false;
					}
					trial_detail.trial_url(talk_content,bind_taobao);
				},
				cancelVal:'取消',
				cancel:function() {return true},
			})
			return false;
		},
		/* 现在去下单 [免费试用] */
		trial_url : function(talk_content,bind_taobao) {
			// 抢购成功
			$.post(site.site_root + '/?m=product&c=api&a=pay_submit',{
				goods_id : goods.id,
				bind_taobao: bind_taobao,	// 亚马逊帐号id
				talk_content : talk_content //对商家说点什么
			},function(ret){
				if (ret.status==1){
					art.dialog({
						title:'申请成功',
						fixed:true,
						lock:true,
						width:300,
						content:buy_succes,
						okVal:'继续逛逛',
						ok:true,
					})
				}else{
					error(ret.info);
					return false;
				}
			},'JSON');
			// 抢购成功后的模版选择
			var buy_succes = '<div class="CPM_style CPM_style_2 border_radius_5" style="position: static;">';
			// 下单方式
			switch(goods.type) {
				case 'general': //普通下单 
					buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=84" target="_blank">如何提高通过率?</a></p><font class="font f_bg_yes font_2">恭喜您，已成功提交申请，请等待商家审核试用资格哦！</font></div>';
					break;
				case 'qrcode': //二维码下单
				  	buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=84" target="_blank">如何提高通过率?</a></p><font class="font f_bg_yes font_2">恭喜您，已成功提交申请，请等待商家审核试用资格哦！</font></div>';
					break;
				case 'search': //搜索下单
					buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=84" target="_blank">如何提高通过率?</a></p><font class="font f_bg_yes font_2">恭喜您，已成功提交申请，请等待商家审核试用资格哦！</font></div>';
					break;
				case 'ask' : //问答下单
					buy_succes += '<p class="issue"><a href="'+site.site_root+'/help/?catid=84" target="_blank">如何提高通过率?</a></p><font class="font f_bg_yes font_2">恭喜您，已成功提交申请，请等待商家审核试用资格哦！</font></div>';
					break;
			};
		},
        
        /* 试用报告 */
        trail_report:function() {
        	if(isLoadReport == false) return false;
            $.getJSON(site.site_root + '/?m=product&c=api&a=trail_report', {
                goods_id:goods.id,
                limit:3,
                page:pages.report_pages
            }, function(ret) {
                if(ret.lists.length > 0) {
                    var _html = '';
                    $.each(ret.lists, function(i, n) {
                    	_html += '<div class="user_appr_box">';
                    	_html += '<font class="user_name">'+ n.nickname +'<em>'+ n.inputtime +'</em></font>';
                    	_html += '<p class="user_appr">'+ n.content +'</p>';
                    	if(n.albums) {
                    		_html += '<span class="shop_img">';
                    		$.each(n.albums, function(k, album) {
                    			_html += '<img src="'+ album +'" alt="试用图片" />';
                    		});
                    		_html += '</span>';
                    	}
                    	_html += '</div>';
                    });
                    $("#trail_report").before(_html);
                    pages.report_pages++;
                } else {
                	isLoadReport = false;
                	$("#trail_report > a").text('加载完毕');
                	error('没有更多内容');
                	return false;
                }
            });
        },

        /* 已审批试客 */
        buyer_list:function() {
        	if(isLoadBuyer == false) return false;
            $.getJSON(site.site_root + '/?m=product&c=api&a=buyer_list', {
                goods_id:goods.id,
                limit:9,
                page:pages.buyer_pages
            }, function(ret) {
                if(ret.lists.length > 0) {
                    var _html = '';
                    $.each(ret.lists, function(i, n) {
                    	_html += '<li><a href="javascript:;"><img src="'+ n.avatar +'" alt="'+ n.nickname +'" /><span>'+ n.nickname +'</span></a></li>';
                        if((i + 1) / 9 == 0) _html += '<span class="bor_bottom"></span>';
                    })
                    $("#buyer_list").append(_html);
                    pages.buyer_pages++;
                } else {
                	isLoadBuyer = false;
                	error('没有更多内容');
                	$("#buyer_list").siblings('.load_more').find('a').text('加载完毕');
                	return false;
                }
            });
        },
        
        report_list:function(page) {
            return false;
        }
	};
})();