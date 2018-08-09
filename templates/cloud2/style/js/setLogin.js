//input移入初始化
function setInputValue(obj,otype)
{
	var aValue = $(obj).val();
	var aColor = $(obj).css('color');
	
	$(obj).focus(function(){
		
		if(otype)$(this).attr('type',otype);
		
		if($(this).val() == aValue)
		{
			$(this).css('color','#666');
			$(this).val('');
		}
		else
		{
			return;
		}
	});
	
	$(obj).blur(function(){
		if($(this).val() != '')
		{
			return;
		}
		else
		{
			$(this).css('color',aColor);
			$(this).val(aValue);
		}
	});	
}