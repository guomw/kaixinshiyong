<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/12/24
 * Time: 11:23
 */
namespace Document\Controller;
use \Common\Controller\BaseController;
class AbnormalOrdersController extends BaseController
{
    public function _initialize(){
        parent::_initialize();
    }

    public function AbnormalOrders_yun(){
        $query = "select order_id,userid,num,dateline,goods_id,Count(*) from xw_member_finance_log where order_id != 0 and goods_id > 0 and num>0 group by order_id,userid,num having Count(*)>1 order by id desc";
        $arrs = M('MemberFinanceLog')->query($query);

        $totle = 0;
        $totle2 = 0;
        $totle3 = 0;

        foreach($arrs as $k=>$v){
            $arrs[$k]['time'] = date("Y-m-d H:i:s",$v['dateline']);

            $userinfo = M('Member')->getByUserid($v['userid']);
            $arrs[$k]['email'] = $userinfo['email'];
            $arrs[$k]['yue'] = $userinfo['money'];
            $arrs[$k]['money'] = ($v['Count(*)']-1)*$v['num'];

            $totle += $arrs[$k]['money'];

            if($arrs[$k]['yue']-$arrs[$k]['money'] >=0){

            }else{
                $a = $arrs[$k]['money']-$arrs[$k]['yue'];
                $totle2 += $a;
            }

        }

        print_r($arrs);
        echo "<hr/>";
        echo '全部'.$totle;
        echo "<hr/>";
        echo $totle2;
    }
}