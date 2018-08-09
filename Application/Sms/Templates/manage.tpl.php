<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
	include $this->admin_tpl('header','Admin');
?>
<div class="pad_10">
<div class="table-list">
<form name="smsform" action="" method="get" >
<input type="hidden" value="sms" name="m">
<input type="hidden" value="sms" name="c">
<input type="hidden" value="init" name="a">
<input type="hidden" value="<?php echo $_GET['menuid']?>" name="menuid">
<div class="explain-col search-form">
<font color="red">公告：</font><br>短信服务由第三方提供技术支持！云划算系统目前集成2家短信平台接口<br/> (1)中国网建短信平台接口 平台地址：<a href="http://www.smschinese.cn/" target="_blank">http://www.smschinese.cn/</a><br/> (2)阿里大鱼短信短信接口接口 平台地址：<a href="http://www.alidayu.com//" target="_blank">http://www.alidayu.com/</a><br/> 中国网建配置比较方便 但价格比较高， 阿里大鱼价格比较便宜 需配置短信模板等 比较麻烦</div>
</form>
<div class="btn text-l">
<?php if($sms_num) {?>
<span class="font-fixh green"><span class="font-fixh green">当前余额</span> ： </span><span class="font-fixh"><?php echo $sms_num?></span> <span class="font-fixh green">条</span>
<?php } else { ?>
<?php if(!$sms_setting) {?>

            <span class="font-fixh green">平台接口不存在或未启用，请点击<a href="<?php echo U('setting') ?>"><span class="font-fixh">平台设置</span></a>绑定。</span>
<?php }?>
<?php }?>
</div><br>

<div class="btn text-l">
<span class="font-fixh green">当前服务器IP为 ： <span class="font-fixh"><?php echo $_SERVER["SERVER_ADDR"];?></span>
</div>
<br>
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="5%" align="center">编号</th>
            <th width="10%" align="left">手机号</th>
            <th width="30%" align="left">类型</th>
            <th width="10%" align="left">所属会员</th>
            <th align="left">消息内容</th>
            <th width="5%" align="center">状态</th>
            <th width="10%" align="center">ip</th>
            <th width="10%" align="left">发送时间</th>
<!--             <th width="10%" align="left">操作</th> -->
            </tr>
        </thead>
    <tbody>

<?php if(is_array($infos)) foreach($infos as $v) {?>
	<tr>
	<td align="center"><?php echo $v['id']?></td>

	<td align="left"><?php echo $v['mobile']?></td>
	<td align="left"><?php echo $v['enum']?></td>
	<td align="left"><?php echo $v['userid']?></td>
	<td align="left"><?php echo $v['msg']?></td>
    <td align="center"><?php echo ($v['status'] == 1) ? L('icon_unlock') : L('icon_locked');?></td>
    <td align="left"><?php echo $v['ip']?></td>
	<td align="left"><?php echo dgmdate($v['posttime'], 'Y-m-d H:i:s');?></td>
<!-- 	<td align="left"><a href="<?php echo U('delete') ?>" target="_blank">删除</a></td> -->
	</tr>
<?php }?>
    </tbody>
    </table>
</div>
</div>
    <div id="pages"><?php echo $pages;?></div>

<br>

<br>
<br>
<br>
</body>
</html>