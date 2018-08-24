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
};
function error(msg) {	//	错误提示
	art.dialog({
		lock: true,
		fixed: true,
		icon: 'face-sad',
		title: '错误提示',
		content: msg,
		ok: true,
	});
};
var goods = {
	/**
	 * 发布商品亚马逊选择否时提示
	 */
	taoke_tip : function() {
		var html = '<li>1、如选择“否”，则买家购买时产生的淘客佣金，由商家承担。</li>';
		html += '<li>2、如选择“是”，则需要在下单地址里放入商家的淘客链接，若不放入商家的淘客链接，产生的淘客佣金由商家承担。</li>';
		html += '<li>3、活动上线后，将无法修改下单地址。</li>';
		html = '<ul>' + html + '</ul>';
		art.dialog({
			id : 'taoke_tip',
			lock : true,
			fixed : true,
			title : '温馨提示',
			width : '40em',
			drag : false,
			init : function() {
				var win = this;
				win.content(html);
			},
			button : [{
				name : '我知道了'
			}]
		});

	},
	/**
	 * 批量审核
	 */
	batch_check : function(o, page) {
		page = (typeof page!="undefined")?page:1;
		var target_btn = $(o);
		var gid = parseInt(target_btn.attr('data-gid'));
		var pg = parseInt(page);
		pg = isNaN(pg) ? 1 : pg;
		if(gid<=0){
			return false;
		}
		
		var oids = [];
		$(o.form).find('tbody :checkbox:checked').each(function(){
			oids.push(this.value);
		});
		if(oids.length<=0){
			return goods.show_message('操作提示!', '您没有选择任何操作项', 'E');
		}
		var p_d = $(o.form).serialize();
		var reg = /(oids%5B\d+%5D=)(-?1|2)&\1/i;
		if(reg.test(p_d)){
			return goods.show_message('操作提示!', '同一个订单不能同时执行多个操作', 'E');
		}
		target_btn.attr('disabled', 'disabled');
		var _tips_dialog = goods.do_submit('正在操作，请稍后...', 120, function() {
			$.ajax({
				url : '/order/batch_submit/' + gid + '/' + pg+ '?inajax=1',
				type : 'post',
				dataType : 'json',
				data : $(o.form).serialize(),
				success : function(back) {
					_tips_dialog.close();
					if ( typeof back == 'object') {
						if (back.error == 1) {
							artDialog.tips(back, 3, null, target_btn.removeAttr('disabled'), 'W');
						} else {
							var str = back.msg;
							var have_ok = back.chk_right_success + back.chk_error_success + back.chk_return_success;
							if(back.chk_return_success > 0){
								art.dialog.confirm(str, function() {
									var target_btn = $(o);
									var return_form = $('<form action="/pay/return_submit/'+gid+'" method="post" target="_blank"></form>');
									for(var i in back.chk_return_success_oids){
										var oidinput = $('<input name="oids[]" value="'+back.chk_return_success_oids[i]+'" type="hidden">');
										return_form.append(oidinput);
									}
									$('#order_return').append(return_form);
									$(return_form).submit();
									target_btn.removeAttr('disabled');
									if($.isFunction(window.load_order_list)){
										window.load_order_list('/order/batch_load/'+gid+'/'+pg);
									}else{
										location.href = '/order/batch_check/'+gid;
									}
								}, function() {
									target_btn.removeAttr('disabled');
									if($.isFunction(window.load_order_list)){
										window.load_order_list('/order/batch_load/'+gid+'/'+pg);
									}else{
										location.href = '/order/batch_check/'+gid;
									}
									return true;
								});
							}else{
								artDialog.tips(back.msg, 3, null,
									function(){
										if($.isFunction(window.load_order_list)){
											window.load_order_list('/order/batch_load/'+gid+'/'+pg);
										}else{
											location.href = '/order/batch_check/'+gid;
										}
									}, (have_ok > 0 ? 'FSE' : 'FSD')
								);
							}
						}
					} else {
						artDialog.tips(back, 3, null, target_btn.removeAttr('disabled'), 'W');
					}
				}
			});
		}, function() {
			return true;
		},'FSE');
	},
	/**
	 * 用于快速审核的批量审核
	 * state:1为审核订单正确，-1为审核订单正确，
	 */
	quick_batch_check : function(o, state, page) {
		var page = page || 1;
		var target_btn = $(o);
		var gid = parseInt(target_btn.attr('data-gid'));
		var pg = parseInt(page);
		pg = isNaN(pg) ? 1 : pg;
		if(gid<=0){
			return false;
		}
		
		var oids = [];
		$(o.form).find('tbody :checkbox:checked').each(function(){
			oids.push(this.value);
		});	
		if(oids.length<=0){
			return goods.show_message('操作提示!', '您没有选择任何操作项', 'E');
		}
		target_btn.attr('disabled', 'disabled');
		var _tips_dialog = goods.do_submit('正在操作，请稍后...', 120, function() {
			$.ajax({
				url : '/order/quick_batch_submit/' + gid  + '?inajax=1',
				type : 'post',
				dataType : 'json',
				data : $(o.form).serialize()+'&state='+state,
				success : function(back) {
					_tips_dialog.close();
					if ( typeof back == 'object') {
						if (back.success == false) {
							artDialog.tips(back.data, 3, null, target_btn.removeAttr('disabled'), 'W');
						} else {
							artDialog.tips(back.data, 3, null,
								function(){
									if($.isFunction(window.load_order_list)){
										window.load_order_list('/order/quick_check_upload_result/'+gid+'/'+pg);
									}else{
										location.href = '/order/quick_check/'+gid;
									}
								}, 'S'
							);
						}
					} else {
						artDialog.tips(back, 3, null, target_btn.removeAttr('disabled'), 'W');
					}
				}
			});
		}, function() {
			return true;
		},'FSE');
	},
	/**
	 * 用于快速审核的直接返款
	 * state:1为审核订单正确，-1为审核订单正确，
	 */
	quick_order_return : function(o, type, state, page) {
		var page = page || 1;
		var target_btn = $(o);
		var gid = parseInt(target_btn.attr('data-gid'));
		var pg = parseInt(page);
		pg = isNaN(pg) ? 1 : pg;
		if(gid<=0){
			return false;
		}
		var oids = [],adjust_add_order = [],adjust_sub_order = [],common_order = [],chkInput = [];
		$(o.form).find('tbody :checkbox:checked').each(function(){
			var adjust = $(this).data('adjust');
			if(adjust == '1'){
				adjust_add_order.push(this.value);
			}else if(adjust == '-1'){
				adjust_sub_order.push(this.value);
			}else{
				common_order.push(this.value);
			}
			oids.push(this.value);
			chkInput.push($(this));
		});
		if(oids.length<=0){
			return goods.show_message('操作提示!', '您没有选择任何操作项', 'E');
		}else if((adjust_add_order.length > 0 || adjust_sub_order.length > 0) && common_order.length > 0){
			return goods.show_message('操作提示!', '有调整划算金的订单和正常订单不能一起返款', 'E');
		}else if(((adjust_add_order.length == 1 && adjust_sub_order.length == 1) || (adjust_add_order.length > 1 || adjust_sub_order.length > 1)) && common_order.length == 0){
			return goods.show_message('操作提示!', '有调整划算金的订单只能单个返款，不能批量返款', 'E');
		}
		target_btn.attr('disabled', 'disabled');
		art.dialog.confirm('您将给<b>' + oids.length + '</b>位试客返还划算金。<br>返款金额将直接汇入试客账户，是否继续?', function() {
			var target_btn = $(o);
			var return_form = $('<form id="quick_return_form" action="/order/quick_order_return/'+gid+'" method="post" target="_blank"></form>');
			$('#quick_order_return').append(return_form);
			for(var i in oids){
				var oidinput = $('<input name="oids[]" value="'+oids[i]+'" type="hidden">');
				$('#quick_check_form table #tr_'+oids[i]).remove();
				$('#quick_return_form').append(oidinput);
			}
			$('#quick_return_form').submit().remove();
			target_btn.removeAttr('disabled');
		}, function() {
			target_btn.removeAttr('disabled');
			return true;
		});
	},
	/**
	 * 快速审核
	 */
	quick_check : function(o) {
		var gid = parseInt($(o).attr('data-gid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		if (gid > 0 && $.trim(url) != '') {
			art.dialog({
				lock : true,
				fixed : true,
				title : '快速审核',
				drag : false,
				init : function() {
					var win = this;
					$.get(url, function(ret) {
						win.content(ret);
					});
				},
				button : [{
					name : '确认导入',
					callback : function() {
						var win = this;
						var form = $('#form_order_quick_check');
						form.ajaxSubmit({
							dataType : 'json',
							success : function(ret) {
								if (ret.success) {
									win.close();
									goods.quick_check_confirm(gid);
								} else {
									alert(ret.data);
								}
							}
						});
						return false;
					},
					focus : true
				}],
				cancel : true
			});
		}
	},
	quick_check_confirm : function(gid) {
		art.dialog({
			lock : true,
			fixed : true,
			title : '快速审核',
			drag : false,
			init : function() {
				var win = this;
				$.get('/order/quick_check_upload_result/' + gid + '/', function(ret) {
					win.content(ret);
				});
			},
			ok : function() {
				var win = this;
				$.get('/order/quick_check_upload_confirm/' + gid + '/', function(ret) {
					ret = eval('(' + ret + ')');
					if (ret.success) {
						win.close();
					} else {
						alert(ret.data);
					}
				});
				return false;
			},
			cancel : true
		});
	},
	/**
	 * 审核订单号(单个)
	 */
	single_check : function(o) {
		var target_btn = $(o);
		var gid = parseInt($(o).attr('data-gid'));
		var oid = parseInt($(o).attr('data-oid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		if (gid > 0 && oid > 0 && $.trim(url) != '') {
			art.dialog({
				lock : true,
				fixed : true,
				title : '审核订单号',
				drag : false,
				init : function() {
					var win = this;
					$.get(url, function(ret) {
						if (ret.indexOf('"error"') != -1) {
							var errObj = eval('(' + ret + ')');
							win.close();
							goods.show_message('出错啦!', errObj['msg'], 'E')
						} else {
							win.content(ret);
						}
					});
				},
				button : [{
					name : '提交审核',
					callback : function() {
						var check_oid = $('#check_oid').val();
						if ($.trim(check_oid) != '') {
							target_btn.attr('disabled', 'disabled');
							var _tips_dialog = goods.do_submit('正在操作，请稍后...', 120, function() {
								$.ajax({
									url : '/order/single_check/' + gid + '/' + oid + '?inajax=1',
									type : 'post',
									dataType : 'json',
									data : 'doCheck=1&check_oid=' + check_oid,
									success : function(back) {
										_tips_dialog.close();
										if (back.error == 1) {
											artDialog.tips(back.msg, 3, null, target_btn.removeAttr('disabled'), 'W');
										} else {
											artDialog.tips(back.msg, 3, null,
												function(){
													location.href = '/order/order_list/'+gid;
												}, 'S'
											);
										}
									}
								});
							}, function() {
								return true;
							},'FSE');
						} else {
							return goods.show_message('出错啦!', '请选择核对操作!', 'E')
						}

					},
					focus : true
				}],
				cancel : true
			});
		}
	},
	/**
	 * 撤销审核通过 
	 */
	revoke_pass:function(o){
		var gid = parseInt($(o).attr('data-gid'));
		if(gid<=0){
			return false;
		}
		
		var oids = [];
		$(o.form).find('tbody :checkbox:checked').each(function(){
			oids.push(this.value);
		});	
		if(oids.length<=0){
			return goods.show_message('操作提示!', '您没有选择任何操作项', 'E');
		}
		art.dialog.confirm('您将撤销<b>' + oids.length + '</b>个订单的审核，是否继续?', function() {
			var target_btn = $(o);
			target_btn.attr('disabled', 'disabled');
			var _tips_dialog = goods.do_submit('正在操作，请稍后...', 120, function() {
				$.post('/order/revoke_pass/'+gid,{
					doRevoke:1,
					oids:oids
				}, function(ret){
					_tips_dialog.close();
					ret = $.parseJSON(ret);
					if(ret.success){
						artDialog.tips(ret.data, 3, null,
							function(){
								location.href = '/pay/order_return/'+gid;
							}, 'S'
						);
					}else{
						artDialog.tips(ret.data, 3, null, target_btn.removeAttr('disabled'), 'W');
					}
				});
			}, function() {
				return true;
			},'FSE');
		}, function() {
			return true;
		});
	},
	/**
	 * 撤销支付 
	 */
	revoke_pay:function(o){
		var data = $(o).data();
		var gid = parseInt(data['gid']),
		type = parseInt(data['type']),
		url = data['url'];
		gid = gid || 0;
		type = type || 0;
		if(gid<=0 || type <= 0 || url == ''){
			return false;
		}
		art.dialog.confirm('您将撤销<b>支付</b>活动，是否继续?', function() {
			var target_btn = $(o);
			target_btn.attr('disabled', 'disabled');
			var _tips_dialog = goods.do_submit('正在操作，请稍后...', 120, function() {
				$.post(url, data, function(ret){
					_tips_dialog.close();
					ret = $.parseJSON(ret);
					if(ret.success){
						artDialog.tips(ret.data, 3, null,
							function(){
								location.href = '/goods/goods_list/';
							}, 'S'
						);
					}else{
						artDialog.tips(ret.data, 3, null, target_btn.removeAttr('disabled'), 'W');
					}
				});
			}, function() {
				return true;
			},'FSE');
		}, function() {
			return true;
		});
	},
	/**
	 * 返款(单个或多个)
	 */
	order_return : function(o) {
		var gid = parseInt($(o).attr('data-gid'));
		var oid = parseInt($(o).attr('data-oid'));
		if (gid > 0) {
			var oids = $(o.form).find('input');
			var oidCount = 0;
			var chkInput = [];
			oids.each(function() {
				var v = $(this).val();
				if (this.checked && v > 0) {
					oidCount++;
					chkInput.push($(this).parent().parent());
				}
			});
			oidCount = oidCount > 0 ? oidCount : 0;
			if (oidCount > 0) {
				art.dialog.confirm('您将给<b>' + oidCount + '</b>位试客返还划算金。<br>返款金额将直接汇入试客账户，是否继续?', function() {
					var target_btn = $(o);
					target_btn.attr('disabled', 'disabled');
					var _tips_dialog = goods.do_submit('正在发送返款请求中...', 120, function() {
						o.form.action = '/pay/return_submit/'+gid;
						$(o.form).submit();
					}, function() {
						return true;
					});
				}, function() {
					return true;
				});
			} else {
				return goods.show_message('操作提示!', '您没有选择任何操作项', 'E');
			}
		}
	},
	/**
	 * 结算活动确认
	 */
	balance_confirm : function(o) {
		var gid = parseInt($(o).attr('data-gid'));
		var url = $(o).attr('data-url');
		var title = $(o).attr('data-title');
		title = $.trim(title) == '' ? '' : '[<b style="color:blue">' + title + '</b>]';
		artDialog.confirm('您将结算活动' + title + '.<br>结算成功后将不能再追加上架.<br>您确定要结算活动吗?', function() {
			window.location.href = url;
		}, function() {
			return true
		});
	},
	/**
	 * 结算详情
	 */
	balance_view : function(o) {
		var gid = parseInt($(o).attr('data-gid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		if (gid > 0 && $.trim(url) != '') {
			art.dialog({
				id:'balance_view',
				lock : true,
				fixed : true,
				title : '结算详情',
				width : '60em',
				drag : false,
				init : function() {
					var win = this;
					$.get(url, function(ret) {
						if (ret.indexOf('"error"') != -1) {
							var errObj = eval('(' + ret + ')');
							win.close();
							goods.show_message('出错啦!', errObj['msg'], 'E')
						} else {
							win.content(ret);
						}
					});
				},
				button : [{
					name : '关闭窗口',
					focus : true
				}]
			});
		}
	},
	/**
	 * 退/换货申诉
	 * defaultselect 申诉类型默认选择值，默认为0
	 */
	appeal_add : function(o,defaultselect) {
		var defaultselect = defaultselect || 0;
		var oids = $(o.form).find('input');
		var oidCount = 0,targetOid = 0,oid_state = 0;
		var chkInput = [];
		var inputElements = {};
		oids.each(function() {
			var v = $(this).val();
			if (this.checked && v > 0) {
				oidCount++;
				targetOid = v;
				inputElements[targetOid]=parseInt($(this).data('state'));
				chkInput.push($(this).parent().parent());
			}
		});
		oidCount = oidCount > 0 ? oidCount : 0;
		if(oidCount == 0 && targetOid == 0){
			targetOid = parseInt($(o).attr('data-oid'));
			if(targetOid > 0){
				oidCount = 1;
			}
		}
		if (oidCount > 1) {
			return goods.show_message('操作提示!', '申述不支持批量操作，建议您勾选一个要申述的订单', 'E');
		}else if (oidCount == 1) {
			oid_state = inputElements[targetOid];
			if(isNaN(oid_state) || oid_state == 8){
				return goods.show_message('操作提示!', '该订单状态不能进行申诉', 'E');
			}
			art.dialog.confirm('您将申诉所选的订单，申诉后该订单返款流程将进入暂停状态，是否继续?', function() {
				$('#quick_check_form table #tr_'+targetOid).remove();
				var form = $('<form target="_blank" type="get" action="/appeal/appeal_add/'+targetOid+'" style="display:none;"></form>');
				$('body').append(form);
				form.submit();
				form.remove();
				//goods.appeal_add2(o,targetOid,defaultselect);
			}, function() {
				return true;
			});
		} else {
			return goods.show_message('操作提示!', '您没有选择任何操作项', 'E');
		}
	},
	/**
	 * 我要申诉
	 * targetoid 指定oid，默认为0
	 * defaultselect 申诉类型默认选择值，默认为0
	 */
	appeal_add2 : function(o,targetoid,defaultselect) {
		var targetoid = targetoid || 0;
		var defaultselect = defaultselect || 0;
		var oid = parseInt(targetoid);
		oid = isNaN(oid) ? parseInt($(o).attr('data-oid')) : oid;
		var url = oid > 0 ? '/appeal/appeal_add/'+oid : '';
		url = goods.ajax_url(url);
		if (oid > 0 && $.trim(url) != '') {
			art.dialog({
				lock : true,
				fixed : true,
				title : '我要申诉',
				drag : false,
				button : [{
					name : '提交申诉',
					callback : function() {
						var appeal_form = $('#appeal_add_form');
						if (appeal_form.attr('bindonsubmit') != 'true') {
							appeal_form.on('submit', function() {
								var appeal_form = $('#appeal_add_form');
								var appeal_type = appeal_form.find('select[name=appeal_type]');
								var appeal_reason = appeal_form.find('textarea[name=content]');
								var appeal_img = appeal_form.find('input[name=img_1]');
								var contact_qq = appeal_form.find('input[name=contact_qq]');
								var contact_ww = appeal_form.find('input[name=contact_wangwang]');
								var contact_mobile = appeal_form.find('input[name=contact_telephone]');
								var regQq = /^[1-9]\d{4,13}$/;
								var regMobile = /^1[3-8]\d{9}$/;
								if (appeal_type.val() == '0') {
									return goods.show_message('出错啦!', '请选择申诉类型', 'E', {}, appeal_type);
								} else if (goods.str_len(appeal_reason.val()) <= 0) {
									return goods.show_message('出错啦!', '请填写申诉原因', 'E', {}, appeal_reason);
								} else if (goods.str_len(appeal_img.val()) <= 0) {
									return goods.show_message('出错啦!', '未选择凭证图片', 'E', {}, appeal_img);
								} else if ($.trim(contact_qq.val()) == '' && $.trim(contact_ww.val()) == '' && $.trim(contact_mobile.val()) == '') {
									return goods.show_message('出错啦!', '联系方式至少要填写一项', 'E');
								} else if ($.trim(contact_qq.val()) != '' && ! regQq.test(contact_qq.val())) {
									return goods.show_message('出错啦!', '填写联系QQ不正确', 'E', {}, contact_qq);
								} else if ($.trim(contact_mobile.val()) != '' && ! regMobile.test(contact_mobile.val())) {
									return goods.show_message('出错啦!', '填写联系手机号不正确', 'E', {}, contact_mobile);
								} else {
									return true;
								}
							});
							appeal_form.attr('bindonsubmit', true);
						}
						$('#appeal_add_form').submit();
						return false;
					},
					focus : true
				}],
				init : function() {
					var win = this;
					$.get(url, function(ret) {
						if (ret.indexOf('"error"') != -1) {
							var errObj = eval('(' + ret + ')');
							win.close();
							goods.show_message('出错啦!', errObj['msg'], 'E')
						} else {
							win.content(ret);
							
							/*添加申诉凭证*/
							var pos = 1;
							var form = $(".appeal_add_form");
							var copy = form.find(".upload ul").html();
							var addProof = form.find('.upload .add');

							form.find(".upload ul").on('click', 'li a', function() {
								$(this).parent().remove();
								addProof.removeClass('disabled');
							}).find('a').hide();
							addProof.click(function() {
								var len = form.find(".upload_img").length;
								if (len < 5) {
									var file = $(copy);
									file.find('input').attr('name', 'img_' + (++pos));
									form.find("ul").append(file);
									len===4 && addProof.addClass('disabled');
								}
							});
							form.find('select[name=appeal_type]').val(defaultselect);
						}
					});
				},
				cancel : true
			});
		}
	},
	/**
	 * 申诉详情
	 */
	appeal_view : function(o) {
		var oid = parseInt($(o).attr('data-oid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		if (oid > 0 && $.trim(url) != '') {
			art.dialog({
				title : '查看申诉',
				drag : true,
				lock : true,
				init : function() {
					var win = this;
					$.get(url, function(ret) {
						if (ret.indexOf('"error"') != -1) {
							var errObj = eval('(' + ret + ')');
							win.close();
							goods.show_message('出错啦!', errObj['msg'], 'E')
						} else {
							win.content(ret);
						}
					});
				},
				button : [{
					name : '关闭窗口',
					focus : true
				}]
			});
		}
	},
	/**
	 * 我要抗诉
	 */
	appeal_reply : function(o) {
		var oid = parseInt($(o).attr('data-oid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		if (oid > 0 && $.trim(url) != '') {
			art.dialog({
				lock : true,
				fixed : true,
				title : '回应申诉',
				ok : function() {
					var appeal_form = $('#appeal_reply_form');
					$('#appeal_reply_form').submit();
					return false;
				},
				init : function() {
					var win = this;
					function set_btn_state(win, can_use) {
						if (can_use) {
							win.DOM.buttons.find('button:first').html('确定').removeAttr('disabled').addClass('aui_state_highlight');
						} else {
							win.DOM.buttons.find('button:first').html('操作中...').attr('disabled', 'disabled').removeClass('aui_state_highlight');
						}
					}


					$.get(url, function(ret) {
						if (ret.indexOf('"error"') != -1) {
							var errObj = eval('(' + ret + ')');
							win.close();
							goods.show_message('出错啦!', errObj['msg'], 'E')
						} else {
							win.content(ret);
							var form = win.DOM.content.find('form');
							
							
							/*添加申诉凭证*/
							var pos = 1;
							var wrap_upload = form.find('.respond-appeal-proof');
							var copy = wrap_upload.find("ul").html();
							var addProof = wrap_upload.find('.add');

							wrap_upload.find("ul").on('click', 'li a', function() {
								$(this).parent().remove();
								addProof.removeClass('disabled');
							}).find('a').hide();
							addProof.click(function() {
								var len = wrap_upload.find(".upload_img").length;
								if (len < 5) {
									var file = $(copy);
									file.find('input').attr('name', 'img_' + (++pos));
									form.find("ul").append(file);
									len===4 && addProof.addClass('disabled');
								}
							});
							
							form.submit(function() {
								var appeal_form = $('#appeal_reply_form');
								var appeal_reason = appeal_form.find('textarea[name=content]');
								var appeal_img = appeal_form.find('input[name=appeal_img_1]');
								if (goods.str_len(appeal_reason.val()) <= 0) {
									return goods.show_message('出错啦!', '请填写回应申诉理由', 'E')
								}

								set_btn_state(win, false);

								form.ajaxSubmit({
									type : "post",
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
							});
						}
					});
				},
				cancel : true
			});
		}
	},
	appeal_cancel : function(o){
		var oid = parseInt($(o).attr('data-oid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		if (oid > 0 && $.trim(url) != '') {
			artDialog.confirm('撤销申诉后订单状态将恢复到申诉前的状态，您确定要撤销申诉吗？', function(){
					$.get(url, function(ret) {
						var ret = ret || '{"success":false,"data":"撤销申诉未返回结果，撤销失败"}';
						ret = $.parseJSON(ret);
						if(ret.success){
							$(o).parents().filter('tr').remove();
							goods.show_message('撤销申诉!', ret.data, 'S', {time:3,refresh:true})
						}else{
							goods.show_message('撤销申诉!', ret.data, 'E')
						}
					});
				}, function(){return true;}
			); 
		}
	},
	/**
	 * 订单详情
	 */
	order_detail : function(o) {
		var oid = parseInt($(o).attr('data-oid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		if (oid > 0 && $.trim(url) != '') {
			art.dialog({
				id:'order_detail',
				lock : true,
				fixed : true,
				title : '订单详情',
				width : '40em',
				drag : false,
				init : function() {
					var win = this;
					$.get(url, function(ret) {
						win.content(ret);
					});
				},
				button : [{
					name : '关闭窗口',
					focus : true
				}]
			});
		}
	},
	do_submit : function(msg, time, initfunc, closefunc, icon) {
		return artDialog.tips(msg, time, initfunc, closefunc, icon);
	},
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
			ok : true
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
	turn_pages : function(win, a) {
		var ajaxurl = goods.ajax_url($(a).attr('ajaxhref'));
		$.get(ajaxurl, function(ret) {
			if (ret.indexOf('"error"') != -1) {
				var errObj = eval('(' + ret + ')');
				win.close();
				goods.show_message('出错啦!', errObj['msg'], 'E')
			} else {
				win.content('<div style="width: 680px; max-height: 420px; _height: 420px; overflow: auto;">'+ ret + '</div>');
			}
		});
	},
	/**
	 * 商品操作记录
	 */
	log : function(o){
		var gid = parseInt($(o).attr('data-gid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		if (gid > 0 && $.trim(url) != '') {
			art.dialog({
				id:'goods_log',
				lock : true,
				fixed : true,
				title : '操作记录',
				width : '60em',
				drag : false,
				init : function() {
					var win = this;
					$.get(url, function(ret) {
						if (ret.indexOf('"error"') != -1) {
							var errObj = eval('(' + ret + ')');
							win.close();
							goods.show_message('出错啦!', errObj['msg'], 'E')
						} else {
							win.content('<div style="width: 680px; max-height: 420px; _height: 420px; overflow: auto;">'+ ret + '</div>');
						}
					});
				},
				button : [{
					name : '关闭窗口',
					focus : true
				}]
			});
		}
	},
	/**
	 *  商品推荐操作
	 */
	recommend:function(o){
		var gid = parseInt($(o).attr('data-gid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		art.dialog.confirm('您将推荐该产品，是否继续?', function() {
			$.get(url, {gid:gid}, function(ret) {
				if(ret.status == 1){
					goods.show_message('提示消息', ret.info, 'S');
					location.reload();
				}else{
					goods.show_message('提示消息', ret.info, 'E');
					location.reload();
				}
			});
			},function(){return true;}
		); 
	},
	/**
	 * 商品取消推荐
	 */
	unrecommend:function(o){
		var gid = parseInt($(o).attr('data-gid'));
		var url = $(o).attr('data-url');
		url = goods.ajax_url(url);
		art.dialog.confirm('您将取消该产品的推荐，是否继续?', function() {
			$.get(url, {gid:gid}, function(ret) {
				if(ret.status == 1){
					goods.show_message('提示消息', ret.info, 'S');
					location.reload();
				}else{
					goods.show_message('提示消息', ret.info, 'E');
					location.reload();
				}
			});
			},function(){return true;}
		); 
	},
	/* 活动结算 */
	activity_over:function(url,id){
		$.get(url,{
			goods_id : id,
		}, function(ret) {
			if (ret.status==0) {
				art.dialog({
					lock: true,
					fixed: true,
					icon: 'face-sad',
					title: '错误提示',
					content: ret.info,
					ok: true,
				});
			}else{
				art.dialog({
					title : '【活动结算】',
					id:'activity_over',
					drag : true,
					lock : true,
					padding : 0,
					content : ret,
					width:550,
					height:450,
					okVal:'点此确认结算',
					ok : function() {
						$.post(url,{
							goods_id : id
						},function(rst){
							if (rst.status==1) {
								succes(rst.info);
							}else{
								error(rst.info);
							}
						},'JSON');
					},
					cancel:true,
					cancelVal:'取消结算',
				});
			}			
		});
	},
};