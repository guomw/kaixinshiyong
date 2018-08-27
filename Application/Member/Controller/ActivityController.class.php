<?php
namespace Member\Controller;
use \Admin\Controller\InitController;
/* 后台活动管理 */
class ActivityController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->activity_set = model('activity_set');
		$this->pagecurr = max(1, I('page', 0, 'intval'));
		$this->pagesize = 10;
    }
	/* 活动管理->购物返利 设置 [Adalways试用系统] */
	public function activity_set(){
        $activity_type = I('get.type');
		$setting = $this->activity_set->where(array('activity_type' => $activity_type))->getField('key,value');
        // 公用
        if($setting['single_mode']) $setting['single_mode'] = string2array($setting['single_mode']);
        if($setting['seller_join_condition']) $setting['seller_join_condition'] = string2array($setting['seller_join_condition']);
        if($setting['buyer_join_condition']) $setting['buyer_join_condition'] = string2array($setting['buyer_join_condition']);
        if($setting['seller_discount_range']) $setting['seller_discount_range'] = string2array($setting['seller_discount_range']);
        if($setting['seller_get_appeal']) $setting['seller_get_appeal'] = string2array($setting['seller_get_appeal']);
        // 免费试用
        if($setting['seller_trialtalk_check']) $setting['seller_trialtalk_check'] = string2array($setting['seller_trialtalk_check']);
        /*佣金*/
         if($setting['commission_price']){

            $bonus_price = string2array($setting['commission_price']);
            $bonus = $bonus_price['commission'];
            $bonus_count = count($bonus['commission_price']);
            /*var_dump($bonus);*/
            /*$price_count = count($commission['seller_price']);
            $bonus_count = count($bonus['seller_price']);*/
        }

         if($setting['trial_fee_price']){

            $trial_price = string2array($setting['trial_fee_price']);
            $trial_data = $trial_price['trial'];
            $trial_count = count($trial_data['trial_fee_price']);
           
        }

         if($setting['commission_type']) $setting['commission_type'] = string2array($setting['commission_type']);

        //红包设置
        if($setting['bonus_price']) $setting['bonus_price'] = string2array($setting['bonus_price']);
        $form = new \Common\Library\form();
        $show_validator = TRUE;
		include $this->admin_tpl($activity_type.'_set');
	}
	/**
     * 保存活动设置  [Adalways试用系统]
     * @author   <jason>
     */
    public function update() {
        if (submitcheck('dosubmit')) {
            $activity_type = I('post.activity_type');
            if (!$activity_type) $this->error('请勿非法访问！');
            $info = $_POST['setting'];
            if (empty($info)) { $this->error('参数错误'); }
            switch ($activity_type) {
                case 'rebate':
                    if($info['single_mode']){$info['single_mode']=array2string($info['single_mode']);}
                    if($info['seller_join_condition']){$info['seller_join_condition']=array2string($info['seller_join_condition']);}else{$info['seller_join_condition']='';}
                    if($info['seller_discount_range']){$info['seller_discount_range']=array2string($info['seller_discount_range']);}
                    if($info['seller_get_appeal']){$info['seller_get_appeal']=array2string($info['seller_get_appeal']);}
                    if($info['buyer_join_condition']){$info['buyer_join_condition']=array2string($info['buyer_join_condition']);}else{$info['buyer_join_condition']='';}
                    break;
                case 'trial':
                    if($info['single_mode']){$info['single_mode']=array2string($info['single_mode']);}
                    if($info['seller_join_condition']){$info['seller_join_condition']=array2string($info['seller_join_condition']); }else{$info['seller_join_condition']=''; }
                    if($info['seller_trialtalk_check']){$info['seller_trialtalk_check']=array2string($info['seller_trialtalk_check']);}
                    if($info['buyer_join_condition']){$info['buyer_join_condition']=array2string($info['buyer_join_condition']);}else{$info['buyer_join_condition']='';}
                    if($info['bonus_price']){$info['bonus_price'] = array2string($info['bonus_price']);}
                    if($info['config']){
                       $info['trial_fee_price'] = array2string($info['config']);

                    }
                   
                    break;

                case 'commission':
                    if($info['commission_type']){$info['commission_type']=array2string($info['commission_type']);}
                    if($info['seller_join_condition']){$info['seller_join_condition']=array2string($info['seller_join_condition']); }else{$info['seller_join_condition']=''; }
                    if($info['buyer_join_condition']){$info['buyer_join_condition']=array2string($info['buyer_join_condition']);}else{$info['buyer_join_condition']='';}
                    if($info['config']){
                       $info['commission_price'] = array2string($info['config']);

                    }
                    break;
            }
            foreach ($info as $k => $v) {
                    $this->activity_set->where(array('key' => $k, 'activity_type' => $activity_type))->setField('value', $v);
            }
                 $lists = $this->activity_set->distinct(true)->field('activity_type')->select();
                 foreach ($lists as $k => $v) {
                    //获取每个类型的所有值
                    $activity_types = model('activity_set')->where(array('activity_type' => $v['activity_type']))->select();
                    $types = array();
                    foreach ($activity_types as $key => $value) {
                        $types[$value['key']] = $value['value'];
                    }
                    setcache($v['activity_type'],$types,'activity_set');
                    unset($activity_types);
                 }
            @file_put_contents(CONF_PATH.$activity_type.'.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>");
            $this->success('操作成功');
        } else {
            $this->error('请勿非法提交');
        }
    }
	/**
     * 检测GD库 [Adalways试用系统]
     * @author   <jason>
     */
    private function check_gd() {
        if(!function_exists('imagepng') && !function_exists('imagejpeg') && !function_exists('imagegif')) {
            $gd = L('gd_unsupport');
        } else {
            $gd = L('gd_support');
        }
        return $gd;
    }

}