/*
	选项卡封装
	by 井底的蛙
	2008-2-4
	window.onload = function()
{
	var aa = new opCard();	
	// 默认的onclick中，第一个例子
	var bba = new opCard();
	bba.bind = ["a2","li","b2","div"];
	bba.style = ["style1","style2","style3"];
	bba.overStyle = true;
	bba.creat();
	bba = null;
}

*/
opCard = function(){
	this.bind = new Array();
	this.index = 0;		//默认显示哪个选项卡，从0开始
	
	this.style = new Array();		//["","",""]
	this.overStyle = false;		//选项是否有over, out变换样式事件，样式为this.style[2]
	this.overChange = false;		//内容是否用over, out直接激活
	this.menu = false;				//菜单类型
	this.nesting = [false,false,"",""];		//是否嵌套，后面2个参数是指定menu,info的子集深度所用id
	
	this.auto = [false, 1000];		//自动滚动[true,2000]
	this.timerID = null;			//自动播放的
	this.menutimerID = null;		//菜单延时的
	
	this.creat = function(func){
		var _arrMenu = document.getElementById(this.bind[0]).getElementsByTagName(this.bind[1]);
		var _arrInfo = document.getElementById(this.bind[2]).getElementsByTagName(this.bind[3]);
		var my = this, i;
		var argLen = arguments.length;
		var arrM = new Array();
		
		if(this.nesting[0] || this.nesting[1]){	// 有选项卡嵌套
			// 过滤出需要的数据
			var arrMenu = this.nesting[0]?getChilds(_arrMenu,this.bind[0],2):_arrMenu;
			var arrInfo = this.nesting[1]?getChilds(_arrInfo,this.bind[2],3):_arrInfo;
		}else{
			var arrMenu = _arrMenu;
			var arrInfo = _arrInfo;
		}
		
		var l = arrMenu.length;
		if(l!=arrInfo.length){alert("菜单和内容必须拥有相同的数量\n如果需要，你可以放一个空的在那占位。")}
		
		// 修正
		if(this.menu){this.auto=false;this.overChange=true;} //如果是菜单，则没有自动运行，有over, out直接激活
		
		// 循环添加各个事件等
		for(i=0;i<l;i++){
			arrMenu[i].cName = arrMenu[i].className;
			arrMenu[i].className = (i!=this.index || this.menu)?getClass(arrMenu[i],this.style[0]):getClass(arrMenu[i],this.style[1]);		//加载样式，菜单的话统一样式
			
			if(arrMenu[i].getAttribute("skip")){ // 需要跳过的容器
				if(this.overStyle || this.overChange){	// 有over, out 改变样式 或者 激活
					arrMenu[i].onmouseover = function(){changeTitle(this, 2);autoStop(this, 0);}
					arrMenu[i].onmouseout = function(){changeTitle(this, 0);autoStop(this, 1);}
				}
				arrMenu[i].onclick = function(){if(argLen==1){func();}}
				arrInfo[i].style.display = "none";
				continue;
			}
			
			if(i!=this.index || this.menu){arrInfo[i].style.display="none"};	//隐藏初始化，菜单的话全部隐藏
			arrMenu[i].index = i;	//记录自己激活值[序号]
			arrInfo[i].index = i;
			
			
			if(this.overChange){	//有鼠标over, out事件
			
				arrMenu[i].onmouseover = function(){changeOption(this);my.menu?changeMenu(1):autoStop(this, 0);}
				arrMenu[i].onmouseout = function(){changeOption(this);my.menu?changeMenu(0):autoStop(this, 1);}
			}else{	//onclick触发
				arrMenu[i].onclick = function(){changeOption(this);autoStop(this, 0);if(argLen==1){func()}}
				if(this.overStyle){	// 有over, out 改变样式
					arrMenu[i].onmouseover = function(){changeTitle(this, 2);autoStop(this, 0);}
					arrMenu[i].onmouseout = function(){changeTitle(this, 0);autoStop(this, 1);}
				}else{	// 没有over, out 改变样式
					if(this.auto[0]){	// 有自动运行
						arrMenu[i].onmouseover = function(){autoStop(this, 0);}
						arrMenu[i].onmouseout = function(){autoStop(this, 1);}
					}
				}
			}
			
			if(this.auto[0] || this.menu){	//arrinfo 控制自动播放
				arrInfo[i].onmouseover = function(){my.menu?changeMenu(1):autoStop(this, 0);}
				arrInfo[i].onmouseout = function(){my.menu?changeMenu(0):autoStop(this, 1);}
			}
		}	//for结束
		
		if(this.auto[0]){
			this.timerID = setTimeout(autoMove,this.auto[1])
		}
		
		// 自动播放
		function autoMove(){
			var n;
			n = my.index + 1;
			if(n==l){n=0};
			while(arrMenu[n].getAttribute("skip"))		// 需要跳过的容器
			{
				n += 1;
				if(n==l){n=0};
			}
			changeOption(arrMenu[n]);
			my.timerID = setTimeout(autoMove,my.auto[1]);
		}
		
		// onmouseover时，自动播放停止。num：0为over，1为out。 obj暂时无用。 -_-!!
		function autoStop(obj, num){
			if(!my.auto[0]){return;}
			//if(obj.index==my.index)
			num == 0 ? clearTimeout(my.timerID) : my.timerID = setTimeout(autoMove,my.auto[1]);
		}
		
		// 改变选项卡
		function changeOption(obj){
			arrMenu[my.index].className = getClass(arrMenu[my.index],my.style[0]);	//修改旧内容
			arrInfo[my.index].style.display = "none";	//隐藏旧内容
			
			obj.className = getClass(obj,my.style[1]);		//修改为新样式
			arrInfo[obj.index].style.display = "";	//显示新内容
			
			my.index = obj.index;	//更新当前选择的index
		}
		
		/*		
			只有onclick时，overStyle的onmouseover,onmouseout事件。用来预激活
			obj：目标对象。	num：1为over，0为out
		*/
		function changeTitle(obj, num){
			if(!my.overStyle){return;};
			if(obj.index!=my.index){obj.className = getClass(obj,my.style[num])}
		}
		
		/*		
			菜单类型时用
			obj：目标对象。	num：1为over，0为out
		*/
		function changeMenu(num){
			if(!my.menu){return;}
			num==0?my.menutimerID = setTimeout(menuClose,1000):clearTimeout(my.menutimerID)
		}
		
		//关闭菜单
		function menuClose(){
			arrInfo[my.index].style.display = "none";
			arrMenu[my.index].className = getClass(arrMenu[my.index],my.style[0]);
		}
		
		// 得到className（防止将原有样式覆盖）
		function getClass(o, s){
			if(o.cName==""){return s}
			else{return o.cName + " " + s}
		}
		
		//嵌套情况下得到真正的子集
		function getChilds(arrObj, id, num){
			var depth = 0;
			var firstObj = my.nesting[num]==""?arrObj[0]:document.getElementById(my.nesting[num]);		//得到第一个子集
			do{	//计算深度
				if(firstObj.parentNode.getAttribute("id")==id){break;}else{depth+=1}
				firstObj = firstObj.parentNode;
			}while(firstObj.tagName.toLowerCase()!="body")	// body强制退出。
			var t;
			var arr = new Array();
			for(i=0;i<arrObj.length;i++){	//过滤出需要的数据
				t = arrObj[i], d = 0;
				do{
					if(t.parentNode.getAttribute("id")==id && d == depth){
						arr.push(arrObj[i]);break;		//得到数据
					}else{
						if(d==depth){break;};d+=1;
					}
					t = t.parentNode;
				}
				while(t.tagName.toLowerCase()!="body")	// body强制退出
			}
			return arr;
		}
	}
}