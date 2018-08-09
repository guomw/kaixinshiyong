// 1.1  2015 - 09 - 27

;(function($,window,document,undefined){
	var Validate = function(ele,opt){
		this.ele = ele;
		this.defaults = {
			'username' : {
				'mate' : false,
				'focus' : '请输入用户名！',
				'null' : '用户名不能为空！',
				'error' : '用户名输入错误！',
				'success' : '用户名输入正确！',
				'reg' : '(^[A-Za-z0-9]{6,16}$)|(^[\u4E00-\u9FA5]{2,8}$)'
			},
			'password' : {
				'mate' : false,
				'focus' : '请输入密码！',
				'null' : '密码不能为空！',
				'error' : '密码输入错误！',
				'success' : '密码输入正确！',
				'reg' : '[a-zA-Z0-9]{6,16}'
			},
			'repeatpass' : {
				'mate' : ['password','确认密码与密码输入不一致'],
				'focus' : '请输入确认密码！',
				'null' : '确认密码不能为空！',
				'error' : '确认密码输入错误！',
				'success' : '确认密码输入正确！',
				'reg' : '[a-zA-Z0-9]{6,16}'
			},
			'phone' : {
				'mate' : false,  
				'focus' : '请输入手机号！',
				'null' : '手机号不能为空！',
				'error' : '手机号输入错误！',
				'success' : '手机号输入正确！',
				'reg' : '^1\\d{10}$'
			},
			'email' : {
				'mate' : false,
				'focus' : '请输入您的邮箱！',
				'null' : '邮箱不能为空！',
				'error' : '邮箱输入不合法！',
				'success' : '输入正确！',
				'reg' : '^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$'
			},
			change : false,  // 是否实时监测状态
			focus : function(data){
				$(this).next('span').remove();
				$(this).after('<span>'+data+'<span>');
			},
			error : function(data){
				$(this).next('span').remove();
				$(this).after('<span>'+data+'<span>');
			},
			success : function(data){
				$(this).next('span').remove();
				$(this).after('<span>'+data+'<span>');
			},
			null : function(data){
				$(this).next('span').remove();
				$(this).after('<span>'+data+'<span>');
			},
			status : function(status){
				
			},
		}
		this.opt = $.extend({},this.defaults,opt);
	}
	Validate.prototype = {
		init : function(){
			this.focus();
			this.blur();
			this.sub();
			this.opt.change && this.change();
		},
		change : function(){
			var This = this;
			this.ele.find('input[validate]').on('input propertychange',function(){
				var val = $(this).val().trim();
				var reg = new RegExp($(this).attr('reg') || This.opt[$(this).attr('validate')].reg);
				if(val == ''){
					var nul = $(this).attr('null') || This.opt[$(this).attr('validate')]['null'];
					This.opt.null.call(this,nul);
					$(this).attr('data-status',false);
					This.outsub();
					return false;
				}else if(!reg.test($(this).val())){
					var err = $(this).attr('error') || This.opt[$(this).attr('validate')].error;
					This.opt.error.call(this,err);
					$(this).attr('data-status',false);
					
					This.outsub();
					return false;
				}else{
					if($(this).attr('mate') && $(this).attr('mate').split(',').length == 2){
						This.opt[$(this).attr('validate')].mate = $(this).attr('mate').split(',');
					}
					// 存在
					if(This.opt[$(this).attr('validate')].mate){
						var val = This.ele.find('input[validate="'+This.opt[$(this).attr('validate')].mate[0]+'"]').val().trim();
						if($(this).val().trim() != val){
							This.opt.error.call(this,This.opt[$(this).attr('validate')].mate[1]);
							$(this).attr('data-status',false);
							This.outsub();
							return false;
						}else{
							var suc = $(this).attr('success') || This.opt[$(this).attr('validate')].success;
							This.opt.success.call(this,suc);
							$(this).attr('data-status',true);
							// 每次成功比较一次
							This.outsub();
							return false;
						}
					}else{
						var suc = $(this).attr('success') || This.opt[$(this).attr('validate')].success;
						This.opt.success.call(this,suc);
						$(this).attr('data-status',true);
						This.outsub();
						return false;
					}
				}
			});
		},
		outsub : function(){
			var status = false;
			if(this.ele.find('input[validate]').length == this.ele.find('input[validate][data-status="true"]').length){  // 全部正确
				status = true;
			}
			this.opt.status.call(this.ele,status);
		},
		focus : function(){
			var This = this;
			this.ele.find('input[validate]').on('focus',function(){
				$(this).attr('data-status',false);  // 状态值
				//如果不存在则创建一个内置对象
				if(!This.opt[$(this).attr('validate')]){
					This.opt[$(this).attr('validate')] = {
						'mate' : false,
						'focus' : '请输入！',
						'null' : '不能为空！',
						'error' : '输入不合法！',
						'success' : '输入正确！',
						'reg' : ''
					}
				}
				var txt = $(this).attr('focus') || This.opt[$(this).attr('validate')].focus;
				This.opt.focus.call(this,txt);
			});
		},
		blur : function(){
			var This = this;
			this.ele.find('input[validate]').on('blur',function(){
				var val = $(this).val().trim();
				var reg = new RegExp($(this).attr('reg') || This.opt[$(this).attr('validate')].reg);
				if(val == ''){
					var nul = $(this).attr('null') || This.opt[$(this).attr('validate')]['null'];
					This.opt.null.call(this,nul);
					$(this).attr('data-status',false);
					This.outsub();
					return false;
				}else if(!reg.test($(this).val())){
					var err = $(this).attr('error') || This.opt[$(this).attr('validate')].error;
					This.opt.error.call(this,err);
					$(this).attr('data-status',false);
					
					This.outsub();
					return false;
				}else{
					if($(this).attr('mate') && $(this).attr('mate').split(',').length == 2){
						This.opt[$(this).attr('validate')].mate = $(this).attr('mate').split(',');
					}
					// 存在
					if(This.opt[$(this).attr('validate')].mate){
						var val = This.ele.find('input[validate="'+This.opt[$(this).attr('validate')].mate[0]+'"]').val().trim();
						if($(this).val().trim() != val){
							This.opt.error.call(this,This.opt[$(this).attr('validate')].mate[1]);
							$(this).attr('data-status',false);
							This.outsub();
							return false;
						}else{
							var suc = $(this).attr('success') || This.opt[$(this).attr('validate')].success;
							This.opt.success.call(this,suc);
							$(this).attr('data-status',true);
							// 每次成功比较一次
							This.outsub();
							return false;
						}
					}else{
						var suc = $(this).attr('success') || This.opt[$(this).attr('validate')].success;
						This.opt.success.call(this,suc);
						$(this).attr('data-status',true);
						This.outsub();
						return false;
					}
				}
			});
		},
		sub : function(){
			var This = this;
			this.ele.submit(function(event){
				This.ele.find('input[validate]').blur();
				if( $(this).find('input[validate]').length != $(this).find('input[data-status="true"]').length){
					event.preventDefault();
				}
			});
			var id = this.ele.attr('id') ? '#'+this.ele.attr('id') : '.' + this.ele.attr('class').split(',')[0];
			this.ele.find('a,button,input[type="button"]').on('mousedown',function(){
				This.ele.find('input[validate]').blur();
			});
		},
	}
	$.fn.validate = function(opt){
		return new Validate(this,opt).init();
	}
	
})(jQuery,window,document);
