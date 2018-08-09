function error_no(msg) {	//	错误提示，不刷新
	art.dialog({
		lock: true,
		fixed: true,
		icon: 'face-sad',
		title: '错误提示',
		content: msg,
		ok: true,
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

var order = (function() {
	return {
		/* 订单审核 */
		check:function(o) {
			var oid = parseInt($(o).attr('data-id'));
			var status = $(o).attr('data-status');
			var title = $(o).attr('data-title');
			if(status == 3){//待审核
			art.dialog({
	                id:oid+'_审核',
	                lock:true,
	                title:'订单审核',
	                padding:'1px',
	                content:'<div style="padding:10px 10px;">您确定通过，该操作不可逆转，是否确认？</div>',
	                okVal:'订单号正确审核通过',
	                cancelVal:'订单号错误审核拒绝',
	                ok:function() {
	                    $.get('index.php?m=order&c=order&a=pass',{oid:oid},function(data){
	                    	if(data.status == 1){
	                    		art.dialog({
	        						id:oid,
	        						lock:true,
	        						title:'提示',
	        						padding:'1px',
	        						content:data.info,
	        						ok:function() {
	        							location.href=data.url;
	        							return true;
	        						}
	        					});
	                    	}else{
	                    		art.dialog({
	        						id:oid,
	        						lock:true,
	        						title:'提示',
	        						padding:'1px',
	        						content:'<div style="padding:10px 10px;">操作失败</div>',
	        						ok:function() {
	        							location.href=data.url;
	        							return true;
	        						}
	        					});
	                    	}
	                    },'json');
	                },
	                cancel:function(){
	                	var html = [];
	        			html.push('<form class="woyao-show" method="post" enctype="multipart/form-data"><table>');
	        			html.push('<tr><th>标题:</th><td>', title , '</td></tr><br/><br/>');
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
	        					form.submit(function() {
	        						var text = form.find('textarea').val();
	        						if ($.trim(text) == '') {
	        							error_no('原因不能为空！');
	        							form.find('textarea').val('').focus();
	        							return false;
	        						}
	        						$.ajax({
	        							type : "POST",
	        							url  : site.site_root+"index.php?m=order&c=order&a=refuse",
	        							data : {oid:oid,content:text},
	        							dataType : 'JSON',
	        							error:function(ret){
	        								error_no('系统错误，请稍后重试。');
	        							},
	        							success : function(ret) {
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
	                }
	            });
			}else{//审核通过
				var mod = $(o).attr('data-mod');
				art.dialog({
	                id:oid+'_订单付款',
	                lock:true,
	                title:'订单付款',
	                padding:'1px',
	                content:'<div style="padding:10px 10px;">您确定付款，该操作不可逆转，是否确认？</div>',
	                okVal:'确认付款给买家',
	                cancelVal:'撤销订单',
	                ok:function() {
	                    $.get('index.php?m=order&c=order&a=pay',{oid:oid,mod:mod},function(data){
	                    	if(data.status == 1){
	                    		art.dialog({
	        						id:oid,
	        						lock:true,
	        						title:'提示',
	        						padding:'1px',
	        						content:data.info,
	        						ok:function() {
	        							location.href=data.url;
	        							return true;
	        						}
	        					});
	                    	}else{
	                    		art.dialog({
	        						id:oid,
	        						lock:true,
	        						title:'提示',
	        						padding:'1px',
	        						content:'<div style="padding:10px 10px;">操作失败</div>',
	        						ok:function() {
	        							location.href=data.url;
	        							return true;
	        						}
	        					});
	                    	}
	                    },'json');
	                },
	                cancel:function(){
	                	  $.get('index.php?m=order&c=order&a=cancel',{oid:oid},function(data){
		                    	if(data.status == 1){
		                    		art.dialog({
		        						id:oid,
		        						lock:true,
		        						title:'提示',
		        						padding:'1px',
		        						content:data.info,
		        						ok:function() {
		        							location.href=data.url;
		        							return true;
		        						}
		        					});
		                    	}else{
		                    		art.dialog({
		        						id:oid,
		        						lock:true,
		        						title:'提示',
		        						padding:'1px',
		        						content:'<div style="padding:10px 10px;">操作失败</div>',
		        						ok:function() {
		        							location.href=data.url;
		        							return true;
		        						}
		        					});
		                    	}
		                    },'json');
	                }
	            });
			}
		}
	};
})();