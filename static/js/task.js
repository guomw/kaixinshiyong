var task = (function() {
	return {
		/* 审核通过 */
		pass:function(task_id) {
			window.top.art.dialog({
				id:'pass',
				iframe: 'index.php?m=task&c=task&a=pass&task_id=' + task_id, 
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
		refuse:function(task_id) {
			confirmurl('index.php?m=task&c=task&a=refuse&task_id='+task_id, '您确认拒绝该任务并退换佣金给商家吗？');
		},
		
		/* 用户做任务记录 */
		record:function(task_id) {
			$.getJSON('index.php?m=task&c=task&a=record', {task_id : task_id}, function(ret){
				var _content = '';
				if(ret.status == 1) {
					_content += '<div class="table-list" style="overflow-y:scroll;height:500px;"><table width="500"><thead><tr>';
					_content += '<th>ID</th>';
					_content += '<th>昵称</th>';
					_content += '<th>用户手机</th>';
					_content += '<th>时间</th>';
					_content += '<th>完成状态</th>';
					_content += '<th>IP</th>';
					_content += '</tr></thead>';
					$.each(ret.data, function(i, n) {
						_content += '<tbody><tr>';
						_content += '<td>'+n.id+'</td>';
						_content += '<td>'+n.username+'</td>';
						_content += '<td>'+n.phone+'</td>';
						_content += '<td>' +n.start_time+'</td>';
						_content += '<td>';
						if(n.status == 0){
							_content += '未完成</td>';
						}else{
							_content += '已完成</td>';
						}
						_content += '<td>'+n.clientip+'</td>';
						_content += '</tr></tbody>';
					});
					_content += '</table></div>';
					window.top.art.dialog({
						id:task_id+'_record',
						lock:true,
						title:'查看任务记录',
						padding:'1px',
						content:_content,
						ok:function() {
							return true;
						}
					});
				} else {
					alert('无任何记录');
					return false;
				}
			});
		}
	};
})();