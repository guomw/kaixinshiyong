<?php 
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form method="post" action="<?php echo U('edit');?>" name="myform" id="myform">
<input type="hidden" name="announce[announceid]" value="<?php echo $announceid;?>">
<table class="table_form" width="100%">
<tbody>
	<tr>
		<th width="80"><strong><?php echo L('announce_type')?></strong></th>
		<td>
			<input name="announce[type]" id="type" <?php if( $type == '0'){?> checked <?php }?> class="input-text" type="radio" value="0" >普通公告
			<input name="announce[type]" id="type" <?php if( $type == '1'){?> checked <?php }?> class="input-text" type="radio" value="1">商家公告
			<input name="announce[type]" id="type"  <?php if( $type == '2'){?> checked <?php }?> class="input-text" type="radio" value="2">招商公告
			<input name="announce[type]" id="type" <?php if( $type == '3'){?> checked <?php }?>  class="input-text" type="radio" value="3">官方公告
			<input name="announce[type]" id="type" <?php if( $type == '5'){?> checked <?php }?> class="input-text" type="radio" value="5">VIP公告
		</td>
	</tr>
	
	<tr>
		<th width="80"><?php echo L('announce_title')?></th>
		<td><input name="announce[title]" id="title" value="<?php echo $title;?>" class="input-text" type="text" size="50" ></td>
	</tr>
	<tr>
		<th><?php echo L('startdate')?>：</th>
		<td><?php echo $form::date('announce[starttime]', date('Y-m-d H:i:s',$starttime), 1)?></td>
	</tr>
	<tr>
		<th><?php echo L('enddate')?>：</th>
		<td><?php $an_info['endtime'] = $an_info['endtime']=='0000-00-00' ? '' : date('Y-m-d H:i:s',$endtime); echo $form::date('announce[endtime]', date('Y-m-d H:i:s',$endtime), 1);?></td>
	</tr>
	<tr>
		<th><?php echo L('announce_content')?></th>
		<td >
		<textarea name="announce[content]" id="content"><?php echo stripslashes($content);?></textarea>
		<?php echo $form::editor('content','basic');?>
		</td>
	</tr>
	<tr>
		<th><?php echo L('announce_status')?></th>
		<td><input name="announce[passed]" type="radio" value="1" <?php if($passed==1) {?>checked<?php }?>></input>&nbsp;<?php echo L('pass')?>&nbsp;&nbsp;<input name="announce[passed]" type="radio" value="0" <?php if($passed==0) {?>checked<?php }?>>&nbsp;<?php echo L('unpass')?></td>
	</tr>
    </tbody>
</table>
<input type="submit" name="dosubmit" id="dosubmit" value=" <?php echo L('ok')?> " class="dialog">&nbsp;<input type="reset" class="dialog" value=" <?php echo L('clear')?> ">
</form>
</div>
</body>
</html>
<script type="text/javascript">
function load_file_list(id) {
	$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&module=announce&templates=show&name=announce&pc_hash='+pc_hash, function(data){$('#show_template').html(data.show_template);});
}

$(document).ready(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'220',height:'70'}, function(){this.close();$(obj).focus();})}});
	$('#title').formValidator({onshow:"<?php echo L('input_announce_title')?>",onfocus:"<?php echo L('title_min_3_chars')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:1,onerror:"<?php echo L('title_cannot_empty')?>"}).ajaxValidator({type:"get",url:"",data:"m=announce&c=admin_announce&a=public_check_title&aid=<?php echo $_GET['aid']?>",datatype:"html",cached:false,async:'true',success : function(data) {
        if( data == "1" )
		{
            return true;
		}
        else
		{
            return false;
		}
	},
	error: function(){alert("<?php echo L('server_no_data')?>");},
	onerror : "<?php echo L('announce_exist')?>",
	onwait : "<?php echo L('checking')?>"
	}).defaultPassed();
	$('#starttime').formValidator({onshow:"<?php echo L('select_stardate')?>",onfocus:"<?php echo L('select_stardate')?>",oncorrect:"<?php echo L('right_all')?>"}).defaultPassed();
	$('#endtime').formValidator({onshow:"<?php echo L('select_downdate')?>",onfocus:"<?php echo L('select_downdate')?>",oncorrect:"<?php echo L('right_all')?>"}).defaultPassed();
	$("#content").formValidator({autotip:true,onshow:"",onfocus:"<?php echo L('announcements_cannot_be_empty')?>"}).functionValidator({
	    fun:function(val,elem){
	    //获取编辑器中的内容
		var oEditor = CKEDITOR.instances.content;
		var data = oEditor.getData();
        if(data==''){
		    return "<?php echo L('announcements_cannot_be_empty')?>"
	    } else {
			return true;
		}
	}
	}).defaultPassed();
	$('#style').formValidator({onshow:"<?php echo L('select_style')?>",onfocus:"<?php echo L('select_style')?>",oncorrect:"<?php echo L('right_all')?>"}).inputValidator({min:1,onerror:"<?php echo L('select_style')?>"}).defaultPassed();
});
</script>