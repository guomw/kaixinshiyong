$(function() {
		var noclick = 0;//当点击进行时，设置不能重复
		
		function setDegree($obj, deg) {
			$obj.css({
				'transform': 'rotate(' + deg + 'deg)',
				'-moz-transform': 'rotate(' + deg + 'deg)',
				'-o-transform': 'rotate(' + deg + 'deg)'
			});
		}
		
		function rotate() {
			var $tar = $('.lo-bg'),
				i,
				cnt = 100, //用做ratio的索引(10-29)  
				total = 0, //记录上一次的变化结果  
				ratio = [], //存放角度的变化比例，制造快慢过渡效果  
				offset = 4 , //0-9,代表需要停到的奖项,由后端传入  
				amount = 18 - (0.18 * offset), //每次每多出40/200=0.2度,200次就多偏转36度   
				result =  '奖项名称用于显示,由后端传入'; //奖项名称用于显示,由后端传入  
	
			ratio[1] = [0.2, 0.4, 0.6, 0.8, 1, 1, 1.2, 1.4, 1.6, 1.8];
			ratio[2] = [1.8, 1.6, 1.4, 1.2, 1, 1, 0.8, 0.6, 0.4, 0.2];
			for (i = 0; i < 200; i++) {
				//设计为200次50ms的间隔，10s出结果感觉比较好  
				setTimeout(function() {
					//计算每次偏转增量，对应阶段的增减比例最终造成快慢变化  
					var deg = amount * (ratio[String(cnt).substr(0, 1)][String(cnt).substr(1, 1)]);
					setDegree($tar, deg + total); //改变偏转  
					total += deg; //记录  
					cnt++; //依据次数用作ratio的索引，这里用到了闭包不能使用i  
				}, i * 20);
			}
			setTimeout(function() {
				alert(result);
				noclick = 0;   //当返回完结果之后  初始化点击
			}, 200 * 20 + 500);
		}
		//绑定事件，点击指针开始
		
		var Flag = 10;   // 设置用户每天允许抽奖的次数
		$(function() {
			$(".l-btn").click(function() {
				if(Flag == 0){ alert('您今天的次数已经用完了，改天再来吧！'); return; }
				if(noclick == 1)return;
				noclick = 1;
				Flag--;  //减去用户已使用次数
				rotate();
			});
		});
	});