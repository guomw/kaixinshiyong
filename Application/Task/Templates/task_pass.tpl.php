<?php defined('IN_ADMIN') or exit('No permission resources.');
$show_header = true;
?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<div class="pad-10">
    <form name="myform" id="myform" method="post" action="<?php echo U('pass') ?>">
	<input type="hidden" name="task_id" id="task_id" value="<?php echo $task_id?>">
	<fieldset>
		<legend>任务信息</legend>
		<table width="100%" class="table_form">
			<tr>
				<td width="80">商品信息：</td> 
				<td><?php echo $rs['title']?></td>
            </tr>
            <tr>
                <td>任务份数：</td> 
                <td><?php echo $rs['goods_number']; ?></td>
            </tr>
            <tr>
                <td>发布时间：</td> 
                <td><?php echo dgmdate($rs['inputtime'], 'Y/m/d H:i') ?></td>
            </tr>
        </table>
    </fieldset>
    <div class="bk15"></div>
    <fieldset>
        <legend>审核信息</legend>
        <table width="100%" class="table_form">
            <tr>
                <td width="80">上线时间：</td>
                <td>
                    <select id="startYear" name="start_time[Y]" onchange="changeYear(this.value,true)"></select>年
                    <select id="startMonth" name="start_time[m]" onchange="changeMonth(this.value,true)"></select>月
                    <select id="startDay" name="start_time[d]"></select>日
                    <input type="text" id="startH" name="start_time[H]" value="10" style="width:30px;">时
                    <input type="text" id="startI" name="start_time[i]" value="00" style="width:30px;">分
                </td>
                <!--<td><?php echo $form::date('start_time','', 1);?></td>-->
            </tr>
            <tr>
                <td style="border-bottom: none;">操作理由：</td> 
                <td style="border-bottom: none;"><textarea name="msg" rows="6" style="width:96%"></textarea></td>
            </tr>
        </table>
    </fieldset>
    <input name="dosubmit" id="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog">
    </form>
</div>
<script type="text/javascript">
var pre = "start"; 
dateStart();
function changeYear(str,isstart)  { 
    var startMonth = $("#" + pre + "Month").val(); 
    if(startMonth == "") {
        var e = $("#" + pre + "Month"); 
        optionClear(e); 
        return; 
    } 
    var n = MonHead[startMonth - 1]; 
    if(startMonth == 2 && IsPinYear($("#" + pre + "Year").val())) {
        n++;
    }
    writeDay(n,pre); 
} 
function changeMonth(str,isstart) {
    var year = $("#" + pre + "Year").val(); 
    if(year == "") {
        var e = $("#" + pre + "Day");
        optionClear(e);
        return; 
    } 
    var n = MonHead[str - 1]; 
    if(str == 2 && IsPinYear($("#" + pre + "Year").val())) {
        n++;
    }
    writeDay(n,pre); 
} 
function dateStart(defaultStartY, defaultStartM, defaultStartD) {
    defaultStartY = defaultStartY || new Date().getFullYear();
    defaultStartM = defaultStartM || new Date().getMonth()+1;
    defaultStartD = defaultStartD || new Date().getDate();
    var defaultEndY = "2011"; 
    var defaultEndM = "2"; 
    var defaultEndD = "23"; 
    MonHead = [31,28,31,30,31,30,31,31,30,31,30,31]; 
    var y = new Date().getFullYear(); 
    var i_index =0; 
    for(var i=(y+5); i>=y-5; i--) { 
        $('#' + pre + 'Year').append("<option value='"+ i +"'>"+ i +"</option>");
        if(i == defaultStartY) {
            $('#' + pre + 'Year option:eq('+ i_index +')').attr('selected', true);
        } 
        i_index++; 
    }
    defaultM = (pre == "start" ? defaultStartM : defaultEndM);
    for(var i = 1; i < 13; i++) { 
        $('#' + pre + 'Month').append("<option value='"+ i +"'>"+ i +"</option>");
        if(i == defaultM) {
            $('#' + pre + 'Month option:eq('+ (i-1) +')').attr('selected', true);
        } 
    }
    var n = MonHead[$('#' + pre + 'Month').val() - 1];
    if(new Date().getMonth == 1 && IsPinYear($('#' + pre + 'Year').val())) { 
        n++; 
    } 
    defaultD = (pre == "start" ? defaultStartD : defaultEndD) ;
    writeDay(n,pre);
    $('#' + pre + 'Day option:eq('+ (defaultD-1) +')').attr('selected', true);

} 
function writeDay(n,pre) {    
    var e = $('#' + pre + 'Day'); 
    optionClear(e); 
    for (var i=1; i<(n+1); i++) {
        e.append("<option value='"+ i +"'>"+ i +"</option>");
    } 
} 
function IsPinYear(year) { 
    return (0 == year%4 && (year%100 != 0 || year % 4 == 0)); 
} 
function optionClear(e) { 
    for(var i= e.find('option').length; i>=0; i--) { 
        e.find('option:eq('+ i +')').remove();
    } 
} 
</script>
</body> 
</html>