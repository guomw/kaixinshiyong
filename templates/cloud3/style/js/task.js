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
		ok: true
	});
};
var task = {
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
	 * task.show_message('操作提示!', '提示文本', 'S', {time:3, refresh:true});
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
		var ajaxurl = task.ajax_url($(a).attr('ajaxhref'));
		$.get(ajaxurl, function(ret) {
			if (ret.indexOf('"error"') != -1) {
				var errObj = eval('(' + ret + ')');
				win.close();
				task.show_message('出错啦!', errObj['msg'], 'E');
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
		url = task.ajax_url(url);
		if (gid > 0 && $.trim(url) != '') {
			art.dialog({
				id:'task_log',
				lock : true,
				fixed : true,
				title : '操作记录',
				drag : false,
				init : function() {
					var win = this;
					$.get(url, function(ret) {
						if (ret.indexOf('"error"') != -1) {
							var errObj = eval('(' + ret + ')');
							win.close();
							task.show_message('出错啦!', errObj['msg'], 'E');
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
	/*删除图片*/
	delete_image:function(o){
		var id = parseInt($(o).attr('data-id'));
		var name = $(o).attr('data-name');
		var url = $(o).attr('data-url');
		url = task.ajax_url(url);
		if ($.trim(url) != '') {
			art.dialog.confirm('您确定删除该图片，是否继续?', function() {
				$.get(url, {name:name,id:id}, function(ret) {
					if(ret.status == 1){
						task.show_message('删除成功', ret.info, 'S');
						location.reload();
					}else{
						task.show_message('删除失败', ret.info, 'E');
						location.reload();
					}
				});
			},function(){return true;});
		}
	},
	/*用户记录*/
	person_log:function(o){
		var id = parseInt($(o).attr('data-id'));
		var url = $(o).attr('data-url');
		url = task.ajax_url(url);
		if (id > 0 && $.trim(url) != '') {
			art.dialog({
				id:'person_log',
				lock : true,
				fixed : true,
				title : '操作记录',
				width : '60em',
				drag : false,
				init : function() {
					var win = this;
					$.get(url, {id:id},function(ret) {
						if (ret.indexOf('"error"') != -1) {
							var errObj = eval('(' + ret + ')');
							win.close();
							task.show_message('出错啦!', errObj['msg'], 'E');
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
	/*提交答案*/
	submit_answer:function(o){
		var id = parseInt($(o).attr('data-id'));
		var url = $(o).attr('data-url');
		url = task.ajax_url(url);
		var content = $("#content").val();
		var price = $(o).attr('data-price');
		if (id > 0 && $.trim(url) != '') {
			if(content != ''){
				$.get(url, {content:content,id:id},function(ret) {
					if(ret.status == 1){
						art.dialog({
							lock: true,
							fixed: true,
							icon: 'face-smile',
							title: '正确提示',
							content: '恭喜您回答成功,您已获得' + price + '奖励',
							ok: function(){
								location.href=ret.url;
							}
						});
					}else{
						art.dialog({
							lock: true,
							fixed: true,
							icon: 'face-sad',
							title: '错误提示',
							content: ret.info,
							ok:  function(){
								window.location.reload(true);
							}
						});
					}
				});					
			}else{
				art.dialog({
					lock: true,
					fixed: true,
					icon: 'face-sad',
					title: '错误提示',
					content: '请输入内容',
					ok: true
				});
			}
		}
	}
};