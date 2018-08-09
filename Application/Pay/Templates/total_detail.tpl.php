<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>

<div class="pad-lr-10">


<div class="btn text-l">
    <span class="font-fixh green">
            总累计收取商家保证金 : <span class="font-fixh"><?php echo $total_deposit;?></span>&nbsp;&nbsp;（元）

    </span>
</div>
<div class="btn text-l">
    <span class="font-fixh green">
            总累计收取商家vip费用： <span class="font-fixh"><?php echo $seller_vips;?></span>&nbsp;&nbsp;（元）
    </span>
</div>
<div class="btn text-l">
    <span class="font-fixh green">
            总累计收取买家vip费用： <span class="font-fixh"><?php echo $buyer_vips;?></span>&nbsp;&nbsp;（元）<br>
    </span>
</div>
<div class="btn text-l">
    <span class="font-fixh green">
            总累计返还给会员费用： <span class="font-fixh"><?php echo $buyer_money;?></span>&nbsp;&nbsp;（元）<br>
    </span>
</div>

<div class="btn text-l">
    <span class="font-fixh green">
            总累计退还商家保证金： <span class="font-fixh"><?php echo $seller_money;?></span>&nbsp;&nbsp;（元）<br>
    </span>
</div>

<div class="btn text-l">
    <span class="font-fixh green">
            总累计成功处理提现： <span class="font-fixh"><?php echo $cash_total;?></span>&nbsp;&nbsp;（元）<br>
    </span>
</div>

<div class="btn text-l">
    <span class="font-fixh green">
            累计待处理提现多少元: <span class="font-fixh"><?php echo $have_cash;?></span>&nbsp;&nbsp;（元）<br>
    </span>
</div>

<div class="btn text-l">
    <span class="font-fixh green">
            所有会员账户余额累计: <span class="font-fixh"><?php echo $member_toatl;?></span>&nbsp;&nbsp;（元）<br>
    </span>
</div>




</div>

</body>
</html>