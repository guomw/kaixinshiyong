
/**
 * 确认
 * @param	{String}	消息内容
 * @param	{Function}	确定按钮回调函数
 * @param	{Function}	取消按钮回调函数
 */
artDialog.confirm = function(content, yes, no) {
	return artDialog({
		id : 'Confirm',
		title : '操作提示',
		icon : 'question',
		fixed : true,
		lock : true,
		opacity : .1,
		content : content,
		ok : function(here) {
			return yes.call(this, here);
		},
		cancel : function(here) {
			return no && no.call(this, here);
		}
	});
};
/**
 * 短暂提示
 * @param	{String}	提示内容
 * @param	{Number}	显示时间 (默认1.5秒)
 * @param	{function}	提示框初始化时执行
 * @param	{function}	提示框关闭时执行
 * @param	{String}	icon图标，默认null
 */
artDialog.tips = function(content, time, initfunc, closefunc, icon) {
	var _icon;
	if (icon == 'E') {
		_icon = 'error';
	} else if (icon == 'S') {
		_icon = 'succeed';
	} else if (icon == 'Q') {
		_icon = 'question';
	} else if (icon == 'FSD') {
		_icon = 'face-sad';
	} else if (icon == 'FSE') {
		_icon = 'face-smile';
	} else if (icon == 'W') {
		_icon = 'warning';
	}else {
		_icon = null;
	}
	return artDialog({
		id : 'Tips',
		title : false,
		cancel : false,
		fixed : true,
		lock : true,
		icon: (_icon || null),
		init : (initfunc || null),
		close : (closefunc || null)
	}).content('<div style="padding: 0 1em;">' + content + '</div>').time(time || 1);
};

var commission = {
	/**
	 * title:标题
	 * msg:提示文本
	 * type:提示类型，S:成功，：E：错误，W:警告，Q:疑问
	 * redirection{
	 * 		time:int 弹窗关闭倒计时，单位：秒
	 * 		url:string 弹窗关闭时重定向到url地址
	 * 		refresh: boolean 是否刷新parent页面，(url优先级高于refresh)
	 * }
	 * target:可获取焦点的HTML对象
	 * goods.show_message('操作提示!', '提示文本', 'S', {time:3, refresh:true});
	 */
	show_message : function(title, msg, type, redirection, target) {
		var _type = '';
		if (type == 'E') {
			_type = 'error';
		} else if (type == 'S') {
			_type = 'succeed';
		} else if (type == 'Q') {
			_type = 'question';
		} else if (type == 'FSD') {
			_type = 'face-sad';
		} else if (type == 'FSE') {
			_type = 'face-smile';
		} else {
			_type = 'warning';
		}
		var _title = title == '' ? '提示' : title;
		art.dialog({
			background : 'none',
			icon : _type,
			lock : true,
			fixed : true,
			title : _title,
			init : function() {
				var win = this;
				var timeHandle;
				var secondCount = 0;
				win.content(msg);
				if ( typeof target == 'object') {
					target.focus();
				}
				if ( typeof redirection == 'object') {
					if ( typeof redirection.time == 'number') {
						secondCount = redirection.time;
						var timefunc = function() {
							win.title(_title + ' [' + secondCount + '后关闭]');
							secondCount--;
							if (secondCount <= 0) {
								clearInterval(timeHandle);
								if ( typeof redirection.url == 'string') {
									parent.location.href = redirection.url;
								} else if ( typeof redirection.refresh == 'boolean') {
									parent.location.reload();
								}
							}
						}
						timeHandle = setInterval(timefunc, 1000);
					}
				}
			},
			ok : function(){
                if ( typeof redirection.url == 'string') {
                    location.href = redirection.url;
                } else if ( typeof redirection.refresh == 'boolean') {
                    location.reload();
                }
			}
		});
		return false;
	},
	str_len : function(str) {
		String.prototype.len = function() {
			return this.replace(/[^\x00-\xff]/g, "aa").length;
		}
		return $.trim(str) == '' ? 0 : str.len();
	},
	ajax_url : function(url) {
		var _url = url;
		var reg = /(&|\?)inajax=\d+/;
		if (!reg.test(_url) && !reg.test(_url)) {
			if (_url.indexOf('?') != -1) {
				_url = _url + '&inajax=1';
			} else {
				_url = _url + '?inajax=1';
			}
		}
		return _url;
	},

  fill_trade_no:function(oid) {
 
 	// 根据控制显示验证码的参数判断表单是否需要验证码
  	var is_captcha = 0;
  	var diy_form = '<form style="font-size:12px;"><table>'
  				 + 		'<colgroup><col style="width:60px"><col style="width:350px"></colgroup>'
  				 + 		'<tbody style="color:#797979;">'
  				 + 			'<tr>'
  				 + 				'<td>商品名称：</td>'
  				 + 				'<td style="color: #06f">'+ goods.title +'</td>'
  				 + 			'</tr>'
  				 + 			'<tr>'
  				 + 				'<td>下单价：</td>'
  				 + 				'<td style="font-weight:bold;color:#000;">$'+ goods.goods_price +'</td>'
  				 + 			'</tr>'
  				 + 			'<tr>'
  				 + 				'<td>订单编号：</td>'
  				 + 				'<td><input type="text" name="order_sn" class="ui-form-text ui-form-textRed" /><span class="trade-no-msg" style="color: #cc0000;"></span></td>'
  				 + 			'</tr>'
  				 +				'<tr id="js_images" >'
  				 + 				'<td>下单截图：</td>'
  				 + 				'<td style="padding-top:10px;font-weight:bold;color:#000;">'
  				 +		        '<input type="text" name="order_img[1]" id="urls" class="ui-form-text ui-form-textRed" />'
  				 +		        '<input id="file_uploads" name="Filedata" type="file" style="display:none;" onchange = "return ajaxFileUploadOrder()"/>'
  				 +		        '<input type="button" id="images" name="report_imgs" value="上 传 图 片" style="background-color: #F1F1F1;margin-left: 8px;padding: 1px 3px;"/>'
  				 +		        '<span id="notices"></span>'
  				 +			'</td>'
  				 + 			'</tr>'
  				 + 			'<tr>'
  				 + 				'<td style="vertical-align: top;">温馨提示：</td>'
  				 +				'<td style="color:#999;">'
  				 +					'<p>1、请填写已付款的订单编号，若填入未付款单号，属于违规行为且将无法获得返款；<a style="color:#09f;" href="'+site.site_root+'/help/show/?catid=59&id=143 " target="_blank">填写的订单号规则？</a></p>'
  				 +					'<p>2、若单号被审核有误，请在 小时内进行申诉或修改，逾期将无法领回划算金（建议平时经常登录网站查看站内信提醒哦！）<a style="color:#09f;display:block;" href="'+site.site_root+'/help/show/?catid=6&id=43" target="_blank">如何获取订单编号？</a> <a style="color:#09f;display:block;" href="'+site.site_root+'/help/show/?catid=6&id=43" target="_blank">如何获取订单截图？</a></p>'
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

  			$("#images").click(function (){
  				$("#file_uploads").click();
  			});

  			form.submit(function(){
  				var order_sn_val   = $.trim( trade.val() );
  				var imgs = $('#urls').val();
					var data = {
						order_sn:order_sn_val,
						order_id:oid,
						order_img:imgs
					};
  				noMsg.css('color', '#c00').html('');
  				var re = new RegExp("^[0-9]*$");
                 if ( $('#urls').val() == '' ) {
			      	noMsg.html('请上传订单付款截图');
			         $('#urls').val('').focus();
			      	  return false;
			         }

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
  				commission.set_btn_state(dialog, false); 
  				noMsg.css('color', '#666').html(" 正在发送...");
  				$.post('index.php?m=order&c=api&a=fill_sn', data, function(ret) {
  					commission.set_btn_state(dialog, true);
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
  		cancel:function(){
  			location.reload();
  		}
  	});
  },

  set_btn_state:function (win, can_use) {
  	if (can_use) {
  		win.DOM.buttons.find('button:first').html('确定').removeAttr('disabled').addClass('aui_state_highlight');
  	} else {
  		win.DOM.buttons.find('button:first').html('操作中...').attr('disabled', 'disabled').removeClass('aui_state_highlight');
  	}
  },
   

	general:function(id,taobao){
		$.post(site.site_root + '/index.php?m=product&c=api&a=pay_submit',{goods_id:id,bind_taobao:taobao},function(data){

    		if(data.status == 1){
    			art.dialog({
					lock: true,
					fixed: true,
					title: '温馨提示',
					cancelVal: '继续逛逛',
					okVal: '抢购成功，现在马上去下单',
					content: commisson_html,
					ok: function(){
					window.open(goods_url1);
						commission.fill_trade_no(data.info.oid);

					},
					cancel : function(){
						location.reload();
					}
				});
    		}else{
    			art.dialog({
					lock: true,
					fixed: true,
					icon: 'face-sad',
					title: '温馨提示',
					content: data.info,
					ok: true
				});
    		}
    	},'json');
	},

	/* 搜索下单 todo*/
	serach:function(id,taobao){
		var ask_html = '<div class="CPM_style CPM_style_2 border_radius_5" style="position: static;">';
		ask_html += '<p class="issue"><a href="'+site.site_root+'/help/?catid=81" target="_blank">搜素下单帮助</a></p>';
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
					commission.general(id,taobao);
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
	/* 二维码下单*/
	code:function(id,taobao){
		
	},
	/* 检测是否绑定亚马逊账号 */
	buy:function(id){
        if (id > 0) {
        	//检测是否已经登录
        	if (site.user.length < 1) {	// 未登录
				member.login();
				return false;
			}


			if (com_set != 4) {
				commission.general(id,'');

			}else{
		        	art.dialog({
		                id:'goods_logs',
		                lock : true,
		                fixed : true,
		                title : '亚马逊绑定',
		                drag : false,
		                init : function() {
		                    var win = this;
		                	$.get(site.site_root + '/index.php?m=product&c=index&a=alipay_check', function(ret) {
                                  
                                  console.log(ret);
		                           if (ret.indexOf('"error"') != -1) {
			                        var errObj = eval('(' + ret + ')');
			                        win.close();
			                        commission.show_message('出错啦!', errObj['msg'], 'E')
		                        } else {
		                            win.content(ret);
		                        }
		                    });
		                },
		                ok:function(){
		                	//判断该产品下单类型
		                	var top = art.dialog.top;
		                	var taobao = top.document.getElementById("taobao").value;

		                   if (taobao == "") {
		                      alert("请选择亚马逊账号");
		                  }
		                	 commission.general(id,taobao);

		                	                            			
		                },
		                button : [{
		                    name : '取消',
		                    focus : true
		                }]
		            });
			}



            
               
        }
	}
	
}; 