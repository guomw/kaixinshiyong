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
var bind = {
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

	/**
	 * 删除亚马逊帐号
	 */
	unbind:function(o){
		var id = parseInt($(o).attr('data-id'));
		var url = $(o).attr('data-url');
		url = bind.ajax_url(url);
		art.dialog.confirm('确定删除该亚马逊帐号？', function() {
			$.get(url, {id:id}, function(ret) {
				if(ret.status == 1){
					bind.show_message('提示信息',ret.info,'S');
					// location.reload();
				}else{
					bind.show_message('提示信息',ret.info,'E');
					// location.reload();
				}
			});
			},function(){return true;}
		); 
	},
/**
 * 成为皇冠商家
 */
	hmerchant:function(o){
		var type = parseInt($(o).attr('data-type'));
		var url = $(o).attr('data-url');
		var money = $(o).attr('data-money');
		url = bind.ajax_url(url);
		art.dialog.confirm('您确定购买/续费皇冠商家？ $'+money, function() {
			$.get(url, {money:money,type:type}, function(ret) {
				if(ret.status == 1){
					bind.show_message('提示信息',ret.info,'S');
					location.reload();
				}else{
					bind.show_message('提示信息',ret.info,'E',{time:3, url:ret.url,refresh:false});
				}
			});
			},function(){return true;}
		);
	},
	/**
	 * 成为钻石商家
	 */
	zmerchant:function(o){
		var type = parseInt($(o).attr('data-type'));
		var url = $(o).attr('data-url');
		var money = $(o).attr('data-money');
		url = bind.ajax_url(url);
		art.dialog.confirm('您确定购买/续费钻石商家？ $'+money, function() {
			$.get(url, {money:money,type:type}, function(ret) {
				if(ret.status == 1){
					bind.show_message('提示信息',ret.info,'S');
					location.reload();
				}else{
					bind.show_message('提示信息',ret.info,'E',{time:3, url:ret.url,refresh:false});
				}
			});
			},function(){return true;}
		);
	},

    /**
     * 成为VIP用户
     */
    member_vip:function(o){
        var type = parseInt($(o).attr('data-type'));
        var url = $(o).attr('data-url');
        var money = $(o).attr('data-money');
        url = bind.ajax_url(url);
        art.dialog.confirm('您确定购买VIP会员？ $'+money, function() {
                $.get(url, {money:money,type:type}, function(ret) {
                    if(ret.status == 1){
                        bind.show_message('提示信息',ret.info,'S');
                        location.reload();
                    }else{
                        bind.show_message('提示信息',ret.info,'E',{time:3, url:ret.url,refresh:false});
                    }
                });
            },function(){return true;}
        );
    },
	
	/* 设置为默认账号 */
	setdefault:function(o){
		var id = parseInt($(o).attr('data-id'));
		var url = $(o).attr('data-url');
		url = bind.ajax_url(url);
		art.dialog.confirm('确定设置该亚马逊帐号为默认？', function() {
			$.get(url, {id:id}, function(ret) {				
				if(ret.status == 1){
					bind.show_message('提示信息',ret.info,'S');
				}else{
					bind.show_message('提示信息',ret.info,'E');
				}
			});
			},function(){return true;}
		);
	}
	
}; 