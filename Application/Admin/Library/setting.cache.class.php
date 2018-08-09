<?php 
class setting
{
	public function run() {
		$arrs = array('rebate','trial','postal');
		foreach ($arrs as $key => $val) {
			$val = model('activity_set')->where(array('activity_type'=>$val))->getField("key,value",true);
			@file_put_contents(CONF_PATH.$arrs[$key].'.php', "<?php \n return ".array2string($val,CASE_UPPER).";\n?>");
		}	
	}
}