<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
    SwapTab('setting','on','',3, 'base');
})
//--> 
</script>
<form action="<?php echo U('update');?>" method="post" id="myform">
<input type='hidden' name='activity_type' value="trial"/>
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_base" <?php if ($this->groupid == 'base'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',3, 'base');">首页设置</li>
            <li id="tab_setting_safe" <?php if ($this->groupid == 'safe'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',3, 'safe');">列表页设置</li>
            <li id="tab_setting_bonus" <?php if ($this->groupid == 'bonus'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',3, 'bonus');">内容页设置</li>
        </ul>
        <!-- 商家设置 -->
        <div id="div_setting_base" class="contentList pad-10">
              <div class="bk15"></div>
             <table class="table_form" width="100%">
            <tr>
                <td width="120">可用变量</td>
                <td>
                <label>网站名称：<input type="text" value="{site_title}" size="15" class="input-botton input-text"></label>
                 <label>内容页(title)：<input type="text" value="{title}" size="15" class="input-botton input-text"></label>
               
                </td>
            </tr>
        </table>
          <div class="bk15"></div>
        <fieldset>
            <legend>首页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>

                
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;" name="setting[site_web_title]" value="<?php echo $setting['site_web_title'] ?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[keyword]"><?php echo $setting['keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[description]"><?php echo $setting['description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset>
        <div class="bk15"></div>
         <fieldset>
            <legend>积分商城首页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;" class="input-text" name="setting[score_seo][score_title]" value="<?php echo $setting['score_seo']['score_title'] ?>">
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[score_seo][score_keyword]"><?php echo $setting['score_seo']['score_keyword']  ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[score_seo][score_description]"><?php echo $setting['score_seo']['score_description']  ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 


        <div class="bk15"></div>
         <fieldset>
            <legend>活动报名首页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;" class="input-text" name="setting[activity_seo][activity_title]" value="<?php echo $setting['activity_seo']['activity_title'] ?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[activity_seo][activity_keyword]"><?php echo $setting['activity_seo']['activity_keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[activity_seo][activity_description]"><?php echo $setting['activity_seo']['activity_description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 


        <div class="bk15"></div>
         <fieldset>
            <legend>帮助中心首页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;" class="input-text" name="setting[help_seo][help_title]" value="<?php echo $setting['help_seo']['help_title'] ?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键词</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[help_seo][help_keyword]"><?php echo $setting['help_seo']['help_keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[help_seo][help_description]"><?php echo $setting['help_seo']['help_description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 




       
        </div>

        <!-- 列表页SEO设置 -->
        <div id="div_setting_safe" class="contentList pad-10 hidden" style="display:none;">
            <fieldset>
            <legend>[<?php echo C('REBATE_NAME') ?>]列表页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;" class="input-text" name="setting[rebate_seo][rebate_title]" value="<?php echo $setting['rebate_seo']['rebate_title']?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[rebate_seo][rebate_keyword]"><?php echo $setting['rebate_seo']['rebate_keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[rebate_seo][rebate_description]"><?php echo $setting['rebate_seo']['rebate_description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 
         <div class="bk15"></div>
         <fieldset>
            <legend>[<?php echo C_READ('TRIAL_NAME','trial') ?>]列表页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;" class="input-text" name="setting[trial_seo][trial_title]" value="<?php echo $setting['trial_seo']['trial_title']?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[trial_seo][trial_keyword]"><?php echo $setting['trial_seo']['trial_keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[trial_seo][trial_description]"><?php echo $setting['trial_seo']['trial_description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 


        <div class="bk15"></div>
         <fieldset>
            <legend>[<?php echo C_READ('POSTAL_NAME','postal') ?>]列表页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;" class="input-text" name="setting[postal_seo][postal_title]" value="<?php echo $setting['postal_seo']['postal_title']?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[postal_seo][postal_keyword]"><?php echo $setting['postal_seo']['postal_keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[postal_seo][postal_description]"><?php echo $setting['postal_seo']['postal_description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 



        <div class="bk15"></div>
         <fieldset>
            <legend>[晒单]列表页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;"  name="setting[shai_seo][shai_title]" value="<?php echo $setting['shai_seo']['shai_title']?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[shai_seo][shai_keyword]"><?php echo $setting['shai_seo']['shai_keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[shai_seo][shai_description]"><?php echo $setting['shai_seo']['shai_description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 


        <div class="bk15"></div>
         <fieldset>
            <legend>[试用报告]列表页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;"  name="setting[report_seo][report_title]" value="<?php echo $setting['report_seo']['report_title']?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[report_seo][report_keyword]"><?php echo $setting['report_seo']['report_keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[report_seo][report_description]"><?php echo $setting['report_seo']['report_description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 



        <div class="bk15"></div>
         <fieldset>
            <legend>[商品总汇]列表页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;"  name="setting[all_seo][all_title]" value="<?php echo $setting['all_seo']['all_title']?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[all_seo][all_keyword]"><?php echo $setting['all_seo']['all_keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[all_seo][all_description]"><?php echo $setting['all_seo']['all_description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 


         <div class="bk15"></div>
         <fieldset>
            <legend>[红包试用]列表页SEO设置</legend>
            <table class="table_form" width="100%">
                <tbody>
                    <tr>
                        <th>seo标题</th>
                        <td class="y-bg">
                        <input type="text" style="margin: 0px; width: 272px;"  name="setting[red_seo][red_title]" value="<?php echo $setting['red_seo']['red_title']?>">
                            
                        </td>
                    </tr>
                   
                    <tr>
                        <th>seo关键字</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[red_seo][red_keyword]"><?php echo $setting['red_seo']['red_keyword'] ?></textarea>
                            
                        </td>
                    </tr>
                    
                    <tr>
                        <th>seo描述</th>
                        <td class="y-bg">
                         <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[red_seo][red_description]"><?php echo $setting['red_seo']['red_description'] ?></textarea>
                         
                        </td>
                    </tr>     
            </tbody>
             </table>
        </fieldset> 


        </div>
        <div id="div_setting_bonus" class="contentList pad-10" style="display:none;">
        <fieldset>
            <legend>[<?php echo C('REBATE_NAME') ?>]内容页SEO设置</legend>
                <table class="table_form" width="100%">
                    <tbody>
                        <tr>
                            <th>seo标题</th>
                            <td class="y-bg">
                            <input type="text" style="margin: 0px; width: 272px;" class="input-text" name="setting[rebate_show][show_rebate_title]" value="<?php echo $setting['rebate_show']['show_rebate_title']?>">
                                
                            </td>
                        </tr>
                       
                        <tr>
                            <th>seo关键字</th>
                            <td class="y-bg">
                             <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[rebate_show][show_rebate_keyword]"><?php echo $setting['rebate_show']['show_rebate_keyword'] ?></textarea>
                                
                            </td>
                        </tr>
                        
                        <tr>
                            <th>seo描述</th>
                            <td class="y-bg">
                             <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[rebate_show][show_rebate_description]"><?php echo $setting['rebate_show']['show_rebate_description'] ?></textarea>
                             
                            </td>
                        </tr>     
                </tbody>
                 </table>
        </fieldset> 
         <div class="bk15"></div>
         <fieldset>
            <legend>[<?php echo C_READ('TRIAL_NAME','trial') ?>]内容页SEO设置</legend>
                <table class="table_form" width="100%">
                    <tbody>
                        <tr>
                            <th>seo标题</th>
                            <td class="y-bg">
                            <input type="text" style="margin: 0px; width: 272px;" class="input-text" name="setting[trial_show][show_trial_title]" value="<?php echo $setting['trial_show']['show_trial_title']?>">
                                
                            </td>
                        </tr>
                       
                        <tr>
                            <th>seo关键字</th>
                            <td class="y-bg">
                             <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[trial_show][show_trial_keyword]"><?php echo $setting['trial_show']['show_trial_keyword'] ?></textarea>
                                
                            </td>
                        </tr>
                        
                        <tr>
                            <th>seo描述</th>
                            <td class="y-bg">
                             <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[trial_show][show_trial_description]"><?php echo $setting['trial_show']['show_trial_description'] ?></textarea>
                             
                            </td>
                        </tr>     
                </tbody>
                 </table>
        </fieldset> 
        <div class="bk15"></div>
         <fieldset>
            <legend>[<?php echo C_READ('POSTAL_NAME','postal') ?>]内容页SEO设置</legend>
                <table class="table_form" width="100%">
                    <tbody>
                        <tr>
                            <th>seo标题</th>
                            <td class="y-bg">
                            <input type="text" style="margin: 0px; width: 272px;" class="input-text" name="setting[postal_show][show_postal_title]" value="<?php echo $setting['postal_show']['show_postal_title']?>">
                                
                            </td>
                        </tr>
                       
                        <tr>
                            <th>seo关键字</th>
                            <td class="y-bg">
                             <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[postal_show][show_postal_keyword]"><?php echo $setting['postal_show']['show_postal_keyword'] ?></textarea>
                                
                            </td>
                        </tr>
                        
                        <tr>
                            <th>seo描述</th>
                            <td class="y-bg">
                             <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[postal_show][show_postal_description]"><?php echo $setting['postal_show']['show_postal_description'] ?></textarea>
                             
                            </td>
                        </tr>     
                </tbody>
                 </table>
        </fieldset> 

        </div>
        <div class="bk15"></div>
        <input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
    </div>
</div>
</form>
</body>
<script type="text/javascript">
function SwapTab(name,cls_show,cls_hide,cnt,cur) {
    $('div.contentList').hide();
    $('ul.tabBut > li').attr('class', cls_hide);
    $('#div_'+name+'_'+cur).show();
    $('#tab_'+name+'_'+cur).attr('class',cls_show);
}


</script>
</html>