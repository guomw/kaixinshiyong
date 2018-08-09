function getStyle(obj,attr)
{
	if(obj.currentStyle)
	{
		return obj.currentStyle[attr];
	}
	else
	{
		return getComputedStyle(obj,false)[attr];
	}
}
timer = null;
function move(obj,json,pace,fnEnd)
{
	clearInterval(obj.timer);
	
	obj.timer = setInterval(function(){
		var mobel = true;
		var cur = 0;
		for(attr in json)
		{
			if(attr == 'opacity')
			{
				cur = Math.round(parseFloat(getStyle(obj,attr))*100);	
			}
			else
			{
				cur = parseInt(getStyle(obj,attr));
			}
			if(json[attr] != cur)
			{
				mobel = false;
			}
			if(pace)
			{
				speed = (json[attr] - cur)/pace;
			}
			else
			{
				speed = (json[attr] - cur)/10;
			}	
			speed = speed > 0 ? Math.ceil(speed) : Math.floor(speed);

			cur+=speed;
				
			if(attr == 'opacity')
			{
				obj.style.filter = 'alpha(opacity:'+cur+')';
				obj.style.opacity = cur/100;
			}
			else
			{
				obj.style[attr] = cur+'px';
			}		
		}

		if(mobel)
		{
			clearInterval(obj.timer);
			if(fnEnd)
			{
				fnEnd();
			}
		}		
	},30);
		
}