var admin_product = (function() {
	return {
		/* 审核通过 */
		pass:function(product_id) {
			window.top.art.dialog({
				id:'pass',
				iframe: 'index.php?m=product&c=product&a=pass&product_id=' + product_id, 
				title:'审核通过',
				padding:'1px',
				width:'500px',
				height:'350px',
				lock:true,
				ok:function() {
					var d = window.top.art.dialog({id: 'pass'}).data.iframe;
					var form = d.document.getElementById('dosubmit');
					form.click();
					return false;
				},
				cancel:function() {
					return true;
				}
			});
		},

		/* 拒绝 */
		refuse:function(product_id) {
			confirmurl('index.php?m=product&c=product&a=refuse&product_id='+product_id, '您确认拒绝该活动商品并退换保证金给商家吗？');
		},
        /* 屏蔽 */
        blocked:function(product_id) {
            _prompt('屏蔽后商家将不能对本产品做任何操作（包括但不限于编辑、结算、订单审核/付款等）；<font color=red>请注意：本操作不可逆，请确认该商品违规或因不可抗力因素需要强制下架。</font>', function(val){
                var bool = false;
                if(val != '' || confirm('您确认不输入任何操作理由？')) {
                    $.ajax({
                        url : 'index.php?m=product&c=product&a=blocked',
                        data: {product_id:product_id, msg:val},
                        type:'get',
                        dataType:'JSON',
                        async:false,
                        success:function(ret) {
                            window.top.art.dialog({id:'Prompt'}).title(ret.info);
                            bool = (ret.status == 1) ? true : false;
                            if(ret.status == 1) {
                                setTimeout('window.location.reload()', 1000);
                            }
                        }
                    });
                    return bool;
                }
                return false;
            });
        },
		/* 日志 */
		log:function(product_id) {
			$.getJSON('index.php?m=product&c=product&a=log', {product_id : product_id}, function(ret){
				var _content = '';
				if(ret.status == 1) {
					_content += '<div class="table-list"><table width="500"><thead><tr>';
					_content += '<th>ID</th>';
					_content += '<th>操作理由</th>';
					_content += '<th>操作人</th>';
					_content += '<th>操作时间</th>';
					_content += '<th>IP</th>';
					_content += '</tr></thead>';
					$.each(ret.data, function(i, n) {
						var is_sys = (n.is_sys == 1) ? '<font color="red">(管理员)</font>' : '';
						_content += '<tbody><tr>';
						_content += '<td>'+n.id+'</td>';
						_content += '<td>'+n.msg+'</td>';
						_content += '<td>'+n.username + is_sys + '</td>';
						_content += '<td>'+n.dateline+'</td>';
						_content += '<td>'+n.clientip+'</td>';
						_content += '</tr></tbody>';
					})
					_content += '</table></div>';
					window.top.art.dialog({
						id:product_id+'_log',
						lock:true,
						title:'查看日志',
						padding:'1px',
						content:_content,
						ok:function() {
							return true;
						}
					})
				} else {
					alert('无任何日志');
					return false;
				}
			});
		}

	};
})();