function confirmurl(url,message) {
	url = url+'&fromhash='+fromhash ;
	if(confirm(message)) redirect(url);
}

/**
 * 弹窗确认步骤封装
 * @param {string} msg  消息内容
 * @param {function} callback 回调函数
 * @param {string} value 默认值
 */
function _prompt(msg, callback, value){
    value = value || '';
    var input;
    var _content = '<div class="pad-10" style="text-align:left;">';
    if(msg) {
        _content += '<p class="explain-col mb10">'+ msg +'</p>';
    }
    _content += '<p>请输入操作理由：<input type="text" value="'+ value +'" class="input-text" style="width:200px;"></p>';
    _content += '</div>'
    window.top.art.dialog({
        id: 'Prompt',
		fixed: true,
        width:350,
        padding:1,
		lock: true,
        content:_content,
        init: function () {
			input = this.DOM.content.find('input')[0];
			input.select();
			input.focus();
        },
        ok:function() {
            return callback && callback.call(this, input.value);
        },
        cancel:true
    });
}

function redirect(url) {
	location.href = url;
}
//滚动条
$(function(){
	$(":text").addClass('input-text');
})

/**
 * 全选checkbox,注意：标识checkbox id固定为为check_box
 * @param string name 列表check名称,如 uid[]
 */
function selectall(name) {
	if ($("#check_box").attr("checked")=='checked') {
		$("input[name='"+name+"']").each(function() {
  			$(this).attr("checked","checked");
			
		});
	} else {
		$("input[name='"+name+"']").each(function() {
  			$(this).removeAttr("checked");
		});
	}
}
function openwinx(url,name,w,h) {
	if(!w) w=screen.width-4;
	if(!h) h=screen.height-95;
	url = url+'&fromhash='+fromhash ;
    window.open(url,name,"top=100,left=400,width=" + w + ",height=" + h + ",toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,status=no");
}
//弹出对话框
function omnipotent(id,linkurl,title,close_type,w,h) {
	if(!w) w=700;
	if(!h) h=500;
	art.dialog({id:id,iframe:linkurl, title:title, width:w, height:h, lock:true},
	function(){
		if(close_type==1) {
			art.dialog({id:id}).close()
		} else {
			var d = art.dialog({id:id}).data.iframe;
			var form = d.document.getElementById('dosubmit');form.click();
		}
		return false;
	},
	function(){art.dialog({id:id}).close()
	});void(0);
}