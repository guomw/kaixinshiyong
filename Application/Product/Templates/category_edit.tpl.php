<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript"> 
<!--
$(function(){
	$.formValidator.initConfig({
		formid:"myform",
		autotip:true,
		onerror:function(msg,obj){
			$(obj).focus();
			return false;
		}
	});		
	$("#catname").formValidator({
		onshow:"请输入分类名称",
		onfocus:"请输入分类名称",
		oncorrect:"输入正确"
	}).inputValidator({
		min:1,
		onerror:"请输入分类名称"
	});
});
//-->
</script>
<form name="myform" id="myform" action="?m=product&c=category&a=edit" method="post">
	<input type="hidden" value="<?php echo $catid ?>" name="$info['catid']">
<div class="pad-10">
	<div class="col-tab">
		<!--<ul class="tabBut cu-li">
			<li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',6,1);"><?php echo L('catgory_basic');?></li>
			<li id="tab_setting_2" onclick="SwapTab('setting','on','',6,2);"><?php echo L('catgory_createhtml');?></li>
			<li id="tab_setting_3" onclick="SwapTab('setting','on','',6,3);"><?php echo L('catgory_template');?></li>
			<li id="tab_setting_4" onclick="SwapTab('setting','on','',6,4);"><?php echo L('catgory_seo');?></li>
			<li id="tab_setting_5" onclick="SwapTab('setting','on','',6,5);"><?php echo L('catgory_private');?></li>
			<li id="tab_setting_6" onclick="SwapTab('setting','on','',6,6);"><?php echo L('catgory_readpoint');?></li>
		</ul>-->
		
		<div id="div_setting_1" class="contentList pad-10">
			<table width="100%" class="table_form ">
				<tr>
					<th width="200"><?php echo L('上级分类')?>：</th>
					<td><?php echo $form::select_category($parentid,'name="info[parentid]" id="parentid"',L('作为一级分类'),0,0);?></td>
				</tr>
				<tr>
					<th><?php echo L('分类名称')?>：</th>
					<td>
						<input type="text" name="info[catname]" id="catname" class="input-text" value="<?php echo $catname;?>">
					</td>
				</tr>
				
				<tr>
					<th><?php echo L('是否推荐')?>：</th>
					<td>
						<label><input type="radio" value="1"  name="info[isrecommend]" <?php if ($isrecommend == 1){?> checked <?php }?>/>是</label>
						<label><input type="radio" value="0"  name="info[isrecommend]" <?php if ($isrecommend == 0){?> checked <?php }?>/>否</label>
					</td>
				</tr>
				
				<tr>
					<th><?php echo L('分类图片')?>：</th>
					<td><?php echo $form::images('info[image]', 'image', $image, 'document');?></td>
				</tr>

				<tr>
					<th><?php echo L('分类图片2')?>：</th>
					<td><?php echo $form::images('info[image2]', 'image2', $image2, 'document');?></td>
				</tr>

				<!-- <tr>
					<th><strong>META Title（分类标题）</strong><br>针对搜索引擎设置的标题</th>					
					<td><input name='setting[meta_title]' type='text' id='meta_title' value='<?php echo $setting['meta_title'];?>' size='60' maxlength='60'></td>
				</tr>
				<tr>
					<th><strong>META Keywords（分类关键词）</strong><br>关键字中间用半角逗号隔开</th>
					<td><textarea name='setting[meta_keywords]' id='meta_keywords' rows="4" cols="60"><?php echo $setting['meta_keywords'];?></textarea></td>
				</tr>
				<tr>
					<th><strong>META Description（分类描述）</strong><br>针对搜索引擎设置的网页描述</th>
					<td><textarea name='setting[meta_description]' id='meta_description' rows="4" cols="60"><?php echo $setting['meta_description'];?></textarea></td>
				</tr> -->
				
			</table>

</div>
<div id="div_setting_2" class="contentList pad-10 hidden">
<table width="100%" class="table_form">
	<tr>
      <th width="200"><?php echo L('html_category');?>：</th>
      <td>
	  <input type='radio' name='setting[ishtml]' value='1' <?php if($setting['ishtml']) echo 'checked';?> onClick="$('#category_php_ruleid').css('display','none');$('#category_html_ruleid').css('display','');$('#tr_domain').css('display','');"> <?php echo L('yes');?>&nbsp;&nbsp;&nbsp;&nbsp;
	  <input type='radio' name='setting[ishtml]' value='0' <?php if(!$setting['ishtml']) echo 'checked';?>  onClick="$('#category_php_ruleid').css('display','');$('#category_html_ruleid').css('display','none');$('#tr_domain').css('display','none');"> <?php echo L('no');?>
	  </td>
    </tr>
	<tr>
      <th><?php echo L('html_show');?>：</th>
      <td>
	  <input type='radio' name='setting[content_ishtml]' value='1' <?php if($setting['content_ishtml']) echo 'checked';?> onClick="$('#show_php_ruleid').css('display','none');$('#show_html_ruleid').css('display','')"> <?php echo L('yes');?>&nbsp;&nbsp;&nbsp;&nbsp;
	  <input type='radio' name='setting[content_ishtml]' value='0' <?php if(!$setting['content_ishtml']) echo 'checked';?>  onClick="$('#show_php_ruleid').css('display','');$('#show_html_ruleid').css('display','none')"> <?php echo L('no');?>
	  </td>
    </tr>
	<tr>
      <th><?php echo L('category_urlrules');?>：</th>
      <td><div id="category_php_ruleid" style="display:<?php if($setting['ishtml']) echo 'none';?>">
	<?php
		echo $form::urlrule('Document','category',0,$setting['category_ruleid'],'name="category_php_ruleid"');
	?>
	</div>
	<div id="category_html_ruleid" style="display:<?php if(!$setting['ishtml']) echo 'none';?>">
	<?php
		echo $form::urlrule('Document','category',1,$setting['category_ruleid'],'name="category_html_ruleid"');
	?>
	</div>
	</td>
    </tr>
	
	<tr>
      <th><?php echo L('show_urlrules');?>：</th>
      <td><div id="show_php_ruleid" style="display:<?php if($setting['content_ishtml']) echo 'none';?>">
	  <?php
		echo $form::urlrule('Document','show',0,$setting['show_ruleid'],'name="show_php_ruleid"');
	?>
	</div>
	<div id="show_html_ruleid" style="display:<?php if(!$setting['content_ishtml']) echo 'none';?>">
	  <?php	
		echo $form::urlrule('Document','show',1,$setting['show_ruleid'],'name="show_html_ruleid"');
	?>
	</div>
	</td>
    </tr>
<tr>
     <th><?php echo L('create_to_rootdir');?>：</th>
      <td>
	  <input type='radio' name='setting[create_to_html_root]' value='1' <?php if($setting['create_to_html_root']) echo 'checked';?> > <?php echo L('yes');?>&nbsp;&nbsp;&nbsp;&nbsp;
	  <input type='radio' name='setting[create_to_html_root]' value='0' <?php if(!$setting['create_to_html_root']) echo 'checked';?> > <?php echo L('no');?>
	  （<?php echo L('create_to_rootdir_tips');?>）</td>
    </tr>
    <tr id="tr_domain" style="display:<?php if(!$setting['ishtml']) echo 'none';?>">
        <th><?php echo L('domain')?>：</th>
        <td><input type="text" name="info[url]" id="url" class="input-text" size="50" value="<?php if(preg_match('/^http:\/\/([a-z0-9\-\.]+)\/$/i',$url)) echo $url;?>"></td>
      </tr>
</table>
</div>
<div id="div_setting_3" class="contentList pad-10 hidden">
<table width="100%" class="table_form ">
<tr>
  <th width="200"><?php echo L('available_styles');?>：</th>
        <td>
		<?php echo $form::select($template_list, $setting['template_list'], 'name="setting[template_list]" id="template_list" onchange="load_file_list(this.value)"', L('please_select'))?> 
		</td>
</tr>
		<tr>
        <th width="200"><?php echo L('category_index_tpl')?>：</th>
 <td  id="category_template">
		</td>      </tr>
	  <tr>
        <th width="200"><?php echo L('category_list_tpl')?>：</th>
        <td  id="list_template">
		</td>
      </tr>
	  <tr>
        <th width="200"><?php echo L('content_tpl')?>：</th>
        <td  id="show_template">
		</td>
      </tr>
	  
	  <!--模版应用到子分类配置-->
	  <tr>
        <th width="200"><?php echo '模板应用到子分类';?></th>
        <td><input type='radio' name='template_child' value='1'> <?php echo L('yes');?>&nbsp;&nbsp;&nbsp;&nbsp;
          <input type='radio' name='template_child' value='0' checked> <?php echo L('no');?></td></td>
      </tr>
	  <!--end 模版应用到子分类配置-->
	  
</table>
</div>
<div id="div_setting_5" class="contentList pad-10 hidden">
<table width="100%" >
		<tr>
        <th width="200"><?php echo L('role_private')?>：</th>
        <td>
			<table width="100%" class="table-list">
			  <thead>
				<tr>
				  <th align="left"><?php echo L('role_name');?></th><th><?php echo L('view');?></th><th><?php echo L('add');?></th><th><?php echo L('edit');?></th><th><?php echo L('delete');?></th><th><?php echo L('listorder');?></th><th><?php echo L('push');?></th><th><?php echo L('move');?></th>
			  </tr>
			    </thead>
				 <tbody>
				<?php
				$roles = F('Role');
				foreach($roles as $roleid=> $rolrname) {
				$disabled = $roleid==1 ? 'disabled' : '';				
				?>
		  		<tr>
				  <td><?php echo $rolrname['rolename']?></td>
				  <td align="center"><input type="checkbox" name="priv_roleid[]" <?php echo $disabled;?> <?php echo $this->check_category_priv('init',$roleid);?> value="init,<?php echo $roleid;?>" ></td>
				  <td align="center"><input type="checkbox" name="priv_roleid[]" <?php echo $disabled;?> <?php echo $this->check_category_priv('add',$roleid);?> value="add,<?php echo $roleid;?>" ></td>
				  <td align="center"><input type="checkbox" name="priv_roleid[]" <?php echo $disabled;?> <?php echo $this->check_category_priv('edit',$roleid);?> value="edit,<?php echo $roleid;?>" ></td>
				  <td align="center"><input type="checkbox" name="priv_roleid[]" <?php echo $disabled;?> <?php echo $this->check_category_priv('delete',$roleid);?> value="delete,<?php echo $roleid;?>" ></td>
				  <td align="center"><input type="checkbox" name="priv_roleid[]" <?php echo $disabled;?> <?php echo $this->check_category_priv('listorder',$roleid);?> value="listorder,<?php echo $roleid;?>" ></td>
				  <td align="center"><input type="checkbox" name="priv_roleid[]" <?php echo $disabled;?> <?php echo $this->check_category_priv('push',$roleid);?> value="push,<?php echo $roleid;?>" ></td>
				  <td align="center"><input type="checkbox" name="priv_roleid[]" <?php echo $disabled;?> <?php echo $this->check_category_priv('move',$roleid);?> value="move,<?php echo $roleid;?>" ></td>
			  </tr>
			  <?php }?>
	
			 </tbody>
			</table>
		</td>

      </tr>
		<tr><td colspan=2><hr style="border:1px dotted #F2F2F2;"></td>
		</tr>

	  <tr>
        <th width="200"><?php echo L('group_private')?>：</th>
        <td>
			<table width="100%" class="table-list">
			  <thead>
				<tr>
				  <th align="left"><?php echo L('group_name');?></th><th><?php echo L('allow_vistor');?></th><th><?php echo L('allow_contribute');?></th>
			  </tr>
			    </thead>
				 <tbody>
			<?php
			$group_cache = F('member_group');
			foreach($group_cache as $_key=>$_value) {
			if($_value['groupid']==1) continue;
			?>
		  		<tr>
				  <td><?php echo $_value['name'];?></td>
				  <td align="center"><input type="checkbox" name="priv_groupid[]"  <?php echo $this->check_category_priv('visit',$_value['groupid'],0);?> value="visit,<?php echo $_value['groupid'];?>" ></td>
				  <td align="center"><input type="checkbox" name="priv_groupid[]"  <?php echo $this->check_category_priv('add',$_value['groupid'],0);?> value="add,<?php echo $_value['groupid'];?>" ></td>
			  </tr>
			<?php }?>
			 </tbody>
			</table>
		</td>
      </tr>
	  <tr>
	   <th width="200"><?php echo L('apply_to_child');?></th>
        <td><input type='radio' name='priv_child' value='1'> <?php echo L('yes');?>&nbsp;&nbsp;&nbsp;&nbsp;
	  <input type='radio' name='priv_child' value='0' checked> <?php echo L('no');?></td></td>
	  </tr>
</table>
</div>
<div id="div_setting_6" class="contentList pad-10 hidden">
<table width="100%" class="table_form">
<tr>
     <th width="200"><?php echo L('contribute_add_point');?></th>
      <td><input name='setting[presentpoint]' type='text' value='<?php echo $setting['presentpoint'];?>' size='5' maxlength='5' style='text-align:center'> <?php echo L('contribute_add_point_tips');?></td>
</tr>
   <tr>
      <th ><?php echo L('default_readpoint');?></th>
      <td><input name='setting[defaultchargepoint]' type='text' value='<?php echo $setting['defaultchargepoint'];?>' size='4' maxlength='4' style='text-align:center'> <select name="setting[paytype]"><option value="0" <?php if(!$setting['paytype']) echo 'selected';?>><?php echo L('readpoint');?></option><option value="1" <?php if($setting['paytype']) echo 'selected';?>><?php echo L('money');?></option></select> <?php echo L('readpoint_tips');?></td>
    </tr>
    <tr>
      <th><?php echo L('repeatchargedays');?></th>
      <td>
	    <input name='setting[repeatchargedays]' type='text' value='<?php echo $setting['repeatchargedays'];?>' size='4' maxlength='4' style='text-align:center'> <?php echo L('repeat_tips');?>&nbsp;&nbsp;
        <font color="red"><?php echo L('repeat_tips2');?></font></td>
    </tr>
</table>   
</div>
 <div class="bk15"></div>
	<input name="catid" type="hidden" value="<?php echo $catid;?>">
    <input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">

</form>
</div>

</div>
<!--table_form_off-->
</div>

<script language="JavaScript">
<!--
	window.top.$('#display_center_id').css('display','none');
	$(function(){
		var url = $('#url').val();
		if(!url.match(/^http:\/\//)) $('#url').val('');
	})
	function SwapTab(name,cls_show,cls_hide,cnt,cur){
		for(i=1;i<=cnt;i++){
			if(i==cur){
				 $('#div_'+name+'_'+i).show();
				 $('#tab_'+name+'_'+i).attr('class',cls_show);
			}else{
				 $('#div_'+name+'_'+i).hide();
				 $('#tab_'+name+'_'+i).attr('class',cls_hide);
			}
		}
	}
	function load_file_list(id) {
		if(id=='') return false;
		$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&catid=<?php echo $catid?>', function(data){$('#category_template').html(data.category_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
	}
	<?php if(isset($setting['template_list']) && !empty($setting['template_list'])) echo "load_file_list('".$setting['template_list']."')"?>
//-->
</script>