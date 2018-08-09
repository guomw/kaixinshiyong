//复选框事件  
//全选、取消全选的事件  
function selectAll(){  
    if ($("#SelectAll").attr("checked")) {  
        $(":checkbox").attr("checked", true);  
    } else {  
        $(":checkbox").attr("checked", false);  
    }  
}  
//子复选框的事件  
function setSelectAll(){  
    //当没有选中某个子复选框时，SelectAll取消选中  
    if (!$("#subcheck").checked) {  
        $("#SelectAll").attr("checked", false);  
    }  
    var chsub = $("input[type='checkbox'][id='subcheck']").length; //获取subcheck的个数  
    var checkedsub = $("input[type='checkbox'][id='subcheck']:checked").length; //获取选中的subcheck的个数  
    if (checkedsub == chsub) {  
        $("#SelectAll").attr("checked", true);  
    }  
}

function selectall(name) {
	if ($("#check_box").prop("checked")) {
        $("input[name='"+name+"']").attr('checked', 'checked').prop('checked', 'checked');
	} else {
        $("input[name='"+name+"']").removeAttr('checked');
	}
}
/*小数点保留两位*/
function changeTwoDecimal(num){
	num += '';  
    num = num.replace(/[^0-9|\.]/g, ''); //清除字符串中的非数字非.字符  
    if(/^0+/) //清除字符串开头的0  
        num = num.replace(/^0+/, '');  
    if(!/\./.test(num)) //为整数字符串在末尾添加.00  
        num += '.00';  
    if(/^\./.test(num)) //字符以.开头时,在开头添加0  
        num = '0' + num;  
    num += '00';        //在字符串末尾补零  
    num = num.match(/\d+\.\d{2}/)[0];  
    return num;
}
/*判断链接是否有效*/
function CheckStatus(url){
    var XMLHTTP = new ActiveXObject("Microsoft.XMLHTTP");
    XMLHTTP.open("HEAD",url,false);
    XMLHTTP.send();
    return XMLHTTP.status==200;
}
/* 确定弹窗 */
function enter(content,url) {
    art.dialog.confirm(content,function(){
        // art.dialog.tips('执行确定操作');
        location.href = url;
    }, function () {
        // art.dialog.tips('执行取消操作');
    })
}
