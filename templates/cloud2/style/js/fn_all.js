function checkclass (startclass,endclass) {//判断类名  
var arr=startclass.split(" ");//多个类名用空格分隔成不同美元素的数组;
for (var i=0; i<arr.length; i++) {  
if(arr[i]==endclass){//1.2.被分割的数组美元素里面如果有一个等于classname就返回真;
return true;  
}  
}  
return false; 
}	
function getClass (obj,classname) {//obj就是找的标签范围;  

	var obj=obj||document;//如果obj没有参数传进来的话就为假,就返回document;  
	var arr=[];//设置一个数组来存储在ie下标签的className属性值等于类名的美元素;
	if(document.getElementsByClassName){//如果条件为真,就代表浏览器为火狐;  
  		return document.getElementsByClassName(classname)//火狐下面直接返回结果;  
	}else{
		var alls=document.getElementsByTagName("*");//首先找到页面所有的标签;  
		for (var i=0; i<alls.length; i++) {  
			if(checkclass(alls[i].className,classname)){//1.1.回调函数判断类名,因为同一标签可能有多个类名;  
				arr.push(alls[i])//1.3.如果是真的,就把这个美元素推进数组里面;
 			}  
		}  
  		return arr;  
	} 	
}